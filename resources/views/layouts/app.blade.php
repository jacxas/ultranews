<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'UltraNews') — Noticias de videos virales</title>
    <meta name="description" content="@yield('meta_description', 'Las noticias de los videos más virales de YouTube, TikTok, Instagram y X. Tendencias, memes y fenómenos de internet.')">
    <link rel="stylesheet" href="{{ asset('css/ultranews.css') }}">
    @stack('head')
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="{{ route('home') }}">⚡ Ultra<span>News</span></a>

        <nav class="main-nav" aria-label="Navegación principal">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a>
            <a href="{{ route('trending') }}" class="{{ request()->routeIs('trending') ? 'active' : '' }}">🔥 Tendencias</a>
            @foreach(($navCategories ?? collect())->take(4) as $navCategory)
                <a href="{{ route('categories.show', $navCategory) }}"
                   class="{{ request()->routeIs('categories.show') && request()->route('category')?->slug === $navCategory->slug ? 'active' : '' }}">
                    {{ $navCategory->name }}
                </a>
            @endforeach
        </nav>

        <form class="search-form" action="{{ route('search') }}" method="GET" role="search">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar noticias virales..." aria-label="Buscar">
            <button type="submit">Buscar</button>
        </form>

        <div class="header-actions">
            @auth
                @if(auth()->user()->isAdmin())
                    <a class="btn btn-small btn-neon" href="{{ route('admin.dashboard') }}">Panel</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="link-like">Salir</button>
                </form>
            @else
                <a class="btn btn-small btn-ghost" href="{{ route('login') }}">Ingresar</a>
            @endauth
        </div>
    </div>
</header>

<main class="container">
    @include('partials.flash')
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <div>
            ⚡ <strong>UltraNews</strong> — Noticias de videos virales de las redes sociales.
            <br>Construido con Laravel. © {{ date('Y') }} jacxas.
        </div>
        <div class="footer-links">
            <a href="{{ route('trending') }}">Tendencias</a>
            <a href="{{ route('search') }}">Buscar</a>
            <a href="{{ url('/api/articles') }}">API</a>
            <a href="{{ route('login') }}">Admin</a>
        </div>
    </div>
</footer>
</body>
</html>
