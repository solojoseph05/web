<!-- src/pages/NotFound.vue -->
<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 sm:p-6 text-center dark:bg-gray-950">
    <div class="max-w-md w-full rounded-3xl bg-white p-8 sm:p-12 shadow-xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
      <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400 mx-auto mb-6 text-3xl font-black">
        404
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mb-2">
        Página no encontrada
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
        Lo sentimos, la página que buscas no existe o ha sido movida. ¡Pero siempre tenemos un pollo recién asado esperándote!
      </p>

      <router-link
        to="/"
        class="inline-flex items-center justify-center gap-2 rounded-full bg-red-600 px-8 py-3.5 text-sm font-bold text-white shadow-lg hover:bg-red-700 transition transform hover:scale-105"
      >
        <span>Volver al Inicio</span>
        <span>→</span>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'

useHead({
  title: '404 - Página no encontrada | Pollo Feliz',
})
</script>
