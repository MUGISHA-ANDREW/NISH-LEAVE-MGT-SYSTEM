@extends('layouts.app')

@section('title', 'Pending Leave Approvals - Nish Auto Limited')
@section('page-title', 'Pending Leave Approvals')

@section('content')
@auth
    @if(Auth::user()->isHead())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Pending Leave Approvals</h1>
                <div class="flex space-x-3">
                    <a href="{{ route('head.dashboard') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                    <a href="{{ route('head.leaves.history') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-history mr-2"></i>View History
                    </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-blue-600">Awaiting Your Approval</p>
                            <p class="text-2xl font-bold text-blue-800">{{ $pendingLeaves->total() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-green-600">Approved by You</p>
                            <p class="text-2xl font-bold text-green-800">
                                {{ \App\Models\LeaveRequest::whereHas('user', function($q) use ($department) {
                                        $q->where('department_id', $department->id);
                                    })
                                    ->where('status', 'pending')
                                    ->whereHas('approvals', function($q) {
                                        $q->where('level', 'department_head')
                                          ->where('status', 'approved');
                                    })
                                    ->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-red-600">Rejected by You</p>
                            <p class="text-2xl font-bold text-red-800">
                                {{ \App\Models\LeaveRequest::whereHas('user', function($q) use ($department) {
                                        $q->where('department_id', $department->id);
                                    })
                                    ->where('status', 'rejected')
                                    ->whereHas('approvals', function($q) {
                                        $q->where('level', 'department_head')
                                          ->where('status', 'rejected');
                                    })
                                    ->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($pendingLeaves->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Employee</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Leave Type</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Period</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Duration</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Contact</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Applied On</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pendingLeaves as $leave)
                                <tr class="hover:bg-gray-50" id="leave-row-{{ $leave->id }}">
                                    <td class="py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-blue-800 font-semibold text-sm">
                                                    {{ substr($leave->user->first_name, 0, 1) }}{{ substr($leave->user->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $leave->user->first_name }} {{ $leave->user->last_name }}</p>
                                                <p class="text-sm text-gray-600">{{ $leave->user->employee_id ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">
                                            {{ $leave->leaveType->name }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-sm text-gray-600">
                                        <div>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">to</div>
                                        <div>{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</div>
                                    </td>
                                    <td class="py-4 text-sm text-gray-600">
                                        <span class="font-medium">{{ $leave->total_days }} day(s)</span>
                                    </td>
                                    <td class="py-4 text-sm text-gray-600">
                                        @if($leave->contact_number)
                                            <i class="fas fa-phone-alt text-gray-400 mr-1"></i>
                                            {{ $leave->contact_number }}
                                        @else
                                            <span class="text-gray-400">Not provided</span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($leave->created_at)->format('M d, Y') }}
                                    </td>
                                    <td class="py-4">
                                        <div class="flex space-x-2">
                                            <button type="button" 
                                                    data-id="{{ $leave->id }}"
                                                    data-name="{{ $leave->user->first_name }} {{ $leave->user->last_name }}"
                                                    data-leave-type="{{ $leave->leaveType->name }}"
                                                    data-days="{{ $leave->total_days }}"
                                                    class="approve-btn px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors flex items-center">
                                                <i class="fas fa-check mr-1"></i>Approve
                                            </button>
                                            <button type="button" 
                                                    data-id="{{ $leave->id }}"
                                                    data-name="{{ $leave->user->first_name }} {{ $leave->user->last_name }}"
                                                    class="reject-btn px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors flex items-center">
                                                <i class="fas fa-times mr-1"></i>Reject
                                            </button>
                                            <a href="{{ route('head.leave.details', $leave->id) }}" 
                                               class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $pendingLeaves->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Pending Approvals</h3>
                    <p class="text-gray-500 mb-6">All leave requests awaiting your approval have been processed.</p>
                    <div class="flex justify-center space-x-3">
                        <a href="{{ route('head.dashboard') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                            Return to Dashboard
                        </a>
                        <a href="{{ route('head.leaves.history') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition duration-200">
                            View Approval History
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Approval Modal -->
        <div id="approvalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl p-6 w-full max-w-lg mx-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Approve Leave Request</h3>
                <p class="text-sm text-gray-600 mb-4" id="approvalInfo"></p>
                
                <!-- Stand-In Employee Selection -->
                <div class="mb-4">
                    <label for="standInEmployee" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-user-shield text-indigo-600 mr-1"></i>Select Stand-In Employee <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-2">Choose an employee from the same department who will cover during this leave. Only employees not on leave during this period are shown.</p>
                    <div id="standInLoading" class="hidden">
                        <div class="flex items-center text-sm text-gray-500 py-2">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Loading available employees...
                        </div>
                    </div>
                    <select id="standInEmployee" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Select Stand-In Employee --</option>
                    </select>
                    <p id="standInError" class="text-xs text-red-500 mt-1 hidden">Please select a stand-in employee</p>
                    <p id="noStandInMsg" class="text-xs text-orange-500 mt-1 hidden">No available employees found for this period</p>
                </div>

                <div class="mb-4">
                    <label for="approvalRemarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks (Optional)</label>
                    <textarea id="approvalRemarks" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Add any remarks..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeApprovalModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="button" onclick="submitApproval()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700" id="approvalSubmitBtn">
                        <i class="fas fa-check mr-2"></i>Approve
                    </button>
                </div>
            </div>
        </div>

        <!-- Rejection Modal -->
        <div id="rejectionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Reject Leave Request</h3>
                <p class="text-sm text-gray-600 mb-4" id="rejectionInfo"></p>
                <div class="mb-4">
                    <label for="rejectionReason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Rejection *</label>
                    <textarea id="rejectionReason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Please provide a reason for rejection..." required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectionModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="button" onclick="submitRejection()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" id="rejectionSubmitBtn">
                        <i class="fas fa-times mr-2"></i>Reject
                    </button>
                </div>
            </div>
        </div>

        <!-- Include the same JavaScript for approval/rejection functionality -->
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let currentLeaveId = null;
                
                function getCsrfToken() {
                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                    return metaTag ? metaTag.getAttribute('content') : '';
                }

                function showNotification(message, type) {
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white z-50 ${
                        type === 'success' ? 'bg-green-500' : 'bg-red-500'
                    }`;
                    notification.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                            <span>${message}</span>
                        </div>
                    `;
                    document.body.appendChild(notification);
                    setTimeout(() => notification.remove(), 5000);
                }

                function loadStandInCandidates(leaveId) {
                    const select = document.getElementById('standInEmployee');
                    const loading = document.getElementById('standInLoading');
                    const noMsg = document.getElementById('noStandInMsg');
                    const errorMsg = document.getElementById('standInError');
                    
                    // Reset
                    select.innerHTML = '<option value="">-- Select Stand-In Employee --</option>';
                    select.classList.add('hidden');
                    loading.classList.remove('hidden');
                    noMsg.classList.add('hidden');
                    errorMsg.classList.add('hidden');

                    fetch(`/head/leave/${leaveId}/stand-in-candidates`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        loading.classList.add('hidden');
                        select.classList.remove('hidden');
                        
                        if (data.success && data.candidates.length > 0) {
                            data.candidates.forEach(emp => {
                                const option = document.createElement('option');
                                option.value = emp.id;
                                option.textContent = `${emp.name} (${emp.employee_id || 'N/A'})${emp.designation ? ' - ' + emp.designation : ''}`;
                                select.appendChild(option);
                            });
                        } else {
                            noMsg.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        loading.classList.add('hidden');
                        select.classList.remove('hidden');
                        noMsg.textContent = 'Error loading employees. Please try again.';
                        noMsg.classList.remove('hidden');
                    });
                }

                // Approve button functionality
                document.querySelectorAll('.approve-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        currentLeaveId = this.getAttribute('data-id');
                        const employeeName = this.getAttribute('data-name');
                        const leaveType = this.getAttribute('data-leave-type');
                        const days = this.getAttribute('data-days');
                        
                        document.getElementById('approvalInfo').innerHTML = `
                            Approve leave request for <strong>${employeeName}</strong><br>
                            <span class="text-sm">${leaveType} - ${days} day(s)</span>
                        `;
                        
                        // Load stand-in candidates
                        loadStandInCandidates(currentLeaveId);
                        
                        document.getElementById('approvalModal').classList.remove('hidden');
                        document.getElementById('approvalModal').classList.add('flex');
                    });
                });

                // Reject button functionality
                document.querySelectorAll('.reject-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        currentLeaveId = this.getAttribute('data-id');
                        const employeeName = this.getAttribute('data-name');
                        
                        document.getElementById('rejectionInfo').innerHTML = `
                            Reject leave request for <strong>${employeeName}</strong>
                        `;
                        
                        document.getElementById('rejectionModal').classList.remove('hidden');
                        document.getElementById('rejectionModal').classList.add('flex');
                    });
                });

                window.closeApprovalModal = function() {
                    document.getElementById('approvalModal').classList.add('hidden');
                    document.getElementById('approvalModal').classList.remove('flex');
                    document.getElementById('approvalRemarks').value = '';
                    document.getElementById('standInEmployee').value = '';
                    document.getElementById('standInError').classList.add('hidden');
                    currentLeaveId = null;
                };

                window.closeRejectionModal = function() {
                    document.getElementById('rejectionModal').classList.add('hidden');
                    document.getElementById('rejectionModal').classList.remove('flex');
                    document.getElementById('rejectionReason').value = '';
                    currentLeaveId = null;
                };

                window.submitApproval = function() {
                    const standInId = document.getElementById('standInEmployee').value;
                    const errorMsg = document.getElementById('standInError');
                    
                    // Validate stand-in selection
                    if (!standInId) {
                        errorMsg.classList.remove('hidden');
                        document.getElementById('standInEmployee').focus();
                        return;
                    }
                    errorMsg.classList.add('hidden');

                    const button = document.getElementById('approvalSubmitBtn');
                    const originalText = button.innerHTML;
                    const remarks = document.getElementById('approvalRemarks').value;
                    
                    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Approving...';
                    button.disabled = true;

                    fetch(`/head/approve/${currentLeaveId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ 
                            head_remarks: remarks || 'Approved by department head',
                            stand_in_employee_id: standInId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            // Remove the table row
                            const row = document.getElementById(`leave-row-${currentLeaveId}`);
                            if (row) {
                                row.style.opacity = '0';
                                row.style.transform = 'translateX(-20px)';
                                setTimeout(() => row.remove(), 300);
                            }
                            
                            closeApprovalModal();
                        } else {
                            showNotification(data.message, 'error');
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }
                    })
                    .catch(error => {
                        showNotification('An error occurred while approving the leave request.', 'error');
                        button.innerHTML = originalText;
                        button.disabled = false;
                    });
                };

                window.submitRejection = function() {
                    const button = document.getElementById('rejectionSubmitBtn');
                    const originalText = button.innerHTML;
                    const reason = document.getElementById('rejectionReason').value.trim();
                    
                    if (!reason) {
                        showNotification('Please provide a reason for rejection.', 'error');
                        return;
                    }
                    
                    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Rejecting...';
                    button.disabled = true;

                    fetch(`/head/reject/${currentLeaveId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ 
                            reason: reason
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            // Remove the table row
                            const row = document.getElementById(`leave-row-${currentLeaveId}`);
                            if (row) {
                                row.style.opacity = '0';
                                row.style.transform = 'translateX(-20px)';
                                setTimeout(() => row.remove(), 300);
                            }
                            
                            closeRejectionModal();
                        } else {
                            showNotification(data.message, 'error');
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }
                    })
                    .catch(error => {
                        showNotification('An error occurred while rejecting the leave request.', 'error');
                        button.innerHTML = originalText;
                        button.disabled = false;
                    });
                };
            });
        </script>
        @endpush

    @else
        <!-- Access Denied Message -->
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-8 rounded-2xl text-center">
            <h2 class="text-2xl font-bold mb-2">Access Denied</h2>
            <p class="text-lg mb-4">This page is for department heads only.</p>
        </div>
    @endif
@else
    <!-- Not Authenticated Message -->
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-8 rounded-2xl text-center">
        <h2 class="text-2xl font-bold mb-2">Authentication Required</h2>
        <p class="text-lg mb-4">Please log in to access this page.</p>
    </div>
@endauth
@endsection