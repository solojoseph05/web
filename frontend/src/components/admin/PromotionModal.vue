<!-- src/components/admin/PromotionModal.vue -->
<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-xl rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 my-8 transition-all max-h-[92vh] flex flex-col overflow-hidden"
        role="dialog"
        aria-modal="true"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800 shrink-0">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 text-lg">
              🏷️
            </span>
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">
                {{ isEditing ? 'Editar Promoción' : 'Nueva Promoción' }}
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ isEditing ? 'Actualiza los términos, vigencia automática y descuentos' : 'Crea una oferta especial con auto-expiración para clientes de Pollo Feliz' }}
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

        <!-- Form Body (Scrollable) -->
        <form @submit.prevent="handleSubmit" class="mt-4 space-y-4 overflow-y-auto flex-1 pr-1">
          <!-- Title & Subtitle -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Título de la Promoción *
            </label>
            <input
              v-model="form.title"
              type="text"
              required
              placeholder="Ej. Martes de 2x1 en Pollo Entero al Carbón"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-red-500 focus:bg-white dark:focus:bg-gray-800 dark:focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white font-semibold transition"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Subtítulo / Bajada Atractiva
            </label>
            <input
              v-model="form.subtitle"
              type="text"
              placeholder="Ej. Válido en sucursales Centro y Polanco, incluye salsas y tortillas"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-red-500 focus:bg-white dark:focus:bg-gray-800 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
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
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-red-600 font-mono font-black tracking-wider uppercase focus:border-red-500 focus:bg-white dark:focus:bg-gray-800 dark:focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-red-400 transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Distintivo / Badge (Ej. "2x1", "Ahorra 30%")
              </label>
              <input
                v-model="form.badge"
                type="text"
                placeholder="Ej. 2x1 Martes, Nuevo, Exclusivo"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-red-500 focus:bg-white dark:focus:bg-gray-800 dark:focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
              />
            </div>
          </div>

          <!-- Discount % or Promo Price -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Porcentaje de Descuento (%)
              </label>
              <div class="relative">
                <input
                  v-model.number="form.discount_percentage"
                  type="number"
                  min="0"
                  max="100"
                  placeholder="Ej. 20"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                />
                <span class="absolute right-3.5 top-2 text-xs font-bold text-gray-400">%</span>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Precio Especial / Combo (MXN $)
              </label>
              <div class="relative">
                <span class="absolute left-3.5 top-2 text-xs font-bold text-gray-400">$</span>
                <input
                  v-model.number="form.promo_price"
                  type="number"
                  min="0"
                  placeholder="Ej. 199"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 pl-7 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                />
              </div>
            </div>
          </div>

          <!-- Vigencia Automática: valid_from y valid_until -->
          <div class="p-3.5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 space-y-2">
            <span class="block text-xs font-black text-amber-900 dark:text-amber-300">
              📅 Rango de Vigencia Automática (Se oculta al expirar)
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Válido Desde
                </label>
                <input
                  v-model="form.valid_from"
                  type="date"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                />
              </div>

              <div>
                <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Válido Hasta *
                </label>
                <input
                  v-model="form.valid_until"
                  type="date"
                  required
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                />
              </div>
            </div>
            <p class="text-[10px] text-amber-800 dark:text-amber-400">
              La plataforma pública filtra y muestra solo las promociones activas cuya fecha actual esté dentro del rango programado.
            </p>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Descripción de la Oferta *
            </label>
            <textarea
              v-model="form.description"
              rows="2"
              required
              placeholder="Detalla lo que incluye la promoción, paquetes o piezas de pollo..."
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
            ></textarea>
          </div>

          <!-- Terms & Conditions -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Términos & Condiciones
            </label>
            <textarea
              v-model="form.terms_conditions"
              rows="2"
              placeholder="Ej. No acumulable con otros cupones. Aplica solo para consumo en sucursal o para llevar..."
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
            ></textarea>
          </div>

          <!-- Image Selection & Upload Section -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
              Banner / Imagen de la Promoción *
            </label>
            <ImageUploader v-model="form.image_url" />
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
                <span class="block text-xs font-bold text-gray-800 dark:text-gray-200">Promoción Habilitada</span>
                <span class="block text-[10px] text-gray-400">Si está desmarcada, no se mostrará en la landing page aún si está en fecha</span>
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
import ImageUploader from '@/components/admin/ImageUploader.vue'

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

const isEditing = ref(false)
const isSubmitting = ref(false)

const form = ref({
  title: '',
  subtitle: '',
  description: '',
  code: '',
  badge: 'Oferta Especial',
  discount_percentage: null,
  promo_price: null,
  valid_from: new Date().toISOString().split('T')[0],
  valid_until: '2026-12-31',
  terms_conditions: '',
  image_url: '',
  is_active: true,
})

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      if (props.promoToEdit) {
        isEditing.value = true
        form.value = {
          title: props.promoToEdit.title || '',
          subtitle: props.promoToEdit.subtitle || '',
          description: props.promoToEdit.description || '',
          code: props.promoToEdit.code || '',
          badge: props.promoToEdit.badge || props.promoToEdit.badge_text || '',
          discount_percentage: props.promoToEdit.discount_percentage ?? null,
          promo_price: props.promoToEdit.promo_price ?? null,
          valid_from: props.promoToEdit.valid_from ? props.promoToEdit.valid_from.split('T')[0] : new Date().toISOString().split('T')[0],
          valid_until: props.promoToEdit.valid_until ? props.promoToEdit.valid_until.split('T')[0] : '2026-12-31',
          terms_conditions: props.promoToEdit.terms_conditions || '',
          image_url: props.promoToEdit.image_url || props.promoToEdit.banner_image || '',
          is_active: props.promoToEdit.is_active ?? true,
        }
      } else {
        isEditing.value = false
        form.value = {
          title: '',
          subtitle: '',
          description: '',
          code: 'POLLO' + Math.floor(10 + Math.random() * 90),
          badge: 'Oferta Especial',
          discount_percentage: null,
          promo_price: null,
          valid_from: new Date().toISOString().split('T')[0],
          valid_until: '2026-12-31',
          terms_conditions: 'Válido en sucursales autorizadas de Durango. No acumulable con otras ofertas.',
          image_url: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80',
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
      banner_image: form.value.image_url,
      badge_text: form.value.badge,
      code: form.value.code.trim().toUpperCase(),
      id: props.promoToEdit?.id,
    })
  } finally {
    isSubmitting.value = false
  }
}
</script>
