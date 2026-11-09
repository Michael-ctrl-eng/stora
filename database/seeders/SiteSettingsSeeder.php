<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('site_settings')->insert([
            'site_name' => 'Stora',
            'tagline' => 'Everything you love. In one place.',
            'meta_title' => 'Stora - Premium Online Store',
            'meta_description' => 'Stora is your destination for premium tech, audio, wearables and fashion — carefully curated, beautifully simple.',
            'meta_keywords' => 'stora, online store, electronics, audio, wearables, fashion',
            'logo' => 'images/logo.png',
            'favicon' => 'favicon.ico',
            'contact_email' => 'hello@stora.store',
            'contact_phone' => '+1 234 567 890',
            'address' => '1 Stora Way, Commerce City, Webland',
            'footer_text' => '© 2026 Stora. All rights reserved.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
