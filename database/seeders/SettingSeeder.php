<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Global site settings.
     * Safe to re-run: uses Setting::set (which uses updateOrCreate internally).
     */
    public function run(): void
    {
        // ============================================
        // CURRENCY
        // ============================================
        Setting::set('default_currency', 'KES', 'string');
        Setting::set('supported_currencies', [
            'KES' => 'Kenyan Shilling',
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
        ], 'json');
        Setting::set('allow_multi_currency', false, 'bool');

        // ============================================
        // VENDOR LIMITS
        // ============================================
        Setting::set('vendor_product_cap', 100, 'int');

        // ============================================
        // RESTRICTED PRODUCTS
        // ============================================
        // Keyword list used to flag potentially restricted listings.
        // Admin can edit this list. Vendor products containing any of these
        // in name/description get flagged for review.
        Setting::set('restricted_keywords', [
            'firearm',
            'gun',
            'rifle',
            'pistol',
            'ammunition',
            'ammo',
            'bullet',
            'explosive',
            'grenade',
            'tnt',
            'ivory',
            'rhino horn',
            'narcotic',
            'cocaine',
            'heroin',
            'marijuana',
            'bhang',
            'counterfeit currency',
            'fake money',
            'human remains',
            'prescription drug',
            'unlicensed pharmaceutical',
            'endangered species',
            'wildlife',
            'poison',
        ], 'json');

        // ============================================
        // GRADIENT PRESETS (for category theming)
        // ============================================
        Setting::set('gradient_presets', [
            [
                'name' => 'Silver Metallic',
                'css' => 'linear-gradient(135deg, #b8b8c4 0%, #e8e8ee 25%, #8a8a96 50%, #d4d4dc 75%, #7a7a86 100%)',
            ],
            [
                'name' => 'Gold Metallic',
                'css' => 'linear-gradient(135deg, #8B6F1F 0%, #F5D76E 25%, #C9A227 50%, #FBE79A 75%, #8B6F1F 100%)',
            ],
            [
                'name' => 'Bronze Metallic',
                'css' => 'linear-gradient(135deg, #7a4a1f 0%, #c98a4a 25%, #8a5a2a 50%, #e0a870 75%, #6a3f1a 100%)',
            ],
            [
                'name' => 'Gunmetal Metallic',
                'css' => 'linear-gradient(135deg, #2c2f33 0%, #4e5257 25%, #1f2226 50%, #5a5f66 75%, #1a1c1f 100%)',
            ],
            [
                'name' => 'Copper Metallic',
                'css' => 'linear-gradient(135deg, #8a4a2a 0%, #d98555 25%, #a0562f 50%, #e8a87a 75%, #6f3a1f 100%)',
            ],
            [
                'name' => 'Emerald Deep',
                'css' => 'linear-gradient(135deg, #14231C 0%, #20503C 50%, #0F2A1E 100%)',
            ],
            [
                'name' => 'Sunset Warm',
                'css' => 'linear-gradient(135deg, #7C2D12 0%, #EA580C 50%, #FBBF24 100%)',
            ],
            [
                'name' => 'Midnight Blue',
                'css' => 'linear-gradient(135deg, #0F172A 0%, #1E3A8A 50%, #3B82F6 100%)',
            ],
            [
                'name' => 'Rose Gold',
                'css' => 'linear-gradient(135deg, #831843 0%, #F472B6 50%, #FBCFE8 100%)',
            ],
        ], 'json');

        // ============================================
        // SITE INFO
        // ============================================
        Setting::set('site_name', 'Vuka Shop', 'string');
        Setting::set('site_tagline', "Kenya's trusted marketplace", 'string');
        Setting::set('support_email', 'support@vukashop.com', 'string');
        Setting::set('support_phone', '+254 700 000 000', 'string');

        // ============================================
        // MODERATION THRESHOLDS
        // ============================================
        // Number of open complaints on a product that triggers auto-flagging
        // for admin review.
        Setting::set('complaint_auto_flag_threshold', 10, 'int');
    }
}
