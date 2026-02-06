@extends('layouts.auth')

@section('title', 'Login')
@section('auth-title', 'Welcome Back')
@section('auth-subtitle', 'Sign in to your account')

@section('auth-content')
<form action="{{ route('login.submit') }}" method="POST" class="space-y-6">
    @csrf
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
            </div>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                   autocomplete="username"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none transition duration-200"
                   placeholder="Enter your email">
        </div>
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input type="password" id="password" name="password" required 
                   autocomplete="current-password"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none transition duration-200"
                   placeholder="Enter your password">
        </div>
    </div>

    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <input type="checkbox" id="remember" name="remember" 
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
        </div>
        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500 transition duration-200">Forgot password?</a>
    </div>

    <button type="submit" 
            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium shadow-sm">
        Sign In
    </button>
</form>
@endsection

