<template>
  <section id="jobs" class="py-16 bg-gradient-to-br from-red-50 via-white to-orange-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4">
      <div class="mx-auto max-w-6xl overflow-hidden rounded-[2rem] border border-red-100 bg-white shadow-[0_30px_80px_-20px_rgba(239,68,68,0.2)] dark:border-gray-700 dark:bg-gray-900 dark:shadow-[0_30px_80px_-20px_rgba(0,0,0,0.6)]">
        <div class="grid lg:grid-cols-[0.95fr_1.05fr]">
          <div class="bg-red-600 px-6 py-8 text-white sm:px-8 md:px-10 md:py-10 lg:px-12">
            <span class="inline-flex items-center rounded-full bg-white/20 px-4 py-2 text-sm font-semibold backdrop-blur-sm">
              💼 Oportunidades laborales
            </span>
            <h2 class="mt-5 text-3xl font-bold leading-tight sm:text-4xl">
              Únete a <span class="text-red-100">Pollo Feliz</span>
            </h2>
            <p class="mt-4 text-base leading-relaxed text-red-50 md:text-lg">
              Postúlate a nuestras vacantes activas. Buscamos personas con actitud de servicio y ganas de crecer.
            </p>
          </div>

          <div class="bg-gradient-to-br from-white to-red-50/70 p-6 sm:p-8 md:p-10 lg:p-12 dark:from-gray-900 dark:to-gray-800">
            <div class="space-y-5">
              <div
                v-for="job in visibleJobs"
                :key="job.id"
                class="rounded-[1.25rem] border border-red-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
              >
                <img
                  v-if="job.image_url"
                  :src="job.image_url"
                  :alt="job.title"
                  class="mb-3 h-40 w-full rounded-xl object-cover"
                  width="640"
                  height="320"
                />
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                  <div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ job.title }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ job.description }}</p>
                    <p v-if="job.location || job.schedule" class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                      {{ job.location || 'Ubicación por definir' }} · {{ job.schedule || 'Horario por definir' }}
                    </p>
                  </div>
                  <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-600">{{ job.employment_type }}</span>
                </div>
              </div>
            </div>

            <a
              href="#contact"
              class="mt-8 inline-flex items-center justify-center rounded-full bg-red-600 px-5 py-3 font-semibold text-white transition hover:bg-red-700"
            >
              Interesado en postular
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { useLandingContent } from '@/composables/useLandingContent'

const { landingJobs } = useLandingContent()

const fallbackJobs = [
  {
    id: 'fallback-1',
    title: 'Cajero(a) / Atención al cliente',
    description: 'Experiencia deseable, actitud positiva y disposición para aprender.',
    employment_type: 'Tiempo completo',
    location: 'Sucursal Centro',
    schedule: 'Turno mixto',
  },
  {
    id: 'fallback-2',
    title: 'Ayudante de cocina',
    description: 'Responsable, ordenado(a) y con ganas de crecer dentro del equipo.',
    employment_type: 'Tiempo parcial',
    location: 'Sucursal Norte',
    schedule: 'Turno vespertino',
  },
]

const visibleJobs = computed(() => {
  const activeJobs = (landingJobs.value || []).filter((job) => job.is_active)
  if (activeJobs.length > 0) {
    return activeJobs
  }
  return fallbackJobs
})
</script>
