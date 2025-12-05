@extends('layouts.app')

@section('title', 'Leave Calendar - Nish Auto Limited')
@section('page-title', 'Leave Calendar')

@section('content')
<div class="space-y-6">
    <!-- Calendar Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Company Leave Calendar</h2>
                <p class="text-gray-600 mt-1">View approved leaves and plan accordingly</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Month Navigation -->
                <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-2">
                    <a href="?month={{ $currentDate->copy()->subMonth()->format('Y-m') }}" 
                       class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white transition duration-200">
                        <i class="fas fa-chevron-left text-gray-600"></i>
                    </a>
                    <span class="text-lg font-semibold text-gray-800">{{ $currentDate->format('F Y') }}</span>
                    <a href="?month={{ $currentDate->copy()->addMonth()->format('Y-m') }}" 
                       class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white transition duration-200">
                        <i class="fas fa-chevron-right text-gray-600"></i>
                    </a>
                </div>
                
                <!-- View Toggle -->
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button class="px-3 py-2 rounded-md bg-white shadow-sm text-sm font-medium text-gray-800">Month</button>
                    <button class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-800">Week</button>
                    <button class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-800">Day</button>
                </div>
                
                <!-- Today Button -->
                <a href="?month={{ date('Y-m') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 text-sm font-medium">
                    Today
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Calendar -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Calendar Header -->
                <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="p-4 text-center text-sm font-medium text-gray-500">{{ $day }}</div>
                    @endforeach
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7">
                    @foreach($calendarData as $day)
                        <div class="min-h-32 p-2 border-r border-b border-gray-200 calendar-day 
                                    {{ !$day['is_current_month'] ? 'bg-gray-50' : '' }}
                                    {{ $day['is_today'] ? 'today bg-blue-50 border-blue-200' : '' }}
                                    {{ $day['is_public_holiday'] && !empty($day['public_holiday']) ? 'bg-red-50' : '' }}">
                            <div class="text-right mb-2">
                                <span class="inline-block w-6 h-6 text-sm 
                                            {{ !$day['is_current_month'] ? 'text-gray-400' : 'text-gray-600' }}
                                            {{ $day['is_today'] ? 'font-bold text-blue-600' : '' }}
                                            {{ $day['is_public_holiday'] && !empty($day['public_holiday']) ? 'font-bold text-red-600' : '' }}">
                                    {{ $day['date']->format('j') }}
                                </span>
                            </div>
                            
                            @if($day['is_public_holiday'] && !empty($day['public_holiday']))
                                <div class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded mb-1 truncate" 
                                     title="{{ $day['public_holiday']['name'] ?? 'Holiday' }}">
                                    <i class="fas fa-{{ $day['public_holiday']['icon'] ?? 'calendar' }} mr-1"></i>
                                    Holiday
                                </div>
                            @endif

                            <div class="space-y-1 max-h-20 overflow-y-auto">
                                @foreach($day['leaves']->take(3) as $leave)
                                    @php
                                        $colors = [
                                            'Annual' => ['bg' => 'blue', 'icon' => 'umbrella-beach'],
                                            'Sick' => ['bg' => 'green', 'icon' => 'procedures'],
                                            'Emergency' => ['bg' => 'orange', 'icon' => 'exclamation-triangle'],
                                            'Maternity' => ['bg' => 'purple', 'icon' => 'baby'],
                                            'Paternity' => ['bg' => 'blue', 'icon' => 'male'],
                                            'Other' => ['bg' => 'gray', 'icon' => 'calendar-alt']
                                        ];
                                        $leaveTypeName = $leave->leaveType->name ?? 'Other';
                                        $color = $colors[$leaveTypeName] ?? $colors['Other'];
                                        $initials = implode('', array_map(fn($n) => $n[0], explode(' ', $leave->user->name)));
                                    @endphp
                                    <div class="text-xs bg-{{ $color['bg'] }}-100 text-{{ $color['bg'] }}-800 px-2 py-1 rounded truncate calendar-event"
                                         title="{{ $leave->user->name }} - {{ $leave->leaveType->name ?? 'Leave' }}">
                                        <i class="fas fa-{{ $color['icon'] }} mr-1"></i>
                                        {{ \Illuminate\Support\Str::limit($leave->user->name, 10) }}
                                    </div>
                                @endforeach
                                
                                @if($day['leaves']->count() > 3)
                                    <div class="text-xs text-gray-500 px-2 py-1">
                                        +{{ $day['leaves']->count() - 3 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Legend -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mt-6">
                <h4 class="text-sm font-medium text-gray-800 mb-3">Legend</h4>
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded"></div>
                        <span class="text-sm text-gray-600">Annual Leave</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded"></div>
                        <span class="text-sm text-gray-600">Sick Leave</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-purple-500 rounded"></div>
                        <span class="text-sm text-gray-600">Maternity Leave</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-orange-500 rounded"></div>
                        <span class="text-sm text-gray-600">Emergency Leave</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded"></div>
                        <span class="text-sm text-gray-600">Public Holiday</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">This Month</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">On Leave Today</span>
                        <span class="font-bold text-blue-600">{{ $monthlyStats['today_leaves'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total This Month</span>
                        <span class="font-bold text-green-600">{{ $monthlyStats['total_leaves'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Your Team</span>
                        <span class="font-bold text-purple-600">{{ $monthlyStats['team_leaves'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Public Holidays</span>
                        <span class="font-bold text-red-600">{{ $monthlyStats['public_holidays'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Upcoming Leaves -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Upcoming Leaves</h3>
                    <a href="{{ route('employee.leave.create') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        Apply
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingLeaves as $leave)
                        @php
                            $colors = [
                                'Annual' => 'blue',
                                'Sick' => 'green',
                                'Emergency' => 'orange',
                                'Maternity' => 'purple',
                                'Paternity' => 'blue',
                                'Other' => 'gray'
                            ];
                            $leaveTypeName = $leave->leaveType->name ?? 'Other';
                            $color = $colors[$leaveTypeName] ?? 'gray';
                            $initials = implode('', array_map(fn($n) => $n[0], explode(' ', $leave->user->name)));
                        @endphp
                        <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                            <div class="w-8 h-8 bg-{{ $color }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-{{ $color }}-600 text-xs font-semibold">{{ $initials }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $leave->user->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $leave->start_date->format('M d') }}-{{ $leave->end_date->format('d') }} • {{ $leave->leaveType->name ?? 'Leave' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No upcoming leaves</p>
                    @endforelse
                </div>
            </div>

            <!-- Public Holidays -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Public Holidays</h3>
                <div class="space-y-3">
                    @php
                        $upcomingHolidays = $publicHolidays->where('date', '>=', today())->take(3) ?? collect();
                    @endphp
                    @forelse($upcomingHolidays as $holiday)
                        <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-red-800">{{ $holiday['name'] ?? 'Holiday' }}</p>
                                <p class="text-xs text-red-600">{{ $holiday['date']->format('F j') ?? '' }}</p>
                            </div>
                            <i class="fas fa-{{ $holiday['icon'] ?? 'calendar' }} text-red-500"></i>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No upcoming public holidays</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Filters</h3>
                <div class="space-y-3">
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option>All Departments</option>
                        <option>Assembly</option>
                        <option>Spare Parts</option>
                        <option>Mechanical</option>
                    </select>
                    
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option>All Leave Types</option>
                        <option>Annual Leave</option>
                        <option>Sick Leave</option>
                        <option>Maternity Leave</option>
                    </select>
                    
                    <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200 text-sm font-medium">
                        Apply Filters
                    </button>
                    
                    <button class="w-full bg-gray-200 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-200 text-sm font-medium">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .calendar-day {
        transition: all 0.2s ease;
    }
    
    .calendar-day:hover {
        background-color: #f8fafc;
    }
    
    .calendar-event {
        transition: all 0.2s ease;
    }
    
    .calendar-event:hover {
        transform: translateX(2px);
    }
    
    .today {
        background-color: #dbeafe;
        border-color: #3b82f6;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to calendar events
        const calendarEvents = document.querySelectorAll('.calendar-event');
        calendarEvents.forEach(event => {
            event.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
            });
            event.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Add click handlers for view toggle buttons
        const viewButtons = document.querySelectorAll('.flex.bg-gray-100 button');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                viewButtons.forEach(btn => {
                    btn.classList.remove('bg-white', 'shadow-sm', 'text-gray-800');
                    btn.classList.add('text-gray-600');
                });
                this.classList.add('bg-white', 'shadow-sm', 'text-gray-800');
                this.classList.remove('text-gray-600');
                
                const view = this.textContent;
                alert(`Switching to ${view} view would be implemented here.`);
            });
        });
    });
</script>
@endpush