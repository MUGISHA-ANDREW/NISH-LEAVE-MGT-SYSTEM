@extends('layouts.app')

@section('title', 'System Settings - Nish Auto Limited')
@section('page-title', 'System Settings')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
    <!-- Main Content -->
    <div class="lg:col-span-3">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">System Settings</h2>
                    <p class="text-gray-600 mt-1">Configure leave types, departments, and system preferences</p>
                </div>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </div>

        <!-- Settings Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-4 px-6" aria-label="Tabs">
                    <button class="px-4 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                        Leave Types
                    </button>
                    <button class="px-4 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Departments
                    </button>
                    <button class="px-4 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        General Settings
                    </button>
                </nav>
            </div>

            <!-- Leave Types Settings -->
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Leave Types Configuration</h3>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i> Add Leave Type
                    </button>
                </div>

                <!-- Leave Types List -->
                <div class="space-y-4">
                    @forelse($leaveTypes as $type)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-800">{{ $type->name }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $type->description ?? 'No description' }}</p>
                                <div class="flex items-center space-x-4 mt-2">
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-calendar-day mr-1"></i>
                                        Max Days: <strong>{{ $type->max_days }}</strong>
                                    </span>
                                    @if($type->requires_documentation)
                                    <span class="text-xs text-orange-600">
                                        <i class="fas fa-file-alt mr-1"></i>
                                        Requires Documentation
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition duration-200">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition duration-200">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 border border-gray-200 rounded-lg">
                        <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Leave Types Configured</h3>
                        <p class="text-gray-600 mb-4">Add your first leave type to get started</p>
                        <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-plus mr-2"></i> Add Leave Type
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Department Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Department Configuration</h3>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i> Add Department
                    </button>
                </div>

                <!-- Departments List -->
                <div class="space-y-4">
                    @forelse($departments as $dept)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-800">{{ $dept->name }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $dept->description ?? 'No description' }}</p>
                                <div class="mt-2">
                                    @if($dept->manager)
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-user-tie mr-1"></i>
                                        Head: <strong>{{ $dept->manager->first_name }} {{ $dept->manager->last_name }}</strong>
                                    </span>
                                    @else
                                    <span class="text-xs text-orange-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        No department head assigned
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition duration-200">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition duration-200">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 border border-gray-200 rounded-lg">
                        <i class="fas fa-building text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Departments Configured</h3>
                        <p class="text-gray-600 mb-4">Add your first department to get started</p>
                        <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-plus mr-2"></i> Add Department
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <!-- Quick Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Configuration Status</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Leave Types</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $leaveTypes->count() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Departments</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $departments->count() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">With Heads</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $departments->where('manager_id', '!=', null)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">System Information</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Version:</span>
                    <span class="font-medium text-gray-800">1.0.0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Last Updated:</span>
                    <span class="font-medium text-gray-800">{{ now()->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
