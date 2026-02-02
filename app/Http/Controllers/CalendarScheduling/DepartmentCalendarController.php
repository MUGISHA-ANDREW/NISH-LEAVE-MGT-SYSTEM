<?php

namespace App\Http\Controllers\CalendarScheduling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;

class DepartmentCalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $user = Auth::user();
        $department = $user->department;
        
        if (!$department) {
            abort(404, 'Department not found');
        }

        // Get team members (excluding the head)
        $teamMembers = User::where('department_id', $department->id)
            ->where('id', '!=', $user->id)
            ->get();

        // Get team stats - FIXED calculations
        $teamSize = $teamMembers->count();

        $onLeaveToday = LeaveRequest::whereIn('user_id', $teamMembers->pluck('id'))
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();

        $upcomingLeavesCount = LeaveRequest::whereIn('user_id', $teamMembers->pluck('id'))
            ->where('status', 'approved')
            ->whereDate('start_date', '>', today())
            ->whereDate('start_date', '<=', today()->addDays(30))
            ->count();

        $stats = [
            'team_size' => $teamSize,
            'on_leave_today' => $onLeaveToday,
            'available_today' => $teamSize - $onLeaveToday,
            'upcoming_leaves' => $upcomingLeavesCount
        ];

        // Get upcoming leaves
        $upcomingLeaves = LeaveRequest::with(['user', 'leaveType'])
            ->whereIn('user_id', $teamMembers->pluck('id'))
            ->where('status', 'approved')
            ->whereDate('start_date', '>=', today())
            ->whereDate('start_date', '<=', today()->addDays(30))
            ->orderBy('start_date')
            ->take(10)
            ->get();

        // Monthly summary - FIXED calculation
        $monthlySummary = [];
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        foreach ($teamMembers as $member) {
            $leaveDays = LeaveRequest::where('user_id', $member->id)
                ->where('status', 'approved')
                ->where(function($query) use ($currentMonthStart, $currentMonthEnd) {
                    $query->whereBetween('start_date', [$currentMonthStart, $currentMonthEnd])
                          ->orWhereBetween('end_date', [$currentMonthStart, $currentMonthEnd])
                          ->orWhere(function($q) use ($currentMonthStart, $currentMonthEnd) {
                              $q->where('start_date', '<=', $currentMonthStart)
                                ->where('end_date', '>=', $currentMonthEnd);
                          });
                })
                ->get()
                ->sum(function($leave) use ($currentMonthStart, $currentMonthEnd) {
                    $start = max($leave->start_date, $currentMonthStart);
                    $end = min($leave->end_date, $currentMonthEnd);
                    return $start->diffInDays($end) + 1;
                });

            $monthlySummary[] = [
                'employee_name' => $member->name,
                'leave_days' => $leaveDays,
                'status' => $leaveDays > 0 ? 'On Leave' : 'Available',
                'status_color' => $leaveDays > 0 ? 'yellow' : 'green'
            ];
        }

        return view('modules.Leave-management.department_head.team-calendar', compact(
            'department', 'stats', 'upcomingLeaves', 'monthlySummary'
        ));
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

    public function getTeamLeaves(Request $request)
{
    $user = Auth::user();
    $year = $request->get('year', now()->year);
    $month = $request->get('month', now()->month);
    
    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = Carbon::create($year, $month, 1)->endOfMonth();
    
    $leaves = LeaveRequest::with(['user', 'leaveType'])
        ->whereHas('user', function($query) use ($user) {
            $query->where('department_id', $user->department_id);
        })
        ->where('status', 'approved')
        ->where(function($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($q) use ($startDate, $endDate) {
                      $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                  });
        })
        ->get()
        ->map(function($leave) {
            return [
                'title' => $leave->user->name . ' - ' . ($leave->leaveType->name ?? 'Leave'),
                'start' => $leave->start_date->format('Y-m-d'),
                'end' => $leave->end_date->format('Y-m-d'),
                'color' => '#ef4444', // red color for leaves
                'allDay' => true
            ];
        });
    
    return response()->json($leaves);
}
}
