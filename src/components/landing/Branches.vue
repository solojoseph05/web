<template>
  <section id="branches" class="py-16 scroll-mt-24 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6">
      <!-- Section Title -->
      <div class="text-center mb-12" data-aos="fade-up">
        <span class="inline-block bg-red-100 text-red-600 text-xs sm:text-sm font-semibold px-4 py-1.5 rounded-full mb-3 dark:bg-red-950/60 dark:text-red-300">
          📍 Cobertura y Puntos de Venta
        </span>
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white">
          {{ branchesTitle }}
        </h2>
        <p class="mt-3 text-sm sm:text-base text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
          {{ branchesSubtitle }}
        </p>
      </div>

      <!-- Grid of Branches -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <BranchCard
          v-for="branch in branchList"
          :key="branch.id"
          :branch="branch"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import BranchCard from '@/components/landing/BranchCard.vue'
import branchesConfig from '@/config/branches'
import { useLandingContent } from '@/composables/useLandingContent'

const { landingBranches, landingSettings } = useLandingContent()

const branchesTitle = computed(() => landingSettings.value.branches_section_title || 'Nuestras Sucursales')
const branchesSubtitle = computed(() => landingSettings.value.branches_section_subtitle || 'Visítanos o llámanos directamente para disfrutar del mejor sabor al carbón recién salido de la parrilla.')

const branchList = computed(() => {
  if (landingBranches.value && landingBranches.value.length > 0) {
    return landingBranches.value
  }
  return branchesConfig.active || []
})
</script>
