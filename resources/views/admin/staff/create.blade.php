<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Send Staff Invitation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.staff.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                   class="w-full rounded-lg border-gray-300 focus:border-[#1E3C2C] focus:ring-[#1E3C2C]" 
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" 
                                   class="w-full rounded-lg border-gray-300 focus:border-[#1E3C2C] focus:ring-[#1E3C2C]" 
                                   required>
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="user_type" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select id="user_type" name="user_type" 
                                    class="w-full rounded-lg border-gray-300 focus:border-[#1E3C2C] focus:ring-[#1E3C2C]" 
                                    required>
                                <option value="">Select a role...</option>
                                <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="delivery" {{ old('user_type') == 'delivery' ? 'selected' : '' }}>Delivery Staff</option>
                                <option value="pickup" {{ old('user_type') == 'pickup' ? 'selected' : '' }}>Pickup Staff</option>
                            </select>
                            @error('user_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg mb-4">
                            <p class="text-sm text-blue-800">
                                <strong>📧 Email Invitation:</strong><br>
                                The staff member will receive an email with registration instructions.
                                They will need to complete their registration before approval.
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="bg-[#1E3C2C] text-white px-6 py-2 rounded-lg hover:bg-[#143023] transition">
                                Send Invitation
                            </button>
                            <a href="{{ route('admin.staff.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>