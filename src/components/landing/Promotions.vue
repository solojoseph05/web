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

      <!-- Empty State si no hay promociones vigentes -->
      <div
        v-if="promotions.length === 0"
        class="mx-auto max-w-xl rounded-3xl bg-white p-10 text-center shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700"
        data-aos="fade-up"
      >
        <span class="text-4xl block mb-3">🔥</span>
        <h3 class="text-lg font-black text-gray-900 dark:text-white">
          Estamos preparando nuevas promociones para ti
        </h3>
        <p class="mt-2 text-xs sm:text-sm text-gray-600 dark:text-gray-300">
          Por el momento todas nuestras ofertas de temporada concluyeron, pero puedes explorar nuestros paquetes familiares al carbón en el menú.
        </p>
        <router-link
          to="/menu"
          class="mt-5 inline-flex items-center justify-center rounded-full bg-red-600 px-6 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-red-700"
        >
          Ver Menú Completo
        </router-link>
      </div>

      <!-- Grid de promociones con PromotionCard -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <PromotionCard
          v-for="(promo, index) in promotions"
          :key="promo.id"
          :promo="promo"
          data-aos="fade-up"
          :data-aos-delay="index * 120"
        />
      </div>

      <!-- Contador y CTA -->
      <div v-if="promotions.length > 0" class="text-center mt-12 flex flex-col items-center" data-aos="fade-up">
        <p class="text-gray-500 text-xs sm:text-sm mb-4">
          Mostrando <span class="font-bold text-gray-800 dark:text-gray-200">{{ promotions.length }}</span> {{ promotions.length === 1 ? 'promoción vigente' : 'promociones vigentes' }}
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

const parseLocalDate = (value, endOfDay = false) => {
  if (!value) return null
  const raw = String(value).trim()
  const ymd = raw.match(/^(\d{4})-(\d{2})-(\d{2})/)
  if (ymd) {
    const year = Number(ymd[1])
    const month = Number(ymd[2]) - 1
    const day = Number(ymd[3])
    return endOfDay
      ? new Date(year, month, day, 23, 59, 59, 999)
      : new Date(year, month, day, 0, 0, 0, 0)
  }
  const parsed = new Date(raw)
  if (Number.isNaN(parsed.getTime())) return null
  if (endOfDay) parsed.setHours(23, 59, 59, 999)
  else parsed.setHours(0, 0, 0, 0)
  return parsed
}

const isPromoCurrentlyValid = (promo) => {
  if (promo.isActive === false) return false
  const now = new Date()

  if (promo.validFrom) {
    const start = parseLocalDate(promo.validFrom, false)
    if (start && now < start) return false
  }
  if (promo.validUntil) {
    const end = parseLocalDate(promo.validUntil, true)
    if (end && now > end) return false
  }
  return true
}

const normalizePromotion = (promo, index) => ({
  id: promo.id ?? index,
  title: promo.title || 'Promoción',
  subtitle: promo.subtitle || '',
  description: promo.description || '',
  image: promo.image_url || promo.image || '',
  validFrom: promo.valid_from || promo.validFrom || '',
  validUntil: promo.valid_until || promo.validUntil || '',
  termsConditions: promo.terms_conditions || promo.termsConditions || '',
  discountPercentage: promo.discount_percentage ?? promo.discountPercentage ?? null,
  promoPrice: promo.promo_price ?? promo.promoPrice ?? null,
  code: promo.code || '',
  badge: promo.badge || '',
  isActive: promo.is_active !== false && promo.isActive !== false,
})

const promotions = computed(() => {
  const currentPromotions = Array.isArray(landingPromotions.value) ? landingPromotions.value : []
  const source = currentPromotions.length
    ? currentPromotions
    : (Array.isArray(promotionsData) ? promotionsData : (promotionsData.active || []))

  return source
    .map((promo, index) => normalizePromotion(promo, index))
    .filter(isPromoCurrentlyValid)
})
</script>