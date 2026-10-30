<!-- src/pages/Landing/Home.vue -->
<template>
  <main class="min-h-screen bg-white dark:bg-gray-950 overflow-x-hidden">
    <!-- Hero Component -->
    <Hero />

    <!-- Featured Menu Component -->
    <FeaturedMenu />

    <!-- Promotions Section Component -->
    <Promotions />

    <!-- Branches Section Component -->
    <Branches />

    <!-- Customer Reviews Section Component -->
    <Reviews />

    <!-- Contact & Feedback Section Component -->
    <Contact />
  </main>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import Hero from '@/components/landing/Hero.vue'
import FeaturedMenu from '@/components/landing/FeaturedMenu.vue'
import Promotions from '@/components/landing/Promotions.vue'
import Branches from '@/components/landing/Branches.vue'
import Reviews from '@/components/landing/Reviews.vue'
import Contact from '@/components/landing/Contact.vue'

useHead({
  title: 'Pollo Feliz | El Mejor Pollo Asado al Carbón',
  meta: [
    {
      name: 'description',
      content: 'Disfruta del mejor pollo asado al carbón en Durango. Receta tradicional, paquetes familiares, servicio a domicilio y entrega rápida.',
    },
    { property: 'og:title', content: 'Pollo Feliz | El Mejor Pollo Asado al Carbón' },
    {
      property: 'og:description',
      content: 'Pollo asado al carbón de mezquite con receta secreta, complementos y salsas caseras.',
    },
  ],
})
</script>
