<!-- src/components/landing/StickyMobileBar.vue -->
<template>
  <div
    v-if="!isAdminRoute"
    class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-gray-200/80 dark:border-gray-800 px-3 py-2 shadow-2xl transition-all"
  >
    <div class="flex items-center justify-around gap-1 text-[11px] font-black">
      <!-- Ir a Menú -->
      <a
        href="#menu"
        class="flex flex-col items-center justify-center p-1.5 text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition"
      >
        <span class="text-base leading-none mb-0.5">🍗</span>
        <span>Menú</span>
      </a>

      <!-- Ir a Sucursales -->
      <a
        href="#branches"
        class="flex flex-col items-center justify-center p-1.5 text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition"
      >
        <span class="text-base leading-none mb-0.5">📍</span>
        <span>Sucursales</span>
      </a>

      <!-- Ir a Vacantes -->
      <a
        href="#vacantes"
        class="flex flex-col items-center justify-center p-1.5 text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition"
      >
        <span class="text-base leading-none mb-0.5">💼</span>
        <span>Vacantes</span>
      </a>

      <!-- Direct WhatsApp Order Button -->
      <a
        :href="whatsappUrl"
        target="_blank"
        rel="noopener noreferrer"
        class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl shadow-md font-black text-xs active:scale-95 transition"
      >
        <span>💬</span>
        <span>Pedir Ahora</span>
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useLandingContent } from '@/composables/useLandingContent'
import company from '@/config/company'

const route = useRoute()
const { landingSettings } = useLandingContent()

const isAdminRoute = computed(() => {
  return route.path.startsWith('/admin')
})

const whatsappUrl = computed(() => {
  const raw = landingSettings.value.company_whatsapp || company.whatsapp || '526181234567'
  const cleanPhone = raw.replace(/\D/g, '')
  const phone = cleanPhone.startsWith('52') ? cleanPhone : `52${cleanPhone}`
  const message = encodeURIComponent('¡Hola Pollo Feliz! Quisiera ordenar comida a domicilio.')
  return `https://wa.me/${phone}?text=${message}`
})
</script>
