<template>
  <Teleport to="body">
    <div
      v-if="visible"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
      @click.self="cancel"
    >
      <div class="w-full max-w-3xl rounded-2xl bg-white p-8 shadow-2xl dark:bg-gray-900">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Recortar Imagen</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Arrastra para ajustar. La relación de aspecto es {{ aspectRatio }}.</p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Canvas Area -->
          <div class="lg:col-span-2">
            <div
              class="relative rounded-xl border-4 border-red-300 overflow-hidden bg-gray-900"
              :style="{ aspectRatio: aspectRatio }"
            >
              <img
                ref="imageElement"
                :src="sourceImage"
                alt="Crop"
                class="absolute inset-0 w-full h-full object-cover cursor-move"
                @mousedown="startDrag"
                @touchstart="startDrag"
              />
              <div class="absolute inset-0 border-4 border-dashed border-yellow-300 pointer-events-none" />
            </div>
          </div>

          <!-- Controls -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Zoom</label>
              <input
                v-model.number="zoom"
                type="range"
                min="0.5"
                max="3"
                step="0.1"
                class="w-full"
              />
              <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ (zoom * 100).toFixed(0) }}%</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Relación de Aspecto</label>
              <select v-model="aspectRatio" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:bg-gray-800">
                <option value="1/1">1:1 (Cuadrado)</option>
                <option value="4/3">4:3 (Panorama)</option>
                <option value="16/9">16:9 (Pantalla)</option>
                <option value="3/2">3:2 (Foto)</option>
                <option value="2/3">2:3 (Portada)</option>
                <option value="1.5/1">1.5:1 (Custom)</option>
              </select>
            </div>

            <div class="rounded-lg bg-gray-100 dark:bg-gray-800 p-3">
              <p class="text-xs font-semibold text-gray-900 dark:text-white mb-2">Vista Previa</p>
              <div
                class="relative rounded-lg overflow-hidden bg-gray-200"
                :style="{ aspectRatio: aspectRatio }"
              >
                <canvas
                  ref="previewCanvas"
                  class="w-full h-full"
                />
              </div>
            </div>

            <div class="flex gap-2">
              <button
                class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300"
                @click="cancel"
              >
                Cancelar
              </button>
              <button
                class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700"
                @click="applyCrop"
              >
                Aplicar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue'

const emit = defineEmits(['crop', 'cancel'])

const visible = ref(false)
const sourceImage = ref('')
const imageElement = ref(null)
const previewCanvas = ref(null)
const zoom = ref(1)
const aspectRatio = ref('1/1')
const offsetX = ref(0)
const offsetY = ref(0)
let isDragging = false
let dragStartX = 0
let dragStartY = 0

watch(
  [zoom, offsetX, offsetY, aspectRatio],
  () => {
    updatePreview()
  },
  { flush: 'post' }
)

const show = (imageSrc, ratio = '1/1') => {
  sourceImage.value = imageSrc
  aspectRatio.value = ratio
  zoom.value = 1
  offsetX.value = 0
  offsetY.value = 0
  visible.value = true
}

const startDrag = (e) => {
  isDragging = true
  dragStartX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX
  dragStartY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY

  const handleMove = (moveEvent) => {
    if (!isDragging) return

    const currentX = moveEvent.type.includes('touch') ? moveEvent.touches[0].clientX : moveEvent.clientX
    const currentY = moveEvent.type.includes('touch') ? moveEvent.touches[0].clientY : moveEvent.clientY

    const deltaX = (currentX - dragStartX) * 2
    const deltaY = (currentY - dragStartY) * 2

    offsetX.value += deltaX
    offsetY.value += deltaY

    dragStartX = currentX
    dragStartY = currentY
  }

  const handleEnd = () => {
    isDragging = false
    document.removeEventListener('mousemove', handleMove)
    document.removeEventListener('touchmove', handleMove)
    document.removeEventListener('mouseup', handleEnd)
    document.removeEventListener('touchend', handleEnd)
  }

  document.addEventListener('mousemove', handleMove)
  document.addEventListener('touchmove', handleMove)
  document.addEventListener('mouseup', handleEnd)
  document.addEventListener('touchend', handleEnd)
}

const updatePreview = async () => {
  if (!previewCanvas.value || !imageElement.value) return

  await nextTick()

  const canvas = previewCanvas.value
  const ctx = canvas.getContext('2d')
  const img = imageElement.value

  const rect = imageElement.value.parentElement.getBoundingClientRect()
  canvas.width = rect.width
  canvas.height = rect.height

  ctx.save()
  ctx.translate(canvas.width / 2, canvas.height / 2)
  ctx.scale(zoom.value, zoom.value)
  ctx.translate(offsetX.value / zoom.value, offsetY.value / zoom.value)
  ctx.drawImage(img, -img.width / 2, -img.height / 2)
  ctx.restore()
}

const applyCrop = () => {
  if (!previewCanvas.value) return

  const canvas = previewCanvas.value
  const croppedImage = canvas.toDataURL('image/png')

  emit('crop', croppedImage)
  visible.value = false
}

const cancel = () => {
  visible.value = false
  emit('cancel')
}

defineExpose({ show })
</script>
