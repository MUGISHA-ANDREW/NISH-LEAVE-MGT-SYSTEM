<?php

namespace App\Http\Controllers\LeaveManagement\HrAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display HR Admin dashboard with real data
     */
    public function index()
    {
        $today = Carbon::today();
        $currentYear = Carbon::now()->year;

        $totalEmployees = User::count();

        $pendingApprovals = LeaveRequest::where('status', 'pending')->count();

        $onLeaveToday = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->distinct('user_id')
            ->count('user_id');

        $totalLeaveDaysApproved = LeaveRequest::where('status', 'approved')
            ->whereYear('created_at', $currentYear)
            ->sum('total_days');

        $totalAnnualLeaveDays = $totalEmployees * 21;

        $leaveUtilization = $totalAnnualLeaveDays > 0
            ? round(($totalLeaveDaysApproved / $totalAnnualLeaveDays) * 100)
            : 0;

        $recentLeaveRequests = LeaveRequest::with(['user.department', 'leaveType'])
            ->latest()
            ->limit(10)
            ->get();

        return view('modules.Leave-management.hr_admin.dashboard', [
            'stats' => [
                'total_employees' => $totalEmployees,
                'pending_approvals' => $pendingApprovals,
                'on_leave_today' => $onLeaveToday,
                'leave_utilization' => $leaveUtilization,
                'leave_utilization_raw' => $totalLeaveDaysApproved,
                'total_possible_days' => $totalAnnualLeaveDays,
                'average_processing_time' => $this->getAverageProcessingTime(),
                'approval_rate' => $this->getApprovalRate(),
            ],
            'recentLeaveRequests' => $recentLeaveRequests,
            'leaveStatistics' => $this->getLeaveStatistics(),
            'departmentDistribution' => $this->getDepartmentDistribution(),
            'monthlyTrend' => $this->getMonthlyTrend(),
        ]);
    }

    /**
     * Leave statistics by type
     */
    private function getLeaveStatistics()
    {
        $year = Carbon::now()->year;

        return LeaveType::all()->map(function ($type) use ($year) {
            return [
                'label' => $type->name,
                'count' => LeaveRequest::where('leave_type_id', $type->id)
                    ->whereYear('created_at', $year)
                    ->where('status', 'approved')
                    ->count(),
                'color' => $this->getColorForLeaveType($type->id),
            ];
        });
    }

    /**
     * Department distribution (SAFE & CORRECT)
     */
    private function getDepartmentDistribution()
    {
        try {
            $year = Carbon::now()->year;

            return Department::withCount('users')
                ->get()
                ->map(function ($department, $index) use ($year) {
                    $leaveCount = LeaveRequest::whereYear('created_at', $year)
                        ->whereHas('user', function ($q) use ($department) {
                            $q->where('department_id', $department->id);
                        })
                        ->count();

                    $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1'];

                    return [
                        'label' => $department->name,
                        'employee_count' => $department->users_count,
                        'leave_count' => $leaveCount,
                        'color' => $colors[$index % count($colors)],
                    ];
                });
        } catch (\Exception $e) {
            Log::error('Department distribution error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Monthly leave trend
     */
    private function getMonthlyTrend()
    {
        $year = Carbon::now()->year;
        $labels = [];
        $data = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create($year, $m)->format('M');

            $data[] = LeaveRequest::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->where('status', 'approved')
                ->count();
        }

        return compact('labels', 'data');
    }

    /**
     * Average processing time (hours)
     */
    private function getAverageProcessingTime()
    {
        $leaves = LeaveRequest::whereNotNull('approved_at')
            ->where('status', '!=', 'pending')
            ->get();

        if ($leaves->isEmpty()) {
            return 0;
        }

        return round(
            $leaves->sum(fn ($l) => $l->created_at->diffInHours($l->approved_at)) / $leaves->count()
        );
    }

    /**
     * Approval rate %
     */
    private function getApprovalRate()
    {
        $total = LeaveRequest::where('status', '!=', 'pending')->count();

        if ($total === 0) {
            return 0;
        }

        return round(
            (LeaveRequest::where('status', 'approved')->count() / $total) * 100
        );
    }

    /**
     * Chart colors
     */
    private function getColorForLeaveType($id)
    {
        $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1'];
        return $colors[$id % count($colors)];
    }

    /**
     * Status badge colors
     */
    private function getStatusColor($status)
    {
        return [
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray',
        ][$status] ?? 'gray';
    }

    /**
     * Get dashboard statistics (AJAX endpoint)
     */
    public function getDashboardStats()
    {
        $today = Carbon::today();
        $currentYear = Carbon::now()->year;

        return response()->json([
            'total_employees' => User::count(),
            'pending_approvals' => LeaveRequest::where('status', 'pending')->count(),
            'on_leave_today' => LeaveRequest::where('status', 'approved')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->distinct('user_id')
                ->count('user_id'),
            'leave_utilization' => $this->getLeaveUtilizationPercentage(),
        ]);
    }

    /**
     * Get recent leave requests (AJAX endpoint)
     */
    public function getRecentLeaveRequests()
    {
        $recentLeaveRequests = LeaveRequest::with(['user.department', 'leaveType'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json($recentLeaveRequests);
    }

    /**
     * Get chart data for dashboard (AJAX endpoint)
     */
    public function getChartData()
    {
        return response()->json([
            'leave_statistics' => $this->getLeaveStatistics(),
            'department_distribution' => $this->getDepartmentDistribution(),
            'monthly_trend' => $this->getMonthlyTrend(),
        ]);
    }

    /**
     * Reports page
     */
    public function reports(Request $request)
    {
        $departments = Department::all();
        $leaveTypes = LeaveType::all();

        // Determine date range
        $period = $request->input('period', '7');
        $endDate = Carbon::today();
        $startDate = match ($period) {
            '30' => Carbon::today()->subDays(30),
            '90' => Carbon::today()->subDays(90),
            '180' => Carbon::today()->subDays(180),
            'ytd' => Carbon::now()->startOfYear(),
            'custom' => $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::today()->subDays(7),
            default => Carbon::today()->subDays(7),
        };
        if ($period === 'custom' && $request->input('end_date')) {
            $endDate = Carbon::parse($request->input('end_date'));
        }

        // Base query
        $query = LeaveRequest::with(['user.department', 'leaveType'])
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        // Department filter
        if ($request->input('department') && $request->input('department') !== 'all') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department_id', $request->input('department'));
            });
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->get();

        // Stats
        $totalLeaveDays = $leaveRequests->sum('total_days');
        $avgLeaveDuration = $leaveRequests->count() > 0 ? round($leaveRequests->avg('total_days'), 1) : 0;
        $approvedCount = $leaveRequests->where('status', 'approved')->count();
        $approvalRate = $leaveRequests->count() > 0 ? round(($approvedCount / $leaveRequests->count()) * 100, 1) : 0;

        // Most common leave type
        $mostCommonType = $leaveRequests->groupBy('leave_type_id')->sortByDesc(function ($group) {
            return $group->count();
        })->keys()->first();
        $mostCommonLeaveType = $mostCommonType ? (LeaveType::find($mostCommonType)->name ?? 'N/A') : 'N/A';
        $mostCommonPercentage = $leaveRequests->count() > 0 && $mostCommonType
            ? round(($leaveRequests->where('leave_type_id', $mostCommonType)->count() / $leaveRequests->count()) * 100, 1)
            : 0;

        $stats = [
            'total_leave_days' => $totalLeaveDays,
            'avg_leave_duration' => $avgLeaveDuration,
            'most_common_leave_type' => $mostCommonLeaveType,
            'most_common_percentage' => $mostCommonPercentage,
            'approval_rate' => $approvalRate,
        ];

        // Monthly data for trends chart
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = LeaveRequest::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
            $monthlyData[$month->format('M Y')] = $count;
        }

        // Leave type distribution
        $leaveDistribution = [];
        foreach ($leaveTypes as $lt) {
            $count = $leaveRequests->where('leave_type_id', $lt->id)->count();
            if ($count > 0) {
                $leaveDistribution[$lt->name] = $count;
            }
        }

        // Department stats
        $departmentStats = [];
        foreach ($departments as $dept) {
            $deptLeaves = $leaveRequests->filter(function ($lr) use ($dept) {
                return $lr->user && $lr->user->department_id == $dept->id;
            });
            if ($deptLeaves->count() > 0) {
                $departmentStats[] = [
                    'name' => $dept->name,
                    'leave_days' => $deptLeaves->sum('total_days'),
                    'avg_duration' => round($deptLeaves->avg('total_days'), 1),
                ];
            }
        }

        // Recent leaves
        $recentLeaves = $leaveRequests->take(10);

        return view('modules.Leave-management.hr_admin.reports', compact(
            'stats', 'departments', 'leaveTypes', 'monthlyData',
            'leaveDistribution', 'departmentStats', 'recentLeaves'
        ));
    }

    /**
     * Download Reports & Analytics as PDF
     */
    public function downloadReportsPdf(Request $request)
    {
        try {
            $departments = Department::all();
            $leaveTypes = LeaveType::all();

            // Determine date range
            $period = $request->input('period', '7');
            $endDate = Carbon::today();
            $startDate = match ($period) {
                '30' => Carbon::today()->subDays(30),
                '90' => Carbon::today()->subDays(90),
                '180' => Carbon::today()->subDays(180),
                'ytd' => Carbon::now()->startOfYear(),
                'custom' => $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::today()->subDays(7),
                default => Carbon::today()->subDays(7),
            };
            if ($period === 'custom' && $request->input('end_date')) {
                $endDate = Carbon::parse($request->input('end_date'));
            }

            $periodLabels = [
                '7' => 'Last 7 Days',
                '30' => 'Last 30 Days',
                '90' => 'Last 3 Months',
                '180' => 'Last 6 Months',
                'ytd' => 'Year to Date',
                'custom' => 'Custom Range (' . $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y') . ')',
            ];
            $periodLabel = $periodLabels[$period] ?? 'Last 7 Days';

            // Base query
            $query = LeaveRequest::with(['user.department', 'leaveType'])
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

            $filterDeptName = 'All Departments';
            if ($request->input('department') && $request->input('department') !== 'all') {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('department_id', $request->input('department'));
                });
                $dept = Department::find($request->input('department'));
                $filterDeptName = $dept ? $dept->name : 'Selected';
            }

            $reportTypeLabels = [
                'summary' => 'Leave Summary',
                'department' => 'Department Analysis',
                'trends' => 'Leave Trends',
                'employee' => 'Employee Utilization',
            ];
            $reportType = $request->input('report_type', 'summary');
            $reportTypeLabel = $reportTypeLabels[$reportType] ?? 'Leave Summary';

            $leaveRequests = $query->orderBy('created_at', 'desc')->get();

            // Stats
            $totalLeaveDays = $leaveRequests->sum('total_days');
            $avgLeaveDuration = $leaveRequests->count() > 0 ? round($leaveRequests->avg('total_days'), 1) : 0;
            $approvedCount = $leaveRequests->where('status', 'approved')->count();
            $pendingCount = $leaveRequests->where('status', 'pending')->count();
            $rejectedCount = $leaveRequests->where('status', 'rejected')->count();
            $approvalRate = $leaveRequests->count() > 0 ? round(($approvedCount / $leaveRequests->count()) * 100, 1) : 0;

            $mostCommonType = $leaveRequests->groupBy('leave_type_id')->sortByDesc(function ($group) {
                return $group->count();
            })->keys()->first();
            $mostCommonLeaveType = $mostCommonType ? (LeaveType::find($mostCommonType)->name ?? 'N/A') : 'N/A';

            $stats = [
                'total_requests' => $leaveRequests->count(),
                'total_leave_days' => $totalLeaveDays,
                'avg_leave_duration' => $avgLeaveDuration,
                'approved' => $approvedCount,
                'pending' => $pendingCount,
                'rejected' => $rejectedCount,
                'approval_rate' => $approvalRate,
                'most_common_leave_type' => $mostCommonLeaveType,
            ];

            // Leave type distribution
            $leaveDistribution = [];
            foreach ($leaveTypes as $lt) {
                $count = $leaveRequests->where('leave_type_id', $lt->id)->count();
                if ($count > 0) {
                    $leaveDistribution[] = ['name' => $lt->name, 'count' => $count];
                }
            }

            // Department stats
            $departmentStats = [];
            foreach ($departments as $deptItem) {
                $deptLeaves = $leaveRequests->filter(function ($lr) use ($deptItem) {
                    return $lr->user && $lr->user->department_id == $deptItem->id;
                });
                if ($deptLeaves->count() > 0) {
                    $departmentStats[] = [
                        'name' => $deptItem->name,
                        'total_requests' => $deptLeaves->count(),
                        'leave_days' => $deptLeaves->sum('total_days'),
                        'avg_duration' => round($deptLeaves->avg('total_days'), 1),
                        'approved' => $deptLeaves->where('status', 'approved')->count(),
                        'pending' => $deptLeaves->where('status', 'pending')->count(),
                        'rejected' => $deptLeaves->where('status', 'rejected')->count(),
                    ];
                }
            }

            $filters = [
                'period' => $periodLabel,
                'department' => $filterDeptName,
                'report_type' => $reportTypeLabel,
            ];

            $pdf = Pdf::loadView('modules.Leave-management.hr_admin.reports-pdf', [
                'leaveRequests' => $leaveRequests,
                'stats' => $stats,
                'filters' => $filters,
                'leaveDistribution' => $leaveDistribution,
                'departmentStats' => $departmentStats,
            ])
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

            $filename = 'Reports_Analytics_' . Carbon::now()->format('Y-m-d_H-i') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error generating reports PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating PDF report: ' . $e->getMessage());
        }
    }

    /**
     * Settings page
     */
    public function settings()
    {
        $leaveTypes = LeaveType::all();
        $departments = Department::with('manager')->get();
        
        return view('modules.Leave-management.hr_admin.settings', compact('leaveTypes', 'departments'));
    }

    /**
     * Calculate leave utilization percentage
     */
    private function getLeaveUtilizationPercentage()
    {
        $currentYear = Carbon::now()->year;
        $totalEmployees = User::count();
        $totalLeaveDaysApproved = LeaveRequest::where('status', 'approved')
            ->whereYear('created_at', $currentYear)
            ->sum('total_days');
        
        $totalAnnualLeaveDays = $totalEmployees * 21;

        return $totalAnnualLeaveDays > 0
            ? round(($totalLeaveDaysApproved / $totalAnnualLeaveDays) * 100)
            : 0;
    }
}
