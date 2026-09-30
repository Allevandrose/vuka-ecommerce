<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Review Vendor Application') }}
            </h2>
            <a href="{{ route('admin.vendors.index') }}"
                class="text-sm text-indigo-600 hover:text-indigo-800">
                ← Back to Vendors
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Status Banner --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $application->shop_name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Submitted {{ $application->created_at->format('M d, Y \a\t H:i') }}
                            </p>
                        </div>
                        <div>
                            @if ($application->isPending())
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Pending Review</span>
                            @elseif ($application->isApproved())
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Approved</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Rejected</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Applicant Details --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                        Applicant Details
                    </h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs text-gray-500 uppercase">Full Name</dt>
                            <dd class="text-sm text-gray-900 mt-1">{{ $application->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 uppercase">Email</dt>
                            <dd class="text-sm text-gray-900 mt-1">{{ $application->email }}</dd>
                        </div>
                        @if ($application->phone)
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Phone</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $application->phone }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Shop Details --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                        Shop Details
                    </h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-500 uppercase">Shop Name</dt>
                            <dd class="text-sm text-gray-900 mt-1">{{ $application->shop_name }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-500 uppercase">Address</dt>
                            <dd class="text-sm text-gray-900 mt-1">{{ $application->shop_address }}</dd>
                        </div>
                        @if ($application->shop_latitude && $application->shop_longitude)
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Latitude</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $application->shop_latitude }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Longitude</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $application->shop_longitude }}</dd>
                            </div>
                        @endif
                        @if ($application->product_categories)
                            <div class="sm:col-span-2">
                                <dt class="text-xs text-gray-500 uppercase">What they sell</dt>
                                <dd class="text-sm text-gray-900 mt-1 whitespace-pre-line">{{ $application->product_categories }}</dd>
                            </div>
                        @endif
                        @if ($application->message)
                            <div class="sm:col-span-2">
                                <dt class="text-xs text-gray-500 uppercase">Message from applicant</dt>
                                <dd class="text-sm text-gray-900 mt-1 whitespace-pre-line bg-gray-50 p-3 rounded">{{ $application->message }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Review Info (if already reviewed) --}}
            @if (!$application->isPending())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                            Review
                        </h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Reviewed At</dt>
                                <dd class="text-sm text-gray-900 mt-1">
                                    {{ $application->reviewed_at?->format('M d, Y \a\t H:i') ?? '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Reviewed By</dt>
                                <dd class="text-sm text-gray-900 mt-1">
                                    {{ $application->reviewer?->name ?? '—' }}
                                </dd>
                            </div>
                            @if ($application->admin_notes)
                                <div class="sm:col-span-2">
                                    <dt class="text-xs text-gray-500 uppercase">Admin Notes</dt>
                                    <dd class="text-sm text-gray-900 mt-1 whitespace-pre-line">{{ $application->admin_notes }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif

            {{-- Actions (only for pending) --}}
            @if ($application->isPending())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                            Decision
                        </h4>

                        <form method="POST" action="{{ route('admin.vendors.approve', $application) }}" class="mb-4">
                            @csrf
                            <div class="mb-4">
                                <label for="approve_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Notes (optional — for approval record)
                                </label>
                                <textarea id="approve_notes" name="admin_notes" rows="2"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. Verified location, established shop">{{ old('admin_notes') }}</textarea>
                            </div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                                onclick="return confirm('Approve this application and send the invite email?')">
                                ✓ Approve & Send Invite
                            </button>
                        </form>

                        <hr class="my-6 border-gray-200">

                        <form method="POST" action="{{ route('admin.vendors.reject', $application) }}">
                            @csrf
                            <div class="mb-4">
                                <label for="reject_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Reason for rejection
                                </label>
                                <textarea id="reject_notes" name="admin_notes" rows="2"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. Incomplete address, unable to verify business"></textarea>
                            </div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                                onclick="return confirm('Reject this application?')">
                                ✗ Reject Application
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>