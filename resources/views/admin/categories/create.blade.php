<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Category') }}
            </h2>
            <a href="{{ route('admin.categories.index') }}"
                class="text-sm text-indigo-600 hover:text-indigo-800">
                ← Back to Categories
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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

            <form method="POST" action="{{ route('admin.categories.store') }}"
                  x-data="categoryForm()"
                  @submit="submitting = true">
                @csrf

                {{-- ============================================ --}}
                {{-- BASIC INFO --}}
                {{-- ============================================ --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 space-y-5">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Basic Information</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input id="name" name="name" type="text" required
                                    x-model="name"
                                    value="{{ old('name') }}"
                                    placeholder="e.g. Electronics"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">
                                    Slug auto-generates: <span class="font-mono" x-text="slug || '...'"></span>
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Parent Category
                                </label>
                                <select id="parent_id" name="parent_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">— None (top-level) —</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    Leave empty for a top-level category.
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Description
                                </label>
                                <textarea id="description" name="description" rows="3"
                                    placeholder="Short description shown on the category page"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================================ --}}
                {{-- VISUAL IDENTITY --}}
                {{-- ============================================ --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 space-y-5">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Visual Identity</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">
                                    Icon <span class="text-gray-400 text-xs">(FontAwesome class)</span>
                                </label>
                                <input id="icon" name="icon" type="text"
                                    x-model="icon"
                                    value="{{ old('icon') }}"
                                    placeholder="fa-microchip"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">
                                <p class="mt-1 text-xs text-gray-500">
                                    Preview: <i class="fas" :class="icon || 'fa-question'" x-text="''"></i>
                                    <span class="font-mono" x-text="icon || 'fa-question'"></span>
                                </p>
                            </div>

                            <div>
                                <label for="theme" class="block text-sm font-medium text-gray-700 mb-1">
                                    Theme Key
                                </label>
                                <input id="theme" name="theme" type="text"
                                    value="{{ old('theme', 'default') }}"
                                    placeholder="default"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">
                                <p class="mt-1 text-xs text-gray-500">
                                    Maps to a Blade layout: <span class="font-mono">categories.{theme}</span>
                                </p>
                            </div>

                            <div>
                                <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-1">
                                    Primary Color
                                </label>
                                <div class="flex gap-2">
                                    <input id="primary_color" name="primary_color" type="color"
                                        x-model="primaryColor"
                                        value="{{ old('primary_color', '#1E3C2C') }}"
                                        class="h-10 w-14 rounded-md border border-gray-300 cursor-pointer">
                                    <input type="text"
                                        x-model="primaryColor"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="accent_color" class="block text-sm font-medium text-gray-700 mb-1">
                                    Accent Color
                                </label>
                                <div class="flex gap-2">
                                    <input id="accent_color" name="accent_color" type="color"
                                        x-model="accentColor"
                                        value="{{ old('accent_color', '#E7A93B') }}"
                                        class="h-10 w-14 rounded-md border border-gray-300 cursor-pointer">
                                    <input type="text"
                                        x-model="accentColor"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- Gradient picker --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Hero Background Gradient <span class="text-gray-400 text-xs">(optional — overrides primary color)</span>
                            </label>

                            <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 mb-3">
                                <template x-for="preset in presets" :key="preset.name">
                                    <button type="button"
                                        @click="gradientCss = preset.css"
                                        :title="preset.name"
                                        :class="gradientCss === preset.css ? 'ring-2 ring-indigo-500 ring-offset-2' : 'ring-1 ring-gray-200'"
                                        class="h-12 rounded-lg shadow-sm transition"
                                        :style="`background: ${preset.css};`"></button>
                                </template>
                            </div>

                            <textarea id="gradient_css" name="gradient_css" rows="2"
                                x-model="gradientCss"
                                placeholder="Paste a CSS gradient, e.g. linear-gradient(135deg, #14231C 0%, #20503C 100%)"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs"></textarea>

                            <div class="mt-2 flex items-center justify-between">
                                <button type="button" @click="gradientCss = ''" class="text-xs text-red-600 hover:text-red-800">
                                    Clear gradient
                                </button>
                            </div>

                            {{-- Preview --}}
                            <div class="mt-3 h-24 rounded-lg border border-gray-200 flex items-center justify-center text-white text-lg font-semibold shadow-sm"
                                :style="`background: ${gradientCss || primaryColor};`">
                                <span x-text="name || 'Category Preview'"></span>
                            </div>
                        </div>

                        <div>
                            <label for="accent_gradient_css" class="block text-sm font-medium text-gray-700 mb-1">
                                Accent Gradient <span class="text-gray-400 text-xs">(optional — for buttons/CTAs)</span>
                            </label>
                            <textarea id="accent_gradient_css" name="accent_gradient_css" rows="2"
                                placeholder="Leave empty to use the accent color above"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs">{{ old('accent_gradient_css') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ============================================ --}}
                {{-- SEO --}}
                {{-- ============================================ --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 space-y-5">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">SEO</h3>

                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                                Meta Title
                            </label>
                            <input id="meta_title" name="meta_title" type="text" maxlength="255"
                                value="{{ old('meta_title') }}"
                                placeholder="Leave blank to auto-generate from name"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                                Meta Description
                            </label>
                            <textarea id="meta_description" name="meta_description" rows="2" maxlength="500"
                                placeholder="155-character description for search engines"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('meta_description') }}</textarea>
                        </div>

                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">
                                Meta Keywords
                            </label>
                            <input id="meta_keywords" name="meta_keywords" type="text"
                                value="{{ old('meta_keywords') }}"
                                placeholder="comma, separated, keywords"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- ============================================ --}}
                {{-- SETTINGS --}}
                {{-- ============================================ --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Settings</h3>

                        <div class="flex items-center gap-6 flex-wrap">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Active (visible on site)</span>
                            </label>

                            <div class="flex items-center gap-2">
                                <label for="sort_order" class="text-sm text-gray-700">Sort order:</label>
                                <input id="sort_order" name="sort_order" type="number" min="0" max="999999"
                                    value="{{ old('sort_order', 0) }}"
                                    class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 mb-10">
                    <a href="{{ route('admin.categories.index') }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                        Cancel
                    </a>
                    <button type="submit"
                        :disabled="submitting"
                        class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-6 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold disabled:opacity-60">
                        <span x-show="!submitting">Create Category</span>
                        <span x-show="submitting" class="flex items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin"></i> Saving…
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function categoryForm() {
            return {
                name: '{{ old('name', '') }}',
                icon: '{{ old('icon', '') }}',
                primaryColor: '{{ old('primary_color', '#1E3C2C') }}',
                accentColor: '{{ old('accent_color', '#E7A93B') }}',
                gradientCss: '{{ old('gradient_css', '') }}',
                submitting: false,
                presets: @json(\App\Models\Setting::get('gradient_presets', [])),
                get slug() {
                    return this.name
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                },
            }
        }
    </script>
    @endpush
</x-app-layout>