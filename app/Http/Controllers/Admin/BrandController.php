<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    /**
     * List all brands.
     */
    public function index(Request $request): View
    {
        $query = Brand::query();

        // Optional search
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Optional filter
        if ($request->input('filter') === 'verified') {
            $query->where('is_verified', true);
        } elseif ($request->input('filter') === 'unverified') {
            $query->where('is_verified', false);
        } elseif ($request->input('filter') === 'inactive') {
            $query->where('is_active', false);
        }

        $brands = $query->withCount('products')
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.brands.create');
    }

    /**
     * Persist a new brand.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBrand($request);

        $brand = Brand::create($validated);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', "Brand \"{$brand->name}\" created successfully.");
    }

    /**
     * Show edit form.
     */
    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update an existing brand.
     */
    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $this->validateBrand($request, $brand);

        $brand->update($validated);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', "Brand \"{$brand->name}\" updated successfully.");
    }

    /**
     * Delete a brand.
     * Products that reference this brand will have brand_id set to null (nullOnDelete FK).
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        $name = $brand->name;
        $productCount = $brand->products()->count();

        $brand->delete();

        $message = "Brand \"{$name}\" deleted.";
        if ($productCount > 0) {
            $message .= " {$productCount} product(s) now have no brand assigned.";
        }

        return redirect()
            ->route('admin.brands.index')
            ->with('success', $message);
    }

    // ============================================
    // HELPERS
    // ============================================

    protected function validateBrand(Request $request, ?Brand $existing = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
            'is_verified' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];

        $validated = $request->validate($rules);

        $validated['is_verified'] = (bool) ($validated['is_verified'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        // Null out empty strings
        foreach (['description', 'logo', 'website', 'meta_title', 'meta_description'] as $field) {
            if (isset($validated[$field]) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        return $validated;
    }
}
