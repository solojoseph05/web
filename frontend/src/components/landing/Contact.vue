<!-- src/components/landing/Contact.vue -->
<template>
  <section id="contact" class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4">
      <!-- Encabezado -->
      <div class="mx-auto mb-12 max-w-3xl text-center" data-aos="fade-up">
        <span class="inline-block bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-full mb-4">
          💬 Contáctanos
        </span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 dark:text-gray-100">
          ¿Tienes alguna <span class="text-red-600">pregunta?</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto text-sm md:text-base dark:text-gray-300">
          Escríbenos o visítanos. Estamos para servirte y resolver todas tus dudas.
        </p>
      </div>

      <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-10">
        <!-- COLUMNA IZQUIERDA: Información y Mapa -->
        <div class="space-y-6" data-aos="fade-right">
          <div class="rounded-3xl bg-white p-6 shadow-lg sm:p-8 dark:bg-gray-800 dark:border dark:border-gray-700">
            <h3 class="mb-4 text-xl font-bold text-gray-800 dark:text-gray-100">Información de contacto</h3>
            <div class="space-y-4 text-gray-600 dark:text-gray-300">
              <div class="flex items-start gap-4">
                <div class="rounded-full bg-red-100 p-3 text-red-600 dark:bg-red-900/40 dark:text-red-300">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                  <span class="block font-bold">Dirección</span>
                  {{ landingAddress }}
                </div>
              </div>
              <div class="flex items-start gap-4">
                <div class="rounded-full bg-red-100 p-3 text-red-600 dark:bg-red-900/40 dark:text-red-300">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <div>
                  <span class="block font-bold">Teléfono</span>
                  <a :href="`tel:${company.phone}`" class="transition-colors hover:text-red-600">{{ company.phone }}</a>
                </div>
              </div>
              <div class="flex items-start gap-4">
                <div class="rounded-full bg-red-100 p-3 text-red-600 dark:bg-red-900/40 dark:text-red-300">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                  <span class="block font-bold">Correo Electrónico</span>
                  <a :href="`mailto:${company.email}`" class="transition-colors hover:text-red-600">{{ company.email }}</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Mini Mapa de Google (Placeholder embebido) -->
          <div class="h-64 overflow-hidden rounded-3xl bg-white shadow-lg dark:bg-gray-800 dark:border dark:border-gray-700">
            <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15051.11887028168!2d-99.1332!3d19.4326!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1f92b4b1f1f1f%3A0x8f1f1f1f1f1f1f1f!2sZ%C3%B3calo%20de%20la%20Ciudad%20de%20M%C3%A9xico!5e0!3m2!1ses!2smx!4v1700000000000"
              width="100%" 
              height="100%" 
              style="border:0;" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>

        <!-- COLUMNA DERECHA: Formulario -->
        <div class="rounded-3xl bg-white p-6 shadow-lg sm:p-8 dark:bg-gray-800 dark:border dark:border-gray-700" data-aos="fade-left">
          <h3 class="mb-6 text-xl font-bold text-gray-800 dark:text-gray-100">Envíanos un mensaje</h3>

          <!-- El formulario captura el mensaje del usuario y lo envía al flujo de contacto.
               Si WhatsApp no puede abrirse, el respaldo pasa por el backend para persistir y notificar. -->
          <form @submit.prevent="handleSubmit" class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-200">Nombre completo</label>
              <input 
                v-model="form.name"
                type="text" 
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                placeholder="Tu nombre"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-200">Correo electrónico</label>
              <input 
                v-model="form.email"
                type="email" 
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                placeholder="tucorreo@ejemplo.com"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-200">Mensaje</label>
              <textarea 
                v-model="form.message"
                rows="4" 
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none"
                placeholder="Escribe tu consulta aquí..."
              ></textarea>
            </div>

            <button 
              type="submit" 
              :disabled="isSubmitting"
              class="w-full bg-red-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-red-700 transition-all transform hover:scale-[1.02] shadow-lg flex items-center justify-center gap-2 disabled:cursor-not-allowed disabled:bg-red-400"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
              {{ isSubmitting ? 'Enviando...' : 'Enviar mensaje' }}
            </button>

            <p v-if="feedbackMessage" class="rounded-2xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
              {{ feedbackMessage }}
            </p>
            <p v-if="feedbackError" class="rounded-2xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
              {{ feedbackError }}
            </p>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue'
import api from '@/plugins/axios'
import company from '@/config/company'
import { useLandingContent } from '@/composables/useLandingContent'

const form = ref({
  name: '',
  email: '',
  message: ''
})

const isSubmitting = ref(false)
const feedbackMessage = ref('')
const feedbackError = ref('')
const { landingSettings } = useLandingContent()
const landingAddress = computed(() => landingSettings.value.company_address || company.address)

// El número de WhatsApp se toma de variables de entorno; si no existe, cae al valor de la empresa.
// Cambiar este valor afecta el flujo de contacto directo por WhatsApp.
const phone = import.meta.env.VITE_WHATSAPP_NUMBER || import.meta.env.VITE_WHATSAPP || company.phone || '6181233694'

const validateForm = () => {
  const trimmedName = form.value.name.trim()
  const trimmedEmail = form.value.email.trim()
  const trimmedMessage = form.value.message.trim()

  if (!trimmedName) {
    return 'Por favor, escribe tu nombre.'
  }

  if (!trimmedEmail) {
    return 'Por favor, escribe tu correo electrónico.'
  }

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedEmail)) {
    return 'Por favor, ingresa un correo electrónico válido.'
  }

  if (!trimmedMessage) {
    return 'Por favor, escribe un mensaje.'
  }

  return ''
}

const sendByEmailFallback = async () => {
  const response = await api.post('/feedback', {
    name: form.value.name.trim(),
    email: form.value.email.trim(),
    message: form.value.message.trim(),
    phone,
    source: 'landing_contact',
    rating: '5 estrellas',
    type: 'Contacto'
  })

  return response.data
}

// El flujo de envío intenta abrir WhatsApp y, en caso de que eso falle, usa el backend como respaldo.
// Si cambias el endpoint o la estructura del payload, el backend también debe adaptarse.
const handleSubmit = async () => {
  feedbackMessage.value = ''
  feedbackError.value = ''
  isSubmitting.value = true

  try {
    const validationError = validateForm()
    if (validationError) {
      feedbackError.value = validationError
      return
    }

    const message = `Hola, soy ${form.value.name.trim()} (${form.value.email.trim()}).\n\n${form.value.message.trim()}`
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`
    const popup = window.open(url, '_blank', 'noopener,noreferrer')

    if (!popup) {
      const response = await sendByEmailFallback()
      feedbackMessage.value = response.message || 'No se pudo abrir WhatsApp automáticamente. Tu mensaje se envió por correo como respaldo.'
      form.value = { name: '', email: '', message: '' }
      return
    }

    feedbackMessage.value = 'Se abrió WhatsApp para continuar la conversación.'
    form.value = { name: '', email: '', message: '' }
  } catch (error) {
    const fallbackMessage = error?.userMessage || error?.message || 'No se pudo abrir WhatsApp automáticamente. Intenta de nuevo.'
    feedbackError.value = fallbackMessage
  } finally {
    isSubmitting.value = false
  }
}
</script>