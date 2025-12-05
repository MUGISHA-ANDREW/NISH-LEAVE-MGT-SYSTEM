@extends('layouts.app')

@section('title', 'Edit Profile - Nish Auto Limited')
@section('page-title', 'Edit Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-800">Profile Information</h2>
            <p class="text-gray-600 text-sm mt-1">Update your account's profile information</p>
        </div>
        
        <div class="p-6">
            <form method="POST" action="{{ route('users.update-profile') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Profile Picture -->
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-6">
                            <div class="relative">
                                @if(auth()->user()->profile_picture)
                                    <img id="profile-preview" src="{{ asset('storage/' . auth()->user()->profile_picture) }}" 
                                         class="w-32 h-32 rounded-full object-cover border-4 border-white shadow">
                                @else
                                    <div id="profile-preview" class="w-32 h-32 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                        <span class="text-white text-2xl font-bold">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                                <label for="profile_picture" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition duration-200">
                                    <i class="fas fa-camera text-sm"></i>
                                </label>
                                <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/*">
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-800">{{ auth()->user()->name }}</h3>
                                <p class="text-gray-600">{{ auth()->user()->email }}</p>
                                <p class="text-sm text-gray-500 mt-2">Click the camera icon to change your profile picture</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Designation -->
                    <div>
                        <label for="designation" class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                        <input type="text" id="designation" name="designation" value="{{ old('designation', auth()->user()->designation) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('designation') border-red-500 @enderror">
                        @error('designation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Department -->
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select id="department_id" name="department_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('department_id') border-red-500 @enderror">
                            <option value="">Select Department</option>
                            @foreach($departments ?? [] as $department)
                                <option value="{{ $department->id }}" {{ auth()->user()->department_id == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', auth()->user()->date_of_birth) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <div class="flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="gender" value="male" 
                                       {{ auth()->user()->gender == 'male' ? 'checked' : '' }}
                                       class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700">Male</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="gender" value="female"
                                       {{ auth()->user()->gender == 'female' ? 'checked' : '' }}
                                       class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700">Female</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="gender" value="other"
                                       {{ auth()->user()->gender == 'other' ? 'checked' : '' }}
                                       class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700">Other</span>
                            </label>
                        </div>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea id="address" name="address" rows="3"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', auth()->user()->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Bio -->
                <div class="mt-6">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                    <textarea id="bio" name="bio" rows="4"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bio') border-red-500 @enderror">{{ old('bio', auth()->user()->bio) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">A brief description about yourself</p>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ url()->previous() }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile picture preview
        const profilePictureInput = document.getElementById('profile_picture');
        const profilePreview = document.getElementById('profile-preview');
        
        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (profilePreview.tagName === 'IMG') {
                        profilePreview.src = e.target.result;
                    } else {
                        // If it's a div, convert to img
                        const img = document.createElement('img');
                        img.id = 'profile-preview';
                        img.src = e.target.result;
                        img.className = 'w-32 h-32 rounded-full object-cover border-4 border-white shadow';
                        profilePreview.parentNode.replaceChild(img, profilePreview);
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush