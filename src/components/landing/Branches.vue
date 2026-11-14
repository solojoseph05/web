<template>
  <section id="branches" class="py-16 sm:py-20 scroll-mt-24 bg-white dark:bg-gray-900 transition-colors">
    <div class="container mx-auto px-4 sm:px-6">
      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-10" data-aos="fade-up">
        <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-600 text-xs sm:text-sm font-bold px-4 py-1.5 rounded-full mb-3 dark:bg-red-950/60 dark:text-red-300">
          <span>📍</span>
          <span>Cobertura & Puntos de Venta</span>
        </span>
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight">
          {{ branchesTitle }}
        </h2>
        <p class="mt-3 text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
          {{ branchesSubtitle }}
        </p>
      </div>

      <!-- Controls Bar: Filter Tabs & View Mode (Carousel vs Grid) -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
        <!-- Filter Tabs -->
        <div class="inline-flex items-center gap-1.5 p-1.5 rounded-2xl bg-gray-100 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 text-xs font-bold overflow-x-auto max-w-full">
          <button
            type="button"
            @click="activeFilter = 'all'"
            class="px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap"
            :class="activeFilter === 'all'
              ? 'bg-white dark:bg-gray-700 text-red-600 dark:text-white shadow-xs font-black'
              : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
          >
            <span>Todas</span>
            <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="activeFilter === 'all' ? 'bg-red-50 text-red-600 dark:bg-gray-800 dark:text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300'">
              {{ allBranches.length }}
            </span>
          </button>

          <button
            type="button"
            @click="activeFilter = 'open'"
            class="px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap"
            :class="activeFilter === 'open'
              ? 'bg-white dark:bg-gray-700 text-emerald-600 dark:text-emerald-400 shadow-xs font-black'
              : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
          >
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>En Operación</span>
            <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="activeFilter === 'open' ? 'bg-emerald-50 text-emerald-700 dark:bg-gray-800 dark:text-emerald-300' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300'">
              {{ operatingCount }}
            </span>
          </button>

          <button
            v-if="comingSoonCount > 0"
            type="button"
            @click="activeFilter = 'coming_soon'"
            class="px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap"
            :class="activeFilter === 'coming_soon'
              ? 'bg-white dark:bg-gray-700 text-amber-600 dark:text-amber-400 shadow-xs font-black'
              : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
          >
            <span>🚀</span>
            <span>Próximas Aperturas</span>
            <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="activeFilter === 'coming_soon' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300'">
              {{ comingSoonCount }}
            </span>
          </button>
        </div>

        <!-- View Switcher & Carousel Arrow Controls -->
        <div class="flex items-center gap-2">
          <!-- View Switcher: Carousel vs Grid -->
          <div class="flex items-center rounded-2xl bg-gray-100 dark:bg-gray-800/80 p-1 border border-gray-200/80 dark:border-gray-700/80">
            <button
              type="button"
              @click="viewMode = 'slider'"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
              :class="viewMode === 'slider'
                ? 'bg-white dark:bg-gray-700 text-red-600 dark:text-white shadow-xs'
                : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'"
              title="Vista Carrusel interactivo"
            >
              <span>🎠 Carrusel</span>
            </button>
            <button
              type="button"
              @click="viewMode = 'grid'"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
              :class="viewMode === 'grid'
                ? 'bg-white dark:bg-gray-700 text-red-600 dark:text-white shadow-xs'
                : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'"
              title="Ver todas en cuadrícula"
            >
              <span>▦ Cuadrícula</span>
            </button>
          </div>

          <!-- Carousel Arrow Buttons (only visible in slider mode) -->
          <div v-if="viewMode === 'slider'" class="hidden sm:flex items-center gap-1">
            <button
              type="button"
              @click="scrollPrev"
              :disabled="!canScrollPrev"
              class="h-9 w-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer border border-gray-200 dark:border-gray-700"
              aria-label="Sucursal anterior"
            >
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button
              type="button"
              @click="scrollNext"
              :disabled="!canScrollNext"
              class="h-9 w-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer border border-gray-200 dark:border-gray-700"
              aria-label="Siguiente sucursal"
            >
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- MODE 1: Interactive Slider / Carousel -->
      <div v-if="viewMode === 'slider'" class="relative">
        <div
          ref="sliderRef"
          @scroll="handleScroll"
          class="flex gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-4 pt-1 px-1 -mx-4 sm:-mx-6 px-4 sm:px-6 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700"
          style="scrollbar-width: thin;"
        >
          <!-- Branch Cards -->
          <div
            v-for="branch in filteredBranches"
            :key="branch.id"
            class="snap-start shrink-0 w-[290px] sm:w-[350px] md:w-[380px]"
          >
            <BranchCard :branch="branch" class="h-full" @focus-map="handleFocusBranchOnMap" />
          </div>

          <!-- Teaser Card: Suggest New Location in Durango -->
          <div class="snap-start shrink-0 w-[290px] sm:w-[350px] md:w-[380px]">
            <div class="h-full flex flex-col justify-between rounded-3xl border-2 border-dashed border-red-300 dark:border-red-900/60 bg-linear-to-b from-red-50/50 via-white to-amber-50/40 dark:from-red-950/20 dark:via-gray-800 dark:to-amber-950/20 p-6 sm:p-7 shadow-xs hover:shadow-xl transition-all hover:-translate-y-1 group">
              <div>
                <div class="flex items-center justify-between mb-4">
                  <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-600 text-white text-2xl shadow-md group-hover:scale-110 transition-transform">
                    🗺️
                  </span>
                  <span class="text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-full bg-amber-100 text-amber-900 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                    Expansión 2025
                  </span>
                </div>

                <h3 class="text-xl font-black text-gray-950 dark:text-white leading-tight mb-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                  ¿Quieres una sucursal en tu colonia?
                </h3>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                  Estamos buscando nuevas ubicaciones estratégicas en Durango para llevarte el pollo al carbón más fresco. ¡Cuéntanos dónde te gustaría vernos!
                </p>

                <div class="mt-4 p-3 rounded-2xl bg-white/80 dark:bg-gray-900/60 border border-red-100 dark:border-gray-700/60 text-xs space-y-1.5 text-gray-700 dark:text-gray-300">
                  <div class="flex items-center gap-2">
                    <span class="text-emerald-500 font-bold">✓</span>
                    <span>Fraccionamientos en crecimiento</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-emerald-500 font-bold">✓</span>
                    <span>Bulevares y avenidas principales</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-emerald-500 font-bold">✓</span>
                    <span>Locales comerciales disponibles</span>
                  </div>
                </div>
              </div>

              <div class="mt-6 pt-4 border-t border-red-100 dark:border-gray-700/60">
                <a
                  :href="suggestLocationWhatsApp"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-full flex items-center justify-center gap-2 rounded-xl bg-linear-to-r from-red-600 to-amber-600 hover:from-red-700 hover:to-amber-700 text-white font-black px-4 py-3 text-xs shadow-md transition"
                >
                  <span>💬 Sugerir mi Colonia por WhatsApp</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Slider Pagination Dots & Mobile Navigation Buttons -->
        <div class="flex items-center justify-between pt-4">
          <!-- Dots -->
          <div class="flex items-center gap-1.5">
            <button
              v-for="(_, index) in sliderDotsCount"
              :key="index"
              type="button"
              @click="scrollToIndex(index)"
              class="h-2 rounded-full transition-all cursor-pointer"
              :class="activeSlideIndex === index
                ? 'w-7 bg-red-600 dark:bg-red-500'
                : 'w-2 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400'"
              :aria-label="`Ir a grupo de sucursales ${index + 1}`"
            ></button>
          </div>

          <!-- Mobile arrow buttons -->
          <div class="flex sm:hidden items-center gap-1.5">
            <button
              type="button"
              @click="scrollPrev"
              :disabled="!canScrollPrev"
              class="h-8 w-8 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 disabled:opacity-30 transition border border-gray-200 dark:border-gray-700"
            >
              ‹
            </button>
            <button
              type="button"
              @click="scrollNext"
              :disabled="!canScrollNext"
              class="h-8 w-8 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 disabled:opacity-30 transition border border-gray-200 dark:border-gray-700"
            >
              ›
            </button>
          </div>
        </div>
      </div>

      <!-- MODE 2: Full Grid Layout -->
      <div v-else>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <BranchCard
            v-for="branch in filteredBranches"
            :key="branch.id"
            :branch="branch"
            @focus-map="handleFocusBranchOnMap"
          />

          <!-- Suggest Location Card in Grid -->
          <div class="rounded-3xl border-2 border-dashed border-red-300 dark:border-red-900/60 bg-linear-to-b from-red-50/50 via-white to-amber-50/40 dark:from-red-950/20 dark:via-gray-800 dark:to-amber-950/20 p-6 sm:p-7 shadow-xs hover:shadow-xl transition-all flex flex-col justify-between group">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-600 text-white text-2xl shadow-md group-hover:scale-110 transition-transform">
                  🗺️
                </span>
                <span class="text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-full bg-amber-100 text-amber-900 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                  Expansión 2025
                </span>
              </div>

              <h3 class="text-xl font-black text-gray-950 dark:text-white leading-tight mb-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                ¿Quieres una sucursal en tu colonia?
              </h3>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                Estamos buscando nuevas ubicaciones en Durango. ¡Cuéntanos dónde te gustaría que abramos la próxima sucursal de Pollo Feliz!
              </p>
            </div>

            <div class="mt-6 pt-4 border-t border-red-100 dark:border-gray-700/60">
              <a
                :href="suggestLocationWhatsApp"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full flex items-center justify-center gap-2 rounded-xl bg-linear-to-r from-red-600 to-amber-600 hover:from-red-700 hover:to-amber-700 text-white font-black px-4 py-3 text-xs shadow-md transition"
              >
                <span>💬 Sugerir mi Colonia por WhatsApp</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Live Interactive Map Section with Branch Pins -->
      <div ref="mapSectionWrapperRef" class="scroll-mt-24">
        <InteractiveMap ref="interactiveMapRef" :branches="allBranches" />
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import BranchCard from '@/components/landing/BranchCard.vue'
import InteractiveMap from '@/components/landing/InteractiveMap.vue'
import branchesConfig from '@/config/branches'
import { useLandingContent } from '@/composables/useLandingContent'

const { landingBranches, landingSettings, loadLandingContent } = useLandingContent()

const branchesTitle = computed(() => landingSettings.value.branches_section_title || 'Nuestras Sucursales')
const branchesSubtitle = computed(() => landingSettings.value.branches_section_subtitle || 'Visítanos o llámanos directamente para disfrutar del mejor sabor al carbón recién salido de la parrilla.')

// Display mode: 'slider' or 'grid'
const viewMode = ref('slider')

// Filter state: 'all' | 'open' | 'coming_soon'
const activeFilter = ref('all')

// Slider & Interactive Map DOM refs
const sliderRef = ref(null)
const interactiveMapRef = ref(null)
const mapSectionWrapperRef = ref(null)
const canScrollPrev = ref(false)
const canScrollNext = ref(true)
const activeSlideIndex = ref(0)

const allBranches = computed(() => {
  if (landingBranches.value && landingBranches.value.length > 0) {
    return landingBranches.value
  }
  return branchesConfig.active || []
})

const operatingCount = computed(() => {
  return allBranches.value.filter(b => !b.is_coming_soon && b.is_open !== false).length
})

const comingSoonCount = computed(() => {
  return allBranches.value.filter(b => b.is_coming_soon).length
})

const filteredBranches = computed(() => {
  if (activeFilter.value === 'open') {
    return allBranches.value.filter(b => !b.is_coming_soon && b.is_open !== false)
  }
  if (activeFilter.value === 'coming_soon') {
    return allBranches.value.filter(b => b.is_coming_soon)
  }
  return allBranches.value
})

const sliderDotsCount = computed(() => {
  // Total cards in slider = filtered branches + 1 teaser card
  const total = filteredBranches.value.length + 1
  return Math.max(1, Math.min(total, 6))
})

const handleScroll = () => {
  if (!sliderRef.value) return
  const el = sliderRef.value
  canScrollPrev.value = el.scrollLeft > 10
  canScrollNext.value = el.scrollLeft + el.clientWidth < el.scrollWidth - 10

  const maxScroll = el.scrollWidth - el.clientWidth
  if (maxScroll > 0) {
    const ratio = el.scrollLeft / maxScroll
    activeSlideIndex.value = Math.min(
      sliderDotsCount.value - 1,
      Math.max(0, Math.round(ratio * (sliderDotsCount.value - 1)))
    )
  }
}

const scrollPrev = () => {
  if (!sliderRef.value) return
  const cardWidth = sliderRef.value.clientWidth > 768 ? 380 : 310
  sliderRef.value.scrollBy({ left: -cardWidth, behavior: 'smooth' })
}

const scrollNext = () => {
  if (!sliderRef.value) return
  const cardWidth = sliderRef.value.clientWidth > 768 ? 380 : 310
  sliderRef.value.scrollBy({ left: cardWidth, behavior: 'smooth' })
}

const scrollToIndex = (idx) => {
  if (!sliderRef.value) return
  const el = sliderRef.value
  const maxScroll = el.scrollWidth - el.clientWidth
  const target = (idx / (sliderDotsCount.value - 1)) * maxScroll
  el.scrollTo({ left: target, behavior: 'smooth' })
}

const handleFocusBranchOnMap = (branch) => {
  mapSectionWrapperRef.value?.scrollIntoView({ behavior: 'smooth', block: 'center' })
  setTimeout(() => {
    interactiveMapRef.value?.selectBranch(branch)
  }, 220)
}

const suggestLocationWhatsApp = computed(() => {
  const phone = '526181234567'
  const message = '¡Hola Pollo Feliz Durango! Me encantaría sugerir una nueva ubicación para una sucursal en mi colonia/zona: '
  return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`
})

onMounted(async () => {
  try {
    await loadLandingContent(true)
  } catch (err) {
    console.warn('Error al refrescar sucursales en landing:', err)
  }
  setTimeout(() => {
    handleScroll()
  }, 200)
  window.addEventListener('resize', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleScroll)
})
</script>
