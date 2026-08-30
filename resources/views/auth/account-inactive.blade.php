<x-guest-layout>
    <div class="text-center py-8">
        <div class="text-6xl mb-4">🔒</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Account Inactive</h2>
        <p class="text-gray-600 mb-4">Your account is currently inactive or pending approval.</p>
        
        <div class="bg-red-50 p-4 rounded-lg mb-6">
            <p class="text-sm text-red-800">
                <strong>⛔ Access Denied:</strong><br>
                Please contact the administrator for assistance.
            </p>
        </div>

        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
            Return to Login
        </a>
    </div>
</x-guest-layout>