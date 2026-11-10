@php
    // Wishlist Count (real data only)
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

                <button class="apple-globalnav-action apple-globalnav-searchbtn" id="appleSearchBtn" type="button"
                        aria-label="{{ __('store.header.search_placeholder') }}" aria-expanded="false" aria-controls="appleNavSearch">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 13.7 15 15" width="15" height="15" aria-hidden="true" focusable="false"><path fill="currentColor" d="M14.298,27.202l-3.87-3.87c0.701-0.929,1.122-2.081,1.122-3.332c0-3.06-2.489-5.55-5.55-5.55c-3.06,0-5.55,2.49-5.55,5.55 c0,3.061,2.49,5.55,5.55,5.55c1.251,0,2.403-0.421,3.332-1.122l3.87,3.87c0.151,0.151,0.35,0.228,0.548,0.228 s0.396-0.076,0.548-0.228C14.601,27.995,14.601,27.505,14.298,27.202z M1.55,20c0-2.454,1.997-4.45,4.45-4.45 c2.454,0,4.45,1.997,4.45,4.45S8.454,24.45,6,24.45C3.546,24.45,1.55,22.454,1.55,20z"></path></svg>
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

            {{-- Apple-style expanding search field (grows from the icon, covers the nav) --}}
            <div class="apple-nav-search" id="appleNavSearch">
                <form action="{{ url('/search') }}" method="GET" class="apple-searchform" autocomplete="off" role="search">
                    <svg class="apple-searchform-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 13.7 15 15" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M14.298,27.202l-3.87-3.87c0.701-0.929,1.122-2.081,1.122-3.332c0-3.06-2.489-5.55-5.55-5.55c-3.06,0-5.55,2.49-5.55,5.55 c0,3.061,2.49,5.55,5.55,5.55c1.251,0,2.403-0.421,3.332-1.122l3.87,3.87c0.151,0.151,0.35,0.228,0.548,0.228 s0.396-0.076,0.548-0.228C14.601,27.995,14.601,27.505,14.298,27.202z M1.55,20c0-2.454,1.997-4.45,4.45-4.45 c2.454,0,4.45,1.997,4.45,4.45S8.454,24.45,6,24.45C3.546,24.45,1.55,22.454,1.55,20z"></path></svg>
                    <input type="text" id="search-input" name="q" placeholder="{{ __('store.header.search_placeholder') }}" aria-label="{{ __('store.header.search_placeholder') }}" autocomplete="off" spellcheck="false">
                </form>
                <button type="button" class="apple-searchform-cancel d-lg-none" id="appleSearchCancel">{{ __('store.header.search_cancel') }}</button>
            </div>
        </div>
    </nav>

    {{-- Search results flyout (frosted, hangs under the nav like apple.com) --}}
    <div class="apple-search-drop" id="appleSearchDrop">
        <div class="apple-search-drop-inner">
            <div class="apple-search-drop-box">
                <div class="apple-search-quicklinks" id="searchQuicklinks">
                    <p class="s-ql-label">{{ __('store.header.search_quick_links') }}</p>
                    <ul>
                        @if ($headerMenu && $headerMenu->menuItems->count())
                            @foreach ($headerMenu->menuItems as $menuItem)
                                <li>
                                    <a href="{{ url($menuItem->slug) }}" class="s-ql-link">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="7" height="12" viewBox="0 0 8 14" fill="none" aria-hidden="true" focusable="false"><path d="M1.5 1.5L6.5 7L1.5 12.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <span>{{ $menuItem->translation->title ?? 'No Translation' }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <div id="search-suggestions" class="apple-searchsuggestions d-none"></div>
            </div>
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
        var drop = document.getElementById('appleSearchDrop');
        var input = document.getElementById('search-input');
        var cancelBtn = document.getElementById('appleSearchCancel');
        var quicklinks = document.getElementById('searchQuicklinks');
        var suggestions = document.getElementById('search-suggestions');
        var menuBtn = document.getElementById('appleMenuBtn');
        var mobileMenu = document.getElementById('appleMobileMenu');

        function closeSearch() {
            nav.classList.remove('search-open');
            if (drop) drop.classList.remove('open');
            if (searchBtn) searchBtn.setAttribute('aria-expanded', 'false');
        }

        function closeAll() {
            closeSearch();
            if (mobileMenu) mobileMenu.classList.remove('open');
            if (menuBtn) {
                menuBtn.classList.remove('active');
                menuBtn.setAttribute('aria-expanded', 'false');
            }
        }

        function openSearch() {
            closeAll();
            /* Apple behaviour: fresh field + Quick Links every time it opens */
            if (input) {
                input.value = '';
                try { input.setSelectionRange(0, 0); } catch (e) {}
            }
            if (suggestions) {
                suggestions.classList.add('d-none');
                suggestions.innerHTML = '';
            }
            if (quicklinks) quicklinks.classList.remove('d-none');
            nav.classList.add('search-open');
            if (drop) drop.classList.add('open');
            if (searchBtn) searchBtn.setAttribute('aria-expanded', 'true');
            setTimeout(function () { if (input) input.focus(); }, 80);
        }

        if (searchBtn) {
            searchBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (nav.classList.contains('search-open')) { closeSearch(); }
                else { openSearch(); }
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                closeSearch();
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

        var qlLinks = quicklinks ? quicklinks.querySelectorAll('a') : [];
        qlLinks.forEach(function (link) {
            link.addEventListener('click', closeAll);
        });
    });
</script>
