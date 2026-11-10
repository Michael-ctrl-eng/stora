<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    /**
     * Instant-search suggestions for the header search field.
     *
     * Returns compact JSON rows (thumbnail, name, effective price) that the
     * global nav renders as a dropdown while the customer types.
     */
    public function suggestions(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        $locale = App::getLocale();

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::whereHas('translations', function ($q) use ($query, $locale) {
            $q->where('name', 'like', "%{$query}%")->where('language_code', $locale);
        })
            ->with([
                'translations' => function ($q) use ($locale) {
                    $q->where('language_code', $locale)->select('product_id', 'name');
                },
                'thumbnail',
                'primaryVariant',
            ])
            ->limit(6)
            ->get(['id', 'slug']);

        $products = $products->map(function ($product) {
            $variant = $product->primaryVariant;
            $regular = optional($variant)->converted_price;
            $discount = optional($variant)->converted_discount_price;

            return [
                'id' => $product->id,
                'slug' => $product->slug,
                'thumbnail' => $product->thumbnail
                    ? Storage::url($product->thumbnail->image_url)
                    : asset('default-thumbnail.jpg'),
                'name' => $product->translations->first()->name ?? null,
                // Effective price first; original kept for a struck-through pair.
                'price' => $discount ?? $regular,
                'original_price' => $discount ? $regular : null,
            ];
        });

        return response()->json($products);
    }

    /**
     * Full search results page.
     *
     * NOTE: product translations are keyed by the language_code column —
     * querying a non-existent "locale" column made SQLite silently match
     * zero rows (double-quoted string misfeature), so every search came
     * back empty.
     */
    public function searchResults(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        $locale = App::getLocale();

        $products = Product::whereHas('translations', function ($q) use ($query, $locale) {
            $q->where('name', 'like', "%{$query}%")->where('language_code', $locale);
        })
            ->with([
                'translations' => function ($q) use ($locale) {
                    $q->where('language_code', $locale)->select('product_id', 'name', 'description');
                },
                'thumbnail',
                'primaryVariant',
            ])
            ->withCount('reviews')
            ->paginate(12);

        return view('search-results', compact('products', 'query'));
    }
}
