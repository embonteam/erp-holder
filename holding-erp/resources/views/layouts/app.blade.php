<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#8b1a1a">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>@yield('title', 'Holding ERP')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@yield('theme', 'theme-holding')">
    <div id="erp-app" class="erp-shell">
        <aside class="erp-sidebar">
            <h1>Holding ERP</h1>
            <p>Enterprise control center</p>

            <nav class="erp-nav">
                @foreach ($navigation as $item)
                    <a href="{{ route($item['route']) }}" @class(['is-active' => request()->routeIs($item['active'] ?? $item['route'])])>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="erp-user-card">
                <strong>{{ auth()->user()?->name }}</strong>
                <span>{{ auth()->user()?->role?->name }}</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Keluar</button>
                </form>
            </div>
        </aside>

        <main class="erp-main">
            @if (session('status'))
                <p class="erp-alert erp-alert-success">{{ session('status') }}</p>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
