<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        <p class="font-semibold text-gray-900">Staff Registration</p>
        <p class="mt-1">Complete your registration to join the Vuka Shop team.</p>
    </div>

    <form method="POST" action="{{ route('staff.register') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- User Type -->
        <div class="mt-4">
            <x-input-label for="user_type" :value="__('Role')" />
            <select id="user_type" name="user_type" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="">Select your role...</option>
                <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>Administrator</option>
                <option value="delivery" {{ old('user_type') == 'delivery' ? 'selected' : '' }}>Delivery Staff</option>
                <option value="pickup" {{ old('user_type') == 'pickup' ? 'selected' : '' }}>Pickup Staff</option>
            </select>
            <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <strong>⚠️ Important:</strong> Your account will be inactive until approved by an administrator.
                    You will receive an email when your account is activated.
                </p>
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>