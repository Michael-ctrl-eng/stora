<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\StoreSetting;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Catalog, system configuration and demo shop data only.
     * No fake orders, payments or refunds are seeded — order
     * history is only ever populated by real checkouts.
     */
    public function run(): void
    {
        // ---------- System configuration ----------
        $this->call([
            LanguageSeeder::class,
            CurrencySeeder::class,
            SiteSettingsSeeder::class,
            PaymentGatewaySeeder::class,
            MenuSeeder::class,
        ]);

        // ---------- Demo storefront (Stora) ----------
        // Demo admin account for the admin panel (change in production!).
        User::firstOrCreate(
            ['email' => 'admin@stora.store'],
            [
                'name' => 'Stora Admin',
                'password' => Hash::make('stora123'),
            ]
        );

        $vendor = Vendor::firstOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => 'Stora',
                'password' => Hash::make('abc123'),
                'phone' => '+1 234 567 890',
                'profile_image' => 'images/logo.png',
            ]
        );

        Shop::firstOrCreate(
            ['name' => 'Stora'],
            [
                'vendor_id' => $vendor->id,
                'name' => 'Stora',
                'logo' => 'images/logo.png',
                'description' => 'Curated tech, audio and everyday essentials — beautifully simple. Stora is home to the brands and products you love.',
            ]
        );

        StoreSetting::firstOrCreate(
            ['key' => 'default_currency'],
            ['key' => 'default_currency', 'value' => 'USD']
        );
        StoreSetting::firstOrCreate(
            ['key' => 'meta_title'],
            ['key' => 'meta_title', 'value' => 'Stora - Everything you love. In one place.']
        );
        StoreSetting::firstOrCreate(
            ['key' => 'meta_description'],
            ['key' => 'meta_description', 'value' => 'Stora is your destination for premium tech, audio, wearables and fashion — carefully curated, beautifully simple.']
        );

        // ---------- Catalog ----------
        $this->call([
            BrandSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            BannerSeeder::class,
        ]);
    }
}
