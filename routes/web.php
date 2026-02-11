<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\userManagement\UserController;
use App\Http\Controllers\userManagement\RoleController;
use App\Http\Controllers\LeaveManagement\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\LeaveManagement\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\LeaveManagement\DepartmentHead\DashboardController as DeptHeadDashboardController;
use App\Http\Controllers\LeaveManagement\DepartmentHead\LeaveApprovalController as DeptHeadLeaveApprovalController;
use App\Http\Controllers\LeaveManagement\HrAdmin\DashboardController as HrAdminDashboardController;
use App\Http\Controllers\LeaveManagement\HrAdmin\LeaveApprovalController as HrAdminLeaveApprovalController;
use App\Http\Controllers\LeaveManagement\LeaveTypeController;
use App\Http\Controllers\ApprovalWorkflow\ApprovalSummaryController;
use App\Http\Controllers\CalendarScheduling\CalendarController;
use App\Http\Controllers\CalendarScheduling\DepartmentCalendarController;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update.reset');

// Dashboard routes (protected)
Route::middleware('auth')->group(function () {
    
    // Password Change Routes
    Route::get('/change-password', [UserController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('password.update');
    
    // Employee Routes
    Route::prefix('employee')->group(function () {
        // Dashboard
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
        Route::get('/leave-balance', [EmployeeDashboardController::class, 'balance'])->name('employee.leave.balance');
        Route::get('/team-calendar', [CalendarController::class, 'index'])->name('employee.team.calendar');
        Route::get('/profile', [UserController::class, 'profile'])->name('employee.profile');
        Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('users.edit-profile');
        Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('users.update-profile');
        
        // Leave Management
        Route::get('/apply-leave', [EmployeeLeaveController::class, 'create'])->name('employee.leave.create');
        Route::post('/apply-leave', [EmployeeLeaveController::class, 'store'])->name('employee.leave.store');
        Route::get('/leave-history', [EmployeeLeaveController::class, 'index'])->name('employee.leave.history');
        Route::get('/leave-history/export', [EmployeeLeaveController::class, 'export'])->name('employee.leave.export');
        Route::get('/leave/{id}/edit', [EmployeeLeaveController::class, 'edit'])->name('employee.leave.edit');
        Route::get('/leave/{id}', [EmployeeLeaveController::class, 'show'])->name('employee.leave.show');
        Route::put('/leave/{id}', [EmployeeLeaveController::class, 'update'])->name('employee.leave.update');
        Route::post('/leave/{id}/retrieve', [EmployeeLeaveController::class, 'retrieve'])->name('employee.leave.retrieve');
        Route::post('/leave/{id}/cancel', [EmployeeLeaveController::class, 'cancel'])->name('employee.leave.cancel');
    });

    // Department Head Routes
    Route::prefix('head')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DeptHeadDashboardController::class, 'index'])->name('head.dashboard');
        Route::get('/dashboard-stats', [DeptHeadDashboardController::class, 'getDashboardStats'])->name('head.dashboard.stats');
        
        // Leave Actions (Approval/Rejection)
        Route::post('/approve/{id}', [DeptHeadDashboardController::class, 'approveLeave'])->name('head.approve');
        Route::post('/reject/{id}', [DeptHeadDashboardController::class, 'rejectLeave'])->name('head.reject');
        
        // Leave Management Pages
        Route::get('/pending-leaves', [DeptHeadLeaveApprovalController::class, 'pending'])->name('head.leaves.pending');
        Route::get('/leave-history', [DeptHeadLeaveApprovalController::class, 'history'])->name('head.leaves.history');
        Route::get('/leave/{id}', [DeptHeadLeaveApprovalController::class, 'show'])->name('head.leave.details');
        
        // Calendar
        Route::get('/team-calendar', [DepartmentCalendarController::class, 'index'])->name('head.team.calendar');
        Route::get('/calendar', [CalendarController::class, 'index'])->name('head.calendar');
        
        // Management Pages
        Route::get('/reports', [DeptHeadDashboardController::class, 'reports'])->name('head.reports');
        Route::get('/team-members', [DeptHeadDashboardController::class, 'teamMembers'])->name('head.team.members');
        Route::get('/leave-policies', [DeptHeadDashboardController::class, 'leavePolicies'])->name('head.leave.policies');
    });

    // HR Admin Routes
   // HR Admin Routes
Route::prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HrAdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/stats', [HrAdminDashboardController::class, 'getDashboardStats'])->name('admin.dashboard.stats');
    Route::get('/dashboard/recent-requests', [HrAdminDashboardController::class, 'getRecentLeaveRequests'])->name('admin.dashboard.recent-requests');
    Route::get('/dashboard/chart-data', [HrAdminDashboardController::class, 'getChartData'])->name('admin.dashboard.chart-data');
    Route::get('/reports', [HrAdminDashboardController::class, 'reports'])->name('admin.reports');
    Route::get('/settings', [HrAdminDashboardController::class, 'settings'])->name('admin.settings');
    
    // Employee Management
    Route::get('/employees', [UserController::class, 'index'])->name('admin.employees');
    Route::get('/employees/create', [UserController::class, 'create'])->name('admin.employees.create');
    Route::post('/employees', [UserController::class, 'store'])->name('admin.employees.store');
    Route::get('/employees/{id}/edit', [UserController::class, 'edit'])->name('admin.employees.edit');
    
    // Leave Management
    Route::get('/pending-approvals', [HrAdminLeaveApprovalController::class, 'pending'])->name('admin.leaves.pending');
    Route::get('/leave-history', [HrAdminLeaveApprovalController::class, 'history'])->name('admin.leaves.history');
    Route::get('/approval-summary', [ApprovalSummaryController::class, 'index'])->name('admin.approval.summary');
    
    // System Management
    Route::get('/departments', [UserController::class, 'departments'])->name('admin.departments');
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles');
    Route::get('/leave-types', [LeaveTypeController::class, 'index'])->name('admin.leave.types');

     Route::get('/leave-history', [HrAdminLeaveApprovalController::class, 'history'])->name('admin.leaves.history');
    
    Route::get('/leaves/pending', [HrAdminLeaveApprovalController::class, 'pending'])->name('leaves.pending');
    // In your admin routes group (inside prefix('admin'))
Route::post('/leaves/{id}/approve', [HrAdminLeaveApprovalController::class, 'approve'])->name('admin.approve');
Route::post('/leaves/{id}/reject', [HrAdminLeaveApprovalController::class, 'reject'])->name('admin.reject');
    // Actions
    Route::post('/leaves/{id}/approve', [HrAdminLeaveApprovalController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{id}/reject', [HrAdminLeaveApprovalController::class, 'reject'])->name('leaves.reject');
    Route::post('/leaves/approve-all', [HrAdminLeaveApprovalController::class, 'approveAll'])->name('leaves.approve-all');
    Route::post('/leaves/send-reminders', [HrAdminLeaveApprovalController::class, 'sendReminders'])->name('leaves.send-reminders');
    
    // History and details
    Route::get('/leaves/history', [HrAdminLeaveApprovalController::class, 'history'])->name('leaves.history');
    Route::get('/leaves/{id}/details', [HrAdminLeaveApprovalController::class, 'getLeaveDetails'])->name('leaves.details');
    
    // Export
    Route::get('/leaves/export', [HrAdminLeaveApprovalController::class, 'export'])->name('leaves.export');
     
        
        // Download report (file download)
        Route::get('/download-report', [HrAdminLeaveApprovalController::class, 'downloadReport'])->name('download-report');

      Route::get('/generate-report', [HrAdminLeaveApprovalController::class, 'generateReport'])->name('generate-report');
});

Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/help', [PageController::class, 'helpCenter'])->name('help');

    // User Management Routes (separate from admin)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    });

    // Shared Calendar Routes
    Route::prefix('calendar')->group(function () {
        Route::get('/', [CalendarController::class, 'index'])->name('calendar.index');
        Route::get('/department', [DepartmentCalendarController::class, 'index'])->name('calendar.department');
    });
    Route::post('/employees/bulk-status', [UserController::class, 'bulkStatusUpdate'])->name('admin.employees.bulk-status');
});

// Add this at the end of your routes, before the closing }) of middleware
Route::get('/debug/hr-pending', function() {
    $pendingRequests = \App\Models\LeaveRequest::with(['user', 'user.department', 'approvals'])
        ->where('status', 'pending')
        ->whereHas('approvals', function($q) {
            $q->where('level', 'department_head')
              ->where('status', 'approved');
        })
        ->whereDoesntHave('approvals', function($q) {
            $q->where('level', 'hr_admin');
        })
        ->get();
    
    dd([
        'total_pending' => $pendingRequests->count(),
        'requests' => $pendingRequests->map(function($req) {
            return [
                'id' => $req->id,
                'user' => $req->user->first_name . ' ' . $req->user->last_name,
                'department' => $req->user->department->name ?? null,
                'approvals_count' => $req->approvals->count(),
                'approvals' => $req->approvals->map(function($app) {
                    return [
                        'level' => $app->level,
                        'status' => $app->status,
                        'approver' => $app->approver->first_name ?? null
                    ];
                })
            ];
        })
    ]);
})->middleware('auth');


Route::get('/test-leave-workflow', function() {
    // Check if we have test data
    $testUser = \App\Models\User::where('email', 'like', '%test%@example.com')->first();
    if (!$testUser) {
        $testUser = \App\Models\User::first();
    }
    
    if (!$testUser) {
        return "No users found. Please create a user first.";
    }
    
    // Check if we have a leave type
    $leaveType = \App\Models\LeaveType::first();
    if (!$leaveType) {
        $leaveType = \App\Models\LeaveType::create([
            'name' => 'Annual Leave',
            'description' => 'Paid annual leave',
            'max_days' => 21
        ]);
    }
    
    // Check if user has a department with a manager
    $departmentHead = null;
    if ($testUser->department && $testUser->department->manager_id) {
        $departmentHead = \App\Models\User::find($testUser->department->manager_id);
    }
    
    if (!$departmentHead) {
        // Get or create department head role
        $deptHeadRole = \App\Models\Role::where('name', 'department_head')->first();
        if (!$deptHeadRole) {
            $deptHeadRole = \App\Models\Role::create([
                'name' => 'department_head',
                'description' => 'Department Head/Manager'
            ]);
        }
        
        // Create a test department head WITH ALL REQUIRED FIELDS
        $departmentHead = \App\Models\User::create([
            'first_name' => 'Department',
            'last_name' => 'Head',
            'email' => 'depthead@example.com',
            'password' => bcrypt('password'),
            'role_id' => $deptHeadRole->id,
            'employee_id' => 'NISH-DH001',
            'join_date' => now()->subYear(), // Required field
            'status' => 'active', // Required field
            'employment_type' => 'full_time', // Required field
        ]);
        
        // Assign as department manager if department exists
        if ($testUser->department) {
            $testUser->department->update(['manager_id' => $departmentHead->id]);
        }
    }
    
    // Create a test leave request
    $leaveRequest = \App\Models\LeaveRequest::create([
        'user_id' => $testUser->id,
        'leave_type_id' => $leaveType->id,
        'start_date' => now()->addDays(7),
        'end_date' => now()->addDays(9),
        'total_days' => 3,
        'reason' => 'Test leave request for HR approval workflow',
        'contact_number' => '1234567890',
        'status' => 'pending'
    ]);
    
    // Create department head approval
    \App\Models\Approval::create([
        'leave_request_id' => $leaveRequest->id,
        'approver_id' => $departmentHead->id,
        'level' => 'department_head',
        'status' => 'approved',
        'remarks' => 'Approved by department head for testing'
    ]);
    
    // Check HR pending query
    $hrPendingQuery = \App\Models\LeaveRequest::with(['approvals'])
        ->where('status', 'pending')
        ->whereHas('approvals', function($q) {
            $q->where('level', 'department_head')
              ->where('status', 'approved');
        })
        ->whereDoesntHave('approvals', function($q) {
            $q->where('level', 'hr_admin');
        })
        ->get();
    
    // Check total pending for stats
    $totalPending = \App\Models\LeaveRequest::where('status', 'pending')->count();
    $totalDeptHeadApproved = \App\Models\LeaveRequest::where('status', 'pending')
        ->whereHas('approvals', function($q) {
            $q->where('level', 'department_head')
              ->where('status', 'approved');
        })
        ->count();
    
    return [
        'message' => 'Test data created for HR approval workflow',
        'test_data' => [
            'employee' => $testUser->first_name . ' ' . $testUser->last_name,
            'department_head' => $departmentHead->first_name . ' ' . $departmentHead->last_name,
            'leave_request_id' => $leaveRequest->id,
            'department_head_approval_created' => 'Yes',
        ],
        'database_stats' => [
            'total_pending_requests' => $totalPending,
            'pending_with_dept_head_approval' => $totalDeptHeadApproved,
            'ready_for_hr_approval' => $hrPendingQuery->count(),
        ],
        'hr_pending_query_results' => [
            'count' => $hrPendingQuery->count(),
            'should_see_in_hr_pending' => $hrPendingQuery->count() > 0 ? 'YES' : 'NO',
            'requests_found' => $hrPendingQuery->pluck('id')
        ],
        'check_url' => url('/admin/leaves/pending'),
        'note' => 'Go to /admin/leaves/pending to see if this request appears'
    ];
});