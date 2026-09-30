<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Create Your Vendor Account</h2>
        <p class="mt-1 text-sm text-gray-600">
            Welcome to Vuka Shop. Fill in the details below to set up your vendor account.
        </p>
    </div>

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm font-medium text-red-800 mb-1">Please fix the following:</p>
            <ul class="text-sm text-red-700 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('vendor.register') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text"
                name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email"
                name="email" :value="old('email')" required />
            <p class="mt-1 text-xs text-gray-500">
                Use the same email you applied with.
            </p>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password"
                name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Notice --}}
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                <strong>ℹ️ What happens next?</strong><br>
                After creating your account, our team will perform a final activation.
                You'll receive an email once your account is live and you can log in.
            </p>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('Create Account') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>