<template>
  <section id="home" class="relative flex min-h-screen items-center overflow-hidden pt-20 sm:pt-24 dark:bg-gray-950">
    <!-- Background Decorations -->
    <div class="absolute inset-0 z-0">
      <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-red-50 to-transparent dark:from-red-950/30"></div>
      <div class="absolute -top-20 -right-20 w-64 h-64 bg-red-200 rounded-full blur-3xl opacity-30 dark:bg-red-900/50"></div>
      <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-orange-200 rounded-full blur-3xl opacity-30 dark:bg-orange-900/40"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
      <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
        <!-- Content -->
        <div class="order-2 md:order-1 text-center sm:text-left">
          <div class="space-y-6">
            <!-- Badge -->
            <div class="inline-flex items-center bg-red-100 text-red-700 px-4 py-2 rounded-full dark:bg-red-900/50 dark:text-red-200">
              <FireIcon class="w-5 h-5 mr-2" />
              <span class="text-sm font-medium">¡Sabor auténtico mexicano!</span>
            </div>

            <!-- Title -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight text-gray-800 dark:text-gray-100">
              {{ heroTitle }}
            </h1>

            <p class="text-base sm:text-lg text-gray-600 max-w-md mx-auto sm:mx-0 dark:text-gray-300">
              {{ heroDescription }}
            </p>

            <!-- CTA Buttons -->
            <!-- Estos enlaces apuntan a las secciones del landing: menú y contacto.
                 Si cambias el id de alguna sección, también debes actualizar aquí el href. -->
            <div class="flex flex-wrap justify-center sm:justify-start gap-4">
              <a href="#menu" class="inline-flex items-center justify-center rounded-full bg-red-600 px-6 py-3 font-semibold text-white shadow-lg transition hover:bg-red-700">
                Ver Menú
              </a>
              <a href="#contact" class="inline-flex items-center justify-center rounded-full border border-red-200 bg-white px-6 py-3 font-semibold text-red-600 shadow-sm transition hover:border-red-300 hover:bg-red-50 dark:border-red-800 dark:bg-gray-900 dark:text-red-300 dark:hover:bg-red-950/30">
                Ordenar Ahora
              </a>
            </div>

            <!-- Stats -->
            <div class="flex flex-wrap justify-center sm:justify-start gap-4 sm:gap-8 pt-4">
              <div v-for="stat in company.stats" :key="stat.label" class="text-center">
                <div class="text-2xl font-bold text-red-600">{{ stat.number }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-300">{{ stat.label }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Image -->
        <div class="order-1 md:order-2">
          <div class="relative mx-auto max-w-[560px]">
            <!-- Floating Elements -->
            <div class="absolute -top-4 -left-4 w-20 h-20 bg-yellow-400 rounded-full blur-xl opacity-40 animate-pulse"></div>
            <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-red-400 rounded-full blur-xl opacity-40 animate-pulse delay-1000"></div>
            
            <!-- Main Image -->
            <!-- La imagen del hero usa variantes responsive para ahorrar peso en móvil.
                 Si cambias el asset, mantén también la versión small para que el srcset siga siendo útil. -->
            <div class="relative overflow-hidden rounded-[1.75rem] shadow-2xl transition-transform duration-500 hover:scale-[1.02]">
              <img 
                :src="heroImageUrl"
                :srcset="heroImageSrcSet"
                sizes="(max-width: 768px) 92vw, (max-width: 1280px) 50vw, 560px"
                alt="Pollo Feliz - El mejor pollo asado de Durango"
                class="w-full h-auto"
                loading="eager"
                decoding="async"
                fetchpriority="high"
                width="560"
                height="420"
                @error="handleImageError"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            </div>

            <!-- Floating Card -->
            <div class="absolute -bottom-4 left-4 sm:-bottom-6 sm:-left-6 bg-white rounded-xl shadow-xl p-4 animate-bounce dark:bg-gray-900 dark:border dark:border-gray-700">
              <div class="flex items-center gap-2">
                <StarIcon class="w-6 h-6 text-yellow-400 fill-current" />
                <div>
                  <div class="font-bold text-gray-800 dark:text-gray-100">4.9/5</div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">500+ reseñas</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { FireIcon, StarIcon } from '@heroicons/vue/24/solid'
import company from '@/config/company'
import heroImage from '@/assets/images/3.webp'
import heroImageSm from '@/assets/images/3-sm.webp'
import { useLandingContent } from '@/composables/useLandingContent'

const { landingSettings } = useLandingContent()
const heroTitle = computed(() => landingSettings.value.hero_title || 'El mejor Pollo Asado de Durango')
const heroDescription = computed(() => landingSettings.value.hero_description || company.description)
const heroImageUrl = computed(() => landingSettings.value.hero_image_url || heroImage)
const heroImageSrcSet = computed(() => {
  if (landingSettings.value.hero_image_url) {
    return `${landingSettings.value.hero_image_url} 1400w`
  }
  return `${heroImageSm} 800w, ${heroImage} 1400w`
})

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjUwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZTJlOGYwIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxOHB4IiBmaWxsPSIjNjQ3NDhiIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+UG9sbG8gRmVsaXo8L3RleHQ+PC9zdmc+'
}
</script>