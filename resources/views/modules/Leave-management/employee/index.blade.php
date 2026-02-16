@extends('layouts.app')

@section('title', 'My Leave History - Nish Auto Limited')
@section('page-title', 'My Leave History')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-clock text-blue-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $summary['pending'] }}</h3>
            <p class="text-sm text-gray-600">Pending</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $summary['approved'] }}</h3>
            <p class="text-sm text-gray-600">Approved</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $summary['rejected'] }}</h3>
            <p class="text-sm text-gray-600">Rejected</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-calendar-alt text-purple-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $summary['total_days'] }}</h3>
            <p class="text-sm text-gray-600">Total Days</p>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('employee.leave.history') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                
                <select name="leave_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all" {{ request('leave_type') == 'all' ? 'selected' : '' }}>All Leave Types</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ request('leave_type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                
                <input type="month" name="month_year" value="{{ request('month_year') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('employee.leave.history') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                    <i class="fas fa-refresh mr-2"></i>Reset
                </a>
                <button type="button" onclick="exportLeaveHistory()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 font-medium">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
            </div>
        </form>
    </div>

    <!-- Leave History Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($leaveRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Details</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stand-In</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($leaveRequests as $request)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="{{ $request->leaveType->getIconClass() }} text-lg mr-3"></i>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $request->leaveType->name }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($request->reason, 30) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Applied: {{ $request->created_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $request->total_days }} day{{ $request->total_days != 1 ? 's' : '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($request->status === 'approved' && $request->standInEmployee)
                                        <div class="flex items-center space-x-2">
                                            <div class="w-7 h-7 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <span class="text-indigo-600 text-xs font-semibold">
                                                    {{ strtoupper(substr($request->standInEmployee->first_name, 0, 1) . substr($request->standInEmployee->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $request->standInEmployee->first_name }} {{ $request->standInEmployee->last_name }}</div>
                                                <div class="text-xs text-gray-500">{{ $request->standInEmployee->designation ?? '' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 {{ $request->getStatusBadgeClass() }} text-xs rounded-full font-medium">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('employee.leave.show', $request->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        View
                                    </a>
                                    
                                    @if($request->status == 'pending')
                                        <a href="{{ route('employee.leave.edit', $request->id) }}" class="text-green-600 hover:text-green-900 mr-3">
                                            Edit
                                        </a>
                                        <form action="{{ route('employee.leave.cancel', $request->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this leave request?')">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($request->status == 'cancelled')
                                        <form action="{{ route('employee.leave.retrieve', $request->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to retrieve this leave request?')">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="text-green-600 hover:text-green-900">
                                                Retrieve
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $leaveRequests->firstItem() }}</span> to 
                        <span class="font-medium">{{ $leaveRequests->lastItem() }}</span> of 
                        <span class="font-medium">{{ $leaveRequests->total() }}</span> results
                    </div>
                    <div class="flex space-x-2">
                        {{ $leaveRequests->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No leave requests found</h3>
                <p class="text-gray-500 mb-6">You haven't submitted any leave requests yet.</p>
                <a href="{{ route('employee.leave.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                    <i class="fas fa-plus mr-2"></i>Apply for Leave
                </a>
            </div>
        @endif
    </div>
</div>

@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            alert('{{ session('success') }}');
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            alert('{{ session('error') }}');
        });
    </script>
@endif

<script>
function exportLeaveHistory() {
    // Get current filter values
    const status = document.querySelector('select[name="status"]').value;
    const leaveType = document.querySelector('select[name="leave_type"]').value;
    const monthYear = document.querySelector('input[name="month_year"]').value;
    
    // Build query string
    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (leaveType) params.append('leave_type', leaveType);
    if (monthYear) params.append('month_year', monthYear);
    
    // Redirect to export route
    window.location.href = '{{ route("employee.leave.export") }}?' + params.toString();
}
</script>
@endsection