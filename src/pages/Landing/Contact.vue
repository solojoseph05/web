<!-- src/pages/Landing/Contact.vue -->
<template>
  <div class="min-h-screen bg-gray-50 pt-24 pb-16 dark:bg-gray-950">
    <div class="bg-linear-to-b from-red-600 to-red-700 py-12 text-white shadow-inner">
      <div class="container mx-auto px-4 text-center">
        <span class="inline-block rounded-full bg-white/20 px-4 py-1.5 text-xs font-semibold backdrop-blur-xs mb-3">
          📞 Atención Inmediata & Pedidos
        </span>
        <h1 class="text-3xl md:text-5xl font-black tracking-tight">
          Contacto & Sucursales
        </h1>
        <p class="mt-3 text-sm md:text-base text-red-100 max-w-2xl mx-auto">
          ¿Tienes dudas, deseas cotizar un evento o pedir a domicilio? Ponte en contacto con nosotros hoy mismo.
        </p>
      </div>
    </div>

    <!-- Contact Component -->
    <div class="py-8">
      <Contact />
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import Contact from '@/components/landing/Contact.vue'

useHead({
  title: 'Contacto | Pollo Feliz',
  meta: [
    { name: 'description', content: 'Contáctanos para pedidos a domicilio, eventos o sugerencias en Pollo Feliz.' }
  ]
})
</script>
