<?php

namespace App\Http\Controllers\userManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start with all users
        $query = User::with(['role', 'department', 'supervisor']);
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('employee_id', 'LIKE', "%{$search}%")
                  ->orWhere('designation', 'LIKE', "%{$search}%"); // Changed from position to designation
            });
        }
        
        // Department filter
        if ($request->has('department') && $request->input('department') != 'all') {
            $query->where('department_id', $request->input('department'));
        }
        
        // Status filter
        if ($request->has('status') && $request->input('status') != 'all') {
            $query->where('status', $request->input('status'));
        }
        
        // Role filter
        if ($request->has('role') && $request->input('role') != 'all') {
            $query->where('role_id', $request->input('role')); // Simplified
        }
        
        // Paginate results
        $users = $query->orderBy('first_name')->paginate(10);
        
        // Get statistics
        $stats = [
            'total_employees' => User::count(),
            'active_employees' => User::where('status', 'active')->count(),
            'on_leave_today' => $this->getOnLeaveTodayCount(),
            'new_this_month' => User::whereMonth('join_date', Carbon::now()->month)
                                    ->whereYear('join_date', Carbon::now()->year)
                                    ->count(),
        ];
        
        // Get all departments for filter dropdown
        $departments = Department::all();
        
        // Get all roles for filter dropdown
        $roles = Role::all();
        
        return view('modules.user-management.index', compact('users', 'stats', 'departments', 'roles'));
    }

    /**
     * Get count of employees on leave today
     */
    private function getOnLeaveTodayCount()
    {
        try {
            return LeaveRequest::where('status', 'approved')
                ->whereDate('start_date', '<=', Carbon::today())
                ->whereDate('end_date', '>=', Carbon::today())
                ->distinct('user_id')
                ->count('user_id');
        } catch (\Exception $e) {
            Log::error('Error getting on leave count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get user status badge color
     */
    private function getUserStatusColor($status)
    {
        $colors = [
            'active' => 'green',
            'inactive' => 'gray',
            'suspended' => 'red',
            'on_leave' => 'orange'
        ];
        
        return $colors[$status] ?? 'gray';
    }

    public function profile()
    {
        $user = User::findOrFail(Auth::id());
        $user->load(['department', 'role']);
        
        $joinDate = $user->created_at ?? now();
        $yearsOfService = $joinDate->diffInYears(now());
        $monthsOfService = $joinDate->diffInMonths(now()) % 12;
        
        $leaveBalance = $this->calculateLeaveBalance($user);
        $performance = $this->getPerformanceRating($user);
        $completedProjects = $this->getCompletedProjectsCount($user);

        return view('modules.user-management.profile', compact(
            'user', 
            'yearsOfService', 
            'monthsOfService',
            'leaveBalance',
            'performance',
            'completedProjects'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $departments = Department::all();
        $supervisors = User::whereIn('role_id', function($query) {
            $query->select('id')->from('roles')->whereIn('name', ['department_head', 'supervisor', 'manager']);
        })->get();

        return view('modules.user-management.create', compact('roles', 'departments', 'supervisors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request data
        Log::info('Creating new user', $request->all());
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Employment Information
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation' => 'required|string|max:255', // Changed from position to designation
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'join_date' => 'required|date',
            'supervisor_id' => 'nullable|exists:users,id',
            
            // Account Settings
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        try {
            $userData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
                'department_id' => $validated['department_id'],
                'phone' => $validated['phone'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'emergency_contact' => $validated['emergency_contact'],
                'designation' => $validated['designation'], // Changed from position to designation
                'employment_type' => $validated['employment_type'],
                'join_date' => $validated['join_date'],
                'supervisor_id' => $validated['supervisor_id'],
                'status' => $validated['status'],
            ];

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $profilePicture = $request->file('profile_picture');
                
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $profilePicture->getClientOriginalExtension();
                
                // Store in profile_pictures directory
                $path = $profilePicture->storeAs('profile_pictures', $filename, 'public');
                $userData['profile_picture'] = $path;
            }

            // Create the user - employee_id will be auto-generated by the model boot method
            $user = User::create($userData);

            Log::info('User created successfully', ['user_id' => $user->id, 'employee_id' => $user->employee_id]);

            return redirect()->route('admin.employees')->with('success', 'User created successfully! Employee ID: ' . $user->employee_id);

        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the change password form
     */
    public function showChangePasswordForm()
    {
        return view('modules.user-management.change-password');
    }

    /**
     * Handle password change request
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = User::findOrFail(Auth::id());

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('users.profile')->with('success', 'Password changed successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['department', 'role', 'supervisor', 'leaveRequests'])->findOrFail($id);
        $leaveBalance = $this->calculateLeaveBalance($user);
        
        return view('modules.user-management.show', compact('user', 'leaveBalance'));
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(string $id)
    {
        $user = User::with(['department', 'role', 'supervisor'])->findOrFail($id);
        $roles = Role::all();
        $departments = Department::all();
        $supervisors = User::whereIn('role_id', function($query) {
            $query->select('id')->from('roles')->whereIn('name', ['department_head', 'supervisor', 'manager']);
        })->get();

        return view('modules.user-management.edit', compact('user', 'roles', 'departments', 'supervisors'));
    }

    /**
     * Update the user.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Employment Information
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation' => 'required|string|max:255', // Changed from position to designation
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'join_date' => 'required|date',
            'supervisor_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive,suspended',
            
            // Password (optional)
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            $userData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'role_id' => $validated['role_id'],
                'department_id' => $validated['department_id'],
                'phone' => $validated['phone'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'emergency_contact' => $validated['emergency_contact'],
                'designation' => $validated['designation'], // Changed from position to designation
                'employment_type' => $validated['employment_type'],
                'join_date' => $validated['join_date'],
                'supervisor_id' => $validated['supervisor_id'],
                'status' => $validated['status'],
            ];

            // Update password if provided
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                
                $profilePicture = $request->file('profile_picture');
                
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $profilePicture->getClientOriginalExtension();
                
                // Store in profile_pictures directory
                $path = $profilePicture->storeAs('profile_pictures', $filename, 'public');
                $userData['profile_picture'] = $path;
            }

            // Handle profile picture removal
            if ($request->has('remove_profile_picture')) {
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $userData['profile_picture'] = null;
            }

            $user->update($userData);

            Log::info('User updated successfully', ['user_id' => $user->id]);

            return redirect()->route('admin.employees')->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the user profile.
     */
    public function editProfile()
    {
        $user = User::with(['department', 'role', 'supervisor'])->findOrFail(Auth::id());
        $departments = Department::all(); // Added for dropdown
        
        return view('modules.user-management.edit-profile', compact('user', 'departments'));
    }

    /**
     * Update the user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255', // Added designation
            'department_id' => 'nullable|exists:departments,id', // Added department
            'bio' => 'nullable|string|max:1000', // Added bio
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $userData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'emergency_contact' => $validated['emergency_contact'],
                'designation' => $validated['designation'] ?? $user->designation,
                'department_id' => $validated['department_id'] ?? $user->department_id,
                'bio' => $validated['bio'] ?? $user->bio,
            ];

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                
                $profilePicture = $request->file('profile_picture');
                
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $profilePicture->getClientOriginalExtension();
                
                // Store in profile_pictures directory
                $path = $profilePicture->storeAs('profile_pictures', $filename, 'public');
                $userData['profile_picture'] = $path;
            }

            // Handle profile picture removal
            if ($request->has('remove_profile_picture')) {
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $userData['profile_picture'] = null;
            }

            $user->update($userData);

            return redirect()->route('users.profile')->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating profile: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Don't allow deleting yourself
            if ($user->id === Auth::id()) {
                return redirect()->back()->with('error', 'You cannot delete your own account!');
            }
            
            // Check if user has any leave requests before deleting
            $hasLeaveRequests = LeaveRequest::where('user_id', $id)->exists();
            if ($hasLeaveRequests) {
                return redirect()->back()->with('error', 'Cannot delete user with existing leave requests. Deactivate instead.');
            }
            
            // Delete profile picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            $user->delete();
            
            Log::info('User deleted', ['user_id' => $id, 'deleted_by' => Auth::id()]);
            
            return redirect()->route('admin.employees')->with('success', 'User deleted successfully!');
            
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $validated = $request->validate([
                'status' => 'required|in:active,inactive,suspended'
            ]);
            
            $user->status = $validated['status'];
            $user->save();
            
            Log::info('User status updated', ['user_id' => $id, 'new_status' => $user->status]);
            
            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully!',
                'status' => $user->status
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating user status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating user status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate leave balance for user
     */
    public function calculateLeaveBalance($user)
    {
        try {
            // Default annual leave days
            $annualLeaveDays = 21;
            
            // Get approved leave days for current year
            $usedLeaveDays = LeaveRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_days') ?? 0;
            
            // Calculate remaining leave balance
            $remainingBalance = max(0, $annualLeaveDays - $usedLeaveDays);
            
            return [
                'used' => $usedLeaveDays,
                'remaining' => $remainingBalance,
                'total' => $annualLeaveDays,
                'percentage' => $annualLeaveDays > 0 ? round(($usedLeaveDays / $annualLeaveDays) * 100) : 0
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating leave balance: ' . $e->getMessage());
            return [
                'used' => 0,
                'remaining' => 21,
                'total' => 21,
                'percentage' => 0
            ];
        }
    }

    /**
     * Get performance rating for user
     */
    private function getPerformanceRating($user)
    {
        // TODO: Implement actual performance rating logic
        // For now, return a default value
        return 'Excellent';
    }

    /**
     * Get completed projects count for user
     */
    private function getCompletedProjectsCount($user)
    {
        // TODO: Implement actual project counting logic
        // For now, return a default value
        return 12;
    }

    /**
     * Display departments page
     */
    public function departments()
    {
        $departments = Department::with(['manager', 'users'])->get();
        return view('modules.user-management.departments', compact('departments'));
    }

    /**
     * Bulk status update for employees
     */
    public function bulkStatusUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'status' => 'required|in:active,inactive,suspended'
            ]);

            User::whereIn('id', $validated['user_ids'])->update([
                'status' => $validated['status']
            ]);

            Log::info('Bulk status update', [
                'user_ids' => $validated['user_ids'], 
                'status' => $validated['status'],
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully for selected employees!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk status update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
}