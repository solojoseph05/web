<!-- src/components/landing/ReviewModal.vue -->
<template>
  <Transition name="fade">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
      @click.self="handleClose"
    >
      <div
        class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 transition-all my-8 max-h-[90vh] overflow-y-auto"
      >
        <!-- Close button -->
        <button
          @click="handleClose"
          class="absolute top-5 right-5 h-9 w-9 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 flex items-center justify-center transition cursor-pointer"
          aria-label="Cerrar modal"
        >
          ✕
        </button>

        <!-- Header -->
        <div class="mb-6">
          <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 mb-2">
            ⭐ Tu opinión nos hace mejores
          </span>
          <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white">
            Califica tu Experiencia
          </h2>
          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
            Comparte tu opinión sobre el sabor de nuestro pollo asado, la atención y el servicio.
          </p>
        </div>

        <!-- Google Reviews Option Banner -->
        <div class="mb-5 p-3 rounded-2xl bg-blue-50/80 border border-blue-100 dark:bg-blue-950/30 dark:border-blue-900/40 flex items-center justify-between gap-3">
          <div class="flex items-center gap-2 text-xs">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
              <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.14C3.31 21.45 7.37 24 12 24z"/>
              <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.59H1.27C.46 8.21 0 10.05 0 12s.46 3.79 1.27 5.41l4.01-3.14z"/>
              <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.31 2.55 1.27 6.59l4.01 3.14c.95-2.83 3.6-4.98 6.72-4.98z"/>
            </svg>
            <span class="text-gray-700 dark:text-gray-300 font-medium">¿Prefieres calificar en Google Maps?</span>
          </div>
          <a
            :href="googleReviewsUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shrink-0 transition"
          >
            Abrir Google ↗
          </a>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Interactive Stars Rating Picker -->
          <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/60 dark:bg-amber-950/20 dark:border-amber-900/40 text-center space-y-2">
            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 block">
              ¿Cuántas estrellas le das a Pollo Feliz?
            </span>
            <div class="flex items-center justify-center gap-2">
              <button
                v-for="star in 5"
                :key="star"
                type="button"
                @mouseenter="hoverRating = star"
                @mouseleave="hoverRating = 0"
                @click="formData.rating = star"
                class="text-3xl sm:text-4xl transition-transform hover:scale-125 focus:outline-hidden cursor-pointer"
                :title="getRatingLabel(star)"
              >
                <span v-if="star <= (hoverRating || formData.rating)" class="text-amber-400 drop-shadow-xs">★</span>
                <span v-else class="text-gray-300 dark:text-gray-700">★</span>
              </button>
            </div>
            <p class="text-xs font-extrabold text-amber-800 dark:text-amber-300 min-h-[18px]">
              {{ getRatingLabel(hoverRating || formData.rating) }}
            </p>
          </div>

          <!-- Customer Name -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
              Tu Nombre Completo *
            </label>
            <input
              v-model="formData.customer_name"
              type="text"
              required
              placeholder="Ej. Martha Salcido"
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <!-- Branch and Favorite Dish Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                Sucursal visitada
              </label>
              <select
                v-model="formData.branch"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white cursor-pointer"
              >
                <option value="Sucursal Centro Histórico">Sucursal Centro Histórico</option>
                <option value="Sucursal Jardines">Sucursal Jardines</option>
                <option value="Sucursal Coyoacán">Sucursal Coyoacán</option>
                <option value="Servicio a Domicilio">Servicio a Domicilio</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                Platillo favorito (opcional)
              </label>
              <input
                v-model="formData.favorite_dish"
                type="text"
                placeholder="Ej. Paquete Familiar"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
            </div>
          </div>

          <!-- Quick Praise Tags Selection -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
              ¿Qué fue lo que más te gustó? (Elige las que apliquen)
            </label>
            <div class="flex flex-wrap gap-1.5">
              <button
                v-for="tag in availableTags"
                :key="tag"
                type="button"
                @click="toggleTag(tag)"
                class="rounded-full px-3 py-1 text-xs font-semibold transition cursor-pointer border"
                :class="formData.tags.includes(tag)
                  ? 'bg-red-600 text-white border-red-600 shadow-2xs font-bold'
                  : 'bg-gray-50 text-gray-700 border-gray-200 hover:border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300'"
              >
                {{ tag }}
              </button>
            </div>
          </div>

          <!-- Comment Textarea -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
              Escribe tu reseña u opinión *
            </label>
            <textarea
              v-model="formData.comment"
              required
              rows="3"
              placeholder="Cuéntanos sobre el sabor, la temperatura del pollo, el tiempo de entrega o la amabilidad del personal..."
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none"
            ></textarea>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-800">
            <button
              type="button"
              @click="handleClose"
              class="rounded-xl px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition cursor-pointer"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-6 py-2.5 text-xs font-black text-white shadow-md hover:from-red-700 hover:to-red-800 transition disabled:opacity-50 cursor-pointer flex items-center gap-2"
            >
              <span v-if="!submitting">Publicar Reseña ⭐</span>
              <span v-else>Guardando...</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import api from '@/plugins/axios'
import company from '@/config/company'
import { useToasts } from '@/composables/useToasts'

const googleReviewsUrl = ref(company.googleReviewsUrl || 'https://share.google/ETUIpZEvqbGtcyBGa')

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['close', 'saved'])
const { addToast } = useToasts()

const hoverRating = ref(0)
const submitting = ref(false)

const availableTags = [
  'Sabor al carbón',
  'Pollo súper jugoso',
  'Crujiente y dorado',
  'Salsas caseras deliciosas',
  'Cebollitas asadas',
  'Excelente atención',
  'Entrega rápida',
  'Empaque higiénico',
  'Horchata natural',
  'Ambiente familiar',
  'Buen precio',
]

const formData = reactive({
  customer_name: '',
  rating: 5,
  comment: '',
  branch: 'Sucursal Centro Histórico',
  favorite_dish: '',
  tags: ['Sabor al carbón', 'Excelente atención'],
})

watch(() => props.isOpen, (open) => {
  if (open) {
    hoverRating.value = 0
  }
})

const getRatingLabel = (val) => {
  const map = {
    1: '1 Estrella: Mala experiencia',
    2: '2 Estrellas: Regular, puede mejorar',
    3: '3 Estrellas: Bueno, aceptable',
    4: '4 Estrellas: ¡Muy bueno y sabroso!',
    5: '5 Estrellas: ¡Excelente, el mejor pollo al carbón!',
  }
  return map[val] || '5 Estrellas: ¡Excelente!'
}

const toggleTag = (tag) => {
  const idx = formData.tags.indexOf(tag)
  if (idx > -1) {
    formData.tags.splice(idx, 1)
  } else {
    formData.tags.push(tag)
  }
}

const handleClose = () => {
  emit('close')
}

const handleSubmit = async () => {
  if (!formData.customer_name.trim() || !formData.comment.trim()) {
    addToast('Por favor escribe tu nombre y tu reseña.', 'error')
    return
  }

  submitting.value = true
  try {
    const res = await api.post('/reviews', {
      customer_name: formData.customer_name,
      rating: formData.rating,
      comment: formData.comment,
      branch: formData.branch,
      favorite_dish: formData.favorite_dish,
      tags: formData.tags,
    })

    addToast(res.data?.message || '¡Gracias! Tu reseña ha sido publicada exitosamente.', 'success')
    emit('saved', res.data?.review)
    emit('close')

    // Reset fields
    formData.comment = ''
    formData.favorite_dish = ''
  } catch (err) {
    console.error('Error al guardar reseña', err)
    addToast(err.response?.data?.message || 'Error al enviar tu reseña.', 'error')
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
