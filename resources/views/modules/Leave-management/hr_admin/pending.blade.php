@extends('layouts.app')

@section('title', 'HR Pending Approvals - Nish Auto Limited')
@section('page-title', 'HR Pending Approvals')

@php
    $isAdmin = true;
    use Carbon\Carbon;
@endphp

@section('content')
<div class="space-y-6">
    <!-- HR Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">HR Pending</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">{{ $stats['hr_pending'] ?? 0 }}</p>
                    <p class="text-xs text-orange-600 mt-1">
                        <i class="fas fa-clock mr-1"></i>Awaiting HR approval
                    </p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Approved Today</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">{{ $stats['approved_today'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-check-circle mr-1"></i>HR approved today
                    </p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Departments</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_departments'] ?? 0 }}</p>
                    <p class="text-xs text-purple-600 mt-1">
                        <i class="fas fa-building mr-1"></i>Active departments
                    </p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-purple-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_employees'] ?? 0 }}</p>
                    <p class="text-xs text-blue-600 mt-1">
                        <i class="fas fa-users mr-1"></i>All employees
                    </p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6">
        <form method="GET" action="{{ route('admin.leaves.pending') }}" class="space-y-4 md:space-y-0">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 flex-1">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="all">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                        <select name="leave_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="all">All Leave Types</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ request('leave_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                        <select name="employee" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="all">All Employees</option>
                            @foreach($pendingRequests->pluck('user')->unique() as $user)
                                <option value="{{ $user->id }}" {{ request('employee') == $user->id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium text-sm whitespace-nowrap">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('admin.leaves.pending') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-200 font-medium text-sm text-center whitespace-nowrap">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Department-wise Pending Requests -->
    @if($departmentGroups->count() > 0 && $pendingRequests->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 md:px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Pending HR Approvals by Department</h3>
                <p class="text-sm text-gray-600 mt-1">Leave requests approved by department heads, awaiting HR final approval</p>
            </div>
            
            <div class="divide-y divide-gray-200">
                @foreach($departmentGroups as $departmentId => $requests)
                    @if($requests->count() > 0)
                        @php
                            $department = $requests->first()->user->department ?? null;
                            $pendingCount = $requests->count();
                            $deptIcon = match($department->name ?? '') {
                                'Assembly' => 'fa-cogs',
                                'Mechanical' => 'fa-wrench',
                                'Electrical' => 'fa-bolt',
                                'Sales & Marketing' => 'fa-chart-line',
                                'Spare Parts' => 'fa-box',
                                'Finance' => 'fa-money-bill-wave',
                                'HR' => 'fa-users',
                                'IT' => 'fa-laptop-code',
                                default => 'fa-building'
                            };
                            $deptColor = match($department->name ?? '') {
                                'Assembly' => 'blue',
                                'Mechanical' => 'orange',
                                'Electrical' => 'yellow',
                                'Sales & Marketing' => 'green',
                                'Spare Parts' => 'purple',
                                'Finance' => 'red',
                                'HR' => 'pink',
                                'IT' => 'indigo',
                                default => 'gray'
                            };
                        @endphp
                        
                        <div class="p-4 md:p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                                <h4 class="text-base md:text-lg font-medium text-gray-900 flex items-center">
                                    <i class="fas {{ $deptIcon }} text-{{ $deptColor }}-500 mr-2"></i>
                                    {{ $department->name ?? 'No Department' }}
                                </h4>
                                <span class="px-3 py-1 bg-{{ $deptColor }}-100 text-{{ $deptColor }}-800 text-sm rounded-full font-medium whitespace-nowrap">
                                    {{ $pendingCount }} {{ Str::plural('pending', $pendingCount) }}
                                </span>
                            </div>
                            
                            <div class="space-y-3 md:space-y-4">
                                @foreach($requests as $request)
                                    <div class="approval-item flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-3 md:p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                                        <div class="flex items-center space-x-3 md:space-x-4 flex-1">
                                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-full flex items-center justify-center bg-blue-100">
                                                <span class="text-sm md:text-base font-semibold text-blue-600">
                                                    {{ substr($request->user->first_name, 0, 1) }}{{ substr($request->user->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="font-medium text-gray-900 truncate">
                                                    {{ $request->user->first_name }} {{ $request->user->last_name }}
                                                    @if($request->user->employee_id)
                                                        <span class="text-sm text-gray-500">({{ $request->user->employee_id }})</span>
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    {{ $request->leaveType->name ?? 'N/A' }} • {{ $request->total_days }} {{ Str::plural('day', $request->total_days) }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-user-tie mr-1"></i>
                                                    @if($department && $department->manager)
                                                        Dept Manager: {{ $department->manager->first_name }} {{ $department->manager->last_name }}
                                                    @else
                                                        No Dept Manager
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-col md:text-right space-y-1">
                                            <p class="text-sm text-gray-900">
                                                {{ Carbon::parse($request->start_date)->format('M d') }} - {{ Carbon::parse($request->end_date)->format('M d, Y') }}
                                            </p>
                                            @php
                                                $deptApproval = $request->approvals->where('level', 'department_head')->where('status', 'approved')->first();
                                            @endphp
                                            @if($deptApproval)
                                                <p class="text-xs text-green-600">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Dept Approved: {{ Carbon::parse($deptApproval->created_at)->format('M d') }}
                                                </p>
                                            @endif
                                            @if($request->is_urgent ?? false)
                                                <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full mt-1">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>Urgent
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex flex-wrap gap-2">
                                            <form action="{{ route('leaves.approve', $request->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Approve leave request for {{ $request->user->first_name }} {{ $request->user->last_name }}?')"
                                                        class="approve-btn bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition duration-200 text-sm font-medium whitespace-nowrap">
                                                    <i class="fas fa-check mr-1 md:mr-2"></i><span class="hidden sm:inline">Approve</span>
                                                </button>
                                            </form>
                                            
                                            <button type="button" 
                                                    data-request-id="{{ $request->id }}"
                                                    data-employee-name="{{ $request->user->first_name }} {{ $request->user->last_name }}"
                                                    class="reject-btn bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition duration-200 text-sm font-medium whitespace-nowrap">
                                                <i class="fas fa-times mr-1 md:mr-2"></i><span class="hidden sm:inline">Reject</span>
                                            </button>
                                            
                                            <a href="{{ route('leaves.details', $request->id) }}" 
                                               class="bg-gray-200 text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-300 transition duration-200 text-sm font-medium whitespace-nowrap">
                                                <i class="fas fa-eye mr-1 md:mr-2"></i><span class="hidden sm:inline">View</span>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Pending Approvals</h3>
            <p class="text-gray-600">All leave requests have been processed. Great job!</p>
        </div>
    @endif

    <!-- Quick Actions -->
    @if($pendingRequests->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <button onclick="if(confirm('Approve all {{ $stats['hr_pending'] ?? 0 }} pending requests?')) { document.getElementById('approve-all-form').submit(); }"
                        class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-200 transition duration-200 w-full">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-double text-blue-600"></i>
                    </div>
                    <div class="text-left flex-1">
                        <p class="font-medium text-gray-800">Approve All</p>
                        <p class="text-sm text-gray-600">Approve {{ $stats['hr_pending'] ?? 0 }} pending requests</p>
                    </div>
                </button>
                
                <a href="{{ route('leaves.export') }}?{{ http_build_query(request()->all()) }}"
                   class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-200 transition duration-200 w-full">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-export text-green-600"></i>
                    </div>
                    <div class="text-left flex-1">
                        <p class="font-medium text-gray-800">Export Report</p>
                        <p class="text-sm text-gray-600">Download pending approvals</p>
                    </div>
                </a>
                
                <button onclick="sendReminders()"
                        class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-200 transition duration-200 w-full">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-envelope text-purple-600"></i>
                    </div>
                    <div class="text-left flex-1">
                        <p class="font-medium text-gray-800">Send Reminders</p>
                        <p class="text-sm text-gray-600">Notify department heads</p>
                    </div>
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Leave Request</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="employeeInfo" class="mb-4 p-3 bg-gray-50 rounded-lg"></div>
                    <div class="form-group">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Rejection *</label>
                        <textarea name="rejection_reason" id="rejection_reason" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                  rows="4" required
                                  placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">Submit Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve All Form (hidden) -->
<form id="approve-all-form" action="{{ route('leaves.approve-all') }}" method="POST" class="hidden">
    @csrf
    @foreach(request()->all() as $key => $value)
        @if(!in_array($key, ['_token', '_method']))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
</form>

@endsection

@push('styles')
<style>
    .approval-item {
        transition: all 0.2s ease;
    }
    
    .approval-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
        .approval-item {
            padding: 0.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentRejectId = null;
        
        // Reject button click handler
        document.querySelectorAll('.reject-btn').forEach(button => {
            button.addEventListener('click', function() {
                currentRejectId = this.dataset.requestId;
                const employeeName = this.dataset.employeeName;
                
                document.getElementById('employeeInfo').innerHTML = `
                    <p class="font-medium text-gray-800">Rejecting leave request for:</p>
                    <p class="text-lg font-semibold text-red-600">${employeeName}</p>
                `;
                
                document.getElementById('rejectForm').action = `/admin/leaves/${currentRejectId}/reject`;
                $('#rejectModal').modal('show');
            });
        });
        
        // Reject form submission
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            if (!this.rejection_reason.value.trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Reason Required',
                    text: 'Please provide a reason for rejection.',
                });
            }
        });
        
        // Approve button animation
        document.querySelectorAll('.approve-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('form');
                const item = this.closest('.approval-item');
                
                if (item) {
                    item.style.opacity = '0.5';
                    item.style.pointerEvents = 'none';
                }
                
                // Submit the form
                form.submit();
            });
        });
    });
    
    function sendReminders() {
        Swal.fire({
            title: 'Send Reminders?',
            text: 'Send reminder emails to department heads about pending approvals?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Send Reminders',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch('{{ route("leaves.send-reminders") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText);
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    );
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Reminders have been sent to department heads.',
                    icon: 'success',
                    timer: 3000
                });
            }
        });
    }
</script>
@endpush