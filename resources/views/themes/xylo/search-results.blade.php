@extends('themes.xylo.layouts.master')
@section('css')
@endsection
@section('content')
    @php $currency = activeCurrency(); @endphp
    <section class="search-page py-5">
        <div class="container">
            <header class="search-page-head mb-5">
                <h1 class="sec-heading">
                    {{ __('store.home.search_results_title', ['query' => $query]) }}
                </h1>
                @if ($products->count() > 0)
                    <p class="search-page-count">
                        {{ $products->total() }} {{ __('store.home.search_results_count') }}
                    </p>
                @endif
            </header>

            @if ($products->count() > 0)
                <div class="row">
                    @foreach ($products as $product)
                        <div class="col-md-3">
                            <div class="product-card">
                                <div class="product-img">
                                    <a href="{{ route('product.show', $product->slug) }}">
                                        <img src="{{ Storage::url(optional($product->thumbnail)->image_url ?? 'default.jpg') }}"
                                            alt="{{ $product->translation->name ?? 'Product Name Not Available' }}"
                                            loading="lazy" decoding="async">
                                    </a>
                                    <button class="wishlist-btn" data-product-id="{{ $product->id }}">
                                        <i class="fa-solid fa-heart"></i>
                                    </button>
                                </div>
                                <div class="product-info mt-4">
                                    <div class="top-info">
                                        <div class="reviews">
                                            <i class="fa-solid fa-star"></i> ({{ $product->reviews_count }} {{ __('store.home.reviews') }})
                                        </div>
                                    </div>
                                    <div class="bottom-info">
                                        <div class="left">
                                            <h3>
                                                <a href="{{ route('product.show', $product->slug) }}" class="product-title">
                                                    {{ $product->translation->name ?? 'Product Name Not Available' }}
                                                </a>
                                            </h3>
                                            <p class="price">
                                                <span class="original {{ optional($product->primaryVariant)->converted_discount_price ? 'has-discount' : '' }}">
                                                    {{ $currency->symbol }}{{ optional($product->primaryVariant)->converted_price ?? 'N/A' }}
                                                </span>

                                                @if (optional($product->primaryVariant)->converted_discount_price)
                                                    <span class="discount">
                                                        {{ $currency->symbol }}{{ $product->primaryVariant->converted_discount_price }}
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                        <button class="cart-btn" onclick="addToCart({{ $product->id }})">
                                            <i class="fa fa-shopping-bag"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <nav class="search-pagination mt-4">
                    {{ $products->links() }}
                </nav>
            @else
                <div class="search-empty">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <h2>{{ __('store.home.search_empty_title') }}</h2>
                    <p>{{ __('store.home.search_empty_text', ['query' => $query]) }}</p>
                    <a href="{{ route('shop.index') }}" class="read-more pe-4 ps-4">
                        {{ __('store.home.search_empty_cta') }}
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
@section('js')
<script>
    function addToCart(productId) {
        storaApi.addToCart(productId, { quantity: 1 });
    }
</script>
@endsection
