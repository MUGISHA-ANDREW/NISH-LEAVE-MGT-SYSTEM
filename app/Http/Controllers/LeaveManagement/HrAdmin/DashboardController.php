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
}
