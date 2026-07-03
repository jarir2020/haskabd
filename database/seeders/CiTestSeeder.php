<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;

class CiTestSeeder extends Seeder
{
    public function run()
    {
        if (GeneralSetting::where('status', 1)->exists()) {
            return;
        }

        GeneralSetting::create([
            'name' => 'Haskabd',
            'white_logo' => 'public/uploads/settings/default.png',
            'dark_logo' => 'public/uploads/settings/default.png',
            'favicon' => 'public/uploads/settings/default.png',
            'og_baner' => 'public/uploads/settings/default.png',
            'copyright' => 'Haskabd',
            'status' => 1,
            'facebook_verification' => '',
            'google_verification' => '',
            'meta_description' => 'Haskabd ecommerce store',
            'meta_keyword' => 'haskabd,ecommerce',
            'hot_deal_end_date' => now()->addYear()->toDateString(),
            'flash_sale_end_date' => now()->addYear()->toDateString(),
            'show_all_products' => 0,
            'show_category_wise_products' => 0,
        ]);
    }
}
