{{--
    Componente Blade reutilizable para campos de entrada (input) en Dark Mode del Panel Admin.
    Soporta texto, correo, numérico, archivo (con botón rojo oscuro) y validación automática.
--}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'hint' => null,
])

@php
    $inputId = $attributes->get('id', 'field_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name));
    $baseClasses = 'w-full rounded-xl bg-[#161e2e] border border-slate-700 text-slate-100 placeholder-slate-400 px-4 py-3 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-none transition';
    if ($type === 'file') {
        $baseClasses .= ' file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-red-600 file:text-white hover:file:bg-red-700 cursor-pointer';
    }
@endphp

<div class="space-y-1.5">
    @if($label)
        <label for="{{ $inputId }}" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if($type !== 'file' && $type !== 'password')
            value="{{ old($name, $value ?? '') }}"
        @endif
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => $baseClasses]) }}
    >

    @if($hint)
        <p class="text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs font-semibold text-red-400">{{ $message }}</p>
    @enderror
</div>
