<x-guest-layout>
    <div class="min-h-[60vh] flex flex-col items-center justify-center">
        <div class="text-center">
            <!-- Error Code -->
            <h1 class="text-8xl font-bold text-gray-900 mb-4">401</h1>
            
            <!-- Icon -->
            <div class="text-9xl mb-6">🔒</div>
            
            <!-- Title -->
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">Unauthorized</h2>
            
            <!-- Message -->
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                {{ $exception->getMessage() ?? 'You need to be logged in to access this page.' }}
            </p>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Login
                </a>
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 border border-transparent rounded-lg font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Register
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>