<!-- src/components/landing/InteractiveMap.vue -->
<template>
  <div v-if="validBranches.length > 0" class="mt-12 rounded-3xl overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl transition-all">
    <!-- Map Control Header -->
    <div class="p-4 sm:p-6 bg-linear-to-r from-red-50/70 via-white to-amber-50/70 dark:from-red-950/40 dark:via-gray-800 dark:to-amber-950/40 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2">
          <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-red-600 text-white text-base shadow-xs">
            🗺️
          </span>
          <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">
            Mapa Interactivo de Cobertura en Durango
          </h3>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
          Selecciona una sucursal para ver su ubicación exacta, horarios y ruta en GPS
        </p>
      </div>

      <!-- Branch Quick Selector Chips -->
      <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
        <button
          v-for="branch in validBranches"
          :key="branch.id"
          type="button"
          @click="selectBranch(branch)"
          class="rounded-xl px-3 py-1.5 text-xs font-bold transition whitespace-nowrap cursor-pointer border"
          :class="selectedBranch?.id === branch.id
            ? 'bg-red-600 text-white border-red-600 shadow-xs font-black'
            : 'bg-white text-gray-700 border-gray-200 hover:border-red-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700'"
        >
          📍 {{ branch.name }}
        </button>
      </div>
    </div>

    <!-- Active Branch Selected Banner -->
    <div v-if="selectedBranch" class="px-6 py-3 bg-red-600 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
      <div class="flex items-center gap-2">
        <span class="font-black text-sm">{{ selectedBranch.name }}</span>
        <span class="opacity-80">· {{ selectedBranch.address }}</span>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <span class="bg-black/20 px-2.5 py-0.5 rounded-full font-mono">{{ selectedBranch.phone }}</span>
        <a
          :href="selectedBranch.map_link || `https://maps.google.com/?q=${selectedBranch.latitude},${selectedBranch.longitude}`"
          target="_blank"
          rel="noopener noreferrer"
          class="rounded-lg bg-white text-red-700 font-black px-3 py-1 hover:bg-red-50 transition shadow-2xs"
        >
          Cómo llegar (GPS) ↗
        </a>
      </div>
    </div>

    <!-- Leaflet Map Container -->
    <div
      ref="mapContainer"
      class="h-[360px] sm:h-[420px] w-full z-10 relative bg-gray-100 dark:bg-gray-900"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({
  branches: {
    type: Array,
    default: () => [],
  },
})

const mapContainer = ref(null)
const selectedBranch = ref(null)
let mapInstance = null
let markersGroup = null

const validBranches = computed(() => {
  return props.branches.filter(b => b && b.is_open !== false && !b.is_coming_soon)
})

const initMap = () => {
  if (!mapContainer.value || validBranches.value.length === 0) return

  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }

  const defaultLat = validBranches.value[0]?.latitude || 24.0277
  const defaultLng = validBranches.value[0]?.longitude || -104.6532

  mapInstance = L.map(mapContainer.value, {
    scrollWheelZoom: false,
  }).setView([defaultLat, defaultLng], 14)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19,
  }).addTo(mapInstance)

  markersGroup = L.featureGroup().addTo(mapInstance)

  validBranches.value.forEach((b) => {
    const lat = Number(b.latitude) || 24.0277
    const lng = Number(b.longitude) || -104.6532

    const customIcon = L.divIcon({
      className: 'custom-pollo-marker',
      html: `
        <div style="background-color: #dc2626; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 4px 10px rgba(0,0,0,0.35); border: 2.5px solid white; cursor: pointer;">
          🍗
        </div>
      `,
      iconSize: [36, 36],
      iconAnchor: [18, 36],
      popupAnchor: [0, -36],
    })

    const popupHtml = `
      <div style="font-family: inherit; padding: 4px; max-width: 240px;">
        <h4 style="margin: 0 0 4px; font-weight: 900; color: #111827; font-size: 14px;">${b.name}</h4>
        <p style="margin: 0 0 6px; font-size: 11px; color: #4b5563; line-height: 1.3;">${b.address}</p>
        <p style="margin: 0 0 8px; font-size: 11px; color: #d97706; font-weight: 700;">🕒 ${b.schedule || '10:00 AM - 10:00 PM'}</p>
        <a href="${b.map_link || `https://maps.google.com/?q=${lat},${lng}`}" target="_blank" rel="noopener noreferrer" style="display: inline-block; background-color: #dc2626; color: white; padding: 5px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; text-decoration: none;">
          Cómo llegar ↗
        </a>
      </div>
    `

    const marker = L.marker([lat, lng], { icon: customIcon }).bindPopup(popupHtml)
    marker.on('click', () => {
      selectedBranch.value = b
    })
    markersGroup.addLayer(marker)
  })

  if (validBranches.value.length > 1) {
    mapInstance.fitBounds(markersGroup.getBounds().pad(0.2))
  }

  if (validBranches.value.length > 0) {
    selectedBranch.value = validBranches.value[0]
  }

  setTimeout(() => {
    mapInstance?.invalidateSize()
  }, 300)
}

const selectBranch = (branch) => {
  selectedBranch.value = branch
  if (!mapInstance || !branch) return

  const lat = Number(branch.latitude) || 24.0277
  const lng = Number(branch.longitude) || -104.6532

  mapInstance.flyTo([lat, lng], 16, {
    animate: true,
    duration: 1.2,
  })

  // Open corresponding popup
  markersGroup?.eachLayer((layer) => {
    const latlng = layer.getLatLng()
    if (Math.abs(latlng.lat - lat) < 0.0001 && Math.abs(latlng.lng - lng) < 0.0001) {
      layer.openPopup()
    }
  })
}

watch(
  () => props.branches,
  () => {
    nextTick(() => {
      initMap()
    })
  },
  { deep: true }
)

onMounted(() => {
  nextTick(() => {
    initMap()
  })
})

onBeforeUnmount(() => {
  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }
})
</script>
