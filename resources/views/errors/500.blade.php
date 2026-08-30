<x-guest-layout>
    <div class="min-h-[60vh] flex flex-col items-center justify-center">
        <div class="text-center">
            <!-- Error Code -->
            <h1 class="text-8xl font-bold text-gray-900 mb-4">500</h1>
            
            <!-- Icon -->
            <div class="text-9xl mb-6">⚠️</div>
            
            <!-- Title -->
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">Server Error</h2>
            
            <!-- Message - REMOVED $exception variable -->
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                Something went wrong on our end. Please try again later.
            </p>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="window.location.reload()" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Try Again
                </button>
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 border border-transparent rounded-lg font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Home
                </a>
            </div>
            
            <!-- Contact Support -->
            <p class="mt-8 text-sm text-gray-400">
                Need help? 
                <a href="mailto:support@vuka.com" class="text-indigo-600 hover:text-indigo-500">
                    Contact Support
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>