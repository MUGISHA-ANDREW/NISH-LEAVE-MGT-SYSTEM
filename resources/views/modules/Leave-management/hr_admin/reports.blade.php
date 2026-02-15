@extends('layouts.app')

@section('title', 'Reports - Nish Auto Limited')
@section('page-title', 'Reports & Analytics')

@php
    $isAdmin = true;
    use Carbon\Carbon;
    
    // Dynamic data passed from controller
    $totalLeaveDays = $stats['total_leave_days'] ?? 0;
    $avgLeaveDuration = $stats['avg_leave_duration'] ?? 0;
    $mostCommonLeaveType = $stats['most_common_leave_type'] ?? 'N/A';
    $approvalRate = $stats['approval_rate'] ?? 0;
    $departments = $departments ?? collect();
    $leaveTypes = $leaveTypes ?? collect();
    $monthlyData = $monthlyData ?? [];
    $leaveDistribution = $leaveDistribution ?? [];
    $departmentStats = $departmentStats ?? [];
    $recentLeaves = $recentLeaves ?? collect();
@endphp

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
    {{ session('error') }}
</div>
@endif

@section('content')
<!-- Report Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Report Filters</h3>
    <form method="GET" action="{{ route('admin.reports') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="7" {{ request('period') == '7' ? 'selected' : '' }}>Last 7 days</option>
                    <option value="30" {{ request('period') == '30' ? 'selected' : '' }}>Last 30 days</option>
                    <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>Last 3 months</option>
                    <option value="180" {{ request('period') == '180' ? 'selected' : '' }}>Last 6 months</option>
                    <option value="ytd" {{ request('period') == 'ytd' ? 'selected' : '' }}>Year to date</option>
                    <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom range</option>
                </select>
            </div>
            
            <!-- Department -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Report Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                <select name="report_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="summary" {{ request('report_type') == 'summary' ? 'selected' : '' }}>Leave Summary</option>
                    <option value="department" {{ request('report_type') == 'department' ? 'selected' : '' }}>Department Analysis</option>
                    <option value="trends" {{ request('report_type') == 'trends' ? 'selected' : '' }}>Leave Trends</option>
                    <option value="employee" {{ request('report_type') == 'employee' ? 'selected' : '' }}>Employee Utilization</option>
                </select>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-end space-x-3">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 font-medium">
                    Generate Report
                </button>
                <a href="{{ route('admin.reports.download-pdf') }}?{{ http_build_query(request()->all()) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition duration-200 flex items-center" title="Download PDF Report">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <button type="button" onclick="exportReport()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200" title="Export CSV">
                    <i class="fas fa-download text-gray-600"></i>
                </button>
            </div>
        </div>
        
        <!-- Custom Date Range (hidden by default) -->
        @if(request('period') == 'custom')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
        </div>
        @endif
    </form>
</div>

<!-- Report Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Leave Days -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Leave Days</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalLeaveDays) }}</p>
                <p class="text-xs text-blue-600 mt-1">
                    <i class="fas fa-calendar-alt mr-1"></i>This period
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Average Leave Duration -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Avg. Leave Duration</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($avgLeaveDuration, 1) }}</p>
                <p class="text-xs text-green-600 mt-1">
                    <i class="fas fa-clock mr-1"></i>Days per request
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-hourglass-half text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Most Common Leave Type -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Most Common Type</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $mostCommonLeaveType }}</p>
                <p class="text-xs text-purple-600 mt-1">
                    <i class="fas fa-stethoscope mr-1"></i>
                    @if(isset($stats['most_common_percentage']))
                        {{ number_format($stats['most_common_percentage'], 1) }}% of all leaves
                    @else
                        Most requested
                    @endif
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-stethoscope text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Approval Rate -->
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Approval Rate</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($approvalRate, 1) }}%</p>
                <p class="text-xs text-orange-600 mt-1">
                    <i class="fas fa-check-circle mr-1"></i>Of all requests
                </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Visualizations -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Leave Trends Over Time -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Leave Trends Over Time</h3>
            <div class="flex space-x-2">
                <button onclick="updateChartPeriod('monthly')" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg font-medium">Monthly</button>
                <button onclick="updateChartPeriod('quarterly')" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-lg font-medium">Quarterly</button>
                <button onclick="updateChartPeriod('yearly')" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-lg font-medium">Yearly</button>
            </div>
        </div>
        <div class="h-64">
            <canvas id="trendsChart"></canvas>
        </div>
    </div>

    <!-- Leave Type Distribution -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Leave Type Distribution</h3>
        <div class="h-64">
            <canvas id="leaveTypeChart"></canvas>
        </div>
    </div>
</div>

<!-- Detailed Reports Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Recent Leave Requests</h3>
        <div class="flex space-x-3">
            <a href="{{ route('admin.leaves.history') }}" class="flex items-center space-x-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                <i class="fas fa-list text-gray-600"></i>
                <span class="text-sm font-medium text-gray-700">View All</span>
            </a>
            <a href="{{ route('leaves.export') }}?{{ http_build_query(request()->all()) }}" class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                <i class="fas fa-file-export"></i>
                <span class="text-sm font-medium">Export</span>
            </a>
        </div>
    </div>
    
    @if($recentLeaves->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentLeaves as $leave)
                        @php
                            $statusColors = [
                                'approved' => 'green',
                                'pending' => 'yellow',
                                'rejected' => 'red',
                                'cancelled' => 'gray'
                            ];
                            $statusColor = $statusColors[$leave->status] ?? 'gray';
                            $initials = substr($leave->user->first_name ?? 'E', 0, 1) . substr($leave->user->last_name ?? 'M', 0, 1);
                        @endphp
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-3 px-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-blue-600 text-sm font-semibold">{{ strtoupper($initials) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">
                                        {{ $leave->user->first_name }} {{ $leave->user->last_name }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ $leave->user->department->name ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ $leave->leaveType->name ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ $leave->total_days }} days
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 rounded-full font-medium capitalize">
                                    {{ $leave->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ Carbon::parse($leave->start_date)->format('d M Y') }} - 
                                {{ Carbon::parse($leave->end_date)->format('d M Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-calendar-times text-gray-400 text-3xl mb-3"></i>
            <p class="text-gray-500">No leave requests found for the selected period</p>
        </div>
    @endif
</div>

<!-- Department Comparison -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Department Comparison</h3>
    <div class="h-64">
        <canvas id="departmentComparisonChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare data from PHP
        const monthlyLabels = @json(array_keys($monthlyData));
        const monthlyValues = @json(array_values($monthlyData));
        
        const leaveTypeLabels = @json(array_keys($leaveDistribution));
        const leaveTypeValues = @json(array_values($leaveDistribution));
        
        const deptLabels = @json(array_column($departmentStats, 'name'));
        const deptLeaveDays = @json(array_column($departmentStats, 'leave_days'));
        const deptAvgDuration = @json(array_column($departmentStats, 'avg_duration'));

        // Leave Trends Chart
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        const trendsChart = new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Leave Requests',
                    data: monthlyValues,
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
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Requests'
                        }
                    }
                }
            }
        });

        // Leave Type Distribution Chart
        const leaveTypeCtx = document.getElementById('leaveTypeChart').getContext('2d');
        const leaveTypeChart = new Chart(leaveTypeCtx, {
            type: 'pie',
            data: {
                labels: leaveTypeLabels,
                datasets: [{
                    data: leaveTypeValues,
                    backgroundColor: [
                        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', 
                        '#8B5CF6', '#6B7280', '#EC4899', '#14B8A6'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        // Department Comparison Chart
        const deptCompCtx = document.getElementById('departmentComparisonChart').getContext('2d');
        const deptCompChart = new Chart(deptCompCtx, {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [{
                    label: 'Total Leave Days',
                    data: deptLeaveDays,
                    backgroundColor: '#3B82F6',
                    borderColor: '#3B82F6',
                    borderWidth: 1
                }, {
                    label: 'Avg. Duration (Days)',
                    data: deptAvgDuration,
                    backgroundColor: '#10B981',
                    borderColor: '#10B981',
                    borderWidth: 1,
                    type: 'line',
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Leave Days'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Average Duration (Days)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        window.updateChartPeriod = function(period) {
            // Update URL with new period parameter
            const url = new URL(window.location.href);
            url.searchParams.set('chart_period', period);
            window.location.href = url.toString();
        };

        window.exportReport = function() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = `{{ route('leaves.export') }}?${params.toString()}`;
        };
    });
</script>
@endpush