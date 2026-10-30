<template>
  <div
    class="group flex flex-col justify-between rounded-2xl bg-white shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-800 dark:bg-gray-800/90 cursor-pointer"
    @click="$emit('view-details', item)"
  >
    <!-- Image Box -->
    <div class="relative overflow-hidden h-48 bg-gray-100 dark:bg-gray-900">
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
        :src="resolveImage(item)"
        :alt="item.name"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        loading="lazy"
        decoding="async"
        @error="handleImageError"
      />
      <!-- Badge -->
      <span
        v-if="item.badge"
        class="absolute top-3 right-3 bg-red-600 text-white text-[11px] font-bold px-2.5 py-1 rounded-full shadow-md"
      >
        {{ item.badge }}
      </span>

      <!-- Quick inspect tag -->
      <span class="absolute bottom-2.5 right-2.5 rounded-md bg-black/60 backdrop-blur-xs px-2 py-0.5 text-[10px] font-bold text-white flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Ingredientes</span>
        <span>🔍</span>
      </span>

      <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-colors"></div>
    </div>

    <!-- Details -->
    <div class="flex flex-1 flex-col justify-between p-5">
      <div>
        <div class="flex items-start justify-between gap-2 mb-1.5">
          <h3 class="text-base font-bold text-gray-800 group-hover:text-red-600 transition-colors dark:text-gray-100">
            {{ item.name }}
          </h3>
          <span class="text-red-600 font-extrabold text-lg whitespace-nowrap">${{ item.price }}</span>
        </div>
        <p class="text-xs text-gray-500 line-clamp-2 mb-4 dark:text-gray-300">
          {{ item.description }}
        </p>
      </div>

      <!-- Footer Action -->
      <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700/60">
        <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400">
          Receta Tradicional
        </span>

        <!-- 
          Botón para ver ingredientes y detalles del platillo.
          El carrito directo está pausado temporalmente; este botón abre el modal detallado
        -->
        <button
          type="button"
          @click.stop="$emit('view-details', item)"
          class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-bold bg-red-50 text-red-700 hover:bg-red-600 hover:text-white dark:bg-red-950/60 dark:text-red-300 dark:hover:bg-red-600 dark:hover:text-white transition-all transform active:scale-95 shadow-2xs cursor-pointer"
        >
          <span>Ver Ingredientes</span>
          <span class="text-xs">→</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { PlusIcon, CheckIcon } from '@heroicons/vue/20/solid'
import { HeartIcon as HeartIconOutline } from '@heroicons/vue/24/outline'
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid'
import { useCart } from '@/composables/useCart'
import { useToasts } from '@/composables/useToasts'
import { useFavorites } from '@/composables/useFavorites'
import { generateFoodPlaceholder } from '@/utils/placeholderGenerator'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['view-details', 'add-to-cart'])

const { addToCart, openCart } = useCart()
const { addToast } = useToasts()
const { isFavorite, toggleFavorite } = useFavorites()
const isAdded = ref(false)

const handleAddToCart = () => {
  addToCart(props.item, 1)
  addToast(`¡"${props.item.name}" agregado al pedido!`, 'success')
  isAdded.value = true
  setTimeout(() => {
    isAdded.value = false
  }, 1600)
}

const resolveImage = (item) => {
  if (item?.image && typeof item.image === 'string' && item.image.trim().length > 0) {
    return item.image
  }
  if (item?.image_url && typeof item.image_url === 'string' && item.image_url.trim().length > 0) {
    return item.image_url
  }
  return generateFoodPlaceholder(item)
}

const handleImageError = (event) => {
  event.target.src = generateFoodPlaceholder(props.item)
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
