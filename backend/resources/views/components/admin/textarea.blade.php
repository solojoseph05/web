{{--
    Componente Blade reutilizable para áreas de texto (textarea) en Dark Mode del Panel Admin.
    Imprime el contenido sin espacios ni saltos de línea adicionales para evitar texto desplazado o en blanco.
--}}
@props([
    'name',
    'label' => null,
    'value' => '',
    'rows' => 4,
    'placeholder' => '',
    'required' => false,
    'hint' => null,
])

@php
    $textareaId = $attributes->get('id', 'field_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name));
    $baseClasses = 'w-full rounded-xl bg-[#161e2e] border border-slate-700 text-slate-100 placeholder-slate-400 px-4 py-3 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-none transition';
@endphp

<div class="space-y-1.5">
    @if($label)
        <label for="{{ $textareaId }}" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <textarea
        id="{{ $textareaId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => $baseClasses]) }}
    >{{ old($name, $value ?? '') }}</textarea>

    @if($hint)
        <p class="text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs font-semibold text-red-400">{{ $message }}</p>
    @enderror
</div>
