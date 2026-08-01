<!-- src/components/landing/FeaturedMenu.vue -->
<template>
  <section id="menu" class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4">
      <!-- Encabezado -->
      <div class="text-center mb-12" data-aos="fade-up">
        <span class="inline-block bg-red-100 text-red-600 text-sm font-semibold px-4 py-2 rounded-full mb-4 dark:bg-red-900/40 dark:text-red-200">
          🍗 Nuestro Menú
        </span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 dark:text-gray-100">
          Platos <span class="text-red-600">Destacados</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto dark:text-gray-300">
          Descubre nuestros platillos más populares, preparados con la receta secreta que nos hace únicos
        </p>
      </div>

      <!-- Filtros -->
      <div class="flex flex-wrap justify-center gap-2 mb-10" data-aos="fade-up">
        <button
          v-for="category in categories"
          :key="category.id"
          @click="selectedCategory = category.id"
          class="px-4 py-2 rounded-full transition-all duration-300 text-sm font-medium"
          :class="selectedCategory === category.id 
            ? 'bg-red-600 text-white shadow-lg' 
            : 'bg-white text-gray-600 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'"
        >
          {{ category.name }}
        </button>
      </div>

      <!-- Grid de productos -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="(item, index) in filteredItems"
          :key="item.id"
          class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden dark:bg-gray-800 dark:border dark:border-gray-700"
          data-aos="fade-up"
          :data-aos-delay="index * 100"
        >
          <!-- Imagen -->
          <div class="relative overflow-hidden h-48">
            <img
              :src="resolveItemImage(item)"
              :alt="item.name"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              loading="lazy"
              decoding="async"
              width="640"
              height="384"
              @error="handleImageError"
            />
            <!-- Badge -->
            <span v-if="item.badge" class="absolute top-4 right-4 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
              {{ item.badge }}
            </span>
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors duration-300"></div>
          </div>

          <!-- Contenido -->
          <div class="p-5">
            <div class="flex justify-between items-start mb-2">
              <h3 class="text-lg font-bold text-gray-800 group-hover:text-red-600 transition-colors dark:text-gray-100">
                {{ item.name }}
              </h3>
              <span class="text-red-600 font-bold text-lg">${{ item.price }}</span>
            </div>
            <p class="text-sm text-gray-600 mb-3 line-clamp-2 dark:text-gray-300">{{ item.description }}</p>
            <div class="flex items-center justify-between">
              <span class="text-xs text-gray-400 dark:text-gray-400">{{ item.category }}</span>
              <button class="bg-red-600 text-white px-4 py-1.5 rounded-full text-sm hover:bg-red-700 transition-all transform hover:scale-105">
                Agregar
              </button>
            </div>
          </div>
        </div>
      </div>

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
import menu from '@/config/menu'
import { useLandingContent } from '@/composables/useLandingContent'

const props = defineProps({
  displayLimit: {
    type: Number,
    default: 4
  }
})

const selectedCategory = ref('all')
const router = useRouter()
const { landingMenuItems } = useLandingContent()

const categoryMap = {
  whole: 'Pollo Entero',
  half: 'Medio Pollo',
  combos: 'Paquetes',
  sides: 'Complementos',
  drinks: 'Bebidas',
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

const categories = computed(() => {
  const availableCategories = Array.from(new Set(sourceItems.value.map((item) => item.category)))
  return [
    { id: 'all', name: 'Todos' },
    ...availableCategories.map((categoryName) => ({
      id: categoryName,
      name: categoryName,
    })),
  ]
})

const computedItems = computed(() => {
  if (selectedCategory.value === 'all') {
    return sourceItems.value
  }
  const categoryName = categoryMap[selectedCategory.value] || selectedCategory.value
  return sourceItems.value.filter(item => item.category === categoryName)
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