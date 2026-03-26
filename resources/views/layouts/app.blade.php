<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartShop') — Smart Coupon Shop</title>
    <meta name="description" content="SmartShop — a smart coupon and order simulator with real-time pricing.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-surface min-h-screen font-inter text-on-surface">

    {{-- ──────────────────────────────────────────────────────── --}}
    {{-- NAVBAR --}}
    {{-- ──────────────────────────────────────────────────────── --}}
    <nav class="navbar" id="main-navbar">
        <div class="navbar-inner">

            {{-- Logo --}}
            <a href="{{ route('products.index') }}" class="navbar-logo">
                <div class="logo-icon">S</div>
                <span class="logo-text">SmartShop</span>
            </a>

            {{-- Desktop Right Controls --}}
            <div class="navbar-right">
                @auth
                    <div class="nav-user">
                        <svg class="nav-user-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="nav-username">{{ auth()->user()->name }}</span>
                        @if(auth()->user()->is_premium)
                            <span class="premium-badge">★ Premium</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="nav-link">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                    <a href="{{ route('register') }}" class="nav-link-accent">Register</a>
                @endauth

                {{-- Cart Button --}}
                <a href="{{ route('cart.index') }}" class="cart-btn" id="cart-btn-desktop">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>Cart</span>
                    <span class="cart-badge" id="cart-count">{{ app(\App\Services\CartService::class)->count() }}</span>
                </a>
            </div>

            {{-- Mobile: Cart + Hamburger --}}
            <div class="mobile-actions">
                <a href="{{ route('cart.index') }}" class="cart-btn-mobile">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="cart-badge-mobile"
                        id="cart-count-mobile">{{ app(\App\Services\CartService::class)->count() }}</span>
                </a>
                <button onclick="toggleMobileMenu()" class="hamburger" id="hamburger" aria-label="Menu">
                    <span class="ham-line"></span>
                    <span class="ham-line"></span>
                    <span class="ham-line"></span>
                </button>
            </div>

        </div>

        {{-- Mobile Drop-down Menu --}}
        <div class="mobile-menu hidden" id="mobile-menu">
            @auth
                <div class="mob-user">
                    <span class="mob-username">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->is_premium)
                        <span class="premium-badge">★ Premium</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mob-link w-full text-left">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mob-link">Login</a>
                <a href="{{ route('register') }}" class="mob-link">Register</a>
            @endauth
            <a href="{{ route('products.index') }}" class="mob-link">All Products</a>
        </div>
    </nav>

    {{-- ──────────────────────────────────────────────────────── --}}
    {{-- FLASH MESSAGES --}}
    {{-- ──────────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="flash flash-success" id="flash-success" role="alert">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flash flash-error" id="flash-error" role="alert">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M12 5a7 7 0 100 14 7 7 0 000-14z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ──────────────────────────────────────────────────────── --}}
    {{-- MAIN CONTENT --}}
    {{-- ──────────────────────────────────────────────────────── --}}
    <main class="site-main">
        @yield('content')
    </main>

    {{-- ──────────────────────────────────────────────────────── --}}
    {{-- FOOTER --}}
    {{-- ──────────────────────────────────────────────────────── --}}
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-logo">
                <div class="logo-icon logo-icon-sm">S</div>
                <span class="text-slate-600 text-sm font-medium">SmartShop</span>
            </div>
            <p class="footer-copy">© {{ date('Y') }} SmartShop — Smart Coupon &amp; Order Simulator</p>
            <div class="footer-tags">
                <span>Darsh Patel</span>
            </div>
        </div>
    </footer>

    @stack('scripts')

    <script>
        // Auto-dismiss flash messages
        setTimeout(() => {
            document.getElementById('flash-success')?.remove();
            document.getElementById('flash-error')?.remove();
        }, 5000);

        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const ham = document.getElementById('hamburger');
            menu.classList.toggle('hidden');
            ham.classList.toggle('open');
        }

        // Sync both cart count badges
        function updateCartCount(n) {
            const badges = document.querySelectorAll('#cart-count, #cart-count-mobile');
            badges.forEach(b => { if (b) b.textContent = n; });
        }
    </script>
</body>

</html>