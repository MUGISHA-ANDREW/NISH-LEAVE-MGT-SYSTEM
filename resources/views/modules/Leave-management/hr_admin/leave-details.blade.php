@extends('layouts.app')

@section('title', 'Leave Request Details - Nish Auto Limited')
@section('page-title', 'Leave Request Details')

@php
    $isAdmin = true;
    use Carbon\Carbon;
    
    // Set default values
    $leaveRequest = $leaveRequest ?? null;
    $statusColors = $statusColors ?? [
        'pending' => 'yellow',
        'approved' => 'green',
        'rejected' => 'red',
        'cancelled' => 'gray'
    ];
@endphp

@section('content')
@if(!$leaveRequest)
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-center py-12">
                <i class="fas fa-exclamation-triangle text-red-500 text-5xl mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Leave Request Not Found</h2>
                <p class="text-gray-600 mb-6">The requested leave request could not be found or has been deleted.</p>
                <a href="{{ route('admin.leaves.history') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Leave History
                </a>
            </div>
        </div>
    </div>
@else
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <!-- Back Button -->
        <a href="{{ route('admin.leaves.history') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
            <i class="fas fa-arrow-left mr-2"></i> Back to Leave History
        </a>
        
        <!-- Leave Details -->
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Leave Request Details</h2>
                    <p class="text-gray-600">Request ID: #{{ $leaveRequest->id }}</p>
                </div>
                <span class="px-4 py-2 bg-{{ $statusColors[$leaveRequest->status] ?? 'gray' }}-100 text-{{ $statusColors[$leaveRequest->status] ?? 'gray' }}-800 rounded-full font-medium capitalize">
                    {{ $leaveRequest->status }}
                </span>
            </div>
            
            <!-- Employee Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Employee Information</h3>
                    <div class="flex items-center space-x-4">
                        @if($leaveRequest->user && $leaveRequest->user->profile_picture)
                            <img src="{{ Storage::url($leaveRequest->user->profile_picture) }}" 
                                 alt="{{ $leaveRequest->user->name ?? 'Employee' }}" 
                                 class="w-16 h-16 rounded-full object-cover">
                        @else
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 text-2xl font-semibold">
                                    @if($leaveRequest->user)
                                        {{ strtoupper(substr($leaveRequest->user->first_name ?? 'E', 0, 1) . substr($leaveRequest->user->last_name ?? 'M', 0, 1)) }}
                                    @else
                                        NA
                                    @endif
                                </span>
                            </div>
                        @endif
                        <div>
                            @if($leaveRequest->user)
                                <h4 class="text-xl font-bold text-gray-800">
                                    {{ $leaveRequest->user->first_name }} {{ $leaveRequest->user->last_name }}
                                </h4>
                                <p class="text-gray-600">{{ $leaveRequest->user->employee_id ?? 'N/A' }}</p>
                                <p class="text-gray-600">{{ $leaveRequest->user->department->name ?? 'No Department' }}</p>
                                <p class="text-gray-600">{{ $leaveRequest->user->position ?? 'No Position' }}</p>
                            @else
                                <p class="text-red-500">Employee information not available</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Leave Information -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Leave Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Leave Type:</span>
                            <span class="font-medium">{{ $leaveRequest->leaveType->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Duration:</span>
                            <span class="font-medium">{{ $leaveRequest->total_days ?? '0' }} day(s)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dates:</span>
                            <span class="font-medium">
                                @if($leaveRequest->start_date)
                                    {{ Carbon::parse($leaveRequest->start_date)->format('M d, Y') }} - 
                                    {{ Carbon::parse($leaveRequest->end_date)->format('M d, Y') }}
                                @else
                                    Not specified
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Applied On:</span>
                            <span class="font-medium">{{ $leaveRequest->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if($leaveRequest->action_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Processed On:</span>
                            <span class="font-medium">{{ $leaveRequest->action_at->format('M d, Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Reason and Notes -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Reason & Notes</h3>
                <div class="space-y-4">
                    @if($leaveRequest->reason)
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-2">Employee's Reason:</h4>
                        <p class="text-gray-800 bg-gray-50 p-3 rounded-lg">{{ $leaveRequest->reason }}</p>
                    </div>
                    @endif
                    
                    @if($leaveRequest->head_remarks)
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-2">Department Head Remarks:</h4>
                        <p class="text-gray-800 bg-blue-50 p-3 rounded-lg">{{ $leaveRequest->head_remarks }}</p>
                    </div>
                    @endif
                    
                    @if($leaveRequest->rejection_reason)
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-2">Rejection Reason:</h4>
                        <p class="text-gray-800 bg-red-50 p-3 rounded-lg">{{ $leaveRequest->rejection_reason }}</p>
                    </div>
                    @endif
                    
                    @if($leaveRequest->handover_notes)
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-2">Handover Notes:</h4>
                        <p class="text-gray-800 bg-yellow-50 p-3 rounded-lg">{{ $leaveRequest->handover_notes }}</p>
                    </div>
                    @endif
                    
                    @if($leaveRequest->emergency_contact)
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-2">Emergency Contact:</h4>
                        <p class="text-gray-800 bg-green-50 p-3 rounded-lg">{{ $leaveRequest->emergency_contact }}</p>
                    </div>
                    @endif
                    
                    @if($leaveRequest->contact_number)
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-2">Contact Number:</h4>
                        <p class="text-gray-800 bg-purple-50 p-3 rounded-lg">{{ $leaveRequest->contact_number }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Approval History -->
            @if($leaveRequest->action_by || $leaveRequest->head_remarks)
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Approval History</h3>
                <div class="space-y-4">
                    @if($leaveRequest->head_remarks)
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-800">Department Head</h4>
                            @if($leaveRequest->user && $leaveRequest->user->department && $leaveRequest->user->department->head)
                                <p class="text-sm text-gray-600">{{ $leaveRequest->user->department->head->name ?? 'Unknown' }}</p>
                            @endif
                            <p class="text-sm text-gray-500 mt-1">{{ $leaveRequest->head_remarks }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Reviewed
                            </span>
                        </div>
                    </div>
                    @endif
                    
                    @if($leaveRequest->action_by)
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-800">HR Admin</h4>
                            <p class="text-sm text-gray-600">{{ $leaveRequest->actionBy->name ?? 'Unknown' }}</p>
                            @if($leaveRequest->action_at)
                                <p class="text-xs text-gray-500 mt-1">{{ $leaveRequest->action_at->format('M d, Y H:i') }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                @if($leaveRequest->status == 'approved') bg-green-100 text-green-800
                                @elseif($leaveRequest->status == 'rejected') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($leaveRequest->status) }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Actions -->
            @if($leaveRequest->status == 'pending')
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                    <form action="{{ route('leaves.approve', $leaveRequest->id) }}" method="POST" class="flex-1">
                        @csrf
                        <div class="mb-4">
                            <label for="hr_remarks" class="block text-sm font-medium text-gray-700 mb-2">HR Remarks (Optional)</label>
                            <textarea name="hr_remarks" id="hr_remarks" rows="2" 
                                      placeholder="Add any remarks or notes..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-200 font-medium">
                            <i class="fas fa-check mr-2"></i> Approve Leave
                        </button>
                    </form>
                    
                    <form action="{{ route('leaves.reject', $leaveRequest->id) }}" method="POST" class="flex-1">
                        @csrf
                        <div class="mb-4">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason *</label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="2" required
                                      placeholder="Please provide a reason for rejection..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200 font-medium">
                            <i class="fas fa-times mr-2"></i> Reject Leave
                        </button>
                    </form>
                </div>
            </div>
            @else
            <!-- View Only Actions -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.leaves.history') }}" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i> Back to List
                    </a>
                    
                    @if($leaveRequest->status == 'approved')
                        <button class="bg-green-600 text-white py-2 px-4 rounded-lg font-medium cursor-default">
                            <i class="fas fa-check mr-2"></i> Already Approved
                        </button>
                    @elseif($leaveRequest->status == 'rejected')
                        <button class="bg-red-600 text-white py-2 px-4 rounded-lg font-medium cursor-default">
                            <i class="fas fa-times mr-2"></i> Already Rejected
                        </button>
                    @endif
                </div>
            </div>
            @endif

            {{-- Stand-In Employee Section (Visible to HR only on approved leaves) --}}
            @if($leaveRequest->status == 'approved' && Auth::user()->isAdmin())
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user-shield text-indigo-600 mr-2"></i>Stand-In Employee Assignment
                </h3>

                @if($leaveRequest->standInEmployee)
                    {{-- Currently assigned stand-in --}}
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 font-semibold text-sm">
                                        {{ strtoupper(substr($leaveRequest->standInEmployee->first_name, 0, 1) . substr($leaveRequest->standInEmployee->last_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Currently Assigned</p>
                                    <p class="text-base font-semibold text-indigo-700">
                                        {{ $leaveRequest->standInEmployee->first_name }} {{ $leaveRequest->standInEmployee->last_name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $leaveRequest->standInEmployee->employee_id ?? '' }}
                                        @if($leaveRequest->standInEmployee->designation)
                                            &middot; {{ $leaveRequest->standInEmployee->designation }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-medium">
                                <i class="fas fa-check-circle mr-1"></i> Assigned
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Assignment / Reassignment Form --}}
                <form action="{{ route('leaves.assign-stand-in', $leaveRequest->id) }}" method="POST" id="standInForm">
                    @csrf
                    <div class="mb-4">
                        <label for="stand_in_employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $leaveRequest->standInEmployee ? 'Reassign Stand-In Employee' : 'Select Stand-In Employee' }}
                        </label>
                        <p class="text-xs text-gray-500 mb-2">
                            Only employees from the same department ({{ $leaveRequest->user->department->name ?? 'N/A' }}) are shown.
                        </p>
                        <select name="stand_in_employee_id" id="stand_in_employee_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Stand-In Employee --</option>
                            @forelse($standInCandidates ?? [] as $candidate)
                                <option value="{{ $candidate->id }}" 
                                    {{ $leaveRequest->stand_in_employee_id == $candidate->id ? 'selected' : '' }}>
                                    {{ $candidate->first_name }} {{ $candidate->last_name }}
                                    ({{ $candidate->employee_id ?? 'N/A' }})
                                    @if($candidate->designation) - {{ $candidate->designation }} @endif
                                </option>
                            @empty
                                <option value="" disabled>No eligible employees found in this department</option>
                            @endforelse
                        </select>
                        @error('stand_in_employee_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-200 font-medium"
                            {{ empty($standInCandidates) || $standInCandidates->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-user-shield mr-2"></i>
                        {{ $leaveRequest->standInEmployee ? 'Reassign Stand-In' : 'Assign Stand-In' }}
                    </button>
                </form>
            </div>
            @endif

            {{-- Stand-In Display (read-only, visible when stand-in is assigned for non-HR users) --}}
            @if($leaveRequest->standInEmployee && !Auth::user()->isAdmin())
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user-shield text-indigo-600 mr-2"></i>Stand-In Employee
                </h3>
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-indigo-600 font-semibold text-sm">
                                {{ strtoupper(substr($leaveRequest->standInEmployee->first_name, 0, 1) . substr($leaveRequest->standInEmployee->last_name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-gray-800">
                                {{ $leaveRequest->standInEmployee->first_name }} {{ $leaveRequest->standInEmployee->last_name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $leaveRequest->standInEmployee->employee_id ?? '' }}
                                @if($leaveRequest->standInEmployee->designation)
                                    &middot; {{ $leaveRequest->standInEmployee->designation }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .detail-card {
        transition: all 0.3s ease;
    }
    
    .detail-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .status-badge {
        transition: all 0.3s ease;
    }
    
    .action-button {
        transition: all 0.2s ease;
    }
    
    .action-button:hover:not(:disabled) {
        transform: translateY(-2px);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize textarea autoresize
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });
        
        // Confirm rejection
        const rejectForm = document.querySelector('form[action*="reject"]');
        if (rejectForm) {
            rejectForm.addEventListener('submit', function(e) {
                const reason = document.getElementById('rejection_reason').value;
                if (!reason || reason.trim().length < 5) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Reason Required',
                        text: 'Please provide a rejection reason (at least 5 characters).'
                    });
                } else {
                    if (!confirm('Are you sure you want to reject this leave request?')) {
                        e.preventDefault();
                    }
                }
            });
        }
        
        // Confirm approval
        const approveForm = document.querySelector('form[action*="approve"]');
        if (approveForm) {
            approveForm.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to approve this leave request?')) {
                    e.preventDefault();
                }
            });
        }

        // Confirm stand-in assignment
        const standInForm = document.getElementById('standInForm');
        if (standInForm) {
            standInForm.addEventListener('submit', function(e) {
                const select = document.getElementById('stand_in_employee_id');
                if (!select.value) {
                    e.preventDefault();
                    alert('Please select a stand-in employee.');
                    return;
                }
                const selectedText = select.options[select.selectedIndex].text;
                if (!confirm('Assign ' + selectedText + ' as the stand-in employee?')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush