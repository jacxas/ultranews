<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') — UltraNews Admin</title>
    <link rel="stylesheet" href="{{ asset('css/ultranews.css') }}">
</head>
<body>
<header class="admin-header">
    <div class="container header-inner">
        <a class="logo" href="{{ route('admin.dashboard') }}">⚡ Ultra<span>News</span></a>
        <span class="admin-tagline">Admin</span>

        <nav class="main-nav" aria-label="Navegación del panel">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.articles.index') }}" class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">Noticias</a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Categorías</a>
        </nav>

        <div class="header-actions" style="margin-left:auto">
            <a class="btn btn-small btn-ghost" href="{{ route('home') }}" target="_blank">Ver sitio ↗</a>
            <span style="color:var(--text-faint);font-size:.85rem">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="link-like">Salir</button>
            </form>
        </div>
    </div>
</header>

<main class="container admin-main">
    @include('partials.flash')
    @yield('content')
</main>
</body>
</html>
