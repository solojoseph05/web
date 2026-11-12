<!-- src/components/menu/MenuItemDetailModal.vue -->
<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="isOpen && item"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-black/75 backdrop-blur-sm overflow-y-auto"
        @click.self="handleClose"
      >
        <div
          class="relative w-full max-w-2xl rounded-3xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden my-auto max-h-[92vh] flex flex-col"
          role="dialog"
          aria-modal="true"
          :aria-labelledby="`modal-title-${item.id}`"
        >
          <!-- Sticky Modal Header Controls (Close & Favorite) -->
          <div class="absolute top-3 right-3 z-20 flex items-center gap-2">
            <!-- Favorite button -->
            <button
              @click.stop="toggleFavorite(item)"
              type="button"
              class="flex h-10 w-10 items-center justify-center rounded-full bg-white/90 shadow-md backdrop-blur-md transition hover:scale-110 active:scale-90 dark:bg-gray-800/90 cursor-pointer"
              :class="isFavorite(item.id) ? 'text-red-500' : 'text-gray-400 hover:text-red-500'"
              :title="isFavorite(item.id) ? 'Quitar de favoritos' : 'Guardar en favoritos'"
            >
              <HeartIconSolid v-if="isFavorite(item.id)" class="h-5 w-5 text-red-500" />
              <HeartIconOutline v-else class="h-5 w-5 text-gray-400 hover:text-red-500" />
            </button>

            <!-- Close button -->
            <button
              @click="handleClose"
              class="flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-gray-700 shadow-md backdrop-blur-md transition hover:bg-white hover:text-black dark:bg-gray-800/90 dark:text-gray-200 dark:hover:bg-gray-700 cursor-pointer"
              title="Cerrar ventana"
            >
              <XMarkIcon class="h-5 w-5" />
            </button>
          </div>

          <!-- Scrollable Body Content -->
          <div class="overflow-y-auto flex-1 no-scrollbar">
            <!-- LARGER DISH IMAGE HERO -->
            <div class="relative h-64 sm:h-80 w-full overflow-hidden bg-gray-100 dark:bg-gray-950 group">
              <img
                :src="resolveItemImage(item)"
                :alt="item.name"
                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                @error="handleImgError"
              />
              <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/25 to-transparent"></div>

              <!-- Badges on Image -->
              <div class="absolute bottom-4 left-5 right-5 flex flex-wrap items-end justify-between gap-3">
                <div class="space-y-1">
                  <div class="flex items-center gap-2">
                    <span class="rounded-md bg-red-600 px-2.5 py-1 text-[11px] font-black uppercase tracking-wider text-white shadow-md">
                      {{ item.category || 'Pollo Feliz' }}
                    </span>
                    <span
                      v-if="item.badge"
                      class="rounded-md bg-amber-400 px-2.5 py-1 text-[11px] font-black text-gray-950 shadow-md"
                    >
                      ★ {{ item.badge }}
                    </span>
                  </div>
                  <h2 :id="`modal-title-${item.id}`" class="text-2xl sm:text-3xl font-black text-white drop-shadow-md">
                    {{ item.name }}
                  </h2>
                </div>

                <div class="rounded-2xl bg-black/60 backdrop-blur-md px-4 py-2 text-right border border-white/20">
                  <span class="text-xs text-red-200 font-bold block">Precio</span>
                  <span class="text-2xl sm:text-3xl font-black text-white">
                    ${{ formatPrice(item.price) }}
                  </span>
                  <span class="text-[10px] text-gray-300 ml-1 font-bold">MXN</span>
                </div>
              </div>
            </div>

            <!-- DETAILS, INGREDIENTS & ALLERGENS TABS -->
            <div class="p-5 sm:p-6 space-y-6">
              <!-- Description -->
              <div class="space-y-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                  Descripción del Platillo
                </h4>
                <p class="text-sm sm:text-base text-gray-700 dark:text-gray-200 leading-relaxed">
                  {{ item.description || 'Preparado diariamente con pollo fresco de primera calidad, marinado con la receta tradicional secreta de hierbas y cítricos naturales, asado pacientemente al calor del carbón de mezquite.' }}
                </p>
              </div>

              <!-- Quick Highlights Grid -->
              <div class="grid grid-cols-3 gap-3">
                <div class="rounded-2xl bg-red-50 p-3 text-center dark:bg-red-950/30 border border-red-100 dark:border-red-900/40">
                  <span class="text-lg block">🔥</span>
                  <span class="text-[11px] font-bold text-red-700 dark:text-red-300 block mt-0.5">Al Carbón</span>
                  <span class="text-[10px] text-gray-500 dark:text-gray-400">100% Mezquite</span>
                </div>
                <div class="rounded-2xl bg-amber-50 p-3 text-center dark:bg-amber-950/30 border border-amber-100 dark:border-amber-900/40">
                  <span class="text-lg block">⏱️</span>
                  <span class="text-[11px] font-bold text-amber-700 dark:text-amber-300 block mt-0.5">Preparación</span>
                  <span class="text-[10px] text-gray-500 dark:text-gray-400">10 - 15 min</span>
                </div>
                <div class="rounded-2xl bg-emerald-50 p-3 text-center dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40">
                  <span class="text-lg block">🌶️</span>
                  <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 block mt-0.5">Picante</span>
                  <span class="text-[10px] text-gray-500 dark:text-gray-400">{{ (allergensInfo.spiceLevel || 'Medio').split(' ')[0] }}</span>
                </div>
              </div>

              <!-- INGREDIENTS LIST -->
              <div class="space-y-3 rounded-2xl bg-gray-50 p-4 sm:p-5 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between">
                  <h4 class="text-sm font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <span>🥗</span>
                    <span>Lista de Ingredientes & Acompañamientos</span>
                  </h4>
                  <span class="rounded-full bg-white dark:bg-gray-700 px-2.5 py-0.5 text-[10px] font-bold text-gray-600 dark:text-gray-300">
                    {{ ingredientsList.length }} elementos
                  </span>
                </div>

                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-300">
                  <li
                    v-for="(ingredient, idx) in ingredientsList"
                    :key="`ing-${idx}`"
                    class="flex items-start gap-2 bg-white dark:bg-gray-800 p-2.5 rounded-xl border border-gray-100 dark:border-gray-700/60"
                  >
                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                    <span>{{ ingredient }}</span>
                  </li>
                </ul>
              </div>

              <!-- ALLERGENS INFORMATION -->
              <div class="space-y-3 rounded-2xl bg-amber-50/70 p-4 sm:p-5 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/50">
                <div class="flex items-center justify-between">
                  <h4 class="text-sm font-black text-amber-900 dark:text-amber-200 flex items-center gap-2">
                    <span>⚠️</span>
                    <span>Información de Alérgenos & Dietas</span>
                  </h4>
                  <span class="text-[11px] font-semibold text-amber-700 dark:text-amber-400">
                    Nivel de picante: {{ allergensInfo.spiceLevel }}
                  </span>
                </div>

                <!-- Contains Allergens -->
                <div v-if="allergensInfo.contains && allergensInfo.contains.length > 0" class="space-y-1.5">
                  <p class="text-xs font-bold text-amber-800 dark:text-amber-300">
                    Contiene los siguientes alérgenos declarados:
                  </p>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="allergen in allergensInfo.contains"
                      :key="allergen.id"
                      class="inline-flex items-center gap-1.5 rounded-xl bg-amber-100 dark:bg-amber-900/50 px-3 py-1.5 text-xs font-bold text-amber-900 dark:text-amber-200 border border-amber-300 dark:border-amber-800"
                    >
                      <span>{{ allergen.icon }}</span>
                      <span>{{ allergen.name }}</span>
                    </span>
                  </div>
                </div>

                <!-- Free Of Allergens -->
                <div v-if="allergensInfo.freeOf && allergensInfo.freeOf.length > 0" class="space-y-1.5">
                  <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300">
                    Garantías dietéticas:
                  </p>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="freeItem in allergensInfo.freeOf"
                      :key="freeItem.id"
                      class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 px-3 py-1 text-xs font-semibold text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800"
                    >
                      <span>✓</span>
                      <span>{{ freeItem.name }}</span>
                    </span>
                  </div>
                </div>

                <p class="text-[11px] text-amber-800/80 dark:text-amber-400/80 italic pt-1">
                  * {{ allergensInfo.crossContaminationWarning }}
                </p>
              </div>

              <!-- Special Order Notes -->
              <div class="space-y-1.5">
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                  Instrucciones o notas especiales para tu pedido (opcional):
                </label>
                <input
                  v-model="orderNotes"
                  type="text"
                  placeholder="Ej. Tortillas bien calientes, salsas aparte, pechuga dorada..."
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-800 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                />
              </div>
            </div>
          </div>

          <!-- 
            BARRA DE ACCIÓN: PEDIDO DIRECTO POR WHATSAPP O TELÉFONO
            Dado que el carrito de venta directa está pausado temporalmente, se brinda un botón
            directo para solicitar este platillo vía WhatsApp con las cantidades y notas seleccionadas.
          -->
          <div class="border-t border-gray-100 p-4 sm:p-5 bg-white dark:bg-gray-900 dark:border-gray-800 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shrink-0">
            <!-- Selector de Cantidad -->
            <div class="flex items-center justify-center gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-1.5 dark:border-gray-700 dark:bg-gray-800 self-center sm:self-auto">
              <button
                @click="quantity = Math.max(1, quantity - 1)"
                class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-gray-800 font-black shadow-xs hover:bg-gray-100 dark:bg-gray-700 dark:text-white cursor-pointer transition active:scale-90"
                title="Disminuir cantidad"
              >
                -
              </button>
              <span class="w-8 text-center text-base font-black text-gray-900 dark:text-white">
                {{ quantity }}
              </span>
              <button
                @click="quantity++"
                class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-gray-800 font-black shadow-xs hover:bg-gray-100 dark:bg-gray-700 dark:text-white cursor-pointer transition active:scale-90"
                title="Aumentar cantidad"
              >
                +
              </button>
            </div>

            <!-- Botones de Pedido Directo -->
            <div class="flex-1 flex flex-col sm:flex-row items-center gap-2">
              <a
                :href="whatsappOrderUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full flex-1 flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3.5 text-sm font-black text-white shadow-xl hover:bg-emerald-700 transition transform active:scale-98 cursor-pointer"
              >
                <span>💬 Pedir por WhatsApp</span>
                <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-extrabold">
                  ${{ formatPrice(item.price * quantity) }} MXN
                </span>
              </a>

              <a
                href="tel:6181234567"
                class="w-full sm:w-auto px-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-xs font-bold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-center transition"
                title="Llamar a sucursal"
              >
                📞 Llamar
              </a>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { XMarkIcon, HeartIcon as HeartIconOutline } from '@heroicons/vue/24/outline'
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid'
import { useCart } from '@/composables/useCart'
import { useToasts } from '@/composables/useToasts'
import { useFavorites } from '@/composables/useFavorites'
import { getItemIngredients, getItemAllergens } from '@/utils/menuItemDetails.js'
import { generateFoodPlaceholder } from '@/utils/placeholderGenerator'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  item: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'add-to-cart'])

const { addToCart } = useCart()
const { addToast } = useToasts()
const { isFavorite, toggleFavorite } = useFavorites()

const quantity = ref(1)
const orderNotes = ref('')

const ingredientsList = computed(() => {
  return getItemIngredients(props.item)
})

const allergensInfo = computed(() => {
  return getItemAllergens(props.item)
})

watch(
  () => props.item,
  () => {
    quantity.value = 1
    orderNotes.value = ''
  }
)

const handleClose = () => {
  emit('close')
}

// Genera el enlace dinámico para ordenar este platillo vía WhatsApp con las notas y cantidades especificadas
const whatsappOrderUrl = computed(() => {
  if (!props.item) return 'https://wa.me/526181234567'
  const itemName = props.item.name || 'Platillo'
  const qty = quantity.value || 1
  const notes = orderNotes.value.trim() ? ` (Notas: ${orderNotes.value.trim()})` : ''
  const msg = `¡Hola Pollo Feliz Durango! Me gustaría ordenar ${qty}x ${itemName}${notes}. ¿Me confirman disponibilidad y tiempo de entrega?`
  return `https://wa.me/526181234567?text=${encodeURIComponent(msg)}`
})

const handleAddToCart = () => {
  if (!props.item) return

  const itemToAdd = {
    ...props.item,
    notes: orderNotes.value.trim() || undefined,
  }

  addToCart(itemToAdd, quantity.value)
  addToast(
    `¡${quantity.value}x "${props.item.name}" agregado(s) a tu pedido!`,
    'success'
  )

  emit('add-to-cart', {
    item: itemToAdd,
    quantity: quantity.value,
  })

  handleClose()
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

const handleImgError = (event) => {
  event.target.src = generateFoodPlaceholder(props.item)
}

// Handle ESC key press
const handleKeyDown = (e) => {
  if (e.key === 'Escape' && props.isOpen) {
    handleClose()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown)
})
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.25s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
