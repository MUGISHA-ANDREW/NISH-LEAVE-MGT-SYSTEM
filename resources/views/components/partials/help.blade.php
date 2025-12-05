@extends('layouts.app')

@section('title', 'Help Center - Nish Auto Limited')
@section('page-title', 'Help Center')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg mb-8 overflow-hidden">
        <div class="px-8 py-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-6">
                <i class="fas fa-headset text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-4">How can we help you?</h1>
            <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto">
                Find answers to common questions, learn how to use our system, and get support when you need it.
            </p>
            
            <!-- Search -->
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="help-search" 
                           placeholder="Search for help articles, guides, or FAQs..."
                           class="w-full pl-10 pr-4 py-4 rounded-lg border-0 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-500">
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Quick Links -->
        <div class="lg:col-span-2">
            <!-- Getting Started -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-play text-green-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Getting Started</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="#" class="group block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition duration-200">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center mr-3 group-hover:bg-blue-200 transition duration-200">
                                <i class="fas fa-user-plus text-blue-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-800 group-hover:text-blue-600">Account Setup</h3>
                        </div>
                        <p class="text-sm text-gray-600">Learn how to set up and configure your account</p>
                    </a>
                    
                    <a href="#" class="group block p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:shadow-md transition duration-200">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center mr-3 group-hover:bg-green-200 transition duration-200">
                                <i class="fas fa-calendar-plus text-green-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-800 group-hover:text-green-600">First Leave Request</h3>
                        </div>
                        <p class="text-sm text-gray-600">Step-by-step guide to submitting your first leave request</p>
                    </a>
                </div>
            </div>

            <!-- FAQs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-question-circle text-purple-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Frequently Asked Questions</h2>
                </div>
                
                <div class="space-y-4" id="faq-accordion">
                    <!-- FAQ 1 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button class="faq-question w-full px-5 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition duration-200">
                            <span class="font-semibold text-gray-800">How do I apply for annual leave?</span>
                            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
                        </button>
                        <div class="faq-answer px-5 py-4 border-t border-gray-200 hidden">
                            <p class="text-gray-700 mb-3">To apply for annual leave:</p>
                            <ol class="list-decimal pl-5 space-y-2 text-gray-600">
                                <li>Navigate to "Leave Management" in the sidebar</li>
                                <li>Click "Apply for Leave" button</li>
                                <li>Select "Annual Leave" from the leave type dropdown</li>
                                <li>Choose your start and end dates</li>
                                <li>Add any required comments</li>
                                <li>Click "Submit Request"</li>
                            </ol>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button class="faq-question w-full px-5 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition duration-200">
                            <span class="font-semibold text-gray-800">How long does approval take?</span>
                            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
                        </button>
                        <div class="faq-answer px-5 py-4 border-t border-gray-200 hidden">
                            <p class="text-gray-700 mb-3">Approval times vary:</p>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-clock text-blue-500 mr-2"></i>
                                    <span>Department Head: Usually within 24-48 hours</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-clock text-green-500 mr-2"></i>
                                    <span>HR Approval: 1-3 business days</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                    <span>Urgent requests: Email your department head directly</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button class="faq-question w-full px-5 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition duration-200">
                            <span class="font-semibold text-gray-800">Can I cancel a submitted leave request?</span>
                            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
                        </button>
                        <div class="faq-answer px-5 py-4 border-t border-gray-200 hidden">
                            <p class="text-gray-700 mb-3">Yes, you can cancel leave requests under certain conditions:</p>
                            <div class="space-y-3">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <p class="text-sm font-medium text-blue-800 mb-1">Pending Approval</p>
                                    <p class="text-sm text-blue-700">Can be cancelled anytime from your dashboard</p>
                                </div>
                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <p class="text-sm font-medium text-yellow-800 mb-1">Already Approved</p>
                                    <p class="text-sm text-yellow-700">Contact HR department for cancellation</p>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <p class="text-sm font-medium text-green-800 mb-1">Immediate Need</p>
                                    <p class="text-sm text-green-700">Call HR hotline: Ext. 1234 for urgent cancellations</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Video Guides -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-video text-red-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Video Tutorials</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-100 h-40 flex items-center justify-center">
                            <i class="fas fa-play-circle text-gray-400 text-4xl"></i>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Leave Application Process</h3>
                            <p class="text-sm text-gray-600 mb-3">Complete guide to submitting leave requests</p>
                            <span class="text-xs text-gray-500">Duration: 5:23</span>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-100 h-40 flex items-center justify-center">
                            <i class="fas fa-play-circle text-gray-400 text-4xl"></i>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Calendar & Planning</h3>
                            <p class="text-sm text-gray-600 mb-3">How to use the leave calendar effectively</p>
                            <span class="text-xs text-gray-500">Duration: 3:45</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Support Options -->
        <div class="space-y-6">
            <!-- Quick Support -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Support</h3>
                <div class="space-y-4">
                    <a href="mailto:leave-support@nishauto.com" class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition duration-200">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-envelope text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Email Support</p>
                            <p class="text-sm text-gray-600">Response within 24 hours</p>
                        </div>
                    </a>
                    
                    <a href="tel:+94112345678" class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-green-300 hover:shadow-sm transition duration-200">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-phone text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Phone Support</p>
                            <p class="text-sm text-gray-600">Mon-Fri, 8AM-5PM</p>
                        </div>
                    </a>
                    
                    <div class="p-3 border border-yellow-200 bg-yellow-50 rounded-lg">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Urgent Issues</p>
                                <p class="text-sm text-gray-600">IT Hotline: Ext. 999</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">For system outages or critical access issues</p>
                    </div>
                </div>
            </div>

            <!-- Download Guides -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Download Guides</h3>
                <div class="space-y-3">
                    <a href="#" class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-500 text-xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">User Manual</p>
                                <p class="text-sm text-gray-600">Complete system guide</p>
                            </div>
                        </div>
                        <i class="fas fa-download text-gray-400"></i>
                    </a>
                    
                    <a href="#" class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-file-word text-blue-500 text-xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">Quick Start Guide</p>
                                <p class="text-sm text-gray-600">Basic operations</p>
                            </div>
                        </div>
                        <i class="fas fa-download text-gray-400"></i>
                    </a>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">System Status</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-gray-700">Leave Management</span>
                        </div>
                        <span class="text-sm text-green-600 font-medium">Operational</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-gray-700">Calendar System</span>
                        </div>
                        <span class="text-sm text-green-600 font-medium">Operational</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                            <span class="text-gray-700">Mobile App</span>
                        </div>
                        <span class="text-sm text-yellow-600 font-medium">Maintenance</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Last updated: {{ now()->format('g:i A') }}
                        <br>
                        <a href="#" class="text-blue-600 hover:text-blue-800">View status history →</a>
                    </p>
                </div>
            </div>

            <!-- Contact Card -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                <h3 class="text-xl font-bold mb-3">Need Immediate Help?</h3>
                <p class="text-blue-100 mb-4">Our support team is ready to assist you</p>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-phone mr-3"></i>
                        <span>+94 11 234 5678</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope mr-3"></i>
                        <span>support@nishauto.com</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-3"></i>
                        <span>24/7 Emergency Support</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // FAQ Accordion
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        faqQuestions.forEach(question => {
            question.addEventListener('click', function() {
                const answer = this.nextElementSibling;
                const icon = this.querySelector('i');
                
                // Toggle current FAQ
                answer.classList.toggle('hidden');
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
                
                // Close other FAQs
                faqQuestions.forEach(otherQuestion => {
                    if (otherQuestion !== this) {
                        const otherAnswer = otherQuestion.nextElementSibling;
                        const otherIcon = otherQuestion.querySelector('i');
                        otherAnswer.classList.add('hidden');
                        otherIcon.classList.remove('fa-chevron-up');
                        otherIcon.classList.add('fa-chevron-down');
                    }
                });
            });
        });

        // Search functionality
        const searchInput = document.getElementById('help-search');
        const faqItems = document.querySelectorAll('.faq-question');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            faqItems.forEach(item => {
                const question = item.querySelector('span').textContent.toLowerCase();
                const parent = item.parentElement;
                
                if (question.includes(searchTerm)) {
                    parent.style.display = 'block';
                } else {
                    parent.style.display = 'none';
                }
            });
        });
    });
</script>
@endpush

<style>
    .faq-answer {
        transition: all 0.3s ease;
    }
    
    .faq-question i {
        transition: transform 0.3s ease;
    }
</style>
@endsection