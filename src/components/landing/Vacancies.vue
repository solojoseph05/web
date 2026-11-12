<!-- src/components/landing/Vacancies.vue -->
<template>
  <section id="vacantes" class="py-16 md:py-24 bg-linear-to-b from-amber-50/40 via-white to-gray-50 dark:from-gray-900/60 dark:via-gray-950 dark:to-gray-900 border-t border-gray-100 dark:border-gray-800 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-14" data-aos="fade-up">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 mb-3 border border-amber-200 dark:border-amber-800">
          <span>💼</span>
          <span>Bolsa de Trabajo Activa</span>
        </span>
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 dark:text-white tracking-tight">
          ¡Únete a la Gran Familia <span class="text-red-600 dark:text-red-500">Pollo Feliz</span>!
        </h2>
        <p class="mt-4 text-sm sm:text-base text-gray-600 dark:text-gray-300">
          Crecemos juntos con pasión por la tradición, el mejor pollo asado al carbón de Durango y un ambiente de trabajo estable, cálido y lleno de oportunidades.
        </p>
      </div>

      <!-- Perks Banner -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-12 max-w-4xl mx-auto">
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800/80 border border-gray-100 dark:border-gray-700 shadow-xs text-center transition-all hover:shadow-md hover:-translate-y-0.5">
          <span class="text-2xl block mb-1">💰</span>
          <h4 class="text-xs font-black text-gray-900 dark:text-white">Sueldo Competitivo</h4>
          <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">+ Propinas íntegras</p>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800/80 border border-gray-100 dark:border-gray-700 shadow-xs text-center transition-all hover:shadow-md hover:-translate-y-0.5">
          <span class="text-2xl block mb-1">🛡️</span>
          <h4 class="text-xs font-black text-gray-900 dark:text-white">Prestaciones de Ley</h4>
          <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">IMSS, aguinaldo y vacaciones</p>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800/80 border border-gray-100 dark:border-gray-700 shadow-xs text-center transition-all hover:shadow-md hover:-translate-y-0.5">
          <span class="text-2xl block mb-1">🍗</span>
          <h4 class="text-xs font-black text-gray-900 dark:text-white">Comida Incluida</h4>
          <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Alimentos durante tu turno</p>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800/80 border border-gray-100 dark:border-gray-700 shadow-xs text-center transition-all hover:shadow-md hover:-translate-y-0.5">
          <span class="text-2xl block mb-1">🚀</span>
          <h4 class="text-xs font-black text-gray-900 dark:text-white">Plan de Crecimiento</h4>
          <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Capacitación y desarrollo</p>
        </div>
      </div>

      <!-- Vacancies Grid (Active Jobs with Image Cards) -->
      <div v-if="activeJobs.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        <div
          v-for="job in activeJobs"
          :key="job.id"
          class="group flex flex-col justify-between rounded-3xl bg-white dark:bg-gray-800/90 border border-gray-200/90 dark:border-gray-700/80 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 hover:-translate-y-1.5"
        >
          <div>
            <!-- Card Top Image Header -->
            <div class="relative h-48 sm:h-52 w-full overflow-hidden bg-gray-900">
              <img
                v-if="job.image_url"
                :src="job.image_url"
                :alt="job.title"
                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                loading="lazy"
                @error="(e) => e.target.style.display = 'none'"
              />
              <!-- Branded Fallback Pattern when no custom photo is provided -->
              <div v-else class="h-full w-full bg-linear-to-br from-red-700 via-amber-700 to-amber-900 flex flex-col items-center justify-center p-6 text-white text-center">
                <span class="text-4xl block mb-2 group-hover:scale-110 transition-transform">🍗</span>
                <span class="text-xs font-black uppercase tracking-wider text-amber-200">Pollo Feliz Durango</span>
                <span class="text-sm font-bold text-white/90">Oportunidad Laboral</span>
              </div>

              <!-- Gradient overlay for text contrast -->
              <div class="absolute inset-0 bg-linear-to-t from-black/75 via-black/25 to-transparent pointer-events-none"></div>

              <!-- Top floating badges -->
              <div class="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between gap-2 pointer-events-none">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-black/60 backdrop-blur-md text-amber-300 border border-amber-300/30 shadow-xs">
                  <span>💼</span>
                  <span>{{ job.employment_type || 'Tiempo completo' }}</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-emerald-600/90 backdrop-blur-md text-white border border-emerald-400 shadow-xs">
                  <span class="h-2 w-2 rounded-full bg-white animate-pulse"></span>
                  <span>Abierta</span>
                </span>
              </div>

              <!-- Bottom Title on Image for Rich Card Presence -->
              <div class="absolute bottom-3 left-4 right-4 pointer-events-none">
                <h3 class="text-lg sm:text-xl font-black text-white leading-tight drop-shadow-md group-hover:text-amber-300 transition-colors">
                  {{ job.title }}
                </h3>
              </div>
            </div>

            <!-- Card Body Info -->
            <div class="p-5 sm:p-6 space-y-3.5">
              <!-- Location & Schedule Metadata -->
              <div class="space-y-1.5 text-xs text-gray-600 dark:text-gray-300">
                <div class="flex items-center gap-2">
                  <span class="text-base text-red-600 dark:text-red-400 shrink-0">📍</span>
                  <span class="font-bold text-gray-800 dark:text-gray-200">{{ job.location || 'Sucursal Centro Histórico' }}</span>
                </div>
                <div v-if="job.schedule" class="flex items-center gap-2">
                  <span class="text-base text-amber-500 shrink-0">⏰</span>
                  <span class="text-gray-600 dark:text-gray-300">{{ job.schedule }}</span>
                </div>
              </div>

              <!-- Description -->
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed line-clamp-3">
                {{ job.description }}
              </p>

              <!-- Quick Perks Chips -->
              <div class="flex flex-wrap gap-1.5 pt-1">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60">
                  ✓ Seguro & IMSS
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-red-50 text-red-800 dark:bg-red-950/60 dark:text-red-300 border border-red-200/60 dark:border-red-800/60">
                  ✓ Alimentos diarios
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                  ✓ Pago puntual
                </span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="p-5 sm:p-6 pt-0 space-y-2">
            <div class="pt-3 border-t border-gray-100 dark:border-gray-700/80 flex flex-col sm:flex-row gap-2">
              <button
                type="button"
                @click="openJobDetails(job)"
                class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-bold bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition cursor-pointer"
              >
                <span>🔍 Requisitos</span>
              </button>
              <button
                type="button"
                @click="openApplyModal(job)"
                class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-bold bg-linear-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white shadow-xs transition cursor-pointer"
              >
                <span>📝 Postularme</span>
              </button>
              <a
                :href="getWhatsAppApplicationLink(job)"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center p-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition"
                title="Postularme por WhatsApp"
              >
                <span>💬</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State (No active vacancies) -->
      <div
        v-else
        class="text-center py-12 px-6 rounded-3xl bg-white dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 max-w-2xl mx-auto shadow-sm"
      >
        <span class="text-5xl block mb-3">📬</span>
        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2">
          ¡Siempre estamos abiertos a nuevo talento!
        </h3>
        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mb-6 max-w-md mx-auto">
          En este momento todas nuestras posiciones operativas están cubiertas, pero recibimos solicitudes constantemente para nuestras próximas aperturas y temporadas.
        </p>
        <a
          href="https://wa.me/526181234567?text=Hola%20Pollo%20Feliz,%20me%20gustar%C3%ADa%20dejar%20mi%20curr%C3%ADculum%20para%20futuras%20vacantes"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold bg-red-600 hover:bg-red-700 text-white shadow-md transition"
        >
          <span>Dejar mi Currículum por WhatsApp</span>
        </a>
      </div>
    </div>

    <!-- Job Details & Application Modal -->
    <Teleport to="body">
      <div
        v-if="selectedJob"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
        @click.self="selectedJob = null"
      >
        <div
          class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-100 dark:border-gray-800 my-6 max-h-[92vh] flex flex-col overflow-hidden"
          role="dialog"
          aria-modal="true"
        >
          <!-- Modal Top Cover Image -->
          <div class="relative h-44 sm:h-52 w-full bg-gray-900 overflow-hidden shrink-0">
            <img
              v-if="selectedJob.image_url"
              :src="selectedJob.image_url"
              :alt="selectedJob.title"
              class="h-full w-full object-cover"
            />
            <div v-else class="h-full w-full bg-linear-to-br from-red-600 to-amber-700 flex items-center justify-center">
              <span class="text-5xl">🍗</span>
            </div>
            <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/30 to-transparent"></div>

            <!-- Close Button -->
            <button
              @click="selectedJob = null"
              class="absolute top-3.5 right-3.5 rounded-full p-2 bg-black/50 text-white hover:bg-black/70 cursor-pointer transition backdrop-blur-xs"
              aria-label="Cerrar modal"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            <!-- Title & Badges in Cover -->
            <div class="absolute bottom-3.5 left-4 right-4 text-white">
              <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-amber-950 mb-1.5">
                {{ selectedJob.employment_type || 'Tiempo completo' }}
              </span>
              <h3 class="text-xl sm:text-2xl font-black leading-tight drop-shadow-md">
                {{ selectedJob.title }}
              </h3>
            </div>
          </div>

          <!-- Modal Body Content -->
          <div class="p-6 space-y-4 overflow-y-auto flex-1">
            <!-- Location and Schedule Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 text-xs">
              <div>
                <span class="text-gray-400 block font-semibold mb-0.5">Ubicación asignada:</span>
                <span class="font-bold text-gray-900 dark:text-white flex items-center gap-1">
                  <span>📍</span> {{ selectedJob.location || 'Sucursal Centro Histórico' }}
                </span>
              </div>
              <div>
                <span class="text-gray-400 block font-semibold mb-0.5">Horario & Turno:</span>
                <span class="font-bold text-gray-900 dark:text-white flex items-center gap-1">
                  <span>⏰</span> {{ selectedJob.schedule || 'Turnos rotativos de ley' }}
                </span>
              </div>
            </div>

            <!-- Description -->
            <div>
              <h4 class="text-xs font-black uppercase tracking-wider text-gray-400 mb-2">
                Descripción & Actividades del Puesto
              </h4>
              <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                {{ selectedJob.description }}
              </p>
            </div>

            <!-- Benefits included -->
            <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-900/40">
              <h4 class="text-xs font-black text-amber-900 dark:text-amber-300 mb-2 flex items-center gap-1.5">
                <span>🎁</span> Beneficios de Pollo Feliz Durango
              </h4>
              <ul class="text-xs text-amber-950 dark:text-amber-200 space-y-1.5">
                <li class="flex items-center gap-2">✓ Sueldo semanal competitivo + propinas íntegras</li>
                <li class="flex items-center gap-2">✓ Seguro Social (IMSS), aguinaldo, vacaciones y prima vacacional</li>
                <li class="flex items-center gap-2">✓ Alimentos diarios durante tu turno laboral</li>
                <li class="flex items-center gap-2">✓ Uniformes oficiales gratuitos y capacitación continua</li>
                <li class="flex items-center gap-2">✓ Estabilidad laboral en empresa líder en Durango</li>
              </ul>
            </div>
          </div>

          <!-- Modal Action Buttons (Pinned Footer) -->
          <div class="p-4 sm:p-5 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 flex flex-col sm:flex-row gap-2.5 shrink-0">
            <button
              type="button"
              @click="openApplyModal(selectedJob); selectedJob = null"
              class="flex-1 inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold bg-linear-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white shadow-md transition cursor-pointer"
            >
              <span>📝 Llenar Solicitud en Línea</span>
            </button>
            <a
              :href="getWhatsAppApplicationLink(selectedJob)"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md transition"
            >
              <span>💬 WhatsApp</span>
            </a>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Public Application Modal -->
    <ApplicationModal
      :is-open="showApplyModal"
      :vacancy="jobToApply"
      @close="showApplyModal = false"
    />
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useLandingContent } from '@/composables/useLandingContent'
import ApplicationModal from '@/components/landing/ApplicationModal.vue'

const { landingJobs } = useLandingContent()
const selectedJob = ref(null)
const showApplyModal = ref(false)
const jobToApply = ref(null)

const activeJobs = computed(() => {
  return (landingJobs.value || []).filter((j) => j && j.is_active && !j.deleted_at)
})

const openJobDetails = (job) => {
  selectedJob.value = job
}

const openApplyModal = (job) => {
  jobToApply.value = job
  showApplyModal.value = true
}

const getWhatsAppApplicationLink = (job) => {
  const phone = '526181234567'
  const message = `Hola Pollo Feliz Durango, me interesa postularme para la vacante de "${job.title}" en la ${job.location || 'sucursal'}. ¿Podrían indicarme qué documentación requiero para mi entrevista? ¡Muchas gracias!`
  return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`
}

const getEmailApplicationLink = (job) => {
  const email = 'soporte@pollofelizdgo.com'
  const subject = `Postulación de Empleo: ${job.title} - ${job.location || 'Pollo Feliz Durango'}`
  const body = `Hola equipo de Recursos Humanos de Pollo Feliz Durango,\n\nMe pongo en contacto para postularme formalmente a la vacante de "${job.title}" en la ${job.location || 'sucursal'}.\n\nMis datos de contacto:\n- Nombre Completo:\n- Teléfono celular:\n- Sucursal de preferencia: ${job.location || 'Cualquiera'}\n- Experiencia laboral previa:\n\nQuedo a su disposición para acordar una entrevista.\n\n¡Muchas gracias!`
  return `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`
}
</script>
