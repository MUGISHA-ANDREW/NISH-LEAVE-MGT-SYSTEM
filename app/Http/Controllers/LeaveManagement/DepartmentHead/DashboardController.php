<?php

namespace App\Http\Controllers\LeaveManagement\DepartmentHead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display department head dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $department = $user->department;
        
        // Your existing dashboard logic
        $teamStats = [
            'total_members' => User::where('department_id', $department->id)->count(),
            'pending_approvals' => LeaveRequest::whereHas('user', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })->where('status', 'pending')->count(),
            'on_leave_today' => 0, // Add your logic
            'approval_rate' => 85, // Add your logic
        ];

        $pendingApprovals = LeaveRequest::with(['user', 'leaveType'])
            ->whereHas('user', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Add other data you need for the dashboard
        $teamLeaveOverview = [];
        $upcomingTeamLeaves = [];

        return view('modules.leave-management.department_head.dashboard', compact(
            'department', 'teamStats', 'pendingApprovals', 'teamLeaveOverview', 'upcomingTeamLeaves'
        ));
    }

    /**
     * Approve a leave request
     */
   public function approveLeave(Request $request, $id)
{
    \Log::info('=== APPROVE LEAVE METHOD STARTED ===');
    \Log::info('Leave ID: ' . $id);
    \Log::info('User ID: ' . Auth::id());
    \Log::info('Request Data: ', $request->all());
    
    try {
        \Log::info('Looking for leave request with ID: ' . $id);
        $leaveRequest = LeaveRequest::with('user')->find($id);
        
        if (!$leaveRequest) {
            \Log::error('Leave request not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Leave request not found.'
            ], 404);
        }
        
        \Log::info('Leave request found:', [
            'id' => $leaveRequest->id,
            'user_id' => $leaveRequest->user_id,
            'user_department_id' => $leaveRequest->user->department_id,
            'status' => $leaveRequest->status
        ]);

        $user = Auth::user();
        \Log::info('Current user:', [
            'id' => $user->id,
            'department_id' => $user->department_id,
            'name' => $user->name
        ]);
        
        // Check if the leave request belongs to user's department
        if ($leaveRequest->user->department_id !== $user->department_id) {
            \Log::error('Department mismatch:', [
                'leave_user_department' => $leaveRequest->user->department_id,
                'current_user_department' => $user->department_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Access denied. This leave request does not belong to your department.'
            ], 403);
        }

        // Check if already processed
        if ($leaveRequest->status !== 'pending') {
            \Log::warning('Leave already processed:', [
                'current_status' => $leaveRequest->status
            ]);
            return response()->json([
                'success' => false,
                'message' => 'This leave request has already been processed.'
            ], 400);
        }

        // Check if department head already approved this
        $existingApproval = \App\Models\Approval::where('leave_request_id', $id)
            ->where('level', 'department_head')
            ->first();
            
        if ($existingApproval) {
            \Log::warning('Department head already approved this request:', [
                'approval_status' => $existingApproval->status
            ]);
            return response()->json([
                'success' => false,
                'message' => 'You have already processed this leave request.'
            ], 400);
        }

        \Log::info('Creating department head approval record');
        
        // Create approval record instead of updating leave request status
        $approval = \App\Models\Approval::create([
            'leave_request_id' => $leaveRequest->id,
            'approver_id' => $user->id,
            'level' => 'department_head',
            'status' => 'approved',
            'remarks' => $request->head_remarks ?? 'Approved by department head - Waiting for HR approval'
        ]);
        
        \Log::info('Approval record created:', [
            'approval_id' => $approval->id,
            'level' => $approval->level,
            'status' => $approval->status
        ]);

        // IMPORTANT: Do NOT change leave request status here
        // Leave it as 'pending' for HR approval
        // $leaveRequest->status remains 'pending'
        
        \Log::info('=== APPROVE LEAVE METHOD COMPLETED SUCCESSFULLY ===');
        return response()->json([
            'success' => true,
            'message' => 'Leave request approved by department head. Now waiting for HR approval.'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error in approveLeave method: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error approving leave request: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Reject a leave request
     */
    public function rejectLeave(Request $request, $id)
    {
        \Log::info('=== REJECT LEAVE METHOD STARTED ===');
        \Log::info('Leave ID: ' . $id);
        \Log::info('User ID: ' . Auth::id());
        \Log::info('Request Data: ', $request->all());
        
        try {
            \Log::info('Looking for leave request with ID: ' . $id);
            $leaveRequest = LeaveRequest::with('user')->find($id);
            
            if (!$leaveRequest) {
                \Log::error('Leave request not found with ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Leave request not found.'
                ], 404);
            }
            
            \Log::info('Leave request found:', [
                'id' => $leaveRequest->id,
                'user_id' => $leaveRequest->user_id,
                'user_department_id' => $leaveRequest->user->department_id,
                'status' => $leaveRequest->status
            ]);

            $user = Auth::user();
            \Log::info('Current user:', [
                'id' => $user->id,
                'department_id' => $user->department_id,
                'name' => $user->name
            ]);
            
            // Check if the leave request belongs to user's department
            if ($leaveRequest->user->department_id !== $user->department_id) {
                \Log::error('Department mismatch:', [
                    'leave_user_department' => $leaveRequest->user->department_id,
                    'current_user_department' => $user->department_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. This leave request does not belong to your department.'
                ], 403);
            }

            // Check if already processed
            if ($leaveRequest->status !== 'pending') {
                \Log::warning('Leave already processed:', [
                    'current_status' => $leaveRequest->status
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'This leave request has already been processed.'
                ], 400);
            }

            // Validate rejection reason
            \Log::info('Validating rejection reason');
            
            // For JSON requests, we need to validate differently
            $reason = $request->input('reason');
            if (empty($reason) || strlen(trim($reason)) < 5) {
                \Log::error('Invalid rejection reason:', ['reason' => $reason]);
                return response()->json([
                    'success' => false,
                    'message' => 'Rejection reason is required and must be at least 5 characters long.'
                ], 422);
            }

            \Log::info('Validation passed, reason: ' . $reason);

            \Log::info('Attempting to update leave request to rejected status');
            
            // Update the leave request
            $updateData = [
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_at' => Carbon::now(),
                'head_remarks' => $reason
            ];
            
            \Log::info('Update data:', $updateData);
            
            $result = $leaveRequest->update($updateData);
            
            \Log::info('Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
            
            if ($result) {
                $updatedLeave = LeaveRequest::find($id);
                \Log::info('After update status: ' . $updatedLeave->status);
                
                \Log::info('=== REJECT LEAVE METHOD COMPLETED SUCCESSFULLY ===');
                return response()->json([
                    'success' => true,
                    'message' => 'Leave request rejected successfully.'
                ]);
            } else {
                \Log::error('Update operation returned false');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update leave request.'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Error in rejectLeave method: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get leave details
     */
    public function getLeaveDetails($id)
    {
        try {
            $leaveRequest = LeaveRequest::with(['user', 'leaveType'])->findOrFail($id);
            
            // Check if the leave request belongs to user's department
            if ($leaveRequest->user->department_id !== Auth::user()->department_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $leaveRequest
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Leave request not found.'
            ], 404);
        }
    }

    /**
     * Get dashboard stats
     */
    public function getDashboardStats()
    {
        $user = Auth::user();
        $department = $user->department;
        
        $stats = [
            'total_members' => User::where('department_id', $department->id)->count(),
            'pending_approvals' => LeaveRequest::whereHas('user', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })->where('status', 'pending')->count(),
            'on_leave_today' => 0,
            'approval_rate' => 85,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    
    public function reports()
{
    $user = Auth::user();
    $department = $user->department;
    
    // Sample report data (replace with actual database queries)
    $reportData = [
        'total_leave_days' => 45,
        'approval_rate' => 85,
        'avg_processing_time' => 24,
        'team_coverage' => 92,
        'avg_response_time' => 12,
        'pending_applications' => 3
    ];

    // Sample team stats
    $teamStats = [
        [
            'name' => 'John Doe',
            'position' => 'Senior Developer',
            'leave_taken' => 12,
            'balance' => 18
        ],
        [
            'name' => 'Jane Smith',
            'position' => 'Project Manager',
            'leave_taken' => 8,
            'balance' => 22
        ],
        [
            'name' => 'Mike Johnson',
            'position' => 'QA Engineer',
            'leave_taken' => 15,
            'balance' => 15
        ]
    ];

    // Sample detailed report
    $detailedReport = [
        [
            'employee_name' => 'John Doe',
            'leave_type' => 'Annual Leave',
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(7),
            'duration' => 4,
            'status' => 'approved',
            'applied_on' => now()->subDays(15),
            'processed_on' => now()->subDays(13)
        ],
        [
            'employee_name' => 'Jane Smith',
            'leave_type' => 'Sick Leave',
            'start_date' => now()->subDays(5),
            'end_date' => now()->subDays(4),
            'duration' => 2,
            'status' => 'approved',
            'applied_on' => now()->subDays(5),
            'processed_on' => now()->subDays(5)
        ]
    ];

    return view('modules.leave-management.department_head.reports', compact(
        'department', 'reportData', 'teamStats', 'detailedReport'
    ));
}

   public function teamMembers()
{
    $user = Auth::user();
    $department = $user->department;
    
    // Get team members in the department
    $teamMembers = User::where('department_id', $department->id)
        ->where('id', '!=', $user->id) // Exclude the department head
        ->orderBy('name')
        ->paginate(10);

    // Add additional data to team members
    $teamMembers->getCollection()->transform(function ($member) {
        // Check if member is on leave today
        $member->is_on_leave = LeaveRequest::where('user_id', $member->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->exists();
        
        // Get last leave date
        $lastLeave = LeaveRequest::where('user_id', $member->id)
            ->where('status', 'approved')
            ->orderBy('end_date', 'desc')
            ->first();
        
        $member->last_leave = $lastLeave ? $lastLeave->end_date : null;
        
        // Default leave balance (you can replace this with actual logic)
        $member->leave_balance = 21; // Default annual leave
        
        return $member;
    });

    // Get team stats
    $teamStats = [
        'total_members' => User::where('department_id', $department->id)->count() - 1, // Exclude head
        'on_leave_today' => LeaveRequest::whereHas('user', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })->where('status', 'approved')
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->count(),
        'pending_approvals' => LeaveRequest::whereHas('user', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })->where('status', 'pending')->count(),
    ];

    // Get upcoming leaves
    $upcomingLeaves = LeaveRequest::with(['user', 'leaveType'])
        ->whereHas('user', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->where('status', 'approved')
        ->whereDate('start_date', '>=', now())
        ->orderBy('start_date')
        ->take(5)
        ->get();

    return view('modules.leave-management.department_head.team-members', compact(
        'department', 'teamMembers', 'teamStats', 'upcomingLeaves'
    ));
}
public function leavePolicies()
{
    $user = Auth::user();
    $department = $user->department;
    
    // Get leave types - remove the is_active condition since the column doesn't exist
    $leaveTypes = \App\Models\LeaveType::orderBy('name')->get();

    // Get team stats
    $teamStats = [
        'total_members' => User::where('department_id', $department->id)->count() - 1, // Exclude head
        'pending_approvals' => LeaveRequest::whereHas('user', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })->where('status', 'pending')->count(),
        'approved_this_month' => LeaveRequest::whereHas('user', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })->where('status', 'approved')
        ->whereMonth('created_at', now()->month)
        ->count(),
        'rejected_this_month' => LeaveRequest::whereHas('user', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })->where('status', 'rejected')
        ->whereMonth('created_at', now()->month)
        ->count(),
    ];

    return view('modules.leave-management.department_head.leave-policies', compact(
        'department', 'leaveTypes', 'teamStats'
    ));
}

// Add this method to your DashboardController class
private function getLeaveStatusWithWorkflow($leaveRequest)
{
    // Check if approvals are loaded
    if (!$leaveRequest->relationLoaded('approvals')) {
        $leaveRequest->load('approvals');
    }
    
    $status = $leaveRequest->status;
    $deptHeadApproval = $leaveRequest->approvals->where('level', 'department_head')->first();
    $hrApproval = $leaveRequest->approvals->where('level', 'hr_admin')->first();
    
    if ($status == 'pending') {
        if (!$deptHeadApproval) {
            return [
                'status' => 'pending',
                'badge_class' => 'bg-yellow-100 text-yellow-800',
                'text' => 'Pending Department Head',
                'progress' => 0
            ];
        } elseif ($deptHeadApproval && $deptHeadApproval->status == 'approved' && !$hrApproval) {
            return [
                'status' => 'pending',
                'badge_class' => 'bg-blue-100 text-blue-800',
                'text' => 'Pending HR Approval',
                'progress' => 50
            ];
        } elseif ($deptHeadApproval && $deptHeadApproval->status == 'rejected') {
            return [
                'status' => 'rejected',
                'badge_class' => 'bg-red-100 text-red-800',
                'text' => 'Rejected by Department Head',
                'progress' => 0
            ];
        }
    }
    
    // Return default status
    return [
        'status' => $status,
        'badge_class' => match($status) {
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-yellow-100 text-yellow-800'
        },
        'text' => ucfirst($status),
        'progress' => $status == 'approved' ? 100 : 0
    ];
}
    
}

