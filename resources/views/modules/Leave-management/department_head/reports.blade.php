@extends('layouts.app')

@section('title', 'Leave Reports - Nish Auto Limited')
@section('page-title', 'Leave Reports & Analytics')

@section('content')
@auth
    @if(Auth::user()->isHead())
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Leave Reports & Analytics</h1>
                        <p class="text-gray-600">Comprehensive leave analysis and insights for your department</p>
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

                <!-- Date Range Filter -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                            <select class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Last 30 Days</option>
                                <option>Last 3 Months</option>
                                <option>Last 6 Months</option>
                                <option>This Year</option>
                                <option>Custom Range</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                            <select class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Summary Report</option>
                                <option>Detailed Analysis</option>
                                <option>Trend Analysis</option>
                                <option>Team Comparison</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <a href="{{ route('head.reports.export') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 inline-flex items-center">
                                <i class="fas fa-download mr-2"></i>Export Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Leave Days</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $reportData['total_leave_days'] ?? 0 }}</p>
                            <p class="text-sm text-green-600">+12% from last month</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Approval Rate</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $reportData['approval_rate'] ?? 0 }}%</p>
                            <p class="text-sm text-green-600">+5% from last month</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Avg. Processing Time</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $reportData['avg_processing_time'] ?? 0 }}h</p>
                            <p class="text-sm text-red-600">-2h from last month</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Team Coverage</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $reportData['team_coverage'] ?? 0 }}%</p>
                            <p class="text-sm text-green-600">Optimal level</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Leave Distribution Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Leave Type Distribution</h3>
                    <div class="h-80">
                        <canvas id="leaveTypeChart"></canvas>
                    </div>
                </div>

                <!-- Monthly Trend Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Leave Trend</h3>
                    <div class="h-80">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Team Performance -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Team Leave Statistics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Team Leave Statistics</h3>
                    <div class="space-y-4">
                        @foreach($teamStats as $member)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-800 font-semibold text-sm">
                                        {{ substr($member['name'], 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $member['name'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $member['position'] }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ $member['leave_taken'] }} days</p>
                                <p class="text-xs text-gray-500">{{ $member['balance'] }} days left</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Approval Performance -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Approval Performance</h3>
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Approval Rate</span>
                                <span class="text-sm font-medium text-gray-700">{{ $reportData['approval_rate'] ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $reportData['approval_rate'] ?? 0 }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Average Response Time</span>
                                <span class="text-sm font-medium text-gray-700">{{ $reportData['avg_response_time'] ?? 0 }} hours</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Pending Applications</span>
                                <span class="text-sm font-medium text-gray-700">{{ $reportData['pending_applications'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ min(100, (($reportData['pending_applications'] ?? 0) / 10) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Reports Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Detailed Leave Report</h3>
                    <a href="{{ route('head.reports.export') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 inline-flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>Export to Excel
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Employee</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Leave Type</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Period</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Duration</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Status</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Applied On</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Processed On</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($detailedReport as $leave)
                                @php
                                    $statusColors = [
                                        'approved' => 'green',
                                        'pending' => 'yellow',
                                        'rejected' => 'red'
                                    ];
                                    $color = $statusColors[$leave['status']] ?? 'gray';
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-blue-800 font-semibold text-xs">
                                                    {{ substr($leave['employee_name'], 0, 1) }}
                                                </span>
                                            </div>
                                            <span class="font-medium text-gray-800">{{ $leave['employee_name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-sm text-gray-600">{{ $leave['leave_type'] }}</td>
                                    <td class="py-4 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($leave['start_date'])->format('M d') }} - 
                                        {{ \Carbon\Carbon::parse($leave['end_date'])->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 text-sm text-gray-600">{{ $leave['duration'] }} days</td>
                                    <td class="py-4">
                                        <span class="px-3 py-1 bg-{{ $color }}-100 text-{{ $color }}-800 text-xs rounded-full font-medium">
                                            {{ ucfirst($leave['status']) }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($leave['applied_on'])->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 text-sm text-gray-600">
                                        {{ $leave['processed_on'] ? \Carbon\Carbon::parse($leave['processed_on'])->format('M d, Y') : 'Pending' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-500">
                                        No leave data available for the selected period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Leave Type Distribution Chart
                const leaveTypeCtx = document.getElementById('leaveTypeChart').getContext('2d');
                new Chart(leaveTypeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Annual Leave', 'Sick Leave', 'Emergency', 'Maternity', 'Paternity', 'Other'],
                        datasets: [{
                            data: [35, 25, 15, 10, 8, 7],
                            backgroundColor: [
                                '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#6b7280'
                            ],
                            borderWidth: 0
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

                // Monthly Trend Chart
                const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
                new Chart(monthlyTrendCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Leave Days',
                            data: [12, 19, 15, 22, 18, 25, 30, 27, 20, 18, 22, 15],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
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