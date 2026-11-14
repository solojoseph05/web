{{--
    Layout base para páginas de error HTTP en Pollo Feliz Durango.
    Importante: NO realiza consultas a base de datos, Auth ni Settings para evitar fallos en cascada (500/503).
--}}
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('code', 'Error') · @yield('title', 'Pollo Feliz Durango')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0b1120] text-slate-100 antialiased min-h-screen flex flex-col justify-between px-4 py-10">
    <header class="max-w-xl w-full mx-auto flex items-center justify-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-white text-xl font-black shadow-lg shadow-red-600/20">🍗</span>
        <div class="text-left">
            <span class="block font-black text-base tracking-tight text-white">POLLO FELIZ DURANGO</span>
            <span class="block text-[10px] font-bold uppercase tracking-widest text-red-400">Tradición al Carbón</span>
        </div>
    </header>

    <main class="max-w-xl w-full mx-auto my-8">
        <div class="rounded-3xl bg-[#111827] border border-slate-800 p-8 sm:p-10 text-center shadow-2xl">
            <div class="inline-flex items-center justify-center px-4 py-1.5 rounded-xl bg-red-600/15 border border-red-500/30 text-red-400 text-xs font-black uppercase tracking-widest mb-4">
                Error HTTP @yield('code')
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                @yield('title')
            </h1>

            <p class="mt-3 text-sm sm:text-base text-slate-300 leading-relaxed">
                @yield('message')
            </p>

            @hasSection('hint')
                <p class="mt-3 text-xs text-slate-400">
                    @yield('hint')
                </p>
            @endif

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ url('/') }}" class="rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-5 py-3 transition">
                    Volver al Inicio
                </a>
                @yield('actions')
            </div>
        </div>
    </main>

    <footer class="max-w-xl w-full mx-auto text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Pollo Feliz Durango · Atención a Clientes:
        <a href="mailto:soporte@pollofelizdgo.com" class="text-slate-300 hover:text-red-400 underline">soporte@pollofelizdgo.com</a>
    </footer>
</body>
</html>
