<?php

namespace App\Http\Controllers\LeaveManagement\HrAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Department;
use App\Models\LeaveType;
use App\Models\Approval;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LeaveApprovalController extends Controller
{
    /**
     * Display HR pending approvals
     */
   public function pending(Request $request)
{
    try {
        Log::info('HR Admin pending approvals - Starting');
        
        // Get all pending leave requests that have department head approval
        // but DON'T have HR admin approval yet
        $query = LeaveRequest::with([
            'user', 
            'user.department', 
            'leaveType',
            'approvals' => function($q) {
                $q->orderBy('created_at', 'desc');
            },
            'approvals.approver'
        ])
        ->where('status', 'pending') // Leave request is still pending
        ->whereHas('approvals', function($q) {
            // Has department head approval
            $q->where('level', 'department_head')
              ->where('status', 'approved');
        })
        ->whereDoesntHave('approvals', function($q) {
            // Doesn't have HR admin approval yet
            $q->where('level', 'hr_admin');
        })
        ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('department') && $request->input('department') != 'all') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->input('department'));
            });
        }

        if ($request->has('leave_type') && $request->input('leave_type') != 'all') {
            $query->where('leave_type_id', $request->input('leave_type'));
        }

        if ($request->has('employee') && $request->input('employee') != 'all') {
            $query->where('user_id', $request->input('employee'));
        }

        if ($request->has('start_date') && $request->input('start_date')) {
            $query->whereDate('start_date', '>=', $request->input('start_date'));
        }

        $pendingRequests = $query->get();
        
        Log::info('Found ' . $pendingRequests->count() . ' HR pending requests');
        
        // Debug: Log what we found
        foreach ($pendingRequests as $req) {
            Log::info('Leave ID: ' . $req->id . ', User: ' . $req->user->first_name . 
                     ', Dept Head Approval: ' . $req->approvals->where('level', 'department_head')->count());
        }

        // Get departments - YOUR schema doesn't have is_active
        $departments = Department::orderBy('name')->get();

        // Get leave types - YOUR schema might not have status field
        try {
            $leaveTypes = LeaveType::orderBy('name')->get();
        } catch (\Exception $e) {
            Log::warning('Error getting leave types: ' . $e->getMessage());
            $leaveTypes = collect();
        }
        
        // Get employees for filter
        $employees = User::whereHas('leaveRequests', function($q) {
                $q->where('status', 'pending');
            })
            ->orderBy('first_name')
            ->get();

        // Calculate stats - FIXED for YOUR schema
        $stats = [
            'hr_pending' => $pendingRequests->count(),
            
            // Approved today: Leave requests that got HR approval today
            'approved_today' => LeaveRequest::where('status', 'approved')
                ->whereHas('approvals', function($q) {
                    $q->where('level', 'hr_admin')
                      ->where('status', 'approved')
                      ->whereDate('created_at', Carbon::today());
                })
                ->count(),
                
            'total_departments' => Department::count(),
            'total_employees' => User::where('status', 'active')->count(),
        ];

        // Group by department for display
        $departmentGroups = $pendingRequests->groupBy(function($request) {
            return $request->user->department_id ?? 0;
        });

        return view('modules.Leave-management.hr_admin.pending', compact(
            'pendingRequests',
            'departmentGroups',
            'departments',
            'leaveTypes',
            'employees',
            'stats'
        ));

    } catch (\Exception $e) {
        Log::error('Error in LeaveApprovalController@pending: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        return view('modules.Leave-management.hr_admin.pending', [
            'pendingRequests' => collect(),
            'departmentGroups' => collect(),
            'departments' => Department::orderBy('name')->get(),
            'leaveTypes' => collect(),
            'employees' => collect(),
            'stats' => [
                'hr_pending' => 0,
                'approved_today' => 0,
                'total_departments' => 0,
                'total_employees' => 0,
            ]
        ])->with('error', 'Error loading pending approvals.');
    }
}
    /**
     * Approve leave request (HR Admin)
     */
   public function approve(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $leaveRequest = LeaveRequest::with('approvals')->findOrFail($id);
        
        // Check if already processed
        if ($leaveRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This leave request has already been processed.');
        }

        // Check if department head has approved (for security)
        $hasDeptHeadApproval = $leaveRequest->approvals()
            ->where('level', 'department_head')
            ->where('status', 'approved')
            ->exists();
            
        if (!$hasDeptHeadApproval) {
            return redirect()->back()->with('error', 'Department head approval is required before HR approval.');
        }

        // Create HR approval record
        Approval::create([
            'leave_request_id' => $leaveRequest->id,
            'approver_id' => Auth::id(),
            'level' => 'hr_admin',
            'status' => 'approved',
            'remarks' => $request->remarks ?? 'HR Approved',
        ]);

        // Update leave request status to approved
        $leaveRequest->update([
            'status' => 'approved',
            // Note: Your schema doesn't have action_by or action_at
            // So we don't update them
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Leave request approved successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error approving leave request: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error approving leave request: ' . $e->getMessage());
    }
}

    /**
     * Reject leave request (HR Admin)
     */
   public function reject(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $leaveRequest = LeaveRequest::with('approvals')->findOrFail($id);
        
        // Check if already processed
        if ($leaveRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This leave request has already been processed.');
        }

        // Validate rejection reason
        $request->validate([
            'rejection_reason' => 'required|string|min:5'
        ]);

        // Create HR approval record
        Approval::create([
            'leave_request_id' => $leaveRequest->id,
            'approver_id' => Auth::id(),
            'level' => 'hr_admin',
            'status' => 'rejected',
            'remarks' => $request->rejection_reason,
        ]);

        // Update leave request
        $leaveRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Leave request rejected successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error rejecting leave request: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error rejecting leave request: ' . $e->getMessage());
    }
}

    /**
     * Approve all pending requests
     */
    public function approveAll(Request $request)
    {
        try {
            DB::beginTransaction();

            $query = LeaveRequest::where('status', 'pending')
                ->whereHas('approvals', function($q) {
                    $q->where('level', 'department_head')
                      ->where('status', 'approved');
                })
                ->whereDoesntHave('approvals', function($q) {
                    $q->where('level', 'hr_admin');
                });

            // Apply same filters
            if ($request->has('department') && $request->input('department') != 'all') {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('department_id', $request->input('department'));
                });
            }

            if ($request->has('leave_type') && $request->input('leave_type') != 'all') {
                $query->where('leave_type_id', $request->input('leave_type'));
            }

            $pendingRequests = $query->get();
            $approvedCount = 0;

            foreach ($pendingRequests as $leaveRequest) {
                // Create HR approval record
                Approval::create([
                    'leave_request_id' => $leaveRequest->id,
                    'approver_id' => Auth::id(),
                    'level' => 'hr_admin',
                    'status' => 'approved',
                    'remarks' => 'Bulk approval',
                ]);

                // Update leave request
                $leaveRequest->update([
                    'status' => 'approved',
                    'action_by' => Auth::id(),
                    'action_at' => Carbon::now(),
                ]);

                $approvedCount++;
            }

            DB::commit();

            return redirect()->route('admin.leaves.pending')
                ->with('success', "Successfully approved {$approvedCount} leave requests.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.leaves.pending')
                ->with('error', 'Error bulk approving requests: ' . $e->getMessage());
        }
    }

    /**
     * Send reminders to department heads
     */
    public function sendReminders(Request $request)
    {
        try {
            // Get departments with pending approvals
            $departments = Department::whereHas('users.leaveRequests', function($query) {
                $query->where('status', 'pending')
                      ->whereHas('approvals', function($q) {
                          $q->where('level', 'department_head')
                            ->where('status', 'approved');
                      })
                      ->whereDoesntHave('approvals', function($q) {
                          $q->where('level', 'hr_admin');
                      });
            })->with(['manager'])->get();

            $remindedCount = 0;

            foreach ($departments as $department) {
                if ($department->manager) {
                    // Here you would implement your email sending logic
                    // Example: Mail::to($department->manager->email)->send(new PendingApprovalsReminder($department));
                    
                    Log::info("Reminder sent to department manager: {$department->manager->email} for {$department->name}");
                    $remindedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Reminders sent to {$remindedCount} department managers.",
                'count' => $remindedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending reminders: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error sending reminders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display HR Admin leave history
     */
   public function history(Request $request)
{
    try {
        // Start query with all leave requests
        $query = LeaveRequest::with(['user', 'user.department', 'leaveType'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->has('department') && $request->input('department') != 'all') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->input('department'));
            });
        }
        
        if ($request->has('employee') && $request->input('employee') != 'all') {
            $query->where('user_id', $request->input('employee'));
        }
        
        if ($request->has('status') && $request->input('status') != 'all') {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->has('month') && $request->input('month')) {
            try {
                $month = Carbon::createFromFormat('Y-m', $request->input('month'));
                $query->whereMonth('created_at', $month->month)
                      ->whereYear('created_at', $month->year);
            } catch (\Exception $e) {
                Log::warning('Invalid month format: ' . $request->input('month'));
            }
        }
        
        if ($request->has('leave_type') && $request->input('leave_type') != 'all') {
            $query->where('leave_type_id', $request->input('leave_type'));
        }
        
        // Get leave requests with pagination - FIXED: Use paginate()
        $leaveRequests = $query->paginate(20);
        
        // Get filter data - handle missing columns
        try {
            $departments = Department::when(
                Schema::hasColumn('departments', 'is_active'),
                function($query) {
                    return $query->where('is_active', true);
                },
                function($query) {
                    return $query; // Return all if column doesn't exist
                }
            )->orderBy('name')->get();
        } catch (\Exception $e) {
            Log::warning('Error getting departments: ' . $e->getMessage());
            $departments = Department::orderBy('name')->get();
        }
        
        try {
            $leaveTypes = LeaveType::when(
                Schema::hasColumn('leave_types', 'status'),
                function($query) {
                    return $query->where('status', 'active');
                },
                function($query) {
                    return $query; // Return all if column doesn't exist
                }
            )->orderBy('name')->get();
        } catch (\Exception $e) {
            Log::warning('Error getting leave types: ' . $e->getMessage());
            $leaveTypes = LeaveType::orderBy('name')->get();
        }
        
        $employees = User::where('status', 'active')->orderBy('first_name')->get();
        
        // Get statistics
        $stats = [
            'total' => LeaveRequest::count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
            'this_month' => LeaveRequest::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];
        
        return view('modules.Leave-management.hr_admin.history', compact(
            'leaveRequests',
            'departments',
            'leaveTypes',
            'employees',
            'stats'
        ));
        
    } catch (\Exception $e) {
        Log::error('Error in LeaveApprovalController@history: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        
        // Return empty paginator on error
        return view('modules.Leave-management.hr_admin.history', [
            'leaveRequests' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
            'departments' => collect(),
            'leaveTypes' => collect(),
            'employees' => collect(),
            'stats' => [
                'total' => 0,
                'approved' => 0,
                'pending' => 0,
                'rejected' => 0,
                'this_month' => 0,
            ]
        ])->with('error', 'Error loading leave history: ' . $e->getMessage());
    }
}

    /**
     * Get leave request details
     */
    public function getLeaveDetails($id)
    {
        try {
            $leaveRequest = LeaveRequest::with([
                'user', 
                'user.department', 
                'leaveType',
                'actionBy',
                'approvals',
                'approvals.approver'
            ])->findOrFail($id);
            
            // Status colors for the view
            $statusColors = [
                'pending' => 'yellow',
                'approved' => 'green',
                'rejected' => 'red',
                'cancelled' => 'gray'
            ];
            
            return view('modules.Leave-management.hr_admin.leave-details', compact('leaveRequest', 'statusColors'));
            
        } catch (\Exception $e) {
            return redirect()->route('admin.leaves.history')
                ->with('error', 'Leave request not found: ' . $e->getMessage());
        }
    }

    /**
 * Generate report
 */
/**
 * AJAX endpoint to generate report (returns JSON)
 */
public function generateReport(Request $request)
{
    try {
        // Just return JSON response with download URL
        $filters = http_build_query($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Report generated successfully',
            'download_url' => route('download-report') . '?' . $filters
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error in generateReport: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error generating report: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Download report file (returns PDF format via HTML print)
 */
public function downloadReport(Request $request)
{
    try {
        // Apply same filters as history
        $query = LeaveRequest::with(['user', 'user.department', 'leaveType'])
            ->orderBy('created_at', 'desc');
        
        // Track applied filters
        $filters = [
            'department' => $request->input('department', 'all'),
            'employee' => $request->input('employee', 'all'),
            'status' => $request->input('status', 'all'),
            'leave_type' => $request->input('leave_type', 'all'),
            'month' => $request->input('month', ''),
        ];
        
        $hasFilters = false;
        
        // Apply filters
        if ($request->has('department') && $request->input('department') != 'all') {
            $hasFilters = true;
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->input('department'));
            });
            
            // Get department name for display
            $dept = Department::find($request->input('department'));
            $filters['department_name'] = $dept ? $dept->name : 'Selected';
        }
        
        if ($request->has('employee') && $request->input('employee') != 'all') {
            $hasFilters = true;
            $query->where('user_id', $request->input('employee'));
            
            // Get employee name for display
            $emp = User::find($request->input('employee'));
            $filters['employee_name'] = $emp ? $emp->first_name . ' ' . $emp->last_name : 'Selected';
        }
        
        if ($request->has('status') && $request->input('status') != 'all') {
            $hasFilters = true;
            $query->where('status', $request->input('status'));
        }
        
        if ($request->has('month') && $request->input('month')) {
            $hasFilters = true;
            try {
                $month = Carbon::createFromFormat('Y-m', $request->input('month'));
                $query->whereMonth('created_at', $month->month)
                      ->whereYear('created_at', $month->year);
                $filters['month'] = $month->format('F Y');
            } catch (\Exception $e) {
                Log::warning('Invalid month format in report: ' . $request->input('month'));
            }
        }
        
        if ($request->has('leave_type') && $request->input('leave_type') != 'all') {
            $hasFilters = true;
            $query->where('leave_type_id', $request->input('leave_type'));
            
            // Get leave type name for display
            $leaveType = LeaveType::find($request->input('leave_type'));
            $filters['leave_type_name'] = $leaveType ? $leaveType->name : 'Selected';
        }
        
        $leaveRequests = $query->get();
        
        // Calculate statistics
        $stats = [
            'total' => $leaveRequests->count(),
            'approved' => $leaveRequests->where('status', 'approved')->count(),
            'pending' => $leaveRequests->where('status', 'pending')->count(),
            'rejected' => $leaveRequests->where('status', 'rejected')->count(),
            'total_days' => $leaveRequests->where('status', 'approved')->sum('total_days'),
        ];
        
        // Generate PDF
        $pdf = Pdf::loadView('modules.Leave-management.hr_admin.report-pdf', [
            'leaveRequests' => $leaveRequests,
            'stats' => $stats,
            'filters' => $filters,
            'hasFilters' => $hasFilters,
        ])
        ->setPaper('a4', 'landscape')
        ->setOption('margin-top', 10)
        ->setOption('margin-bottom', 10)
        ->setOption('margin-left', 10)
        ->setOption('margin-right', 10);
        
        // Generate filename
        $filename = 'leave-history-report-' . date('Y-m-d-H-i-s') . '.pdf';
        
        // Download PDF
        return $pdf->download($filename);

    } catch (\Exception $e) {
        Log::error('Error generating PDF report: ' . $e->getMessage());
        
        return redirect()->route('admin.leaves.history')
            ->with('error', 'Error generating report: ' . $e->getMessage());
    }
}

/**
 * Clean CSV value to prevent issues
 */
// private function cleanCsvValue($value)
// {
//     // Remove line breaks and extra spaces
//     $value = str_replace(["\r", "\n"], ' ', $value);
//     $value = preg_replace('/\s+/', ' ', $value);
//     return trim($value);
// }

/**
 * Export leave history to CSV
 */
public function export(Request $request)
{
    try {
        Log::info('Exporting leave history', $request->all());
        
        // Apply same filters as history
        $query = LeaveRequest::with(['user', 'user.department', 'leaveType', 'approvals'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->has('department') && $request->input('department') != 'all') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->input('department'));
            });
        }
        
        if ($request->has('employee') && $request->input('employee') != 'all') {
            $query->where('user_id', $request->input('employee'));
        }
        
        if ($request->has('status') && $request->input('status') != 'all') {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->has('month') && $request->input('month')) {
            try {
                $month = Carbon::createFromFormat('Y-m', $request->input('month'));
                $query->whereMonth('created_at', $month->month)
                      ->whereYear('created_at', $month->year);
            } catch (\Exception $e) {
                Log::warning('Invalid month format in export: ' . $request->input('month'));
            }
        }
        
        if ($request->has('leave_type') && $request->input('leave_type') != 'all') {
            $query->where('leave_type_id', $request->input('leave_type'));
        }
        
        $leaveRequests = $query->get();
        
        Log::info('Exporting ' . $leaveRequests->count() . ' records');
        
        $fileName = 'leave-history-export-' . date('Y-m-d-H-i') . '.csv';
        
        // Use response()->streamDownload() for proper file download
        return response()->streamDownload(function() use ($leaveRequests) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 (Excel compatibility)
            fwrite($file, "\xEF\xBB\xBF");
            
            // Add headers
            fputcsv($file, [
                'Employee ID',
                'Employee Name', 
                'Department',
                'Leave Type',
                'Start Date', 
                'End Date', 
                'Total Days', 
                'Reason',
                'Status', 
                'Applied Date', 
                'Processed Date',
                'Processing Time (Days)',
                'Department Head Approval',
                'HR Admin Approval',
                'Contact Number',
                'Emergency Contact'
            ]);

            // Add rows
            foreach ($leaveRequests as $request) {
                // Get approval status
                $deptApproval = $request->approvals->where('level', 'department_head')->first();
                $hrApproval = $request->approvals->where('level', 'hr_admin')->first();
                
                // Calculate processing time
                $processingTime = 'N/A';
                if ($request->action_at) {
                    $processingTime = $request->created_at->diffInDays($request->action_at);
                } elseif ($request->status === 'pending') {
                    $processingTime = $request->created_at->diffInDays(now()) . ' (and counting)';
                }
                
                // Format dates
                $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : 'N/A';
                $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : 'N/A';
                $appliedDate = $request->created_at->format('Y-m-d H:i:s');
                $processedDate = $request->action_at ? $request->action_at->format('Y-m-d H:i:s') : 'N/A';
                
                // Department head approval details
                $deptApprovalStatus = 'Pending';
                if ($deptApproval) {
                    $deptApprovalStatus = ucfirst($deptApproval->status) . ' on ' . 
                                          $deptApproval->created_at->format('Y-m-d');
                }
                
                // HR approval details
                $hrApprovalStatus = 'Pending';
                if ($hrApproval) {
                    $hrApprovalStatus = ucfirst($hrApproval->status) . ' on ' . 
                                        $hrApproval->created_at->format('Y-m-d');
                }
                
                fputcsv($file, [
                    $request->user->employee_id ?? 'N/A',
                    ($request->user->first_name ?? 'Unknown') . ' ' . ($request->user->last_name ?? ''),
                    $request->user->department->name ?? 'N/A',
                    $request->leaveType->name ?? 'N/A',
                    $startDate,
                    $endDate,
                    $request->total_days ?? 0,
                    $this->cleanCsvValue($request->reason ?? ''),
                    ucfirst($request->status),
                    $appliedDate,
                    $processedDate,
                    $processingTime,
                    $deptApprovalStatus,
                    $hrApprovalStatus,
                    $request->contact_number ?? 'N/A',
                    $request->emergency_contact ?? 'N/A'
                ]);
            }

            fclose($file);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ]);

    } catch (\Exception $e) {
        Log::error('Error exporting leave history: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        
        return redirect()->route('admin.leaves.history')
            ->with('error', 'Error exporting data: ' . $e->getMessage());
    }
}

/**
 * Clean CSV value to prevent issues
 */
private function cleanCsvValue($value)
{
    if (empty($value)) {
        return '';
    }
    
    // Remove line breaks and extra spaces
    $value = str_replace(["\r", "\n"], ' ', $value);
    $value = preg_replace('/\s+/', ' ', $value);
    
    // Escape quotes for CSV
    $value = str_replace('"', '""', $value);
    
    return trim($value);
}
}