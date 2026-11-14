{{--
    Layout principal en Dark Mode para el Panel Administrativo de Pollo Feliz Durango.
    Incluye navegación lateral para los 11 módulos del sistema y verificación segura de base de datos.
--}}
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Administrativo | Pollo Feliz Durango')</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{-- Respaldo Tailwind CSS para entornos sin compilación Vite previa --}}
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = { darkMode: 'class' };
        </script>
    @endif
</head>
<body class="bg-[#0b1120] text-slate-100 admin-panel antialiased min-h-screen flex flex-col lg:flex-row">
    @php
        $dbConnected = false;
        $rawDriver = strtolower((string) config('database.default', 'mysql'));
        $dbEngineLabel = match ($rawDriver) {
            'mysql', 'mariadb' => 'MySQL',
            'pgsql', 'postgres', 'postgresql' => 'PostgreSQL',
            'sqlite' => 'SQLite',
            default => strtoupper($rawDriver),
        };
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $dbConnected = true;
        } catch (\Throwable $e) {
            $dbConnected = false;
        }
        $dbStatusText = $dbConnected ? "{$dbEngineLabel} · Conectado" : "{$dbEngineLabel} · Sin conexión";

        $modules = [
            ['key' => 'landing', 'label' => 'Editar Landing', 'href' => '#modulo-landing', 'icon' => '🎨'],
            ['key' => 'menu', 'label' => 'Menú', 'href' => '#modulo-menu', 'icon' => '🍗'],
            ['key' => 'promotions', 'label' => 'Promociones', 'href' => '#modulo-promociones', 'icon' => '🏷️'],
            ['key' => 'branches', 'label' => 'Sucursales', 'href' => '#modulo-sucursales', 'icon' => '📍'],
            ['key' => 'jobs', 'label' => 'Bolsa de Vacantes', 'href' => '#modulo-vacantes', 'icon' => '💼'],
            ['key' => 'orders', 'label' => 'Pedidos en Vivo', 'href' => '#modulo-pedidos', 'icon' => '🛵'],
            ['key' => 'reviews', 'label' => 'Reseñas & Opiniones', 'href' => '#modulo-resenas', 'icon' => '⭐'],
            ['key' => 'messages', 'label' => 'Mensajes', 'href' => '#modulo-mensajes', 'icon' => '✉️'],
            ['key' => 'logs', 'label' => 'Logs del Sistema', 'href' => '#modulo-logs', 'icon' => '🛡️'],
            ['key' => 'users', 'label' => 'Usuarios & Roles', 'href' => '#modulo-usuarios', 'icon' => '👥'],
            ['key' => 'approvals', 'label' => 'Aprobaciones & Cambios', 'href' => '#modulo-aprobaciones', 'icon' => '✅'],
        ];
    @endphp

    {{-- Barra lateral fija de módulos --}}
    <aside class="w-full lg:w-72 shrink-0 bg-[#111827] border-b lg:border-b-0 lg:border-r border-slate-800 flex flex-col justify-between">
        <div class="p-5">
            <div class="flex items-center justify-between gap-3 pb-5 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-white text-xl font-black shadow-lg shadow-red-600/20">🍗</span>
                    <div>
                        <p class="font-black text-sm tracking-tight text-white">POLLO FELIZ</p>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-red-400">Admin Durango</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $dbConnected ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/15 text-amber-300 border border-amber-500/30' }}">
                    {{ $dbStatusText }}
                </span>
            </div>

            <nav class="mt-5 space-y-1">
                @foreach($modules as $mod)
                    <a href="{{ $mod['href'] }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-[#161e2e] border border-transparent hover:border-slate-700/80 transition">
                        <span class="text-base">{{ $mod['icon'] }}</span>
                        <span>{{ $mod['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="p-5 border-t border-slate-800 space-y-2">
            <a href="{{ url('/') }}" class="w-full flex items-center justify-center gap-2 rounded-xl bg-[#161e2e] hover:bg-slate-800 border border-slate-700 px-4 py-2.5 text-xs font-bold text-slate-200 transition">
                <span>🌐</span>
                <span>Ver Landing Pública</span>
            </a>
        </div>
    </aside>

    {{-- Contenido principal --}}
    <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
        @if(session('success'))
            <div class="mb-6 rounded-xl bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-sm font-semibold text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl bg-red-500/10 border border-red-500/30 px-4 py-3 text-sm text-red-300">
                <p class="font-bold mb-1">Revisa los campos marcados:</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
