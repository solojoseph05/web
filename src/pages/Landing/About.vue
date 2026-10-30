<!-- src/pages/Landing/About.vue -->
<template>
  <div class="min-h-screen bg-gray-50 pt-24 pb-16 dark:bg-gray-950">
    <div class="bg-linear-to-b from-red-600 to-red-700 py-12 text-white shadow-inner">
      <div class="container mx-auto px-4 text-center">
        <span class="inline-block rounded-full bg-white/20 px-4 py-1.5 text-xs font-semibold backdrop-blur-xs mb-3">
          ❤️ Tradición Familiar Desde 2010
        </span>
        <h1 class="text-3xl md:text-5xl font-black tracking-tight">
          Sobre Pollo Feliz
        </h1>
        <p class="mt-3 text-sm md:text-base text-red-100 max-w-2xl mx-auto">
          La pasión por la parrilla, el marinado artesanal y la calidez mexicana que une a las familias duranguenses.
        </p>
      </div>
    </div>

    <!-- About Components -->
    <div class="py-8">
      <About />
      <WhyUs />
    </div>

    <div class="container mx-auto px-4 text-center mt-8">
      <router-link
        to="/vision-mision"
        class="inline-flex items-center gap-2 rounded-full bg-red-600 px-8 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-red-700"
      >
        <span>Conoce nuestra Visión y Misión</span>
        <span>→</span>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import About from '@/components/landing/About.vue'
import WhyUs from '@/components/landing/WhyUs.vue'

useHead({
  title: 'Nosotros | Pollo Feliz',
  meta: [
    { name: 'description', content: 'Conoce la historia, valores y tradición de Pollo Feliz. El mejor pollo asado a las brasas.' }
  ]
})
</script>
