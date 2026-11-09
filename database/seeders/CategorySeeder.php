<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $languages = Language::where('active', 1)->pluck('code')->toArray();

            $categories = [
                [
                    'slug' => 'electronics',
                    'parent_slug' => null,
                    'translations' => [
                        'en' => ['name' => 'Electronics', 'description' => 'Devices for work, play and everything between'],
                        'fr' => ['name' => 'Électronique', 'description' => 'Appareils pour le travail, le jeu et tout le reste'],
                        'es' => ['name' => 'Electrónica', 'description' => 'Dispositivos para trabajar, jugar y todo lo demás'],
                    ],
                    'image' => 'electronics.jpg',
                ],
                [
                    'slug' => 'fashion',
                    'parent_slug' => null,
                    'translations' => [
                        'en' => ['name' => 'Fashion', 'description' => 'Clothing and accessories'],
                        'fr' => ['name' => 'Mode', 'description' => 'Vêtements et accessoires'],
                        'es' => ['name' => 'Moda', 'description' => 'Ropa y accesorios'],
                    ],
                    'image' => 'fashion.jpg',
                ],
                [
                    'slug' => 'smartphones',
                    'parent_slug' => 'electronics',
                    'translations' => [
                        'en' => ['name' => 'Smartphones', 'description' => 'Latest mobile phones'],
                        'fr' => ['name' => 'Smartphones', 'description' => 'Derniers téléphones mobiles'],
                        'es' => ['name' => 'Smartphones', 'description' => 'Últimos teléfonos móviles'],
                    ],
                    'image' => 'smartphones.jpg',
                ],
                [
                    'slug' => 'audio',
                    'parent_slug' => 'electronics',
                    'translations' => [
                        'en' => ['name' => 'Audio', 'description' => 'Headphones, earbuds and speakers'],
                        'fr' => ['name' => 'Audio', 'description' => 'Casques, écouteurs et enceintes'],
                        'es' => ['name' => 'Audio', 'description' => 'Auriculares, audífonos y altavoces'],
                    ],
                    'image' => 'audio.jpg',
                ],
                [
                    'slug' => 'wearables',
                    'parent_slug' => 'electronics',
                    'translations' => [
                        'en' => ['name' => 'Wearables', 'description' => 'Smartwatches and fitness trackers'],
                        'fr' => ['name' => 'Objets connectés', 'description' => 'Montres connectées et traqueurs'],
                        'es' => ['name' => 'Vestibles', 'description' => 'Relojes inteligentes y pulseras'],
                    ],
                    'image' => 'wearables.jpg',
                ],
                [
                    'slug' => 'laptops',
                    'parent_slug' => 'electronics',
                    'translations' => [
                        'en' => ['name' => 'Laptops', 'description' => 'Notebooks for every kind of work'],
                        'fr' => ['name' => 'Ordinateurs portables', 'description' => 'Ordinateurs pour chaque usage'],
                        'es' => ['name' => 'Portátiles', 'description' => 'Portátiles para cada tipo de trabajo'],
                    ],
                    'image' => 'laptops.jpg',
                ],
                [
                    'slug' => 'tablets',
                    'parent_slug' => 'electronics',
                    'translations' => [
                        'en' => ['name' => 'Tablets', 'description' => 'Light, bright and endlessly useful'],
                        'fr' => ['name' => 'Tablettes', 'description' => 'Légères, lumineuses et utiles'],
                        'es' => ['name' => 'Tablets', 'description' => 'Ligeras, brillantes y muy útiles'],
                    ],
                    'image' => 'tablets.jpg',
                ],
                [
                    'slug' => 'cameras',
                    'parent_slug' => 'electronics',
                    'translations' => [
                        'en' => ['name' => 'Cameras', 'description' => 'DSLR, mirrorless and everything optical'],
                        'fr' => ['name' => 'Appareils photo', 'description' => 'Reflex, hybrides et plus'],
                        'es' => ['name' => 'Cámaras', 'description' => 'Réflex, sin espejo y todo lo óptico'],
                    ],
                    'image' => 'cameras.jpg',
                ],
                [
                    'slug' => 't-shirts',
                    'parent_slug' => 'fashion',
                    'translations' => [
                        'en' => ['name' => 'T-Shirts', 'description' => 'Casual wear t-shirts'],
                        'fr' => ['name' => 'T-shirts', 'description' => 'T-shirts décontractés'],
                        'es' => ['name' => 'Camisetas', 'description' => 'Camisetas informales'],
                    ],
                    'image' => 't-shirts.jpg',
                ],
                [
                    'slug' => 'sneakers',
                    'parent_slug' => 'fashion',
                    'translations' => [
                        'en' => ['name' => 'Sneakers', 'description' => 'Everyday kicks, elevated'],
                        'fr' => ['name' => 'Baskets', 'description' => 'Des baskets pour chaque jour'],
                        'es' => ['name' => 'Zapatillas', 'description' => 'Zapatillas para cada día'],
                    ],
                    'image' => 'sneakers.jpg',
                ],
                [
                    'slug' => 'accessories',
                    'parent_slug' => 'fashion',
                    'translations' => [
                        'en' => ['name' => 'Accessories', 'description' => 'Bags, eyewear and the perfect finish'],
                        'fr' => ['name' => 'Accessoires', 'description' => 'Sacs, lunettes et la touche finale'],
                        'es' => ['name' => 'Accesorios', 'description' => 'Bolsos, gafas y el acabado perfecto'],
                    ],
                    'image' => 'accessories.jpg',
                ],
            ];

            foreach ($categories as $categoryData) {
                $parentId = $categoryData['parent_slug']
                    ? Category::where('slug', $categoryData['parent_slug'])->value('id')
                    : null;

                $category = Category::firstOrCreate(
                    ['slug' => $categoryData['slug']],
                    [
                        'parent_category_id' => $parentId,
                        'status' => true,
                    ]
                );

                // Copy the bundled category image into the public storage disk
                $source = public_path('images/categories/'.$categoryData['image']);
                $localPath = 'categories/'.$categoryData['image'];
                if (is_file($source)) {
                    Storage::disk('public')->put($localPath, file_get_contents($source));
                }

                foreach ($languages as $lang) {
                    $translation = $categoryData['translations'][$lang] ?? $categoryData['translations']['en'];

                    $category->translations()->updateOrCreate(
                        ['language_code' => $lang],
                        [
                            'name' => $translation['name'],
                            'description' => $translation['description'],
                            'image_url' => $localPath,
                        ]
                    );
                }
            }
        });
    }
}
