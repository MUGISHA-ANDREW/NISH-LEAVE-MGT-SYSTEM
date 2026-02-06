@extends('layouts.app')

@section('title', 'Create User - Nish Auto Limited')
@section('page-title', 'Create New User')

@php
    $isAdmin = true;
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Create New User</h2>
                <p class="text-gray-600 mt-1">Add a new employee to the system</p>
            </div>
            
            <div class="flex space-x-3">
                <a href="{{ route('users.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-200 font-medium flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Users</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Personal Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center space-x-2">
                        <i class="fas fa-user-circle text-blue-600"></i>
                        <span>Personal Information</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" placeholder="Enter first name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Last Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" placeholder="Enter last name" value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" placeholder="Enter email address" value="{{ old('email') }}" required>
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" placeholder="Enter phone number" value="{{ old('phone') }}">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Date of Birth -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Date of Birth
                            </label>
                            <input type="date" name="date_of_birth" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" value="{{ old('date_of_birth') }}">
                            @error('date_of_birth')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Gender -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Gender
                            </label>
                            <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Address
                            </label>
                            <textarea name="address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" placeholder="Enter full address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Contact -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Emergency Contact
                            </label>
                            <input type="text" name="emergency_contact" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" placeholder="Enter emergency contact name and number" value="{{ old('emergency_contact') }}">
                            @error('emergency_contact')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Employment Information Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center space-x-2">
                        <i class="fas fa-briefcase text-green-600"></i>
                        <span>Employment Information</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Employee ID -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Employee ID
                            </label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" value="Auto-generated (NISH-XXX)" disabled>
                            <p class="text-xs text-gray-500 mt-1">Employee ID will be automatically generated</p>
                        </div>
                        
                        <!-- Department -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <select name="department_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Position/Designation -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Position/Designation <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="designation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" placeholder="e.g., Accountant, Sales Manager" value="{{ old('designation') }}" required>
                            @error('designation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                User Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Join Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Join Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="join_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" value="{{ old('join_date', date('Y-m-d')) }}" required>
                            @error('join_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Employment Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Employment Type <span class="text-red-500">*</span>
                            </label>
                            <select name="employment_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" required>
                                <option value="full_time" {{ old('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                                <option value="intern" {{ old('employment_type') == 'intern' ? 'selected' : '' }}>Intern</option>
                            </select>
                            @error('employment_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supervisor -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Supervisor
                            </label>
                            <select name="supervisor_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                                <option value="">Select Supervisor (Optional)</option>
                                @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                        {{ $supervisor->first_name }} {{ $supervisor->last_name }} ({{ $supervisor->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('supervisor_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Account Settings Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center space-x-2">
                        <i class="fas fa-lock text-purple-600"></i>
                        <span>Account Settings</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" placeholder="Enter password" required>
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" placeholder="Confirm password" required>
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Account Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Actions & Additional Info -->
            <div class="space-y-6">
                <!-- Profile Picture Card -->
                {{-- <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Picture</h3>
                    
                    <div class="flex flex-col items-center space-y-4">
                        <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center border-4 border-white shadow-lg profile-picture">
                            <i class="fas fa-user text-gray-400 text-4xl"></i>
                        </div>
                        
                        <div class="text-center">
                            <input type="file" name="profile_picture" id="profile_picture_input" class="hidden" accept="image/jpeg,image/png,image/gif">
                            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium flex items-center space-x-2 mx-auto profile-picture-upload">
                                <i class="fas fa-upload"></i>
                                <span>Upload Photo</span>
                            </button>
                            <p class="text-xs text-gray-500 mt-2">JPG, PNG or GIF. Max 2MB</p>
                            @error('profile_picture')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div> --}}

                <!-- Actions Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-save"></i>
                            <span>Create User</span>
                        </button>
                        
                        <a href="{{ route('users.index') }}" class="w-full bg-gray-200 text-gray-800 px-4 py-3 rounded-lg hover:bg-gray-300 transition duration-200 font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-times"></i>
                            <span>Cancel</span>
                        </a>
                    </div>
                </div>

                <!-- Leave Allocation Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center space-x-2">
                        <i class="fas fa-calendar-alt text-orange-600"></i>
                        <span>Leave Allocation</span>
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Annual Leave -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Annual Leave Days
                            </label>
                            <input type="number" value="25" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" disabled>
                        </div>
                        
                        <!-- Sick Leave -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sick Leave Days
                            </label>
                            <input type="number" value="14" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" disabled>
                        </div>
                        
                        <!-- Emergency Leave -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Emergency Leave Days
                            </label>
                            <input type="number" value="7" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .form-section {
        transition: all 0.3s ease;
    }
    
    .form-input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .profile-picture-upload {
        transition: all 0.3s ease;
    }
    
    .profile-picture-upload:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profilePictureInput = document.getElementById('profile_picture_input');
        const uploadButton = document.querySelector('.profile-picture-upload');
        const profilePicture = document.querySelector('.profile-picture');
        
        // Handle upload button click
        if (uploadButton && profilePictureInput) {
            uploadButton.addEventListener('click', function() {
                profilePictureInput.click();
            });
        }

        // Handle file selection
        if (profilePictureInput) {
            profilePictureInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Check file size (2MB limit)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB');
                        this.value = '';
                        return;
                    }
                    
                    // Check file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPG, PNG, GIF)');
                        this.value = '';
                        return;
                    }
                    
                    // Preview image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profilePicture.innerHTML = `<img src="${e.target.result}" class="w-full h-full rounded-full object-cover" alt="Profile Preview">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Password confirmation validation
        const password = document.querySelector('input[name="password"]');
        const confirmPassword = document.querySelector('input[name="password_confirmation"]');
        
        if (password && confirmPassword) {
            confirmPassword.addEventListener('input', function() {
                if (password.value !== confirmPassword.value) {
                    this.classList.add('border-red-500');
                } else {
                    this.classList.remove('border-red-500');
                }
            });
        }

        // Add smooth transitions to form sections
        const formSections = document.querySelectorAll('.bg-white.rounded-xl');
        formSections.forEach(section => {
            section.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.1)';
            });
            
            section.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 1px 3px 0 rgba(0, 0, 0, 0.1)';
            });
        });

        // Real-time validation for required fields
        const requiredFields = document.querySelectorAll('input[required], select[required]');
        requiredFields.forEach(field => {
            field.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.classList.add('border-red-500');
                } else {
                    this.classList.remove('border-red-500');
                }
            });
        });
    });
</script>
@endpush