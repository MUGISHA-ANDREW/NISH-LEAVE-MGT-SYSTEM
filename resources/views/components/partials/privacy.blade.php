@extends('layouts.app')

@section('title', 'Privacy Policy - Nish Auto Limited')
@section('page-title', 'Privacy Policy')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-10">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-shield-alt text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">Privacy Policy</h1>
                    <p class="text-blue-100 mt-2">Last updated: {{ date('F d, Y') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-8">
            <div class="space-y-8">
                <!-- Introduction -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                        Introduction
                    </h2>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                        <p class="text-gray-700">
                            Welcome to Nish Auto Limited's Leave Management System. We are committed to protecting your privacy 
                            and ensuring that your personal information is handled in a safe and responsible manner.
                        </p>
                    </div>
                </section>

                <!-- Information Collection -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-database text-blue-500 mr-3"></i>
                        Information We Collect
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white border border-gray-200 rounded-lg p-5">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-user text-green-500 mr-2"></i>
                                Personal Information
                            </h3>
                            <ul class="space-y-2">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-gray-600">Name and contact details</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-gray-600">Employee identification</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-gray-600">Department and position</span>
                                </li>
                            </ul>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-5">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
                                Leave Information
                            </h3>
                            <ul class="space-y-2">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-gray-600">Leave history and requests</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-gray-600">Approval records</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-gray-600">System usage data</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Data Usage -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-cogs text-blue-500 mr-3"></i>
                        How We Use Your Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <i class="fas fa-calendar-check text-blue-600 text-2xl mb-3"></i>
                            <h4 class="font-semibold text-gray-700 mb-2">Leave Management</h4>
                            <p class="text-sm text-gray-600">Process and manage leave requests efficiently</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <i class="fas fa-chart-line text-green-600 text-2xl mb-3"></i>
                            <h4 class="font-semibold text-gray-700 mb-2">Reporting</h4>
                            <p class="text-sm text-gray-600">Generate management reports and analytics</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <i class="fas fa-shield-alt text-purple-600 text-2xl mb-3"></i>
                            <h4 class="font-semibold text-gray-700 mb-2">Compliance</h4>
                            <p class="text-sm text-gray-600">Ensure compliance with company policies</p>
                        </div>
                    </div>
                </section>

                <!-- Data Security -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-lock text-blue-500 mr-3"></i>
                        Data Security
                    </h2>
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-5 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-green-600 text-2xl mr-4 mt-1"></i>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Advanced Security Measures</h3>
                                <p class="text-gray-700">
                                    We implement enterprise-grade security measures including encryption, access controls, 
                                    regular security audits, and secure data storage to protect your personal information 
                                    against unauthorized access or disclosure.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Your Rights -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-balance-scale text-blue-500 mr-3"></i>
                        Your Rights
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="border border-gray-200 rounded-lg p-5">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-eye text-blue-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Right to Access</h3>
                            </div>
                            <p class="text-gray-600">Access your personal information stored in our systems</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-5">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-edit text-green-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Right to Correction</h3>
                            </div>
                            <p class="text-gray-600">Request corrections to inaccurate or incomplete data</p>
                        </div>
                    </div>
                </section>

                <!-- Contact Information -->
                <section class="bg-gradient-to-r from-blue-50 to-gray-50 rounded-xl p-6 border border-blue-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-headset text-blue-600 mr-3"></i>
                        Contact Our Privacy Team
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <h4 class="font-semibold text-gray-700 mb-1">Email</h4>
                            <a href="mailto:privacy@nishauto.com" class="text-blue-600 hover:text-blue-800">
                                privacy@nishauto.com
                            </a>
                        </div>
                        <div class="text-center p-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-phone text-green-600"></i>
                            </div>
                            <h4 class="font-semibold text-gray-700 mb-1">Phone</h4>
                            <a href="tel:+94112345678" class="text-gray-700">+94 11 234 5678</a>
                        </div>
                        <div class="text-center p-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-map-marker-alt text-purple-600"></i>
                            </div>
                            <h4 class="font-semibold text-gray-700 mb-1">Address</h4>
                            <p class="text-gray-600 text-sm">123 Auto Lane, Colombo 05</p>
                        </div>
                    </div>
                </section>

                <!-- Update Notice -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This privacy policy may be updated periodically. We recommend checking this page 
                                regularly for any changes. Significant updates will be communicated via email.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection