<?php

namespace App\Http\Controllers\LeaveManagement\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function index()
{
    $user = Auth::user();
    $currentYear = date('Y');

    // Get leave balances
    $leaveBalances = $this->getLeaveBalances($user->id, $currentYear);
    
    // Get quick stats
    $quickStats = $this->getQuickStats($user->id, $currentYear);
    
    // Get upcoming leaves with approvals loaded
    $upcomingLeaves = $this->getUpcomingLeaves($user->id)->load('approvals');
    
    // Get recent applications with approvals loaded
    $recentApplications = $this->getRecentApplications($user->id)->load('approvals');

    // Add workflow status to each leave request
    $upcomingLeaves->each(function($leave) {
        $leave->workflow_status = $this->getLeaveStatusWithWorkflow($leave);
    });
    
    $recentApplications->each(function($leave) {
        $leave->workflow_status = $this->getLeaveStatusWithWorkflow($leave);
    });

    return view('modules.Leave-management.employee.dashboard', compact(
        'leaveBalances',
        'quickStats',
        'upcomingLeaves',
        'recentApplications'
    ));
}

    private function getLeaveBalances($userId, $year)
    {
        $leaveTypes = LeaveType::all();
        $balances = [];

        foreach ($leaveTypes as $type) {
            $usedDays = LeaveRequest::where('user_id', $userId)
                ->where('leave_type_id', $type->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $year)
                ->sum('total_days');

            $available = $type->max_days ? ($type->max_days - $usedDays) : null;
            $percentage = $type->max_days ? min(100, ($usedDays / $type->max_days) * 100) : 100;

            $balances[] = [
                'type' => $type,
                'used' => $usedDays,
                'available' => $available,
                'max_days' => $type->max_days,
                'percentage' => $percentage
            ];
        }

        return $balances;
    }

    private function getQuickStats($userId, $year)
    {
        // Available annual leave
        $annualLeave = LeaveType::where('name', 'Annual')->first();
        $usedAnnual = LeaveRequest::where('user_id', $userId)
            ->where('leave_type_id', $annualLeave->id)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('total_days');
        $availableAnnual = $annualLeave->max_days - $usedAnnual;

        // Pending requests
        $pendingRequests = LeaveRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        // Total used this year
        $totalUsed = LeaveRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('total_days');

        return [
            'available_leave' => $availableAnnual,
            'pending_requests' => $pendingRequests,
            'used_this_year' => $totalUsed
        ];
    }

    private function getUpcomingLeaves($userId)
    {
        return LeaveRequest::with('leaveType')
            ->where('user_id', $userId)
            ->where('start_date', '>=', today())
            ->whereIn('status', ['approved', 'pending'])
            ->orderBy('start_date')
            ->limit(3)
            ->get();
    }

    private function getRecentApplications($userId)
    {
        return LeaveRequest::with('leaveType')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function balance()
    {
        // Your existing balance method
        $user = Auth::user();
        $currentYear = date('Y');

        // Get all leave types
        $leaveTypes = LeaveType::all();
        $leaveBalances = [];
        $totalUsed = 0;
        $totalMaxDays = 0;

        // Calculate balances for each leave type
        foreach ($leaveTypes as $leaveType) {
            $usedDays = LeaveRequest::where('user_id', $user->id)
                ->where('leave_type_id', $leaveType->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $currentYear)
                ->sum('total_days');

            $available = $leaveType->max_days ? ($leaveType->max_days - $usedDays) : null;
            
            $leaveBalances[] = [
                'leave_type' => $leaveType,
                'used' => $usedDays,
                'available' => $available,
                'max_days' => $leaveType->max_days
            ];

            if ($leaveType->max_days) {
                $totalUsed += $usedDays;
                $totalMaxDays += $leaveType->max_days;
            }
        }

        // Calculate totals
        $totalRemaining = $totalMaxDays - $totalUsed;
        $totalAvailable = $totalMaxDays;

        // Get monthly usage data
        $monthlyUsage = $this->getMonthlyUsage($user->id, $currentYear);
        $maxMonthlyUsage = max(array_column($monthlyUsage, 'days')) ?: 1;

        // Get recent leave requests (all statuses for history)
        $recentLeaves = LeaveRequest::with('leaveType')
            ->where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->limit(5)
            ->get();

        // Calculate projections
        $projections = $this->calculateProjections($user->id, $currentYear, $leaveTypes);

        return view('modules.Leave-management.employee.leave-balance', compact(
            'leaveBalances',
            'totalUsed',
            'totalAvailable',
            'totalRemaining',
            'monthlyUsage',
            'maxMonthlyUsage',
            'recentLeaves',
            'projections'
        ));
    }

    private function getMonthlyUsage($userId, $year)
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $days = LeaveRequest::where('user_id', $userId)
                ->where('status', 'approved')
                ->whereYear('start_date', $year)
                ->whereMonth('start_date', $i)
                ->sum('total_days');

            $months[] = [
                'month' => date('M', mktime(0, 0, 0, $i, 1)),
                'days' => $days
            ];
        }

        return $months;
    }

    private function calculateProjections($userId, $year, $leaveTypes)
    {
        // Get annual leave type
        $annualLeave = $leaveTypes->where('name', 'Annual')->first();
        
        if (!$annualLeave) {
            return [
                'annual_balance' => 0,
                'carry_over' => 0,
                'recommended_usage' => 0
            ];
        }

        $usedAnnual = LeaveRequest::where('user_id', $userId)
            ->where('leave_type_id', $annualLeave->id)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('total_days');

        $remainingAnnual = $annualLeave->max_days - $usedAnnual;
        
        // Simple projection logic
        $monthsRemaining = 12 - date('n');
        $projectedBalance = max(0, $remainingAnnual);
        $carryOver = min(5, $projectedBalance); // Assume max 5 days carry over
        $recommendedUsage = max(0, $projectedBalance - $carryOver);

        return [
            'annual_balance' => $projectedBalance,
            'carry_over' => $carryOver,
            'recommended_usage' => $recommendedUsage
        ];
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