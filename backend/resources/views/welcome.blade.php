<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $brandName = $settings['company_name'] ?? 'Pollo Feliz Durango';
        $pageTitle = $settings['hero_title'] ?? 'Pollo Feliz Durango | El Mejor Pollo Asado al Carbón';
        $pageDescription = $settings['hero_description'] ?? 'Disfruta del mejor pollo asado al carbón de mezquite en Durango. Menú digital, promociones vigentes, sucursales y contacto directo.';
        $companyPhone = $settings['company_phone'] ?? '618-123-4567';
        $companyWhatsapp = preg_replace('/\D+/', '', (string) ($settings['company_whatsapp'] ?? '526181234567'));
        $primaryLoc = isset($locations) && is_iterable($locations) ? collect($locations)->first() : null;
        $menuList = isset($menuItems) && is_iterable($menuItems) ? collect($menuItems) : collect([]);
        $menuCategories = $menuList->pluck('category')->filter()->unique()->values();
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">

    {{-- Metadatos OpenGraph y Twitter Card sincronizados con configuración administrativa --}}
    <meta property="og:type" content="restaurant">
    <meta property="og:locale" content="es_MX">
    <meta property="og:site_name" content="{{ $brandName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $settings['hero_image_url'] ?? asset('favicon.jpg') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $settings['hero_image_url'] ?? asset('favicon.jpg') }}">

    {{-- Metadatos de geolocalización SEO calculados dinámicamente desde la primera sucursal activa --}}
    @if(!empty($primaryLoc?->latitude) && !empty($primaryLoc?->longitude))
        <meta name="geo.position" content="{{ $primaryLoc->latitude }};{{ $primaryLoc->longitude }}">
        <meta name="ICBM" content="{{ $primaryLoc->latitude }}, {{ $primaryLoc->longitude }}">
        <meta name="geo.placename" content="{{ $primaryLoc->branch_name ?? $primaryLoc->name ?? 'Durango, México' }}">
        <meta name="geo.region" content="MX-DUR">
    @endif

    {{-- Datos estructurados JSON-LD (Schema.org) con sucursales activas y menú digital --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FastFoodRestaurant",
        "name": "{{ $brandName }}",
        "image": "{{ $settings['hero_image_url'] ?? asset('favicon.jpg') }}",
        "servesCuisine": "Pollo Asado al Carbón, Comida Mexicana",
        "priceRange": "$$",
        "telephone": "{{ $companyPhone }}",
        "email": "{{ $settings['company_email'] ?? 'soporte@pollofelizdgo.com' }}",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ $settings['company_address'] ?? 'Av. 5 de Mayo #123' }}",
            "addressLocality": "Durango",
            "addressRegion": "Durango",
            "postalCode": "34000",
            "addressCountry": "MX"
        },
        "department": [
            @forelse(($locations ?? []) as $loc)
            {
                "@type": "Restaurant",
                "name": "Pollo Feliz - {{ $loc?->branch_name ?? $loc?->name ?? 'Sucursal Durango' }}",
                "telephone": "{{ $loc?->phone ?? $companyPhone }}",
                "openingHours": "{{ $loc?->schedule ?? 'Mo-Su 10:00-22:00' }}",
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "{{ $loc?->address ?? 'Durango, Dgo.' }}",
                    "addressLocality": "Durango",
                    "addressRegion": "Durango",
                    "addressCountry": "MX"
                }
                @if(!empty($loc?->latitude) && !empty($loc?->longitude))
                ,
                "hasMap": "https://www.google.com/maps/dir/?api=1&destination={{ $loc->latitude }},{{ $loc->longitude }}",
                "geo": {
                    "@type": "GeoCoordinates",
                    "latitude": {{ $loc->latitude }},
                    "longitude": {{ $loc->longitude }}
                }
                @endif
            }{{ !$loop->last ? ',' : '' }}
            @empty
            @endforelse
        ],
        "hasMenu": {
            "@type": "Menu",
            "name": "Menú Digital Pollo Feliz Durango",
            "hasMenuItem": [
                @foreach($menuList->take(12) as $dish)
                {
                    "@type": "MenuItem",
                    "name": "{{ $dish->name }}",
                    "description": "{{ \Illuminate\Support\Str::limit($dish->description ?? '', 140) }}",
                    "offers": {
                        "@type": "Offer",
                        "price": "{{ number_format((float) $dish->price, 2, '.', '') }}",
                        "priceCurrency": "MXN",
                        "availability": "{{ !empty($dish->is_sold_out) ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock' }}"
                    }
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
            ]
        }
    }
    </script>

    {{-- Tailwind CSS CDN para renderizado de respaldo en Blade --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red: '#dc2626',
                            amber: '#f59e0b',
                            dark: '#0f172a'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 antialiased selection:bg-red-500 selection:text-white">

    {{-- Barra informativa superior --}}
    <header class="bg-red-600 text-white text-xs font-bold py-2 px-4 text-center">
        🍗 ¡Bienvenidos a {{ $brandName }}! Sabor 100% Asado al Carbón de Mezquite · <a href="#branches" class="underline hover:text-amber-200">Ver Sucursales y Horarios</a>
    </header>

    {{-- Navegación principal fija y responsiva --}}
    <nav class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <a href="#" class="flex items-center gap-3 shrink-0">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-600 text-white font-black text-xl shadow-md">🍗</span>
                <div>
                    <span class="font-black text-lg tracking-tight text-gray-900 block leading-tight">POLLO FELIZ</span>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-red-600 block">Durango, Dgo.</span>
                </div>
            </a>

            <div class="hidden lg:flex items-center gap-5 text-sm font-bold text-gray-700 whitespace-nowrap">
                <a href="#menu" class="hover:text-red-600 transition">Menú</a>
                <a href="#promotions" class="hover:text-red-600 transition">Promociones</a>
                <a href="#branches" class="hover:text-red-600 transition">Sucursales ({{ isset($locations) ? count($locations) : 0 }})</a>
                <a href="#vacancies" class="hover:text-red-600 transition">Bolsa de Trabajo</a>
                <a href="#contact" class="hover:text-red-600 transition">Contacto</a>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="/admin/login" class="hidden sm:inline-flex text-xs font-bold text-gray-600 hover:text-red-600 px-3 py-2 rounded-xl border border-gray-200 hover:border-red-300 transition whitespace-nowrap">
                    Acceso Panel
                </a>
                <a href="https://wa.me/{{ $companyWhatsapp }}?text={{ urlencode('Hola Pollo Feliz Durango, me gustaría realizar un pedido.') }}" target="_blank" rel="noopener noreferrer" class="bg-red-600 hover:bg-red-700 text-white text-xs font-black px-4 py-2 rounded-xl shadow-md transition flex items-center gap-1.5 whitespace-nowrap">
                    <span>💬 Ordenar Ahora</span>
                </a>
            </div>
        </div>
    </nav>

    {{-- Sección Hero optimizada para LCP con fetchpriority="high" --}}
    <section class="relative bg-gradient-to-b from-red-50 via-white to-gray-50 py-14 sm:py-20 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 text-center lg:text-left">
                <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs sm:text-sm font-extrabold px-4 py-1.5 rounded-full mb-4">
                    {{ $settings['hero_badge'] ?? '🔥 Tradición & Sabor Inigualable' }}
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-gray-950 tracking-tight leading-tight">
                    {{ $pageTitle }}
                </h1>
                <p class="mt-4 text-base sm:text-lg text-gray-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    {{ $pageDescription }}
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center lg:justify-start gap-4">
                    <a href="#menu" class="bg-red-600 hover:bg-red-700 text-white font-black px-6 py-3.5 rounded-2xl shadow-lg hover:shadow-xl transition">
                        🍗 {{ $settings['hero_cta_primary'] ?? 'Ver Menú Completo' }}
                    </a>
                    <a href="#branches" class="bg-white hover:bg-gray-100 text-gray-900 border border-gray-300 font-bold px-6 py-3.5 rounded-2xl shadow-xs transition">
                        📍 Ubicar Sucursales
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-gray-900 aspect-[4/3]">
                    <img
                        src="{{ $settings['hero_image_url'] ?? 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?auto=format&fit=crop&w=1000&q=80' }}"
                        alt="{{ $pageTitle }}"
                        class="w-full h-full object-cover"
                        fetchpriority="high"
                        decoding="sync"
                    />
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== MENÚ DIGITAL INTERACTIVO ==================== --}}
    <section id="menu" class="py-16 sm:py-20 bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div>
                    <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-extrabold px-3.5 py-1 rounded-full mb-2">
                        🍗 Menú Digital al Carbón
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-gray-950 tracking-tight">
                        {{ $settings['menu_section_title'] ?? 'Nuestro Menú al Carbón' }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ $settings['menu_section_subtitle'] ?? 'Filtra por categoría o busca tu platillo favorito al instante.' }}
                    </p>
                </div>

                {{-- Buscador rápido de platillos sin recargar página --}}
                <div class="w-full md:w-80">
                    <label for="blade-menu-search" class="sr-only">Buscar platillo</label>
                    <input
                        id="blade-menu-search"
                        type="search"
                        placeholder="Buscar pollo, combos, salsas..."
                        class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:outline-none"
                    />
                </div>
            </div>

            {{-- Pestañas de filtro por categoría --}}
            @if($menuCategories->isNotEmpty())
                <div id="blade-menu-tabs" class="flex flex-wrap gap-2 mb-8">
                    <button type="button" data-category="all" class="menu-tab px-4 py-2 rounded-xl text-xs font-bold bg-red-600 text-white shadow-xs transition">
                        Todos ({{ $menuList->count() }})
                    </button>
                    @foreach($menuCategories as $cat)
                        <button type="button" data-category="{{ strtolower($cat) }}" class="menu-tab px-4 py-2 rounded-xl text-xs font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>
            @endif

            @if($menuList->isNotEmpty())
                <div id="blade-menu-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($menuList as $item)
                        @php $isSoldOut = (bool) ($item->is_sold_out ?? false); @endphp
                        <article
                            data-category="{{ strtolower($item->category ?? '') }}"
                            data-search="{{ strtolower(($item->name ?? '') . ' ' . ($item->description ?? '') . ' ' . ($item->category ?? '')) }}"
                            class="menu-card rounded-3xl border border-gray-200 bg-white overflow-hidden shadow-xs hover:shadow-lg transition flex flex-col justify-between {{ $isSoldOut ? 'opacity-75' : '' }}"
                        >
                            <div>
                                <div class="relative h-48 bg-gray-100 overflow-hidden">
                                    @if(!empty($item->image_url))
                                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover {{ $isSoldOut ? 'grayscale' : '' }}" loading="lazy" decoding="async" />
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-red-600 to-amber-600 flex items-center justify-center text-white text-4xl">🍗</div>
                                    @endif

                                    @if($isSoldOut)
                                        <span class="absolute top-3 right-3 rounded-lg bg-slate-900/95 text-white px-3 py-1 text-[11px] font-black uppercase tracking-wider">
                                            Agotado
                                        </span>
                                    @elseif(!empty($item->badge) || !empty($item->is_featured))
                                        <span class="absolute top-3 right-3 rounded-lg bg-red-600 text-white px-3 py-1 text-[11px] font-black uppercase tracking-wider">
                                            {{ !empty($item->badge) ? $item->badge : 'Destacado' }}
                                        </span>
                                    @endif
                                </div>

                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <h3 class="text-base font-black text-gray-900">{{ $item->name }}</h3>
                                        <span class="text-lg font-black text-red-600 tabular-nums whitespace-nowrap">
                                            ${{ number_format((float) $item->price, 0) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed">{{ $item->description }}</p>
                                </div>
                            </div>

                            <div class="px-5 pb-5 pt-2 flex items-center justify-between border-t border-gray-100 text-xs">
                                <span class="font-bold text-gray-500 uppercase tracking-wider">{{ $item->category }}</span>
                                @if($isSoldOut)
                                    <span class="font-bold text-gray-400">No disponible temporalmente</span>
                                @else
                                    <a href="https://wa.me/{{ $companyWhatsapp }}?text={{ urlencode('Hola, quisiera pedir: ' . $item->name) }}" target="_blank" rel="noopener noreferrer" class="font-black text-red-600 hover:underline">
                                        Pedir por WhatsApp →
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
                <p id="blade-menu-empty" class="hidden text-center py-10 text-sm font-semibold text-gray-500">
                    No se encontraron platillos que coincidan con tu búsqueda.
                </p>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-3xl border border-gray-200 max-w-xl mx-auto p-6">
                    <h3 class="text-base font-bold text-gray-800">Menú en actualización</h3>
                    <p class="text-xs text-gray-600 mt-1">Muy pronto publicaremos nuestros platillos disponibles.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ==================== PROMOCIONES VIGENTES Y MODAL DE TÉRMINOS ==================== --}}
    <section id="promotions" class="py-16 sm:py-20 bg-gray-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-10">
                <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 text-xs font-extrabold px-4 py-1.5 rounded-full mb-3">
                    🔥 Ofertas Por Tiempo Limitado
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-950 tracking-tight">
                    {{ $settings['promotions_section_title'] ?? 'Promociones Especiales' }}
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ $settings['promotions_section_subtitle'] ?? 'Aprovecha nuestros paquetes y cupones activos en sucursales participantes.' }}
                </p>
            </div>

            @forelse(($promotions ?? []) as $promo)
                @if($loop->first)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @endif
                        <div class="rounded-3xl border border-gray-200 bg-white overflow-hidden shadow-xs hover:shadow-lg transition flex flex-col justify-between">
                            <div>
                                <div class="relative h-48 bg-gray-900">
                                    @if(!empty($promo->image_url))
                                        <img src="{{ $promo->image_url }}" alt="{{ $promo->title }}" class="w-full h-full object-cover" loading="lazy" decoding="async" />
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-red-700 to-amber-600 flex items-center justify-center text-white text-3xl font-black">🔥 PROMO</div>
                                    @endif
                                    <span class="absolute top-3 left-3 rounded-lg bg-emerald-600 text-white px-2.5 py-1 text-[11px] font-black uppercase">
                                        {{ $promo->badge ?? 'Vigente' }}
                                    </span>
                                    @if(!empty($promo->promo_price))
                                        <span class="absolute bottom-3 right-3 rounded-xl bg-red-600 text-white px-3 py-1 text-sm font-black tabular-nums">
                                            ${{ number_format((float) $promo->promo_price, 0) }} MXN
                                        </span>
                                    @endif
                                </div>
                                <div class="p-5 space-y-2">
                                    <h3 class="text-lg font-black text-gray-950">{{ $promo->title }}</h3>
                                    @if(!empty($promo->subtitle))
                                        <p class="text-xs font-bold text-red-600">{{ $promo->subtitle }}</p>
                                    @endif
                                    <p class="text-xs text-gray-600 leading-relaxed">{{ $promo->description }}</p>
                                    @if(!empty($promo->code))
                                        <div class="inline-block rounded-lg bg-red-50 border border-red-200 px-3 py-1 text-xs font-mono font-bold text-red-700">
                                            Cupón: {{ $promo->code }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="px-5 pb-5 pt-3 border-t border-gray-100 flex items-center justify-between gap-2 text-xs">
                                <span class="text-gray-500 font-semibold">
                                    @if(!empty($promo->valid_until))
                                        Válida hasta {{ \Illuminate\Support\Carbon::parse($promo->valid_until)->format('d/m/Y') }}
                                    @else
                                        Vigente en Durango
                                    @endif
                                </span>
                                <button
                                    type="button"
                                    onclick="openPromoTerms(@js($promo->title), @js($promo->terms_conditions ?? 'Promoción válida en sucursales Pollo Feliz Durango participantes. No acumulable con otras promociones ni descuentos.'))"
                                    class="font-bold text-red-600 hover:underline"
                                >
                                    Términos y condiciones
                                </button>
                            </div>
                        </div>
                @if($loop->last)
                    </div>
                @endif
            @empty
                <div class="text-center py-10 bg-white rounded-3xl border border-gray-200 max-w-xl mx-auto p-6">
                    <h3 class="text-base font-bold text-gray-800">Sin promociones activas por el momento</h3>
                    <p class="text-xs text-gray-600 mt-1">Visita nuestro menú digital o consulta nuestras sucursales.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ==================== SUCURSALES Y MAPA INTERACTIVO (LAZY INTERSECTION OBSERVER) ==================== --}}
    <section id="branches" class="py-16 sm:py-20 bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs sm:text-sm font-extrabold px-4 py-1.5 rounded-full mb-3">
                    📍 Cobertura & Puntos de Venta
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-950 tracking-tight">
                    {{ $settings['branches_section_title'] ?? 'Nuestras Sucursales' }}
                </h2>
                <p class="mt-3 text-sm sm:text-base text-gray-600">
                    {{ $settings['branches_section_subtitle'] ?? 'Visítanos o llámanos directamente para disfrutar del mejor sabor recién salido de la parrilla.' }}
                </p>
            </div>

            @forelse(($locations ?? []) as $branch)
                @if($loop->first)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @endif
                        @php
                            $bLat = $branch?->latitude ?? 24.0277;
                            $bLng = $branch?->longitude ?? -104.6532;
                            $directionsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . $bLat . ',' . $bLng;
                        @endphp
                        <div class="rounded-3xl border border-gray-200 bg-white shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                            <div>
                                <div class="relative h-48 w-full bg-gray-900 overflow-hidden">
                                    @if(!empty($branch?->image_url))
                                        <img src="{{ $branch->image_url }}" alt="{{ $branch->branch_name ?? $branch->name ?? 'Sucursal Pollo Feliz' }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                                    @else
                                        <div class="h-full w-full bg-gradient-to-br from-red-600 via-amber-600 to-amber-800 flex flex-col items-center justify-center p-6 text-white text-center">
                                            <span class="text-4xl mb-1">🍗</span>
                                            <span class="text-xs uppercase font-black text-amber-200">Pollo Feliz Durango</span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                                    <div class="absolute top-3 left-3 right-3 flex items-center justify-between">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-black uppercase tracking-wider {{ ($branch?->is_open ?? true) ? 'bg-emerald-600 text-white' : 'bg-gray-800 text-gray-300' }}">
                                            {{ ($branch?->is_open ?? true) ? 'Abierto Ahora' : 'Cerrado' }}
                                        </span>
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-red-600 text-white shadow-xs">
                                            Sucursal #{{ $branch?->id ?? $loop->iteration }}
                                        </span>
                                    </div>

                                    <h3 class="absolute bottom-3 left-4 right-4 text-xl font-black text-white leading-tight">
                                        {{ $branch?->branch_name ?? $branch?->name ?? 'Sucursal Durango' }}
                                    </h3>
                                </div>

                                <div class="p-5 space-y-3 text-sm text-gray-600">
                                    <div class="flex items-start gap-3">
                                        <span class="text-red-600 text-lg">📍</span>
                                        <span class="font-medium text-gray-800 text-xs sm:text-sm">{{ $branch?->address ?? 'Dirección disponible próximamente' }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-orange-500 text-lg">🕒</span>
                                        <span class="text-xs sm:text-sm">{{ $branch?->schedule ?? 'Lun - Dom: 10:00 AM - 10:00 PM' }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-emerald-600 text-lg">📞</span>
                                        <a href="tel:{{ $branch?->phone ?? $companyPhone }}" class="text-xs sm:text-sm font-bold text-gray-900 hover:text-red-600">{{ $branch?->phone ?? $companyPhone }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5 pt-0 grid grid-cols-3 gap-2">
                                <button type="button" onclick="focusBranchOnMap({{ (float) $bLat }}, {{ (float) $bLng }}, {{ (int) ($branch?->id ?? 0) }})" class="flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold px-2.5 py-2.5 text-xs transition">
                                    📍 Mapa
                                </button>
                                <a href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-bold px-2.5 py-2.5 text-xs transition">
                                    🧭 Cómo llegar
                                </a>
                                <a href="tel:{{ $branch?->phone ?? $companyPhone }}" class="flex items-center justify-center rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold px-2.5 py-2.5 text-xs transition">
                                    📞 Llamar
                                </a>
                            </div>
                        </div>
                @if($loop->last)
                    </div>
                @endif
            @empty
                <div class="text-center py-12 bg-amber-50 rounded-3xl border border-amber-200 max-w-xl mx-auto p-6">
                    <h3 class="text-lg font-bold text-amber-900">Ubicaciones en actualización</h3>
                    <p class="text-sm text-amber-800 mt-1">Contáctanos por WhatsApp para ubicar tu sucursal más cercana.</p>
                </div>
            @endforelse

            {{-- Contenedor del mapa cargado de forma diferida al acercarse al viewport --}}
            <div id="map-section-wrapper" class="mt-8 rounded-3xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm overflow-hidden">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-950 flex items-center gap-2">
                            <span>🗺️</span> Mapa de Cobertura en Vivo (Durango)
                        </h3>
                        <p class="text-xs text-gray-500">Haz clic en cualquier sucursal o marcador para ver horario, teléfono y trazar tu ruta GPS.</p>
                    </div>
                    <div id="map-branch-chips" class="flex flex-wrap gap-1.5"></div>
                </div>

                <div id="landing-leaflet-map" class="h-96 w-full rounded-2xl bg-gray-100 border border-gray-200 z-10 flex items-center justify-center text-xs font-semibold text-gray-500">
                    Cargando mapa interactivo de sucursales...
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== BOLSA DE TRABAJO ==================== --}}
    <section id="vacancies" class="py-16 sm:py-20 bg-gray-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-10">
                <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-extrabold px-4 py-1.5 rounded-full mb-3">
                    💼 Únete a la Familia Pollo Feliz
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-950 tracking-tight">Bolsa de Trabajo</h2>
                <p class="mt-2 text-sm text-gray-600">Conoce nuestras vacantes vigentes en Durango y postúlate por WhatsApp.</p>
            </div>

            @forelse(($jobs ?? []) as $job)
                @if($loop->first)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @endif
                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-xs flex flex-col justify-between space-y-4">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-[11px] font-bold">
                                        {{ $job->employment_type ?? 'Tiempo Completo' }}
                                    </span>
                                    <span class="text-xs font-semibold text-gray-500">📍 {{ $job->location ?? 'Durango' }}</span>
                                </div>
                                <h3 class="text-lg font-black text-gray-950">{{ $job->title }}</h3>
                                <p class="mt-2 text-xs text-gray-600 leading-relaxed">{{ $job->description }}</p>
                                @if(!empty($job->schedule))
                                    <p class="mt-3 text-xs font-semibold text-gray-500">🕒 {{ $job->schedule }}</p>
                                @endif
                            </div>
                            <a href="https://wa.me/{{ $companyWhatsapp }}?text={{ urlencode('Hola, me interesa postularme a la vacante: ' . $job->title) }}" target="_blank" rel="noopener noreferrer" class="w-full text-center rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 text-xs transition">
                                Postularme ahora
                            </a>
                        </div>
                @if($loop->last)
                    </div>
                @endif
            @empty
                <div class="text-center py-8 text-sm text-gray-500">No hay vacantes publicadas en este momento.</div>
            @endforelse
        </div>
    </section>

    {{-- ==================== CONTACTO DIRECTO CON ENVÍO A SOPORTE@POLLOFELIZDGO.COM ==================== --}}
    <section id="contact" class="py-16 sm:py-20 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-extrabold px-4 py-1.5 rounded-full mb-3">
                    ✉️ Atención a Clientes
                </span>
                <h2 class="text-3xl font-black text-gray-950 tracking-tight">Contáctanos</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Envíanos tus dudas, sugerencias o cotizaciones para eventos. Recibimos directamente en <strong>soporte@pollofelizdgo.com</strong>.
                </p>
            </div>

            <form id="blade-contact-form" class="rounded-3xl border border-gray-200 bg-gray-50 p-6 sm:p-8 space-y-4">
                {{-- Campo trampa antispam oculto (Honeypot) --}}
                <div class="hidden" aria-hidden="true">
                    <label for="contact-website">Sitio web</label>
                    <input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="contact-name" class="block text-xs font-bold text-gray-700 mb-1">Nombre completo *</label>
                        <input id="contact-name" name="name" type="text" required class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-red-500 focus:outline-none" />
                    </div>
                    <div>
                        <label for="contact-email" class="block text-xs font-bold text-gray-700 mb-1">Correo electrónico *</label>
                        <input id="contact-email" name="email" type="email" required class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-red-500 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="contact-phone" class="block text-xs font-bold text-gray-700 mb-1">Teléfono</label>
                        <input id="contact-phone" name="phone" type="tel" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-red-500 focus:outline-none" />
                    </div>
                    <div>
                        <label for="contact-subject" class="block text-xs font-bold text-gray-700 mb-1">Asunto</label>
                        <input id="contact-subject" name="subject" type="text" value="Consulta general" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-red-500 focus:outline-none" />
                    </div>
                </div>

                <div>
                    <label for="contact-message" class="block text-xs font-bold text-gray-700 mb-1">Mensaje *</label>
                    <textarea id="contact-message" name="message" rows="4" required class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-red-500 focus:outline-none"></textarea>
                </div>

                <div id="blade-contact-status" class="hidden rounded-xl p-3 text-xs font-bold"></div>

                <button type="submit" id="blade-contact-submit" class="w-full rounded-xl bg-red-600 hover:bg-red-700 text-white font-black py-3 text-sm shadow-md transition">
                    Enviar Mensaje a Soporte
                </button>
            </form>
        </div>
    </section>

    {{-- Modal de Términos y Condiciones de Promociones --}}
    <div id="promo-terms-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-gray-200 space-y-4">
            <div class="flex items-start justify-between gap-3">
                <h3 id="promo-terms-title" class="text-lg font-black text-gray-950">Términos y Condiciones</h3>
                <button type="button" onclick="closePromoTerms()" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-700">✕</button>
            </div>
            <p id="promo-terms-body" class="text-sm text-gray-600 leading-relaxed whitespace-pre-line"></p>
            <button type="button" onclick="closePromoTerms()" class="w-full rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 text-xs transition">
                Entendido
            </button>
        </div>
    </div>

    {{-- Botón flotante de WhatsApp sincronizado con ajustes del Admin Panel --}}
    <a
        href="https://wa.me/{{ $companyWhatsapp }}?text={{ urlencode('Hola Pollo Feliz Durango, quiero información o realizar un pedido.') }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Contactar por WhatsApp"
        class="fixed bottom-5 right-5 z-40 flex items-center gap-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black px-4 py-3 text-xs shadow-xl transition"
    >
        <span>💬 WhatsApp</span>
    </a>

    {{-- Pie de página --}}
    <footer class="bg-gray-950 text-white py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-600 text-white font-black text-xl">🍗</span>
                <div>
                    <span class="font-black text-lg block leading-tight">{{ $brandName }}</span>
                    <span class="text-xs text-gray-400">Tradición al Carbón · {{ $settings['company_address'] ?? 'Durango, Dgo.' }}</span>
                </div>
            </div>
            <p class="text-xs text-gray-500">
                &copy; {{ date('Y') }} {{ $brandName }}. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    {{-- Scripts interactivos: filtros de menú, modal de promociones, lazy loading de mapa Leaflet y formulario AJAX --}}
    <script>
        function openPromoTerms(title, terms) {
            const modal = document.getElementById('promo-terms-modal');
            document.getElementById('promo-terms-title').textContent = title || 'Términos y Condiciones';
            document.getElementById('promo-terms-body').textContent = terms || 'Consulta condiciones en sucursal.';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePromoTerms() {
            const modal = document.getElementById('promo-terms-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        let bladeLeafletMap = null;
        const bladeLeafletMarkers = {};

        function focusBranchOnMap(lat, lng, id) {
            const wrapper = document.getElementById('map-section-wrapper');
            if (wrapper) wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
            ensureMapLoaded(() => {
                if (bladeLeafletMap) {
                    bladeLeafletMap.flyTo([lat, lng], 16, { duration: 1.0 });
                    if (bladeLeafletMarkers[id]) bladeLeafletMarkers[id].openPopup();
                }
            });
        }

        let mapLoadStarted = false;
        let mapReadyCallbacks = [];

        function ensureMapLoaded(cb) {
            if (bladeLeafletMap) {
                if (cb) cb();
                return;
            }
            if (cb) mapReadyCallbacks.push(cb);
            if (mapLoadStarted) return;
            mapLoadStarted = true;

            const css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(css);

            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = initBladeLeafletMap;
            document.body.appendChild(script);
        }

        function initBladeLeafletMap() {
            const locationsData = @json($locations ?? []);
            const mapEl = document.getElementById('landing-leaflet-map');
            if (!mapEl || !Array.isArray(locationsData)) return;

            const validLocations = locationsData.filter(loc => loc.latitude && loc.longitude);
            if (validLocations.length === 0) {
                mapEl.textContent = 'No hay coordenadas GPS activas registradas.';
                return;
            }

            mapEl.textContent = '';
            const defaultLat = parseFloat(validLocations[0].latitude) || 24.0277;
            const defaultLng = parseFloat(validLocations[0].longitude) || -104.6532;
            const defaultZoom = validLocations[0].map_zoom || 13;

            bladeLeafletMap = L.map('landing-leaflet-map', { scrollWheelZoom: false }).setView([defaultLat, defaultLng], defaultZoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(bladeLeafletMap);

            const customMarkerIcon = L.divIcon({
                className: 'custom-pollo-pin',
                html: '<div style="background-color:#dc2626; color:white; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 4px 10px rgba(0,0,0,0.3); border:2px solid white; cursor:pointer;">🍗</div>',
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -36]
            });

            const bounds = [];
            const chipsContainer = document.getElementById('map-branch-chips');

            validLocations.forEach((loc) => {
                const lat = parseFloat(loc.latitude);
                const lng = parseFloat(loc.longitude);
                const name = loc.branch_name || loc.name || ('Sucursal #' + loc.id);
                const address = loc.address || '';
                const schedule = loc.schedule || 'Lun - Dom: 10:00 AM - 10:00 PM';
                const directionsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' + lat + ',' + lng;

                bounds.push([lat, lng]);

                const popupContent = `
                    <div style="font-family: inherit; padding: 4px; max-width: 240px;">
                        <h4 style="font-weight: 900; margin: 0 0 4px; font-size: 14px; color: #111;">${name}</h4>
                        <p style="font-size: 11px; color: #555; margin: 0 0 6px;">📍 ${address}</p>
                        <p style="font-size: 11px; color: #555; margin: 0 0 8px;">🕒 ${schedule}</p>
                        <a href="${directionsUrl}" target="_blank" rel="noopener noreferrer" style="display:inline-block; background-color:#dc2626; color:white; text-decoration:none; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:bold;">🧭 Cómo llegar</a>
                    </div>
                `;

                const marker = L.marker([lat, lng], { icon: customMarkerIcon }).addTo(bladeLeafletMap).bindPopup(popupContent);
                if (loc.id) bladeLeafletMarkers[loc.id] = marker;

                if (chipsContainer) {
                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'px-3 py-1 rounded-xl text-xs font-bold bg-gray-100 hover:bg-red-50 hover:text-red-600 text-gray-700 border border-gray-200 transition';
                    chip.textContent = name;
                    chip.onclick = () => {
                        bladeLeafletMap.flyTo([lat, lng], 16, { duration: 1.0 });
                        marker.openPopup();
                    };
                    chipsContainer.appendChild(chip);
                }
            });

            if (bounds.length > 1) {
                bladeLeafletMap.fitBounds(bounds, { padding: [40, 40] });
            }

            mapReadyCallbacks.forEach(fn => fn());
            mapReadyCallbacks = [];
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Filtrado instantáneo de menú por categoría y texto sin recarga
            let activeCat = 'all';
            const searchInput = document.getElementById('blade-menu-search');
            const cards = Array.from(document.querySelectorAll('.menu-card'));
            const tabs = Array.from(document.querySelectorAll('.menu-tab'));
            const emptyMsg = document.getElementById('blade-menu-empty');

            function filterBladeMenu() {
                const q = (searchInput?.value || '').trim().toLowerCase();
                let visible = 0;
                cards.forEach((card) => {
                    const cat = card.dataset.category || '';
                    const text = card.dataset.search || '';
                    const matchCat = activeCat === 'all' || cat === activeCat;
                    const matchSearch = !q || text.includes(q);
                    const show = matchCat && matchSearch;
                    card.classList.toggle('hidden', !show);
                    if (show) visible++;
                });
                if (emptyMsg) emptyMsg.classList.toggle('hidden', visible > 0 || cards.length === 0);
            }

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    activeCat = tab.dataset.category || 'all';
                    tabs.forEach((t) => {
                        const isSelected = t === tab;
                        t.classList.toggle('bg-red-600', isSelected);
                        t.classList.toggle('text-white', isSelected);
                        t.classList.toggle('bg-gray-100', !isSelected);
                        t.classList.toggle('text-gray-700', !isSelected);
                    });
                    filterBladeMenu();
                });
            });

            searchInput?.addEventListener('input', filterBladeMenu);

            // Carga diferida del mapa mediante IntersectionObserver
            const mapWrapper = document.getElementById('map-section-wrapper');
            if (mapWrapper && 'IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    if (entries.some((e) => e.isIntersecting)) {
                        ensureMapLoaded();
                        observer.disconnect();
                    }
                }, { rootMargin: '250px' });
                observer.observe(mapWrapper);
            } else {
                ensureMapLoaded();
            }

            // Envío AJAX de contacto hacia /api/feedback (soporte@pollofelizdgo.com)
            const contactForm = document.getElementById('blade-contact-form');
            const statusBox = document.getElementById('blade-contact-status');
            const submitBtn = document.getElementById('blade-contact-submit');

            contactForm?.addEventListener('submit', async (e) => {
                e.preventDefault();
                if (!submitBtn || !statusBox) return;

                submitBtn.disabled = true;
                submitBtn.textContent = 'Enviando mensaje...';
                statusBox.classList.add('hidden');

                const formData = new FormData(contactForm);
                const payload = Object.fromEntries(formData.entries());

                try {
                    const res = await fetch('/api/feedback', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json();
                    statusBox.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-emerald-100', 'text-emerald-800');
                    if (res.ok) {
                        statusBox.classList.add('bg-emerald-100', 'text-emerald-800');
                        statusBox.textContent = data.message || 'Mensaje enviado correctamente.';
                        contactForm.reset();
                    } else {
                        statusBox.classList.add('bg-red-100', 'text-red-700');
                        statusBox.textContent = data.message || 'No pudimos enviar tu mensaje. Verifica los campos.';
                    }
                } catch (_) {
                    statusBox.classList.remove('hidden', 'bg-emerald-100', 'text-emerald-800');
                    statusBox.classList.add('bg-red-100', 'text-red-700');
                    statusBox.textContent = 'Error de conexión. Intenta nuevamente o contáctanos por WhatsApp.';
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Enviar Mensaje a Soporte';
                }
            });
        });
    </script>
</body>
</html>
