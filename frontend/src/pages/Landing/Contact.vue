<!-- src/pages/Landing/Contact.vue -->
<template>
  <div class="min-h-screen">
    <!-- Encabezado -->
    <div class="bg-linear-to-br from-red-50 to-orange-50 pt-24 pb-12 text-center">
      <div class="container mx-auto px-4" data-aos="fade-up">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
          <span class="text-red-600">Contáctanos</span>
        </h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
          ¿Tienes alguna duda, sugerencia o quieres hacer un pedido especial? Escríbenos.
        </p>
      </div>
    </div>

    <!-- Reutilizamos el componente Contact que creamos antes -->
    <div class="py-16 bg-white">
      <Contact />
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import Contact from '@/components/landing/Contact.vue'

// SEO de la página de Contacto
useHead({
  title: 'Contacto | Pollo Feliz',
  meta: [
    { name: 'description', content: 'Comunícate con nosotros por WhatsApp o visítanos en nuestras sucursales. Estamos aquí para atenderte.' }
  ]
})
</script>