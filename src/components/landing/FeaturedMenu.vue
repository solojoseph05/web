<!-- src/components/landing/FeaturedMenu.vue -->
<template>
  <section id="menu" class="py-16 scroll-mt-24 bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4">
      <!-- Encabezado -->
      <div class="text-center mb-12" data-aos="fade-up">
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

      <!-- Category Filter Tabs System -->
      <div class="flex flex-wrap justify-center gap-2 mb-10" data-aos="fade-up">
        <button
          v-for="category in categories"
          :key="category.id"
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

      <!-- Grid de productos -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        <ProductCard
          v-for="(item, index) in filteredItems"
          :key="item.id"
          :item="item"
          data-aos="fade-up"
          :data-aos-delay="index * 60"
          @view-details="openItemDetails"
        />
      </div>

      <!-- Dish Detail Modal (Displays larger image, ingredients list & allergen information) -->
      <MenuItemDetailModal
        :is-open="!!selectedDetailItem"
        :item="selectedDetailItem"
        @close="selectedDetailItem = null"
      />

      <!-- Ver más -->
      <div class="text-center mt-12" data-aos="fade-up">
        <!-- Redirige a la vista completa del menú para mostrar más productos. -->
        <button @click="goToFullMenu" class="bg-red-600 text-white px-8 py-3 rounded-full hover:bg-red-700 transition-all transform hover:scale-105 shadow-lg">
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
    default: 8
  }
})

const selectedCategory = ref('all')
const selectedDetailItem = ref(null)
const router = useRouter()
const { landingMenuItems, landingSettings } = useLandingContent()

const openItemDetails = (item) => {
  selectedDetailItem.value = item
}

const menuTitle = computed(() => landingSettings.value.menu_section_title || 'Platos Destacados')
const menuSubtitle = computed(() => landingSettings.value.menu_section_subtitle || 'Descubre nuestros platillos más populares, preparados con la receta secreta que nos hace únicos.')

const categoryMap = {
  whole: 'Pollo Entero',
  half: 'Medio Pollo',
  combos: 'Combos',
  paquetes: 'Paquetes',
  sides: 'Complementos',
  extras: 'Extras',
  drinks: 'Bebidas',
  bebidas: 'Bebidas',
  tacos: 'Tacos',
  ensaladas: 'Ensaladas',
  postres: 'Postres'
}

const normalizedLandingItems = computed(() =>
  (landingMenuItems.value || []).map((item) => ({
    id: item.id,
    name: item.name,
    description: item.description,
    price: item.price,
    badge: item.badge,
    category: item.category,
    image_url: item.image_url || menu.featured.find((fallbackItem) => fallbackItem.id === item.id || fallbackItem.name === item.name)?.image || '',
  }))
)

const sourceItems = computed(() => {
  if (normalizedLandingItems.value.length > 0) {
    return normalizedLandingItems.value
  }
  return menu.featured
})

const filterTabs = [
  { id: 'all', name: 'Todos', icon: '🍗' },
  { id: 'pollos', name: 'Pollos', icon: '🍗' },
  { id: 'entradas', name: 'Entradas', icon: '🍟' },
  { id: 'combos', name: 'Combos', icon: '🍱' },
  { id: 'bebidas', name: 'Bebidas', icon: '🥤' },
  { id: 'tacos', name: 'Tacos', icon: '🌮' },
  { id: 'postres', name: 'Postres', icon: '🍮' },
]

const categories = computed(() => {
  return filterTabs
})

const computedItems = computed(() => {
  if (selectedCategory.value === 'all') {
    return sourceItems.value
  }

  const catId = selectedCategory.value.toLowerCase()

  if (catId === 'pollos') {
    const polloCats = ['pollo entero', 'medio pollo', 'pollo', 'pollos', 'pollo asado']
    return sourceItems.value.filter(item => {
      const c = (item.category || '').toLowerCase()
      return polloCats.some(pc => c.includes(pc)) || (item.name || '').toLowerCase().includes('pollo')
    })
  }

  if (catId === 'entradas') {
    const entradaCats = ['entradas', 'entrada', 'extras', 'complementos', 'guarniciones', 'papas', 'salsas', 'tortillas', 'nuggets']
    return sourceItems.value.filter(item => {
      const c = (item.category || '').toLowerCase()
      return entradaCats.some(ec => c.includes(ec))
    })
  }

  if (catId === 'combos') {
    const comboCats = ['combos', 'paquetes', 'combos / paquetes', 'combo']
    return sourceItems.value.filter(item => {
      const c = (item.category || '').toLowerCase()
      return comboCats.some(cc => c.includes(cc))
    })
  }

  if (catId === 'bebidas') {
    const drinkCats = ['bebidas', 'drinks', 'refrescos', 'aguas']
    return sourceItems.value.filter(item => {
      const c = (item.category || '').toLowerCase()
      return drinkCats.some(dc => c.includes(dc))
    })
  }

  if (catId === 'tacos') {
    return sourceItems.value.filter(item => {
      const c = (item.category || '').toLowerCase()
      return c.includes('taco') || c.includes('quesadilla')
    })
  }

  if (catId === 'postres') {
    return sourceItems.value.filter(item => {
      const c = (item.category || '').toLowerCase()
      return c.includes('postre')
    })
  }

  return sourceItems.value.filter(item => {
    return (item.category || '').toLowerCase() === catId
  })
})

const filteredItems = computed(() => computedItems.value.slice(0, props.displayLimit))

const goToFullMenu = () => {
  router.push('/menu')
}

const resolveItemImage = (item) => {
  if (item.image_url) {
    return item.image_url
  }
  return item.image
}

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZmZmIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNHB4IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+UG9sbG8gRmVsaXo8L3RleHQ+PC9zdmc+'
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>