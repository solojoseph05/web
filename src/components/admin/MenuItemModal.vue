<!-- src/components/admin/MenuItemModal.vue -->
<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 my-8 transition-all"
        role="dialog"
        aria-modal="true"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400 text-lg">
              🍗
            </span>
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">
                {{ isEditing ? 'Editar Platillo' : 'Nuevo Platillo del Menú' }}
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ isEditing ? 'Actualiza los datos, categoría y precio del platillo' : 'Agrega un nuevo producto categorizado al catálogo de Pollo Feliz' }}
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

        <!-- Form Body -->
        <form @submit.prevent="handleSubmit" class="mt-5 space-y-4">
          <!-- Dish Name -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Nombre del Platillo *
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="Ej. Combo Familiar Supremo, Refresco 2L, Papas Extra..."
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <!-- Categorization Section -->
          <div class="rounded-2xl border border-red-100 bg-red-50/40 p-3.5 dark:border-red-950/60 dark:bg-red-950/20 space-y-2.5">
            <div class="flex items-center justify-between">
              <label class="block text-xs font-bold text-red-900 dark:text-red-200">
                Categoría del Menú *
              </label>
              <button
                type="button"
                @click="isCustomCategory = !isCustomCategory"
                class="text-[11px] font-semibold text-red-600 dark:text-red-400 hover:underline cursor-pointer"
              >
                {{ isCustomCategory ? 'Elegir de lista predefinida' : '+ Escribir categoría personalizada' }}
              </button>
            </div>

            <!-- Standard Category Select or Custom Text Input -->
            <div v-if="!isCustomCategory" class="relative">
              <select
                v-model="form.category"
                required
                class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 font-semibold focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              >
                <optgroup label="Categorías Principales (Sugeridas)">
                  <option value="Combos">🍱 Combos</option>
                  <option value="Bebidas">🥤 Bebidas (Drinks)</option>
                  <option value="Extras">🍟 Extras (Complementos)</option>
                </optgroup>
                <optgroup label="Especialidades de Pollo">
                  <option value="Pollo Entero">🍗 Pollo Entero</option>
                  <option value="Medio Pollo">🍗 Medio Pollo</option>
                  <option value="Paquetes">📦 Paquetes</option>
                  <option value="Tacos">🌮 Tacos</option>
                  <option value="Ensaladas">🥗 Ensaladas</option>
                  <option value="Postres">🍮 Postres</option>
                </optgroup>
                <optgroup v-if="extraCategoryOptions.length > 0" label="Otras Categorías Existentes">
                  <option v-for="cat in extraCategoryOptions" :key="cat" :value="cat">
                    📁 {{ cat }}
                  </option>
                </optgroup>
              </select>
            </div>

            <div v-else class="space-y-1">
              <input
                v-model="customCategoryText"
                type="text"
                required
                placeholder="Escribe el nombre de la categoría (ej. Combos, Bebidas, Extras, Salsas...)"
                class="w-full rounded-xl border border-red-300 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 font-semibold focus:border-red-600 focus:outline-hidden dark:border-red-800 dark:bg-gray-800 dark:text-white"
                @input="form.category = customCategoryText"
              />
              <p class="text-[10px] text-gray-500 dark:text-gray-400">
                Esta categoría se creará automáticamente y estará disponible como filtro en el menú.
              </p>
            </div>

            <!-- Quick Category Pill Selector -->
            <div>
              <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 block mb-1.5">
                Acceso rápido:
              </span>
              <div class="flex flex-wrap gap-1.5">
                <button
                  v-for="preset in quickCategoryPresets"
                  :key="preset.id"
                  type="button"
                  @click="applyQuickCategory(preset.id)"
                  class="rounded-lg px-2.5 py-1 text-[11px] font-bold transition-all cursor-pointer border"
                  :class="form.category === preset.id
                    ? 'bg-red-600 text-white border-red-600 shadow-xs'
                    : 'bg-white text-gray-700 border-gray-200 hover:border-red-300 hover:bg-red-50/50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300'"
                >
                  {{ preset.icon }} {{ preset.label }}
                </button>
              </div>
            </div>
          </div>

          <!-- Price & Promotional Tag Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Precio (MXN $) *
              </label>
              <div class="relative">
                <span class="absolute left-3.5 top-2.5 text-xs font-bold text-gray-400">$</span>
                <input
                  v-model.number="form.price"
                  type="number"
                  min="1"
                  step="1"
                  required
                  placeholder="180"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 pl-8 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white font-semibold"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Etiqueta Promocional (Opcional)
              </label>
              <input
                v-model="form.badge"
                type="text"
                placeholder="Ej. Más vendido, Especial, Nuevo..."
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Descripción del Platillo
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Describe las porciones, acompañamientos incluidos, aderezos o tamaño..."
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            ></textarea>
          </div>

          <!-- Image URL & Live Preview -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              URL de la Imagen
            </label>
            <div class="flex gap-2">
              <input
                v-model="form.image_url"
                type="url"
                placeholder="https://images.unsplash.com/..."
                class="flex-1 rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 text-xs text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
              <button
                type="button"
                @click="usePresetImage"
                class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition cursor-pointer shrink-0"
                title="Cargar foto sugerida según categoría seleccionada"
              >
                Foto sugerida
              </button>
            </div>

            <!-- Image preview box -->
            <div v-if="form.image_url" class="mt-2 flex items-center gap-3 p-2 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700">
              <img
                :src="form.image_url"
                alt="Vista previa"
                class="h-12 w-12 rounded-lg object-cover"
                @error="imageError = true"
                @load="imageError = false"
              />
              <div class="text-xs">
                <span v-if="!imageError" class="text-green-600 font-semibold">✓ Imagen válida</span>
                <span v-else class="text-red-500 font-semibold">⚠ No se pudo cargar la imagen</span>
                <p class="text-[10px] text-gray-400 truncate max-w-[280px]">{{ form.image_url }}</p>
              </div>
            </div>
          </div>

          <!-- Toggles: is_active & is_featured -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
            <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4 rounded text-red-600 focus:ring-red-500"
              />
              <div>
                <span class="block text-xs font-bold text-gray-800 dark:text-gray-200">Disponible para Venta</span>
                <span class="block text-[10px] text-gray-400">Visible en el catálogo público</span>
              </div>
            </label>

            <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <input
                v-model="form.is_featured"
                type="checkbox"
                class="h-4 w-4 rounded text-red-600 focus:ring-red-500"
              />
              <div>
                <span class="block text-xs font-bold text-gray-800 dark:text-gray-200">Platillo Destacado</span>
                <span class="block text-[10px] text-gray-400">Mostrar en el inicio de la web</span>
              </div>
            </label>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
            <button
              type="button"
              @click="emit('close')"
              class="rounded-xl border border-gray-200 px-4 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 cursor-pointer transition"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:from-red-700 hover:to-red-800 disabled:opacity-50 transition cursor-pointer flex items-center gap-2"
            >
              <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <span>{{ isEditing ? 'Guardar Cambios' : 'Crear Platillo' }}</span>
            </button>
          </div>
        </form>
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
  itemToEdit: {
    type: Object,
    default: null,
  },
  existingCategories: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['close', 'save'])

const isEditing = ref(false)
const isSubmitting = ref(false)
const imageError = ref(false)
const isCustomCategory = ref(false)
const customCategoryText = ref('')

const quickCategoryPresets = [
  { id: 'Combos', label: 'Combos', icon: '🍱' },
  { id: 'Bebidas', label: 'Bebidas (Drinks)', icon: '🥤' },
  { id: 'Extras', label: 'Extras', icon: '🍟' },
  { id: 'Pollo Entero', label: 'Pollo Entero', icon: '🍗' },
  { id: 'Medio Pollo', label: 'Medio Pollo', icon: '🍗' },
  { id: 'Paquetes', label: 'Paquetes', icon: '📦' },
  { id: 'Complementos', label: 'Complementos', icon: '🥔' },
  { id: 'Tacos', label: 'Tacos', icon: '🌮' },
  { id: 'Ensaladas', label: 'Ensaladas', icon: '🥗' },
  { id: 'Postres', label: 'Postres', icon: '🍮' },
]

const PRESET_IMAGES = {
  'Combos': 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80',
  'Paquetes': 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80',
  'Bebidas': 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&auto=format&fit=crop&q=80',
  'Drinks': 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&auto=format&fit=crop&q=80',
  'Extras': 'https://images.unsplash.com/photo-1576107232684-1279f3908594?w=600&auto=format&fit=crop&q=80',
  'Complementos': 'https://images.unsplash.com/photo-1576107232684-1279f3908594?w=600&auto=format&fit=crop&q=80',
  'Pollo Entero': 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=600&auto=format&fit=crop&q=80',
  'Medio Pollo': 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=600&auto=format&fit=crop&q=80',
  'Tacos': 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=600&auto=format&fit=crop&q=80',
  'Ensaladas': 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&auto=format&fit=crop&q=80',
  'Postres': 'https://images.unsplash.com/photo-1551106652-a5bcf4b29ab6?w=600&auto=format&fit=crop&q=80',
}

const form = ref({
  name: '',
  category: 'Combos',
  price: 180,
  badge: '',
  description: '',
  image_url: '',
  is_active: true,
  is_featured: false,
})

const extraCategoryOptions = computed(() => {
  const basePresetIds = [
    'Combos',
    'Bebidas',
    'Drinks',
    'Extras',
    'Pollo Entero',
    'Medio Pollo',
    'Paquetes',
    'Complementos',
    'Tacos',
    'Ensaladas',
    'Postres',
  ]
  return (props.existingCategories || []).filter(
    (c) => c && !basePresetIds.includes(c)
  )
})

const applyQuickCategory = (categoryName) => {
  isCustomCategory.value = false
  form.value.category = categoryName
  if (!form.value.image_url || Object.values(PRESET_IMAGES).includes(form.value.image_url)) {
    usePresetImage()
  }
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      imageError.value = false
      if (props.itemToEdit) {
        isEditing.value = true
        const cat = props.itemToEdit.category || 'Combos'
        const isPreset = quickCategoryPresets.some((p) => p.id === cat) || extraCategoryOptions.value.includes(cat)
        isCustomCategory.value = !isPreset
        customCategoryText.value = isPreset ? '' : cat

        form.value = {
          name: props.itemToEdit.name || '',
          category: cat,
          price: props.itemToEdit.price || 0,
          badge: props.itemToEdit.badge || '',
          description: props.itemToEdit.description || '',
          image_url: props.itemToEdit.image_url || '',
          is_active: props.itemToEdit.is_active ?? true,
          is_featured: props.itemToEdit.is_featured ?? false,
        }
      } else {
        isEditing.value = false
        isCustomCategory.value = false
        customCategoryText.value = ''
        form.value = {
          name: '',
          category: 'Combos',
          price: 180,
          badge: '',
          description: '',
          image_url: PRESET_IMAGES['Combos'],
          is_active: true,
          is_featured: false,
        }
      }
    }
  }
)

const usePresetImage = () => {
  const cat = form.value.category
  if (PRESET_IMAGES[cat]) {
    form.value.image_url = PRESET_IMAGES[cat]
  } else if (cat.toLowerCase().includes('combo')) {
    form.value.image_url = PRESET_IMAGES['Combos']
  } else if (cat.toLowerCase().includes('bebida') || cat.toLowerCase().includes('drink')) {
    form.value.image_url = PRESET_IMAGES['Bebidas']
  } else if (cat.toLowerCase().includes('extra') || cat.toLowerCase().includes('complemento')) {
    form.value.image_url = PRESET_IMAGES['Extras']
  } else {
    form.value.image_url = 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=600&auto=format&fit=crop&q=80'
  }
}

const handleSubmit = async () => {
  if (isCustomCategory.value && customCategoryText.value.trim()) {
    form.value.category = customCategoryText.value.trim()
  }

  if (!form.value.name.trim() || form.value.price <= 0 || !form.value.category.trim()) return

  isSubmitting.value = true
  try {
    emit('save', {
      ...form.value,
      id: props.itemToEdit?.id,
    })
  } finally {
    isSubmitting.value = false
  }
}
</script>
