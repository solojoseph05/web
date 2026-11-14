<!-- src/components/landing/Contact.vue -->
<template>
  <section id="contact" class="py-16 scroll-mt-24 bg-gray-50 dark:bg-gray-900 transition-colors">
    <div class="container mx-auto px-4">
      <!-- Encabezado -->
      <div class="mx-auto mb-12 max-w-3xl text-center" data-aos="fade-up">
        <span class="inline-block bg-red-600 text-white text-xs sm:text-sm font-bold px-4 py-1.5 rounded-full mb-3 shadow-sm">
          💬 Atención & Pedidos
        </span>
        <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tight">
          ¿Tienes alguna <span class="text-red-600 dark:text-red-500">pregunta o evento?</span>
        </h2>
        <p class="mt-3 text-gray-600 max-w-2xl mx-auto text-sm md:text-base dark:text-gray-300">
          Escríbenos para cotizar eventos especiales, taquizas, facturación o resolver cualquier duda. Nuestro equipo te responderá de inmediato.
        </p>
      </div>

      <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-10">
        <!-- COLUMNA IZQUIERDA: Información de Contacto & Sucursales (5 cols) -->
        <div class="lg:col-span-5 space-y-6" data-aos="fade-right">
          <div class="rounded-3xl bg-white p-6 shadow-md sm:p-8 dark:bg-gray-800 dark:border dark:border-gray-700 space-y-5">
            <h3 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
              <span>📍</span>
              <span>Canales de Atención</span>
            </h3>

            <div class="space-y-4 text-xs sm:text-sm text-gray-600 dark:text-gray-300">
              <!-- Dirección -->
              <div class="flex items-start gap-3.5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                  <span class="block font-bold text-gray-900 dark:text-white">Matriz Durango</span>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ landingAddress }}</p>
                </div>
              </div>

              <!-- Teléfono -->
              <div class="flex items-start gap-3.5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <div>
                  <span class="block font-bold text-gray-900 dark:text-white">Llámanos a Mostrador</span>
                  <a :href="`tel:${landingPhone}`" class="text-xs sm:text-sm font-semibold text-red-600 hover:underline dark:text-red-400 block mt-0.5">
                    {{ landingPhone }}
                  </a>
                </div>
              </div>

              <!-- Correo -->
              <div class="flex items-start gap-3.5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                  <span class="block font-bold text-gray-900 dark:text-white">Correo Electrónico</span>
                  <a :href="`mailto:${landingEmail}`" class="text-xs sm:text-sm font-semibold text-red-600 hover:underline dark:text-red-400 block mt-0.5">
                    {{ landingEmail }}
                  </a>
                </div>
              </div>
            </div>

            <!-- WhatsApp Direct Shortcut -->
            <div class="pt-2 border-t border-gray-100 dark:border-gray-700/60">
              <a
                :href="whatsAppDirectUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 text-xs shadow-md transition transform active:scale-98"
              >
                <span>💬 Chatear por WhatsApp Directo</span>
              </a>
            </div>
          </div>

          <!-- Mini Mapa de Google Embebido (carga diferida con IntersectionObserver) -->
          <div ref="mapFrameWrapper" class="relative h-56 overflow-hidden rounded-3xl bg-white shadow-md dark:bg-gray-800 dark:border dark:border-gray-700">
            <div
              v-if="!mapLoaded"
              class="absolute inset-0 flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400"
            >
              <span class="text-xl">📍</span>
              <span>Cargando mapa de Durango...</span>
            </div>
            <iframe
              v-if="mapLoaded"
              :src="googleMapEmbedUrl"
              width="100%"
              height="100%"
              style="border:0;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Mapa de sucursales Pollo Feliz Durango"
            ></iframe>
          </div>
        </div>

        <!-- COLUMNA DERECHA: Formulario Totalmente Funcional (7 cols) -->
        <div class="lg:col-span-7 rounded-3xl bg-white p-6 shadow-xl sm:p-8 dark:bg-gray-800 dark:border dark:border-gray-700" data-aos="fade-left">
          <!-- Successful Submission State -->
          <Transition name="fade">
            <div v-if="submittedSuccess" class="py-8 text-center space-y-4">
              <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 text-3xl animate-bounce">
                ✓
              </div>
              <div>
                <span class="inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 mb-2">
                  Folio: {{ confirmationFolio }}
                </span>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white">
                  ¡Mensaje Enviado con Éxito!
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 max-w-md mx-auto mt-2">
                  {{ feedbackMessage }}
                </p>
              </div>

              <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
                <button
                  @click="resetForm"
                  class="rounded-xl border border-gray-200 px-5 py-2.5 text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700 transition cursor-pointer"
                >
                  Enviar otro mensaje
                </button>
                <router-link
                  to="/menu"
                  class="rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-red-700 transition cursor-pointer"
                >
                  Ver Menú de Alimentos
                </router-link>
              </div>
            </div>
          </Transition>

          <!-- Interactive Form State -->
          <form v-if="!submittedSuccess" @submit.prevent="handleSubmit" class="space-y-4 sm:space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-700 gap-2">
              <div>
                <h3 class="text-lg font-black text-gray-900 dark:text-white">
                  Envíanos tu Consulta
                </h3>
                <p class="text-xs text-red-600 dark:text-red-400 font-semibold flex items-center gap-1 mt-0.5">
                  <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                  Se enviará a: <span class="font-mono font-bold">soporte@pollofelizdgo.com</span>
                </p>
              </div>
              <span class="text-xs text-gray-400">Respuesta en menos de 2 horas</span>
            </div>

            <!-- Error Banner -->
            <div
              v-if="feedbackError"
              class="rounded-2xl bg-red-50 p-4 text-xs font-bold text-red-700 border border-red-200 dark:bg-red-950/40 dark:border-red-900 dark:text-red-300"
            >
              {{ feedbackError }}
            </div>

            <!-- Row 1: Nombre & Teléfono -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 dark:text-gray-300">
                  Nombre completo <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="form.name"
                  type="text" 
                  required
                  placeholder="Ej. Juan Pérez"
                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition"
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 dark:text-gray-300">
                  Teléfono / WhatsApp <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="form.phone"
                  type="tel" 
                  required
                  placeholder="Ej. 618-123-4567"
                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition"
                />
              </div>
            </div>

            <!-- Row 2: Correo & Motivo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 dark:text-gray-300">
                  Correo electrónico <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="form.email"
                  type="email" 
                  required
                  placeholder="tucorreo@ejemplo.com"
                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition"
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 dark:text-gray-300">
                  Motivo de contacto
                </label>
                <select
                  v-model="form.subject"
                  class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition cursor-pointer"
                >
                  <option value="Consulta general">Consulta general</option>
                  <option value="Cotización para evento o taquiza">Cotización para evento o taquiza 🍗</option>
                  <option value="Facturación electrónica">Facturación electrónica 🧾</option>
                  <option value="Sugerencia o felicitación">Sugerencia o felicitación ⭐</option>
                </select>
              </div>
            </div>

            <!-- Row 3: Sucursal de Preferencia -->
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1.5 dark:text-gray-300">
                Sucursal de preferencia (opcional)
              </label>
              <select
                v-model="form.branch"
                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition cursor-pointer"
              >
                <option value="Cualquier sucursal">Cualquier sucursal de Durango</option>
                <option
                  v-for="b in availableBranches"
                  :key="b.id || b.name"
                  :value="b.name"
                >
                  {{ b.name }}
                </option>
              </select>
            </div>

            <!-- Campo Honeypot oculto antispam (invisible para usuarios reales) -->
            <div class="hidden" aria-hidden="true">
              <label for="contact-website-trap">Sitio web</label>
              <input
                id="contact-website-trap"
                v-model="form.website"
                type="text"
                name="website"
                tabindex="-1"
                autocomplete="off"
              />
            </div>

            <!-- Row 4: Mensaje -->
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1.5 dark:text-gray-300">
                Tu mensaje o detalles de tu evento <span class="text-red-500">*</span>
              </label>
              <textarea 
                v-model="form.message"
                rows="4" 
                required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition resize-none"
                placeholder="Escribe tu mensaje, cantidad de comensales para tu taquiza, fecha o detalles de facturación..."
              ></textarea>
            </div>

            <!-- Submit Button -->
            <button 
              type="submit" 
              :disabled="isSubmitting"
              class="w-full bg-linear-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-black py-3 px-6 rounded-2xl shadow-xl transition-all transform hover:scale-[1.01] active:scale-98 flex items-center justify-center gap-2 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer text-xs sm:text-sm"
            >
              <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
              <span>{{ isSubmitting ? 'Enviando mensaje...' : 'Enviar Consulta a Pollo Feliz' }}</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import api from '@/plugins/axios'
import company from '@/config/company'
import { useLandingContent } from '@/composables/useLandingContent'
import { useToasts } from '@/composables/useToasts'

const { addToast } = useToasts()
const { landingSettings, landingBranches } = useLandingContent()

const mapFrameWrapper = ref(null)
const mapLoaded = ref(false)
const googleMapEmbedUrl = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d116744.11887028168!2d-104.6538!3d24.0277!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x869bb7bf73775b03%3A0x6b6c22dfecbbfb0!2sVictoria%20de%20Durango%2C%20Dgo.!5e0!3m2!1ses!2smx!4v1700000000000'
let iframeObserver = null

onMounted(() => {
  if (typeof IntersectionObserver !== 'undefined' && mapFrameWrapper.value) {
    iframeObserver = new IntersectionObserver(
      (entries) => {
        if (entries.some((e) => e.isIntersecting)) {
          mapLoaded.value = true
          iframeObserver?.disconnect()
          iframeObserver = null
        }
      },
      { rootMargin: '250px' }
    )
    iframeObserver.observe(mapFrameWrapper.value)
  } else {
    mapLoaded.value = true
  }
})

onBeforeUnmount(() => {
  iframeObserver?.disconnect()
  iframeObserver = null
})

const availableBranches = computed(() =>
  (landingBranches.value || []).filter((b) => b && b.name)
)

const form = ref({
  name: '',
  email: '',
  phone: '',
  subject: 'Consulta general',
  branch: 'Cualquier sucursal',
  message: '',
  website: '',
})

const isSubmitting = ref(false)
const submittedSuccess = ref(false)
const feedbackMessage = ref('')
const feedbackError = ref('')
const confirmationFolio = ref('')

const landingAddress = computed(() => landingSettings.value.company_address || company.address || 'Av. 20 de Noviembre #804, Durango, Dgo.')
const landingPhone = computed(() => landingSettings.value.company_phone || company.phone || '618-123-4567')
const landingEmail = computed(() => landingSettings.value.company_email || company.email || 'soporte@pollofelizdgo.com')

const whatsAppDirectUrl = computed(() => {
  const cleanPhone = (landingPhone.value || '6181233694').replace(/\D/g, '')
  const text = encodeURIComponent('¡Hola Pollo Feliz! Quisiera información y cotización.')
  return `https://wa.me/52${cleanPhone}?text=${text}`
})

const validateForm = () => {
  const name = form.value.name.trim()
  const email = form.value.email.trim()
  const phone = form.value.phone.trim()
  const message = form.value.message.trim()

  if (!name) return 'Por favor escribe tu nombre completo.'
  if (!phone || phone.length < 7) return 'Por favor ingresa un número telefónico válido (mínimo 7 dígitos).'
  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return 'Por favor ingresa un correo electrónico válido.'
  if (!message || message.length < 5) return 'Por favor escribe un mensaje de al menos 5 caracteres.'

  return ''
}

const handleSubmit = async () => {
  feedbackError.value = ''
  feedbackMessage.value = ''

  const validationError = validateForm()
  if (validationError) {
    feedbackError.value = validationError
    return
  }

  isSubmitting.value = true

  try {
    const payload = {
      name: form.value.name.trim(),
      email: form.value.email.trim(),
      phone: form.value.phone.trim(),
      subject: form.value.subject,
      branch: form.value.branch,
      message: form.value.message.trim(),
      website: form.value.website,
      destination_email: 'soporte@pollofelizdgo.com',
    }

    const response = await api.post('/feedback', payload)
    
    confirmationFolio.value = response.data?.folio || `TKT-${Math.floor(1000 + Math.random() * 9000)}`
    feedbackMessage.value = response.data?.message || '¡Gracias! Tu consulta ha sido enviada a soporte@pollofelizdgo.com y te contactaremos en breve.'
    submittedSuccess.value = true
    addToast('¡Mensaje enviado a soporte@pollofelizdgo.com!', 'success')
  } catch (error) {
    console.error('Error submitting contact form:', error)
    feedbackError.value = error.response?.data?.message || 'Ocurrió un error al enviar tu mensaje. Intenta nuevamente o usa WhatsApp.'
    addToast('No se pudo enviar el mensaje', 'error')
  } finally {
    isSubmitting.value = false
  }
}

const resetForm = () => {
  form.value = {
    name: '',
    email: '',
    phone: '',
    subject: 'Consulta general',
    branch: 'Cualquier sucursal',
    message: '',
    website: '',
  }
  submittedSuccess.value = false
  feedbackError.value = ''
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>