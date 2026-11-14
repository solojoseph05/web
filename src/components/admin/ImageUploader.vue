<!-- src/components/admin/ImageUploader.vue -->
<template>
  <div class="admin-panel space-y-3.5">
    <!-- Top Mode Selector: Subir Archivo vs Galería vs Pegar Enlace -->
    <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-gray-100 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 text-xs font-bold">
      <button
        type="button"
        @click="activeMode = 'upload'"
        class="flex-1 py-1.5 px-2.5 rounded-xl transition cursor-pointer flex items-center justify-center gap-1.5"
        :class="activeMode === 'upload'
          ? 'bg-white text-gray-900 shadow-2xs dark:bg-gray-700 dark:text-white'
          : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'"
      >
        <span>📁</span>
        <span>Subir & Optimizar</span>
      </button>

      <button
        type="button"
        @click="activeMode = 'gallery'"
        class="flex-1 py-1.5 px-2.5 rounded-xl transition cursor-pointer flex items-center justify-center gap-1.5"
        :class="activeMode === 'gallery'
          ? 'bg-white text-gray-900 shadow-2xs dark:bg-gray-700 dark:text-white'
          : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'"
      >
        <span>🖼️</span>
        <span>Galería Pollo Feliz ({{ galleryImages.length }})</span>
      </button>

      <button
        type="button"
        @click="activeMode = 'url'"
        class="flex-1 py-1.5 px-2.5 rounded-xl transition cursor-pointer flex items-center justify-center gap-1.5"
        :class="activeMode === 'url'
          ? 'bg-white text-gray-900 shadow-2xs dark:bg-gray-700 dark:text-white'
          : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'"
      >
        <span>🔗</span>
        <span>Enlace URL</span>
      </button>
    </div>

    <!-- Aspect Ratio Presets Toolbar -->
    <div class="flex items-center justify-between gap-2 px-1 text-[11px]">
      <span class="font-bold text-gray-500 dark:text-gray-400 flex items-center gap-1">
        <span>📐 Formato:</span>
      </span>
      <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-800/60 p-1 rounded-xl border border-gray-200 dark:border-gray-700">
        <button
          v-for="preset in aspectPresets"
          :key="preset.id"
          type="button"
          @click="selectedAspect = preset.id"
          class="px-2 py-0.5 rounded-lg font-semibold transition cursor-pointer"
          :class="selectedAspect === preset.id
            ? 'bg-red-600 text-white shadow-2xs'
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
        >
          {{ preset.label }}
        </button>
      </div>
    </div>

    <!-- MODE 1: Direct File Upload & Drag and Drop with Canvas Compression -->
    <div v-if="activeMode === 'upload'" class="space-y-2.5">
      <div
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleFileDrop"
        class="relative border-2 border-dashed rounded-2xl p-5 text-center transition cursor-pointer group"
        :class="isDragging
          ? 'border-red-500 bg-red-50/60 dark:bg-red-950/30'
          : 'border-gray-300 hover:border-red-400 bg-gray-50/50 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800/40 dark:hover:bg-gray-800/80'"
        @click="triggerFileInput"
      >
        <input
          ref="fileInputRef"
          type="file"
          accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml, image/gif"
          class="hidden"
          @change="handleFileSelect"
        />

        <div class="flex flex-col items-center justify-center space-y-2 pointer-events-none">
          <div class="h-12 w-12 rounded-2xl bg-red-100 text-red-600 dark:bg-red-950/70 dark:text-red-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            ⚡
          </div>
          <div class="text-xs">
            <span class="font-bold text-gray-800 dark:text-gray-200 block sm:inline">
              Arrastra y suelta tu foto aquí o
            </span>
            <span class="font-extrabold text-red-600 dark:text-red-400 ml-1 underline underline-offset-2">
              Explorar archivos
            </span>
          </div>
          <p class="text-[11px] text-gray-400 dark:text-gray-500">
            PNG, JPG, WEBP (Hasta 10 MB). Se comprime y optimiza automáticamente al instante.
          </p>
        </div>

        <!-- Uploading / Processing Overlay -->
        <div
          v-if="uploading"
          class="absolute inset-0 bg-white/95 dark:bg-gray-900/95 rounded-2xl flex flex-col items-center justify-center gap-2 z-10 p-4"
        >
          <div class="animate-spin rounded-full h-8 w-8 border-3 border-red-600 border-t-transparent"></div>
          <span class="text-xs font-bold text-gray-800 dark:text-gray-200">
            {{ compressionStatusText || 'Optimizando y subiendo imagen...' }}
          </span>
          <span v-if="compressionStats" class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-bold">
            {{ compressionStats }}
          </span>
        </div>
      </div>

      <!-- Compression Savings Banner -->
      <div
        v-if="lastOptimization"
        class="flex items-center justify-between px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 text-[11px]"
      >
        <div class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-300 font-medium">
          <span>⚡ Compresión inteligente:</span>
          <span class="font-bold">{{ lastOptimization.originalSize }} → {{ lastOptimization.compressedSize }}</span>
        </div>
        <span class="font-black text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/60 px-2 py-0.5 rounded-full text-[10px]">
          -{{ lastOptimization.savedPercent }}% Reducido
        </span>
      </div>
    </div>

    <!-- MODE 2: Official Restaurant Gallery & Server Presets with Delete -->
    <div v-else-if="activeMode === 'gallery'" class="space-y-2">
      <div class="flex items-center justify-between px-1 text-xs text-gray-500 dark:text-gray-400">
        <span>Selecciona una foto o elimina las que ya no uses:</span>
        <button
          type="button"
          @click="loadServerGallery"
          class="text-red-600 dark:text-red-400 hover:underline font-semibold cursor-pointer"
        >
          🔄 Actualizar
        </button>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 max-h-56 overflow-y-auto p-1">
        <div
          v-for="img in galleryImages"
          :key="img.url"
          class="relative rounded-xl overflow-hidden border-2 text-left group transition aspect-4/3 bg-gray-100 dark:bg-gray-800"
          :class="modelValue === img.url
            ? 'border-red-600 ring-2 ring-red-200 dark:ring-red-900'
            : 'border-gray-200 hover:border-red-300 dark:border-gray-700'"
        >
          <img
            :src="img.url"
            :alt="img.name"
            @click="selectImage(img.url)"
            class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300 cursor-pointer"
          />

          <div
            @click="selectImage(img.url)"
            class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/85 via-black/50 to-transparent p-1.5 cursor-pointer"
          >
            <span class="text-[10px] font-bold text-white block truncate leading-tight">
              {{ img.name }}
            </span>
            <span v-if="img.size_kb" class="text-[9px] text-gray-300 font-mono block">
              {{ img.size_kb }} KB
            </span>
          </div>

          <!-- Active Checked Badge -->
          <span
            v-if="modelValue === img.url"
            class="absolute top-1 right-1 h-5 w-5 bg-red-600 text-white rounded-full flex items-center justify-center text-[10px] font-bold shadow-xs pointer-events-none"
          >
            ✓
          </span>

          <!-- Delete button on hover -->
          <button
            type="button"
            @click.stop="deleteGalleryImage(img)"
            class="absolute top-1 left-1 h-5 w-5 bg-black/70 hover:bg-red-600 text-white rounded-md flex items-center justify-center text-[10px] opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer shadow-xs"
            title="Eliminar de galería"
          >
            ✕
          </button>
        </div>
      </div>
    </div>

    <!-- MODE 3: Paste External URL -->
    <div v-else-if="activeMode === 'url'" class="space-y-2">
      <div class="flex gap-2">
        <input
          v-model="inputUrl"
          type="url"
          placeholder="https://images.unsplash.com/... o https://midominio.com/foto.jpg"
          class="flex-1 rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 text-xs text-gray-900 placeholder-gray-400 dark:placeholder-slate-400 focus:border-red-500 focus:bg-white dark:focus:bg-[#161e2e] focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:text-slate-100"
          @input="handleUrlInput"
        />
        <button
          type="button"
          @click="applyUrl"
          class="rounded-xl bg-gray-900 hover:bg-black text-white dark:bg-gray-700 dark:hover:bg-gray-600 px-3.5 py-2 text-xs font-bold transition cursor-pointer"
        >
          Aplicar
        </button>
      </div>
    </div>

    <!-- Preview Box of Current Selected Image -->
    <div
      v-if="modelValue"
      class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700"
    >
      <div class="flex items-center gap-3 min-w-0">
        <div
          class="shrink-0 rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 transition-all"
          :class="getAspectPreviewClass"
        >
          <img
            :src="modelValue"
            alt="Vista previa"
            class="h-full w-full object-cover"
            @error="hasImageError = true"
            @load="onImageLoadSuccess"
          />
        </div>
        <div class="text-xs truncate">
          <div class="flex items-center gap-1.5 flex-wrap">
            <span v-if="!hasImageError" class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
              <span>✓</span> Imagen lista
            </span>
            <span v-else class="text-red-500 font-bold flex items-center gap-1">
              <span>⚠</span> Enlace no accesible
            </span>
            <span v-if="imageDimensions" class="text-[10px] text-gray-400 font-mono bg-white dark:bg-gray-700 px-1.5 py-0.5 rounded border border-gray-200 dark:border-gray-600">
              {{ imageDimensions }}
            </span>
            <span v-if="selectedFileSize" class="text-[10px] text-gray-400 font-mono">
              ({{ selectedFileSize }})
            </span>
          </div>
          <p class="text-[10px] text-gray-500 dark:text-gray-400 font-mono truncate max-w-[240px] sm:max-w-xs mt-0.5" :title="modelValue">
            {{ modelValue }}
          </p>
        </div>
      </div>

      <div class="flex items-center gap-1.5 shrink-0">
        <button
          type="button"
          @click="copyImageUrl"
          class="rounded-xl border border-gray-200 bg-white hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 px-2.5 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-300 transition cursor-pointer"
          title="Copiar enlace directo"
        >
          Copiar URL
        </button>
        <button
          type="button"
          @click="clearImage"
          class="rounded-xl p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
          title="Quitar imagen"
        >
          ✕
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue'
import api from '@/plugins/axios'
import { useToasts } from '@/composables/useToasts'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue', 'change'])
const { addToast } = useToasts()

const activeMode = ref('upload')
const isDragging = ref(false)
const uploading = ref(false)
const compressionStatusText = ref('')
const compressionStats = ref('')
const lastOptimization = ref(null)
const imageDimensions = ref('')
const fileInputRef = ref(null)
const inputUrl = ref(props.modelValue || '')
const hasImageError = ref(false)
const selectedFileSize = ref('')
const selectedAspect = ref('free')

const aspectPresets = [
  { id: 'free', label: 'Libre' },
  { id: '16-9', label: '16:9 Banner' },
  { id: '4-3', label: '4:3 Platillo' },
  { id: '1-1', label: '1:1 Cuadrado' },
]

const getAspectPreviewClass = computed(() => {
  switch (selectedAspect.value) {
    case '16-9':
      return 'w-24 h-14'
    case '4-3':
      return 'w-20 h-15'
    case '1-1':
      return 'w-14 h-14'
    default:
      return 'w-16 h-14'
  }
})

const galleryImages = ref([
  {
    name: 'Pollo Asado al Carbón',
    url: '/assets/images/hero_pollo_asado_1794076709512.jpg',
    size_kb: 240,
  },
  {
    name: 'Paquete Familiar Tradicional',
    url: '/assets/images/paquete_familiar_1794076710078.jpg',
    size_kb: 195,
  },
  {
    name: 'Salsas Caseras & Complementos',
    url: '/assets/images/salsas_artesanales_1794076710181.jpg',
    size_kb: 180,
  },
  {
    name: 'Logotipo Oficial Pollo Feliz',
    url: '/logo.png',
    size_kb: 65,
  },
  {
    name: 'Pollo Entero Dorado',
    url: 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=600&auto=format&fit=crop&q=80',
    size_kb: 110,
  },
  {
    name: 'Medio Pollo Asado',
    url: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=600&auto=format&fit=crop&q=80',
    size_kb: 125,
  },
  {
    name: 'Combo Pollo y Papas',
    url: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80',
    size_kb: 140,
  },
  {
    name: 'Bebidas & Aguas Frescas',
    url: 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&auto=format&fit=crop&q=80',
    size_kb: 95,
  },
])

watch(
  () => props.modelValue,
  (val) => {
    inputUrl.value = val || ''
    hasImageError.value = false
  }
)

const onImageLoadSuccess = (e) => {
  hasImageError.value = false
  if (e.target && e.target.naturalWidth) {
    imageDimensions.value = `${e.target.naturalWidth}×${e.target.naturalHeight}px`
  }
}

const triggerFileInput = () => {
  if (fileInputRef.value) {
    fileInputRef.value.click()
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files?.[0]
  if (file) {
    processFile(file)
  }
}

const handleFileDrop = (event) => {
  isDragging.value = false
  const file = event.dataTransfer.files?.[0]
  if (file) {
    processFile(file)
  }
}

const formatBytes = (bytes) => {
  if (!bytes) return ''
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}

/**
 * Cliente-side Canvas Image Compressor & Resizer
 * Comprime y redimensiona fotos pesadas manteniendo una calidad óptima.
 */
const compressImageFile = (file, maxWidth = 1600, quality = 0.85) => {
  return new Promise((resolve, reject) => {
    if (file.type === 'image/svg+xml') {
      const reader = new FileReader()
      reader.onload = (e) => resolve({ dataUrl: e.target.result, size: file.size, width: 0, height: 0 })
      reader.onerror = reject
      reader.readAsDataURL(file)
      return
    }

    const reader = new FileReader()
    reader.onload = (readerEvent) => {
      const img = new Image()
      img.onload = () => {
        let width = img.width
        let height = img.height

        if (width > maxWidth || height > maxWidth) {
          if (width > height) {
            height = Math.round((height * maxWidth) / width)
            width = maxWidth
          } else {
            width = Math.round((width * maxWidth) / height)
            height = maxWidth
          }
        }

        const canvas = document.createElement('canvas')
        canvas.width = width
        canvas.height = height

        const ctx = canvas.getContext('2d')
        ctx.imageSmoothingEnabled = true
        ctx.imageSmoothingQuality = 'high'
        ctx.drawImage(img, 0, 0, width, height)

        // Preferir WebP, fallback a JPEG
        const outputMime = file.type === 'image/png' ? 'image/png' : 'image/webp'
        let compressedDataUrl = canvas.toDataURL(outputMime, quality)

        // Calcular tamaño aproximado del base64
        const head = `data:${outputMime};base64,`
        const approxSize = Math.round((compressedDataUrl.length - head.length) * 3 / 4)

        resolve({
          dataUrl: compressedDataUrl,
          size: approxSize,
          width,
          height,
          mime: outputMime,
        })
      }
      img.onerror = () => reject(new Error('Error al decodificar la imagen'))
      img.src = readerEvent.target.result
    }
    reader.onerror = reject
    reader.readAsDataURL(file)
  })
}

const processFile = async (file) => {
  const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/gif']
  if (!validTypes.includes(file.type)) {
    addToast('Formato no soportado. Sube JPG, PNG, WEBP o SVG.', 'error')
    return
  }

  const maxSize = 10 * 1024 * 1024 // 10MB
  if (file.size > maxSize) {
    addToast('El archivo es demasiado grande (máx 10 MB).', 'error')
    return
  }

  uploading.value = true
  compressionStatusText.value = 'Comprimiendo y optimizando resolución...'
  compressionStats.value = ''

  try {
    const originalFormatted = formatBytes(file.size)
    const compressed = await compressImageFile(file, 1600, 0.85)
    const compressedFormatted = formatBytes(compressed.size)

    const savedPercent = Math.max(0, Math.round(((file.size - compressed.size) / file.size) * 100))
    lastOptimization.value = {
      originalSize: originalFormatted,
      compressedSize: compressedFormatted,
      savedPercent,
    }
    selectedFileSize.value = compressedFormatted
    imageDimensions.value = `${compressed.width}×${compressed.height}px`

    compressionStatusText.value = 'Guardando en la galería del restaurante...'
    compressionStats.value = `${originalFormatted} ➔ ${compressedFormatted} (-${savedPercent}%)`

    // Enviar al backend
    try {
      const res = await api.post('/admin/uploads/image', {
        name: file.name,
        image_base64: compressed.dataUrl,
        size_kb: Math.round(compressed.size / 1024),
        mime_type: compressed.mime || file.type,
      })

      const finalUrl = res.data?.url || compressed.dataUrl
      selectImage(finalUrl)

      // Agregar a galería local
      galleryImages.value.unshift({
        id: res.data?.image?.id || 'img-' + Date.now(),
        name: file.name,
        url: finalUrl,
        size_kb: Math.round(compressed.size / 1024),
      })

      addToast(`¡Imagen optimizada exitosamente! Ahorro del ${savedPercent}% de tamaño.`, 'success')
    } catch (uploadErr) {
      console.warn('Fallback a almacenamiento de datos local', uploadErr)
      selectImage(compressed.dataUrl)
      addToast('Imagen cargada y optimizada en tu navegador.', 'success')
    }
  } catch (err) {
    console.error('Error procesando imagen', err)
    addToast('No se pudo procesar la imagen seleccionada.', 'error')
  } finally {
    uploading.value = false
  }
}

const selectImage = (url) => {
  emit('update:modelValue', url)
  emit('change', url)
  hasImageError.value = false
}

const deleteGalleryImage = async (img) => {
  try {
    if (img.id) {
      await api.delete(`/admin/uploads/image/${img.id}`)
    } else {
      await api.delete('/admin/uploads/image', { data: { url: img.url } })
    }
    galleryImages.value = galleryImages.value.filter((g) => g.url !== img.url)
    if (props.modelValue === img.url) {
      clearImage()
    }
    addToast(`"${img.name}" eliminada de la galería.`, 'info')
  } catch (e) {
    // Si es una imagen local, retirarla de la vista
    galleryImages.value = galleryImages.value.filter((g) => g.url !== img.url)
    addToast('Imagen removida de la vista.', 'info')
  }
}

const handleUrlInput = () => {
  if (inputUrl.value.trim().startsWith('http') || inputUrl.value.trim().startsWith('/')) {
    selectImage(inputUrl.value.trim())
  }
}

const applyUrl = () => {
  if (!inputUrl.value.trim()) {
    addToast('Ingresa un enlace URL válido.', 'error')
    return
  }
  selectImage(inputUrl.value.trim())
  addToast('Enlace de imagen aplicado correctamente.', 'success')
}

const clearImage = () => {
  selectImage('')
  inputUrl.value = ''
  selectedFileSize.value = ''
  imageDimensions.value = ''
  lastOptimization.value = null
}

const copyImageUrl = async () => {
  if (!props.modelValue) return
  try {
    if (navigator.clipboard) {
      await navigator.clipboard.writeText(props.modelValue)
      addToast('URL copiada al portapapeles.', 'success')
    }
  } catch (e) {
    addToast('URL: ' + props.modelValue, 'info')
  }
}

const loadServerGallery = async () => {
  try {
    const res = await api.get('/admin/uploads/gallery')
    if (res.data?.images && Array.isArray(res.data.images)) {
      const serverImgs = res.data.images
      // Unir evitando duplicados
      serverImgs.forEach((img) => {
        if (!galleryImages.value.some((g) => g.url === img.url)) {
          galleryImages.value.unshift(img)
        }
      })
    }
  } catch (err) {
    // Galería fallback permanece
  }
}

onMounted(() => {
  loadServerGallery()
})
</script>
