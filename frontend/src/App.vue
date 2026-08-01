<!-- src/App.vue -->
<template>
  <div id="app">
    <!-- El Navbar fuera del router view para que esté en todas las páginas -->
    <Navbar />

    <!-- Aquí se renderiza la página actual (Home, Sucursales, etc.) -->
    <router-view />

    <!-- El Footer fuera del router view -->
    <Footer />

    <!-- AGREGAMOS EL BOTÓN DE WHATSAPP AQUÍ -->
    <WhatsAppButton />
    <ToastHost />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import Navbar from '@/components/landing/Navbar.vue'
import Footer from '@/components/landing/Footer.vue'

// IMPORTAMOS EL NUEVO COMPONENTE
import WhatsAppButton from '@/components/landing/WhatsAppButton.vue'
import ToastHost from '@/components/ui/ToastHost.vue'
import { useLandingContent } from '@/composables/useLandingContent'
import { useTheme } from '@/composables/useTheme'

const { loadLandingContent } = useLandingContent()
const { initTheme } = useTheme()

onMounted(async () => {
  initTheme()
  try {
    await loadLandingContent()
  } catch (error) {
    console.error('No se pudo cargar el contenido dinámico del landing.', error)
  }
})
</script>