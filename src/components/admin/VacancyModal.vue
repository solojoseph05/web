<!-- src/components/admin/VacancyModal.vue -->
<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-lg rounded-3xl bg-white shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 my-6 transition-all max-h-[92vh] flex flex-col overflow-hidden"
        role="dialog"
        aria-modal="true"
      >
        <!-- Modal Header (Pinned) -->
        <div class="flex items-center justify-between p-6 pb-4 border-b border-gray-100 dark:border-gray-800 shrink-0">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 text-lg">
              💼
            </span>
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">
                {{ isEditing ? 'Editar Vacante' : 'Publicar Nueva Vacante' }}
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ isEditing ? 'Actualiza requisitos, horario o estado del puesto' : 'Crea una nueva vacante para la bolsa de trabajo en la web' }}
              </p>
            </div>
          </div>
          <button
            @click="emit('close')"
            class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200 cursor-pointer transition"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Form Body (Scrollable with ref for auto-scroll) -->
        <form novalidate @submit.prevent="handleSubmit" ref="formContainer" class="p-6 space-y-4 overflow-y-auto flex-1">
          <!-- Validation Warning Banner -->
          <div
            v-if="validationError"
            class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 dark:bg-rose-950/60 dark:border-rose-900 dark:text-rose-300 text-xs flex items-center gap-2 animate-pulse"
          >
            <span class="text-base">⚠️</span>
            <span class="font-bold">{{ validationError }}</span>
          </div>

          <!-- Job Title -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Título del Puesto *
            </label>
            <input
              v-model="form.title"
              type="text"
              placeholder="Ej. Parrillero de Pollo Asado, Ayudante de Cocina, Cajero(a)..."
              class="w-full rounded-xl border px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-hidden bg-gray-50 dark:bg-gray-800 dark:text-white font-semibold transition"
              :class="errors.title
                ? 'border-red-500 bg-red-50/50 dark:border-red-500 dark:bg-red-950/30 ring-1 ring-red-500'
                : 'border-gray-200 focus:border-red-500 focus:bg-gray-50 dark:focus:bg-gray-800 dark:border-gray-700'"
              @input="errors.title = ''; validationError = ''"
            />
            <p v-if="errors.title" class="text-[11px] text-red-600 dark:text-red-400 font-bold mt-1">
              {{ errors.title }}
            </p>
          </div>

          <!-- Employment Type & Location Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Tipo de Empleo *
              </label>
              <select
                v-model="form.employment_type"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 font-semibold focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-red-500 cursor-pointer"
              >
                <option value="Tiempo completo">⏰ Tiempo completo</option>
                <option value="Medio tiempo">⏱️ Medio tiempo</option>
                <option value="Fines de semana">📅 Fines de semana</option>
                <option value="Por temporada">🍂 Por temporada</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Sucursal / Ubicación *
              </label>
              <select
                v-model="form.location"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 font-semibold focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-red-500 cursor-pointer"
              >
                <option value="Sucursal Centro Histórico">📍 Sucursal Centro Histórico</option>
                <option value="Sucursal Polanco">📍 Sucursal Polanco</option>
                <option value="Sucursal Jardines de Durango">📍 Sucursal Jardines de Durango</option>
                <option value="Todas las sucursales">🏢 Todas las sucursales</option>
              </select>
            </div>
          </div>

          <!-- Salary & Schedule Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Rango Salarial (Opcional)
              </label>
              <input
                v-model="form.salary_range"
                type="text"
                placeholder="Ej. $9,500 - $12,500 MXN mensuales"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Horario & Jornada
              </label>
              <input
                v-model="form.schedule"
                type="text"
                placeholder="Ej. Lunes a Sábado: 8:00 AM - 4:00 PM"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
              />
            </div>
          </div>

          <!-- Description & Responsibilities -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Descripción del Puesto *
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Detalla las actividades principales del puesto y funciones a desempeñar en el restaurante..."
              class="w-full rounded-xl border px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-hidden bg-gray-50 dark:bg-gray-800 dark:text-white transition"
              :class="errors.description
                ? 'border-red-500 bg-red-50/50 dark:border-red-500 dark:bg-red-950/30 ring-1 ring-red-500'
                : 'border-gray-200 focus:border-red-500 focus:bg-gray-50 dark:focus:bg-gray-800 dark:border-gray-700'"
              @input="errors.description = ''; validationError = ''"
            ></textarea>
            <p v-if="errors.description" class="text-[11px] text-red-600 dark:text-red-400 font-bold mt-1">
              {{ errors.description }}
            </p>
          </div>

          <!-- Requirements & Benefits Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Requisitos del Aspirante
              </label>
              <textarea
                v-model="form.requirements"
                rows="2"
                placeholder="Ej. Mayor de 18 años, actitud de servicio, experiencia previa..."
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
              ></textarea>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Beneficios Pollo Feliz
              </label>
              <textarea
                v-model="form.benefits"
                rows="2"
                placeholder="Ej. Comida diaria incluida, prestaciones de ley, propinas, bonos..."
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
              ></textarea>
            </div>
          </div>

          <!-- Image Upload & Preset Picker -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Fotografía de la Vacante (Opcional)
              </label>
              <button
                v-if="form.image_url"
                type="button"
                @click="form.image_url = ''"
                class="text-[11px] font-bold text-rose-600 hover:text-rose-700 dark:text-rose-400 cursor-pointer flex items-center gap-1"
              >
                <span>✕ Quitar foto</span>
              </button>
              <span v-else class="text-[11px] text-gray-400">
                Visible en la tarjeta pública
              </span>
            </div>

            <!-- Quick Role Presets -->
            <div class="mb-2.5">
              <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 block mb-1">
                Fotos sugeridas por área de trabajo:
              </span>
              <div class="flex flex-wrap gap-1.5">
                <button
                  v-for="preset in jobImagePresets"
                  :key="preset.label"
                  type="button"
                  @click="form.image_url = preset.url"
                  class="rounded-lg px-2.5 py-1 text-[11px] font-bold border transition cursor-pointer"
                  :class="form.image_url === preset.url
                    ? 'bg-red-600 text-white border-red-600 shadow-2xs'
                    : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700'"
                >
                  {{ preset.label }}
                </button>
              </div>
            </div>

            <!-- ImageUploader Component (File Drag&Drop, Pollo Feliz Gallery or URL) -->
            <ImageUploader v-model="form.image_url" />
          </div>

          <!-- State / Availability Toggle -->
          <div class="flex items-center justify-between p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
            <div>
              <span class="block text-xs font-bold text-gray-800 dark:text-gray-200">
                Estado de la Vacante
              </span>
              <span class="text-[11px] text-gray-500 dark:text-gray-400">
                {{ form.is_active ? 'Visible y lista para recibir solicitudes en la web' : 'Pausada (no se muestra al público)' }}
              </span>
            </div>
            <button
              type="button"
              @click="form.is_active = !form.is_active"
              class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden"
              :class="form.is_active ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-gray-700'"
            >
              <span
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
                :class="form.is_active ? 'translate-x-5' : 'translate-x-0'"
              />
            </button>
          </div>
        </form>

        <!-- Modal Footer (Pinned) -->
        <div class="flex items-center justify-end gap-3 p-6 pt-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shrink-0">
          <button
            type="button"
            @click="emit('close')"
            class="rounded-xl border border-gray-200 px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 cursor-pointer transition"
          >
            Cancelar
          </button>
          <button
            type="button"
            :disabled="isSubmitting"
            @click="handleSubmit"
            class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:from-red-700 hover:to-red-800 disabled:opacity-50 transition cursor-pointer flex items-center gap-2"
          >
            <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span>{{ isEditing ? 'Guardar Cambios' : 'Publicar Vacante' }}</span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, reactive, watch, nextTick } from 'vue'
import ImageUploader from '@/components/admin/ImageUploader.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  jobToEdit: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'save'])

const formContainer = ref(null)
const isEditing = ref(false)
const isSubmitting = ref(false)
const validationError = ref('')

const errors = reactive({
  title: '',
  description: '',
})

const jobImagePresets = [
  {
    label: '👨‍🍳 Parrilla & Asador',
    url: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&auto=format&fit=crop&q=80',
  },
  {
    label: '🛎️ Atención & Caja',
    url: 'https://images.unsplash.com/photo-1556740738-b6a63e27c4df?w=800&auto=format&fit=crop&q=80',
  },
  {
    label: '🛵 Reparto en Moto',
    url: 'https://images.unsplash.com/photo-1617347454431-f49d7ff5c3b1?w=800&auto=format&fit=crop&q=80',
  },
  {
    label: '🍳 Cocina & Auxiliar',
    url: 'https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=800&auto=format&fit=crop&q=80',
  },
  {
    label: '🏢 Equipo de Sucursal',
    url: 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=800&auto=format&fit=crop&q=80',
  },
]

const form = ref({
  title: '',
  employment_type: 'Tiempo completo',
  location: 'Sucursal Centro Histórico',
  salary_range: '',
  schedule: 'Lunes a Sábado, turnos rotativos',
  description: '',
  requirements: '',
  benefits: '',
  image_url: '',
  is_active: true,
})

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      validationError.value = ''
      errors.title = ''
      errors.description = ''

      nextTick(() => {
        if (formContainer.value) {
          formContainer.value.scrollTop = 0
        }
      })

      if (props.jobToEdit) {
        isEditing.value = true
        form.value = {
          title: props.jobToEdit.title || '',
          employment_type: props.jobToEdit.employment_type || 'Tiempo completo',
          location: props.jobToEdit.location || 'Sucursal Centro Histórico',
          salary_range: props.jobToEdit.salary_range || '',
          schedule: props.jobToEdit.schedule || '',
          description: props.jobToEdit.description || '',
          requirements: props.jobToEdit.requirements || '',
          benefits: props.jobToEdit.benefits || '',
          image_url: props.jobToEdit.image_url || '',
          is_active: props.jobToEdit.is_active ?? true,
        }
      } else {
        isEditing.value = false
        form.value = {
          title: '',
          employment_type: 'Tiempo completo',
          location: 'Sucursal Centro Histórico',
          salary_range: '',
          schedule: 'Lunes a Sábado: 8:00 AM - 4:00 PM (turnos rotativos)',
          description: '',
          requirements: '',
          benefits: '',
          image_url: '',
          is_active: true,
        }
      }
    }
  }
)

const handleSubmit = () => {
  errors.title = ''
  errors.description = ''
  validationError.value = ''

  let hasError = false
  if (!form.value.title || !form.value.title.trim()) {
    errors.title = 'El título del puesto es obligatorio.'
    hasError = true
  }
  if (!form.value.description || !form.value.description.trim()) {
    errors.description = 'La descripción y requisitos son obligatorios.'
    hasError = true
  }

  if (hasError) {
    validationError.value = 'Por favor completa los campos obligatorios (*) marcados arriba.'
    if (formContainer.value) {
      formContainer.value.scrollTo({ top: 0, behavior: 'smooth' })
    }
    return
  }

  isSubmitting.value = true
  try {
    const payload = {
      title: form.value.title.trim(),
      employment_type: form.value.employment_type,
      location: form.value.location,
      salary_range: form.value.salary_range ? form.value.salary_range.trim() : '',
      schedule: form.value.schedule ? form.value.schedule.trim() : '',
      description: form.value.description.trim(),
      requirements: form.value.requirements ? form.value.requirements.trim() : '',
      benefits: form.value.benefits ? form.value.benefits.trim() : '',
      image_url: form.value.image_url ? form.value.image_url.trim() : null,
      is_active: form.value.is_active,
    }
    if (props.jobToEdit?.id) {
      payload.id = props.jobToEdit.id
    }
    emit('save', payload)
  } finally {
    isSubmitting.value = false
  }
}
</script>
