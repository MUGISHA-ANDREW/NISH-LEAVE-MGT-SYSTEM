@extends('layouts.app')

@section('title', 'Leave History - Department Head - Nish Auto Limited')
@section('page-title', 'Department Leave History')

@php
    $isAdmin = false;
    use Carbon\Carbon;
@endphp

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>All Employees</option>
                </select>
                
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>All Status</option>
                    <option>Approved</option>
                    <option>Rejected</option>
                </select>
                
                <input type="month" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <a href="{{ route('head.history.export') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium inline-flex items-center">
                <i class="fas fa-download mr-2"></i>Export Report
            </a>
        </div>
    </div>

    <!-- Leave History Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Details</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stand-In</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leaveHistory as $leave)
                        @php
                            $initials = 'NA';
                            if ($leave->user) {
                                $first = substr($leave->user->first_name ?? 'E', 0, 1);
                                $last = substr($leave->user->last_name ?? 'M', 0, 1);
                                $initials = strtoupper($first . $last);
                            }
                            
                            $statusColors = [
                                'pending' => 'orange',
                                'approved' => 'green',
                                'rejected' => 'red',
                                'cancelled' => 'gray'
                            ];
                            $statusColor = $statusColors[$leave->status] ?? 'gray';
                            
                            $deptHeadApproval = $leave->approvals->where('level', 'department_head')->first();
                        @endphp
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-blue-600 text-sm font-semibold">{{ $initials }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $leave->user->first_name ?? '' }} {{ $leave->user->last_name ?? '' }}</div>
                                        <div class="text-sm text-gray-500">{{ $leave->user->designation ?? '' }}</div>
                                        <div class="text-xs text-gray-400">{{ $leave->user->employee_id ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-blue-500 text-lg mr-3"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $leave->leaveType->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($leave->reason, 40) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $leave->start_date ? Carbon::parse($leave->start_date)->format('M d') : '' }} - 
                                    {{ $leave->end_date ? Carbon::parse($leave->end_date)->format('M d, Y') : '' }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $leave->total_days ?? 0 }} day(s)</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($leave->standInEmployee)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-7 h-7 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <span class="text-indigo-600 text-xs font-semibold">
                                                {{ strtoupper(substr($leave->standInEmployee->first_name, 0, 1) . substr($leave->standInEmployee->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $leave->standInEmployee->first_name }} {{ $leave->standInEmployee->last_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $leave->standInEmployee->employee_id ?? '' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 text-xs rounded-full font-medium capitalize">{{ $leave->status }}</span>
                                @if($deptHeadApproval)
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        {{ ucfirst($deptHeadApproval->status) }} by you
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('head.leave.details', $leave->id) }}" class="text-blue-600 hover:text-blue-900">View Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500">No leave history found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leaveHistory->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $leaveHistory->firstItem() ?? 0 }}</span> 
                        to <span class="font-medium">{{ $leaveHistory->lastItem() ?? 0 }}</span> 
                        of <span class="font-medium">{{ $leaveHistory->total() ?? 0 }}</span> requests
                    </div>
                    <div class="flex space-x-2">
                        {{ $leaveHistory->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection