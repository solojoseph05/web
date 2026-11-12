<!-- src/pages/Landing/Promotions.vue -->
<template>
  <div class="min-h-screen bg-gray-50 pt-24 pb-16 dark:bg-gray-950">
    <!-- Header Banner -->
    <div class="bg-linear-to-b from-red-600 to-red-700 py-12 text-white shadow-inner">
      <div class="container mx-auto px-4 text-center">
        <span class="inline-block rounded-full bg-white/20 px-4 py-1.5 text-xs font-semibold backdrop-blur-xs mb-3">
          🔥 Promociones & Descuentos Activos
        </span>
        <h1 class="text-3xl md:text-5xl font-black tracking-tight">
          Ofertas Especiales Pollo Feliz
        </h1>
        <p class="mt-3 text-sm md:text-base text-red-100 max-w-2xl mx-auto">
          Disfruta más por menos. Haz clic en cualquier promoción para aplicarla automáticamente a tu pedido online o por WhatsApp.
        </p>
      </div>
    </div>

    <!-- Promotions Component -->
    <div class="py-12">
      <Promotions />
    </div>

    <!-- Instructions on how to redeem -->
    <div class="container mx-auto px-4 max-w-4xl mt-4">
      <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">¿Cómo canjear tus promociones?</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-600 dark:text-gray-300">
          <div class="flex items-start gap-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-sm font-bold text-red-600 dark:bg-red-950/60 dark:text-red-400">1</span>
            <div>
              <strong class="text-gray-900 dark:text-white block">Selecciona tu promo</strong>
              Haz clic en "Aprovechar" para aplicar el cupón en tu canasta.
            </div>
          </div>
          <div class="flex items-start gap-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-sm font-bold text-red-600 dark:bg-red-950/60 dark:text-red-400">2</span>
            <div>
              <strong class="text-gray-900 dark:text-white block">Completa tu pedido</strong>
              Añade tus complementos favoritos o refrescos al carrito.
            </div>
          </div>
          <div class="flex items-start gap-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-sm font-bold text-red-600 dark:bg-red-950/60 dark:text-red-400">3</span>
            <div>
              <strong class="text-gray-900 dark:text-white block">Envía tu orden</strong>
              Confirma en línea o vía WhatsApp y disfruta en minutos.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import Promotions from '@/components/landing/Promotions.vue'

useHead({
  title: 'Promociones | Pollo Feliz',
  meta: [
    { name: 'description', content: 'Promociones exclusivas de Pollo Feliz: 2x1, paquetes familiares, descuentos y ofertas de fin de semana.' }
  ]
})
</script>
