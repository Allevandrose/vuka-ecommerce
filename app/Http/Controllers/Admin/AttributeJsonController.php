<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttributeJsonController extends Controller
{
    /**
     * Return active attributes relevant to a category.
     * Used by the product create/edit form to render dynamic inputs.
     *
     * Query: ?category_id=X
     * Returns JSON: [{id, key, label, type, options, unit, is_required, sort_order}, ...]
     */
    public function forCategory(Request $request): JsonResponse
    {
        $categoryId = $request->integer('category_id') ?: null;

        $query = Attribute::active()->ordered();

        if ($categoryId) {
            // Category-scoped OR global
            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereNull('category_id');
            });
        } else {
            // No category selected → only globals
            $query->whereNull('category_id');
        }

        $attributes = $query->get(['id', 'key', 'label', 'type', 'options', 'unit', 'is_required', 'sort_order']);

        return response()->json([
            'attributes' => $attributes->map(fn($a) => [
                'id' => $a->id,
                'key' => $a->key,
                'label' => $a->label,
                'type' => $a->type,
                'options' => $a->options ?? [],
                'unit' => $a->unit,
                'is_required' => $a->is_required,
            ])->values(),
        ]);
    }
}
