<!-- src/components/admin/BranchModal.vue -->
<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 my-8 transition-all"
        role="dialog"
        aria-modal="true"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400 text-lg">
              📍
            </span>
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">
                {{ isEditing ? 'Editar Sucursal' : 'Nueva Sucursal' }}
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ isEditing ? 'Actualiza teléfono, horarios o dirección' : 'Registra un nuevo punto de venta para Pollo Feliz' }}
              </p>
            </div>
          </div>
          <button
            @click="emit('close')"
            class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200 cursor-pointer"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="handleSubmit" class="mt-5 space-y-4">
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
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white font-semibold"
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
              placeholder="Ej. Av. 5 de Mayo #123, Colonia Centro, Durango"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
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
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white font-medium"
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
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
            </div>
          </div>

          <!-- Google Maps Link -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Enlace de Google Maps / Ubicación
            </label>
            <input
              v-model="form.map_link"
              type="url"
              placeholder="https://maps.google.com/?q=..."
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <!-- Image URL -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Foto de la Sucursal (Opcional)
            </label>
            <div class="flex gap-2">
              <input
                v-model="form.image_url"
                type="url"
                placeholder="https://images.unsplash.com/..."
                class="flex-1 rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 text-xs text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
              <button
                type="button"
                @click="usePresetBranchImage"
                class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition cursor-pointer shrink-0"
              >
                Foto sugerida
              </button>
            </div>

            <!-- Preview box -->
            <div v-if="form.image_url" class="mt-2 flex items-center gap-3 p-2 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700">
              <img
                :src="form.image_url"
                alt="Vista previa"
                class="h-12 w-12 rounded-lg object-cover"
                @error="imageError = true"
                @load="imageError = false"
              />
              <div class="text-xs">
                <span v-if="!imageError" class="text-green-600 font-semibold">✓ Imagen válida</span>
                <span v-else class="text-red-500 font-semibold">⚠ No se pudo cargar la imagen</span>
                <p class="text-[10px] text-gray-400 truncate max-w-[280px]">{{ form.image_url }}</p>
              </div>
            </div>
          </div>

          <!-- Toggle: is_open -->
          <div class="pt-1">
            <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <input
                v-model="form.is_open"
                type="checkbox"
                class="h-4 w-4 rounded text-red-600 focus:ring-red-500"
              />
              <div>
                <span class="block text-xs font-bold text-gray-800 dark:text-gray-200">Sucursal Operativa / Abierta</span>
                <span class="block text-[10px] text-gray-400">Permite a los usuarios elegir esta sucursal para recoger pedidos</span>
              </div>
            </label>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
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
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'

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

const emit = defineEmits(['close', 'save'])

const isEditing = ref(false)
const isSubmitting = ref(false)
const imageError = ref(false)

const SUGGESTED_BRANCH_IMAGES = [
  'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&auto=format&fit=crop&q=80',
]

const form = ref({
  name: '',
  address: '',
  phone: '',
  schedule: 'Lun - Dom: 10:00 AM - 10:00 PM',
  image_url: '',
  map_link: '',
  is_open: true,
})

const usePresetBranchImage = () => {
  const random = SUGGESTED_BRANCH_IMAGES[Math.floor(Math.random() * SUGGESTED_BRANCH_IMAGES.length)]
  form.value.image_url = random
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      imageError.value = false
      if (props.branchToEdit) {
        isEditing.value = true
        form.value = {
          name: props.branchToEdit.name || '',
          address: props.branchToEdit.address || '',
          phone: props.branchToEdit.phone || '',
          schedule: props.branchToEdit.schedule || 'Lun - Dom: 10:00 AM - 10:00 PM',
          image_url: props.branchToEdit.image_url || '',
          map_link: props.branchToEdit.map_link || '',
          is_open: props.branchToEdit.is_open ?? true,
        }
      } else {
        isEditing.value = false
        form.value = {
          name: '',
          address: '',
          phone: '618-123-4567',
          schedule: 'Lun - Dom: 10:00 AM - 10:00 PM',
          image_url: SUGGESTED_BRANCH_IMAGES[0],
          map_link: 'https://maps.google.com/?q=Durango',
          is_open: true,
        }
      }
    }
  }
)

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
