<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-900">Application Received</h2>

        <p class="mt-3 text-sm text-gray-600">
            Thank you for your interest in becoming a Vuka Shop vendor.
        </p>

        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-left">
            <p class="text-sm text-yellow-800">
                <strong>⏳ What happens next?</strong><br>
                Our team will review your application. If approved, you will receive
                an email with a private link to create your vendor account and access
                your dashboard.
            </p>
        </div>

        <p class="mt-6 text-xs text-gray-500">
            This typically takes 1–3 business days.
        </p>

        <div class="mt-8">
            <a href="{{ route('home') }}"
                class="inline-flex items-center px-4 py-2 bg-[#1E3C2C] text-white text-sm font-semibold rounded-md hover:bg-[#143023] transition">
                Return to Home
            </a>
        </div>
    </div>
</x-guest-layout>