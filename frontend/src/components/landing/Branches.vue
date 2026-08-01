<template>
  <section id="branches" class="py-16 bg-white dark:bg-gray-950">
    <div class="container mx-auto px-4">
      <!-- Encabezado -->
      <div class="text-center mb-12" data-aos="fade-up">
        <span class="inline-block bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-full mb-4">
          📍 Encuéntranos
        </span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 dark:text-gray-100">
          Nuestras <span class="text-red-600">Sucursales</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto text-sm md:text-base dark:text-gray-300">
          Visítanos en cualquiera de nuestras sucursales. ¡Te esperamos con el mejor pollo asado!
        </p>
      </div>

      <!-- Grid de sucursales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <BranchCard 
          v-for="branch in branches" 
          :key="branch.id" 
          :branch="branch"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import BranchCard from './BranchCard.vue' // Importación relativa porque están en la misma carpeta
import { useLandingContent } from '@/composables/useLandingContent'
import branchesData from '@/config/branches'

const { landingBranches } = useLandingContent()

const normalizeBranch = (branch, index) => ({
  id: branch.id ?? index,
  name: branch.name || 'Sucursal',
  address: branch.address || '',
  phone: branch.phone || '',
  schedule: branch.schedule || '',
  image: branch.image_url || branch.image || '',
  mapLink: branch.map_link || branch.mapLink || '',
  isOpen: branch.is_open ?? branch.isOpen ?? true,
})

const branches = computed(() => {
  const currentBranches = Array.isArray(landingBranches.value) ? landingBranches.value : []
  if (currentBranches.length) {
    return currentBranches.map((branch, index) => normalizeBranch(branch, index))
  }

  const fallbackBranches = Array.isArray(branchesData) ? branchesData : (branchesData.active || [])
  return fallbackBranches.map((branch, index) => normalizeBranch(branch, index))
})
</script>