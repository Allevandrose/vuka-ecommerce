<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table with your top-level categories
     * and a starter set of subcategories.
     *
     * Safe to re-run: uses updateOrCreate keyed on slug.
     */
    public function run(): void
    {
        $tree = [
            [
                'name' => 'Electronics',
                'icon' => 'fa-microchip',
                'theme' => 'electronics',
                'primary_color' => '#0F172A',
                'accent_color' => '#3B82F6',
                'children' => [
                    'Smartphones',
                    'Laptops & Computers',
                    'Tablets',
                    'Audio & Headphones',
                    'Cameras',
                    'TVs & Home Theater',
                    'Gaming',
                    'Accessories',
                ],
            ],
            [
                'name' => 'Fashion',
                'icon' => 'fa-tshirt',
                'theme' => 'fashion',
                'primary_color' => '#1F2937',
                'accent_color' => '#EC4899',
                'children' => [
                    'Men',
                    'Women',
                    'Kids',
                    'Shoes',
                    'Bags & Wallets',
                    'Watches',
                    'Jewelry',
                    'Accessories',
                ],
            ],
            [
                'name' => 'Home & Living',
                'icon' => 'fa-couch',
                'theme' => 'home',
                'primary_color' => '#1E3C2C',
                'accent_color' => '#E7A93B',
                'children' => [
                    'Kitchen & Dining',
                    'Bedding',
                    'Home Decor',
                    'Lighting',
                    'Storage & Organization',
                    'Cleaning Supplies',
                ],
            ],
            [
                'name' => 'Beauty & Cosmetics',
                'icon' => 'fa-spa',
                'theme' => 'cosmetics',
                'primary_color' => '#831843',
                'accent_color' => '#F472B6',
                'children' => [
                    'Skincare',
                    'Makeup',
                    'Hair Care',
                    'Fragrances',
                    'Personal Care',
                    'Bath & Body',
                ],
            ],
            [
                'name' => 'Furniture',
                'icon' => 'fa-chair',
                'theme' => 'furniture',
                'primary_color' => '#78350F',
                'accent_color' => '#D97706',
                'children' => [
                    'Living Room',
                    'Bedroom',
                    'Office',
                    'Outdoor',
                    'Dining',
                    'Kids Furniture',
                ],
            ],
            [
                'name' => 'Agriculture',
                'icon' => 'fa-seedling',
                'theme' => 'agriculture',
                'primary_color' => '#14532D',
                'accent_color' => '#22C55E',
                'children' => [
                    'Seeds & Seedlings',
                    'Fertilizers',
                    'Farm Tools',
                    'Animal Feed',
                    'Fresh Produce',
                    'Irrigation',
                ],
            ],
            [
                'name' => 'Adult',
                'icon' => 'fa-lock',
                'theme' => 'adult',
                'primary_color' => '#1F1F1F',
                'accent_color' => '#EF4444',
                'children' => [
                    'Wellness',
                    'Novelty',
                    'Protection',
                ],
            ],
            [
                'name' => 'Books & Media',
                'icon' => 'fa-book',
                'theme' => 'books',
                'primary_color' => '#312E81',
                'accent_color' => '#8B5CF6',
                'children' => [
                    'Fiction',
                    'Non-Fiction',
                    'Textbooks',
                    'Children\'s Books',
                    'Music & Film',
                ],
            ],
        ];

        $topOrder = 0;

        foreach ($tree as $top) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($top['name'])],
                [
                    'name' => $top['name'],
                    'parent_id' => null,
                    'icon' => $top['icon'] ?? null,
                    'theme' => $top['theme'] ?? 'default',
                    'primary_color' => $top['primary_color'] ?? null,
                    'accent_color' => $top['accent_color'] ?? null,
                    'is_active' => true,
                    'sort_order' => $topOrder++,
                ]
            );

            $childOrder = 0;
            foreach (($top['children'] ?? []) as $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($top['name'] . '-' . $childName)],
                    [
                        'name' => $childName,
                        'parent_id' => $parent->id,
                        'is_active' => true,
                        'sort_order' => $childOrder++,
                    ]
                );
            }
        }
    }
}
