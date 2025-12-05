@extends('layouts.app')

@section('title', 'Terms of Service - Nish Auto Limited')
@section('page-title', 'Terms of Service')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-8 py-10">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-file-contract text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">Terms of Service</h1>
                    <p class="text-purple-100 mt-2">Effective from: {{ date('F d, Y') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-8">
            <div class="space-y-8">
                <!-- Acceptance -->
                <section>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-handshake text-purple-600 text-2xl mr-3"></i>
                            <h2 class="text-2xl font-bold text-gray-800">Acceptance of Terms</h2>
                        </div>
                        <p class="text-gray-700">
                            By accessing and using Nish Auto Limited's Leave Management System, you agree to be bound by 
                            these Terms of Service and all applicable laws and regulations.
                        </p>
                    </div>
                </section>

                <!-- User Responsibilities -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-user-check text-blue-500 mr-3"></i>
                        User Responsibilities
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition duration-200">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-user-secret text-red-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Account Security</h3>
                            </div>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Keep login credentials confidential</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Notify IT of unauthorized access</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Use strong, unique passwords</span>
                                </li>
                            </ul>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition duration-200">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-balance-scale text-blue-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Proper Usage</h3>
                            </div>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Submit accurate leave information</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Follow approval workflows</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Respect company policies</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- System Usage -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-desktop text-green-500 mr-3"></i>
                        System Usage Guidelines
                    </h2>
                    
                    <div class="overflow-hidden border border-gray-200 rounded-lg mb-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Activity
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Consequence
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <i class="fas fa-user-plus text-blue-500 mr-2"></i>
                                        Multiple accounts
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Prohibited
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Account suspension
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <i class="fas fa-share-alt text-green-500 mr-2"></i>
                                        Data sharing
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Restricted
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Disciplinary action
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <i class="fas fa-code text-purple-500 mr-2"></i>
                                        System manipulation
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Prohibited
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Immediate termination
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Intellectual Property -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-copyright text-orange-500 mr-3"></i>
                        Intellectual Property
                    </h2>
                    
                    <div class="bg-gradient-to-r from-orange-50 to-yellow-50 border border-orange-200 rounded-lg p-6 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-gem text-orange-500 text-2xl mr-4 mt-1"></i>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800 mb-3">Ownership Rights</h3>
                                <p class="text-gray-700 mb-4">
                                    All content, features, and functionality of the Leave Management System, including but not limited to 
                                    text, graphics, logos, and software, are the exclusive property of Nish Auto Limited and are protected 
                                    by international copyright, trademark, and other intellectual property laws.
                                </p>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-exclamation-circle text-orange-500 mr-2"></i>
                                    <span>Unauthorized reproduction or distribution is strictly prohibited</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Termination -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-ban text-red-500 mr-3"></i>
                        Termination of Access
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="text-center p-4 border border-red-200 rounded-lg">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-user-slash text-red-600"></i>
                            </div>
                            <h4 class="font-semibold text-gray-700 mb-2">Policy Violation</h4>
                            <p class="text-sm text-gray-600">Immediate access revocation for serious violations</p>
                        </div>
                        <div class="text-center p-4 border border-yellow-200 rounded-lg">
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-clock text-yellow-600"></i>
                            </div>
                            <h4 class="font-semibold text-gray-700 mb-2">Inactivity</h4>
                            <p class="text-sm text-gray-600">Account deactivation after 90 days of inactivity</p>
                        </div>
                        <div class="text-center p-4 border border-blue-200 rounded-lg">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-building text-blue-600"></i>
                            </div>
                            <h4 class="font-semibold text-gray-700 mb-2">Employment End</h4>
                            <p class="text-sm text-gray-600">Automatic termination upon employment conclusion</p>
                        </div>
                    </div>
                </section>

                <!-- Disclaimer -->
                <section class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle text-gray-600 mr-3"></i>
                        Disclaimer of Warranties
                    </h2>
                    <p class="text-gray-700 mb-4">
                        The Leave Management System is provided "as is" and "as available" without any warranties of any kind, 
                        either express or implied. Nish Auto Limited does not warrant that the system will be uninterrupted, 
                        timely, secure, or error-free.
                    </p>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span>For technical issues, contact the IT department at it-support@nishauto.com</span>
                    </div>
                </section>

                <!-- Updates -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-sync-alt text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Terms Updates</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>
                                    We reserve the right to update these terms at any time. Continued use of the system 
                                    after changes constitutes acceptance of the new terms.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection