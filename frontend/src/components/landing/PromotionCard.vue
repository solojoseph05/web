<template>
  <div
    class="group relative flex flex-col justify-between overflow-hidden rounded-2xl bg-white shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 dark:bg-gray-800"
  >
    <!-- Promo Image -->
    <div class="relative h-52 sm:h-60 overflow-hidden bg-gray-100 dark:bg-gray-900">
      <img
        :src="promo.image"
        :alt="promo.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        loading="lazy"
        decoding="async"
        @error="handleImageError"
      />
      <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/20 to-transparent"></div>

      <!-- Badge -->
      <div
        v-if="promo.badge"
        class="absolute top-3 right-3 rounded-full bg-yellow-400 px-3 py-1 text-xs font-black text-gray-900 shadow-md flex items-center gap-1"
      >
        <span>🔥</span>
        <span>{{ promo.badge }}</span>
      </div>

      <!-- Promo Code pill on image -->
      <div v-if="promo.code" class="absolute bottom-3 left-3 flex items-center gap-1.5 rounded-lg bg-black/60 backdrop-blur-xs px-2.5 py-1 text-xs font-mono font-bold text-white border border-white/20">
        <span>CUPÓN:</span>
        <span class="text-yellow-400">{{ promo.code }}</span>
      </div>
    </div>

    <!-- Content -->
    <div class="flex flex-1 flex-col justify-between p-5">
      <div>
        <h3 class="text-lg font-bold text-gray-800 group-hover:text-red-600 transition-colors line-clamp-1 dark:text-gray-100">
          {{ promo.title }}
        </h3>
        <p class="mt-2 text-xs sm:text-sm text-gray-600 line-clamp-2 dark:text-gray-300">
          {{ promo.description }}
        </p>
      </div>

      <!-- Footer Info & Action -->
      <div class="mt-5 flex items-center justify-between border-t border-gray-100 pt-3 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
        <span class="truncate max-w-[140px]">Vence: {{ formatDate(promo.validUntil) }}</span>

        <button
          @click="applyPromo"
          class="flex items-center gap-1.5 rounded-full bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-md transition hover:bg-red-700 active:scale-95 cursor-pointer"
        >
          <span>Aprovechar</span>
          <span>→</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCart } from '@/composables/useCart'
import { useToasts } from '@/composables/useToasts'

const props = defineProps({
  promo: {
    type: Object,
    required: true,
  },
})

const { applyPromoCode, openCart } = useCart()
const { addToast } = useToasts()

const applyPromo = () => {
  if (props.promo.code) {
    const res = applyPromoCode(props.promo.code)
    addToast(res.message, res.success ? 'success' : 'info')
  } else {
    addToast(`¡Promoción "${props.promo.title}" seleccionada!`, 'success')
  }
  openCart()
}

const formatDate = (dateString) => {
  if (!dateString) return 'Por tiempo limitado'
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) return 'Por tiempo limitado'
    return date.toLocaleDateString('es-MX', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    })
  } catch {
    return 'Por tiempo limitado'
  }
}

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZWVlIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyNHB4IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+UHJvbW8gPC90ZXh0Pjwvc3ZnPg=='
}
</script>
