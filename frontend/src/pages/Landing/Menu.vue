<!-- src/pages/Landing/Menu.vue -->
<template>
  <div class="min-h-screen">
    <!-- Encabezado de la página -->
    <div class="bg-linear-to-br from-red-50 to-orange-50 pt-24 pb-12 text-center">
      <div class="container mx-auto px-4" data-aos="fade-up">
        <span class="inline-block bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-full mb-4">
          🍗 Sabores únicos
        </span>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
          Nuestro <span class="text-red-600">Menú</span>
        </h1>
        <p class="text-gray-600 max-w-2xl mx-auto text-sm md:text-base">
          Descubre la variedad de platillos que tenemos para ti. Todos preparados con el mejor pollo asado y los ingredientes más frescos.
        </p>
        <div class="mt-8 flex justify-center">
          <div class="inline-flex items-center gap-3 rounded-full bg-white/90 border border-red-100 px-5 py-3 shadow-lg shadow-red-100/50 text-sm text-gray-700">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-red-600 text-white">30+</span>
            <span>Más de 30 productos listos para elegir</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Contenido del Menú completo -->
    <div class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 text-center" data-aos="fade-up">
          Menú completo
        </h2>
        <FeaturedMenu :display-limit="30" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'

// Importamos los componentes necesarios
import FeaturedMenu from '@/components/landing/FeaturedMenu.vue'

// SEO de la página de Menú
useHead({
  title: 'Menú | Pollo Feliz',
  meta: [
    { name: 'description', content: 'Explora nuestro delicioso menú de pollo asado. Conoce nuestras especialidades, combos y precios increíbles.' }
  ]
})
</script>