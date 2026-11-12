<!-- src/pages/Landing/VisionMission.vue -->
<template>
  <div class="min-h-screen bg-gray-50 pt-24 pb-16 dark:bg-gray-950">
    <!-- Header Banner -->
    <div class="bg-linear-to-b from-red-600 to-red-700 py-12 text-white shadow-inner">
      <div class="container mx-auto px-4 text-center">
        <span class="inline-block rounded-full bg-white/20 px-4 py-1.5 text-xs font-semibold backdrop-blur-xs mb-3">
          🌟 Nuestra Filosofía y Rumbo
        </span>
        <h1 class="text-3xl md:text-5xl font-black tracking-tight">
          Misión, Visión & Valores
        </h1>
        <p class="mt-3 text-sm md:text-base text-red-100 max-w-2xl mx-auto">
          El compromiso que guía nuestro fuego diario, la atención cercana y el cariño por cada cliente que nos visita.
        </p>
      </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 py-12 max-w-5xl">
      <!-- Misión & Visión Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Misión -->
        <div class="rounded-3xl bg-white p-6 sm:p-8 shadow-md border border-gray-100 dark:bg-gray-900 dark:border-gray-800 flex flex-col justify-between">
          <div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400 mb-6">
              <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Nuestra Misión</h2>
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-sm sm:text-base">
              Deleitar a las familias con el más exquisito pollo asado al carbón, empleando ingredientes frescos de la más alta calidad y un servicio cálido, rápido y honesto que convierte cada comida en una auténtica celebración hogareña.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">
            Compromiso con tu mesa
          </div>
        </div>

        <!-- Visión -->
        <div class="rounded-3xl bg-white p-6 sm:p-8 shadow-md border border-gray-100 dark:bg-gray-900 dark:border-gray-800 flex flex-col justify-between">
          <div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 mb-6">
              <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Nuestra Visión</h2>
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-sm sm:text-base">
              Consolidarnos como el restaurante de pollo asado de referencia en la región, reconocidos por nuestra inconfundible sazón artesanal, innovación en nuestros canales digitales y la continua satisfacción de nuestra comunidad.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">
            Mirando hacia el futuro
          </div>
        </div>
      </div>

      <!-- Core Values -->
      <div class="rounded-3xl bg-linear-to-br from-red-600 to-orange-600 p-8 sm:p-10 text-white shadow-xl mb-12">
        <h3 class="text-2xl sm:text-3xl font-extrabold text-center mb-8">Nuestros Valores Fundamentales</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="rounded-2xl bg-white/10 p-5 backdrop-blur-xs">
            <span class="text-2xl mb-2 block">🤝</span>
            <h4 class="font-bold text-lg mb-1">Calidez Humana</h4>
            <p class="text-xs text-red-100">Atención cordial que hace sentir a cada cliente como en su propia casa.</p>
          </div>
          <div class="rounded-2xl bg-white/10 p-5 backdrop-blur-xs">
            <span class="text-2xl mb-2 block">🛡️</span>
            <h4 class="font-bold text-lg mb-1">Calidad Total</h4>
            <p class="text-xs text-red-100">Cero atajos en higiene, frescura de aves e higiene de nuestras brasas.</p>
          </div>
          <div class="rounded-2xl bg-white/10 p-5 backdrop-blur-xs">
            <span class="text-2xl mb-2 block">⏱️</span>
            <h4 class="font-bold text-lg mb-1">Puntualidad</h4>
            <p class="text-xs text-red-100">Respetamos tu tiempo, despachando pedidos calientes sin demoras.</p>
          </div>
          <div class="rounded-2xl bg-white/10 p-5 backdrop-blur-xs">
            <span class="text-2xl mb-2 block">🇲🇽</span>
            <h4 class="font-bold text-lg mb-1">Tradición Viva</h4>
            <p class="text-xs text-red-100">Orgullo por el sabor mexicano de nuestras recetas y salsas de comal.</p>
          </div>
        </div>
      </div>

      <!-- Feedback / Buzón de sugerencias -->
      <div class="rounded-3xl bg-white p-6 sm:p-10 shadow-lg border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
        <div class="max-w-xl mx-auto text-center mb-8">
          <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-600 dark:bg-red-950/60 dark:text-red-300 mb-2">
            📬 Tu opinión cuenta
          </span>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Buzón de Opinión y Sugerencias</h3>
          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-2">
            ¿Cómo fue tu última experiencia con nosotros? Cuéntanos para seguir mejorando día a día.
          </p>
        </div>

        <form @submit.prevent="submitFeedback" class="max-w-xl mx-auto space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="Tu nombre completo"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Teléfono o WhatsApp</label>
              <input
                v-model="form.phone"
                type="tel"
                placeholder="(Opcional)"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Correo Electrónico</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="tu@correo.com"
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Mensaje o Sugerencia</label>
            <textarea
              v-model="form.message"
              rows="4"
              required
              placeholder="Escribe aquí tus comentarios o sugerencias..."
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            ></textarea>
          </div>

          <button
            type="submit"
            :disabled="isSubmitting"
            class="w-full flex items-center justify-center gap-2 rounded-xl bg-red-600 px-6 py-3 font-bold text-white shadow-md hover:bg-red-700 transition disabled:opacity-50 cursor-pointer min-h-[48px]"
          >
            <span v-if="!isSubmitting">Enviar Sugerencia</span>
            <span v-else>Enviando...</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useHead } from '@vueuse/head'
import api from '@/plugins/axios'
import { useToasts } from '@/composables/useToasts'

useHead({
  title: 'Misión y Visión | Pollo Feliz',
  meta: [
    { name: 'description', content: 'Conoce los valores, la misión y visión que rigen a Pollo Feliz.' }
  ]
})

const { addToast } = useToasts()
const isSubmitting = ref(false)

const form = reactive({
  name: '',
  phone: '',
  email: '',
  message: '',
})

const submitFeedback = async () => {
  if (!form.message.trim()) return
  isSubmitting.value = true
  try {
    await api.post('/feedback', {
      name: form.name,
      phone: form.phone,
      email: form.email,
      message: form.message,
    })
    addToast('¡Gracias por tus comentarios! Los hemos recibido con éxito.', 'success')
    form.name = ''
    form.phone = ''
    form.email = ''
    form.message = ''
  } catch (err) {
    console.error(err)
    addToast('Error al enviar tus comentarios. Inténtalo de nuevo.', 'error')
  } finally {
    isSubmitting.value = false
  }
}
</script>
