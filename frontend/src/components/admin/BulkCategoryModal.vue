<!-- src/components/admin/BulkCategoryModal.vue -->
<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      @click.self="emit('close')"
    >
      <div
        class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-7 shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-4"
        role="dialog"
        aria-modal="true"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400 text-lg">
              🏷️
            </span>
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">
                Cambiar Categoría en Lote
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                Se actualizarán <strong>{{ items.length }}</strong> platillos seleccionados
              </p>
            </div>
          </div>
          <button
            @click="emit('close')"
            class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200 cursor-pointer"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Selected Items Preview Pill List -->
        <div>
          <span class="text-xs font-bold text-gray-700 dark:text-gray-300 block mb-1.5">
            Platillos a reclasificar:
          </span>
          <div class="max-h-24 overflow-y-auto rounded-2xl bg-gray-50 p-2.5 dark:bg-gray-800/50 flex flex-wrap gap-1.5 border border-gray-200/70 dark:border-gray-700">
            <span
              v-for="item in items"
              :key="item.id"
              class="rounded-lg bg-white px-2 py-0.5 text-[11px] font-semibold text-gray-700 shadow-xs dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700"
            >
              {{ item.name }}
            </span>
          </div>
        </div>

        <!-- Category Picker -->
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
              Nueva Categoría *
            </label>
            <button
              type="button"
              @click="isCustom = !isCustom"
              class="text-[11px] font-semibold text-red-600 dark:text-red-400 hover:underline cursor-pointer"
            >
              {{ isCustom ? 'Elegir de lista' : '+ Nueva categoría personalizada' }}
            </button>
          </div>

          <div v-if="!isCustom">
            <select
              v-model="selectedCategory"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 font-bold focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white cursor-pointer"
            >
              <option value="" disabled>Selecciona una categoría...</option>
              <optgroup label="Categorías Principales">
                <option value="Combos">🍱 Combos</option>
                <option value="Bebidas">🥤 Bebidas (Drinks)</option>
                <option value="Extras">🍟 Extras (Complementos)</option>
              </optgroup>
              <optgroup label="Especialidades">
                <option value="Pollo Entero">🍗 Pollo Entero</option>
                <option value="Medio Pollo">🍗 Medio Pollo</option>
                <option value="Paquetes">📦 Paquetes</option>
                <option value="Complementos">🥔 Complementos</option>
                <option value="Tacos">🌮 Tacos</option>
                <option value="Ensaladas">🥗 Ensaladas</option>
                <option value="Postres">🍮 Postres</option>
              </optgroup>
              <optgroup v-if="otherCategories.length > 0" label="Otras Categorías">
                <option v-for="cat in otherCategories" :key="cat" :value="cat">
                  📁 {{ cat }}
                </option>
              </optgroup>
            </select>
          </div>

          <div v-else>
            <input
              v-model="customCategoryText"
              type="text"
              placeholder="Ej. Combos Infantiles, Desayunos, Salsas..."
              class="w-full rounded-xl border border-red-300 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 font-bold focus:border-red-600 focus:outline-hidden dark:border-red-800 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <!-- Quick presets pills -->
          <div class="flex flex-wrap gap-1.5 pt-1">
            <button
              v-for="preset in ['Combos', 'Bebidas', 'Extras', 'Pollo Entero', 'Medio Pollo', 'Tacos']"
              :key="preset"
              type="button"
              @click="applyPreset(preset)"
              class="rounded-lg px-2.5 py-1 text-[11px] font-bold border transition cursor-pointer"
              :class="effectiveCategory === preset
                ? 'bg-red-600 text-white border-red-600 shadow-xs'
                : 'bg-gray-50 text-gray-700 border-gray-200 hover:border-red-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300'"
            >
              {{ preset }}
            </button>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-800">
          <button
            type="button"
            @click="emit('close')"
            class="rounded-xl border border-gray-200 px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition cursor-pointer"
          >
            Cancelar
          </button>
          <button
            type="button"
            :disabled="!effectiveCategory || isSubmitting"
            @click="handleSubmit"
            class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:from-red-700 hover:to-red-800 disabled:opacity-50 transition cursor-pointer flex items-center gap-2"
          >
            <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span>Aplicar a {{ items.length }} Platillos</span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  items: {
    type: Array,
    default: () => [],
  },
  existingCategories: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['close', 'confirm'])

const isCustom = ref(false)
const selectedCategory = ref('Combos')
const customCategoryText = ref('')
const isSubmitting = ref(false)

const standardPresets = [
  'Combos',
  'Bebidas',
  'Extras',
  'Pollo Entero',
  'Medio Pollo',
  'Paquetes',
  'Complementos',
  'Tacos',
  'Ensaladas',
  'Postres',
]

const otherCategories = computed(() => {
  return (props.existingCategories || []).filter(
    (c) => c && !standardPresets.includes(c)
  )
})

const effectiveCategory = computed(() => {
  if (isCustom.value) {
    return customCategoryText.value.trim()
  }
  return selectedCategory.value
})

const applyPreset = (name) => {
  isCustom.value = false
  selectedCategory.value = name
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      isCustom.value = false
      selectedCategory.value = 'Combos'
      customCategoryText.value = ''
      isSubmitting.value = false
    }
  }
)

const handleSubmit = async () => {
  if (!effectiveCategory.value) return
  isSubmitting.value = true
  try {
    emit('confirm', effectiveCategory.value)
  } finally {
    isSubmitting.value = false
  }
}
</script>
