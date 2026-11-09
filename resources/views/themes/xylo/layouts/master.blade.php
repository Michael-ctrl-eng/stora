<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <title>{{ config('app.name', 'Stora') }}</title>

    {{-- ===== Critical inline CSS: instant first paint, never a blank page ===== --}}
    <style>
        :root { --stora-blue: #0071e3; --stora-ink: #1d1d1f; --stora-gray: #6e6e73; --stora-bg: #f5f5f7; }
        html { -webkit-text-size-adjust: 100%; }
        body { margin: 0; background: #fff; color: #1d1d1f; font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'SF Pro Display', 'Helvetica Neue', sans-serif; }
        /* Sticky glass nav shell — real styles take over once apple.css arrives */
        .apple-globalnav-wrap { position: sticky; top: 0; z-index: 1030; height: 48px; background: rgba(255,255,255,.78); border-bottom: 1px solid rgba(0,0,0,.08); }
        .apple-globalnav { height: 48px; display: flex; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 22px; opacity: .97; }
        .apple-globalnav-logo img { height: 26px; display: block; }
        /* Hero shell so the top of the page is never empty */
        .stora-hero-shell { min-height: 300px; background: linear-gradient(180deg, #f5f5f7 0%, #fff 100%); }
        /* Shimmer skeleton for grids that are still painting */
        .stora-skeleton { background: linear-gradient(100deg, #ececf0 40%, #f7f7fa 50%, #ececf0 60%); background-size: 200% 100%; animation: stora-shimmer 1.4s linear infinite; border-radius: 18px; }
        @keyframes stora-shimmer { to { background-position: -200% 0; } }
        @media (prefers-reduced-motion: reduce) { .stora-skeleton { animation: none; } }
        /* Fail-safe: if the async CSS never arrives, content stays visible */
    </style>

    {{-- ===== Self-hosted fonts & vendor CSS (zero external origins) ===== --}}
    <link rel="preload" href="{{ asset('vendor/fonts/inter-var-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('vendor/css/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/css/bootstrap.min.css') }}">
    @if (!App::environment('testing'))
        @vite(['resources/views/themes/xylo/sass/app.scss'])
    @endif
    @if (!App::environment('testing'))
        @vite(['resources/views/themes/xylo/css/animate.min.css'])
    @endif
    @if (!App::environment('testing'))
        @vite(['resources/views/themes/xylo/css/slick.css'])
    @endif
    <link rel="stylesheet" href="{{ asset('vendor/css/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}">
    @if (!App::environment('testing'))
        @vite(['resources/views/themes/xylo/css/style.css'])
    @endif
    @if (!App::environment('testing'))
        @vite(['resources/views/themes/xylo/css/custom.css'])
    @endif
    {{-- Apple-compatible design layer : must load after custom.css --}}
    @if (!App::environment('testing'))
        @vite(['resources/views/themes/xylo/css/apple.css'])
    @endif
    @yield('css')
    <link rel="stylesheet" href="{{ asset('vendor/css/toastr.min.css') }}">
</head>
<body class="apple-theme">
    @include('themes.xylo.layouts.header')

    {{-- ===== Apple-style "Added to Bag" panel =====
         Opens under the bag icon whenever an item is added to the cart.
         Data comes from the /cart/add JSON response (added { name, image,
         price, quantity, in_stock }) — nothing is faked client-side. --}}
    <div class="stora-bag-pop" id="storaBagPop" role="dialog" aria-modal="false" aria-labelledby="storaBagPopTitle" aria-hidden="true">
        <div class="stora-bag-pop-card">
            <button type="button" class="stora-bag-pop-close" id="storaBagPopClose" aria-label="Close">&times;</button>
            <div class="stora-bag-pop-head">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                <span id="storaBagPopTitle">{{ __('store.cart.added_to_bag') }}</span>
            </div>
            <div class="stora-bag-pop-item">
                <img id="storaBagPopImg" src="" alt="" width="72" height="72" decoding="async">
                <div class="stora-bag-pop-meta">
                    <div class="stora-bag-pop-name" id="storaBagPopName"></div>
                    <div class="stora-bag-pop-price"><span id="storaBagPopPrice"></span><span class="stora-bag-pop-qty" id="storaBagPopQty"></span></div>
                    <div class="stora-bag-pop-stock" id="storaBagPopStock">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <span>{{ __('store.product_detail.in_stock') }}</span>
                    </div>
                </div>
            </div>
            <div class="stora-bag-pop-actions">
                <a href="{{ route('cart.view') }}" class="stora-bag-pop-review">{{ __('store.cart.review_bag') }}</a>
                <a href="{{ route('checkout.index') }}" class="stora-bag-pop-checkout">{{ __('store.cart.check_out') }}</a>
            </div>
        </div>
    </div>

    @yield('content')
    @include('themes.xylo.layouts.footer')

    {{-- ===== Stora storefront API helper =====
         Every AJAX action goes through storaPost():
         - always same-origin relative URLs (works behind any domain/alias)
         - Accept: application/json so Laravel returns JSON errors, never HTML
         - one code path for CSRF, toasts, cart badge and error recovery --}}
    <script>
        window.storaApi = (function () {
            function csrf() {
                var m = document.querySelector('meta[name="csrf-token"]');
                return m ? m.content : '';
            }
            function toast(kind, msg) {
                if (window.toastr) {
                    toastr[kind](msg, '', { closeButton: true, progressBar: true, timeOut: 4200, positionClass: 'toast-top-right' });
                } else {
                    console.log('[' + kind + '] ' + msg);
                }
            }
            function updateCartBadge(count) {
                var badge = document.getElementById('cart-count');
                if (badge && typeof count === 'number' && isFinite(count)) {
                    badge.textContent = count;
                    badge.classList.toggle('d-none', count === 0);
                }
            }
            /**
             * storaPost('/cart/add', { product_id: 1, quantity: 1 })
             *   -> Promise resolving with parsed JSON; never rejects silently.
             */
            function storaPost(url, body) {
                return fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf()
                    },
                    body: JSON.stringify(body || {})
                }).then(function (r) {
                    return r.text().then(function (text) {
                        var data = {};
                        try { data = text ? JSON.parse(text) : {}; } catch (e) { /* HTML error page */ }
                        if (!r.ok) {
                            var err = new Error(data.message || ('Request failed (' + r.status + ')'));
                            err.status = r.status;
                            err.data = data;
                            throw err;
                        }
                        return data;
                    });
                });
            }
            /**
             * Apple-style "Added to Bag" panel.
             * data = { message, cart_count, added: { name, image, price, quantity, in_stock } }
             */
            var bagPopTimer = null;
            function showBagPanel(data) {
                var pop = document.getElementById('storaBagPop');
                if (!pop) { toast('success', data.message || 'Added to Bag'); return; }
                var added = data.added || {};
                var img = document.getElementById('storaBagPopImg');
                if (img) {
                    if (added.image) { img.src = added.image; img.alt = added.name || ''; img.style.display = ''; }
                    else { img.removeAttribute('src'); img.style.display = 'none'; }
                }
                document.getElementById('storaBagPopName').textContent = added.name || '';
                document.getElementById('storaBagPopPrice').textContent = added.price || '';
                var qtyEl = document.getElementById('storaBagPopQty');
                if (qtyEl) { qtyEl.textContent = added.quantity ? ('Qty: ' + added.quantity) : ''; }
                var stock = document.getElementById('storaBagPopStock');
                if (stock) { stock.style.display = added.in_stock === false ? 'none' : ''; }
                // restart the open animation
                pop.classList.remove('open');
                void pop.offsetWidth;
                pop.classList.add('open');
                pop.setAttribute('aria-hidden', 'false');
                if (bagPopTimer) clearTimeout(bagPopTimer);
                bagPopTimer = setTimeout(hideBagPanel, 6500);
            }
            function hideBagPanel() {
                var pop = document.getElementById('storaBagPop');
                if (!pop) return;
                pop.classList.remove('open');
                pop.setAttribute('aria-hidden', 'true');
                if (bagPopTimer) { clearTimeout(bagPopTimer); bagPopTimer = null; }
            }
            document.addEventListener('click', function (e) {
                var pop = document.getElementById('storaBagPop');
                if (pop && pop.classList.contains('open') && !pop.contains(e.target)) hideBagPanel();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') hideBagPanel();
            });
            document.addEventListener('DOMContentLoaded', function () {
                var closeBtn = document.getElementById('storaBagPopClose');
                if (closeBtn) closeBtn.addEventListener('click', hideBagPanel);
            });

            /** Shared "add to cart" UX used by listing pages. */
            function addToCart(productId, opts) {
                opts = opts || {};
                var btn = (typeof opts.button === 'object' && opts.button) ? opts.button : null;
                var original = btn ? btn.innerHTML : null;
                if (btn) { btn.disabled = true; btn.classList.add('is-adding'); }
                return storaPost('/cart/add', {
                    product_id: productId,
                    quantity: opts.quantity || 1,
                    attribute_value_ids: opts.attribute_value_ids || []
                }).then(function (data) {
                    if (data.added && data.added.name) {
                        showBagPanel(data);
                    } else {
                        toast('success', data.message || 'Added to Bag');
                    }
                    updateCartBadge(data.cart_count);
                    if (typeof opts.onSuccess === 'function') opts.onSuccess(data);
                }).catch(function (err) {
                    if (err.status === 419) {
                        toast('warning', 'Your session expired — refreshing for a fresh one…');
                        setTimeout(function () { window.location.reload(); }, 900);
                    } else if (err.status === 401) {
                        toast('warning', 'Please sign in first.');
                    } else if (err.status === 422) {
                        toast('warning', err.message || 'That option is not available right now.');
                    } else {
                        toast('error', err.message || 'Could not reach the store. Please try again.');
                    }
                    if (typeof opts.onError === 'function') opts.onError(err);
                }).finally(function () {
                    if (btn) { btn.disabled = false; btn.classList.remove('is-adding'); if (original) btn.innerHTML = original; }
                });
            }
            return { post: storaPost, addToCart: addToCart, toast: toast, updateCartBadge: updateCartBadge, csrf: csrf, showBagPanel: showBagPanel, hideBagPanel: hideBagPanel };
        })();
    </script>

    @if (!App::environment('testing'))
        @vite(['resources/views/themes/xylo/js/app.js'])
    @endif
    <script src="{{ asset('vendor/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/js/bootstrap.bundle.min.js') }}"></script>
    {{-- slick as a classic script: it must register on the global jQuery
         synchronously, before the inline carousel initialisation below --}}
    <script src="{{ asset('js/slick.min.js') }}"></script>
    @if (!App::environment('testing'))
        @vite(['resources/views/themes/xylo/js/main.js'])
    @endif
    <script src="{{ asset('vendor/js/toastr.min.js') }}"></script>
    @yield('js')
    <script>
        $(document).ready(function () {
            if (!$('.category-slider').length && !$('.banner-slider').length && !$('.product-slider').length) {
                return; // no carousels on this page
            }
            $('.category-slider').slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000,
                dots: false,
                arrows: true,
                prevArrow: '<button class="slick-prev"><i class="fa fa-angle-left"></i></button>',
                nextArrow: '<button class="slick-next"><i class="fa fa-angle-right"></i></button>',
                responsive: [
                    { breakpoint: 1024, settings: { slidesToShow: 3 } },
                    { breakpoint: 768, settings: { slidesToShow: 1 } },
                    { breakpoint: 480, settings: { slidesToShow: 1 } }
                ]
            });
            $('.banner-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                fade: true,
                speed: 500,
                cssEase: 'linear',
                autoplaySpeed: 5000,
                dots: true,
                arrows: false,
            });
            $('.product-slider').slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                infinite: true,
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: true,
                prevArrow: $('.prev'),
                nextArrow: $('.next'),
                responsive: [
                    { breakpoint: 1024, settings: { slidesToShow: 3 } },
                    { breakpoint: 768, settings: { slidesToShow: 2 } },
                    { breakpoint: 480, settings: { slidesToShow: 1 } }
                ]
            });
        });
    </script>
    <script>
        /* header script */
        document.addEventListener('DOMContentLoaded', function() {
            const accountToggle = document.getElementById('accountDropdown');
            const accountMenu = document.querySelector('.account-menu');

            if (accountToggle && accountMenu) {
                document.addEventListener('click', function(event) {
                    if (!accountToggle.contains(event.target) && !accountMenu.contains(event.target)) {
                        accountMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>
    <script>
        /* product seach input */
        $(document).ready(function () {
            $('#search-input').on('keyup', function () {
                let query = $(this).val();
                if (query.length > 2) {
                    $.ajax({
                        url: '/search-suggestions',
                        type: 'GET',
                        data: { q: query },
                        success: function (data) {
                            let suggestions = $('#search-suggestions');
                            suggestions.html('');
                            if (data.length > 0) {
                                data.forEach(product => {
                                    suggestions.append(`
                                        <a href="/product/${product.slug}" class="dropdown-item d-flex align-items-center">
                                            <img src="${product.thumbnail}" alt="${product.name}" class="me-2" width="40" height="40" style="object-fit: cover; border-radius: 5px;" loading="lazy">
                                            <span class="search-product-title">${product.name}</span>
                                        </a>
                                    `);
                                });
                                suggestions.removeClass('d-none');
                            } else {
                                suggestions.addClass('d-none');
                            }
                        }
                    });
                } else {
                    $('#search-suggestions').addClass('d-none');
                }
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#search-input, #search-suggestions').length) {
                    $('#search-suggestions').addClass('d-none');
                }
            });
        });


    </script>
</body>
</html>
