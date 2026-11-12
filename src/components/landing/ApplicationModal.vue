<!-- src/components/landing/ApplicationModal.vue -->
<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 my-8 transition-all max-h-[92vh] flex flex-col overflow-hidden"
        role="dialog"
        aria-modal="true"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800 shrink-0">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400 text-lg">
              💼
            </span>
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">
                Postularme a Vacante
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[280px]">
                {{ vacancy?.title || 'Oportunidad de Empleo' }} · {{ vacancy?.location || 'Pollo Feliz Durango' }}
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

        <!-- Success State -->
        <div v-if="submittedSuccess" class="py-8 text-center space-y-4">
          <span class="text-5xl block animate-bounce">🎉</span>
          <h4 class="text-xl font-black text-gray-950 dark:text-white">
            ¡Postulación Recibida con Éxito!
          </h4>
          <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 max-w-sm mx-auto leading-relaxed">
            Gracias por tu interés en unirte a la gran familia de <strong class="text-red-600">Pollo Feliz Durango</strong>. Nuestro equipo de Recursos Humanos revisará tu perfil y te contactará a la brevedad.
          </p>
          <div class="pt-4">
            <button
              type="button"
              @click="handleFinish"
              class="rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 text-xs shadow-md transition cursor-pointer"
            >
              Cerrar y volver al sitio
            </button>
          </div>
        </div>

        <!-- Form Body -->
        <form v-else @submit.prevent="handleSubmit" class="mt-4 space-y-3.5 overflow-y-auto flex-1 pr-1">
          <div v-if="errorMessage" class="p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 dark:bg-rose-950/60 dark:border-rose-900 dark:text-rose-300 text-xs flex items-center gap-2">
            <span>⚠️</span>
            <span class="font-bold">{{ errorMessage }}</span>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Nombre Completo *
            </label>
            <input
              v-model="form.applicant_name"
              type="text"
              required
              placeholder="Ej. Juan Pérez García"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white font-semibold transition"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Teléfono / WhatsApp *
              </label>
              <input
                v-model="form.phone"
                type="tel"
                required
                placeholder="Ej. 618-123-4567"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white font-semibold transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Correo Electrónico *
              </label>
              <input
                v-model="form.email"
                type="email"
                required
                placeholder="ejemplo@correo.com"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
              />
            </div>
          </div>

          <!-- Resume / CV Upload -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Currículum Vitae / Solicitud de Empleo (PDF, Word o Imagen)
            </label>
            <div class="flex items-center gap-3 p-3 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40">
              <input
                ref="fileInput"
                type="file"
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                class="hidden"
                @change="handleFileChange"
              />
              <button
                type="button"
                @click="triggerFileInput"
                class="rounded-xl border border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-100 transition cursor-pointer shrink-0 shadow-2xs"
              >
                📎 Seleccionar Archivo
              </button>
              <div class="text-xs truncate flex-1">
                <span v-if="form.resume_name" class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                  <span>✓</span> <span class="truncate">{{ form.resume_name }}</span>
                </span>
                <span v-else class="text-gray-400">
                  Ningún archivo seleccionado (opcional)
                </span>
              </div>
              <button
                v-if="form.resume_name"
                type="button"
                @click="removeFile"
                class="text-xs text-rose-500 hover:underline font-bold"
              >
                Quitar
              </button>
            </div>
          </div>

          <!-- Notes / Message -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Mensaje o Experiencia Previa (Opcional)
            </label>
            <textarea
              v-model="form.notes"
              rows="3"
              placeholder="Cuéntanos brevemente sobre tu experiencia en cocina, atención al cliente o tu disponibilidad de horario..."
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white transition"
            ></textarea>
          </div>

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
              <span>Enviar Postulación</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import api from '@/plugins/axios'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  vacancy: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'applied'])

const fileInput = ref(null)
const isSubmitting = ref(false)
const submittedSuccess = ref(false)
const errorMessage = ref('')

const form = ref({
  applicant_name: '',
  phone: '',
  email: '',
  notes: '',
  resume_name: null,
  resume_data: null,
})

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileChange = (e) => {
  const file = e.target.files?.[0]
  if (!file) return

  if (file.size > 5 * 1024 * 1024) {
    errorMessage.value = 'El archivo supera el límite de 5MB.'
    return
  }

  form.value.resume_name = file.name
  const reader = new FileReader()
  reader.onload = (event) => {
    form.value.resume_data = event.target?.result
  }
  reader.readAsDataURL(file)
}

const removeFile = () => {
  form.value.resume_name = null
  form.value.resume_data = null
  if (fileInput.value) fileInput.value.value = ''
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      submittedSuccess.value = false
      errorMessage.value = ''
      form.value = {
        applicant_name: '',
        phone: '',
        email: '',
        notes: '',
        resume_name: null,
        resume_data: null,
      }
    }
  }
)

const handleFinish = () => {
  emit('applied')
  emit('close')
}

const handleSubmit = async () => {
  errorMessage.value = ''
  if (!form.value.applicant_name.trim() || !form.value.phone.trim() || !form.value.email.trim()) {
    errorMessage.value = 'Por favor completa todos los campos obligatorios (*).'
    return
  }

  isSubmitting.value = true
  try {
    const payload = {
      vacancy_id: props.vacancy?.id || 1,
      vacancy_title: props.vacancy?.title || 'Vacante General',
      applicant_name: form.value.applicant_name.trim(),
      phone: form.value.phone.trim(),
      email: form.value.email.trim(),
      notes: form.value.notes ? form.value.notes.trim() : '',
      resume_name: form.value.resume_name,
      resume_data: form.value.resume_data,
    }

    await api.post('/jobs/apply', payload)
    submittedSuccess.value = true
  } catch (err) {
    errorMessage.value = err?.response?.data?.message || 'Error al enviar tu solicitud. Intenta nuevamente o llama a nuestra sucursal.'
  } finally {
    isSubmitting.value = false
  }
}
</script>
