<!-- src/components/landing/Promotions.vue -->
<template>
  <section id="promotions" class="py-16 bg-linear-to-br from-red-50 to-orange-50 dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4">
      <!-- Encabezado -->
      <div class="text-center mb-12" data-aos="fade-up">
        <span class="inline-block bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-full mb-4">
          🔥 Ofertas Especiales
        </span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 dark:text-gray-100">
          Promociones <span class="text-red-600">Imperdibles</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto text-sm md:text-base dark:text-gray-300">
          Aprovecha nuestras promociones especiales y disfruta del mejor pollo asado a precios increíbles
        </p>
      </div>

      <!-- Grid de promociones (Mejorado con 3 columnas en pantallas grandes) -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="(promo, index) in promotions"
          :key="promo.id"
          class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-2 flex flex-col dark:bg-gray-800 dark:border dark:border-gray-700"
          data-aos="flip-up"
          :data-aos-delay="index * 150"
        >
          <!-- Imagen -->
          <div class="relative h-56 lg:h-64 overflow-hidden shrink-0">
            <img
              :src="promo.image"
              :alt="promo.title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
              loading="lazy"
              decoding="async"
              width="640"
              height="400"
              @error="handleImageError"
            />
            <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/30 to-transparent"></div>
            
            <!-- Badge de etiqueta -->
            <div v-if="promo.badge" class="absolute top-4 right-4 bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-[10px] font-bold shadow-lg flex items-center gap-1">
              <span>⭐</span> {{ promo.badge }}
            </div>

          </div>

          <!-- Contenido -->
          <div class="p-6 flex flex-col grow">
            <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-red-600 transition-colors line-clamp-1 dark:text-gray-100">
              {{ promo.title }}
            </h3>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2 grow dark:text-gray-300">
              {{ promo.description }}
            </p>
            
            <!-- Footer de la tarjeta -->
            <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
              <div class="flex items-center text-xs text-gray-500">
                <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <span class="whitespace-nowrap">Hasta: {{ formatDate(promo.validUntil) }}</span>
              </div>
              <button class="bg-red-600 text-white px-5 py-1.5 rounded-full text-sm hover:bg-red-700 transition-all transform hover:scale-105 shadow-lg font-medium">
                🛒 Ordenar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Contador y CTA combinados visualmente -->
      <div class="text-center mt-12 flex flex-col items-center" data-aos="fade-up">
        <p class="text-gray-400 text-sm mb-4">
          Mostrando <span class="font-bold text-gray-600">{{ promotions.length }}</span> promociones disponibles
        </p>
        <button class="bg-white text-red-600 px-8 py-3 rounded-full hover:bg-gray-50 transition-all shadow-lg border-2 border-red-600 font-medium hover:scale-105 transform duration-300 dark:bg-gray-900 dark:text-red-300 dark:border-red-700 dark:hover:bg-gray-800">
          Ver todas las promociones
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { useLandingContent } from '@/composables/useLandingContent'
import promotionsData from '@/config/promotions'

const { landingPromotions } = useLandingContent()

const normalizePromotion = (promo, index) => ({
  id: promo.id ?? index,
  title: promo.title || 'Promoción',
  description: promo.description || '',
  image: promo.image_url || promo.image || '',
  validUntil: promo.valid_until || promo.validUntil || '',
  code: promo.code || '',
  badge: promo.badge || '',
})

const promotions = computed(() => {
  const currentPromotions = Array.isArray(landingPromotions.value) ? landingPromotions.value : []
  if (currentPromotions.length) {
    return currentPromotions.map((promo, index) => normalizePromotion(promo, index))
  }

  const fallbackPromotions = Array.isArray(promotionsData) ? promotionsData : (promotionsData.active || [])
  return fallbackPromotions.map((promo, index) => normalizePromotion(promo, index))
})

const formatDate = (dateString) => {
  if (!dateString) return 'Por definir'
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) return 'Por definir'
    return date.toLocaleDateString('es-MX', {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    })
  } catch {
    return 'Por definir'
  }
}

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZWVlIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyNHB4IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+UHJvbW8gPC90ZXh0Pjwvc3ZnPg=='
}
</script>