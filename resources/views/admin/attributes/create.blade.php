<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Attribute') }}
            </h2>
            <a href="{{ route('admin.attributes.index') }}"
                class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                <i class="fas fa-arrow-left text-xs"></i> Back to Attributes
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

            <form method="POST" action="{{ route('admin.attributes.store') }}"
                  x-data="attributeForm()"
                  @submit="submitting = true">
                @csrf

                {{-- ============================================ --}}
                {{-- IDENTITY --}}
                {{-- ============================================ --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 space-y-5">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Identity</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label for="label" class="block text-sm font-medium text-gray-700 mb-1">
                                    Label <span class="text-red-500">*</span>
                                </label>
                                <input id="label" name="label" type="text" required
                                    x-model="label"
                                    value="{{ old('label') }}"
                                    placeholder="e.g. Screen Size"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">
                                    Human-readable name shown on product forms and filters.
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="key" class="block text-sm font-medium text-gray-700 mb-1">
                                    Key <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2 items-center">
                                    <input id="key" name="key" type="text" required
                                        x-model="key"
                                        value="{{ old('key') }}"
                                        pattern="[a-z0-9_]+"
                                        placeholder="screen_size"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">
                                    <button type="button" @click="key = autoKey()"
                                        class="px-3 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                                        title="Auto-generate from label">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Machine name. Lowercase letters, digits, underscore only. Used in URLs and JSON.
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Scope
                                </label>
                                <select id="category_id" name="category_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">— Global (available to all categories) —</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat['id'] }}" {{ old('category_id') == $cat['id'] ? 'selected' : '' }}>
                                            {{ $cat['depth'] > 0 ? '↳ ' : '' }}{{ $cat['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    Global attributes appear for every product. Category-scoped attributes only appear for products in that category.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================================ --}}
                {{-- TYPE & OPTIONS --}}
                {{-- ============================================ --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 space-y-5">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Type & Options</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Type <span class="text-red-500">*</span>
                                </label>
                                <select id="type" name="type" required
                                    x-model="type"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="text">Text — single line</option>
                                    <option value="textarea">Textarea — multi line</option>
                                    <option value="number">Number</option>
                                    <option value="select">Select — pick one</option>
                                    <option value="multiselect">Multiselect — pick many</option>
                                    <option value="boolean">Boolean — yes / no</option>
                                </select>
                            </div>

                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">
                                    Unit <span class="text-gray-400 text-xs">(optional)</span>
                                </label>
                                <input id="unit" name="unit" type="text"
                                    value="{{ old('unit') }}"
                                    placeholder="e.g. GB, inches, kg"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">
                                    Appended after the value in displays: "8 GB", "6.7 inches".
                                </p>
                            </div>
                        </div>

                        {{-- Options editor — visible only for select / multiselect --}}
                        <div x-show="hasOptions()" x-cloak>
                            <label for="options" class="block text-sm font-medium text-gray-700 mb-1">
                                Options <span class="text-red-500">*</span>
                            </label>
                            <textarea id="options" name="options" rows="8"
                                x-model="optionsRaw"
                                placeholder="One option per line:&#10;Black&#10;Blue&#10;Silver"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ old('options') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                One option per line. Blank lines are ignored.
                            </p>

                            {{-- Live chip preview --}}
                            <div class="mt-3">
                                <p class="text-xs text-gray-500 mb-2">
                                    Preview (<span x-text="parsedOptions.length"></span> option<span x-text="parsedOptions.length === 1 ? '' : 's'"></span>):
                                </p>
                                <div class="flex flex-wrap gap-1.5 min-h-[2rem] p-2 bg-gray-50 rounded-lg border border-gray-200">
                                    <template x-for="(opt, i) in parsedOptions" :key="i">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white border border-gray-300 text-gray-700"
                                            x-text="opt"></span>
                                    </template>
                                    <template x-if="parsedOptions.length === 0">
                                        <span class="text-xs text-gray-400 italic">No options yet — type above to preview</span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Type hint for non-option types --}}
                        <div x-show="!hasOptions()" x-cloak class="text-xs text-gray-500 bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                            <span x-text="typeHint()"></span>
                        </div>
                    </div>
                </div>

                {{-- ============================================ --}}
                {{-- BEHAVIOUR --}}
                {{-- ============================================ --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Behaviour</h3>

                        <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                            <input type="hidden" name="is_filterable" value="0">
                            <input type="checkbox" name="is_filterable" value="1"
                                x-model="isFilterable"
                                {{ old('is_filterable') ? 'checked' : '' }}
                                class="mt-0.5 rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-filter text-emerald-500 text-sm"></i>
                                    <span class="text-sm font-medium text-gray-900">Filterable</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Show this attribute as a filter option in the public sidebar.
                                </p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                            <input type="hidden" name="is_required" value="0">
                            <input type="checkbox" name="is_required" value="1"
                                {{ old('is_required') ? 'checked' : '' }}
                                class="mt-0.5 rounded border-gray-300 text-amber-600 shadow-sm focus:ring-amber-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-asterisk text-amber-500 text-xs"></i>
                                    <span class="text-sm font-medium text-gray-900">Required</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Products in this category must supply a value for this attribute.
                                </p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-eye text-indigo-500 text-sm"></i>
                                    <span class="text-sm font-medium text-gray-900">Active</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Inactive attributes are hidden from product forms and filters.
                                </p>
                            </div>
                        </label>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                                Sort order
                            </label>
                            <input id="sort_order" name="sort_order" type="number" min="0" max="999999"
                                value="{{ old('sort_order', 0) }}"
                                class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <p class="mt-1 text-xs text-gray-500">
                                Lower numbers appear first.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="flex items-center justify-end gap-3 mb-10">
                    <a href="{{ route('admin.attributes.index') }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                        Cancel
                    </a>
                    <button type="submit"
                        :disabled="submitting"
                        class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-6 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold disabled:opacity-60">
                        <span x-show="!submitting">Create Attribute</span>
                        <span x-show="submitting" class="flex items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin"></i> Saving…
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function attributeForm() {
            return {
                label: @json(old('label', '')),
                key: @json(old('key', '')),
                type: @json(old('type', 'text')),
                optionsRaw: @json(old('options', '')),
                isFilterable: {{ old('is_filterable') ? 'true' : 'false' }},
                submitting: false,

                hasOptions() {
                    return this.type === 'select' || this.type === 'multiselect';
                },

                get parsedOptions() {
                    return this.optionsRaw
                        .split(/\r?\n/)
                        .map(s => s.trim())
                        .filter(s => s.length > 0);
                },

                autoKey() {
                    return this.label
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9\s_]/g, '')
                        .replace(/\s+/g, '_')
                        .replace(/_+/g, '_');
                },

                typeHint() {
                    const hints = {
                        text: 'A single line of text. Good for names, model numbers, colors as free-form input.',
                        textarea: 'Multi-line text. Good for longer descriptions or specs.',
                        number: 'A numeric value. Add a unit above to control display, e.g. "GB".',
                        boolean: 'Yes / No. Good for attributes like "Vegan", "Cruelty-free", "Waterproof".',
                    };
                    return hints[this.type] || '';
                },
            }
        }
    </script>
</x-app-layout>