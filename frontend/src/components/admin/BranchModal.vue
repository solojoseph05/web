<!-- src/components/admin/BranchModal.vue -->
<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs overflow-y-auto"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-2xl rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 my-8 transition-all max-h-[92vh] flex flex-col overflow-hidden"
        role="dialog"
        aria-modal="true"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800 shrink-0">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400 text-lg">
              📍
            </span>
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">
                {{ isEditing ? 'Editar Sucursal' : 'Nueva Sucursal' }}
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ isEditing ? 'Actualiza teléfono, horarios, coordenadas GPS y foto' : 'Registra un nuevo punto de venta con geolocalización para Pollo Feliz' }}
              </p>
            </div>
          </div>
          <button
            type="button"
            @click="emit('close')"
            class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200 cursor-pointer"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Form Body (Scrollable) -->
        <form @submit.prevent="handleSubmit" class="mt-4 space-y-4 overflow-y-auto flex-1 pr-1">
          <!-- Branch Name -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Nombre de la Sucursal *
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="Ej. Sucursal Centro Histórico"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white dark:focus:bg-gray-800 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white font-semibold transition"
            />
          </div>

          <!-- Address -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Dirección Completa *
            </label>
            <input
              v-model="form.address"
              type="text"
              required
              placeholder="Ej. Av. 5 de Mayo #123, Colonia Centro, Durango, Dgo."
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white dark:focus:bg-gray-800 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
            />
          </div>

          <!-- Phone & Schedule -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Teléfono de Contacto *
              </label>
              <input
                v-model="form.phone"
                type="text"
                required
                placeholder="Ej. 618-123-4567"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white dark:focus:bg-gray-800 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white font-medium transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Horario de Atención *
              </label>
              <input
                v-model="form.schedule"
                type="text"
                required
                placeholder="Ej. Lun - Dom: 10:00 AM - 10:00 PM"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white dark:focus:bg-gray-800 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
              />
            </div>
          </div>

          <!-- Interactive Map & Coordinates Section -->
          <div class="rounded-2xl border border-blue-100 bg-blue-50/50 dark:border-blue-950/60 dark:bg-blue-950/20 p-4 space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-xs font-black text-blue-900 dark:text-blue-300 flex items-center gap-1.5">
                <span>🗺️</span>
                <span>Ubicación en Google Maps & Coordenadas GPS (SEO)</span>
              </span>
              <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold">
                Marcador interactivo
              </span>
            </div>

            <!-- Smart Link & Coordinates Extractor Input -->
            <div>
              <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1">
                Extractor Automático: Pega enlace de Google Maps o "Latitud, Longitud"
              </label>
              <div class="flex gap-2">
                <input
                  v-model="coordInputText"
                  type="text"
                  placeholder="https://maps.google.com/?q=24.0277,-104.6532 o pega '24.0277, -104.6532'"
                  class="flex-1 rounded-xl border border-blue-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-blue-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                  @input="handleCoordInput"
                />
                <button
                  type="button"
                  @click="extractAndApplyCoords"
                  class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 text-xs font-bold transition cursor-pointer shrink-0"
                >
                  Extraer
                </button>
              </div>
              <p v-if="coordExtractMsg" class="text-[10px] font-bold mt-1" :class="coordExtractSuccess ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'">
                {{ coordExtractMsg }}
              </p>
            </div>

            <!-- Lat / Lng / Zoom Numerical Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-400 mb-0.5">
                  Latitud (DECIMAL)
                </label>
                <input
                  v-model.number="form.latitude"
                  type="number"
                  step="any"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-mono font-bold text-gray-900 focus:border-blue-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                  @change="updateMarkerPosition"
                />
              </div>

              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-400 mb-0.5">
                  Longitud (DECIMAL)
                </label>
                <input
                  v-model.number="form.longitude"
                  type="number"
                  step="any"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-mono font-bold text-gray-900 focus:border-blue-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                  @change="updateMarkerPosition"
                />
              </div>

              <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-400 mb-0.5">
                  Zoom Mapa (12-18)
                </label>
                <input
                  v-model.number="form.map_zoom"
                  type="number"
                  min="10"
                  max="19"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-mono font-bold text-gray-900 focus:border-blue-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                />
              </div>
            </div>

            <!-- Quick Durango Location Presets -->
            <div class="flex items-center gap-1.5 flex-wrap">
              <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">Puntos rápidos Durango:</span>
              <button
                v-for="preset in DURANGO_PRESETS"
                :key="preset.name"
                type="button"
                @click="applyPreset(preset)"
                class="rounded-lg px-2 py-0.5 text-[10px] font-bold border border-blue-200 bg-white dark:bg-gray-800 dark:border-gray-700 text-blue-700 dark:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-950 transition cursor-pointer"
              >
                {{ preset.name }}
              </button>
            </div>

            <!-- Interactive Leaflet Map Preview Container -->
            <div class="mt-2">
              <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300">
                  Arrastra el marcador para ajustar la posición exacta del restaurante:
                </span>
                <span class="text-[10px] text-gray-500">
                  Lat: {{ form.latitude?.toFixed(4) }}, Lng: {{ form.longitude?.toFixed(4) }}
                </span>
              </div>
              <div
                ref="mapContainer"
                class="h-48 w-full rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-inner z-10"
              ></div>
            </div>

            <!-- Google Maps URL -->
            <div>
              <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-400 mb-0.5">
                Enlace 'Cómo llegar' (Google Maps)
              </label>
              <input
                v-model="form.map_link"
                type="url"
                placeholder="https://maps.google.com/?q=24.0277,-104.6532"
                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-blue-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
            </div>
          </div>

          <!-- Image URL & Presets -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Fotografía de la Sucursal (Fachada o Comedor)
            </label>
            <div class="flex gap-2">
              <input
                v-model="form.image_url"
                type="url"
                placeholder="https://images.unsplash.com/..."
                class="flex-1 rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 text-xs text-gray-900 focus:border-red-500 focus:bg-white dark:focus:bg-gray-800 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
              />
              <button
                type="button"
                @click="cycleSuggestedImage"
                class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-100 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-900/60 transition cursor-pointer shrink-0"
              >
                💡 Foto sugerida
              </button>
            </div>

            <!-- Suggested Image Thumbnails -->
            <div class="mt-2.5 flex items-center gap-2 overflow-x-auto pb-1">
              <button
                v-for="(img, idx) in SUGGESTED_BRANCH_IMAGES"
                :key="idx"
                type="button"
                @click="form.image_url = img.url"
                class="relative h-12 w-16 shrink-0 rounded-lg overflow-hidden border-2 transition cursor-pointer"
                :class="form.image_url === img.url ? 'border-red-600 ring-2 ring-red-500/30' : 'border-gray-200 dark:border-gray-700 opacity-70 hover:opacity-100'"
                :title="img.label"
              >
                <img :src="img.url" :alt="img.label" class="h-full w-full object-cover" />
              </button>
            </div>
          </div>

          <!-- Status & Badges Section -->
          <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-800">
            <span class="block text-xs font-bold text-gray-700 dark:text-gray-300">
              Estado de la Sucursal en la Web
            </span>

            <!-- Toggle: is_coming_soon (Próxima Apertura) -->
            <label class="flex items-center gap-3 p-3 rounded-2xl border transition cursor-pointer"
              :class="form.is_coming_soon ? 'border-amber-400 bg-amber-50/60 dark:bg-amber-950/30 dark:border-amber-700' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50'"
            >
              <input
                v-model="form.is_coming_soon"
                type="checkbox"
                class="h-4 w-4 rounded text-amber-600 focus:ring-amber-500"
              />
              <div class="flex-1">
                <div class="flex items-center gap-1.5">
                  <span class="text-xs font-bold text-gray-900 dark:text-white">🚀 En Construcción / Próxima Apertura</span>
                  <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300">
                    Proyectada
                  </span>
                </div>
                <span class="block text-[11px] text-gray-500 dark:text-gray-400">
                  Muestra el badge '🚀 Próxima Apertura' en el carrusel de la página de inicio.
                </span>
              </div>
            </label>

            <!-- Toggle: is_new (Nueva Sucursal) -->
            <label class="flex items-center gap-3 p-3 rounded-2xl border transition cursor-pointer"
              :class="form.is_new ? 'border-red-400 bg-red-50/60 dark:bg-red-950/30 dark:border-red-800' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50'"
            >
              <input
                v-model="form.is_new"
                type="checkbox"
                class="h-4 w-4 rounded text-red-600 focus:ring-red-500"
              />
              <div class="flex-1">
                <div class="flex items-center gap-1.5">
                  <span class="text-xs font-bold text-gray-900 dark:text-white">✨ Nueva Sucursal / Recién Inaugurada</span>
                  <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300">
                    Novedad
                  </span>
                </div>
                <span class="block text-[11px] text-gray-500 dark:text-gray-400">
                  Destaca con distintivo '¡Nueva!' en la landing page.
                </span>
              </div>
            </label>

            <!-- Toggle: is_open (Abierta al público) -->
            <label v-if="!form.is_coming_soon" class="flex items-center gap-3 p-3 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <input
                v-model="form.is_open"
                type="checkbox"
                class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500"
              />
              <div class="flex-1">
                <span class="block text-xs font-bold text-gray-800 dark:text-gray-200">Abierta para Atención y Pedidos</span>
                <span class="block text-[11px] text-gray-500 dark:text-gray-400">Visible y en operación en el mapa y listados públicos.</span>
              </div>
            </label>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
            <div>
              <button
                v-if="isEditing"
                type="button"
                @click="handleDelete"
                class="rounded-xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300 px-3.5 py-2.5 text-xs font-bold cursor-pointer transition flex items-center gap-1.5"
              >
                <span>🗑️ Eliminar Sucursal</span>
              </button>
            </div>

            <div class="flex items-center gap-2">
              <button
                type="button"
                @click="emit('close')"
                class="rounded-xl border border-gray-200 px-4 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 cursor-pointer transition"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:from-red-700 hover:to-red-800 disabled:opacity-50 transition cursor-pointer flex items-center gap-2"
              >
                <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>{{ isEditing ? 'Guardar Cambios' : 'Crear Sucursal' }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch, nextTick, onBeforeUnmount } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  branchToEdit: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'save', 'delete'])

const isEditing = ref(false)
const isSubmitting = ref(false)
const mapContainer = ref(null)
const coordInputText = ref('')
const coordExtractMsg = ref('')
const coordExtractSuccess = ref(false)

let leafletMap = null
let leafletMarker = null

const DURANGO_PRESETS = [
  { name: 'Centro Histórico', lat: 24.0277, lng: -104.6532, address: 'Av. 5 de Mayo #123, Centro' },
  { name: 'Jardines de Durango', lat: 24.0385, lng: -104.6392, address: 'Av. Las Rosas #456, Fracc. Jardines' },
  { name: 'Coyoacán / Polanco', lat: 24.0195, lng: -104.6658, address: 'Calle Hidalgo #789, Durango' },
]

const SUGGESTED_BRANCH_IMAGES = [
  {
    url: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=700&auto=format&fit=crop&q=80',
    label: 'Fachada Moderna',
  },
  {
    url: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=700&auto=format&fit=crop&q=80',
    label: 'Comedor Familiar',
  },
  {
    url: 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=700&auto=format&fit=crop&q=80',
    label: 'Restaurante Iluminado',
  },
  {
    url: 'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=700&auto=format&fit=crop&q=80',
    label: 'Terraza y Mostrador',
  },
]

const form = ref({
  name: '',
  address: '',
  phone: '',
  schedule: 'Lun - Dom: 10:00 AM - 10:00 PM',
  image_url: '',
  map_link: '',
  latitude: 24.0277,
  longitude: -104.6532,
  map_zoom: 16,
  is_open: true,
  is_coming_soon: false,
  is_new: false,
})

let currentSuggestedIndex = 0
const cycleSuggestedImage = () => {
  currentSuggestedIndex = (currentSuggestedIndex + 1) % SUGGESTED_BRANCH_IMAGES.length
  form.value.image_url = SUGGESTED_BRANCH_IMAGES[currentSuggestedIndex].url
}

const applyPreset = (preset) => {
  form.value.latitude = preset.lat
  form.value.longitude = preset.lng
  if (!form.value.address) form.value.address = preset.address
  form.value.map_link = `https://maps.google.com/?q=${preset.lat},${preset.lng}`
  updateMarkerPosition()
  coordExtractMsg.value = `✓ Ubicación ajustada a ${preset.name}`
  coordExtractSuccess.value = true
}

const extractCoordinates = (text) => {
  if (!text) return null
  // Check @lat,lng
  const atMatch = text.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/)
  if (atMatch) return { lat: parseFloat(atMatch[1]), lng: parseFloat(atMatch[2]) }

  // Check q=lat,lng
  const qMatch = text.match(/q=(-?\d+\.\d+),(-?\d+\.\d+)/)
  if (qMatch) return { lat: parseFloat(qMatch[1]), lng: parseFloat(qMatch[2]) }

  // Check ll=lat,lng
  const llMatch = text.match(/ll=(-?\d+\.\d+),(-?\d+\.\d+)/)
  if (llMatch) return { lat: parseFloat(llMatch[1]), lng: parseFloat(llMatch[2]) }

  // Check raw lat, lng format
  const rawMatch = text.match(/(-?\d+\.\d{3,})\s*,\s*(-?\d+\.\d{3,})/)
  if (rawMatch) return { lat: parseFloat(rawMatch[1]), lng: parseFloat(rawMatch[2]) }

  return null
}

const handleCoordInput = () => {
  const coords = extractCoordinates(coordInputText.value)
  if (coords) {
    form.value.latitude = Number(coords.lat.toFixed(6))
    form.value.longitude = Number(coords.lng.toFixed(6))
    form.value.map_link = `https://maps.google.com/?q=${form.value.latitude},${form.value.longitude}`
    updateMarkerPosition()
    coordExtractMsg.value = `✓ Coordenadas detectadas: ${form.value.latitude}, ${form.value.longitude}`
    coordExtractSuccess.value = true
  }
}

const extractAndApplyCoords = () => {
  const coords = extractCoordinates(coordInputText.value)
  if (coords) {
    form.value.latitude = Number(coords.lat.toFixed(6))
    form.value.longitude = Number(coords.lng.toFixed(6))
    form.value.map_link = `https://maps.google.com/?q=${form.value.latitude},${form.value.longitude}`
    updateMarkerPosition()
    coordExtractMsg.value = `✓ Coordenadas extraídas exitosamente.`
    coordExtractSuccess.value = true
  } else {
    coordExtractMsg.value = '⚠️ No se encontraron coordenadas válidas en el texto.'
    coordExtractSuccess.value = false
  }
}

const initMap = () => {
  if (!mapContainer.value) return
  if (leafletMap) {
    leafletMap.remove()
    leafletMap = null
  }

  const lat = form.value.latitude || 24.0277
  const lng = form.value.longitude || -104.6532
  const zoom = form.value.map_zoom || 16

  leafletMap = L.map(mapContainer.value).setView([lat, lng], zoom)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19,
  }).addTo(leafletMap)

  // Custom Marker
  const customIcon = L.divIcon({
    className: 'custom-pollo-marker',
    html: `<div style="background-color: #dc2626; color: white; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 2px solid white; cursor: grab;">🍗</div>`,
    iconSize: [34, 34],
    iconAnchor: [17, 34],
  })

  leafletMarker = L.marker([lat, lng], {
    icon: customIcon,
    draggable: true,
  }).addTo(leafletMap)

  leafletMarker.on('dragend', (e) => {
    const pos = e.target.getLatLng()
    form.value.latitude = Number(pos.lat.toFixed(6))
    form.value.longitude = Number(pos.lng.toFixed(6))
    form.value.map_link = `https://maps.google.com/?q=${form.value.latitude},${form.value.longitude}`
    coordExtractMsg.value = `Marcador movido: ${form.value.latitude}, ${form.value.longitude}`
    coordExtractSuccess.value = true
  })

  leafletMap.on('click', (e) => {
    const { lat, lng } = e.latlng
    form.value.latitude = Number(lat.toFixed(6))
    form.value.longitude = Number(lng.toFixed(6))
    form.value.map_link = `https://maps.google.com/?q=${form.value.latitude},${form.value.longitude}`
    leafletMarker.setLatLng([lat, lng])
    coordExtractMsg.value = `Marcador reubicado: ${form.value.latitude}, ${form.value.longitude}`
    coordExtractSuccess.value = true
  })

  // Invalidate map size after animation
  setTimeout(() => {
    leafletMap?.invalidateSize()
  }, 300)
}

const updateMarkerPosition = () => {
  if (leafletMap && leafletMarker && form.value.latitude && form.value.longitude) {
    const latlng = [form.value.latitude, form.value.longitude]
    leafletMarker.setLatLng(latlng)
    leafletMap.panTo(latlng)
  }
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      coordInputText.value = ''
      coordExtractMsg.value = ''
      if (props.branchToEdit) {
        isEditing.value = true
        form.value = {
          name: props.branchToEdit.name || '',
          address: props.branchToEdit.address || '',
          phone: props.branchToEdit.phone || '',
          schedule: props.branchToEdit.schedule || 'Lun - Dom: 10:00 AM - 10:00 PM',
          image_url: props.branchToEdit.image_url || props.branchToEdit.image || '',
          map_link: props.branchToEdit.map_link || props.branchToEdit.mapLink || '',
          latitude: props.branchToEdit.latitude ? Number(props.branchToEdit.latitude) : 24.0277,
          longitude: props.branchToEdit.longitude ? Number(props.branchToEdit.longitude) : -104.6532,
          map_zoom: props.branchToEdit.map_zoom ? Number(props.branchToEdit.map_zoom) : 16,
          is_open: props.branchToEdit.is_open ?? true,
          is_coming_soon: !!props.branchToEdit.is_coming_soon,
          is_new: !!props.branchToEdit.is_new,
        }
      } else {
        isEditing.value = false
        form.value = {
          name: '',
          address: '',
          phone: '618-123-4567',
          schedule: 'Lun - Dom: 10:00 AM - 10:00 PM',
          image_url: SUGGESTED_BRANCH_IMAGES[0].url,
          map_link: 'https://maps.google.com/?q=24.0277,-104.6532',
          latitude: 24.0277,
          longitude: -104.6532,
          map_zoom: 16,
          is_open: true,
          is_coming_soon: false,
          is_new: true,
        }
      }

      nextTick(() => {
        initMap()
      })
    } else {
      if (leafletMap) {
        leafletMap.remove()
        leafletMap = null
      }
    }
  }
)

onBeforeUnmount(() => {
  if (leafletMap) {
    leafletMap.remove()
    leafletMap = null
  }
})

const handleDelete = () => {
  if (!props.branchToEdit?.id) return
  if (confirm(`¿Estás seguro de que deseas eliminar permanentemente la sucursal "${form.value.name}"? Esta acción se reflejará al instante en la web.`)) {
    emit('delete', props.branchToEdit.id)
  }
}

const handleSubmit = async () => {
  if (!form.value.name.trim() || !form.value.address.trim()) return
  isSubmitting.value = true
  try {
    emit('save', {
      ...form.value,
      id: props.branchToEdit?.id,
    })
  } finally {
    isSubmitting.value = false
  }
}
</script>
