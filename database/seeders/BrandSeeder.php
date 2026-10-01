<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Starter brand list. Safe to re-run — keyed on slug.
     * Add more via the admin UI later.
     */
    public function run(): void
    {
        $brands = [
            // Electronics — smartphones & computing
            ['name' => 'Apple', 'website' => 'https://apple.com', 'is_verified' => true],
            ['name' => 'Samsung', 'website' => 'https://samsung.com', 'is_verified' => true],
            ['name' => 'Google', 'website' => 'https://google.com', 'is_verified' => true],
            ['name' => 'Xiaomi', 'website' => 'https://mi.com', 'is_verified' => true],
            ['name' => 'Huawei', 'website' => 'https://huawei.com', 'is_verified' => true],
            ['name' => 'OnePlus', 'website' => 'https://oneplus.com', 'is_verified' => true],
            ['name' => 'Nokia', 'website' => 'https://nokia.com', 'is_verified' => true],
            ['name' => 'Tecno', 'website' => 'https://tecno-mobile.com', 'is_verified' => true],
            ['name' => 'Infinix', 'website' => 'https://infinixmobility.com', 'is_verified' => true],
            ['name' => 'Oppo', 'website' => 'https://oppo.com', 'is_verified' => true],

            // Electronics — laptops & audio
            ['name' => 'Dell', 'website' => 'https://dell.com', 'is_verified' => true],
            ['name' => 'HP', 'website' => 'https://hp.com', 'is_verified' => true],
            ['name' => 'Lenovo', 'website' => 'https://lenovo.com', 'is_verified' => true],
            ['name' => 'Asus', 'website' => 'https://asus.com', 'is_verified' => true],
            ['name' => 'Acer', 'website' => 'https://acer.com', 'is_verified' => true],
            ['name' => 'Sony', 'website' => 'https://sony.com', 'is_verified' => true],
            ['name' => 'Bose', 'website' => 'https://bose.com', 'is_verified' => true],
            ['name' => 'JBL', 'website' => 'https://jbl.com', 'is_verified' => true],
            ['name' => 'Anker', 'website' => 'https://anker.com', 'is_verified' => true],
            ['name' => 'Canon', 'website' => 'https://canon.com', 'is_verified' => true],
            ['name' => 'Nikon', 'website' => 'https://nikon.com', 'is_verified' => true],

            // Fashion
            ['name' => 'Nike', 'website' => 'https://nike.com', 'is_verified' => true],
            ['name' => 'Adidas', 'website' => 'https://adidas.com', 'is_verified' => true],
            ['name' => 'Puma', 'website' => 'https://puma.com', 'is_verified' => true],
            ['name' => 'Levi\'s', 'website' => 'https://levi.com', 'is_verified' => true],
            ['name' => 'Zara', 'website' => 'https://zara.com', 'is_verified' => false],
            ['name' => 'H&M', 'website' => 'https://hm.com', 'is_verified' => false],
            ['name' => 'Casio', 'website' => 'https://casio.com', 'is_verified' => true],
            ['name' => 'Fossil', 'website' => 'https://fossil.com', 'is_verified' => true],

            // Beauty & Cosmetics
            ['name' => 'Nivea', 'website' => 'https://nivea.com', 'is_verified' => true],
            ['name' => 'L\'Oreal', 'website' => 'https://loreal.com', 'is_verified' => true],
            ['name' => 'Maybelline', 'website' => 'https://maybelline.com', 'is_verified' => true],
            ['name' => 'The Ordinary', 'website' => 'https://theordinary.com', 'is_verified' => true],
            ['name' => 'Cerave', 'website' => 'https://cerave.com', 'is_verified' => true],
            ['name' => 'Nivea Men', 'website' => 'https://nivea.com', 'is_verified' => true],

            // Home & Living
            ['name' => 'Philips', 'website' => 'https://philips.com', 'is_verified' => true],
            ['name' => 'Panasonic', 'website' => 'https://panasonic.com', 'is_verified' => true],
            ['name' => 'LG', 'website' => 'https://lg.com', 'is_verified' => true],
            ['name' => 'Whirlpool', 'website' => 'https://whirlpool.com', 'is_verified' => true],

            // Agriculture
            ['name' => 'Kenya Seed', 'website' => 'https://kenyaseed.com', 'is_verified' => true],
            ['name' => 'Bayer', 'website' => 'https://bayer.com', 'is_verified' => true],
            ['name' => 'Syngenta', 'website' => 'https://syngenta.com', 'is_verified' => true],

            // Books & Media
            ['name' => 'Penguin Random House', 'website' => 'https://penguinrandomhouse.com', 'is_verified' => true],
            ['name' => 'Oxford University Press', 'website' => 'https://oup.com', 'is_verified' => true],
            ['name' => 'Longhorn Publishers', 'website' => 'https://longhornpublishers.com', 'is_verified' => false],
        ];

        foreach ($brands as $data) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'website' => $data['website'] ?? null,
                    'is_verified' => $data['is_verified'] ?? false,
                    'is_active' => true,
                ]
            );
        }
    }
}
