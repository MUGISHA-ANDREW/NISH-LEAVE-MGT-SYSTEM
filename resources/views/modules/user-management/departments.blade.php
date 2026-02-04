@extends('layouts.app')

@section('title', 'Departments - Nish Auto Limited')
@section('page-title', 'Department Management')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
    <!-- Main Content -->
    <div class="lg:col-span-3">
        <!-- Header with Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Department Management</h2>
                    <p class="text-gray-600 mt-1">Manage organizational departments and their heads</p>
                </div>
                <div class="flex space-x-3 mt-4 md:mt-0">
                    <button class="flex items-center space-x-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-download text-gray-600"></i>
                        <span class="text-sm font-medium text-gray-700">Export</span>
                    </button>
                    <button class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-plus"></i>
                        <span class="text-sm font-medium">Add Department</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Departments Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            @forelse($departments as $department)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $department->name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $department->description ?? 'No description' }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-blue-600"></i>
                    </div>
                </div>
                
                <div class="mb-4">
                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">
                        {{ $department->users->count() }} Employees
                    </span>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Department Head</p>
                    @if($department->manager)
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-gray-600">
                                    {{ substr($department->manager->first_name, 0, 1) }}{{ substr($department->manager->last_name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ $department->manager->first_name }} {{ $department->manager->last_name }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $department->manager->email }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">No head assigned</p>
                    @endif
                </div>

                <div class="mt-4 flex space-x-2">
                    <button class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    <button class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-users mr-1"></i> View Team
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <i class="fas fa-building text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">No Departments Found</h3>
                <p class="text-gray-600 mb-4">Get started by creating your first department</p>
                <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i> Add Department
                </button>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <!-- Quick Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Overview</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Departments</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $departments->count() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Employees</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $departments->sum(function($d) { return $d->users->count(); }) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">With Heads</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $departments->where('manager_id', '!=', null)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <button class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 transition duration-200">
                    <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                    <span class="text-sm text-gray-700">Add Department</span>
                </button>
                <button class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 transition duration-200">
                    <i class="fas fa-user-plus text-green-600 mr-2"></i>
                    <span class="text-sm text-gray-700">Assign Department Head</span>
                </button>
                <button class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 transition duration-200">
                    <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                    <span class="text-sm text-gray-700">View Reports</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
