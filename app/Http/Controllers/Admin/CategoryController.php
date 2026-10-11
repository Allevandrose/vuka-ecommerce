<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * List all categories in tree order.
     */
    public function index(): View
    {
        $categories = Category::with(['children' => function ($q) {
            $q->orderBy('sort_order')->orderBy('name');
        }])
            ->topLevel()
            ->ordered()
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the create form.
     */
    public function create(): View
    {
        $parents = Category::topLevel()->ordered()->get();

        $gradientPresets = $this->collectGradientPresets();

        return view('admin.categories.create', compact('parents', 'gradientPresets'));
    }

    /**
     * Persist a new category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCategory($request);

        $category = Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" created successfully.");
    }

    /**
     * Show the edit form.
     */
    public function edit(Category $category): View
    {
        // Exclude this category and its descendants from the parent dropdown,
        // so the admin can't make a category its own ancestor.
        $excludedIds = $this->descendantIds($category);
        $excludedIds[] = $category->id;

        $parents = Category::topLevel()
            ->whereNotIn('id', $excludedIds)
            ->ordered()
            ->get();

        $gradientPresets = $this->collectGradientPresets();

        return view('admin.categories.edit', compact('category', 'parents', 'gradientPresets'));
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $this->validateCategory($request, $category);

        // Prevent making this category its own parent
        if (isset($validated['parent_id']) && (int) $validated['parent_id'] === $category->id) {
            return back()->withErrors(['parent_id' => 'A category cannot be its own parent.'])->withInput();
        }

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" updated successfully.");
    }

    /**
     * Delete a category.
     * Children are NOT deleted — they become top-level (parent_id = null).
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Guard: refuse deletion if products exist under this category or its children
        if ($category->products()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', "Cannot delete \"{$category->name}\" — it has products. Move or delete the products first.");
        }

        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category \"{$name}\" deleted. Its subcategories were promoted to top-level.");
    }

    // ============================================
    // HELPERS — validation
    // ============================================

    protected function validateCategory(Request $request, ?Category $existing = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'theme' => ['nullable', 'string', 'max:50'],
            'primary_color' => ['nullable', 'string', 'max:32'],
            'accent_color' => ['nullable', 'string', 'max:32'],
            'gradient_css' => ['nullable', 'string', 'max:1000'],
            'accent_gradient_css' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
        ];

        $validated = $request->validate($rules);

        // Coerce checkbox
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        // Null out empty strings so DB stores NULL, not ''
        foreach (
            [
                'icon',
                'theme',
                'primary_color',
                'accent_color',
                'gradient_css',
                'accent_gradient_css',
                'meta_title',
                'meta_description',
                'meta_keywords',
            ] as $field
        ) {
            if (isset($validated[$field]) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        return $validated;
    }

    /**
     * Get the IDs of all descendants of the given category (for parent dropdown exclusion).
     */
    protected function descendantIds(Category $category): array
    {
        $ids = [];

        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->descendantIds($child));
        }

        return $ids;
    }

    // ============================================
    // HELPERS — gradient presets
    // ============================================

    /**
     * Build the full list of gradient presets available on the create/edit forms.
     *
     * Sources (in order, deduplicated by CSS string):
     *   1. Seeded presets from `settings.gradient_presets`
     *   2. Any distinct `gradient_css` already saved on any category (labelled "Custom")
     *
     * Returns an array of ['name' => string, 'css' => string].
     */
    protected function collectGradientPresets(): array
    {
        $seeded = Setting::get('gradient_presets', []);
        if (!is_array($seeded)) {
            $seeded = [];
        }

        $custom = Category::query()
            ->whereNotNull('gradient_css')
            ->where('gradient_css', '!=', '')
            ->pluck('gradient_css')
            ->unique()
            ->values()
            ->all();

        $presets = [];
        $seenCss = [];

        foreach ($seeded as $preset) {
            if (!is_array($preset) || empty($preset['css'])) {
                continue;
            }
            $css = trim((string) $preset['css']);
            if ($css === '' || isset($seenCss[$css])) {
                continue;
            }
            $seenCss[$css] = true;
            $presets[] = [
                'name' => $preset['name'] ?? 'Preset',
                'css' => $css,
            ];
        }

        foreach ($custom as $css) {
            $css = trim((string) $css);
            if ($css === '' || isset($seenCss[$css])) {
                continue;
            }
            $seenCss[$css] = true;
            $presets[] = [
                'name' => 'Custom',
                'css' => $css,
            ];
        }

        return $presets;
    }
}
