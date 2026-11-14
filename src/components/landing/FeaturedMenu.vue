<!-- src/components/landing/FeaturedMenu.vue -->
<template>
  <section id="menu" class="py-16 scroll-mt-24 bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4">
      <!-- Encabezado de sección -->
      <div class="text-center mb-10" data-aos="fade-up">
        <span class="inline-block bg-red-100 text-red-600 text-sm font-semibold px-4 py-2 rounded-full mb-4 dark:bg-red-900/40 dark:text-red-200">
          🍗 Nuestro Menú
        </span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 dark:text-gray-100">
          {{ menuTitle }}
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto dark:text-gray-300">
          {{ menuSubtitle }}
        </p>
      </div>

      <!-- Buscador instantáneo y pestañas por categoría sin recarga -->
      <div class="max-w-3xl mx-auto mb-6" data-aos="fade-up">
        <div class="relative">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">🔍</span>
          <input
            v-model="searchQuery"
            type="search"
            aria-label="Buscar platillo en el menú"
            placeholder="Buscar platillo al instante (ej. Pollo entero, Paquete, Papas, Tacos)..."
            class="w-full rounded-2xl border border-gray-200 bg-white pl-11 pr-24 py-3 text-sm text-gray-900 placeholder-gray-400 shadow-xs focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 transition"
          />
          <button
            v-if="searchQuery"
            type="button"
            @click="searchQuery = ''"
            class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg bg-gray-100 hover:bg-gray-200 px-2.5 py-1 text-xs font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-200 transition cursor-pointer"
          >
            Limpiar
          </button>
        </div>
      </div>

      <!-- Pestañas de filtro por categoría -->
      <div class="flex flex-wrap justify-center gap-2 mb-10" data-aos="fade-up">
        <button
          v-for="category in categories"
          :key="category.id"
          type="button"
          @click="selectedCategory = category.id"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-full transition-all duration-200 text-sm font-bold cursor-pointer border active:scale-95 shadow-xs"
          :class="selectedCategory === category.id
            ? 'bg-red-600 text-white border-red-600 shadow-md shadow-red-600/20'
            : 'bg-white text-gray-700 border-gray-200 hover:border-red-200 hover:bg-red-50/50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700'"
        >
          <span>{{ category.icon }}</span>
          <span>{{ category.name }}</span>
        </button>
      </div>

      <!-- Estado vacío amigable cuando la búsqueda o filtro no coincide -->
      <div
        v-if="filteredItems.length === 0"
        class="max-w-lg mx-auto rounded-3xl border border-gray-200 bg-white p-8 text-center shadow-xs dark:border-gray-800 dark:bg-gray-800/80"
      >
        <span class="text-4xl block mb-2">🍗</span>
        <h3 class="text-base font-black text-gray-900 dark:text-white">
          No encontramos platillos con ese filtro
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-4">
          Intenta limpiar el buscador o explorar todas nuestras categorías al carbón.
        </p>
        <button
          type="button"
          @click="resetFilters"
          class="rounded-xl bg-red-600 hover:bg-red-700 px-5 py-2 text-xs font-bold text-white transition cursor-pointer"
        >
          Ver todos los platillos
        </button>
      </div>

      <!-- Grid de productos -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        <ProductCard
          v-for="(item, index) in filteredItems"
          :key="item.id"
          :item="item"
          data-aos="fade-up"
          :data-aos-delay="index * 60"
          @view-details="openItemDetails"
        />
      </div>

      <!-- Modal detallado de ingredientes y alérgenos -->
      <MenuItemDetailModal
        :is-open="!!selectedDetailItem"
        :item="selectedDetailItem"
        @close="selectedDetailItem = null"
      />

      <!-- Botón de acción hacia catálogo completo -->
      <div class="text-center mt-12" data-aos="fade-up">
        <button @click="goToFullMenu" class="bg-red-600 text-white font-bold px-8 py-3 rounded-full hover:bg-red-700 transition-all transform hover:scale-105 shadow-lg cursor-pointer">
          Ver Menú Completo
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import ProductCard from '@/components/landing/ProductCard.vue'
import MenuItemDetailModal from '@/components/menu/MenuItemDetailModal.vue'
import menu from '@/config/menu'
import { useLandingContent } from '@/composables/useLandingContent'

const props = defineProps({
  displayLimit: {
    type: Number,
    default: 8,
  },
})

const selectedCategory = ref('all')
const searchQuery = ref('')
const selectedDetailItem = ref(null)
const router = useRouter()
const { landingMenuItems, landingSettings } = useLandingContent()

const openItemDetails = (item) => {
  selectedDetailItem.value = item
}

const resetFilters = () => {
  selectedCategory.value = 'all'
  searchQuery.value = ''
}

const menuTitle = computed(() => landingSettings.value.menu_section_title || 'Platos Destacados')
const menuSubtitle = computed(() => landingSettings.value.menu_section_subtitle || 'Descubre nuestros platillos más populares, preparados con la receta secreta que nos hace únicos.')

/**
 * Normaliza los platillos provenientes de la API conservando estados Destacado y Agotado.
 */
const normalizedLandingItems = computed(() =>
  (landingMenuItems.value || []).map((item) => ({
    id: item.id,
    name: item.name,
    description: item.description,
    price: item.price,
    badge: item.badge,
    category: item.category,
    is_featured: Boolean(item.is_featured),
    is_sold_out: Boolean(item.is_sold_out),
    image_url: item.image_url || menu.featured.find((fallbackItem) => fallbackItem.id === item.id || fallbackItem.name === item.name)?.image || '',
  }))
)

const sourceItems = computed(() => {
  if (normalizedLandingItems.value.length > 0) {
    return normalizedLandingItems.value
  }
  return menu.featured
})

const categories = [
  { id: 'all', name: 'Todos', icon: '🍗' },
  { id: 'pollos', name: 'Pollos', icon: '🍗' },
  { id: 'entradas', name: 'Entradas', icon: '🍟' },
  { id: 'combos', name: 'Combos', icon: '🍱' },
  { id: 'bebidas', name: 'Bebidas', icon: '🥤' },
  { id: 'tacos', name: 'Tacos', icon: '🌮' },
  { id: 'postres', name: 'Postres', icon: '🍮' },
]

const computedItems = computed(() => {
  let list = sourceItems.value

  if (selectedCategory.value !== 'all') {
    const catId = selectedCategory.value.toLowerCase()
    if (catId === 'pollos') {
      const polloCats = ['pollo entero', 'medio pollo', 'pollo', 'pollos', 'pollo asado']
      list = list.filter((item) => {
        const c = (item.category || '').toLowerCase()
        return polloCats.some((pc) => c.includes(pc)) || (item.name || '').toLowerCase().includes('pollo')
      })
    } else if (catId === 'entradas') {
      const entradaCats = ['entradas', 'entrada', 'extras', 'complementos', 'guarniciones', 'papas', 'salsas', 'tortillas', 'nuggets']
      list = list.filter((item) => {
        const c = (item.category || '').toLowerCase()
        return entradaCats.some((ec) => c.includes(ec))
      })
    } else if (catId === 'combos') {
      const comboCats = ['combos', 'paquetes', 'combos / paquetes', 'combo']
      list = list.filter((item) => {
        const c = (item.category || '').toLowerCase()
        return comboCats.some((cc) => c.includes(cc))
      })
    } else if (catId === 'bebidas') {
      const drinkCats = ['bebidas', 'drinks', 'refrescos', 'aguas']
      list = list.filter((item) => {
        const c = (item.category || '').toLowerCase()
        return drinkCats.some((dc) => c.includes(dc))
      })
    } else if (catId === 'tacos') {
      list = list.filter((item) => {
        const c = (item.category || '').toLowerCase()
        return c.includes('taco') || c.includes('quesadilla')
      })
    } else if (catId === 'postres') {
      list = list.filter((item) => {
        const c = (item.category || '').toLowerCase()
        return c.includes('postre')
      })
    } else {
      list = list.filter((item) => (item.category || '').toLowerCase() === catId)
    }
  }

  // Filtrado instantáneo por nombre, descripción o categoría
  const q = searchQuery.value.trim().toLowerCase()
  if (q) {
    list = list.filter((item) =>
      (item.name || '').toLowerCase().includes(q) ||
      (item.description || '').toLowerCase().includes(q) ||
      (item.category || '').toLowerCase().includes(q)
    )
  }

  return list
})

const filteredItems = computed(() => computedItems.value.slice(0, props.displayLimit))

const goToFullMenu = () => {
  router.push('/menu')
}
</script>