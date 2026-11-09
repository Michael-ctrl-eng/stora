<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $languages = Language::where('active', 1)->get();

            $sizeAttr = Attribute::firstOrCreate(['name' => 'Size']);
            $colorAttr = Attribute::firstOrCreate(['name' => 'Color']);

            /* ------------------------------------------------------------
             |  Attribute values (sizes + curated color palette)
             * ------------------------------------------------------------ */
            $sizes = ['Small', 'Medium', 'Large'];
            $sizeTranslations = [
                'Small' => ['es' => 'Pequeño', 'de' => 'Klein'],
                'Medium' => ['es' => 'Mediano', 'de' => 'Mittel'],
                'Large' => ['es' => 'Grande', 'de' => 'Groß'],
            ];

            $colors = ['Black', 'White', 'Midnight', 'Starlight', 'Silver', 'Space Gray', 'Teal', 'Red', 'Blue'];
            $colorTranslations = [
                'Black' => ['es' => 'Negro', 'de' => 'Schwarz'],
                'White' => ['es' => 'Blanco', 'de' => 'Weiß'],
                'Midnight' => ['es' => 'Medianoche', 'de' => 'Mitternacht'],
                'Starlight' => ['es' => 'Blanco estelar', 'de' => 'Sternenlicht'],
                'Silver' => ['es' => 'Plata', 'de' => 'Silber'],
                'Space Gray' => ['es' => 'Gris espacial', 'de' => 'Space Grau'],
                'Teal' => ['es' => 'Verde azulado', 'de' => 'Türkis'],
                'Red' => ['es' => 'Rojo', 'de' => 'Rot'],
                'Blue' => ['es' => 'Azul', 'de' => 'Blau'],
            ];

            $sizeValues = [];
            foreach ($sizes as $size) {
                $attrValue = AttributeValue::firstOrCreate([
                    'attribute_id' => $sizeAttr->id,
                    'value' => $size,
                ]);

                foreach ($languages as $lang) {
                    $attrValue->translations()->firstOrCreate([
                        'language_code' => $lang->code,
                    ], [
                        'translated_value' => $sizeTranslations[$size][$lang->code] ?? $size,
                    ]);
                }
                $sizeValues[$size] = $attrValue;
            }

            $colorValues = [];
            foreach ($colors as $color) {
                $attrValue = AttributeValue::firstOrCreate([
                    'attribute_id' => $colorAttr->id,
                    'value' => $color,
                ]);

                foreach ($languages as $lang) {
                    $attrValue->translations()->firstOrCreate([
                        'language_code' => $lang->code,
                    ], [
                        'translated_value' => $colorTranslations[$color][$lang->code] ?? $color,
                    ]);
                }
                $colorValues[$color] = $attrValue;
            }

            $vendor = Vendor::first() ?? Vendor::factory()->create();

            /* ------------------------------------------------------------
             |  Stora catalog — premium demo products
             |  Images ship with the repo in public/images/products/
             * ------------------------------------------------------------ */
            $products = [
                [
                    'name' => 'Aurora Studio Wireless Headphones',
                    'slug' => 'aurora-studio-headphones',
                    'image' => 'aurora-studio-headphones.jpg',
                    'category' => 'audio',
                    'brand' => 'aurora',
                    'price' => 349, 'discount' => 319, 'stock' => 140,
                    'sizes' => null,
                    'colors' => ['Midnight', 'Silver'],
                    'description' => 'Reference-grade wireless headphones with adaptive noise cancellation, 40 hours of battery life and plush memory-foam cushions.',
                    'es' => ['name' => 'Auriculares Inalámbricos Aurora Studio', 'description' => 'Auriculares inalámbricos de alta fidelidad con cancelación adaptativa de ruido, 40 horas de batería y almohadillas de espuma viscoelástica.'],
                    'de' => ['name' => 'Aurora Studio kabellose Kopfhörer', 'description' => 'Kabellose Referenz-Kopfhörer mit adaptiver Geräuschunterdrückung, 40 Stunden Akkulaufzeit und weichen Memory-Foam-Ohrpolstern.'],
                ],
                [
                    'name' => 'Pulse Wireless Earbuds',
                    'slug' => 'pulse-wireless-earbuds',
                    'image' => 'pulse-earbuds.jpg',
                    'category' => 'audio',
                    'brand' => 'aurora',
                    'price' => 199, 'discount' => 179, 'stock' => 220,
                    'sizes' => null,
                    'colors' => ['Midnight', 'Starlight'],
                    'description' => 'Compact true-wireless earbuds with rich, detailed sound, active noise cancellation and all-day comfort.',
                    'es' => ['name' => 'Auriculares de Oído Pulse', 'description' => 'Auriculares true wireless compactos con sonido rico y detallado, cancelación activa de ruido y comodidad para todo el día.'],
                    'de' => ['name' => 'Pulse kabellose Ohrhörer', 'description' => 'Kompakte True-Wireless-Ohrhörer mit vollem, detailliertem Klang, aktiver Geräuschunterdrückung und ganztägigem Tragekomfort.'],
                ],
                [
                    'name' => 'Orbit 360 Speaker',
                    'slug' => 'orbit-360-speaker',
                    'image' => 'orbit-speaker.jpg',
                    'category' => 'audio',
                    'brand' => 'aurora',
                    'price' => 129, 'discount' => null, 'stock' => 180,
                    'sizes' => null,
                    'colors' => ['Midnight'],
                    'description' => 'A portable speaker with room-filling 360-degree sound, deep bass and 20 hours of playtime.',
                    'es' => ['name' => 'Altavoz Orbit 360', 'description' => 'Altavoz portátil con sonido envolvente de 360 grados, graves profundos y 20 horas de reproducción.'],
                    'de' => ['name' => 'Orbit 360 Lautsprecher', 'description' => 'Tragbarer Lautsprecher mit raumfüllendem 360-Grad-Klang, satten Bässen und 20 Stunden Spielzeit.'],
                ],
                [
                    'name' => 'Lume Watch S5',
                    'slug' => 'lume-watch-s5',
                    'image' => 'lume-watch-s5.jpg',
                    'category' => 'wearables',
                    'brand' => 'lume',
                    'price' => 399, 'discount' => null, 'stock' => 160,
                    'sizes' => null,
                    'colors' => ['Space Gray', 'Silver'],
                    'description' => 'A stunning always-on display, advanced health sensors and all-day battery — the smartwatch that keeps up with you.',
                    'es' => ['name' => 'Lume Watch S5', 'description' => 'Pantalla siempre activa impresionante, sensores de salud avanzados y batería para todo el día: el reloj inteligente que va a tu ritmo.'],
                    'de' => ['name' => 'Lume Watch S5', 'description' => 'Brillantes Always-On-Display, fortschrittliche Gesundheitssensoren und Akku für den ganzen Tag — die Smartwatch, die mithält.'],
                ],
                [
                    'name' => 'Nova X5 Pro',
                    'slug' => 'nova-x5-pro',
                    'image' => 'nova-x5-pro.jpg',
                    'category' => 'smartphones',
                    'brand' => 'nova',
                    'price' => 999, 'discount' => 949, 'stock' => 90,
                    'sizes' => null,
                    'colors' => ['Midnight', 'Starlight'],
                    'description' => 'A powerhouse smartphone with a titanium frame, triple camera system and the fastest chip we have ever made.',
                    'es' => ['name' => 'Nova X5 Pro', 'description' => 'Un smartphone potente con chasis de titanio, sistema de triple cámara y el chip más rápido que hemos creado.'],
                    'de' => ['name' => 'Nova X5 Pro', 'description' => 'Ein leistungsstarkes Smartphone mit Titangehäuse, Dreifach-Kamerasystem und dem schnellsten Chip, den wir je gebaut haben.'],
                ],
                [
                    'name' => 'Nova Pad Air',
                    'slug' => 'nova-pad-air',
                    'image' => 'nova-pad-air.jpg',
                    'category' => 'tablets',
                    'brand' => 'nova',
                    'price' => 599, 'discount' => null, 'stock' => 120,
                    'sizes' => null,
                    'colors' => ['Starlight', 'Space Gray'],
                    'description' => 'Featherlight design, a breathtaking display and all-day battery — creativity wherever you go.',
                    'es' => ['name' => 'Nova Pad Air', 'description' => 'Diseño pluma, pantalla impresionante y batería para todo el día: creatividad donde quieras.'],
                    'de' => ['name' => 'Nova Pad Air', 'description' => 'Federleichtes Design, atemberaubendes Display und Akku für den ganzen Tag — Kreativität, wo immer Sie sind.'],
                ],
                [
                    'name' => 'Vanta Book 14',
                    'slug' => 'vanta-book-14',
                    'image' => 'vanta-book-14.jpg',
                    'category' => 'laptops',
                    'brand' => 'vanta',
                    'price' => 1499, 'discount' => 1399, 'stock' => 60,
                    'sizes' => null,
                    'colors' => ['Space Gray', 'Silver'],
                    'description' => 'A pro laptop with serious performance, a stunning 14-inch display and all-day battery in just 1.5 kg.',
                    'es' => ['name' => 'Vanta Book 14', 'description' => 'Un portátil profesional con rendimiento serio, pantalla de 14 pulgadas espectacular y batería para todo el día en solo 1,5 kg.'],
                    'de' => ['name' => 'Vanta Book 14', 'description' => 'Ein Pro-Laptop mit ernsthaftem Leistungsvermögen, brillantem 14-Zoll-Display und Ganztagesakku bei nur 1,5 kg.'],
                ],
                [
                    'name' => 'Vanta Key Keyboard',
                    'slug' => 'vanta-key-keyboard',
                    'image' => 'vanta-key-keyboard.jpg',
                    'category' => 'accessories',
                    'brand' => 'vanta',
                    'price' => 149, 'discount' => null, 'stock' => 200,
                    'sizes' => null,
                    'colors' => ['Starlight'],
                    'description' => 'A low-profile mechanical keyboard with hot-swappable switches, buttery tactility and a minimal aluminum frame.',
                    'es' => ['name' => 'Teclado Vanta Key', 'description' => 'Teclado mecánico de perfil bajo con interruptores intercambiables, tacto suave y marco de aluminio minimalista.'],
                    'de' => ['name' => 'Vanta Key Tastatur', 'description' => 'Mechanische Low-Profile-Tastatur mit hot-swap-fähigen Switches, butterweichem Tastgefühl und minimalistischem Aluminiumrahmen.'],
                ],
                [
                    'name' => 'Canon EOS 7D DSLR Kit',
                    'slug' => 'canon-eos-7d-kit',
                    'image' => 'canon-eos-7d.jpg',
                    'category' => 'cameras',
                    'brand' => 'canon',
                    'price' => 1099, 'discount' => 999, 'stock' => 45,
                    'sizes' => null,
                    'colors' => ['Black'],
                    'description' => 'The classic 18MP APS-C DSLR bundled with the EF-S 18-135mm zoom lens — built for action and adventure.',
                    'es' => ['name' => 'Kit Canon EOS 7D', 'description' => 'La clásica réflex DSLR de 18 MP con sensor APS-C, acompañada del zoom EF-S 18-135 mm: hecha para la acción.'],
                    'de' => ['name' => 'Canon EOS 7D Kit', 'description' => 'Der klassische 18-MP-DSLR mit APS-C-Sensor, im Set mit dem EF-S 18-135 mm Zoom — gebaut für Action.'],
                ],
                [
                    'name' => 'Cloudline Sneakers',
                    'slug' => 'cloudline-sneakers',
                    'image' => 'cloudline-sneakers.jpg',
                    'category' => 'sneakers',
                    'brand' => 'marlo',
                    'price' => 129, 'discount' => 119, 'stock' => 250,
                    'sizes' => ['Small', 'Medium', 'Large'],
                    'colors' => ['White'],
                    'description' => 'Minimalist leather sneakers in crisp white — cushioned, breathable and effortlessly wearable.',
                    'es' => ['name' => 'Zapatillas Cloudline', 'description' => 'Zapatillas minimalistas de cuero en blanco impecable: acolchadas, transpirables y fáciles de combinar.'],
                    'de' => ['name' => 'Cloudline Sneaker', 'description' => 'Minimalistische Ledersneaker in klarem Weiß — gedämpft, atmungsaktiv und mühelos tragbar.'],
                ],
                [
                    'name' => 'Voyage Leather Backpack',
                    'slug' => 'voyage-leather-backpack',
                    'image' => 'voyage-backpack.jpg',
                    'category' => 'accessories',
                    'brand' => 'northpeak',
                    'price' => 189, 'discount' => null, 'stock' => 110,
                    'sizes' => null,
                    'colors' => ['Black'],
                    'description' => 'Full-grain leather, a padded 16-inch laptop sleeve and smart storage — built for commutes and weekend trips.',
                    'es' => ['name' => 'Mochila de Cuero Voyage', 'description' => 'Cuero de flor completa, funda acolchada para portátil de 16 pulgadas y almacenamiento inteligente: hecha para el día a día.'],
                    'de' => ['name' => 'Voyage Lederrucksack', 'description' => 'Vollnarbenleder, gepolstertes 16-Zoll-Laptopfach und durchdachter Stauraum — für Pendler und Wochenendtrips.'],
                ],
                [
                    'name' => 'Meridian Sunglasses',
                    'slug' => 'meridian-sunglasses',
                    'image' => 'meridian-sunglasses.jpg',
                    'category' => 'accessories',
                    'brand' => 'marlo',
                    'price' => 159, 'discount' => 139, 'stock' => 150,
                    'sizes' => null,
                    'colors' => ['Black'],
                    'description' => 'Hand-finished acetate frames with polarized UV-400 lenses — timeless style with serious protection.',
                    'es' => ['name' => 'Gafas de Sol Meridian', 'description' => 'Monturas de acetato acabadas a mano con lentes polarizadas UV-400: estilo atemporal con protección seria.'],
                    'de' => ['name' => 'Meridian Sonnenbrille', 'description' => 'Handgefertigte Acetatfassungen mit polarisierten UV-400-Gläsern — zeitloser Stil mit echtem Schutz.'],
                ],
                [
                    'name' => 'Essential Tee',
                    'slug' => 'essential-tee',
                    'image' => 'essential-tee.jpg',
                    'category' => 't-shirts',
                    'brand' => 'marlo',
                    'price' => 39, 'discount' => 29, 'stock' => 300,
                    'sizes' => ['Small', 'Medium', 'Large'],
                    'colors' => ['Teal', 'Black'],
                    'description' => 'Heavyweight organic cotton with a clean, structured fit. The one tee your wardrobe keeps asking for.',
                    'es' => ['name' => 'Camiseta Essential', 'description' => 'Algodón orgánico de gramaje alto con un corte limpio y estructurado. La camiseta que tu armario te pide.'],
                    'de' => ['name' => 'Essential T-Shirt', 'description' => 'Schweres Bio-Baumwollshirt mit cleanem, strukturiertem Schnitt. Das eine Shirt, das dein Kleiderschrank verlangt.'],
                ],
                [
                    'name' => 'Nova Leather Case',
                    'slug' => 'nova-leather-case',
                    'image' => 'nova-leather-case.jpg',
                    'category' => 'accessories',
                    'brand' => 'nova',
                    'price' => 49, 'discount' => null, 'stock' => 260,
                    'sizes' => null,
                    'colors' => ['Black'],
                    'description' => 'Slim full-grain leather case with a soft microfiber lining — protection that ages beautifully.',
                    'es' => ['name' => 'Funda de Cuero Nova', 'description' => 'Fina funda de cuero de flor completa con forro de microfibra suave: protección que envejece con belleza.'],
                    'de' => ['name' => 'Nova Ledercase', 'description' => 'Schlanke Hülle aus Vollnarbenleder mit weichem Mikrofaserfutter — Schutz, der schön altert.'],
                ],
            ];

            foreach ($products as $item) {
                $category = Category::where('slug', $item['category'])->first() ?? Category::first();
                $brand = Brand::where('slug', $item['brand'])->first() ?? Brand::first();

                $product = Product::create([
                    'shop_id' => 1,
                    'vendor_id' => $vendor->id,
                    'slug' => $item['slug'],
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'product_type' => 'variable',
                    'status' => 1,
                ]);

                foreach ($languages as $lang) {
                    $translatedName = $item[$lang->code]['name'] ?? $item['name'];
                    $translatedDescription = $item[$lang->code]['description'] ?? $item['description'];

                    $product->translations()->create([
                        'language_code' => $lang->code,
                        'name' => $translatedName,
                        'description' => $translatedDescription,
                    ]);
                }

                // Copy the bundled product image into the public storage disk
                $source = public_path('images/products/'.$item['image']);
                $localPath = 'products/'.$item['image'];
                if (is_file($source)) {
                    Storage::disk('public')->put($localPath, file_get_contents($source));
                }

                $product->images()->create([
                    'name' => $item['name'],
                    'image_url' => $localPath,
                    'type' => 'thumb',
                ]);

                // Variants: every size x color combination (sizes optional)
                $sizeList = $item['sizes'] ?? [null];
                $colorList = $item['colors'] ?? [null];

                foreach ($sizeList as $sizeName) {
                    foreach ($colorList as $colorName) {
                        $sizeValue = $sizeName ? $sizeValues[$sizeName] : null;
                        $colorValue = $colorName ? $colorValues[$colorName] : null;

                        $variantName = trim(implode(' - ', array_filter([$sizeName, $colorName])));
                        $variantCode = strtoupper(substr($sizeName ?? $colorName, 0, 1))
                            .substr($colorName ?? $sizeName, 0, 2);

                        $variant = $product->variants()->create([
                            'variant_slug' => Str::slug($item['name'].' '.$variantName).'-'.uniqid(),
                            'price' => $item['price'],
                            'discount_price' => $item['discount'],
                            'stock' => $item['stock'],
                            'SKU' => strtoupper($variantCode).rand(100, 999),
                            'barcode' => null,
                            'weight' => '0.5',
                            'dimensions' => '10x10x2 cm',
                            'is_primary' => 1,
                        ]);

                        foreach ($languages as $lang) {
                            $variant->translations()->create([
                                'language_code' => $lang->code,
                                'name' => $variantName,
                            ]);
                        }

                        foreach (array_filter([$sizeValue, $colorValue]) as $attrValue) {
                            DB::table('product_variant_attribute_values')->insert([
                                'product_id' => $product->id,
                                'product_variant_id' => $variant->id,
                                'attribute_value_id' => $attrValue->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            ProductAttributeValue::firstOrCreate([
                                'product_id' => $product->id,
                                'attribute_value_id' => $attrValue->id,
                            ]);
                        }
                    }
                }
            }
        });
    }
}
