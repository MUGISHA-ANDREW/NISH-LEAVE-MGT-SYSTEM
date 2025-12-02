@extends('layouts.app')

@section('title', 'Employee Management - Nish Auto Limited')
@section('page-title', 'Employee Management')

@php
    $isAdmin = true;
    use App\Models\LeaveRequest;
    use Carbon\Carbon;
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Employee Management</h2>
                <p class="text-gray-600 mt-1">Manage all employees and their information</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search Form -->
                <form method="GET" action="{{ route('admin.employees') }}" class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search employees..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64">
                </form>
                
                <!-- Add Employee Button -->
                <a href="{{ route('admin.employees.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Add Employee</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Employees -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_employees']) }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-users mr-1"></i>All employees
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Employees -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['active_employees']) }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-check-circle mr-1"></i>
                        {{ $stats['total_employees'] > 0 ? round(($stats['active_employees'] / $stats['total_employees']) * 100, 1) : 0 }}% active
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- On Leave -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">On Leave Today</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['on_leave_today']) }}</p>
                    <p class="text-xs text-orange-600 mt-1">
                        <i class="fas fa-calendar-day mr-1"></i>{{ Carbon::now()->format('M d, Y') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-umbrella-beach text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- New This Month -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">New This Month</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['new_this_month']) }}</p>
                    <p class="text-xs text-purple-600 mt-1">
                        <i class="fas fa-user-plus mr-1"></i>{{ Carbon::now()->format('M Y') }} hires
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-plus text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.employees') }}" class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Department Filter -->
                <select name="department" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                
                <!-- Status Filter -->
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                
                <!-- Role Filter -->
                <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex space-x-2">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 font-medium flex items-center space-x-2">
                    <i class="fas fa-filter"></i>
                    <span>Apply Filters</span>
                </button>
                <a href="{{ route('admin.employees') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-200 font-medium flex items-center space-x-2">
                    <i class="fas fa-sync-alt"></i>
                    <span>Clear All</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span>Employee</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join Date</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Balance</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        @php
                            // Calculate leave balance for this user
                            $leaveBalance = app()->make('App\Http\Controllers\UserManagement\UserController')->calculateLeaveBalance($user);
                            $percentage = $leaveBalance['percentage'];
                            $remaining = $leaveBalance['remaining'];
                            $total = $leaveBalance['total'];
                            
                            // Determine leave bar color
                            $leaveColor = $percentage < 30 ? 'green' : ($percentage < 70 ? 'yellow' : 'red');
                            
                            // Get status color
                            $statusColor = 'green';
                            $statusText = 'Active';
                            $isOnLeave = false;
                            
                            // Check if user is on leave today
                            $onLeave = LeaveRequest::where('user_id', $user->id)
                                ->where('status', 'approved')
                                ->whereDate('start_date', '<=', Carbon::today())
                                ->whereDate('end_date', '>=', Carbon::today())
                                ->exists();
                            
                            if ($onLeave) {
                                $statusColor = 'orange';
                                $statusText = 'On Leave';
                                $isOnLeave = true;
                            } elseif ($user->status == 'inactive') {
                                $statusColor = 'gray';
                                $statusText = 'Inactive';
                            } elseif ($user->status == 'suspended') {
                                $statusColor = 'red';
                                $statusText = 'Suspended';
                            }
                            
                            // Get initials for avatar
                            $initials = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));
                            
                            // Get department icon
                            $deptIcon = 'fa-building';
                            if ($user->department) {
                                $deptName = strtolower($user->department->name);
                                if (str_contains($deptName, 'assembly')) $deptIcon = 'fa-cogs';
                                elseif (str_contains($deptName, 'mechanical')) $deptIcon = 'fa-wrench';
                                elseif (str_contains($deptName, 'electrical')) $deptIcon = 'fa-bolt';
                                elseif (str_contains($deptName, 'sales')) $deptIcon = 'fa-chart-line';
                                elseif (str_contains($deptName, 'hr')) $deptIcon = 'fa-users';
                                elseif (str_contains($deptName, 'finance')) $deptIcon = 'fa-dollar-sign';
                                elseif (str_contains($deptName, 'it')) $deptIcon = 'fa-laptop-code';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <input type="checkbox" class="employee-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3" value="{{ $user->id }}">
                                    <div class="flex items-center space-x-3">
                                        @if($user->profile_picture)
                                            <img src="{{ Storage::url($user->profile_picture) }}" 
                                                 alt="{{ $user->first_name }}" 
                                                 class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-blue-600 font-semibold">{{ $initials }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $user->first_name }} {{ $user->last_name }}
                                                @if($user->role && in_array($user->role->name, ['department_head', 'admin', 'hr_admin']))
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                                        <i class="fas fa-user-tie mr-1"></i>
                                                        {{ ucfirst(str_replace('_', ' ', $user->role->name)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $user->employee_id }}</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <i class="fas {{ $deptIcon }} text-blue-500"></i>
                                    <span class="text-sm text-gray-900">{{ $user->department->name ?? 'No Department' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->position ?? 'Not specified' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 text-xs rounded-full font-medium">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->join_date ? Carbon::parse($user->join_date)->format('M d, Y') : 'Not set' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $leaveColor }}-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-900">{{ $remaining }}/{{ $total }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('users.profile', $user->id) }}" class="text-blue-600 hover:text-blue-900 action-button" title="View Profile">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.employees.edit', $user->id) }}" class="text-green-600 hover:text-green-900 action-button" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="text-orange-600 hover:text-orange-900 action-button leave-history-btn" data-user-id="{{ $user->id }}" title="Leave History">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 action-button" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fas fa-users text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500">No employees found</p>
                                <a href="{{ route('admin.employees.create') }}" class="text-blue-600 hover:text-blue-500 mt-2 inline-block">
                                    Add your first employee
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $users->firstItem() }}</span> 
                    to <span class="font-medium">{{ $users->lastItem() }}</span> 
                    of <span class="font-medium">{{ $users->total() }}</span> employees
                </div>
                <div class="flex space-x-2">
                    {{ $users->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Bulk Actions</h3>
        <div class="flex flex-wrap gap-3">
            <select id="bulk-action" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Bulk Actions</option>
                <option value="export">Export Selected</option>
                <option value="email">Send Email</option>
                <option value="activate">Activate Selected</option>
                <option value="deactivate">Deactivate Selected</option>
            </select>
            <button id="apply-bulk-action" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                Apply
            </button>
            <button id="clear-selection" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                Clear Selection
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .employee-row {
        transition: all 0.2s ease;
    }
    
    .employee-row:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
    }
    
    .status-badge {
        transition: all 0.2s ease;
    }
    
    .action-button {
        transition: all 0.2s ease;
    }
    
    .action-button:hover {
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkbox functionality
        const selectAll = document.getElementById('select-all');
        const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');
        
        selectAll.addEventListener('change', function() {
            employeeCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButton();
        });

        // Individual checkbox functionality
        employeeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(employeeCheckboxes).every(cb => cb.checked);
                const anyChecked = Array.from(employeeCheckboxes).some(cb => cb.checked);
                selectAll.checked = allChecked;
                selectAll.indeterminate = anyChecked && !allChecked;
                updateBulkActionButton();
            });
        });

        // Update bulk action button state
        function updateBulkActionButton() {
            const anyChecked = Array.from(employeeCheckboxes).some(cb => cb.checked);
            document.getElementById('apply-bulk-action').disabled = !anyChecked;
        }

        // Clear selection
        document.getElementById('clear-selection').addEventListener('click', function() {
            employeeCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            selectAll.checked = false;
            selectAll.indeterminate = false;
            updateBulkActionButton();
        });

        // Bulk action handler
        document.getElementById('apply-bulk-action').addEventListener('click', function() {
            const action = document.getElementById('bulk-action').value;
            const selectedIds = Array.from(employeeCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (!action) {
                alert('Please select a bulk action');
                return;
            }
            
            if (selectedIds.length === 0) {
                alert('Please select at least one employee');
                return;
            }
            
            switch(action) {
                case 'export':
                    exportSelectedEmployees(selectedIds);
                    break;
                case 'email':
                    sendBulkEmail(selectedIds);
                    break;
                case 'activate':
                    bulkUpdateStatus(selectedIds, 'active');
                    break;
                case 'deactivate':
                    bulkUpdateStatus(selectedIds, 'inactive');
                    break;
            }
        });

        // Export selected employees
        function exportSelectedEmployees(ids) {
            const url = new URL('{{ route("admin.employees") }}');
            url.searchParams.set('export', 'true');
            ids.forEach(id => url.searchParams.append('ids[]', id));
            window.location.href = url.toString();
        }

        // Send bulk email
        function sendBulkEmail(ids) {
            // Implement bulk email functionality
            alert(`Sending email to ${ids.length} selected employees`);
        }

        // Bulk update status
        function bulkUpdateStatus(ids, status) {
            if (confirm(`Are you sure you want to ${status} ${ids.length} employee(s)?`)) {
                fetch('{{ route("admin.employees.bulk-status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ids: ids,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    alert('Error updating employee status');
                });
            }
        }

        // Leave history button click
        document.querySelectorAll('.leave-history-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.dataset.userId;
                window.location.href = `/admin/leave-history?user_id=${userId}`;
            });
        });

        // Search functionality with debounce
        const searchInput = document.querySelector('input[name="search"]');
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.closest('form').submit();
            }, 500);
        });
    });
</script>
@endpush