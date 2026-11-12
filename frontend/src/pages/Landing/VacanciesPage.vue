<!-- src/pages/Landing/VacanciesPage.vue -->
<template>
  <main class="min-h-screen bg-white dark:bg-gray-950 pt-20">
    <Vacancies />
  </main>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import Vacancies from '@/components/landing/Vacancies.vue'

useHead({
  title: 'Bolsa de Trabajo & Vacantes | Pollo Feliz Durango',
  meta: [
    {
      name: 'description',
      content: 'Consulta las vacantes abiertas en Pollo Feliz Durango. ¡Únete a nuestro equipo con sueldos competitivos, prestaciones y excelente ambiente laboral!',
    },
  ],
})
</script>
