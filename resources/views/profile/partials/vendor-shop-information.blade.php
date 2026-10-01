@if (auth()->user()->isVendor())
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Shop Information') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('These details were verified by our team during your vendor approval. To request a change, contact support.') }}
            </p>
        </header>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <x-input-label :value="__('Shop Name')" />
                <p class="mt-1 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                    {{ auth()->user()->shop_name ?? '—' }}
                </p>
            </div>

            <div class="sm:col-span-2">
                <x-input-label :value="__('Shop Address')" />
                <p class="mt-1 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                    {{ auth()->user()->shop_address ?? '—' }}
                </p>
            </div>

            @if (auth()->user()->shop_latitude && auth()->user()->shop_longitude)
                <div>
                    <x-input-label :value="__('Latitude')" />
                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                        {{ auth()->user()->shop_latitude }}
                    </p>
                </div>

                <div>
                    <x-input-label :value="__('Longitude')" />
                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                        {{ auth()->user()->shop_longitude }}
                    </p>
                </div>
            @endif

            <div>
                <x-input-label :value="__('Status')" />
                <div class="mt-1">
                    @if (auth()->user()->is_active && auth()->user()->approved_at)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                    @elseif (auth()->user()->approved_at && !auth()->user()->is_active)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Inactive
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                    @endif
                </div>
            </div>

            <div>
                <x-input-label :value="__('Member Since')" />
                <p class="mt-1 text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                    {{ auth()->user()->created_at?->format('M d, Y') ?? '—' }}
                </p>
            </div>

            @if (auth()->user()->vendor_notes)
                <div class="sm:col-span-2">
                    <x-input-label :value="__('Admin Notes')" />
                    <p class="mt-1 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-md px-3 py-2 whitespace-pre-line">
                        {{ auth()->user()->vendor_notes }}
                    </p>
                </div>
            @endif
        </div>
    </section>
@endif