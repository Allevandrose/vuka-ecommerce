<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttributeController extends Controller
{
    /**
     * List all attributes.
     */
    public function index(Request $request): View
    {
        $query = Attribute::with('category');

        // Optional search
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('label', 'like', "%{$search}%");
            });
        }

        // Optional filter by category (special value "global" = category_id is null)
        if ($categoryFilter = $request->input('category')) {
            if ($categoryFilter === 'global') {
                $query->whereNull('category_id');
            } else {
                $query->where('category_id', $categoryFilter);
            }
        }

        // Optional filter by type
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // Optional filter: only filterable
        if ($request->input('filter') === 'filterable') {
            $query->where('is_filterable', true);
        } elseif ($request->input('filter') === 'required') {
            $query->where('is_required', true);
        } elseif ($request->input('filter') === 'inactive') {
            $query->where('is_active', false);
        }

        $attributes = $query
            ->orderByRaw('category_id IS NULL DESC')  // global first
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->paginate(30)
            ->withQueryString();

        // For the filter dropdown
        $categories = Category::topLevel()->ordered()->with('children')->get();
        $types = ['text', 'textarea', 'number', 'select', 'multiselect', 'boolean'];

        return view('admin.attributes.index', compact('attributes', 'categories', 'types'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        $categories = $this->categoryTree();

        return view('admin.attributes.create', compact('categories'));
    }

    /**
     * Persist a new attribute.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAttribute($request);

        $attribute = Attribute::create($validated);

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', "Attribute \"{$attribute->label}\" created successfully.");
    }

    /**
     * Show edit form.
     */
    public function edit(Attribute $attribute): View
    {
        $categories = $this->categoryTree();

        return view('admin.attributes.edit', compact('attribute', 'categories'));
    }

    /**
     * Update an existing attribute.
     */
    public function update(Request $request, Attribute $attribute): RedirectResponse
    {
        $validated = $this->validateAttribute($request, $attribute);

        $attribute->update($validated);

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', "Attribute \"{$attribute->label}\" updated successfully.");
    }

    /**
     * Delete an attribute.
     */
    public function destroy(Attribute $attribute): RedirectResponse
    {
        $label = $attribute->label;
        $attribute->delete();

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', "Attribute \"{$label}\" deleted.");
    }

    // ============================================
    // HELPERS
    // ============================================

    protected function validateAttribute(Request $request, ?Attribute $existing = null): array
    {
        $rules = [
            'key' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',  // lowercase, digits, underscore only
            ],
            'label' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'type' => ['required', 'in:text,textarea,number,select,multiselect,boolean'],
            'options' => ['nullable', 'string', 'max:5000'],  // raw textarea, one per line
            'unit' => ['nullable', 'string', 'max:20'],
            'is_filterable' => ['nullable', 'boolean'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ];

        $validated = $request->validate($rules);

        // Only select/multiselect need options — parse newline-separated values into JSON array
        if (in_array($validated['type'], ['select', 'multiselect'], true)) {
            $raw = $validated['options'] ?? '';
            $options = collect(preg_split('/\r?\n/', $raw))
                ->map(fn($line) => trim($line))
                ->filter()
                ->values()
                ->all();

            if (empty($options)) {
                // Validation-level guard: throw so the user sees the message at the field
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'options' => 'At least one option is required for select and multiselect attributes.',
                ]);
            }

            $validated['options'] = $options;
        } else {
            $validated['options'] = null;
        }

        // Booleans
        $validated['is_filterable'] = (bool) ($validated['is_filterable'] ?? false);
        $validated['is_required'] = (bool) ($validated['is_required'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        // Null out empties
        foreach (['unit', 'category_id'] as $field) {
            if (isset($validated[$field]) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        return $validated;
    }

    /**
     * Return categories as a flat array of [id, name, depth] for the dropdown,
     * with children indented under parents.
     */
    protected function categoryTree(): array
    {
        $flat = [];

        $top = Category::topLevel()->ordered()->get();
        foreach ($top as $parent) {
            $flat[] = ['id' => $parent->id, 'name' => $parent->name, 'depth' => 0];
            foreach ($parent->children()->orderBy('sort_order')->orderBy('name')->get() as $child) {
                $flat[] = ['id' => $child->id, 'name' => $child->name, 'depth' => 1];
            }
        }

        return $flat;
    }
}
