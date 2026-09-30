<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vendor Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            @if (session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                    {{ session('info') }}
                </div>
            @endif

            {{-- ============================================ --}}
            {{-- PENDING APPLICATIONS --}}
            {{-- ============================================ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Vendor Applications</h3>
                            <p class="text-sm text-gray-500">Review and approve new vendor applications.</p>
                        </div>
                        @php
                            $pendingCount = $applications->where('status', 'pending')->count();
                        @endphp
                        @if ($pendingCount > 0)
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                                {{ $pendingCount }} pending
                            </span>
                        @endif
                    </div>

                    @if ($applications->isEmpty())
                        <p class="text-gray-500 text-sm py-4">No applications yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Applicant</th>
                                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Shop</th>
                                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Location</th>
                                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Applied</th>
                                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($applications as $application)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4">
                                                <div class="font-medium text-gray-900">{{ $application->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $application->email }}</div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-gray-900">
                                                {{ $application->shop_name }}
                                            </td>
                                            <td class="py-3 px-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $application->shop_address }}">
                                                {{ $application->shop_address }}
                                            </td>
                                            <td class="py-3 px-4">
                                                @if ($application->isPending())
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                                @elseif ($application->isApproved())
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                                @else
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-sm text-gray-600">
                                                {{ $application->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="py-3 px-4">
                                                <a href="{{ route('admin.vendors.show', $application) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                                    Review →
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- REGISTERED VENDORS --}}
            {{-- ============================================ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Registered Vendors</h3>
                        <p class="text-sm text-gray-500">Approved vendors who have completed registration.</p>
                    </div>

                    @if ($vendors->isEmpty())
                        <p class="text-gray-500 text-sm py-4">No registered vendors yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Shop</th>
                                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vendors as $vendor)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4">
                                                <div class="font-medium text-gray-900">{{ $vendor->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $vendor->email }}</div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-gray-900">
                                                {{ $vendor->shop_name ?? '—' }}
                                            </td>
                                            <td class="py-3 px-4">
                                                @if ($vendor->is_active && $vendor->approved_at)
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                                @elseif ($vendor->approved_at && !$vendor->is_active)
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                                @else
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="flex gap-2 flex-wrap">
                                                    @if (!$vendor->is_active || !$vendor->approved_at)
                                                        <form method="POST" action="{{ route('admin.vendors.user.activate', $vendor) }}">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-800 text-sm">Activate</button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('admin.vendors.user.deactivate', $vendor) }}">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Deactivate</button>
                                                        </form>
                                                    @endif

                                                    <form method="POST" action="{{ route('admin.vendors.user.destroy', $vendor) }}"
                                                        onsubmit="return confirm('Delete this vendor?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>