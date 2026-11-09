<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::where('active', 1)->get();

        $brands = [
            [
                'slug' => 'aurora',
                'translations' => [
                    'en' => ['name' => 'Aurora', 'description' => 'Sound, beautifully engineered.'],
                    'es' => ['name' => 'Aurora', 'description' => 'Sonido, bellamente diseñado.'],
                    'de' => ['name' => 'Aurora', 'description' => 'Klang, schön konstruiert.'],
                ],
            ],
            [
                'slug' => 'lume',
                'translations' => [
                    'en' => ['name' => 'Lume', 'description' => 'Wearables that light up your day.'],
                    'es' => ['name' => 'Lume', 'description' => 'Vestibles que iluminan tu día.'],
                    'de' => ['name' => 'Lume', 'description' => 'Wearables, die Ihren Tag erhellen.'],
                ],
            ],
            [
                'slug' => 'nova',
                'translations' => [
                    'en' => ['name' => 'Nova', 'description' => 'Bold tech for everyday life.'],
                    'es' => ['name' => 'Nova', 'description' => 'Tecnología audaz para el día a día.'],
                    'de' => ['name' => 'Nova', 'description' => 'Mutige Technik für den Alltag.'],
                ],
            ],
            [
                'slug' => 'vanta',
                'translations' => [
                    'en' => ['name' => 'Vanta', 'description' => 'Pro-grade tools for modern work.'],
                    'es' => ['name' => 'Vanta', 'description' => 'Herramientas profesionales para el trabajo moderno.'],
                    'de' => ['name' => 'Vanta', 'description' => 'Profi-Werkzeuge für modernes Arbeiten.'],
                ],
            ],
            [
                'slug' => 'northpeak',
                'translations' => [
                    'en' => ['name' => 'Northpeak', 'description' => 'Built for the journey.'],
                    'es' => ['name' => 'Northpeak', 'description' => 'Hecho para el viaje.'],
                    'de' => ['name' => 'Northpeak', 'description' => 'Gebaut für die Reise.'],
                ],
            ],
            [
                'slug' => 'marlo',
                'translations' => [
                    'en' => ['name' => 'Marlo', 'description' => 'Quiet luxury, everyday.'],
                    'es' => ['name' => 'Marlo', 'description' => 'Lujo discreto, cada día.'],
                    'de' => ['name' => 'Marlo', 'description' => 'Leiser Luxus, jeden Tag.'],
                ],
            ],
            [
                'slug' => 'canon',
                'translations' => [
                    'en' => ['name' => 'Canon', 'description' => 'Optics trusted for generations.'],
                    'es' => ['name' => 'Canon', 'description' => 'Óptica confiable de generación en generación.'],
                    'de' => ['name' => 'Canon', 'description' => 'Optik, seit Generationen bewährt.'],
                ],
            ],
        ];

        foreach ($brands as $brandData) {
            $brandId = DB::table('brands')->where('slug', $brandData['slug'])->value('id');

            if (! $brandId) {
                // Copy the bundled wordmark logo into the public storage disk
                $logoPath = 'brands/'.$brandData['slug'].'.png';
                $source = public_path('images/brands/'.$brandData['slug'].'.png');
                if (is_file($source) && ! Storage::disk('public')->exists($logoPath)) {
                    Storage::disk('public')->put($logoPath, file_get_contents($source));
                }

                $brandId = DB::table('brands')->insertGetId([
                    'slug' => $brandData['slug'],
                    'logo_url' => $logoPath,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($languages as $lang) {
                $translation = $brandData['translations'][$lang->code] ?? $brandData['translations']['en'];

                $exists = DB::table('brand_translations')
                    ->where('brand_id', $brandId)
                    ->where('locale', $lang->code)
                    ->exists();

                if (! $exists) {
                    DB::table('brand_translations')->insert([
                        'brand_id' => $brandId,
                        'locale' => $lang->code,
                        'name' => $translation['name'],
                        'description' => $translation['description'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
