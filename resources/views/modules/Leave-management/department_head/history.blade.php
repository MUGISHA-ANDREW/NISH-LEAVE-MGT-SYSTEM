@extends('layouts.app')

@section('title', 'Leave History - Department Head - Nish Auto Limited')
@section('page-title', 'Department Leave History')

@php
    $isAdmin = false;
@endphp

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>All Employees</option>
                    <option>John Doe</option>
                    <option>Sarah Smith</option>
                    <option>Mike Johnson</option>
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
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Approved Leave -->
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-blue-600 text-sm font-semibold">JD</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">John Doe</div>
                                    <div class="text-sm text-gray-500">Assembly Technician</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-umbrella-beach text-blue-500 text-lg mr-3"></i>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Annual Leave</div>
                                    <div class="text-sm text-gray-500">Family vacation</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Dec 20 - Dec 31, 2024</div>
                            <div class="text-sm text-gray-500">12 days</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">Approved</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900">View Details</button>
                        </td>
                    </tr>
                    
                  
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection