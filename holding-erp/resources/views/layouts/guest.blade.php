<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#8b1a1a">
    <title>@yield('title', 'Holding ERP')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="theme-holding erp-auth-body">
    <main class="erp-auth-shell">
        <section class="erp-auth-brand">
            <p class="erp-kicker">Holding ERP Ecosystem</p>
            <h1>Centralized multi-brand operations.</h1>
            <p>Warehouse-centered, role-aware, and built for long-term scale.</p>
        </section>

        <section class="erp-auth-panel">
            @yield('content')
        </section>
    </main>
</body>
</html>
