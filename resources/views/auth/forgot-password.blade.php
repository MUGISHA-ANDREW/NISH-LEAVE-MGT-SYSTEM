@extends('layouts.auth')

@section('title', 'Forgot Password')
@section('auth-title', 'Forgot Password')
@section('auth-subtitle', 'Enter your email to receive a password reset link')

@section('auth-content')
<form action="{{ route('password.email') }}" method="POST" class="space-y-6">
    @csrf
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
            </div>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                   autocomplete="email"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none transition duration-200"
                   placeholder="Enter your email">
        </div>
        <p class="mt-2 text-sm text-gray-500">
            We'll send you a password reset link to this email address.
        </p>
    </div>

    <button type="submit" 
            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium shadow-sm">
        Send Reset Link
    </button>
    
    <div class="text-center">
        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-500 transition duration-200">
            <i class="fas fa-arrow-left mr-1"></i> Back to Login
        </a>
    </div>
</form>
@endsection
