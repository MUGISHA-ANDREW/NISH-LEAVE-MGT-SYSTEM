@extends('layouts.app')

@section('title', 'Team Members - Nish Auto Limited')
@section('page-title', 'Team Members Management')

@section('content')
@auth
    @if(Auth::user()->isHead())
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Team Members</h1>
                        <p class="text-gray-600">Manage your department team members and their leave information</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Department</p>
                            <p class="font-semibold text-gray-800">{{ $department->name ?? 'Your Department' }}</p>
                        </div>
                        <a href="{{ route('head.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-600 font-medium">Total Members</p>
                                <p class="text-2xl font-bold text-blue-800">{{ $teamStats['total_members'] ?? 0 }}</p>
                            </div>
                            <i class="fas fa-users text-blue-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-600 font-medium">On Leave Today</p>
                                <p class="text-2xl font-bold text-green-800">{{ $teamStats['on_leave_today'] ?? 0 }}</p>
                            </div>
                            <i class="fas fa-calendar-times text-green-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-yellow-600 font-medium">Pending Approvals</p>
                                <p class="text-2xl font-bold text-yellow-800">{{ $teamStats['pending_approvals'] ?? 0 }}</p>
                            </div>
                            <i class="fas fa-clock text-yellow-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-purple-600 font-medium">Available Today</p>
                                <p class="text-2xl font-bold text-purple-800">{{ ($teamStats['total_members'] ?? 0) - ($teamStats['on_leave_today'] ?? 0) }}</p>
                            </div>
                            <i class="fas fa-user-check text-purple-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Team Members List</h2>
                    <div class="flex space-x-3">
                        <div class="relative">
                            <input type="text" 
                                   placeholder="Search team members..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   id="searchInput">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        <a href="{{ route('head.team.members.export') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 inline-flex items-center">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
                </div>

                @if($teamMembers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 text-sm font-medium text-gray-600">Employee</th>
                                    <th class="text-left py-3 text-sm font-medium text-gray-600">Position</th>
                                    <th class="text-left py-3 text-sm font-medium text-gray-600">Email</th>
                                    <th class="text-left py-3 text-sm font-medium text-gray-600">Leave Balance</th>
                                    <th class="text-left py-3 text-sm font-medium text-gray-600">Current Status</th>
                                    <th class="text-left py-3 text-sm font-medium text-gray-600">Last Leave</th>
                                    <th class="text-left py-3 text-sm font-medium text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($teamMembers as $member)
                                    @php
                                        $status = $member->is_on_leave ? 'On Leave' : 'Available';
                                        $statusColor = $member->is_on_leave ? 'red' : 'green';
                                        $leaveBalance = $member->leave_balance ?? 21; // Default annual leave
                                    @endphp
                                    <tr class="hover:bg-gray-50 member-row">
                                        <td class="py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <span class="text-blue-800 font-semibold text-sm">
                                                        {{ substr($member->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-800 member-name">{{ $member->name }}</p>
                                                    <p class="text-sm text-gray-600">ID: {{ $member->employee_id ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 text-sm text-gray-600">
                                            {{ $member->designation ?? 'Employee' }}
                                        </td>
                                        <td class="py-4 text-sm text-gray-600">
                                            {{ $member->email }}
                                        </td>
                                        <td class="py-4">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-500 h-2 rounded-full" 
                                                         style="width: {{ min(100, ($leaveBalance / 30) * 100) }}%"></div>
                                                </div>
                                                <span class="text-sm font-medium text-gray-800">{{ $leaveBalance }} days</span>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <span class="px-3 py-1 bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 text-xs rounded-full font-medium">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td class="py-4 text-sm text-gray-600">
                                            @if($member->last_leave)
                                                {{ \Carbon\Carbon::parse($member->last_leave)->format('M d, Y') }}
                                            @else
                                                <span class="text-gray-400">No leave taken</span>
                                            @endif
                                        </td>
                                        <td class="py-4">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('head.leave.details', $member->id) }}" 
                                                   class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-lg hover:bg-blue-200 transition-colors">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </a>
                                                <button class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-lg hover:bg-green-200 transition-colors">
                                                    <i class="fas fa-calendar mr-1"></i>Schedule
                                                </button>
                                                <button class="px-3 py-1 bg-purple-100 text-purple-800 text-xs rounded-lg hover:bg-purple-200 transition-colors">
                                                    <i class="fas fa-chart-bar mr-1"></i>Report
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $teamMembers->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-users text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Team Members Found</h3>
                        <p class="text-gray-500 mb-6">There are no team members in your department.</p>
                    </div>
                @endif
            </div>

            <!-- Leave Statistics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <!-- Leave Distribution -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Leave Distribution</h3>
                    <div class="h-64">
                        <canvas id="leaveDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Upcoming Leaves -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Leaves</h3>
                    <div class="space-y-3">
                        @forelse($upcomingLeaves as $leave)
                            <div class="flex items-center justify-between p-3 border border-yellow-200 bg-yellow-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <span class="text-yellow-800 font-semibold text-xs">
                                            {{ substr($leave->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $leave->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $leave->leaveType->name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $leave->total_days }} days</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-check text-gray-400 text-2xl mb-2"></i>
                                <p class="text-gray-500 text-sm">No upcoming leaves</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Search functionality
                const searchInput = document.getElementById('searchInput');
                const memberRows = document.querySelectorAll('.member-row');

                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    memberRows.forEach(row => {
                        const memberName = row.querySelector('.member-name').textContent.toLowerCase();
                        if (memberName.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });

                // Leave Distribution Chart
                const leaveDistributionCtx = document.getElementById('leaveDistributionChart');
                if (leaveDistributionCtx) {
                    new Chart(leaveDistributionCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Annual Leave', 'Sick Leave', 'Emergency', 'Other'],
                            datasets: [{
                                data: [45, 25, 15, 15],
                                backgroundColor: [
                                    '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
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