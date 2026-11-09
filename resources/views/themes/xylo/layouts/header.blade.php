@php
    {{--  Wishlist Count (real data only) --}}
    $wishlistCount = 0;
    if (auth('customer')->check()) {
        $wishlistCount = auth('customer')->user()->wishlistProducts()->count();
    }
    $cartCount = session('cart') ? collect(session('cart'))->sum('quantity') : 0;
@endphp

<header class="apple-globalnav-wrap" id="appleGlobalNav">
    <nav class="apple-globalnav" aria-label="{{ __('store.header.search_placeholder') ? 'Global' : 'Global' }}">
        <div class="apple-globalnav-content">

            {{-- Mobile menu toggle --}}
            <button class="apple-globalnav-menubtn d-lg-none" id="appleMenuBtn" type="button" aria-label="Menu" aria-expanded="false">
                <span class="apple-globalnav-menubtn-bar"></span>
            </button>

            {{-- Logo --}}
            <a href="{{ route('xylo.home') }}" class="apple-globalnav-logo" aria-label="Stora">
                <img src="{{ asset('images/logo.png') }}" alt="Stora"/>
            </a>

            {{-- Centered nav links (Apple style) --}}
            <ul class="apple-globalnav-list d-none d-lg-flex">
                @if ($headerMenu && $headerMenu->menuItems->count())
                    @foreach ($headerMenu->menuItems as $menuItem)
                        <li class="apple-globalnav-item">
                            <a href="{{ url($menuItem->slug) }}" class="apple-globalnav-link">
                                {{ $menuItem->translation->title ?? 'No Translation' }}
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>

            {{-- Actions: search · language · currency · wishlist · account · bag --}}
            <div class="apple-globalnav-actions">

                <button class="apple-globalnav-action" id="appleSearchBtn" type="button" aria-label="{{ __('store.header.search_placeholder') }}">
                    <i class="fa fa-search"></i>
                </button>

                {{-- Language (desktop) --}}
                <form action="{{ route('change.store.language') }}" method="POST" class="d-none d-md-inline-block">
                    @csrf
                    <select name="lang" class="apple-mini-select" onchange="this.form.submit()" aria-label="Language">
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
                        <option value="fr" {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>FR</option>
                        <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>ES</option>
                        <option value="de" {{ app()->getLocale() == 'de' ? 'selected' : '' }}>DE</option>
                    </select>
                </form>

                {{-- Currency (desktop) --}}
                <form action="{{ route('change.currency') }}" method="POST" class="d-none d-lg-inline-block">
                    @csrf
                    <select name="currency_code" class="apple-mini-select" onchange="this.form.submit()" aria-label="Currency">
                        @foreach (\App\Models\Currency::all() as $currency)
                            <option value="{{ $currency->code }}" {{ session('currency', 'USD') == $currency->code ? 'selected' : '' }}>
                                {{ strtoupper($currency->code) }}
                            </option>
                        @endforeach
                    </select>
                </form>

                {{-- Wishlist --}}
                <a href="{{ auth('customer')->check() ? route('customer.wishlist.index') : route('customer.login') }}"
                   class="apple-globalnav-action apple-globalnav-bag" aria-label="{{ __('store.footer.wishlist') }}">
                    <i class="fa-regular fa-heart"></i>
                    @if ($wishlistCount > 0)
                        <span class="apple-nav-badge">{{ $wishlistCount }}</span>
                    @endif
                </a>

                {{-- Account --}}
                <a href="#" class="apple-globalnav-action dropdown-toggle apple-globalnav-account" data-bs-toggle="dropdown" aria-label="{{ __('store.footer.my_account') }}">
                    @auth('customer')
                        @php $customer = Auth::guard('customer')->user(); @endphp
                        @if($customer->profile_image)
                            <img src="{{ asset('storage/' . $customer->profile_image) }}" alt="Profile" class="apple-nav-avatar">
                        @else
                            <i class="fa-regular fa-user"></i>
                        @endif
                    @else
                        <i class="fa-regular fa-user"></i>
                    @endauth
                </a>
                <ul class="dropdown-menu dropdown-menu-end apple-account-menu">
                    @guest('customer')
                        <li><a class="dropdown-item" href="{{ route('customer.login') }}">Sign In</a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.register') }}">Sign Up</a></li>
                    @else
                        <li><a class="dropdown-item" href="{{ route('customer.profile.edit') }}">My Profile</a></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('customer.logout') }}"
                               onclick="event.preventDefault(); document.getElementById('customer-logout-form').submit();">
                                Logout
                            </a>
                            <form id="customer-logout-form" action="{{ route('customer.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @endguest
                </ul>

                {{-- Bag / Cart --}}
                <a href="{{ route('cart.view') }}" class="apple-globalnav-action apple-globalnav-bag" aria-label="Bag">
                    <i class="fa fa-shopping-bag"></i>
                    <span id="cart-count" class="apple-nav-badge {{ $cartCount > 0 ? '' : 'd-none' }}">{{ $cartCount }}</span>
                </a>
            </div>
        </div>
    </nav>

    {{-- Search panel (Apple style, slides under the nav) --}}
    <div class="apple-searchpanel" id="appleSearchPanel">
        <div class="apple-searchpanel-inner">
            <form action="{{ url('/search') }}" method="GET" class="apple-searchform" autocomplete="off">
                <i class="fa fa-search apple-searchform-icon"></i>
                <input type="text" id="search-input" name="q" placeholder="{{ __('store.header.search_placeholder') }}">
                <div id="search-suggestions" class="apple-searchsuggestions d-none"></div>
            </form>
        </div>
    </div>

    {{-- Mobile menu panel (Apple style frosted dropdown) --}}
    <div class="apple-mobilemenu" id="appleMobileMenu">
        <ul class="apple-mobilemenu-list">
            @if ($headerMenu && $headerMenu->menuItems->count())
                @foreach ($headerMenu->menuItems as $menuItem)
                    <li>
                        <a href="{{ url($menuItem->slug) }}" class="apple-mobilemenu-link">
                            {{ $menuItem->translation->title ?? 'No Translation' }}
                        </a>
                    </li>
                @endforeach
            @endif
            <li class="apple-mobilemenu-divider"></li>
            <li class="apple-mobilemenu-extras">
                <form action="{{ route('change.store.language') }}" method="POST" class="d-flex align-items-center gap-2">
                    @csrf
                    <select name="lang" class="apple-mini-select w-auto" onchange="this.form.submit()" aria-label="Language">
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
                        <option value="fr" {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>FR</option>
                        <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>ES</option>
                        <option value="de" {{ app()->getLocale() == 'de' ? 'selected' : '' }}>DE</option>
                    </select>
                </form>
                <form action="{{ route('change.currency') }}" method="POST" class="d-flex align-items-center gap-2">
                    @csrf
                    <select name="currency_code" class="apple-mini-select w-auto" onchange="this.form.submit()" aria-label="Currency">
                        @foreach (\App\Models\Currency::all() as $currency)
                            <option value="{{ $currency->code }}" {{ session('currency', 'USD') == $currency->code ? 'selected' : '' }}>
                                {{ strtoupper($currency->code) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </li>
        </ul>
    </div>
</header>

{{-- Promo strip (Apple-style ribbon under the nav) --}}
<div class="apple-promo">
    <p>{{ __('store.header.top_bar_message') }}</p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var nav = document.getElementById('appleGlobalNav');
        var searchBtn = document.getElementById('appleSearchBtn');
        var searchPanel = document.getElementById('appleSearchPanel');
        var menuBtn = document.getElementById('appleMenuBtn');
        var mobileMenu = document.getElementById('appleMobileMenu');

        function closeAll() {
            searchPanel.classList.remove('open');
            nav.classList.remove('search-open');
            mobileMenu.classList.remove('open');
            menuBtn.classList.remove('active');
            menuBtn.setAttribute('aria-expanded', 'false');
        }

        if (searchBtn && searchPanel) {
            searchBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = searchPanel.classList.contains('open');
                closeAll();
                if (!isOpen) {
                    searchPanel.classList.add('open');
                    nav.classList.add('search-open');
                    var input = searchPanel.querySelector('#search-input');
                    if (input) input.focus();
                }
            });
        }

        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = mobileMenu.classList.contains('open');
                closeAll();
                if (!isOpen) {
                    mobileMenu.classList.add('open');
                    menuBtn.classList.add('active');
                    menuBtn.setAttribute('aria-expanded', 'true');
                }
            });
        }

        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target)) closeAll();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAll();
        });

        var mobileLinks = mobileMenu ? mobileMenu.querySelectorAll('a') : [];
        mobileLinks.forEach(function (link) {
            link.addEventListener('click', closeAll);
        });
    });
</script>
