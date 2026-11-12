<!-- src/components/menu/MenuGrid.vue -->
<template>
  <div class="w-full">
    <!-- Category Filter Tabs System (Optional based on showTabs prop) -->
    <div v-if="showTabs" class="mb-8 space-y-4">
      <CategoryFilter
        v-model="activeCategory"
        :items="items"
        @change="handleCategoryChange"
      />
    </div>

    <!-- Active Filter Indicator & Counter Banner -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3 px-1">
      <div class="flex items-center gap-2">
        <span class="text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300">
          Mostrando:
        </span>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-black text-red-700 dark:bg-red-950/60 dark:text-red-300">
          <span>{{ currentCategoryLabel }}</span>
          <span class="rounded-full bg-red-600 px-1.5 py-0.2 text-[10px] text-white">
            {{ displayedItems.length }}
          </span>
        </span>
      </div>

      <!-- Quick Layout Selector (Grid vs List) if enabled -->
      <div v-if="allowLayoutSwitch" class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl">
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

    <!-- Loading Skeleton State -->
    <div
      v-if="loading"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
    >
      <div
        v-for="i in 8"
        :key="`skeleton-${i}`"
        class="animate-pulse rounded-2xl border border-gray-100 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800"
      >
        <div class="h-48 w-full rounded-xl bg-gray-200 dark:bg-gray-700 mb-4"></div>
        <div class="h-4 w-3/4 rounded bg-gray-200 dark:bg-gray-700 mb-2"></div>
        <div class="h-3 w-full rounded bg-gray-200 dark:bg-gray-700 mb-1"></div>
        <div class="h-3 w-5/6 rounded bg-gray-200 dark:bg-gray-700 mb-4"></div>
        <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
          <div class="h-6 w-20 rounded bg-gray-200 dark:bg-gray-700"></div>
          <div class="h-8 w-24 rounded-full bg-gray-200 dark:bg-gray-700"></div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="displayedItems.length === 0"
      class="rounded-3xl border border-gray-200 bg-gray-50/70 p-12 text-center dark:border-gray-800 dark:bg-gray-900/50"
    >
      <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-red-100 text-3xl mb-3 dark:bg-red-950/60">
        🍗
      </div>
      <h3 class="text-base font-bold text-gray-900 dark:text-white">
        No hay platillos disponibles en esta categoría
      </h3>
      <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
        Prueba explorando otra categoría como 'Pollos', 'Entradas' o 'Bebidas'.
      </p>
      <button
        @click="resetCategory"
        class="mt-4 rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-red-700 transition cursor-pointer"
      >
        Ver Todos los Alimentos
      </button>
    </div>

    <!-- RESPONSIVE MENU GRID (1 col mobile, 2 cols tablet, 3-4 cols desktop) -->
    <div
      v-else-if="currentLayout === 'grid'"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
    >
      <article
        v-for="item in displayedItems"
        :key="item.id"
        class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-md transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl dark:border-gray-800 dark:bg-gray-800/90 relative"
      >
        <!-- Dish Thumbnail Container -->
        <div class="relative h-48 sm:h-52 w-full overflow-hidden bg-gray-100 dark:bg-gray-900">
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

          <!-- Dish Photo -->
          <img
            :src="resolveItemImage(item)"
            :alt="item.name"
            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            loading="lazy"
            decoding="async"
            @error="handleImgError($event, item)"
          />
          <div class="absolute inset-0 bg-linear-to-t from-black/40 via-transparent to-transparent"></div>

          <!-- Promotional or Popular Badge -->
          <span
            v-if="item.badge"
            class="absolute top-3 right-3 rounded-full bg-red-600 px-3 py-1 text-[11px] font-black text-white shadow-md tracking-tight"
          >
            {{ item.badge }}
          </span>

          <!-- Category Pill -->
          <span class="absolute bottom-3 left-3 rounded-md bg-black/60 backdrop-blur-xs px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">
            {{ item.category }}
          </span>

          <!-- Quick Detail Modal Button on Image Hover -->
          <button
            @click.stop="openDetailModal(item)"
            class="absolute bottom-3 right-3 z-10 hidden sm:flex items-center gap-1 rounded-md bg-white/90 px-2 py-0.5 text-[10px] font-bold text-gray-800 shadow-sm backdrop-blur-xs opacity-0 group-hover:opacity-100 transition-opacity hover:bg-white cursor-pointer dark:bg-gray-800/90 dark:text-gray-200"
          >
            <span>Detalles</span>
            <InformationCircleIcon class="h-3 w-3" />
          </button>
        </div>

        <!-- Food Card Content: Name, Description, Price -->
        <div class="flex flex-1 flex-col justify-between p-5">
          <div>
            <div class="flex items-start justify-between gap-2 mb-2">
              <h3
                @click="openDetailModal(item)"
                class="text-base font-black text-gray-900 group-hover:text-red-600 transition-colors dark:text-white cursor-pointer line-clamp-1"
                :title="item.name"
              >
                {{ item.name }}
              </h3>
              <span class="text-base font-black text-red-600 dark:text-red-400 whitespace-nowrap">
                ${{ formatPrice(item.price) }} <span class="text-[10px] font-semibold text-gray-400">MXN</span>
              </span>
            </div>

            <!-- Description with 2-line clamp -->
            <p class="text-xs text-gray-600 leading-relaxed line-clamp-2 mb-4 dark:text-gray-300">
              {{ item.description || 'Preparado fresco con la auténtica receta al carbón marinada de Pollo Feliz.' }}
            </p>
          </div>

          <!-- Bottom Action Bar -->
          <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700/60">
            <span class="text-[11px] text-gray-400 font-medium">Al carbón</span>

            <button
              @click="handleAddToCart(item)"
              class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-xs font-black text-white shadow-md transition-all active:scale-95 cursor-pointer"
              :class="addedItemIds.includes(item.id)
                ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/20'
                : 'bg-red-600 hover:bg-red-700 shadow-red-500/20'"
            >
              <CheckIcon v-if="addedItemIds.includes(item.id)" class="h-3.5 w-3.5" />
              <PlusIcon v-else class="h-3.5 w-3.5" />
              <span>{{ addedItemIds.includes(item.id) ? '¡Agregado!' : 'Agregar' }}</span>
            </button>
          </div>
        </div>
      </article>
    </div>

    <!-- RESPONSIVE MENU LIST (Alternative View) -->
    <div v-else class="space-y-4">
      <article
        v-for="item in displayedItems"
        :key="`list-${item.id}`"
        class="group flex flex-col sm:flex-row items-stretch justify-between gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all duration-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-800"
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
            >
              <HeartIconSolid v-if="isFavorite(item.id)" class="h-3.5 w-3.5 text-red-500" />
              <HeartIconOutline v-else class="h-3.5 w-3.5 text-gray-400 hover:text-red-500" />
            </button>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <h3
                @click="openDetailModal(item)"
                class="text-base font-bold text-gray-900 group-hover:text-red-600 transition-colors dark:text-white cursor-pointer"
              >
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
          <div class="text-lg font-black text-red-600 dark:text-red-400 sm:mb-2">
            ${{ formatPrice(item.price) }} <span class="text-xs font-semibold text-gray-400">MXN</span>
          </div>
          <button
            @click="handleAddToCart(item)"
            class="inline-flex items-center gap-1.5 rounded-full px-5 py-2 text-xs font-bold text-white shadow-xs transition active:scale-95 cursor-pointer"
            :class="addedItemIds.includes(item.id) ? 'bg-emerald-600' : 'bg-red-600 hover:bg-red-700'"
          >
            <CheckIcon v-if="addedItemIds.includes(item.id)" class="h-3.5 w-3.5" />
            <PlusIcon v-else class="h-3.5 w-3.5" />
            <span>{{ addedItemIds.includes(item.id) ? 'Agregado' : 'Agregar' }}</span>
          </button>
        </div>
      </article>
    </div>

    <!-- Quick Item Details Modal (Displays larger image, ingredients list & allergen information) -->
    <MenuItemDetailModal
      :is-open="!!selectedDetailItem"
      :item="selectedDetailItem"
      @close="selectedDetailItem = null"
      @add-to-cart="handleAddFromModal"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  Squares2X2Icon,
  ListBulletIcon,
  PlusIcon,
  CheckIcon,
  InformationCircleIcon,
  HeartIcon as HeartIconOutline,
} from '@heroicons/vue/24/outline'
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid'
import CategoryFilter from '@/components/menu/CategoryFilter.vue'
import MenuItemDetailModal from '@/components/menu/MenuItemDetailModal.vue'
import { useCart } from '@/composables/useCart'
import { useToasts } from '@/composables/useToasts'
import { useFavorites } from '@/composables/useFavorites'
import { generateFoodPlaceholder } from '@/utils/placeholderGenerator'

const props = defineProps({
  items: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  showTabs: {
    type: Boolean,
    default: true,
  },
  allowLayoutSwitch: {
    type: Boolean,
    default: true,
  },
  defaultCategory: {
    type: String,
    default: 'all',
  },
  limit: {
    type: Number,
    default: null,
  },
})

const emit = defineEmits(['category-change', 'add-to-cart'])

const { addToCart } = useCart()
const { addToast } = useToasts()
const { isFavorite, toggleFavorite } = useFavorites()

const activeCategory = ref(props.defaultCategory)
const currentLayout = ref('grid') // 'grid' | 'list'
const addedItemIds = ref([])
const selectedDetailItem = ref(null)
const modalQuantity = ref(1)

// Map category filter to filtered items
const displayedItems = computed(() => {
  let list = props.items || []

  if (activeCategory.value !== 'all') {
    if (activeCategory.value.startsWith('group:')) {
      const groupKey = activeCategory.value.replace('group:', '')
      if (groupKey === 'favorites') {
        list = list.filter((i) => isFavorite(i.id))
      } else if (groupKey === 'pollos' || groupKey === 'platillos') {
        const polloCategories = ['pollo entero', 'medio pollo', 'pollo', 'pollos', 'pollo asado']
        list = list.filter((i) => {
          const cat = (i.category || '').toLowerCase().trim()
          return polloCategories.some((c) => cat.includes(c)) || (i.name || '').toLowerCase().includes('pollo')
        })
      } else if (groupKey === 'entradas' || groupKey === 'extras' || groupKey === 'complementos') {
        const extraCats = ['entradas', 'entrada', 'extras', 'complementos', 'guarniciones', 'papas', 'salsas', 'tortillas', 'nuggets']
        list = list.filter((i) => {
          const cat = (i.category || '').toLowerCase().trim()
          return extraCats.some((c) => cat.includes(c))
        })
      } else if (groupKey === 'combos' || groupKey === 'paquetes') {
        const comboCats = ['combos', 'paquetes', 'combos / paquetes', 'combo']
        list = list.filter((i) => comboCats.includes((i.category || '').toLowerCase().trim()))
      } else if (groupKey === 'bebidas') {
        const drinkCats = ['bebidas', 'drinks', 'refrescos', 'aguas']
        list = list.filter((i) => drinkCats.includes((i.category || '').toLowerCase().trim()))
      } else if (groupKey === 'tacos') {
        const tacoCats = ['tacos', 'quesadillas']
        list = list.filter((i) => tacoCats.includes((i.category || '').toLowerCase().trim()))
      } else if (groupKey === 'ensaladas') {
        list = list.filter((i) => (i.category || '').toLowerCase().trim() === 'ensaladas')
      } else if (groupKey === 'postres') {
        list = list.filter((i) => (i.category || '').toLowerCase().trim() === 'postres')
      } else if (groupKey.startsWith('custom_')) {
        const cleanName = groupKey.replace('custom_', '').replace(/_/g, ' ')
        list = list.filter((i) => (i.category || '').toLowerCase().trim() === cleanName)
      }
    } else {
      const targetCat = activeCategory.value.toLowerCase().trim()
      list = list.filter((i) => (i.category || '').toLowerCase().trim() === targetCat)
    }
  }

  if (props.limit && props.limit > 0) {
    list = list.slice(0, props.limit)
  }

  return list
})

const currentCategoryLabel = computed(() => {
  if (activeCategory.value === 'all') return 'Todos los platillos'
  if (activeCategory.value === 'group:pollos') return 'Pollos Asados'
  if (activeCategory.value === 'group:entradas') return 'Entradas & Complementos'
  if (activeCategory.value === 'group:combos') return 'Combos & Paquetes'
  if (activeCategory.value === 'group:bebidas') return 'Bebidas'
  if (activeCategory.value === 'group:tacos') return 'Tacos & Quesadillas'
  if (activeCategory.value === 'group:postres') return 'Postres'
  if (activeCategory.value === 'group:favorites') return 'Tus Favoritos'
  return activeCategory.value
})

const handleCategoryChange = (catData) => {
  emit('category-change', catData)
}

const resetCategory = () => {
  activeCategory.value = 'all'
}

const handleAddToCart = (item) => {
  addToCart(item, 1)
  addToast(`¡"${item.name}" agregado a tu pedido!`, 'success')

  if (!addedItemIds.value.includes(item.id)) {
    addedItemIds.value.push(item.id)
    setTimeout(() => {
      const idx = addedItemIds.value.indexOf(item.id)
      if (idx > -1) addedItemIds.value.splice(idx, 1)
    }, 1800)
  }

  emit('add-to-cart', item)
}

const openDetailModal = (item) => {
  selectedDetailItem.value = item
  modalQuantity.value = 1
}

const handleAddFromModal = ({ item, quantity }) => {
  emit('add-to-cart', item)
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
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
