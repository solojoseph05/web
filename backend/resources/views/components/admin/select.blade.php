{{--
    Componente Blade reutilizable para selectores (select) en Dark Mode del Panel Admin.
    Asegura contraste oscuro uniforme tanto en el selector como en cada opción desplegable.
--}}
@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'hint' => null,
])

@php
    $selectId = $attributes->get('id', 'field_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name));
    $currentValue = old($name, $selected);
    $baseClasses = 'w-full rounded-xl bg-[#161e2e] border border-slate-700 text-slate-100 placeholder-slate-400 px-4 py-3 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-none transition';
@endphp

<div class="space-y-1.5">
    @if($label)
        <label for="{{ $selectId }}" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select
        id="{{ $selectId }}"
        name="{{ $name }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => $baseClasses]) }}
    >
        @if($placeholder)
            <option value="" class="bg-[#161e2e] text-slate-400">{{ $placeholder }}</option>
        @endif

        @foreach($options as $optionValue => $optionLabel)
            <option
                value="{{ $optionValue }}"
                class="bg-[#161e2e] text-slate-100"
                @selected((string) $currentValue === (string) $optionValue)
            >
                {{ $optionLabel }}
            </option>
        @endforeach

        {{ $slot }}
    </select>

    @if($hint)
        <p class="text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs font-semibold text-red-400">{{ $message }}</p>
    @enderror
</div>
