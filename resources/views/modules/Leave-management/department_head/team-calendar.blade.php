@extends('layouts.app')

@section('title', 'Team Calendar - Nish Auto Limited')
@section('page-title', 'Team Calendar')

@section('content')
@auth
    @if(Auth::user()->isHead())
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Team Calendar</h1>
                        <p class="text-gray-600">View your team's leave schedule and availability</p>
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
                                <p class="text-sm text-blue-600 font-medium">On Leave Today</p>
                                <p class="text-2xl font-bold text-blue-800">{{ $stats['on_leave_today'] ?? 0 }}</p>
                            </div>
                            <i class="fas fa-calendar-times text-blue-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-600 font-medium">Available Today</p>
                                <p class="text-2xl font-bold text-green-800">{{ $stats['available_today'] ?? 0 }}</p>
                            </div>
                            <i class="fas fa-user-check text-green-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-yellow-600 font-medium">Upcoming Leaves</p>
                                <p class="text-2xl font-bold text-yellow-800">{{ $stats['upcoming_leaves'] ?? 0 }}</p>
                            </div>
                            <i class="fas fa-calendar-alt text-yellow-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-purple-600 font-medium">Team Size</p>
                                <p class="text-2xl font-bold text-purple-800">{{ $stats['team_size'] ?? 0 }}</p>
                            </div>
                            <i class="fas fa-users text-purple-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Controls -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-lg font-semibold text-gray-800" id="calendarTitle">
                            {{ \Carbon\Carbon::now()->format('F Y') }}
                        </h2>
                        <div class="flex space-x-2">
                            <button id="prevMonth" class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <i class="fas fa-chevron-left text-gray-600"></i>
                            </button>
                            <button id="todayBtn" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                Today
                            </button>
                            <button id="nextMonth" class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <i class="fas fa-chevron-right text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <select id="viewSelector" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="month">Month View</option>
                            <option value="week">Week View</option>
                            <option value="day">Day View</option>
                        </select>
                        <a href="{{ route('head.calendar.export') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 inline-flex items-center">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
                </div>
            </div>

            <!-- Calendar View -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div id="calendar" class="min-h-96">
                    <!-- Calendar will be rendered here by JavaScript -->
                    <div class="text-center py-12">
                        <i class="fas fa-calendar text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-500">Calendar loading...</p>
                    </div>
                </div>
            </div>

            <!-- Team Availability -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Current Month Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">This Month's Leave Summary</h3>
                    <div class="space-y-3">
                        @forelse($monthlySummary as $summary)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-800 font-semibold text-sm">
                                        {{ substr($summary['employee_name'], 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $summary['employee_name'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $summary['leave_days'] }} day(s) off</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @php
                                    $statusColor = $summary['status_color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
                                @endphp
                                <span class="px-2 py-1 {{ $statusColor }} text-xs rounded-full">
                                    {{ $summary['status'] }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <i class="fas fa-users text-gray-400 text-2xl mb-2"></i>
                            <p class="text-gray-500 text-sm">No team members found</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Upcoming Leaves -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Leaves (Next 30 Days)</h3>
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
                                    <p class="text-sm text-gray-600">{{ $leave->leaveType->name ?? 'N/A' }}</p>
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
                            <p class="text-gray-500 text-sm">No upcoming leaves in the next 30 days</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Calendar Legend</h3>
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-green-100 border border-green-300 rounded"></div>
                        <span class="text-sm text-gray-600">Available</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-red-100 border border-red-300 rounded"></div>
                        <span class="text-sm text-gray-600">On Leave</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-yellow-100 border border-yellow-300 rounded"></div>
                        <span class="text-sm text-gray-600">Partial Day</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded"></div>
                        <span class="text-sm text-gray-600">Public Holiday</span>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let currentDate = new Date();
                const calendarEl = document.getElementById('calendar');
                const calendarTitle = document.getElementById('calendarTitle');
                const prevMonthBtn = document.getElementById('prevMonth');
                const nextMonthBtn = document.getElementById('nextMonth');
                const todayBtn = document.getElementById('todayBtn');

                // Fetch real leave data from backend
                async function fetchLeaveData(year, month) {
                    try {
                        const response = await fetch(`/api/team-leaves?year=${year}&month=${month + 1}`);
                        return await response.json();
                    } catch (error) {
                        console.error('Error fetching leave data:', error);
                        return [];
                    }
                }

                function renderCalendar() {
                    const year = currentDate.getFullYear();
                    const month = currentDate.getMonth();
                    
                    calendarTitle.textContent = currentDate.toLocaleString('default', { 
                        month: 'long', 
                        year: 'numeric' 
                    });

                    // Simple calendar rendering
                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
                    const daysInMonth = lastDay.getDate();
                    const startingDay = firstDay.getDay();

                    let calendarHTML = `
                        <div class="grid grid-cols-7 gap-1 mb-4">
                            <div class="text-center py-2 text-sm font-medium text-gray-500">Sun</div>
                            <div class="text-center py-2 text-sm font-medium text-gray-500">Mon</div>
                            <div class="text-center py-2 text-sm font-medium text-gray-500">Tue</div>
                            <div class="text-center py-2 text-sm font-medium text-gray-500">Wed</div>
                            <div class="text-center py-2 text-sm font-medium text-gray-500">Thu</div>
                            <div class="text-center py-2 text-sm font-medium text-gray-500">Fri</div>
                            <div class="text-center py-2 text-sm font-medium text-gray-500">Sat</div>
                        </div>
                        <div class="grid grid-cols-7 gap-1">
                    `;

                    // Empty cells for days before the first day of the month
                    for (let i = 0; i < startingDay; i++) {
                        calendarHTML += `<div class="h-20 border border-gray-200 rounded-lg bg-gray-50"></div>`;
                    }

                    // Days of the month
                    for (let day = 1; day <= daysInMonth; day++) {
                        const date = new Date(year, month, day);
                        const isToday = date.toDateString() === new Date().toDateString();
                        const dateString = date.toISOString().split('T')[0];
                        
                        // In real implementation, check against actual leave data
                        const hasLeave = Math.random() > 0.8; // Temporary simulation
                        
                        calendarHTML += `
                            <div class="h-20 border border-gray-200 rounded-lg p-1 ${isToday ? 'bg-blue-50 border-blue-300' : ''}">
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium ${isToday ? 'text-blue-600' : 'text-gray-900'}">${day}</span>
                                    ${hasLeave ? '<span class="w-2 h-2 bg-red-400 rounded-full"></span>' : ''}
                                </div>
                                ${hasLeave ? `
                                    <div class="mt-1 space-y-1">
                                        <div class="text-xs bg-red-100 text-red-800 px-1 rounded truncate">Team Member - Leave</div>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    }

                    calendarHTML += '</div>';
                    calendarEl.innerHTML = calendarHTML;
                }

                // Event listeners
                prevMonthBtn.addEventListener('click', function() {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    renderCalendar();
                });

                nextMonthBtn.addEventListener('click', function() {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    renderCalendar();
                });

                todayBtn.addEventListener('click', function() {
                    currentDate = new Date();
                    renderCalendar();
                });

                // Initial render
                renderCalendar();

                // View selector
                document.getElementById('viewSelector').addEventListener('change', function(e) {
                    // In a real implementation, this would switch between month/week/day views
                    console.log('Switching to ' + e.target.value + ' view');
                    // You can implement different view renderers here
                });
            });
        </script>
        @endpush

    @else
        <!-- Access Denied Message -->
        <div class="max-w-2xl mx-auto mt-8">
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-8 rounded-2xl text-center">
                <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                <h2 class="text-2xl font-bold mb-2">Access Denied</h2>
                <p class="text-lg mb-4">This page is for department heads only.</p>
                <a href="{{ route('dashboard') }}" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition duration-200">
                    Return to Dashboard
                </a>
            </div>
        </div>
    @endif
@else
    <!-- Not Authenticated Message -->
    <div class="max-w-2xl mx-auto mt-8">
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-8 rounded-2xl text-center">
            <i class="fas fa-lock text-4xl mb-4"></i>
            <h2 class="text-2xl font-bold mb-2">Authentication Required</h2>
            <p class="text-lg mb-4">Please log in to access this page.</p>
            <a href="{{ route('login') }}" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition duration-200">
                Login Now
            </a>
        </div>
    </div>
@endauth
@endsection