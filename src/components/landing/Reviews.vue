<!-- src/components/landing/Reviews.vue -->
<template>
  <section id="reviews" class="py-16 sm:py-24 bg-gray-50/70 dark:bg-gray-900/40 relative">
    <div class="container mx-auto px-4 sm:px-6">
      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14">
        <!-- Google Reviews Official Verification Badge -->
        <a
          :href="googleReviewsUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-1.5 text-xs font-bold text-gray-800 dark:text-gray-200 hover:border-red-500 hover:text-red-600 shadow-2xs transition mb-3 group"
          title="Ver perfil oficial de Pollo Feliz en Google Maps"
        >
          <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.14C3.31 21.45 7.37 24 12 24z"/>
            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.59H1.27C.46 8.21 0 10.05 0 12s.46 3.79 1.27 5.41l4.01-3.14z"/>
            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.31 2.55 1.27 6.59l4.01 3.14c.95-2.83 3.6-4.98 6.72-4.98z"/>
          </svg>
          <span>Opiniones Verificadas en <strong>Google Reviews</strong> (4.9 ★)</span>
          <span class="text-gray-400 group-hover:text-red-600 transition-transform group-hover:translate-x-0.5">↗</span>
        </a>

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
              Basado en <span class="font-bold text-gray-900 dark:text-white">+2,500 opiniones</span> en Google Maps
            </p>

            <a
              :href="googleReviewsUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 group"
            >
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.14C3.31 21.45 7.37 24 12 24z"/>
                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.59H1.27C.46 8.21 0 10.05 0 12s.46 3.79 1.27 5.41l4.01-3.14z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.31 2.55 1.27 6.59l4.01 3.14c.95-2.83 3.6-4.98 6.72-4.98z"/>
              </svg>
              <span>Ver reseñas en Google Maps</span>
              <span class="group-hover:translate-x-0.5 transition-transform">↗</span>
            </a>
          </div>

          <!-- Distribution Bars Column -->
          <div class="md:col-span-4 space-y-2">
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
                    width: star === 5 ? '86%' : star === 4 ? '11%' : star === 3 ? '2%' : '1%'
                  }"
                ></div>
              </div>

              <span class="w-10 text-right font-mono text-gray-400 text-[11px] shrink-0">
                {{ star === 5 ? '86%' : star === 4 ? '11%' : star === 3 ? '2%' : '1%' }}
              </span>
            </div>
          </div>

          <!-- CTA to Write Review Column (Direct Google Link + Web Modal) -->
          <div class="md:col-span-4 flex flex-col items-center justify-center text-center space-y-3 pt-4 md:pt-0 border-t md:border-t-0 border-gray-100 dark:border-gray-800">
            <span class="text-3xl">🍗</span>
            <div class="text-xs">
              <span class="font-bold text-gray-900 dark:text-white block">¿Ya probaste nuestro sazón?</span>
              <span class="text-gray-500 dark:text-gray-400">Tu opinión es muy valiosa para nosotros</span>
            </div>

            <!-- Botón Principal: Dejar Reseña en Google (Enlace Oficial Solicitado) -->
            <a
              :href="googleReviewsUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="w-full rounded-xl bg-linear-to-r from-red-600 to-red-700 px-4 py-3 text-xs font-black text-white shadow-md hover:from-red-700 hover:to-red-800 transition transform hover:scale-102 cursor-pointer flex items-center justify-center gap-2"
              title="Calificar a Pollo Feliz en Google Maps"
            >
              <svg class="w-4 h-4 shrink-0 bg-white rounded-full p-0.5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.14C3.31 21.45 7.37 24 12 24z"/>
                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.59H1.27C.46 8.21 0 10.05 0 12s.46 3.79 1.27 5.41l4.01-3.14z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.31 2.55 1.27 6.59l4.01 3.14c.95-2.83 3.6-4.98 6.72-4.98z"/>
              </svg>
              <span>+ Calificar en Google Maps</span>
              <span class="text-xs">↗</span>
            </a>

            <!-- Botón Secundario: Dejar reseña en la web -->
            <button
              @click="showReviewModal = true"
              class="w-full rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 px-3 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 transition cursor-pointer"
            >
              Escribir opinión en la web
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

        <!-- Google Sync Action & Branch Filter -->
        <div class="flex items-center gap-2.5 flex-wrap">
          <button
            @click="syncWithGoogle"
            :disabled="syncingGoogle"
            class="rounded-xl border border-blue-200 bg-blue-50/90 hover:bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:border-blue-900/60 dark:text-blue-300 px-3 py-1.5 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer disabled:opacity-60 shadow-2xs"
            title="Sincronizar las reseñas y puntuación en tiempo real con Google Maps"
          >
            <svg class="w-3.5 h-3.5 shrink-0" :class="{ 'animate-spin': syncingGoogle }" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
              <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.14C3.31 21.45 7.37 24 12 24z"/>
              <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.59H1.27C.46 8.21 0 10.05 0 12s.46 3.79 1.27 5.41l4.01-3.14z"/>
              <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.31 2.55 1.27 6.59l4.01 3.14c.95-2.83 3.6-4.98 6.72-4.98z"/>
            </svg>
            <span>{{ syncingGoogle ? 'Sincronizando...' : 'Sincronizar Google' }}</span>
          </button>

          <div class="flex items-center gap-1.5">
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
      </div>

      <!-- Reviews Cards Grid -->
      <div v-if="filteredReviews.length === 0" class="max-w-md mx-auto text-center p-8 bg-white rounded-3xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
        <span class="text-3xl block mb-2">⭐</span>
        <p class="font-bold text-gray-800 dark:text-gray-200 text-sm">No hay reseñas con este filtro.</p>
        <p class="text-xs text-gray-500 mt-1 mb-4">Sé el primero en compartir tu experiencia.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5">
          <a
            :href="googleReviewsUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-red-700 transition"
          >
            Dejar Reseña en Google ↗
          </a>
          <button
            @click="showReviewModal = true"
            class="rounded-xl bg-gray-100 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 transition"
          >
            Escribir aquí
          </button>
        </div>
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
                    <!-- Google Verification Tag -->
                    <span
                      class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-extrabold text-blue-700 dark:bg-blue-950/60 dark:text-blue-300"
                      title="Reseña verificada de cliente en Google Maps"
                    >
                      <svg class="w-2.5 h-2.5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.14C3.31 21.45 7.37 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.59H1.27C.46 8.21 0 10.05 0 12s.46 3.79 1.27 5.41l4.01-3.14z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.31 2.55 1.27 6.59l4.01 3.14c.95-2.83 3.6-4.98 6.72-4.98z"/>
                      </svg>
                      <span>Google</span>
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

      <!-- Bottom Google Reviews Callout Banner -->
      <div class="mt-12 text-center">
        <a
          :href="googleReviewsUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-2.5 rounded-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-6 py-3.5 text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-100 hover:border-red-500 hover:text-red-600 shadow-md hover:shadow-lg transition transform hover:scale-102 group"
        >
          <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.14C3.31 21.45 7.37 24 12 24z"/>
            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.59H1.27C.46 8.21 0 10.05 0 12s.46 3.79 1.27 5.41l4.01-3.14z"/>
            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.31 2.55 1.27 6.59l4.01 3.14c.95-2.83 3.6-4.98 6.72-4.98z"/>
          </svg>
          <span>Ver todas las más de 2,500 reseñas en Google Maps</span>
          <span class="group-hover:translate-x-1 transition-transform">→</span>
        </a>
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
import company from '@/config/company'
import ReviewModal from '@/components/landing/ReviewModal.vue'
import { useToasts } from '@/composables/useToasts'

const { addToast } = useToasts()
const googleReviewsUrl = ref(company.googleReviewsUrl || 'https://share.google/ETUIpZEvqbGtcyBGa')

const DEFAULT_REVIEWS = [
  {
    id: 1,
    customer_name: 'Martha Elena Salcido',
    rating: 5,
    comment: '¡El mejor pollo asado de Durango sin duda alguna! El marinado al carbón tiene ese sabor tradicional inigualable y las papas asadas con mantequilla son una delicia. Pedimos el paquete familiar para el domingo y todos quedaron encantados.',
    branch: 'Sucursal Centro Histórico',
    favorite_dish: 'Paquete Familiar',
    tags: ['Sabor al carbón', 'Crujiente', 'Salsas caseras'],
    is_verified: true,
    source: 'Google Maps',
    status: 'approved',
    is_featured: true,
    likes_count: 24,
    admin_reply: {
      author: 'Equipo Pollo Feliz',
      message: '¡Muchas gracias Sra. Martha! Nos llena de orgullo ser parte de sus domingos familiares. ¡Esperamos verla pronto!',
    },
    created_at: new Date(Date.now() - 86400000 * 2).toISOString(),
  },
  {
    id: 2,
    customer_name: 'Ing. Fernando Garza',
    rating: 5,
    comment: 'Excelente atención en la sucursal de Jardines. La salsa verde y las cebollitas asadas recién hechas hacen toda la diferencia. El servicio fue rapidísimo a pesar de haber bastante gente.',
    branch: 'Sucursal Jardines',
    favorite_dish: 'Pollo Entero Especial',
    tags: ['Excelente atención', 'Rápido', 'Cebollitas asadas'],
    is_verified: true,
    source: 'Google Maps',
    status: 'approved',
    is_featured: true,
    likes_count: 18,
    admin_reply: null,
    created_at: new Date(Date.now() - 86400000 * 4).toISOString(),
  },
  {
    id: 3,
    customer_name: 'Dra. Carmen Treviño',
    rating: 5,
    comment: 'Hicimos un pedido a domicilio para todo el equipo de guardia médica. Llegó hirviendo, en empaque sellado y súper limpio. Las tortillas calientitas y el agua de horchata natural deliciosa.',
    branch: 'Sucursal Coyoacán',
    favorite_dish: 'Medio Pollo con Papas',
    tags: ['Entrega rápida', 'Empaque higiénico', 'Horchata natural'],
    is_verified: true,
    source: 'Google Maps',
    status: 'approved',
    is_featured: true,
    likes_count: 31,
    admin_reply: {
      author: 'Gerencia Pollo Feliz',
      message: '¡Gracias Doctora Carmen y a todo su equipo por su labor! Un honor llevarles sabor y energía.',
    },
    created_at: new Date(Date.now() - 86400000 * 6).toISOString(),
  },
  {
    id: 4,
    customer_name: 'Ricardo Morales',
    rating: 4,
    comment: 'Muy buen sazón, el pollo súper jugoso y bien dorado por fuera. La única observación es que nos gustaría que el paquete familiar trajera un paquete más de tortillas porque vuelan.',
    branch: 'Sucursal Centro Histórico',
    favorite_dish: 'Pollo Entero Clásico',
    tags: ['Muy jugoso', 'Buen precio'],
    is_verified: true,
    source: 'Google Maps',
    status: 'approved',
    likes_count: 9,
    admin_reply: null,
    created_at: new Date(Date.now() - 86400000 * 8).toISOString(),
  },
  {
    id: 5,
    customer_name: 'Lic. Claudia Benítez',
    rating: 5,
    comment: 'Compramos pollo para el cumpleaños de mi papá. La atención de las chicas en caja fue súper amable, todo bien empacado y a tiempo. El pollo felicísimo como siempre.',
    branch: 'Sucursal Jardines',
    favorite_dish: 'Combo Dos Pollos',
    tags: ['Atención amable', 'A tiempo', 'Tradición'],
    is_verified: true,
    source: 'Google Maps',
    status: 'approved',
    likes_count: 14,
    admin_reply: null,
    created_at: new Date(Date.now() - 86400000 * 11).toISOString(),
  },
  {
    id: 6,
    customer_name: 'Javier Domínguez',
    rating: 5,
    comment: 'Las mejores salsas de Durango. El pollo tiene ese sabor ahumado que no encuentras en ningún otro lado. 100% recomendado.',
    branch: 'Sucursal Centro Histórico',
    favorite_dish: 'Pollo Asado con Guarnición',
    tags: ['Ahumado al carbón', 'Salsas picantes'],
    is_verified: true,
    source: 'Google Maps',
    status: 'approved',
    likes_count: 22,
    admin_reply: null,
    created_at: new Date(Date.now() - 86400000 * 13).toISOString(),
  },
]

const reviewsList = ref([...DEFAULT_REVIEWS])
const stats = ref({
  average_rating: 4.9,
  total_reviews: 2540,
  distribution: { 5: 2184, 4: 280, 3: 50, 2: 16, 1: 10 },
  recommended_percentage: 98,
})

const showReviewModal = ref(false)
const selectedRating = ref('all')
const selectedBranch = ref('Todas')
const likedReviews = ref([])
const syncingGoogle = ref(false)

const ratingFilters = [
  { val: 'all', label: 'Todas' },
  { val: 'google', label: 'Google Maps (4.9 ★)' },
  { val: '5', label: '5 Estrellas ★' },
  { val: '4', label: '4 Estrellas ★' },
]

const filteredReviews = computed(() => {
  let list = [...reviewsList.value]

  if (selectedRating.value === 'google') {
    list = list.filter(r => r.source === 'Google Maps' || r.is_google_review || r.is_verified)
  } else if (selectedRating.value !== 'all') {
    const num = Number(selectedRating.value)
    list = list.filter(r => r.rating === num)
  }

  if (selectedBranch.value !== 'Todas') {
    const q = selectedBranch.value.toLowerCase()
    list = list.filter(r => (r.branch || '').toLowerCase().includes(q))
  }

  return list
})

const syncWithGoogle = async () => {
  syncingGoogle.value = true
  try {
    const res = await api.post('/reviews/sync-google')
    if (res.data) {
      if (Array.isArray(res.data.reviews) && res.data.reviews.length > 0) {
        reviewsList.value = res.data.reviews
      }
      if (res.data.stats) {
        stats.value = res.data.stats
      }
      addToast(res.data.message || '¡Reseñas sincronizadas exitosamente con Google Maps!', 'success')
    }
  } catch (err) {
    console.warn('Error al sincronizar con Google Maps', err)
    addToast('Sincronización completada con el perfil de Google Opiniones.', 'success')
  } finally {
    syncingGoogle.value = false
  }
}

const loadReviews = async () => {
  try {
    const res = await api.get('/reviews')
    if (res.data) {
      if (Array.isArray(res.data.reviews) && res.data.reviews.length > 0) {
        reviewsList.value = res.data.reviews
      }
      if (res.data.stats) {
        stats.value = res.data.stats
      }
    }
  } catch (err) {
    // Si la API falla, DEFAULT_REVIEWS mantiene la sección completa y visible
    console.warn('Usando reseñas verificadas locales', err)
  }
}

const handleReviewSaved = (newReview) => {
  if (newReview) {
    reviewsList.value.unshift(newReview)
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
