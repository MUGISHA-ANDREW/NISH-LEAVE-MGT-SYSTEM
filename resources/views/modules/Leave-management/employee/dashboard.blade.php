@extends('layouts.app')

@section('title', 'Employee Dashboard - Nish Auto Limited')
@section('page-title', 'Employee Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-6 text-white mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h2>
            <p class="text-blue-100">Here's your leave management overview for today</p>
        </div>
        <div class="hidden md:block">
            <i class="fas fa-calendar-check text-4xl text-blue-300"></i>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Available Leave -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-calendar-day text-green-600 text-2xl"></i>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $quickStats['available_leave'] }}</h3>
        <p class="text-gray-600 font-medium">Days Available</p>
        <p class="text-sm text-gray-500 mt-1">Annual Leave Balance</p>
    </div>

    <!-- Pending Requests -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clock text-yellow-600 text-2xl"></i>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $quickStats['pending_requests'] }}</h3>
        <p class="text-gray-600 font-medium">Pending</p>
        <p class="text-sm text-gray-500 mt-1">Awaiting Approval</p>
    </div>

    <!-- Used This Year -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $quickStats['used_this_year'] }}</h3>
        <p class="text-gray-600 font-medium">Days Used</p>
        <p class="text-sm text-gray-500 mt-1">This Year</p>
    </div>
</div>

<!-- Leave Types Breakdown -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Leave Types -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">My Leave Balance</h3>
        <div class="space-y-4">
            @foreach($leaveBalances as $balance)
                @php
                    $type = $balance['type'];
                    $colors = [
                        'Annual' => ['color' => 'blue', 'icon' => 'umbrella-beach'],
                        'Sick' => ['color' => 'red', 'icon' => 'procedures'],
                        'Emergency' => ['color' => 'orange', 'icon' => 'exclamation-triangle'],
                        'Maternity' => ['color' => 'purple', 'icon' => 'baby'],
                        'Paternity' => ['color' => 'blue', 'icon' => 'male'],
                        'Other' => ['color' => 'gray', 'icon' => 'calendar-alt']
                    ];
                    $color = $colors[$type->name] ?? $colors['Other'];
                    $progressColor = match($type->name) {
                        'Annual' => 'bg-green-500',
                        'Sick' => 'bg-yellow-500',
                        'Emergency' => 'bg-orange-500',
                        'Maternity' => 'bg-blue-500',
                        'Paternity' => 'bg-blue-500',
                        default => 'bg-gray-500'
                    };
                @endphp
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-{{ $color['icon'] }} text-{{ $color['color'] }}-500"></i>
                        <span class="font-medium text-gray-700">{{ $type->name }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-gray-800">
                            {{ $balance['available'] ?? '∞' }}/{{ $balance['max_days'] ?: '∞' }}
                        </span>
                        @if($balance['max_days'])
                            <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                                <div class="{{ $progressColor }} h-2 rounded-full" style="width: {{ $balance['percentage'] }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Upcoming Leaves -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Upcoming Leaves</h3>
            <a href="{{ route('employee.leave.history') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">View All</a>
        </div>
        <div class="space-y-3">
            @forelse($upcomingLeaves as $leave)
                @php
                    // Get workflow status
                    $workflowStatus = $leave->workflow_status ?? $this->getLeaveStatusWithWorkflow($leave);
                    $badgeColor = $workflowStatus['badge_class'] ?? 'bg-yellow-100 text-yellow-800';
                @endphp
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <div>
                        <p class="font-medium text-gray-800">{{ $leave->leaveType->name }}</p>
                        <p class="text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - 
                            {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                            <span class="ml-2">({{ $leave->total_days }} days)</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $badgeColor }}">
                            {{ $workflowStatus['text'] ?? ucfirst($leave->status) }}
                        </span>
                        
                        @if(($workflowStatus['progress'] ?? 0) > 0 && ($workflowStatus['progress'] ?? 0) < 100)
                        <div class="mt-1">
                            <div class="w-16 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500" style="width: {{ $workflowStatus['progress'] ?? 0 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $workflowStatus['progress'] ?? 0 }}% complete</p>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fas fa-calendar-plus text-gray-400 text-2xl mb-2"></i>
                    <p class="text-gray-500 text-sm">No upcoming leaves</p>
                    <a href="{{ route('employee.leave.create') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium mt-1 inline-block">
                        Apply for leave
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Recent Leave Applications</h3>
        <a href="{{ route('employee.leave.history') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">View All</a>
    </div>
    
    @if($recentApplications->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 text-sm font-medium text-gray-600">Leave Type</th>
                        <th class="text-left py-3 text-sm font-medium text-gray-600">Period</th>
                        <th class="text-left py-3 text-sm font-medium text-gray-600">Duration</th>
                        <th class="text-left py-3 text-sm font-medium text-gray-600">Status</th>
                        <th class="text-left py-3 text-sm font-medium text-gray-600">Progress</th>
                        <th class="text-left py-3 text-sm font-medium text-gray-600">Applied On</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentApplications as $application)
                        @php
                            // Get workflow status
                            $workflowStatus = $application->workflow_status ?? $this->getLeaveStatusWithWorkflow($application);
                            $badgeColor = $workflowStatus['badge_class'] ?? 'bg-yellow-100 text-yellow-800';
                            
                            $icons = [
                                'Annual' => 'umbrella-beach',
                                'Sick' => 'procedures',
                                'Emergency' => 'exclamation-triangle',
                                'Maternity' => 'baby',
                                'Paternity' => 'male',
                                'Other' => 'calendar-alt'
                            ];
                            $icon = $icons[$application->leaveType->name] ?? 'calendar-alt';
                        @endphp
                        <tr>
                            <td class="py-4">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-{{ $icon }} text-blue-500"></i>
                                    <span class="font-medium text-gray-800">{{ $application->leaveType->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($application->start_date)->format('M d') }} - 
                                {{ \Carbon\Carbon::parse($application->end_date)->format('M d, Y') }}
                            </td>
                            <td class="py-4 text-sm text-gray-600">{{ $application->total_days }} days</td>
                            <td class="py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $badgeColor }}">
                                    {{ $workflowStatus['text'] ?? ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="py-4">
                                @if(($workflowStatus['progress'] ?? 0) > 0)
                                    <div class="w-24">
                                        <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full {{ ($workflowStatus['progress'] ?? 0) == 100 ? 'bg-green-500' : 'bg-blue-500' }}" 
                                                 style="width: {{ $workflowStatus['progress'] ?? 0 }}%"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 text-center">{{ $workflowStatus['progress'] ?? 0 }}%</p>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($application->created_at)->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-inbox text-gray-400 text-3xl mb-3"></i>
            <p class="text-gray-500 mb-4">You haven't submitted any leave requests yet.</p>
            <a href="{{ route('employee.leave.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Apply for Leave
            </a>
        </div>
    @endif
</div>

<!-- Approval Workflow Explanation -->
<div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mt-8">
    <h3 class="text-lg font-semibold text-blue-800 mb-3">Leave Approval Workflow</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-paper-plane text-blue-600"></i>
            </div>
            <p class="text-sm font-medium text-blue-800">Submitted</p>
            <p class="text-xs text-blue-600">You apply for leave</p>
        </div>
        
        <div class="text-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-user-tie text-yellow-600"></i>
            </div>
            <p class="text-sm font-medium text-yellow-800">Department Head</p>
            <p class="text-xs text-yellow-600">Approves or rejects</p>
        </div>
        
        <div class="text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-users text-blue-600"></i>
            </div>
            <p class="text-sm font-medium text-blue-800">HR Admin</p>
            <p class="text-xs text-blue-600">Final approval</p>
        </div>
        
        <div class="text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <p class="text-sm font-medium text-green-800">Approved</p>
            <p class="text-xs text-green-600">Leave is confirmed</p>
        </div>
    </div>
    
    <div class="mt-4 text-sm text-blue-700">
        <p><i class="fas fa-info-circle mr-2"></i>Status meanings:</p>
        <ul class="list-disc pl-5 mt-2 space-y-1">
            <li><span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded">Pending Department Head</span> - Waiting for your manager's approval</li>
            <li><span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded">Pending HR</span> - Approved by manager, waiting for HR</li>
            <li><span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded">Approved</span> - Fully approved and confirmed</li>
            <li><span class="px-2 py-0.5 bg-red-100 text-red-800 text-xs rounded">Rejected</span> - Not approved</li>
        </ul>
    </div>
</div>
@endsection

@push('styles')
<style>
    .progress-bar {
        transition: width 0.3s ease;
    }
    
    .status-badge {
        transition: all 0.2s ease;
    }
    
    .status-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to status badges
        document.querySelectorAll('.status-badge').forEach(badge => {
            badge.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
            });
            
            badge.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });
    });
</script>
@endpush