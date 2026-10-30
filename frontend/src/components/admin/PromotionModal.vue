<!-- src/components/admin/PromotionModal.vue -->
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
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 text-lg">
              🏷️
            </span>
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">
                {{ isEditing ? 'Editar Promoción' : 'Nueva Promoción' }}
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ isEditing ? 'Actualiza los términos, cupón o vigencia' : 'Crea una nueva oferta especial para clientes' }}
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
          <!-- Title -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Título de la Promoción *
            </label>
            <input
              v-model="form.title"
              type="text"
              required
              placeholder="Ej. 2x1 en Pollo Entero los Martes"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white font-semibold"
            />
          </div>

          <!-- Coupon Code & Badge -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Código de Cupón *
              </label>
              <input
                v-model="form.code"
                type="text"
                required
                placeholder="Ej. MARTES2X1"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-red-600 font-mono font-black tracking-wider uppercase focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-red-400"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Etiqueta / Badge
              </label>
              <input
                v-model="form.badge"
                type="text"
                placeholder="Ej. Oferta Especial, 2x1, Exclusivo"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
            </div>
          </div>

          <!-- Validity Date -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Válido Hasta
            </label>
            <input
              v-model="form.valid_until"
              type="text"
              placeholder="Ej. 2026-12-31 o 'Todos los martes'"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Descripción de la Oferta *
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              required
              placeholder="Detalla lo que incluye la promoción, restricciones o días aplicables..."
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            ></textarea>
          </div>

          <!-- Image Selection & Upload Section -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Imagen Promocional
              </label>
              <span class="text-[10px] text-gray-400">Subir archivo o pegar URL</span>
            </div>

            <!-- Upload & Presets Toolbar -->
            <div class="flex flex-wrap items-center gap-2">
              <input
                ref="fileInputRef"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleFileUpload"
              />
              <button
                type="button"
                @click="triggerFileInput"
                :disabled="isUploadingImage"
                class="rounded-xl bg-gray-100 hover:bg-gray-200 px-3 py-2 text-xs font-bold text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200 transition cursor-pointer flex items-center gap-1.5"
              >
                <svg v-if="!isUploadingImage" class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span v-else class="animate-spin inline-block h-3.5 w-3.5 border-2 border-red-600 border-t-transparent rounded-full"></span>
                <span>{{ isUploadingImage ? 'Cargando imagen...' : '📁 Subir desde archivo' }}</span>
              </button>

              <button
                type="button"
                @click="showPresets = !showPresets"
                class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition cursor-pointer"
              >
                🖼️ Fotos recomendadas ({{ SUGGESTED_PROMO_IMAGES.length }})
              </button>

              <button
                v-if="form.image_url"
                type="button"
                @click="clearImage"
                class="rounded-xl px-2.5 py-2 text-xs font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer ml-auto"
                title="Quitar imagen actual"
              >
                ✕ Quitar
              </button>
            </div>

            <!-- Suggested Photos Drawer -->
            <div
              v-if="showPresets"
              class="grid grid-cols-4 gap-2 p-3 rounded-2xl bg-gray-50 dark:bg-gray-800/70 border border-gray-200 dark:border-gray-700"
            >
              <button
                v-for="(item, idx) in SUGGESTED_PROMO_IMAGES"
                :key="idx"
                type="button"
                @click="selectPreset(item.url)"
                class="group relative h-16 rounded-xl overflow-hidden border-2 transition cursor-pointer"
                :class="form.image_url === item.url ? 'border-red-600 shadow-xs' : 'border-transparent hover:border-gray-300'"
                :title="item.label"
              >
                <img :src="item.url" :alt="item.label" class="h-full w-full object-cover group-hover:scale-105 transition" />
                <span class="absolute inset-x-0 bottom-0 bg-black/70 text-[9px] text-white truncate px-1 py-0.5 text-center font-bold">
                  {{ item.label }}
                </span>
              </button>
            </div>

            <!-- URL Input -->
            <input
              v-model="form.image_url"
              type="text"
              placeholder="https://images.unsplash.com/... o pega datos de imagen"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 text-xs text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />

            <!-- Preview box -->
            <div v-if="form.image_url" class="mt-2 flex items-center gap-3 p-2.5 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700">
              <img
                :src="form.image_url"
                alt="Vista previa de promoción"
                class="h-14 w-14 rounded-xl object-cover shrink-0 shadow-xs border border-gray-200 dark:border-gray-700"
                @error="imageError = true"
                @load="imageError = false"
              />
              <div class="text-xs overflow-hidden">
                <span v-if="!imageError" class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                  ✓ Imagen lista para la promoción
                </span>
                <span v-else class="text-red-500 font-bold">
                  ⚠ No se pudo visualizar el enlace (se usará como texto)
                </span>
                <p class="text-[10px] text-gray-400 truncate max-w-[280px] mt-0.5">{{ form.image_url }}</p>
              </div>
            </div>
          </div>

          <!-- Toggle: is_active -->
          <div class="pt-1">
            <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4 rounded text-red-600 focus:ring-red-500"
              />
              <div>
                <span class="block text-xs font-bold text-gray-800 dark:text-gray-200">Promoción Activa</span>
                <span class="block text-[10px] text-gray-400">Visible y aplicable por los clientes en la tienda</span>
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
              <span>{{ isEditing ? 'Guardar Cambios' : 'Crear Promoción' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  promoToEdit: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'save'])

const fileInputRef = ref(null)
const isEditing = ref(false)
const isSubmitting = ref(false)
const isUploadingImage = ref(false)
const imageError = ref(false)
const showPresets = ref(false)

const SUGGESTED_PROMO_IMAGES = [
  {
    label: '2x1 Pollo',
    url: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80',
  },
  {
    label: 'Al Carbón',
    url: 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=600&auto=format&fit=crop&q=80',
  },
  {
    label: 'Banquete',
    url: 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=600&auto=format&fit=crop&q=80',
  },
  {
    label: 'Tacos & Extras',
    url: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&auto=format&fit=crop&q=80',
  },
]

const form = ref({
  title: '',
  description: '',
  code: '',
  badge: 'Oferta Especial',
  valid_until: '2026-12-31',
  image_url: '',
  is_active: true,
})

const triggerFileInput = () => {
  fileInputRef.value?.click()
}

const handleFileUpload = (event) => {
  const file = event.target?.files?.[0]
  if (!file) return

  isUploadingImage.value = true
  imageError.value = false

  try {
    const reader = new FileReader()
    reader.onload = (e) => {
      form.value.image_url = e.target.result
      isUploadingImage.value = false
    }
    reader.onerror = () => {
      isUploadingImage.value = false
    }
    reader.readAsDataURL(file)
  } catch (err) {
    console.error('Error reading file:', err)
    isUploadingImage.value = false
  }
}

const selectPreset = (url) => {
  form.value.image_url = url
  imageError.value = false
  showPresets.value = false
}

const clearImage = () => {
  form.value.image_url = ''
  imageError.value = false
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      imageError.value = false
      showPresets.value = false
      if (props.promoToEdit) {
        isEditing.value = true
        form.value = {
          title: props.promoToEdit.title || '',
          description: props.promoToEdit.description || '',
          code: props.promoToEdit.code || '',
          badge: props.promoToEdit.badge || '',
          valid_until: props.promoToEdit.valid_until || '',
          image_url: props.promoToEdit.image_url || '',
          is_active: props.promoToEdit.is_active ?? true,
        }
      } else {
        isEditing.value = false
        form.value = {
          title: '',
          description: '',
          code: 'POLLO' + Math.floor(10 + Math.random() * 90),
          badge: 'Oferta Especial',
          valid_until: '2026-12-31',
          image_url: SUGGESTED_PROMO_IMAGES[0].url,
          is_active: true,
        }
      }
    }
  }
)

const handleSubmit = async () => {
  if (!form.value.title.trim() || !form.value.code.trim()) return
  isSubmitting.value = true
  try {
    emit('save', {
      ...form.value,
      code: form.value.code.trim().toUpperCase(),
      id: props.promoToEdit?.id,
    })
  } finally {
    isSubmitting.value = false
  }
}
</script>
