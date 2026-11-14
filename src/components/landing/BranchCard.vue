<template>
  <div
    class="flex flex-col justify-between rounded-3xl border border-gray-200/90 dark:border-gray-700/80 bg-white dark:bg-gray-800/90 shadow-xs transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl group overflow-hidden"
  >
    <div>
      <!-- Top Image Header or Branded Pattern Banner -->
      <div class="relative h-44 sm:h-48 w-full overflow-hidden bg-gray-900 shrink-0">
        <img
          v-if="branchImage"
          :src="branchImage"
          :alt="branch.name"
          class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
          loading="lazy"
          @error="(e) => e.target.style.display = 'none'"
        />
        <div v-else class="h-full w-full bg-linear-to-br from-red-700 via-amber-700 to-amber-900 flex flex-col items-center justify-center p-6 text-white text-center">
          <span class="text-4xl block mb-1 group-hover:scale-110 transition-transform">📍</span>
          <span class="text-xs font-black uppercase tracking-wider text-amber-200">Pollo Feliz Durango</span>
          <span class="text-sm font-bold text-white/90">{{ branch.name }}</span>
        </div>

        <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/30 to-transparent pointer-events-none"></div>

        <!-- Floating Top Badges -->
        <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2 pointer-events-none">
          <!-- Status Pill -->
          <span
            v-if="branch.is_coming_soon"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-500/95 text-amber-950 backdrop-blur-md border border-amber-300 shadow-md animate-pulse"
          >
            <span>🚀</span>
            <span>Próxima Apertura</span>
          </span>
          <span
            v-else
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black backdrop-blur-md shadow-xs border"
            :class="isCurrentlyOpen
              ? 'bg-emerald-600/90 text-white border-emerald-400'
              : 'bg-black/60 text-gray-200 border-gray-600'"
          >
            <span
              class="h-2 w-2 rounded-full"
              :class="isCurrentlyOpen ? 'bg-white animate-pulse' : 'bg-gray-400'"
            ></span>
            <span>{{ liveStatusText }}</span>
          </span>

          <!-- Sucursal ID or New Badge -->
          <span
            v-if="branch.is_new"
            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-600 text-white shadow-xs"
          >
            ¡Nueva!
          </span>
          <span
            v-else
            class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-black/50 text-white/90 backdrop-blur-xs border border-white/20"
          >
            Sucursal #{{ branch.id }}
          </span>
        </div>

        <!-- Branch Name on Image -->
        <div class="absolute bottom-3 left-4 right-4 pointer-events-none">
          <h3 class="text-lg sm:text-xl font-black text-white leading-tight drop-shadow-md group-hover:text-amber-300 transition-colors">
            {{ branch.name }}
          </h3>
        </div>
      </div>

      <!-- Card Info Items -->
      <div class="p-5 sm:p-6 space-y-3.5 text-sm text-gray-600 dark:text-gray-300">
        <!-- Address with Copy Button -->
        <div class="flex items-start gap-3">
          <div class="shrink-0 mt-0.5 rounded-xl bg-red-50 p-2 text-red-600 dark:bg-red-950/50 dark:text-red-400">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <div class="flex-1 flex items-start justify-between gap-2">
            <span class="leading-relaxed text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200">
              {{ branch.address }}
            </span>
            <button
              type="button"
              @click="copyAddress"
              class="text-[11px] font-bold text-gray-400 hover:text-red-600 dark:hover:text-red-400 cursor-pointer shrink-0 transition"
              :title="copied ? '¡Copiado!' : 'Copiar dirección'"
            >
              {{ copied ? '✓ Copiado' : 'Copiar' }}
            </button>
          </div>
        </div>

        <!-- Schedule -->
        <div class="flex items-start gap-3">
          <div class="shrink-0 mt-0.5 rounded-xl bg-orange-50 p-2 text-orange-600 dark:bg-orange-950/50 dark:text-orange-400">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span class="leading-relaxed text-xs sm:text-sm font-medium">
            {{ branch.schedule || 'Lun - Dom: 10:00 AM - 10:00 PM' }}
          </span>
        </div>

        <!-- Phone -->
        <div class="flex items-start gap-3">
          <div class="shrink-0 mt-0.5 rounded-xl bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
          </div>
          <a :href="`tel:${branch.phone}`" class="text-xs sm:text-sm font-bold text-gray-800 hover:text-red-600 dark:text-gray-200 dark:hover:text-red-400 transition">
            {{ branch.phone }}
          </a>
        </div>
      </div>
    </div>

    <!-- Actions / CTA -->
    <div class="p-5 sm:p-6 pt-0">
      <div class="pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center gap-2.5">
        <!-- When Coming Soon: WhatsApp inquiry -->
        <template v-if="branch.is_coming_soon">
          <a
            :href="getComingSoonWhatsAppLink(branch)"
            target="_blank"
            rel="noopener noreferrer"
            class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-amber-950 font-black px-4 py-2.5 text-xs shadow-xs transition min-h-[44px]"
          >
            <span>💬 Consultar Apertura</span>
          </a>
          <a
            :href="getDirectionsUrl(branch)"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center justify-center rounded-xl bg-gray-100 px-3.5 py-2.5 text-xs font-bold text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 min-h-[44px]"
            title="Ver ubicación planeada en Google Maps"
          >
            <svg class="h-4 w-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
          </a>
        </template>

        <!-- When in operation: Focus Map, Standard GPS & Call buttons -->
        <template v-else>
          <button
            type="button"
            @click="emit('focus-map', branch)"
            class="flex items-center justify-center gap-1 rounded-xl bg-red-50 px-3 py-2.5 text-xs font-bold text-red-700 transition hover:bg-red-100 dark:bg-red-950/50 dark:text-red-300 dark:hover:bg-red-950/80 min-h-[44px] cursor-pointer"
            title="Ubicar en mapa interactivo"
          >
            <span>📍</span>
            <span class="hidden sm:inline">Mapa</span>
          </button>

          <a
            :href="getDirectionsUrl(branch)"
            target="_blank"
            rel="noopener noreferrer"
            class="flex-1 flex items-center justify-center gap-1.5 rounded-xl bg-gray-100 px-3 py-2.5 text-xs font-bold text-gray-800 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 min-h-[44px]"
          >
            <svg class="h-4 w-4 text-red-600 dark:text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span>Cómo llegar</span>
          </a>

          <a
            :href="`tel:${branch.phone}`"
            class="flex items-center justify-center gap-1.5 rounded-xl bg-red-600 px-3.5 py-2.5 text-xs font-bold text-white transition hover:bg-red-700 shadow-xs min-h-[44px]"
            aria-label="Llamar a sucursal"
          >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            <span>Llamar</span>
          </a>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  branch: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['focus-map'])

const copied = ref(false)

const branchImage = computed(() => {
  return props.branch.image_url || props.branch.image || ''
})

const getDirectionsUrl = (branch) => {
  const lat = Number(branch?.latitude)
  const lng = Number(branch?.longitude)
  if (!Number.isNaN(lat) && !Number.isNaN(lng) && lat !== 0 && lng !== 0) {
    return `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`
  }
  return (
    branch?.directions_url ||
    branch?.map_link ||
    branch?.mapLink ||
    `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent('Pollo Feliz ' + (branch?.name || '') + ' Durango')}`
  )
}

const isCurrentlyOpen = computed(() => {
  if (props.branch.is_coming_soon) return false
  if (props.branch.is_open === false || props.branch.isOpen === false) {
    return false
  }
  // Schedule check for Durango local hours
  const now = new Date()
  const hour = now.getHours()
  return hour >= 10 && hour < 22
})

const liveStatusText = computed(() => {
  if (props.branch.is_coming_soon) {
    return 'Próxima Apertura'
  }
  if (props.branch.is_open === false || props.branch.isOpen === false) {
    return 'Cerrada por hoy'
  }
  const now = new Date()
  const hour = now.getHours()
  if (hour >= 10 && hour < 22) {
    return 'Abierto ahora · Cierra 10:00 PM'
  }
  return 'Cerrado ahora · Abre 10:00 AM'
})

const copyAddress = async () => {
  if (!props.branch.address) return
  try {
    if (navigator?.clipboard?.writeText) {
      await navigator.clipboard.writeText(props.branch.address)
      copied.value = true
      setTimeout(() => {
        copied.value = false
      }, 2500)
    }
  } catch (err) {
    console.error('Error al copiar dirección', err)
  }
}

const getComingSoonWhatsAppLink = (b) => {
  const phone = '526181234567'
  const message = `¡Hola Pollo Feliz Durango! Vi que abrirán una nueva "${b.name}" en "${b.address}". ¿Tienen fecha estimada de inauguración? ¡Mucho éxito!`
  return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`
}
</script>
