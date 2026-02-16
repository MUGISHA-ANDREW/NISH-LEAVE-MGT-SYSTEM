@extends('layouts.app')

@section('title', 'View Leave Request - Nish Auto Limited')
@section('page-title', 'View Leave Request')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Leave Request Details</h2>
                <p class="text-gray-600 mt-1">View your leave application information</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-4 py-2 {{ $leaveRequest->getStatusBadgeClass() }} text-sm rounded-full font-medium">
                    {{ ucfirst($leaveRequest->status) }}
                </span>
                <a href="{{ route('employee.leave.history') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-200 font-medium text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        <!-- Leave Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800">Leave Duration</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $leaveRequest->total_days }} days</p>
                            <p class="text-sm text-blue-600 mt-1">
                                {{ $leaveRequest->start_date->format('M d, Y') }} to {{ $leaveRequest->end_date->format('M d, Y') }}
                            </p>
                        </div>
                        <i class="fas fa-calendar-alt text-blue-500 text-2xl"></i>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Leave Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Leave Type:</span>
                            <div class="flex items-center">
                                <i class="{{ $leaveRequest->leaveType->getIconClass() }} mr-2"></i>
                                <span class="font-medium text-gray-800">{{ $leaveRequest->leaveType->name }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Applied On:</span>
                            <span class="font-medium text-gray-800">{{ $leaveRequest->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-800">{{ $leaveRequest->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact & Handover -->
            <div class="space-y-4">
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Contact Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Contact Number:</span>
                            <span class="font-medium text-gray-800">{{ $leaveRequest->contact_number }}</span>
                        </div>
                        
                        @if($leaveRequest->emergency_contact)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Emergency Contact:</span>
                            <span class="font-medium text-gray-800">{{ $leaveRequest->emergency_contact }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($leaveRequest->handover_notes)
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Handover Notes</h3>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $leaveRequest->handover_notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Reason -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Reason for Leave</h3>
            <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $leaveRequest->reason }}</p>
        </div>

        <!-- Stand-In Employee (visible only after HR approval) -->
        @if($leaveRequest->status === 'approved' && $leaveRequest->standInEmployee)
        <div class="bg-white border border-indigo-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                <i class="fas fa-user-shield text-indigo-600 mr-2"></i>Assigned Stand-In Employee
            </h3>
            <div class="bg-indigo-50 rounded-lg p-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <span class="text-indigo-600 text-lg font-semibold">
                            {{ strtoupper(substr($leaveRequest->standInEmployee->first_name, 0, 1) . substr($leaveRequest->standInEmployee->last_name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $leaveRequest->standInEmployee->first_name }} {{ $leaveRequest->standInEmployee->last_name }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $leaveRequest->standInEmployee->employee_id ?? '' }}
                            @if($leaveRequest->standInEmployee->designation)
                                &middot; {{ $leaveRequest->standInEmployee->designation }}
                            @endif
                        </p>
                        <p class="text-xs text-indigo-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>This employee will cover your duties during your leave
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
<div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
    @if($leaveRequest->status == 'pending')
        <a href="{{ route('employee.leave.edit', $leaveRequest->id) }}" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium text-center">
            <i class="fas fa-edit mr-2"></i>Edit Request
        </a>
        <form action="{{ route('employee.leave.cancel', $leaveRequest->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to cancel this leave request?')">
            @csrf
            @method('POST')
            <button type="submit" class="w-full bg-red-600 text-white py-3 px-6 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 font-medium">
                <i class="fas fa-times mr-2"></i>Cancel Request
            </button>
        </form>
    @endif
    
    @if($leaveRequest->status == 'cancelled')
        <form action="{{ route('employee.leave.retrieve', $leaveRequest->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to retrieve this leave request?')">
            @csrf
            @method('POST')
            <button type="submit" class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200 font-medium">
                <i class="fas fa-undo mr-2"></i>Retrieve Request
            </button>
        </form>
    @endif
    
    @if($leaveRequest->status == 'rejected' && $leaveRequest->rejection_reason)
        <div class="flex-1 bg-red-50 border border-red-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-red-800 mb-2">Rejection Reason</h4>
            <p class="text-sm text-red-700">{{ $leaveRequest->rejection_reason }}</p>
        </div>
    @endif
</div>
    </div>
</div>
@endsection