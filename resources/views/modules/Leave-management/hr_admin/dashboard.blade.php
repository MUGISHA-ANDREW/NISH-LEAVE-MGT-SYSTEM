@extends('layouts.app')

@section('title', 'Admin Dashboard - Nish Auto Limited')
@section('page-title', 'Admin Dashboard')

@php
    $isAdmin = true;
    
    function getStatusColor($status) {
        $colors = [
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray'
        ];
        return $colors[$status] ?? 'gray';
    }
@endphp

@section('content')
<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Employees -->
    <div class="stat-card bg-white rounded-axl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Employees</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_employees']) }}</p>
                <p class="text-xs text-green-600 mt-1">
                    <i class="fas fa-users mr-1"></i>Registered Employees
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Pending Approvals</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['pending_approvals']) }}</p>
                <p class="text-xs {{ $stats['pending_approvals'] > 0 ? 'text-red-600' : 'text-green-600' }} mt-1">
                    <i class="fas {{ $stats['pending_approvals'] > 0 ? 'fa-clock text-red-600' : 'fa-check-circle text-green-600' }} mr-1"></i>
                    {{ $stats['pending_approvals'] > 0 ? 'Requires attention' : 'All caught up' }}
                </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- On Leave Today -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">On Leave Today</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['on_leave_today']) }}</p>
                <p class="text-xs text-blue-600 mt-1">
                    <i class="fas fa-calendar mr-1"></i>Across departments
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-day text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Leave Utilization -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Leave Utilization</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['leave_utilization'] }}%</p>
                <p class="text-xs text-purple-600 mt-1">
                    <i class="fas fa-chart-pie mr-1"></i>
                    {{ number_format($stats['leave_utilization_raw']) }}/{{ number_format($stats['total_possible_days']) }} days
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Average Processing Time -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Average Processing Time</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['average_processing_time'] }}h</p>
                <p class="text-xs text-indigo-600 mt-1">
                    <i class="fas fa-hourglass-half mr-1"></i>For leave requests
                </p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-hourglass-half text-indigo-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Approval Rate -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Approval Rate</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['approval_rate'] }}%</p>
                <p class="text-xs text-green-600 mt-1">
                    <i class="fas fa-check-circle mr-1"></i>Of processed requests
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Data -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Leave Statistics Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Leave Type Distribution</h3>
        <div class="h-64">
            <canvas id="leaveTypeChart"></canvas>
        </div>
    </div>

    <!-- Department Distribution -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Department Leave Distribution</h3>
        <div class="h-64">
            <canvas id="departmentChart"></canvas>
        </div>
    </div>
</div>

<!-- Monthly Trend Chart -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Leave Trend ({{ date('Y') }})</h3>
    <div class="h-64">
        <canvas id="monthlyTrendChart"></canvas>
    </div>
</div>

<!-- Recent Activity & Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Leave Requests -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Recent Leave Requests</h3>
            <a href="{{ route('admin.leaves.pending') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">View All</a>
        </div>
        <div class="space-y-4">
            @forelse($recentLeaveRequests as $request)
            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="px-3 py-1 bg-{{ getStatusColor($request->status) }}-100 text-{{ getStatusColor($request->status) }}-800 text-xs rounded-full font-medium capitalize">
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $request->user->first_name }} {{ $request->user->last_name }}</p>
                        <p class="text-xs text-gray-600">{{ $request->user->department->name ?? 'No Department' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ $request->leaveType->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($request->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('M d') }}</p>
                </div>
                <span class="px-3 py-1 bg-{{ getStatusColor($request->status) }}-100 text-{{ getStatusColor($request->status) }}-800 text-xs rounded-full font-medium capitalize">
            </div>
            @empty
            <div class="text-center py-8">
                <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-500">No recent leave requests</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="space-y-3">
            <a href="{{ route('admin.employees.create') }}" class="w-full flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-200 transition duration-200">
                <i class="fas fa-user-plus text-blue-600"></i>
                <span class="text-sm font-medium text-gray-700">Add New Employee</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="w-full flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-200 transition duration-200">
                <i class="fas fa-file-export text-green-600"></i>
                <span class="text-sm font-medium text-gray-700">Generate Report</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="w-full flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-200 transition duration-200">
                <i class="fas fa-cog text-purple-600"></i>
                <span class="text-sm font-medium text-gray-700">System Settings</span>
            </a>
            <a href="{{ route('admin.leave.types') }}" class="w-full flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-orange-50 hover:border-orange-200 transition duration-200">
                <i class="fas fa-calendar-alt text-orange-600"></i>
                <span class="text-sm font-medium text-gray-700">Manage Leave Types</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Leave Type Distribution Chart
        const leaveTypeCtx = document.getElementById('leaveTypeChart').getContext('2d');
        const leaveTypeData = @json($leaveStatistics);
        
        const leaveTypeChart = new Chart(leaveTypeCtx, {
            type: 'doughnut',
            data: {
                labels: leaveTypeData.map(item => item.label),
                datasets: [{
                    data: leaveTypeData.map(item => item.count),
                    backgroundColor: leaveTypeData.map(item => item.color),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        // Department Distribution Chart
        const deptCtx = document.getElementById('departmentChart').getContext('2d');
        const deptData = @json($departmentDistribution);
        
        const departmentChart = new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: deptData.map(item => item.label),
                datasets: [{
                    label: 'Employees',
                    data: deptData.map(item => item.employee_count),
                    backgroundColor: deptData.map(item => item.color),
                    borderColor: deptData.map(item => item.color),
                    borderWidth: 1
                }, {
                    label: 'Leave Requests',
                    data: deptData.map(item => item.leave_count),
                    backgroundColor: deptData.map(item => hexToRgba(item.color, 0.5)),
                    borderColor: deptData.map(item => item.color),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Monthly Trend Chart
        const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        const trendData = @json($monthlyTrend);
        
        const monthlyTrendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [{
                    label: 'Leave Requests',
                    data: trendData.data,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderColor: '#3B82F6',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Helper function to convert hex to rgba
        function hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        // Real-time stats update (optional - every 30 seconds)
        setInterval(() => {
            fetch('{{ route("admin.dashboard.stats") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update stats cards
                        updateStatCard('Total Employees', data.data.total_employees);
                        updateStatCard('Pending Approvals', data.data.pending_approvals);
                        updateStatCard('On Leave Today', data.data.on_leave_today);
                        updateStatCard('Leave Utilization', data.data.leave_utilization + '%');
                    }
                });
        }, 30000);
        
        function updateStatCard(statName, value) {
            // You can implement this to update specific stat cards
            console.log(`Updating ${statName} to ${value}`);
        }
    });
</script>
@endpush