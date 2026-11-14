{{--
    Vista principal del Panel Administrativo en Blade para gestión integral de Pollo Feliz Durango.
    Consume los componentes oscuros reutilizables <x-admin.input>, <x-admin.textarea> y <x-admin.select>.
--}}
@extends('admin.layout')

@section('title', 'Panel Administrativo | Pollo Feliz Durango')

@section('content')
    <div class="max-w-6xl mx-auto space-y-8">
        {{-- Encabezado principal --}}
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-red-400">Administración Central</span>
                <h1 class="text-2xl sm:text-3xl font-black text-white mt-1">Panel Operativo — Pollo Feliz Durango</h1>
                <p class="text-sm text-slate-400 mt-1">
                    Interfaz Dark Mode unificada con persistencia en base de datos relacional y sincronización inmediata con la Landing Page.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url('/admin') }}" class="rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-black px-4 py-2.5 transition">
                    Abrir Consola SPA
                </a>
            </div>
        </header>

        {{-- Resumen de los 11 módulos del sistema --}}
        <section aria-label="Módulos del sistema" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $summaryCards = [
                    ['id' => 'modulo-landing', 'title' => 'Editar Landing', 'desc' => 'Títulos hero, descripción de bienvenida e imágenes globales.', 'count' => $stats['settings'] ?? 1],
                    ['id' => 'modulo-menu', 'title' => 'Menú', 'desc' => 'Platillos, paquetes familiares, categorías y precios vigentes.', 'count' => $stats['menu_items'] ?? 0],
                    ['id' => 'modulo-promociones', 'title' => 'Promociones', 'desc' => 'Ofertas activas, vigencia y etiquetas destacadas.', 'count' => $stats['promotions'] ?? 0],
                    ['id' => 'modulo-sucursales', 'title' => 'Sucursales', 'desc' => 'Direcciones, horarios, teléfonos y coordenadas GPS.', 'count' => $stats['branches'] ?? 0],
                    ['id' => 'modulo-vacantes', 'title' => 'Bolsa de Vacantes', 'desc' => 'Puestos disponibles, fotografías y requisitos por sucursal.', 'count' => $stats['jobs'] ?? 0],
                    ['id' => 'modulo-pedidos', 'title' => 'Pedidos en Vivo', 'desc' => 'Seguimiento de órdenes activas y estado de entrega.', 'count' => $stats['orders'] ?? 0],
                    ['id' => 'modulo-resenas', 'title' => 'Reseñas & Opiniones', 'desc' => 'Moderación de calificaciones y respuestas oficiales.', 'count' => $stats['reviews'] ?? 0],
                    ['id' => 'modulo-mensajes', 'title' => 'Mensajes', 'desc' => 'Consultas enviadas a soporte@pollofelizdgo.com y seguimiento.', 'count' => $stats['messages'] ?? 0],
                    ['id' => 'modulo-logs', 'title' => 'Logs del Sistema', 'desc' => 'Auditoría técnica, errores recientes e historial de cambios.', 'count' => $stats['logs'] ?? 0],
                    ['id' => 'modulo-usuarios', 'title' => 'Usuarios & Roles', 'desc' => 'Control RBAC (Super Admin, Editor, Soporte) y sesiones.', 'count' => $stats['users'] ?? 0],
                    ['id' => 'modulo-aprobaciones', 'title' => 'Aprobaciones & Cambios', 'desc' => 'Flujo de autorización y reversión segura de modificaciones.', 'count' => $stats['approvals'] ?? 0],
                ];
            @endphp

            @foreach($summaryCards as $card)
                <article id="{{ $card['id'] }}" class="rounded-2xl bg-[#111827] border border-slate-800 p-5 flex flex-col justify-between gap-3">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-white">{{ $card['title'] }}</h2>
                            <span class="px-2.5 py-0.5 rounded-lg bg-[#161e2e] border border-slate-700 text-xs font-bold text-slate-200">
                                {{ $card['count'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">{{ $card['desc'] }}</p>
                    </div>
                </article>
            @endforeach
        </section>

        {{-- Formulario estándar usando los componentes Dark Mode --}}
        <section class="rounded-2xl bg-[#111827] border border-slate-800 p-6">
            <h2 class="text-lg font-bold text-white mb-1">Actualización Rápida de Landing & Sucursal</h2>
            <p class="text-xs text-slate-400 mb-6">
                Todos los controles mantienen contraste oscuro uniforme (#161e2e) y protección CSRF.
            </p>

            <form action="{{ url('/api/admin/settings') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-admin.input
                        name="hero_title"
                        label="Título Principal (Hero)"
                        :value="$settings['hero_title'] ?? 'El Mejor Pollo Asado al Carbón de Durango'"
                        placeholder="Ej. El Mejor Pollo Asado al Carbón"
                        required
                    />

                    <x-admin.select
                        name="default_branch"
                        label="Sucursal Destacada"
                        :options="[
                            'centro' => 'Sucursal Centro Histórico',
                            'jardines' => 'Sucursal Jardines',
                            'coyoacan' => 'Sucursal Coyoacán',
                        ]"
                        :selected="$settings['default_branch'] ?? 'centro'"
                        placeholder="Selecciona una sucursal"
                    />
                </div>

                <x-admin.textarea
                    name="hero_description"
                    label="Descripción de Bienvenida"
                    :value="$settings['hero_description'] ?? ''"
                    placeholder="Describe la propuesta culinaria y tradición al carbón..."
                    rows="3"
                    required
                />

                <x-admin.input
                    name="hero_banner"
                    type="file"
                    label="Fotografía Promocional (WEBP, JPG o PNG máx. 2MB)"
                    accept="image/webp,image/jpeg,image/png"
                    hint="Al reemplazar la imagen, el archivo anterior se limpia del almacenamiento público."
                />

                <div class="flex justify-end pt-2">
                    <button type="submit" class="rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-6 py-3 transition">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
