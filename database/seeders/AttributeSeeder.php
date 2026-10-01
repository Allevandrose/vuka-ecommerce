<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Seed category-scoped attributes.
     *
     * Attributes with category_id = null are global (available to every category).
     * Attributes with category_id = X apply to that category and its descendants.
     *
     * Safe to re-run: uses updateOrCreate keyed on (category_id, key).
     */
    public function run(): void
    {
        // ============================================
        // GLOBAL ATTRIBUTES (available everywhere)
        // ============================================
        $global = [
            ['key' => 'color', 'label' => 'Color', 'type' => 'text', 'is_filterable' => true],
            ['key' => 'material', 'label' => 'Material', 'type' => 'text', 'is_filterable' => true],
            ['key' => 'warranty', 'label' => 'Warranty', 'type' => 'text', 'unit' => 'months'],
        ];

        foreach ($global as $i => $attr) {
            Attribute::updateOrCreate(
                ['category_id' => null, 'key' => $attr['key']],
                array_merge($attr, [
                    'category_id' => null,
                    'is_active' => true,
                    'sort_order' => $i,
                ])
            );
        }

        // ============================================
        // ELECTRONICS
        // ============================================
        $electronics = Category::where('slug', 'electronics')->first();
        if ($electronics) {
            $electronicsAttrs = [
                ['key' => 'brand', 'label' => 'Brand', 'type' => 'text', 'is_filterable' => true],
                ['key' => 'model', 'label' => 'Model', 'type' => 'text'],
                ['key' => 'chipset', 'label' => 'Chipset', 'type' => 'text', 'is_filterable' => true],
                [
                    'key' => 'ram',
                    'label' => 'RAM',
                    'type' => 'select',
                    'unit' => 'GB',
                    'is_filterable' => true,
                    'options' => ['2', '3', '4', '6', '8', '12', '16', '32', '64']
                ],
                [
                    'key' => 'storage',
                    'label' => 'Storage',
                    'type' => 'select',
                    'unit' => 'GB',
                    'is_filterable' => true,
                    'options' => ['16', '32', '64', '128', '256', '512', '1024', '2048']
                ],
                ['key' => 'screen_size', 'label' => 'Screen Size', 'type' => 'number', 'unit' => 'inches', 'is_filterable' => true],
                ['key' => 'battery', 'label' => 'Battery Capacity', 'type' => 'number', 'unit' => 'mAh'],
                [
                    'key' => 'os',
                    'label' => 'Operating System',
                    'type' => 'select',
                    'is_filterable' => true,
                    'options' => ['Android', 'iOS', 'Windows', 'macOS', 'Linux', 'Other']
                ],
                [
                    'key' => 'connectivity',
                    'label' => 'Connectivity',
                    'type' => 'multiselect',
                    'is_filterable' => true,
                    'options' => ['Wi-Fi', 'Bluetooth', '5G', '4G', 'USB-C', 'HDMI']
                ],
            ];

            foreach ($electronicsAttrs as $i => $attr) {
                Attribute::updateOrCreate(
                    ['category_id' => $electronics->id, 'key' => $attr['key']],
                    array_merge($attr, [
                        'category_id' => $electronics->id,
                        'is_active' => true,
                        'sort_order' => $i,
                    ])
                );
            }
        }

        // ============================================
        // FASHION
        // ============================================
        $fashion = Category::where('slug', 'fashion')->first();
        if ($fashion) {
            $fashionAttrs = [
                [
                    'key' => 'size',
                    'label' => 'Size',
                    'type' => 'select',
                    'is_filterable' => true,
                    'options' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL']
                ],
                [
                    'key' => 'fit',
                    'label' => 'Fit',
                    'type' => 'select',
                    'is_filterable' => true,
                    'options' => ['Slim', 'Regular', 'Loose', 'Oversized']
                ],
                ['key' => 'fabric', 'label' => 'Fabric', 'type' => 'text', 'is_filterable' => true],
                [
                    'key' => 'pattern',
                    'label' => 'Pattern',
                    'type' => 'select',
                    'is_filterable' => true,
                    'options' => ['Solid', 'Striped', 'Checked', 'Floral', 'Printed', 'Plain']
                ],
                [
                    'key' => 'sleeve_length',
                    'label' => 'Sleeve Length',
                    'type' => 'select',
                    'options' => ['Sleeveless', 'Short Sleeve', 'Long Sleeve', 'Three-Quarter']
                ],
                [
                    'key' => 'shoe_size',
                    'label' => 'Shoe Size',
                    'type' => 'select',
                    'is_filterable' => true,
                    'options' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45']
                ],
            ];

            foreach ($fashionAttrs as $i => $attr) {
                Attribute::updateOrCreate(
                    ['category_id' => $fashion->id, 'key' => $attr['key']],
                    array_merge($attr, [
                        'category_id' => $fashion->id,
                        'is_active' => true,
                        'sort_order' => $i,
                    ])
                );
            }
        }

        // ============================================
        // BEAUTY & COSMETICS
        // ============================================
        $cosmetics = Category::where('slug', 'beauty-cosmetics')->first();
        if ($cosmetics) {
            $cosmeticAttrs = [
                ['key' => 'volume', 'label' => 'Volume', 'type' => 'number', 'unit' => 'ml', 'is_filterable' => true],
                [
                    'key' => 'skin_type',
                    'label' => 'Skin Type',
                    'type' => 'multiselect',
                    'is_filterable' => true,
                    'options' => ['Oily', 'Dry', 'Combination', 'Sensitive', 'Normal', 'All']
                ],
                ['key' => 'shade', 'label' => 'Shade', 'type' => 'text', 'is_filterable' => true],
                [
                    'key' => 'formulation',
                    'label' => 'Formulation',
                    'type' => 'select',
                    'options' => ['Cream', 'Liquid', 'Gel', 'Powder', 'Serum', 'Oil', 'Foam']
                ],
                ['key' => 'cruelty_free', 'label' => 'Cruelty-Free', 'type' => 'boolean', 'is_filterable' => true],
                ['key' => 'vegan', 'label' => 'Vegan', 'type' => 'boolean', 'is_filterable' => true],
                ['key' => 'spf', 'label' => 'SPF', 'type' => 'number', 'is_filterable' => true],
            ];

            foreach ($cosmeticAttrs as $i => $attr) {
                Attribute::updateOrCreate(
                    ['category_id' => $cosmetics->id, 'key' => $attr['key']],
                    array_merge($attr, [
                        'category_id' => $cosmetics->id,
                        'is_active' => true,
                        'sort_order' => $i,
                    ])
                );
            }
        }

        // ============================================
        // AGRICULTURE
        // ============================================
        $agri = Category::where('slug', 'agriculture')->first();
        if ($agri) {
            $agriAttrs = [
                ['key' => 'crop_type', 'label' => 'Crop Type', 'type' => 'text', 'is_filterable' => true],
                [
                    'key' => 'season',
                    'label' => 'Season',
                    'type' => 'select',
                    'is_filterable' => true,
                    'options' => ['Long Rains', 'Short Rains', 'Dry Season', 'All Year']
                ],
                ['key' => 'weight_kg', 'label' => 'Weight', 'type' => 'number', 'unit' => 'kg'],
                ['key' => 'organic', 'label' => 'Organic', 'type' => 'boolean', 'is_filterable' => true],
                ['key' => 'certification', 'label' => 'Certification', 'type' => 'text'],
            ];

            foreach ($agriAttrs as $i => $attr) {
                Attribute::updateOrCreate(
                    ['category_id' => $agri->id, 'key' => $attr['key']],
                    array_merge($attr, [
                        'category_id' => $agri->id,
                        'is_active' => true,
                        'sort_order' => $i,
                    ])
                );
            }
        }
    }
}
