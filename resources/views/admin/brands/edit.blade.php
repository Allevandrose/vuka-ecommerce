<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Brand') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $brand->name }}</p>
            </div>
            <a href="{{ route('admin.brands.index') }}"
                class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                <i class="fas fa-arrow-left text-xs"></i> Back to Brands
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-4 text-gray-500">
                        <span>
                            <i class="fas fa-hashtag text-gray-400"></i>
                            ID: <span class="font-mono">{{ $brand->id }}</span>
                        </span>
                        <span>
                            <i class="fas fa-link text-gray-400"></i>
                            <span class="font-mono">{{ $brand->slug }}</span>
                        </span>
                        <span>
                            <i class="fas fa-box text-gray-400"></i>
                            {{ $brand->products()->count() }} product(s)
                        </span>
                    </div>
                    <div class="text-gray-400">
                        Created {{ $brand->created_at->format('M d, Y') }}
                        · Updated {{ $brand->updated_at->diffForHumans() }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.brands.update', $brand) }}"
                  x-data="brandForm()"
                  @submit="submitting = true">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 space-y-5">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Basic Information</h3>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Brand Name <span class="text-red-500">*</span>
                            </label>
                            <input id="name" name="name" type="text" required
                                x-model="name"
                                value="{{ old('name', $brand->name) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">
                                Current slug: <span class="font-mono">{{ $brand->slug }}</span>
                                — regenerates automatically on rename.
                            </p>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <textarea id="description" name="description" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $brand->description) }}</textarea>
                        </div>

                        <div>
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-1">
                                Website
                            </label>
                            <input id="website" name="website" type="url" inputmode="url"
                                value="{{ old('website', $brand->website) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Logo</h3>

                        <div>
                            <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">
                                Logo URL
                            </label>
                            <input id="logo" name="logo" type="text"
                                x-model="logo"
                                value="{{ old('logo', $brand->logo) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-2">Preview:</p>
                            <div class="w-20 h-20 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center overflow-hidden">
                                <template x-if="logo">
                                    <img :src="logo" alt="Logo preview" class="w-full h-full object-contain p-2">
                                </template>
                                <template x-if="!logo">
                                    <span class="text-gray-400 font-semibold text-2xl"
                                        x-text="(name || '?').substring(0, 1).toUpperCase()"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Trust & Visibility</h3>

                        <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                            <input type="hidden" name="is_verified" value="0">
                            <input type="checkbox" name="is_verified" value="1"
                                {{ old('is_verified', $brand->is_verified) ? 'checked' : '' }}
                                class="mt-0.5 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-circle-check text-blue-500 text-sm"></i>
                                    <span class="text-sm font-medium text-gray-900">Verified Brand</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Verified brands show a blue check badge on product pages.
                                </p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $brand->is_active) ? 'checked' : '' }}
                                class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-eye text-indigo-500 text-sm"></i>
                                    <span class="text-sm font-medium text-gray-900">Active</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Only active brands appear in product filters.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 space-y-5">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">SEO</h3>

                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                                Meta Title
                            </label>
                            <input id="meta_title" name="meta_title" type="text" maxlength="255"
                                value="{{ old('meta_title', $brand->meta_title) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                                Meta Description
                            </label>
                            <textarea id="meta_description" name="meta_description" rows="2" maxlength="500"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('meta_description', $brand->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 mb-10 flex-wrap">
                    <div class="text-xs text-gray-500">
                        Changes take effect immediately.
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.brands.index') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                            Cancel
                        </a>
                        <button type="submit"
                            :disabled="submitting"
                            class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-6 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold disabled:opacity-60">
                            <span x-show="!submitting">Update Brand</span>
                            <span x-show="submitting" class="flex items-center gap-2">
                                <i class="fas fa-circle-notch fa-spin"></i> Saving…
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function brandForm() {
            return {
                name: @json(old('name', $brand->name)),
                logo: @json(old('logo', $brand->logo)),
                submitting: false,
            }
        }
    </script>
</x-app-layout>