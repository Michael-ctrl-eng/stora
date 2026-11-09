<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $languages = Language::where('active', 1)->get();

            $banners = [
                [
                    'type' => 'promotion',
                    'status' => 1,
                    'translations' => [
                        'title' => 'Ready to Shop.',
                        'description' => 'Curated tech, audio and everyday essentials — beautifully simple.',
                        'image' => 'stora-hero.jpg',
                    ],
                ],
            ];

            foreach ($banners as $item) {

                $banner = Banner::create([
                    'type' => $item['type'],
                    'status' => $item['status'],
                ]);

                // Reference the bundled hero image directly from public/images (served as a static asset — no symlink needed)
                $localPath = 'images/banners/'.$item['translations']['image'];

                foreach ($languages as $lang) {
                    $translatedTitle = match ($lang->code) {
                        'es' => 'Listo para comprar.',
                        'de' => 'Bereit zum Einkaufen.',
                        'fr' => 'Prêt à magasiner.',
                        default => $item['translations']['title'],
                    };

                    $translatedDescription = match ($lang->code) {
                        'es' => 'Tecnología, audio y esenciales cuidadosamente seleccionados — bello y simple.',
                        'de' => 'Sorgfältig kuratierte Technik, Audio und Essentials — schön einfach.',
                        'fr' => 'Tech, audio et essentiels soigneusement sélectionnés — beau et simple.',
                        default => $item['translations']['description'],
                    };

                    $banner->translations()->create([
                        'language_code' => $lang->code,
                        'title' => $translatedTitle,
                        'description' => $translatedDescription,
                        'image_url' => $localPath,
                    ]);
                }
            }
        });
    }
}
