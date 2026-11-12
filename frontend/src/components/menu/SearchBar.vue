<template>
  <div class="w-full space-y-2.5">
    <!-- Main Search Input Bar -->
    <div class="relative flex items-center w-full">
      <div class="pointer-events-none absolute left-4 flex items-center justify-center text-red-600 dark:text-red-400">
        <MagnifyingGlassIcon class="h-5 w-5" />
      </div>

      <input
        ref="searchInputRef"
        :value="modelValue"
        @input="onInput"
        @keydown.esc="clearSearch"
        type="text"
        :placeholder="placeholder"
        class="w-full rounded-2xl border border-gray-200 bg-white py-3.5 pl-12 pr-28 text-sm text-gray-900 shadow-sm transition-all duration-200 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-red-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-red-500 dark:focus:ring-red-500/20"
        aria-label="Buscar alimentos en el menú por nombre"
      />

      <!-- Right controls: Match count badge + Clear button -->
      <div class="absolute right-3 flex items-center gap-2">
        <span
          v-if="modelValue && totalMatches !== null"
          class="hidden sm:inline-flex items-center rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold text-red-600 dark:bg-red-950/60 dark:text-red-400"
        >
          {{ totalMatches }} {{ totalMatches === 1 ? 'resultado' : 'resultados' }}
        </span>

        <button
          v-if="modelValue"
          @click="clearSearch"
          type="button"
          class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 transition dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 cursor-pointer"
          title="Limpiar búsqueda (Esc)"
          aria-label="Limpiar búsqueda"
        >
          <XMarkIcon class="h-4 w-4" />
        </button>
      </div>
    </div>

    <!-- Quick Search Suggestions / Popular Tags -->
    <div v-if="showSuggestions && suggestions.length > 0" class="flex flex-wrap items-center gap-1.5 text-xs">
      <span class="text-gray-400 dark:text-gray-500 text-[11px] font-medium mr-1">Búsquedas rápidas:</span>
      <button
        v-for="tag in suggestions"
        :key="tag"
        @click="setQuery(tag)"
        type="button"
        class="rounded-lg px-2.5 py-1 text-[11px] font-semibold transition cursor-pointer"
        :class="modelValue.toLowerCase() === tag.toLowerCase()
          ? 'bg-red-600 text-white'
          : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'"
      >
        {{ tag }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: 'Buscar por nombre (ej. Pollo Entero, Paquete Familiar, Papas, Horchata)...',
  },
  totalMatches: {
    type: Number,
    default: null,
  },
  showSuggestions: {
    type: Boolean,
    default: true,
  },
  suggestions: {
    type: Array,
    default: () => ['Pollo Entero', 'Paquete Familiar', 'Papas Francesa', 'Tacos', 'Horchata', 'Flan'],
  },
})

const emit = defineEmits(['update:modelValue', 'clear'])
const searchInputRef = ref(null)

const onInput = (event) => {
  emit('update:modelValue', event.target.value)
}

const clearSearch = () => {
  emit('update:modelValue', '')
  emit('clear')
  if (searchInputRef.value) {
    searchInputRef.value.focus()
  }
}

const setQuery = (tag) => {
  if (props.modelValue.toLowerCase() === tag.toLowerCase()) {
    emit('update:modelValue', '')
  } else {
    emit('update:modelValue', tag)
  }
}
</script>
