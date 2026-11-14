<template>
  <section id="home" class="relative min-h-[92vh] flex items-center overflow-hidden pt-28 sm:pt-36 pb-16 scroll-mt-24 bg-radial-[at_top_right] from-red-100/70 via-white to-orange-50/50 dark:from-red-950/40 dark:via-gray-950 dark:to-gray-950">
    <!-- Ambient Atmospheric Glows -->
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
      <div class="absolute -top-32 right-0 h-96 w-96 rounded-full bg-linear-to-bl from-red-500/20 via-orange-400/15 to-transparent blur-3xl"></div>
      <div class="absolute bottom-0 left-[-10%] h-80 w-80 rounded-full bg-linear-to-tr from-amber-400/15 to-red-600/10 blur-3xl"></div>
      <div class="absolute top-1/2 left-1/3 h-72 w-72 -translate-y-1/2 rounded-full bg-red-400/10 blur-3xl"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
      <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-8">
        <!-- Text & Call to Action (Left Column) -->
        <div class="text-center sm:text-left lg:col-span-7 space-y-6">
          <!-- Welcoming Pill / Kicker -->
          <div class="inline-flex items-center gap-2.5 rounded-full border border-red-200/80 bg-white/90 px-4 py-2 shadow-xs backdrop-blur-md dark:border-red-900/60 dark:bg-gray-900/80">
            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-600 text-white shadow-xs">
              <FireIcon class="h-3.5 w-3.5 animate-pulse" />
            </span>
            <span class="text-xs sm:text-sm font-semibold tracking-wide text-gray-800 dark:text-gray-200">
              {{ heroSubtitle }}
            </span>
          </div>

          <!-- Headline -->
          <h1 class="text-balance text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-gray-900 leading-[1.1] dark:text-white">
            {{ heroTitlePrefix }}
            <span class="relative inline-block text-red-600 dark:text-red-500">
              {{ heroTitleHighlight }}
              <svg class="absolute -bottom-2 left-0 w-full h-3 text-red-300 dark:text-red-700/60 -z-10" viewBox="0 0 100 20" preserveAspectRatio="none">
                <path d="M0 10 Q50 0 100 10" stroke="currentColor" stroke-width="4" fill="none" stroke-linecap="round"/>
              </svg>
            </span>
          </h1>

          <!-- Welcoming Warm Description -->
          <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-2xl mx-auto sm:mx-0 leading-relaxed dark:text-gray-300 font-normal">
            {{ heroDescription }}
          </p>

          <!-- Core Value Micro-Pills -->
          <div class="flex flex-wrap items-center justify-center sm:justify-start gap-y-2 gap-x-4 text-xs font-medium text-gray-600 dark:text-gray-300">
            <span class="flex items-center gap-1.5">
              <CheckBadgeIcon class="h-4 w-4 text-red-600 dark:text-red-400" />
              Receta y marinado tradicional
            </span>
            <span class="hidden sm:inline text-gray-300 dark:text-gray-700">·</span>
            <span class="flex items-center gap-1.5">
              <SparklesIcon class="h-4 w-4 text-amber-500" />
              Tortillas y salsas del día
            </span>
            <span class="hidden sm:inline text-gray-300 dark:text-gray-700">·</span>
            <span class="flex items-center gap-1.5">
              <BuildingStorefrontIcon class="h-4 w-4 text-red-600 dark:text-red-400" />
              Para recoger o a domicilio
            </span>
          </div>

          <!-- High-Impact CTA Buttons -->
          <div class="pt-2 flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-4">
            <!-- Primary CTA: View the Menu -->
            <router-link
              to="/menu"
              class="group relative inline-flex w-full sm:w-auto items-center justify-center gap-3 rounded-full bg-red-600 px-8 py-4 text-base font-bold text-white shadow-xl shadow-red-600/30 transition-all duration-300 hover:bg-red-700 hover:shadow-2xl hover:shadow-red-600/50 hover:scale-[1.02] active:scale-95"
            >
              <span>{{ heroCtaPrimary }}</span>
              <ArrowRightIcon class="h-5 w-5 transition-transform duration-300 group-hover:translate-x-1" />
            </router-link>

            <!-- Secondary CTA: Quick Order -->
            <button
              @click="handleOrderNow"
              class="inline-flex w-full sm:w-auto items-center justify-center gap-2.5 rounded-full border-2 border-red-200 bg-white/90 px-7 py-3.5 text-base font-bold text-red-600 shadow-md transition-all duration-300 hover:border-red-400 hover:bg-red-50 hover:scale-[1.02] active:scale-95 dark:border-red-900/60 dark:bg-gray-900/90 dark:text-red-400 dark:hover:bg-red-950/40 cursor-pointer"
            >
              <ShoppingBagIcon class="h-5 w-5" />
              <span>{{ totalCount > 0 ? `Mi Pedido (${totalCount})` : heroCtaSecondary }}</span>
            </button>
          </div>

          <!-- Trust & Social Proof Stats -->
          <div class="pt-6 border-t border-gray-200/70 dark:border-gray-800/80">
            <div class="grid grid-cols-3 gap-4 max-w-lg mx-auto sm:mx-0 text-center sm:text-left">
              <div>
                <p class="tabular-nums text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">{{ stat1Value }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ stat1Label }}</p>
              </div>
              <div>
                <p class="tabular-nums text-2xl sm:text-3xl font-black text-red-600 dark:text-red-500">{{ stat2Value }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ stat2Label }}</p>
              </div>
              <div>
                <p class="tabular-nums text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">{{ stat3Value }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ stat3Label }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Visual Showcase (Right Column) -->
        <div class="lg:col-span-5 flex flex-col items-center">
          <div class="w-full max-w-md sm:max-w-lg">
            <!-- Main Hero Image Frame -->
            <div class="relative overflow-hidden rounded-3xl border-4 border-white bg-white shadow-xl shadow-red-900/10 dark:border-gray-800 dark:bg-gray-900 transition-transform duration-500 hover:scale-[1.01]">
              <img
                :src="heroImageUrl"
                :srcset="heroImageSrcSet"
                sizes="(max-width: 768px) 90vw, (max-width: 1200px) 45vw, 520px"
                alt="Pollo Feliz - El mejor pollo asado al carbón recién hecho"
                class="w-full h-64 sm:h-80 md:h-96 object-cover object-center"
                loading="eager"
                decoding="async"
                fetchpriority="high"
                width="560"
                height="420"
                @error="handleImageError"
              />
              <div class="absolute inset-0 bg-linear-to-t from-black/70 via-transparent to-black/20"></div>

              <!-- Top Freshness Badge -->
              <div class="absolute top-3 right-3 flex items-center gap-2 rounded-full bg-black/60 px-3.5 py-1.5 backdrop-blur-md border border-white/20 text-white">
                <span class="flex h-2.5 w-2.5 rounded-full bg-green-400 animate-pulse"></span>
                <span class="text-xs font-bold tracking-wide">¡Parrilla Encendida!</span>
              </div>

              <!-- Bottom Caption (Not overlapped by any other card) -->
              <div class="absolute bottom-4 left-4 right-4 text-white">
                <span class="inline-block rounded-md bg-red-600 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-white mb-1.5">
                  Especialidad de la Casa
                </span>
                <p class="text-sm sm:text-base font-bold drop-shadow-md">
                  Pollo Entero con Salsas Caseras y Complementos
                </p>
              </div>
            </div>

            <!-- Quick Add Featured Card (Below image, neatly separated with no overlap) -->
            <div class="mt-4 rounded-2xl bg-white p-3.5 sm:p-4 shadow-md border border-gray-100 dark:bg-gray-900 dark:border-gray-800 flex items-center justify-between gap-3">
              <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400">
                  <StarIcon class="h-6 w-6 text-amber-500" />
                </div>
                <div>
                  <h4 class="font-extrabold text-xs sm:text-sm text-gray-900 dark:text-white">Pollo Entero Clásico</h4>
                  <p class="text-xs font-bold text-red-600">$180 MXN <span class="text-[11px] font-normal text-gray-400 dark:text-gray-500">· Con tortillas y salsas</span></p>
                </div>
              </div>
              <button
                @click="addFeaturedToCart"
                class="flex items-center justify-center gap-1.5 rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-red-700 transition cursor-pointer shrink-0"
              >
                <span>+ Agregar</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import {
  FireIcon,
  StarIcon,
  CheckBadgeIcon,
  SparklesIcon,
  BuildingStorefrontIcon,
  ShoppingBagIcon,
  ArrowRightIcon,
} from '@heroicons/vue/24/solid'
import company from '@/config/company'
import defaultHeroImage from '@/assets/images/hero_pollo_asado_1794076709512.jpg'
import { useLandingContent } from '@/composables/useLandingContent'
import { useCart } from '@/composables/useCart'
import { useToasts } from '@/composables/useToasts'

const router = useRouter()
const { totalCount, openCart, addToCart } = useCart()
const { addToast } = useToasts()
const { landingSettings } = useLandingContent()

const defaultTitle = 'El mejor Pollo Asado de Durango'
const fullTitle = computed(() => landingSettings.value.hero_title || defaultTitle)

// Divide title for high-impact typography
const heroTitlePrefix = computed(() => {
  const title = fullTitle.value
  if (title.includes('de Durango')) {
    return title.replace('de Durango', '').trim() + ' '
  }
  return 'El auténtico sabor de '
})

const heroTitleHighlight = computed(() => {
  const title = fullTitle.value
  if (title.includes('de Durango')) {
    return 'de Durango'
  }
  return 'Pollo Feliz'
})

const heroDescription = computed(() =>
  landingSettings.value.hero_description ||
  'Descubre el sabor inigualable que reúne a las familias. Pollo marinado con receta secreta de especias naturales y asado pacientemente al calor del carbón.'
)
const heroSubtitle = computed(() =>
  landingSettings.value.hero_subtitle || '¡Bienvenidos a la Familia Pollo Feliz! · Tradición al Carbón'
)

const heroCtaPrimary = computed(() =>
  landingSettings.value.hero_cta_primary || 'Ver Menú Completo'
)

const heroCtaSecondary = computed(() =>
  landingSettings.value.hero_cta_secondary || 'Ordenar en Línea'
)

const stat1Value = computed(() => landingSettings.value.stat_1_value || '100%')
const stat1Label = computed(() => landingSettings.value.stat_1_label || 'Asado al Carbón')
const stat2Value = computed(() => landingSettings.value.stat_2_value || '4.9 ★')
const stat2Label = computed(() => landingSettings.value.stat_2_label || '+2,500 Reseñas')
const stat3Value = computed(() => landingSettings.value.stat_3_value || '3+')
const stat3Label = computed(() => landingSettings.value.stat_3_label || 'Sucursales Activas')

const heroImageUrl = computed(() => landingSettings.value.hero_image_url || defaultHeroImage)
const heroImageSrcSet = computed(() => {
  const url = heroImageUrl.value
  return `${url} 1200w`
})

const handleOrderNow = () => {
  if (totalCount.value > 0) {
    openCart()
  } else {
    router.push('/menu')
  }
}

const addFeaturedToCart = () => {
  addToCart(
    {
      id: 1,
      name: 'Pollo Entero Clásico',
      price: 180,
      image_url: heroImageUrl.value,
      category: 'Pollo Entero',
      badge: 'Más vendido',
    },
    1,
    'Especialidad de bienvenida'
  )
  addToast('¡Pollo Entero Clásico agregado a tu pedido!', 'success')
  openCart()
}

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjUwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZTJlOGYwIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxOHB4IiBmaWxsPSIjNjQ3NDhiIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+UG9sbG8gRmVsaXo8L3RleHQ+PC9zdmc+'
}
</script>