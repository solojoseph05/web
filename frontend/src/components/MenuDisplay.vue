<template>
  <div class="w-full">
    <!-- Header Controls: Search & Category Filter (Optional based on props) -->
    <div v-if="showFilters || showSearch" class="mb-8 space-y-4">
      <!-- Search Input Bar -->
      <div v-if="showSearch" class="max-w-2xl mx-auto sm:mx-0">
        <SearchBar
          v-model="searchQuery"
          :total-matches="filteredItems.length"
          placeholder="Buscar platillo por nombre (ej. Pollo Entero, Paquete Familiar, Papas, Horchata)..."
          @clear="searchQuery = ''"
        />
      </div>

      <!-- Categories & View Mode Bar -->
      <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <!-- Category Filter Component with Platillos, Bebidas, Postres, etc. -->
        <div v-if="showFilters" class="flex-1 min-w-0">
          <CategoryFilter v-model="selectedCategory" :items="items" />
        </div>

        <!-- Layout Mode Toggle (Grid vs List) -->
        <div class="flex items-center gap-1 self-end sm:self-auto shrink-0 bg-gray-100 p-1 rounded-xl dark:bg-gray-800">
          <button
            @click="currentLayout = 'grid'"
            class="p-2 rounded-lg text-xs font-medium transition cursor-pointer"
            :class="currentLayout === 'grid' ? 'bg-white shadow-xs text-red-600 dark:bg-gray-700 dark:text-red-400' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400'"
            title="Vista Cuadrícula"
          >
            <Squares2X2Icon class="h-4 w-4" />
          </button>
          <button
            @click="currentLayout = 'list'"
            class="p-2 rounded-lg text-xs font-medium transition cursor-pointer"
            :class="currentLayout === 'list' ? 'bg-white shadow-xs text-red-600 dark:bg-gray-700 dark:text-red-400' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400'"
            title="Vista Lista"
          >
            <ListBulletIcon class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State: Skeletons -->
    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="i in 8"
        :key="`skeleton-${i}`"
        class="animate-pulse rounded-2xl border border-gray-100 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800"
      >
        <div class="h-44 w-full rounded-xl bg-gray-200 dark:bg-gray-700 mb-4"></div>
        <div class="h-4 w-3/4 rounded bg-gray-200 dark:bg-gray-700 mb-2"></div>
        <div class="h-3 w-full rounded bg-gray-200 dark:bg-gray-700 mb-1"></div>
        <div class="h-3 w-5/6 rounded bg-gray-200 dark:bg-gray-700 mb-4"></div>
        <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
          <div class="h-5 w-16 rounded bg-gray-200 dark:bg-gray-700"></div>
          <div class="h-8 w-24 rounded-full bg-gray-200 dark:bg-gray-700"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-200 bg-red-50 p-6 text-center dark:border-red-900/60 dark:bg-red-950/30">
      <p class="text-sm font-semibold text-red-700 dark:text-red-300">{{ errorMessage }}</p>
      <button
        @click="fetchMenuItems"
        class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-red-700 transition"
      >
        <ArrowPathIcon class="h-3.5 w-3.5" />
        <span>Reintentar carga</span>
      </button>
    </div>

    <!-- Empty State: Specific for Favorites -->
    <div
      v-else-if="selectedCategory === 'group:favorites' && filteredItems.length === 0"
      class="rounded-2xl border border-red-100 bg-red-50/50 py-16 text-center dark:border-red-950/40 dark:bg-red-950/20"
    >
      <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-3xl mb-3 dark:bg-red-900/40">
        ❤️
      </div>
      <h3 class="text-base font-bold text-gray-800 dark:text-gray-200">Aún no tienes platillos favoritos</h3>
      <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
        Toca el icono de corazón en cualquiera de nuestros platillos para guardarlo en tus favoritos y acceder a él de inmediato.
      </p>
      <button
        @click="selectedCategory = 'all'"
        class="mt-4 rounded-full bg-red-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-red-700 cursor-pointer"
      >
        Explorar todo el menú
      </button>
    </div>

    <!-- Empty State: Generic -->
    <div v-else-if="filteredItems.length === 0" class="rounded-2xl border border-gray-200 bg-gray-50/70 py-16 text-center dark:border-gray-800 dark:bg-gray-900/50">
      <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-xl text-red-600 dark:bg-red-950/50 mb-3">
        🍗
      </div>
      <h3 class="text-base font-bold text-gray-800 dark:text-gray-200">No se encontraron platillos</h3>
      <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
        No hay alimentos disponibles bajo los filtros seleccionados o la búsqueda actual.
      </p>
      <button
        v-if="searchQuery || selectedCategory !== 'all'"
        @click="resetFilters"
        class="mt-4 rounded-full bg-red-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-red-700 cursor-pointer"
      >
        Ver todos los alimentos
      </button>
    </div>

    <!-- Rendered Food Items (GRID LAYOUT) -->
    <div
      v-else-if="currentLayout === 'grid'"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
    >
      <article
        v-for="item in filteredItems"
        :key="item.id"
        @click="selectedDetailItem = item"
        class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-gray-800 dark:bg-gray-800/90 cursor-pointer"
      >
        <!-- Dish Thumbnail -->
        <div class="relative h-48 w-full overflow-hidden bg-gray-100 dark:bg-gray-900">
          <!-- Favorite Bookmark Button -->
          <button
            @click.stop="toggleFavorite(item)"
            type="button"
            class="absolute top-3 left-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 shadow-md backdrop-blur-xs transition-all duration-200 hover:scale-110 active:scale-90 dark:bg-gray-800/90 cursor-pointer"
            :class="isFavorite(item.id) ? 'text-red-500' : 'text-gray-400 hover:text-red-500'"
            :aria-label="isFavorite(item.id) ? 'Quitar de favoritos' : 'Agregar a favoritos'"
            :title="isFavorite(item.id) ? 'Quitar de favoritos' : 'Guardar en favoritos'"
          >
            <HeartIconSolid v-if="isFavorite(item.id)" class="h-4.5 w-4.5 text-red-500" />
            <HeartIconOutline v-else class="h-4.5 w-4.5 text-gray-400 hover:text-red-500" />
          </button>

          <img
            :src="resolveItemImage(item)"
            :alt="item.name"
            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            loading="lazy"
            decoding="async"
            @error="handleImgError($event, item)"
          />
          <div class="absolute inset-0 bg-linear-to-t from-black/30 via-transparent to-transparent"></div>

          <!-- Promotional or Popular Badge -->
          <span
            v-if="item.badge"
            class="absolute top-3 right-3 rounded-full bg-red-600 px-2.5 py-1 text-[11px] font-bold text-white shadow-md"
          >
            {{ item.badge }}
          </span>

          <!-- Category Tag -->
          <span class="absolute bottom-3 left-3 rounded-md bg-black/60 backdrop-blur-xs px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">
            {{ item.category }}
          </span>
        </div>

        <!-- Food Details: Name, Description, Price -->
        <div class="flex flex-1 flex-col justify-between p-5">
          <div>
            <div class="flex items-start justify-between gap-2 mb-2">
              <h3 class="text-base font-bold text-gray-900 group-hover:text-red-600 transition-colors dark:text-white">
                {{ item.name }}
              </h3>
              <span class="text-base font-black text-red-600 whitespace-nowrap">
                ${{ formatPrice(item.price) }}
              </span>
            </div>

            <!-- Description -->
            <p class="text-xs text-gray-600 leading-relaxed line-clamp-2 mb-4 dark:text-gray-300">
              {{ item.description || 'Preparado con pollo fresco marinado al momento con la receta tradicional.' }}
            </p>
          </div>

          <!-- Bottom Action Bar -->
          <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700/60">
            <button
              @click.stop="selectedDetailItem = item"
              class="text-[11px] font-bold text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition cursor-pointer flex items-center gap-1"
            >
              <span>Ingredientes</span>
              <span>🔍</span>
            </button>
            <button
              @click.stop="handleAddToCart(item)"
              class="inline-flex items-center gap-1.5 rounded-full bg-red-600 px-4 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-red-700 active:scale-95 cursor-pointer"
            >
              <PlusIcon class="h-3.5 w-3.5" />
              <span>Agregar</span>
            </button>
          </div>
        </div>
      </article>
    </div>

    <!-- Rendered Food Items (LIST LAYOUT) -->
    <div v-else class="space-y-4">
      <article
        v-for="item in filteredItems"
        :key="`list-${item.id}`"
        @click="selectedDetailItem = item"
        class="group flex flex-col sm:flex-row items-stretch justify-between gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all duration-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-800 cursor-pointer"
      >
        <div class="flex items-start gap-4 flex-1">
          <div class="relative shrink-0">
            <img
              :src="resolveItemImage(item)"
              :alt="item.name"
              class="h-24 w-24 rounded-xl object-cover"
              loading="lazy"
              @error="handleImgError($event, item)"
            />
            <button
              @click.stop="toggleFavorite(item)"
              type="button"
              class="absolute top-1.5 left-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-white/90 shadow-xs backdrop-blur-xs transition-all hover:scale-110 active:scale-90 dark:bg-gray-800/90 cursor-pointer"
              :class="isFavorite(item.id) ? 'text-red-500' : 'text-gray-400 hover:text-red-500'"
              :aria-label="isFavorite(item.id) ? 'Quitar de favoritos' : 'Agregar a favoritos'"
            >
              <HeartIconSolid v-if="isFavorite(item.id)" class="h-3.5 w-3.5 text-red-500" />
              <HeartIconOutline v-else class="h-3.5 w-3.5 text-gray-400 hover:text-red-500" />
            </button>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <h3 class="text-base font-bold text-gray-900 group-hover:text-red-600 transition-colors dark:text-white">
                {{ item.name }}
              </h3>
              <span
                v-if="item.badge"
                class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-600 dark:bg-red-950/60 dark:text-red-400"
              >
                {{ item.badge }}
              </span>
            </div>
            <p class="text-xs text-gray-500 line-clamp-2 mb-2 dark:text-gray-300">
              {{ item.description }}
            </p>
            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
              {{ item.category }}
            </span>
          </div>
        </div>

        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center border-t sm:border-t-0 sm:border-l border-gray-100 pt-3 sm:pt-0 sm:pl-6 dark:border-gray-700">
          <div class="text-lg font-black text-red-600 sm:mb-2">
            ${{ formatPrice(item.price) }}
          </div>
          <button
            @click="handleAddToCart(item)"
            class="inline-flex items-center gap-1.5 rounded-full bg-red-600 px-5 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-red-700 active:scale-95 cursor-pointer"
          >
            <PlusIcon class="h-3.5 w-3.5" />
            <span>Agregar</span>
          </button>
        </div>
      </article>
    </div>

    <!-- Quick Item Details Modal (Displays larger image, ingredients list & allergen information) -->
    <MenuItemDetailModal
      :is-open="!!selectedDetailItem"
      :item="selectedDetailItem"
      @close="selectedDetailItem = null"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  MagnifyingGlassIcon,
  XMarkIcon,
  Squares2X2Icon,
  ListBulletIcon,
  ArrowPathIcon,
  PlusIcon,
  HeartIcon as HeartIconOutline,
} from '@heroicons/vue/24/outline'
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid'
import api from '@/plugins/axios'
import menuConfig from '@/config/menu'
import CategoryFilter from '@/components/menu/CategoryFilter.vue'
import SearchBar from '@/components/menu/SearchBar.vue'
import MenuItemDetailModal from '@/components/menu/MenuItemDetailModal.vue'
import { generateFoodPlaceholder } from '@/utils/placeholderGenerator'
import { useCart } from '@/composables/useCart'
import { useToasts } from '@/composables/useToasts'
import { useFavorites } from '@/composables/useFavorites'

const selectedDetailItem = ref(null)

const props = defineProps({
  limit: {
    type: Number,
    default: null,
  },
  category: {
    type: String,
    default: 'all',
  },
  featuredOnly: {
    type: Boolean,
    default: false,
  },
  showFilters: {
    type: Boolean,
    default: true,
  },
  showSearch: {
    type: Boolean,
    default: true,
  },
  layout: {
    type: String,
    default: 'grid', // 'grid' | 'list'
  },
})

const { addToCart, openCart } = useCart()
const { addToast } = useToasts()
const { isFavorite, toggleFavorite } = useFavorites()

const items = ref([])
const isLoading = ref(true)
const errorMessage = ref('')
const searchQuery = ref('')
const selectedCategory = ref(props.category)
const currentLayout = ref(props.layout)

const fallbackImage = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZmZmIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNHB4IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+UG9sbG8gRmVsaXo8L3RleHQ+PC9zdmc+'

const fetchMenuItems = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await api.get('/landing/content')
    const menuData = response.data?.menu_items || []

    if (Array.isArray(menuData) && menuData.length > 0) {
      items.value = menuData.map((item) => ({
        id: item.id,
        name: item.name,
        price: Number(item.price) || 0,
        description: item.description || '',
        category: item.category || 'Pollo Entero',
        badge: item.badge || '',
        is_featured: item.is_featured ?? true,
        image: item.image_url || menuConfig.featured.find(f => f.name === item.name || f.id === item.id)?.image || '',
      }))
    } else {
      // Fallback to static menu catalog
      items.value = menuConfig.featured
    }
  } catch (error) {
    console.warn('Fallback to local menu items:', error)
    items.value = menuConfig.featured
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchMenuItems()
})

const availableCategories = computed(() => {
  const cats = Array.from(new Set(items.value.map((i) => i.category))).filter(Boolean)
  return [
    { id: 'all', name: 'Todos', count: items.value.length },
    ...cats.map((c) => ({
      id: c,
      name: c,
      count: items.value.filter((i) => i.category === c).length,
    })),
  ]
})

const filteredItems = computed(() => {
  let result = items.value

  if (props.featuredOnly) {
    result = result.filter((i) => i.is_featured)
  }

  if (selectedCategory.value !== 'all') {
    if (selectedCategory.value.startsWith('group:')) {
      const groupKey = selectedCategory.value.replace('group:', '')
      if (groupKey === 'favorites') {
        result = result.filter((i) => isFavorite(i.id))
      } else if (groupKey === 'pollos' || groupKey === 'platillos') {
        const polloCategories = ['pollo entero', 'medio pollo', 'pollo', 'pollos', 'pollo asado']
        result = result.filter((i) => {
          const cat = (i.category || '').toLowerCase().trim()
          return polloCategories.some((c) => cat.includes(c)) || (i.name || '').toLowerCase().includes('pollo')
        })
      } else if (groupKey === 'entradas' || groupKey === 'extras' || groupKey === 'complementos') {
        const extraCats = ['entradas', 'entrada', 'extras', 'complementos', 'guarniciones', 'papas', 'salsas', 'tortillas', 'nuggets']
        result = result.filter((i) => {
          const cat = (i.category || '').toLowerCase().trim()
          return extraCats.some((c) => cat.includes(c))
        })
      } else if (groupKey === 'combos' || groupKey === 'paquetes') {
        const comboCats = ['combos', 'paquetes', 'combos / paquetes', 'combo']
        result = result.filter((i) => comboCats.includes((i.category || '').toLowerCase().trim()))
      } else if (groupKey === 'bebidas') {
        const drinkCats = ['bebidas', 'drinks', 'refrescos', 'aguas']
        result = result.filter((i) => drinkCats.includes((i.category || '').toLowerCase().trim()))
      } else if (groupKey === 'tacos') {
        const tacoCats = ['tacos', 'quesadillas']
        result = result.filter((i) => tacoCats.includes((i.category || '').toLowerCase().trim()))
      } else if (groupKey === 'ensaladas') {
        result = result.filter((i) => (i.category || '').toLowerCase().trim() === 'ensaladas')
      } else if (groupKey === 'postres') {
        result = result.filter((i) => (i.category || '').toLowerCase().trim() === 'postres')
      } else if (groupKey.startsWith('custom_')) {
        const cleanName = groupKey.replace('custom_', '').replace(/_/g, ' ')
        result = result.filter((i) => (i.category || '').toLowerCase().trim() === cleanName)
      }
    } else {
      const targetCat = selectedCategory.value.toLowerCase().trim()
      result = result.filter((i) => (i.category || '').toLowerCase().trim() === targetCat)
    }
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    result = result
      .filter(
        (i) =>
          i.name.toLowerCase().includes(q) ||
          i.description.toLowerCase().includes(q) ||
          i.category.toLowerCase().includes(q)
      )
      .sort((a, b) => {
        const aName = a.name.toLowerCase().includes(q)
        const bName = b.name.toLowerCase().includes(q)
        if (aName && !bName) return -1
        if (!aName && bName) return 1
        return 0
      })
  }

  if (props.limit && props.limit > 0) {
    result = result.slice(0, props.limit)
  }

  return result
})

const handleAddToCart = (item) => {
  addToCart(item, 1)
  addToast(`¡"${item.name}" agregado a tu pedido!`, 'success')
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = 'all'
}

const formatPrice = (price) => {
  return Number(price || 0).toLocaleString('es-MX', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  })
}

const resolveItemImage = (item) => {
  if (item?.image && typeof item.image === 'string' && item.image.trim().length > 0) {
    return item.image
  }
  if (item?.image_url && typeof item.image_url === 'string' && item.image_url.trim().length > 0) {
    return item.image_url
  }
  return generateFoodPlaceholder(item)
}

const handleImgError = (event, item) => {
  event.target.src = generateFoodPlaceholder(item)
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
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
