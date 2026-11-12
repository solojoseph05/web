<!-- src/pages/Landing/Branches.vue -->
<template>
  <div class="min-h-screen bg-gray-50 pt-24 pb-16 dark:bg-gray-950">
    <!-- Header Banner -->
    <div class="bg-linear-to-b from-red-600 to-red-700 py-12 text-white shadow-inner">
      <div class="container mx-auto px-4 text-center">
        <span class="inline-block rounded-full bg-white/20 px-4 py-1.5 text-xs font-semibold backdrop-blur-xs mb-3">
          📍 Encuentra tu Pollo Feliz más cercano
        </span>
        <h1 class="text-3xl md:text-5xl font-black tracking-tight">
          Nuestras Sucursales
        </h1>
        <p class="mt-3 text-sm md:text-base text-red-100 max-w-2xl mx-auto">
          Visítanos en cualquiera de nuestras ubicaciones para disfrutar del auténtico pollo asado al carbón recién hecho, o pide a domicilio.
        </p>
      </div>
    </div>

    <!-- Branches List Component -->
    <div class="py-12">
      <Branches />
    </div>

    <!-- Interactive Delivery CTA -->
    <div class="container mx-auto px-4 mt-6">
      <div class="rounded-3xl bg-linear-to-r from-orange-500 to-red-600 p-8 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
          <h3 class="text-2xl font-bold">¿Prefieres recibirlo en tu hogar?</h3>
          <p class="mt-1 text-sm text-red-100 max-w-lg">
            ¡Tenemos servicio a domicilio rápido y seguro en todas nuestras zonas de cobertura!
          </p>
        </div>
        <router-link
          to="/menu"
          class="rounded-full bg-white px-8 py-3 text-sm font-bold text-red-600 shadow-md transition hover:bg-gray-100 whitespace-nowrap"
        >
          Pedir a Domicilio
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import Branches from '@/components/landing/Branches.vue'

useHead({
  title: 'Sucursales | Pollo Feliz',
  meta: [
    { name: 'description', content: 'Ubicaciones, teléfonos y horarios de las sucursales de Pollo Feliz. Encuentra la más cercana a ti.' }
  ]
})
</script>
