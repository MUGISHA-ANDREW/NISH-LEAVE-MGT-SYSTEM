@extends('layouts.auth')

@section('title', 'Reset Password')
@section('auth-title', 'Reset Password')
@section('auth-subtitle', 'Enter your new password')

@section('auth-content')
<form action="{{ route('password.update.reset') }}" method="POST" class="space-y-6">
    @csrf
    
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">
    
    <div>
        <label for="email_display" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
            </div>
            <input type="email" id="email_display" value="{{ $email }}" readonly
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed"
                   placeholder="Enter your email">
        </div>
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input type="password" id="password" name="password" required 
                   autocomplete="new-password"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none transition duration-200"
                   placeholder="Enter new password">
        </div>
        <p class="mt-1 text-xs text-gray-500">
            Minimum 8 characters required
        </p>
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input type="password" id="password_confirmation" name="password_confirmation" required 
                   autocomplete="new-password"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none transition duration-200"
                   placeholder="Confirm new password">
        </div>
    </div>

    <button type="submit" 
            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium shadow-sm">
        Reset Password
    </button>
    
    <div class="text-center">
        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-500 transition duration-200">
            <i class="fas fa-arrow-left mr-1"></i> Back to Login
        </a>
    </div>
</form>
@endsection
