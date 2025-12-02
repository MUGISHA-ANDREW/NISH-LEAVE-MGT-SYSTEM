<?php

namespace App\Http\Controllers\LeaveManagement\HrAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display HR Admin dashboard with real data
     */
    public function index()
    {
        // Get total employees
        $totalEmployees = User::count();
        
        // Get pending approvals (all departments)
        $pendingApprovals = LeaveRequest::where('status', 'pending')->count();
        
        // Get employees on leave today
        $onLeaveToday = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->distinct('user_id')
            ->count('user_id');
        
        // Get leave utilization (approximate calculation)
        $totalLeaveDaysApproved = LeaveRequest::where('status', 'approved')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_days') ?? 0;
        
        $totalAnnualLeaveDays = $totalEmployees * 21; // Assuming 21 days annual leave per employee
        $leaveUtilization = $totalAnnualLeaveDays > 0 
            ? round(($totalLeaveDaysApproved / $totalAnnualLeaveDays) * 100) 
            : 0;
        
        // Get recent leave requests (last 10)
        $recentLeaveRequests = LeaveRequest::with(['user', 'user.department', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Get leave statistics by month for current year
        $leaveStatistics = $this->getLeaveStatistics();
        
        // Get department distribution
        $departmentDistribution = $this->getDepartmentDistribution();
        
        // Get monthly trend data
        $monthlyTrend = $this->getMonthlyTrend();
        
        // Get quick stats for cards
        $stats = [
            'total_employees' => $totalEmployees,
            'pending_approvals' => $pendingApprovals,
            'on_leave_today' => $onLeaveToday,
            'leave_utilization' => $leaveUtilization,
            'leave_utilization_raw' => $totalLeaveDaysApproved,
            'total_possible_days' => $totalAnnualLeaveDays,
            'average_processing_time' => $this->getAverageProcessingTime(),
            'approval_rate' => $this->getApprovalRate(),
        ];

        return view('modules.leave-management.hr_admin.dashboard', compact(
            'stats',
            'recentLeaveRequests',
            'leaveStatistics',
            'departmentDistribution',
            'monthlyTrend'
        ));
    }

    /**
     * Get leave statistics for chart
     */
    private function getLeaveStatistics()
    {
        $currentYear = Carbon::now()->year;
        $statistics = [];
        
        // Get count of each leave type for current year
        $leaveTypes = LeaveType::all();
        
        foreach ($leaveTypes as $type) {
            $count = LeaveRequest::where('leave_type_id', $type->id)
                ->whereYear('created_at', $currentYear)
                ->where('status', 'approved')
                ->count();
            
            $statistics[] = [
                'label' => $type->name,
                'count' => $count,
                'color' => $this->getColorForLeaveType($type->id)
            ];
        }
        
        return $statistics;
    }

    /**
     * Get department distribution for chart
     */
    private function getDepartmentDistribution()
    {
        try {
            // Explicitly specify the table for created_at column
            $departments = Department::withCount(['users'])
                ->withCount(['leaveRequests' => function($query) {
                    $query->whereYear('leave_requests.created_at', Carbon::now()->year);
                }])
                ->get();
            
            $distribution = [];
            $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1'];
            $colorIndex = 0;
            
            foreach ($departments as $department) {
                $distribution[] = [
                    'label' => $department->name,
                    'employee_count' => $department->users_count ?? 0,
                    'leave_count' => $department->leave_requests_count ?? 0,
                    'color' => $colors[$colorIndex % count($colors)]
                ];
                $colorIndex++;
            }
            
            return $distribution;
        } catch (\Exception $e) {
            // Fallback if relationships don't exist yet
            \Log::error('Error getting department distribution: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get monthly trend data
     */
    private function getMonthlyTrend()
    {
        $currentYear = Carbon::now()->year;
        $months = [];
        $data = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthStart = Carbon::create($currentYear, $i, 1)->startOfMonth();
            $monthEnd = Carbon::create($currentYear, $i, 1)->endOfMonth();
            
            $count = LeaveRequest::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status', 'approved')
                ->count();
            
            $months[] = $monthStart->format('M');
            $data[] = $count;
        }
        
        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    /**
     * Get average processing time for leave requests
     */
    private function getAverageProcessingTime()
    {
        try {
            $processedLeaves = LeaveRequest::whereNotNull('approved_at')
                ->whereNotNull('created_at')
                ->where('status', '!=', 'pending')
                ->get();
            
            if ($processedLeaves->count() === 0) {
                return 0;
            }
            
            $totalHours = 0;
            foreach ($processedLeaves as $leave) {
                $hours = $leave->created_at->diffInHours($leave->approved_at);
                $totalHours += $hours;
            }
            
            return round($totalHours / $processedLeaves->count());
        } catch (\Exception $e) {
            \Log::error('Error calculating average processing time: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get approval rate percentage
     */
    private function getApprovalRate()
    {
        try {
            $totalProcessed = LeaveRequest::where('status', '!=', 'pending')->count();
            
            if ($totalProcessed === 0) {
                return 0;
            }
            
            $approvedCount = LeaveRequest::where('status', 'approved')->count();
            
            return round(($approvedCount / $totalProcessed) * 100);
        } catch (\Exception $e) {
            \Log::error('Error calculating approval rate: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Generate color for leave type (for charts)
     */
    private function getColorForLeaveType($typeId)
    {
        $colors = [
            '#3B82F6', // Blue
            '#10B981', // Green
            '#F59E0B', // Yellow
            '#EF4444', // Red
            '#8B5CF6', // Purple
            '#EC4899', // Pink
            '#6366F1', // Indigo
        ];
        
        return $colors[$typeId % count($colors)];
    }

    /**
     * Get status color for badge
     */
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray'
        ];
        
        return $colors[$status] ?? 'gray';
    }

    /**
     * Get dashboard stats via AJAX for real-time updates
     */
    public function getDashboardStats()
    {
        try {
            // Get total employees
            $totalEmployees = User::count();
            
            // Get pending approvals
            $pendingApprovals = LeaveRequest::where('status', 'pending')->count();
            
            // Get employees on leave today
            $onLeaveToday = LeaveRequest::where('status', 'approved')
                ->whereDate('start_date', '<=', Carbon::today())
                ->whereDate('end_date', '>=', Carbon::today())
                ->distinct('user_id')
                ->count('user_id');
            
            // Get leave utilization
            $totalLeaveDaysApproved = LeaveRequest::where('status', 'approved')
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_days') ?? 0;
            
            $totalAnnualLeaveDays = $totalEmployees * 21;
            $leaveUtilization = $totalAnnualLeaveDays > 0 
                ? round(($totalLeaveDaysApproved / $totalAnnualLeaveDays) * 100) 
                : 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_employees' => $totalEmployees,
                    'pending_approvals' => $pendingApprovals,
                    'on_leave_today' => $onLeaveToday,
                    'leave_utilization' => $leaveUtilization,
                    'average_processing_time' => $this->getAverageProcessingTime(),
                    'approval_rate' => $this->getApprovalRate(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting dashboard stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading dashboard stats'
            ], 500);
        }
    }

    /**
     * Get recent leave requests for AJAX
     */
    public function getRecentLeaveRequests()
    {
        try {
            $recentLeaveRequests = LeaveRequest::with(['user', 'user.department', 'leaveType'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'employee_name' => $request->user->name ?? 'Unknown',
                        'department' => $request->user->department->name ?? 'N/A',
                        'leave_type' => $request->leaveType->name ?? 'N/A',
                        'start_date' => $request->start_date->format('M d, Y'),
                        'end_date' => $request->end_date->format('M d, Y'),
                        'status' => $request->status,
                        'status_color' => $this->getStatusColor($request->status),
                        'applied_on' => $request->created_at->format('M d, Y'),
                        'duration' => $request->total_days,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $recentLeaveRequests
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting recent leave requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading recent requests'
            ], 500);
        }
    }

    /**
     * Get chart data for AJAX
     */
    public function getChartData()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'leave_statistics' => $this->getLeaveStatistics(),
                    'department_distribution' => $this->getDepartmentDistribution(),
                    'monthly_trend' => $this->getMonthlyTrend(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting chart data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading chart data'
            ], 500);
        }
    }

    // Reports method remains the same
   /**
 * Display reports with dynamic data
 */
public function reports(Request $request)
{
    try {
        // Get filter parameters
        $period = $request->input('period', '30'); // Default: last 30 days
        $departmentId = $request->input('department', 'all');
        $reportType = $request->input('report_type', 'summary');
        
        // Calculate date range
        $startDate = null;
        $endDate = now();
        
        switch ($period) {
            case '7':
                $startDate = now()->subDays(7);
                break;
            case '30':
                $startDate = now()->subDays(30);
                break;
            case '90':
                $startDate = now()->subDays(90);
                break;
            case '180':
                $startDate = now()->subDays(180);
                break;
            case 'ytd':
                $startDate = now()->startOfYear();
                break;
            case 'custom':
                $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->subDays(30);
                $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now();
                break;
            default:
                $startDate = now()->subDays(30);
        }
        
        // Base query
        $query = LeaveRequest::with(['user', 'user.department', 'leaveType']);
        
        // Apply date filter
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        // Apply department filter
        if ($departmentId !== 'all') {
            $query->whereHas('user', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }
        
        // Get all leave requests for the period
        $leaves = $query->get();
        
        // Calculate stats
        $totalLeaveDays = $leaves->sum('total_days');
        $avgLeaveDuration = $leaves->count() > 0 ? $leaves->avg('total_days') : 0;
        
        // Most common leave type
        $leaveTypeCounts = $leaves->groupBy('leave_type_id')->map->count();
        $mostCommonLeaveTypeId = $leaveTypeCounts->sortDesc()->keys()->first();
        $mostCommonLeaveType = $mostCommonLeaveTypeId ? 
            LeaveType::find($mostCommonLeaveTypeId)->name ?? 'N/A' : 'N/A';
        
        // Calculate most common percentage
        $mostCommonPercentage = 0;
        if ($leaves->count() > 0 && $mostCommonLeaveTypeId) {
            $mostCommonCount = $leaveTypeCounts->get($mostCommonLeaveTypeId, 0);
            $mostCommonPercentage = ($mostCommonCount / $leaves->count()) * 100;
        }
        
        // Approval rate
        $totalRequests = $leaves->count();
        $approvedRequests = $leaves->where('status', 'approved')->count();
        $approvalRate = $totalRequests > 0 ? ($approvedRequests / $totalRequests) * 100 : 0;
        
        // Monthly trend data (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('M Y');
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $monthCount = LeaveRequest::whereBetween('created_at', [$monthStart, $monthEnd])
                ->when($departmentId !== 'all', function($q) use ($departmentId) {
                    $q->whereHas('user', function($q2) use ($departmentId) {
                        $q2->where('department_id', $departmentId);
                    });
                })
                ->count();
            
            $monthlyData[$monthKey] = $monthCount;
        }
        
        // Leave type distribution
        $leaveDistribution = [];
        $allLeaveTypes = LeaveType::all();
        foreach ($allLeaveTypes as $type) {
            $count = $leaves->where('leave_type_id', $type->id)->count();
            if ($count > 0) {
                $leaveDistribution[$type->name] = $count;
            }
        }
        
        // If no distribution data, add sample for chart
        if (empty($leaveDistribution)) {
            $leaveDistribution = [
                'Annual' => 0,
                'Sick' => 0,
                'Emergency' => 0,
                'Other' => 0
            ];
        }
        
        // Department stats
        $departmentStats = [];
        $allDepartments = Department::all();
        foreach ($allDepartments as $dept) {
            $deptLeaves = $leaves->filter(function($leave) use ($dept) {
                return $leave->user && $leave->user->department_id == $dept->id;
            });
            
            $departmentStats[] = [
                'name' => $dept->name,
                'leave_days' => $deptLeaves->sum('total_days'),
                'avg_duration' => $deptLeaves->count() > 0 ? $deptLeaves->avg('total_days') : 0
            ];
        }
        
        // Recent leaves for table
        $recentLeaves = $query->orderBy('created_at', 'desc')->limit(10)->get();
        
        return view('modules.leave-management.hr_admin.reports', [
            'stats' => [
                'total_leave_days' => $totalLeaveDays,
                'avg_leave_duration' => round($avgLeaveDuration, 1),
                'most_common_leave_type' => $mostCommonLeaveType,
                'most_common_percentage' => round($mostCommonPercentage, 1),
                'approval_rate' => round($approvalRate, 1),
            ],
            'monthlyData' => $monthlyData,
            'leaveDistribution' => $leaveDistribution,
            'departmentStats' => $departmentStats,
            'recentLeaves' => $recentLeaves,
            'departments' => $allDepartments,
            'leaveTypes' => $allLeaveTypes,
            'filters' => [
                'period' => $period,
                'department' => $departmentId,
                'report_type' => $reportType,
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error generating reports: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        
        // Fallback data
        $allDepartments = Department::orderBy('name')->get();
        $allLeaveTypes = LeaveType::all();
        
        return view('modules.leave-management.hr_admin.reports', [
            'stats' => [
                'total_leave_days' => 0,
                'avg_leave_duration' => 0,
                'most_common_leave_type' => 'N/A',
                'most_common_percentage' => 0,
                'approval_rate' => 0,
            ],
            'monthlyData' => [],
            'leaveDistribution' => [],
            'departmentStats' => [],
            'recentLeaves' => collect(),
            'departments' => $allDepartments,
            'leaveTypes' => $allLeaveTypes,
            'filters' => $request->all(),
        ])->with('error', 'Error loading reports. Please check your data.');
    }
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}