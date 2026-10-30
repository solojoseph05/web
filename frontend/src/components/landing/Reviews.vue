<!-- src/components/landing/Reviews.vue -->
<template>
  <section id="reviews" class="py-16 sm:py-24 bg-gray-50/70 dark:bg-gray-900/40 relative">
    <div class="container mx-auto px-4 sm:px-6">
      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3.5 py-1 text-xs font-black text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 mb-3 shadow-2xs">
          <span>⭐</span> EXPERIENCIAS REALES
        </span>
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 dark:text-white tracking-tight">
          Lo que Dicen de Pollo Feliz
        </h2>
        <p class="mt-3 text-sm sm:text-base text-gray-600 dark:text-gray-300">
          La satisfacción de nuestras familias duranguenses es nuestra mayor pasión. Conoce las opiniones de quienes disfrutan la auténtica tradición al carbón.
        </p>
      </div>

      <!-- Rating Overview Card & CTA Banner -->
      <div class="rounded-3xl bg-white p-6 sm:p-8 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 mb-10 max-w-5xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
          <!-- Big Score Column -->
          <div class="md:col-span-4 text-center md:text-left flex flex-col items-center md:items-start justify-center border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-800 pb-6 md:pb-0 md:pr-8">
            <div class="flex items-baseline gap-2">
              <span class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white tracking-tighter">
                {{ stats.average_rating || 4.9 }}
              </span>
              <span class="text-2xl font-bold text-gray-400">/ 5</span>
            </div>

            <!-- Golden Stars -->
            <div class="flex items-center gap-1 text-amber-400 text-xl my-2">
              <span v-for="s in 5" :key="s">★</span>
            </div>

            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">
              Basado en <span class="font-bold text-gray-900 dark:text-white">{{ stats.total_reviews || reviewsList.length }} reseñas</span> verificadas
            </p>

            <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
              <span>✓</span>
              <span>{{ stats.recommended_percentage || 98 }}% recomiendan Pollo Feliz</span>
            </div>
          </div>

          <!-- Distribution Bars Column -->
          <div class="md:col-span-5 space-y-2">
            <div
              v-for="star in [5, 4, 3, 2, 1]"
              :key="star"
              class="flex items-center gap-3 text-xs"
            >
              <span class="w-12 font-bold text-gray-700 dark:text-gray-300 flex items-center gap-0.5 shrink-0">
                <span>{{ star }}</span>
                <span class="text-amber-400">★</span>
              </span>

              <div class="flex-1 h-2 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-500"
                  :class="star >= 4 ? 'bg-amber-400' : 'bg-gray-400'"
                  :style="{
                    width: stats.total_reviews > 0
                      ? `${Math.round(((stats.distribution?.[star] || 0) / stats.total_reviews) * 100)}%`
                      : star === 5 ? '80%' : star === 4 ? '15%' : '5%'
                  }"
                ></div>
              </div>

              <span class="w-10 text-right font-mono text-gray-400 text-[11px] shrink-0">
                {{ stats.distribution?.[star] || (star === 5 ? 5 : star === 4 ? 1 : 0) }}
              </span>
            </div>
          </div>

          <!-- CTA to Write Review Column -->
          <div class="md:col-span-3 flex flex-col items-center justify-center text-center space-y-3 pt-4 md:pt-0 border-t md:border-t-0 border-gray-100 dark:border-gray-800">
            <span class="text-3xl">🍗</span>
            <div class="text-xs">
              <span class="font-bold text-gray-900 dark:text-white block">¿Ya probaste nuestro sazón?</span>
              <span class="text-gray-500 dark:text-gray-400">Tu opinión es muy valiosa para nosotros</span>
            </div>
            <button
              @click="showReviewModal = true"
              class="w-full rounded-xl bg-linear-to-r from-red-600 to-red-700 px-4 py-3 text-xs font-black text-white shadow-md hover:from-red-700 hover:to-red-800 transition transform hover:scale-102 cursor-pointer flex items-center justify-center gap-1.5"
            >
              <span>+ Dejar mi Opinión</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Filters Row -->
      <div class="max-w-5xl mx-auto mb-8 flex flex-wrap items-center justify-between gap-4">
        <!-- Rating Filter Tabs -->
        <div class="flex flex-wrap items-center gap-1.5">
          <button
            v-for="rf in ratingFilters"
            :key="rf.val"
            @click="selectedRating = rf.val"
            class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition cursor-pointer"
            :class="selectedRating === rf.val
              ? 'bg-red-600 text-white shadow-xs'
              : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300'"
          >
            {{ rf.label }}
          </button>
        </div>

        <!-- Branch Selector Filter -->
        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold hidden sm:inline">Sucursal:</span>
          <select
            v-model="selectedBranch"
            class="rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 outline-hidden cursor-pointer"
          >
            <option value="Todas">Todas las sucursales</option>
            <option value="Centro Histórico">Centro Histórico</option>
            <option value="Jardines">Jardines</option>
            <option value="Coyoacán">Coyoacán</option>
            <option value="Domicilio">A Domicilio</option>
          </select>
        </div>
      </div>

      <!-- Reviews Cards Grid -->
      <div v-if="filteredReviews.length === 0" class="max-w-md mx-auto text-center p-8 bg-white rounded-3xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
        <span class="text-3xl block mb-2">⭐</span>
        <p class="font-bold text-gray-800 dark:text-gray-200 text-sm">No hay reseñas con este filtro.</p>
        <p class="text-xs text-gray-500 mt-1 mb-4">Sé el primero en compartir tu experiencia.</p>
        <button
          @click="showReviewModal = true"
          class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-red-700 transition cursor-pointer"
        >
          Escribir Reseña
        </button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
        <div
          v-for="review in filteredReviews"
          :key="review.id"
          class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 flex flex-col justify-between hover:shadow-md transition-shadow relative"
        >
          <!-- Top Row: Avatar, Name, Rating -->
          <div>
            <div class="flex items-start justify-between gap-3 mb-3">
              <div class="flex items-center gap-3">
                <!-- Avatar with Initials -->
                <div class="h-10 w-10 rounded-2xl bg-linear-to-tr from-amber-500 to-red-500 flex items-center justify-center font-black text-white text-sm shadow-xs shrink-0">
                  {{ review.customer_name ? review.customer_name.charAt(0).toUpperCase() : 'C' }}
                </div>
                <div>
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="font-black text-gray-900 dark:text-white text-sm">
                      {{ review.customer_name }}
                    </span>
                    <span
                      v-if="review.is_verified"
                      class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 flex items-center"
                      title="Visita verificada"
                    >
                      ✓ Verificado
                    </span>
                  </div>
                  <span class="text-[11px] text-gray-400 block">
                    {{ formatDate(review.created_at) }}
                  </span>
                </div>
              </div>

              <!-- Rating Badge -->
              <div class="flex items-center gap-0.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-black text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/50">
                <span>{{ review.rating }}</span>
                <span class="text-amber-400">★</span>
              </div>
            </div>

            <!-- Branch & Favorite Dish Pills -->
            <div class="flex flex-wrap items-center gap-1.5 mb-3 text-[11px]">
              <span
                v-if="review.branch"
                class="rounded-md bg-gray-100 px-2 py-0.5 text-gray-600 dark:bg-gray-800 dark:text-gray-300 font-semibold flex items-center gap-1"
              >
                <span>📍</span>
                <span>{{ review.branch }}</span>
              </span>
              <span
                v-if="review.favorite_dish"
                class="rounded-md bg-red-50 px-2 py-0.5 text-red-700 dark:bg-red-950/50 dark:text-red-300 font-semibold flex items-center gap-1"
              >
                <span>🍗</span>
                <span>{{ review.favorite_dish }}</span>
              </span>
            </div>

            <!-- Review Text -->
            <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed italic mb-4">
              "{{ review.comment }}"
            </p>

            <!-- Tags Chips -->
            <div v-if="review.tags && review.tags.length > 0" class="flex flex-wrap gap-1 mb-4">
              <span
                v-for="tag in review.tags"
                :key="tag"
                class="rounded-full bg-amber-50/80 px-2 py-0.5 text-[10px] font-bold text-amber-800 border border-amber-100 dark:bg-amber-950/40 dark:border-amber-900/50 dark:text-amber-300"
              >
                #{{ tag }}
              </span>
            </div>

            <!-- Official Team Reply Bubble (if any) -->
            <div
              v-if="review.admin_reply"
              class="rounded-2xl bg-red-50/80 p-3.5 border border-red-100 dark:bg-red-950/30 dark:border-red-900/40 space-y-1 mb-3"
            >
              <div class="flex items-center gap-1.5">
                <span class="text-xs">🍗</span>
                <span class="text-[11px] font-black text-red-700 dark:text-red-300">
                  {{ review.admin_reply.author || 'Respuesta de Pollo Feliz' }}:
                </span>
              </div>
              <p class="text-xs text-gray-700 dark:text-gray-300 leading-normal">
                {{ review.admin_reply.message }}
              </p>
            </div>
          </div>

          <!-- Bottom Bar: Helpful Upvote -->
          <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs text-gray-500">
            <span class="text-[11px] text-gray-400">¿Te resultó útil?</span>
            <button
              @click="handleLike(review)"
              class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold transition cursor-pointer"
              :class="likedReviews.includes(review.id)
                ? 'bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-300 font-black'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'"
              :title="likedReviews.includes(review.id) ? 'Ya votaste' : 'Marcar como útil'"
            >
              <span>👍</span>
              <span>Útil ({{ review.likes_count || 0 }})</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Review Submission Modal -->
    <ReviewModal
      :is-open="showReviewModal"
      @close="showReviewModal = false"
      @saved="handleReviewSaved"
    />
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/plugins/axios'
import ReviewModal from '@/components/landing/ReviewModal.vue'

const reviewsList = ref([])
const stats = ref({
  average_rating: 4.9,
  total_reviews: 6,
  distribution: { 5: 5, 4: 1, 3: 0, 2: 0, 1: 0 },
  recommended_percentage: 98,
})

const showReviewModal = ref(false)
const selectedRating = ref('all')
const selectedBranch = ref('Todas')
const likedReviews = ref([])

const ratingFilters = [
  { val: 'all', label: 'Todas' },
  { val: '5', label: '5 Estrellas ★' },
  { val: '4', label: '4 Estrellas ★' },
]

const filteredReviews = computed(() => {
  let list = [...reviewsList.value]

  if (selectedRating.value !== 'all') {
    const num = Number(selectedRating.value)
    list = list.filter(r => r.rating === num)
  }

  if (selectedBranch.value !== 'Todas') {
    const q = selectedBranch.value.toLowerCase()
    list = list.filter(r => (r.branch || '').toLowerCase().includes(q))
  }

  return list
})

const loadReviews = async () => {
  try {
    const res = await api.get('/reviews')
    if (res.data) {
      if (Array.isArray(res.data.reviews)) {
        reviewsList.value = res.data.reviews
      }
      if (res.data.stats) {
        stats.value = res.data.stats
      }
    }
  } catch (err) {
    console.warn('Error cargando reseñas públicas', err)
  }
}

const handleReviewSaved = (newReview) => {
  if (newReview) {
    reviewsList.value.unshift(newReview)
    loadReviews()
  }
}

const handleLike = async (review) => {
  if (likedReviews.value.includes(review.id)) return
  likedReviews.value.push(review.id)
  review.likes_count = (review.likes_count || 0) + 1

  try {
    await api.post(`/reviews/${review.id}/like`)
  } catch (err) {
    console.warn('Error al registrar voto', err)
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return 'Reciente'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' })
  } catch {
    return 'Reciente'
  }
}

onMounted(() => {
  loadReviews()
})
</script>
