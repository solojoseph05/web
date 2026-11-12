<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings['hero_title'] ?? 'Pollo Feliz Durango | El Mejor Pollo Asado al Carbón' }}</title>
    <meta name="description" content="{{ $settings['hero_description'] ?? 'Disfruta del mejor pollo asado al carbón de mezquite en Durango. Sucursales, promociones exclusivas, servicio a domicilio y bolsa de trabajo.' }}">

    <!-- OpenGraph / Social Media -->
    <meta property="og:type" content="restaurant">
    <meta property="og:title" content="{{ $settings['hero_title'] ?? 'Pollo Feliz Durango | El Mejor Pollo Asado al Carbón' }}">
    <meta property="og:description" content="{{ $settings['hero_description'] ?? 'Tradición al carbón, salsas caseras y sabor inigualable.' }}">
    <meta property="og:image" content="{{ asset('favicon.jpg') }}">

    <!-- Metadatos de Geolocalización y SEO para Restaurantes (Ubicaciones Dinámicas) -->
    @if(isset($locations) && count($locations) > 0)
        @php $primaryLoc = $locations->first(); @endphp
        @if(!empty($primaryLoc->latitude) && !empty($primaryLoc->longitude))
            <meta name="geo.position" content="{{ $primaryLoc->latitude }};{{ $primaryLoc->longitude }}">
            <meta name="ICBM" content="{{ $primaryLoc->latitude }}, {{ $primaryLoc->longitude }}">
            <meta name="geo.placename" content="{{ $primaryLoc->branch_name ?? $primaryLoc->name ?? 'Durango, México' }}">
            <meta name="geo.region" content="MX-DUR">
        @endif
    @endif

    <!-- JSON-LD Dinámico para Google Rich Snippets / Schema.org Restaurant -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FastFoodRestaurant",
        "name": "Pollo Feliz Durango",
        "image": "{{ asset('favicon.jpg') }}",
        "servesCuisine": "Pollo Asado al Carbón, Comida Mexicana",
        "priceRange": "$$",
        "telephone": "{{ $settings['company_phone'] ?? '618-123-4567' }}",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ $settings['company_address'] ?? 'Av. 5 de Mayo #123' }}",
            "addressLocality": "Durango",
            "addressRegion": "Durango",
            "postalCode": "34000",
            "addressCountry": "MX"
        },
        "department": [
            @if(isset($locations))
                @foreach($locations as $index => $loc)
                {
                    "@type": "Restaurant",
                    "name": "Pollo Feliz - {{ $loc->branch_name ?? $loc->name }}",
                    "telephone": "{{ $loc->phone ?? '618-123-4567' }}",
                    "address": {
                        "@type": "PostalAddress",
                        "streetAddress": "{{ $loc->address }}",
                        "addressLocality": "Durango",
                        "addressRegion": "Durango",
                        "addressCountry": "MX"
                    }
                    @if(!empty($loc->latitude) && !empty($loc->longitude))
                    ,
                    "geo": {
                        "@type": "GeoCoordinates",
                        "latitude": {{ $loc->latitude }},
                        "longitude": {{ $loc->longitude }}
                    }
                    @endif
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
            @endif
        ]
    }
    </script>

    <!-- Tailwind CSS CDN para renderizado instantáneo en Blade -->
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

    <!-- Leaflet CSS & JS para Mapa Interactivo Responsivo -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body class="bg-gray-50 text-gray-900 antialiased selection:bg-red-500 selection:text-white">

    <!-- Top Notification Bar -->
    <header class="bg-red-600 text-white text-xs font-bold py-2 px-4 text-center">
        🍗 ¡Bienvenidos a Pollo Feliz Durango! Sabor 100% Asado al Carbón de Mezquite · <a href="#branches" class="underline hover:text-amber-200">Ver Sucursales y Horarios</a>
    </header>

    <!-- Main Navigation Bar -->
    <nav class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-600 text-white font-black text-xl shadow-md">🍗</span>
                <div>
                    <span class="font-black text-lg tracking-tight text-gray-900 block leading-tight">POLLO FELIZ</span>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-red-600 block">Durango, Dgo.</span>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-6 text-sm font-bold text-gray-700">
                <a href="#menu" class="hover:text-red-600 transition">Menú</a>
                <a href="#promotions" class="hover:text-red-600 transition">Promociones</a>
                <a href="#branches" class="hover:text-red-600 transition text-red-600">Sucursales ({{ isset($locations) ? count($locations) : 0 }})</a>
                <a href="#vacancies" class="hover:text-red-600 transition">Bolsa de Trabajo</a>
            </div>

            <div class="flex items-center gap-3">
                <a href="/admin/login" class="text-xs font-bold text-gray-600 hover:text-red-600 px-3 py-1.5 rounded-xl border border-gray-200 hover:border-red-300 transition">
                    Acceso Panel
                </a>
                <a href="https://wa.me/526181234567" target="_blank" class="bg-red-600 hover:bg-red-700 text-white text-xs font-black px-4 py-2 rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>💬 Pedir en Línea</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-red-50 via-white to-gray-50 py-16 sm:py-24 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-600 text-xs sm:text-sm font-extrabold px-4 py-1.5 rounded-full mb-4">
                🔥 Tradición & Sabor Inigualable
            </span>
            <h1 class="text-4xl sm:text-6xl font-black text-gray-950 tracking-tight max-w-4xl mx-auto leading-tight">
                {{ $settings['hero_title'] ?? 'El Mejor Pollo Asado al Carbón de Durango' }}
            </h1>
            <p class="mt-4 text-base sm:text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                {{ $settings['hero_description'] ?? 'Marinado tradicional, asado al carbón con leña de mezquite, acompañado de nuestras salsas caseras y complementos recién preparados.' }}
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="#branches" class="bg-red-600 hover:bg-red-700 text-white font-black px-6 py-3.5 rounded-2xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                    📍 Ubicar Sucursales Cercanas
                </a>
                <a href="#menu" class="bg-white hover:bg-gray-100 text-gray-900 border border-gray-300 font-bold px-6 py-3.5 rounded-2xl shadow-xs transition">
                    📖 Ver Menú Completo
                </a>
            </div>
        </div>
    </section>

    <!-- ==================== SUCURSALES Y MAPA INTERACTIVO (MÓDULO DINÁMICO) ==================== -->
    <section id="branches" class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-600 text-xs sm:text-sm font-extrabold px-4 py-1.5 rounded-full mb-3">
                    📍 Cobertura & Puntos de Venta
                </span>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-950 tracking-tight">
                    Nuestras Sucursales
                </h2>
                <p class="mt-3 text-sm sm:text-base text-gray-600">
                    Visítanos o llámanos directamente para disfrutar del mejor sabor recién salido de la parrilla.
                </p>
            </div>

            <!-- Grid de Sucursales Dinámicas -->
            @if(isset($locations) && count($locations) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    @foreach($locations as $branch)
                        <div class="rounded-3xl border border-gray-200 bg-white shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                            <div>
                                <!-- Imagen o Banner de la Sucursal -->
                                <div class="relative h-48 w-full bg-gray-900 overflow-hidden">
                                    @if(!empty($branch->image_url))
                                        <img src="{{ $branch->image_url }}" alt="{{ $branch->branch_name ?? $branch->name }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    @else
                                        <div class="h-full w-full bg-gradient-to-br from-red-600 via-amber-600 to-amber-800 flex flex-col items-center justify-center p-6 text-white text-center">
                                            <span class="text-4xl mb-1">🍗</span>
                                            <span class="text-xs uppercase font-black text-amber-200">Pollo Feliz Durango</span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                                    <!-- Badges -->
                                    <div class="absolute top-3 left-3 right-3 flex items-center justify-between">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider {{ ($branch->is_open ?? true) ? 'bg-emerald-600 text-white' : 'bg-gray-800 text-gray-300' }}">
                                            <span class="h-2 w-2 rounded-full {{ ($branch->is_open ?? true) ? 'bg-white animate-pulse' : 'bg-gray-400' }}"></span>
                                            <span>{{ ($branch->is_open ?? true) ? 'Abierto Ahora' : 'Cerrado' }}</span>
                                        </span>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-red-600 text-white shadow-xs">
                                            Sucursal #{{ $branch->id }}
                                        </span>
                                    </div>

                                    <h3 class="absolute bottom-3 left-4 right-4 text-xl font-black text-white leading-tight">
                                        {{ $branch->branch_name ?? $branch->name }}
                                    </h3>
                                </div>

                                <!-- Datos de Contacto -->
                                <div class="p-5 space-y-3 text-sm text-gray-600">
                                    <div class="flex items-start gap-3">
                                        <span class="text-red-600 text-lg">📍</span>
                                        <span class="font-medium text-gray-800 text-xs sm:text-sm">{{ $branch->address }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-orange-500 text-lg">🕒</span>
                                        <span class="text-xs sm:text-sm">{{ $branch->schedule ?? 'Lun - Dom: 10:00 AM - 10:00 PM' }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-emerald-600 text-lg">📞</span>
                                        <a href="tel:{{ $branch->phone }}" class="text-xs sm:text-sm font-bold text-gray-900 hover:text-red-600">{{ $branch->phone ?? '618-123-4567' }}</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="p-5 pt-0 grid grid-cols-2 gap-2">
                                <a href="{{ $branch->google_maps_url ?? $branch->map_link ?? 'https://maps.google.com/?q=' . ($branch->latitude ?? 24.0277) . ',' . ($branch->longitude ?? -104.6532) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold px-3 py-2.5 text-xs transition">
                                    <span>🧭 Cómo llegar</span>
                                </a>
                                <a href="tel:{{ $branch->phone ?? '6181234567' }}" class="flex items-center justify-center gap-1.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-2.5 text-xs transition">
                                    <span>📞 Llamar</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-amber-50 rounded-3xl border border-amber-200 max-w-xl mx-auto p-6">
                    <span class="text-4xl block mb-2">📍</span>
                    <h3 class="text-lg font-bold text-amber-900">Ubicaciones en actualización</h3>
                    <p class="text-sm text-amber-800 mt-1">Estamos actualizando nuestra lista de sucursales activas en Durango. Contáctanos por WhatsApp para tu pedido.</p>
                </div>
            @endif

            <!-- Mapa Interactivo Leaflet / OpenStreetMap con Coordenadas GPS -->
            <div class="mt-8 rounded-3xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm overflow-hidden">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-950 flex items-center gap-2">
                            <span>🗺️</span> Mapa de Cobertura en Vivo (Durango)
                        </h3>
                        <p class="text-xs text-gray-500">Haz clic en cualquier marcador para ver horario, teléfono y trazar tu ruta GPS.</p>
                    </div>
                    <div id="map-branch-chips" class="flex flex-wrap gap-1.5">
                        <!-- Chips inyectados por JavaScript -->
                    </div>
                </div>

                <div id="landing-leaflet-map" class="h-96 w-full rounded-2xl bg-gray-100 border border-gray-200 z-10"></div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 text-white py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-600 text-white font-black text-xl">🍗</span>
                <div>
                    <span class="font-black text-lg block leading-tight">Pollo Feliz Durango</span>
                    <span class="text-xs text-gray-400">Tradición al Carbón · Durango, Dgo.</span>
                </div>
            </div>
            <p class="text-xs text-gray-500">
                &copy; {{ date('Y') }} Pollo Feliz Durango. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <!-- Script de Inicialización de Mapa usando @json($locations) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inyección limpia y segura del listado dinámico de sucursales desde Laravel
            const locationsData = @json($locations ?? []);

            if (!Array.isArray(locationsData) || locationsData.length === 0) {
                const mapEl = document.getElementById('landing-leaflet-map');
                if (mapEl) mapEl.style.display = 'none';
                return;
            }

            // Filtrar las que tienen coordenadas válidas
            const validLocations = locationsData.filter(loc => loc.latitude && loc.longitude);
            if (validLocations.length === 0) return;

            // Inicializar mapa centrado en la primera sucursal o centro de Durango
            const defaultLat = parseFloat(validLocations[0].latitude) || 24.0277;
            const defaultLng = parseFloat(validLocations[0].longitude) || -104.6532;
            const defaultZoom = validLocations[0].map_zoom || 13;

            const map = L.map('landing-leaflet-map', { scrollWheelZoom: false }).setView([defaultLat, defaultLng], defaultZoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            const customMarkerIcon = L.divIcon({
                className: 'custom-pollo-pin',
                html: '<div style="background-color:#dc2626; color:white; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 4px 10px rgba(0,0,0,0.3); border:2px solid white; cursor:pointer;">🍗</div>',
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -36]
            });

            const markers = [];
            const bounds = [];
            const chipsContainer = document.getElementById('map-branch-chips');

            validLocations.forEach((loc, idx) => {
                const lat = parseFloat(loc.latitude);
                const lng = parseFloat(loc.longitude);
                const name = loc.branch_name || loc.name || ('Sucursal #' + loc.id);
                const address = loc.address || '';
                const phone = loc.phone || '618-123-4567';
                const schedule = loc.schedule || 'Lun - Dom: 10:00 AM - 10:00 PM';
                const mapUrl = loc.google_maps_url || loc.map_link || ('https://maps.google.com/?q=' + lat + ',' + lng);

                bounds.push([lat, lng]);

                const popupContent = `
                    <div style="font-family: inherit; padding: 4px; max-width: 240px;">
                        <h4 style="font-weight: 900; margin: 0 0 4px; font-size: 14px; color: #111;">${name}</h4>
                        <p style="font-size: 11px; color: #555; margin: 0 0 6px;">📍 ${address}</p>
                        <p style="font-size: 11px; color: #555; margin: 0 0 8px;">🕒 ${schedule}</p>
                        <a href="${mapUrl}" target="_blank" style="display:inline-block; background-color:#dc2626; color:white; text-decoration:none; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:bold;">🧭 Cómo llegar</a>
                    </div>
                `;

                const marker = L.marker([lat, lng], { icon: customMarkerIcon }).addTo(map).bindPopup(popupContent);
                markers.push(marker);

                // Chip Selector
                if (chipsContainer) {
                    const chip = document.createElement('button');
                    chip.className = 'px-3 py-1 rounded-xl text-xs font-bold bg-gray-100 hover:bg-red-50 hover:text-red-600 text-gray-700 border border-gray-200 transition';
                    chip.textContent = name;
                    chip.onclick = function () {
                        map.flyTo([lat, lng], 16, { duration: 1.2 });
                        marker.openPopup();
                    };
                    chipsContainer.appendChild(chip);
                }
            });

            if (bounds.length > 1) {
                map.fitBounds(bounds, { padding: [40, 40] });
            }
        });
    </script>
</body>
</html>
