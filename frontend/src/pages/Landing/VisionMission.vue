<!-- src/pages/Landing/VisionMission.vue -->
<template>
  <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(239,68,68,0.14),_transparent_32%),linear-gradient(135deg,_#fff7ed_0%,_#ffffff_45%,_#fff5f5_100%)] pt-24 pb-16">
    <div class="container mx-auto px-4">
      <div class="mx-auto max-w-6xl overflow-hidden rounded-[2rem] border border-red-100 bg-white shadow-[0_30px_80px_-20px_rgba(239,68,68,0.28)]">
        <div class="relative overflow-hidden bg-red-600 px-8 py-12 text-white md:px-12">
          <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10 blur-3xl"></div>
          <div class="absolute -bottom-8 -left-8 h-36 w-36 rounded-full bg-orange-300/20 blur-3xl"></div>
          <div class="relative">
            <span class="inline-flex items-center rounded-full bg-white/20 px-4 py-2 text-sm font-semibold backdrop-blur-sm">
              🎯 Nuestra identidad
            </span>
            <h1 class="mt-5 text-3xl font-bold md:text-5xl">
              Visión y <span class="text-red-100">Misión</span>
            </h1>
            <p class="mt-4 max-w-3xl text-base text-red-50 md:text-lg">
              Nos guiamos por una idea sencilla: ofrecer una experiencia auténtica, deliciosa y cercana en cada visita.
            </p>
          </div>
        </div>

        <div class="border-t border-red-100 bg-gradient-to-br from-white to-red-50/70 p-8 md:p-10">
          <div class="mb-8">
            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-600">
              🍗 Historia de Pollo Feliz
            </span>
            <h2 class="mt-4 text-3xl font-bold text-gray-800">Desde la familia hasta tu mesa</h2>
            <p class="mt-4 text-lg leading-relaxed text-gray-600">
              Pollo Feliz nació de la idea de compartir un sabor auténtico, preparado con cariño y tradición familiar.
              Cada etapa de nuestra historia nos ha acercado más a las personas que hoy forman parte de esta familia.
            </p>
          </div>

          <div class="relative">
            <div class="absolute left-4 top-0 h-full w-0.5 bg-gradient-to-b from-red-400 via-orange-300 to-transparent md:left-1/2 md:-translate-x-1/2"></div>
            <div class="space-y-8">
              <div v-for="(item, index) in timeline" :key="item.year" class="relative flex flex-col md:flex-row md:items-start">
                <div class="absolute left-4 top-2 h-6 w-6 rounded-full border-4 border-white bg-red-500 shadow-md md:left-1/2 md:-translate-x-1/2"></div>
                <div class="ml-12 rounded-[1.25rem] border border-red-100 bg-white p-5 shadow-sm md:ml-0 md:w-[calc(50%-1.5rem)]"
                     :class="index % 2 === 0 ? 'md:mr-auto md:pr-8' : 'md:ml-auto md:pl-8'">
                  <p class="text-sm font-semibold uppercase tracking-[0.2em] text-red-500">{{ item.year }}</p>
                  <h3 class="mt-2 text-xl font-bold text-gray-800">{{ item.title }}</h3>
                  <p class="mt-2 text-base leading-relaxed text-gray-600">{{ item.description }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid gap-0 border-t border-red-100 md:grid-cols-2">
          <div class="border-b border-red-100 bg-gradient-to-br from-white to-red-50/70 p-8 md:border-b-0 md:border-r md:p-10">
            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 text-red-600 shadow-sm">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h2 class="mt-6 text-2xl font-bold text-gray-800">Misión</h2>
            <p class="mt-4 text-lg leading-relaxed text-gray-600">
              Brindar a nuestros clientes pollo asado de la más alta calidad, preparado con ingredientes frescos,
              un sabor auténtico y un servicio cercano que haga de cada pedido una experiencia memorable.
            </p>
            <div class="mt-6 rounded-2xl border border-red-100 bg-white/80 p-4 text-sm text-gray-600 shadow-sm">
              <span class="font-semibold text-gray-800">En cada visita</span> buscamos que la comida y la atención se sientan como una tradición compartida.
            </div>
          </div>

          <div class="bg-gradient-to-br from-orange-50 to-white p-8 md:p-10">
            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-100 text-orange-600 shadow-sm">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21l7-7 4 4 7-7" />
              </svg>
            </div>
            <h2 class="mt-6 text-2xl font-bold text-gray-800">Visión</h2>
            <p class="mt-4 text-lg leading-relaxed text-gray-600">
              Ser la marca de pollo asado más querida en la región, reconocida por su sabor, tradición y la alegría que
              transmitimos en cada plato y en cada familia que servimos.
            </p>
            <div class="mt-6 rounded-2xl border border-orange-100 bg-white/80 p-4 text-sm text-gray-600 shadow-sm">
              <span class="font-semibold text-gray-800">Nuestro futuro</span> está en convertir cada pedido en una razón para volver.
            </div>
          </div>
        </div>

        <div class="border-t border-red-100 bg-gradient-to-br from-red-50 via-orange-50 to-white p-8 md:p-10">
          <div class="grid gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-start">
            <div class="rounded-[1.5rem] border border-red-100 bg-white/70 p-7 shadow-sm backdrop-blur">
              <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-600">
                ⭐ Califica a Pollo Feliz
              </span>
              <h2 class="mt-4 text-3xl font-bold text-gray-800">Tu opinión nos ayuda a mejorar</h2>
              <p class="mt-4 text-lg leading-relaxed text-gray-600">
                Nos importa saber qué te gustó, qué podríamos mejorar y cómo podemos ofrecerte una mejor experiencia cada vez.
              </p>
              <div class="mt-6 space-y-3 text-sm text-gray-600">
                <div class="flex items-center gap-3 rounded-2xl bg-white p-3 shadow-sm">
                  <span class="text-xl">👍</span>
                  <span>Comparte tu experiencia y calificación.</span>
                </div>
                <div class="flex items-center gap-3 rounded-2xl bg-white p-3 shadow-sm">
                  <span class="text-xl">💬</span>
                  <span>Envíanos sugerencias, quejas o comentarios.</span>
                </div>
                <div class="flex items-center gap-3 rounded-2xl bg-white p-3 shadow-sm">
                  <span class="text-xl">🌟</span>
                  <span>Tu voz nos ayuda a crecer como empresa.</span>
                </div>
              </div>
            </div>

            <form @submit.prevent="submitFeedback" class="rounded-[1.5rem] border border-red-100 bg-white p-6 shadow-[0_20px_60px_-20px_rgba(239,68,68,0.28)]">
              <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold text-gray-700">Nombre</label>
                <input v-model="form.name" type="text" required class="w-full rounded-2xl border border-gray-200 px-4 py-3 outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100" placeholder="Tu nombre" />
              </div>
              <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold text-gray-700">Correo</label>
                <input v-model="form.email" type="email" required class="w-full rounded-2xl border border-gray-200 px-4 py-3 outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100" placeholder="tu@email.com" />
              </div>
              <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold text-gray-700">Calificación</label>
                <select v-model="form.rating" class="w-full rounded-2xl border border-gray-200 px-4 py-3 outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                  <option value="5 estrellas">5 estrellas</option>
                  <option value="4 estrellas">4 estrellas</option>
                  <option value="3 estrellas">3 estrellas</option>
                  <option value="2 estrellas">2 estrellas</option>
                  <option value="1 estrella">1 estrella</option>
                </select>
              </div>
              <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold text-gray-700">Tipo de comentario</label>
                <select v-model="form.type" class="w-full rounded-2xl border border-gray-200 px-4 py-3 outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                  <option value="Sugerencia">Sugerencia</option>
                  <option value="Queja">Queja</option>
                  <option value="Felicitación">Felicitación</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
              <div class="mb-6">
                <label class="mb-2 block text-sm font-semibold text-gray-700">Mensaje</label>
                <textarea v-model="form.message" rows="4" required class="w-full rounded-2xl border border-gray-200 px-4 py-3 outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100" placeholder="Cuéntanos tu experiencia..."></textarea>
              </div>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="w-full rounded-full bg-red-600 px-5 py-3 font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-400"
              >
                {{ isSubmitting ? 'Enviando...' : 'Enviar comentario' }}
              </button>
              <p v-if="feedbackMessage" class="mt-4 rounded-2xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ feedbackMessage }}
              </p>
              <p v-if="feedbackError" class="mt-4 rounded-2xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ feedbackError }}
              </p>
            </form>
          </div>
        </div>
      </div>

      <div class="mt-8 flex justify-center">
        <router-link
          to="/"
          class="rounded-full bg-white px-7 py-3 font-semibold text-red-600 shadow-md transition hover:-translate-y-0.5 hover:bg-red-50"
        >
          Volver al inicio
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useHead } from '@vueuse/head'
import api from '@/plugins/axios'

const timeline = [
  {
    year: '2010',
    title: 'Nacemos con una idea familiar',
    description: 'Pollo Feliz comenzó como un sueño de ofrecer un pollo asado con sabor auténtico, preparado con ingredientes frescos y mucho cariño.'
  },
  {
    year: '2015',
    title: 'Crecemos con nuestra comunidad',
    description: 'Nuestra receta y servicio comenzaron a consolidarse gracias a la confianza de quienes nos visitaban y compartían su experiencia.'
  },
  {
    year: '2020',
    title: 'Nos adaptamos y mejoramos',
    description: 'Ampliamos nuestra propuesta para acompañar más momentos especiales, siempre cuidando la calidad y la atención cercana.'
  },
  {
    year: '2025',
    title: 'Seguimos construyendo la historia',
    description: 'Hoy seguimos creciendo como marca, con la misma esencia de familia, sabor y calidez que nos distinguió desde el inicio.'
  }
]

const form = ref({
  name: '',
  email: '',
  rating: '5 estrellas',
  type: 'Sugerencia',
  message: ''
})

const feedbackMessage = ref('')
const feedbackError = ref('')
const isSubmitting = ref(false)

const submitFeedback = async () => {
  feedbackMessage.value = ''
  feedbackError.value = ''
  isSubmitting.value = true

  try {
    const response = await api.post('/feedback', {
      ...form.value
    })

    feedbackMessage.value = response.data.message || `Gracias ${form.value.name || 'por tu comentario'}. Hemos recibido tu ${form.value.type.toLowerCase()} y la revisaremos pronto.`

    form.value = {
      name: '',
      email: '',
      rating: '5 estrellas',
      type: 'Sugerencia',
      message: ''
    }
  } catch (error) {
    feedbackError.value = error.response?.data?.message || 'No se pudo enviar el comentario. Intenta de nuevo.'
  } finally {
    isSubmitting.value = false
  }
}

useHead({
  title: 'Visión y Misión | Pollo Feliz',
  meta: [{ name: 'description', content: 'Conoce la visión y misión de Pollo Feliz.' }]
})
</script>
