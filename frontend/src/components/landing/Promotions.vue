<!-- src/components/landing/Promotions.vue -->
<template>
  <section id="promotions" class="py-16 scroll-mt-24 bg-linear-to-br from-red-50 to-orange-50 dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4">
      <!-- Encabezado -->
      <div class="text-center mb-12" data-aos="fade-up">
        <span class="inline-block bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-full mb-4">
          🔥 Ofertas Especiales
        </span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 dark:text-gray-100">
          {{ promotionsTitle }}
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto text-sm md:text-base dark:text-gray-300">
          {{ promotionsSubtitle }}
        </p>
      </div>

      <!-- Grid de promociones con PromotionCard -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <PromotionCard
          v-for="(promo, index) in promotions"
          :key="promo.id"
          :promo="promo"
          data-aos="fade-up"
          :data-aos-delay="index * 120"
        />
      </div>

      <!-- Contador y CTA -->
      <div class="text-center mt-12 flex flex-col items-center" data-aos="fade-up">
        <p class="text-gray-500 text-xs sm:text-sm mb-4">
          Mostrando <span class="font-bold text-gray-800 dark:text-gray-200">{{ promotions.length }}</span> promociones vigentes
        </p>
        <router-link
          to="/menu"
          class="rounded-full bg-red-600 px-8 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-red-700 hover:scale-105"
        >
          Explorar todo el Menú
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import PromotionCard from '@/components/landing/PromotionCard.vue'
import { useLandingContent } from '@/composables/useLandingContent'
import promotionsData from '@/config/promotions'

const { landingPromotions, landingSettings } = useLandingContent()

const promotionsTitle = computed(() => landingSettings.value.promotions_section_title || 'Promociones Imperdibles')
const promotionsSubtitle = computed(() => landingSettings.value.promotions_section_subtitle || 'Aprovecha nuestras promociones especiales y disfruta del mejor pollo asado con descuentos exclusivos.')

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
</script>