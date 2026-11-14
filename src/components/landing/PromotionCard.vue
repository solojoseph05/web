<template>
  <div
    class="group relative flex flex-col justify-between overflow-hidden rounded-2xl bg-white shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 dark:bg-gray-800"
  >
    <!-- Promo Image -->
    <div class="relative h-52 sm:h-60 overflow-hidden bg-gray-100 dark:bg-gray-900">
      <img
        :src="promo.image"
        :alt="promo.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        loading="lazy"
        decoding="async"
        @error="handleImageError"
      />
      <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/20 to-transparent"></div>

      <!-- Badges superiores (Vigencia/Urgencia + Promocional) -->
      <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2 pointer-events-none">
        <span
          class="rounded-full px-2.5 py-1 text-[11px] font-black shadow-md"
          :class="validityBadge.cls"
        >
          {{ validityBadge.text }}
        </span>

        <div
          v-if="promo.badge"
          class="rounded-full bg-yellow-400 px-3 py-1 text-xs font-black text-gray-900 shadow-md flex items-center gap-1"
        >
          <span>🔥</span>
          <span>{{ promo.badge }}</span>
        </div>
      </div>

      <!-- Promo Code / Price pills on image -->
      <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
        <div v-if="promo.code" class="flex items-center gap-1.5 rounded-lg bg-black/65 backdrop-blur-xs px-2.5 py-1 text-xs font-mono font-bold text-white border border-white/20">
          <span>CUPÓN:</span>
          <span class="text-yellow-400">{{ promo.code }}</span>
        </div>
        <div
          v-if="promo.promoPrice || promo.discountPercentage"
          class="ml-auto rounded-lg bg-red-600/95 px-2.5 py-1 text-xs font-black text-white shadow-md tabular-nums"
        >
          <span v-if="promo.promoPrice">${{ Number(promo.promoPrice).toFixed(0) }} MXN</span>
          <span v-else>-{{ promo.discountPercentage }}%</span>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="flex flex-1 flex-col justify-between p-5">
      <div>
        <p v-if="promo.subtitle" class="text-[11px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400 mb-1 line-clamp-1">
          {{ promo.subtitle }}
        </p>
        <h3 class="text-lg font-bold text-gray-800 group-hover:text-red-600 transition-colors line-clamp-1 dark:text-gray-100">
          {{ promo.title }}
        </h3>
        <p class="mt-2 text-xs sm:text-sm text-gray-600 line-clamp-2 dark:text-gray-300">
          {{ promo.description }}
        </p>
      </div>

      <!-- Footer Info & Action -->
      <div class="mt-5 flex flex-wrap items-center justify-between gap-2 border-t border-gray-100 pt-3 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
        <div class="flex flex-col">
          <span class="truncate max-w-[155px] font-medium">Vence: {{ formatDate(promo.validUntil) }}</span>
          <button
            type="button"
            @click="showTermsModal = true"
            class="text-left text-[11px] font-semibold text-red-600 hover:underline dark:text-red-400 cursor-pointer mt-0.5"
          >
            Términos y condiciones
          </button>
        </div>

        <button
          type="button"
          @click="applyPromo"
          class="flex items-center gap-1.5 rounded-full bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-md transition hover:bg-red-700 active:scale-95 cursor-pointer"
        >
          <span>Aprovechar</span>
          <span>→</span>
        </button>
      </div>
    </div>

    <!-- Modal de Términos y Condiciones -->
    <Teleport to="body">
      <div
        v-if="showTermsModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs"
        @click.self="showTermsModal = false"
      >
        <div
          class="w-full max-w-md rounded-3xl bg-[#0f172a] border border-slate-800 p-6 text-slate-100 shadow-2xl space-y-4"
          role="dialog"
          aria-modal="true"
          :aria-label="`Términos y condiciones de ${promo.title}`"
        >
          <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
            <div>
              <span class="text-[11px] font-bold uppercase tracking-wider text-amber-400">Términos y Condiciones</span>
              <h4 class="text-base font-black text-white mt-0.5">{{ promo.title }}</h4>
            </div>
            <button
              type="button"
              @click="showTermsModal = false"
              class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white cursor-pointer transition"
              aria-label="Cerrar modal"
            >
              ✕
            </button>
          </div>

          <div class="space-y-3 text-xs sm:text-sm text-slate-300 leading-relaxed">
            <p v-if="promo.termsConditions">{{ promo.termsConditions }}</p>
            <p v-else>
              Válido en sucursales participantes de Pollo Feliz Durango en pedidos para recoger, comedor y servicio a domicilio sujeto a cobertura. No acumulable con otras promociones o descuentos. Aplican restricciones y disponibilidad diaria en parrilla.
            </p>
            <div class="rounded-2xl bg-[#161e2e] border border-slate-700 p-3 flex items-center justify-between text-xs">
              <span class="text-slate-400">Vigencia hasta:</span>
              <span class="font-bold text-white">{{ formatDate(promo.validUntil) }}</span>
            </div>
            <div v-if="promo.code" class="rounded-2xl bg-[#161e2e] border border-slate-700 p-3 flex items-center justify-between text-xs">
              <span class="text-slate-400">Código aplicable:</span>
              <span class="font-mono font-black text-amber-400">{{ promo.code }}</span>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
            <button
              type="button"
              @click="showTermsModal = false"
              class="rounded-xl border border-slate-700 px-4 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-800 cursor-pointer transition"
            >
              Cerrar
            </button>
            <button
              type="button"
              @click="showTermsModal = false; applyPromo()"
              class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700 cursor-pointer transition"
            >
              Aplicar Promoción
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useCart } from '@/composables/useCart'
import { useToasts } from '@/composables/useToasts'

const props = defineProps({
  promo: {
    type: Object,
    required: true,
  },
})

const { applyPromoCode, openCart } = useCart()
const { addToast } = useToasts()
const showTermsModal = ref(false)

const handleKeyDown = (event) => {
  if (event.key === 'Escape' && showTermsModal.value) {
    showTermsModal.value = false
  }
}

onMounted(() => window.addEventListener('keydown', handleKeyDown))
onBeforeUnmount(() => window.removeEventListener('keydown', handleKeyDown))

const validityBadge = computed(() => {
  const raw = props.promo.validUntil
  if (!raw) return { text: '✓ Vigente', cls: 'bg-emerald-600/95 text-white' }

  const match = String(raw).trim().match(/^(\d{4})-(\d{2})-(\d{2})/)
  const endDate = match
    ? new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]))
    : new Date(raw)

  if (Number.isNaN(endDate.getTime())) {
    return { text: '✓ Vigente', cls: 'bg-emerald-600/95 text-white' }
  }

  const today = new Date()
  today.setHours(0, 0, 0, 0)
  endDate.setHours(0, 0, 0, 0)
  const daysLeft = Math.round((endDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24))

  if (daysLeft <= 0) return { text: '⏰ Vence hoy', cls: 'bg-rose-600 text-white' }
  if (daysLeft <= 7) return { text: '⏳ Últimos días', cls: 'bg-amber-400 text-gray-950' }
  return { text: '✓ Vigente', cls: 'bg-emerald-600/95 text-white' }
})

const applyPromo = () => {
  if (props.promo.code) {
    const res = applyPromoCode(props.promo.code)
    addToast(res.message, res.success ? 'success' : 'info')
  } else {
    addToast(`¡Promoción "${props.promo.title}" seleccionada!`, 'success')
  }
  openCart()
}

const formatDate = (dateString) => {
  if (!dateString) return 'Por tiempo limitado'
  try {
    const match = String(dateString).trim().match(/^(\d{4})-(\d{2})-(\d{2})/)
    const date = match
      ? new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]))
      : new Date(dateString)
    if (isNaN(date.getTime())) return 'Por tiempo limitado'
    return date.toLocaleDateString('es-MX', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    })
  } catch {
    return 'Por tiempo limitado'
  }
}

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZWVlIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyNHB4IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+UHJvbW8gPC90ZXh0Pjwvc3ZnPg=='
}
</script>
