<!-- src/pages/Landing/Menu.vue -->
<template>
  <div class="min-h-screen bg-gray-50 pt-24 pb-20 dark:bg-gray-950">
    <!-- Header Banner -->
    <div class="bg-linear-to-b from-red-600 to-red-700 py-12 text-white shadow-inner">
      <div class="container mx-auto px-4 text-center">
        <span class="inline-block rounded-full bg-white/20 px-4 py-1.5 text-xs font-semibold backdrop-blur-xs mb-3">
          🍗 Catálogo Completo & Especialidades
        </span>
        <h1 class="text-3xl md:text-5xl font-black tracking-tight">
          Nuestro Menú Pollo Feliz
        </h1>
        <p class="mt-3 text-sm md:text-base text-red-100 max-w-2xl mx-auto">
          Preparado al momento con el auténtico marinado y carbón que nos distingue. ¡Añade tus favoritos a tu pedido!
        </p>
      </div>
    </div>

    <!-- Menu Display Component Container -->
    <div class="container mx-auto px-4 py-10">
      <MenuDisplay
        :show-filters="true"
        :show-search="true"
        layout="grid"
      />
    </div>

    <!-- Floating Quick Cart Bar -->
    <Transition name="fade">
      <div
        v-if="totalCount > 0"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 w-[90%] max-w-md rounded-2xl bg-gray-900/95 p-3.5 text-white shadow-2xl backdrop-blur-md border border-white/10 dark:bg-black/95 flex items-center justify-between"
      >
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-white font-bold text-sm">
            {{ totalCount }}
          </div>
          <div>
            <p class="text-xs text-gray-300">Total acumulado</p>
            <p class="text-base font-extrabold text-white">${{ total.toFixed(0) }} MXN</p>
          </div>
        </div>

        <button
          @click="openCart"
          class="flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white shadow-lg hover:bg-red-700 transition cursor-pointer"
        >
          <span>Ver Mi Pedido</span>
          <ArrowRightIcon class="h-4 w-4" />
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import { ArrowRightIcon } from '@heroicons/vue/24/outline'
import MenuDisplay from '@/components/MenuDisplay.vue'
import { useCart } from '@/composables/useCart'

const { totalCount, total, openCart } = useCart()

useHead({
  title: 'Menú Completo | Pollo Feliz',
  meta: [
    {
      name: 'description',
      content: 'Explora nuestro delicioso menú de pollo asado al carbón, combos familiares, complementos y promociones en Pollo Feliz.'
    }
  ]
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translate(-50%, 20px);
}
</style>