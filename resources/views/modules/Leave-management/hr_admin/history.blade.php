@extends('layouts.app')

@section('title', 'HR Leave History - Nish Auto Limited')
@section('page-title', 'HR Leave History')

@php
    $isAdmin = true;
    use Carbon\Carbon;
    
    // Set default values if variables are not passed
    $leaveRequests = $leaveRequests ?? collect();
    $departments = $departments ?? collect();
    $leaveTypes = $leaveTypes ?? collect();
    $employees = $employees ?? collect();
    $stats = $stats ?? [
        'total' => 0,
        'approved' => 0,
        'pending' => 0,
        'rejected' => 0,
        'this_month' => 0,
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
        <!-- Total Requests -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Requests</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Approved</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['approved'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['pending'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Rejected -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Rejected</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['rejected'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['this_month'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.leaves.history') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Department Filter -->
                <select name="department" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                
                <!-- Employee Filter -->
                <select name="employee" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </option>
                    @endforeach
                </select>
                
                <!-- Status Filter -->
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                
                <!-- Leave Type Filter -->
                <select name="leave_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Leave Types</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ request('leave_type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                
                <!-- Month Filter -->
                <input type="month" 
                       name="month" 
                       value="{{ request('month') ?? date('Y-m') }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div class="flex justify-between items-center">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
                
                <!-- Replace the Export Form in your history.blade.php -->
<div class="flex space-x-2">
    <!-- Export Form - FIXED: Use correct route -->
    <form method="GET" action="{{ route('leaves.export') }}" class="inline">
        @foreach(request()->all() as $key => $value)
            @if(!in_array($key, ['_token', '_method']))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <!-- Simple direct download for Export Excel -->
<a href="{{ route('leaves.export') }}?{{ http_build_query(request()->all()) }}" 
   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 font-medium flex items-center">
    <i class="fas fa-download mr-2"></i>Export Excel
</a>
    </form>
    
    <!-- Generate Report -->
    <!-- Replace the Generate Report button with this -->
<a href="{{ route('download-report') }}?{{ http_build_query(request()->all()) }}" 
   class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-200 font-medium flex items-center">
    <i class="fas fa-chart-bar mr-2"></i>Generate Report
</a>
</div>
            </div>
        </form>
    </div>

    <!-- Comprehensive Leave History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee & Department</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Details</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timeline</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status & Approval</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leaveRequests as $request)
                        @php
                            // Get employee initials
                            $initials = 'NA';
                            if ($request->user) {
                                $first = substr($request->user->first_name ?? 'E', 0, 1);
                                $last = substr($request->user->last_name ?? 'M', 0, 1);
                                $initials = strtoupper($first . $last);
                            }
                            
                            // Status colors
                            $statusColors = [
                                'pending' => 'orange',
                                'approved' => 'green',
                                'rejected' => 'red',
                                'cancelled' => 'gray'
                            ];
                            
                            // Leave type icons
                            $leaveIcons = [
                                'annual' => 'fa-umbrella-beach',
                                'sick' => 'fa-heartbeat',
                                'maternity' => 'fa-baby',
                                'paternity' => 'fa-user-tie',
                                'casual' => 'fa-coffee',
                                'unpaid' => 'fa-money-bill-alt'
                            ];
                            
                            $defaultIcon = 'fa-calendar-alt';
                            $icon = $defaultIcon;
                            if ($request->leaveType && $request->leaveType->name) {
                                $leaveTypeName = strtolower($request->leaveType->name);
                                $icon = $leaveIcons[$leaveTypeName] ?? $defaultIcon;
                            }
                            
                            // Calculate duration
                            $duration = $request->total_days ?? 0;
                            if ($request->start_date && $request->end_date) {
                                try {
                                    $start = Carbon::parse($request->start_date);
                                    $end = Carbon::parse($request->end_date);
                                    $duration = $start->diffInDays($end) + 1;
                                } catch (\Exception $e) {
                                    $duration = $request->total_days ?? 0;
                                }
                            }
                            
                            // Get status color
                            $statusColor = $statusColors[$request->status] ?? 'gray';
                        @endphp
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        @if($request->user && $request->user->profile_picture)
                                            <img src="{{ Storage::url($request->user->profile_picture) }}" 
                                                 alt="{{ $request->user->first_name ?? 'Employee' }}" 
                                                 class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <span class="text-blue-600 font-semibold">{{ $initials }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($request->user)
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $request->user->first_name }} {{ $request->user->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $request->user->department->name ?? 'No Department' }}</div>
                                            <div class="text-xs text-gray-400">{{ $request->user->employee_id ?? 'N/A' }}</div>
                                        @else
                                            <div class="text-sm text-gray-900">Employee Not Found</div>
                                            <div class="text-xs text-red-500">User ID: {{ $request->user_id }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas {{ $icon }} text-blue-500 text-lg mr-3"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $request->leaveType->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $duration }} day(s)</div>
                                        <div class="text-xs text-gray-400">
                                            @if($request->start_date)
                                                {{ Carbon::parse($request->start_date)->format('M d, Y') }} - 
                                                {{ Carbon::parse($request->end_date)->format('M d, Y') }}
                                            @else
                                                Dates not set
                                            @endif
                                        </div>
                                        @if($request->reason)
                                            <div class="text-xs text-gray-500 mt-1 truncate max-w-xs" title="{{ $request->reason }}">
                                                <i class="fas fa-comment mr-1"></i>{{ Str::limit($request->reason, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="text-sm text-gray-900">
                                        <i class="fas fa-paper-plane mr-1 text-blue-500"></i>
                                        Applied: {{ $request->created_at->format('M d, Y') }}
                                    </div>
                                    @if($request->action_at)
                                        <div class="text-sm text-green-600">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Processed: {{ $request->action_at->format('M d, Y') }}
                                        </div>
                                    @endif
                                    <div class="text-xs text-gray-500">
                                        Processing Time: 
                                        @if($request->action_at)
                                            {{ $request->created_at->diffInDays($request->action_at) }} day(s)
                                        @else
                                            {{ $request->created_at->diffInDays(now()) }} day(s) and counting
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    <span class="px-3 py-1 bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 text-xs rounded-full font-medium capitalize">
                                        {{ $request->status }}
                                    </span>
                                    
                                    <!-- Simple approval indicator (without approvals table) -->
                                    <div class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        @if($request->status == 'approved')
                                            Approved by HR
                                        @elseif($request->status == 'rejected')
                                            Rejected by HR
                                        @elseif($request->status == 'pending')
                                            Awaiting HR approval
                                        @else
                                            {{ ucfirst($request->status) }}
                                        @endif
                                    </div>
                                    
                                    @if($request->action_by)
                                        <div class="text-xs text-gray-600 mt-1">
                                            By: {{ \App\Models\User::find($request->action_by)->name ?? 'Unknown' }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('leaves.details', $request->id) }}" 
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium flex items-center">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    
                                    @if($request->status == 'pending')
                                        <form action="{{ route('leaves.approve', $request->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-medium flex items-center ml-2">
                                                <i class="fas fa-check mr-1"></i> Approve
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('leaves.reject', $request->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reject this leave request?')">
                                            @csrf
                                            <div class="hidden">
                                                <input type="text" name="rejection_reason" value="Rejected by HR Admin" required>
                                            </div>
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium flex items-center ml-2">
                                                <i class="fas fa-times mr-1"></i> Reject
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500">No leave requests found</p>
                                @if(request()->hasAny(['department', 'employee', 'status', 'month', 'leave_type']))
                                    <a href="{{ route('admin.leaves.history') }}" class="text-blue-600 hover:text-blue-500 mt-2 inline-block">
                                        Clear filters to see all requests
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
@if($leaveRequests->count() > 0)
<div class="bg-white px-6 py-4 border-t border-gray-200">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ $leaveRequests->firstItem() ?? 0 }}</span> 
            to <span class="font-medium">{{ $leaveRequests->lastItem() ?? 0 }}</span> 
            of <span class="font-medium">{{ $leaveRequests->total() ?? 0 }}</span> requests
        </div>
        <div class="flex space-x-2">
            {{ $leaveRequests->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function generateReport() {
    // Show loading
    Swal.fire({
        title: 'Generating Report',
        text: 'Please wait while we generate your report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Get filter parameters
    const params = new URLSearchParams(window.location.search);
    
    // Make AJAX request to generate report
    fetch(`{{ route('generate-report') }}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        
        if (data.success && data.download_url) {
            // Show success message with download option
            Swal.fire({
                icon: 'success',
                title: 'Report Generated',
                html: `Report has been generated successfully!<br><br>
                       <small>Click Download to get your CSV file</small>`,
                showCancelButton: true,
                confirmButtonText: 'Download',
                cancelButtonText: 'Close',
                showDenyButton: true,
                denyButtonText: 'Preview Data'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Trigger download by creating an invisible link
                    const link = document.createElement('a');
                    link.href = data.download_url;
                    link.target = '_blank';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else if (result.isDenied) {
                    // Show preview (optional)
                    previewReportData();
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to generate report'
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to generate report. Please try again.'
        });
    });
}

// Optional: Preview function
function previewReportData() {
    // Get filter parameters
    const params = new URLSearchParams(window.location.search);
    
    fetch(`{{ route('generate-report') }}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show preview in a modal or new tab
            window.open(data.download_url, '_blank');
        }
    });
}
    
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any tooltips if needed
        const tooltipElements = document.querySelectorAll('[title]');
        tooltipElements.forEach(el => {
            new bootstrap.Tooltip(el);
        });
        
        // Auto-submit month filter on change
        const monthFilter = document.querySelector('input[name="month"]');
        if (monthFilter) {
            monthFilter.addEventListener('change', function() {
                this.closest('form').submit();
            });
        }
    });
</script>
@endpush