<!-- src/App.vue -->
<!--
  COMPONENTE RAÍZ DE LA APLICACIÓN (Pollo Feliz Durango)
  - Administra el enrutamiento global mediante <router-view />
  - Renderiza el Navbar y Footer únicamente en páginas públicas para no interferir con el Panel Administrativo
  - Inicializa el modo oscuro/claro y la carga del contenido dinámico desde el backend
-->
<template>
  <div id="app" class="min-h-screen flex flex-col bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <!-- Navbar público: Se oculta en el panel administrativo y login para mantener una interfaz limpia -->
    <Navbar v-if="!isAdminRoute" />

    <!-- Área principal donde se renderiza la vista activa (Landing, Menú, Sucursales, Admin, etc.) -->
    <div class="flex-1">
      <router-view />
    </div>

    <!-- Footer público: Solo visible en páginas orientadas al cliente -->
    <Footer v-if="!isAdminRoute" />

    <!-- Botón flotante oficial de WhatsApp para contacto y pedidos directos -->
    <WhatsAppButton v-if="!isAdminRoute" />

    <!--
      CARRITO DE VENTA TEMPORALMENTE DESACTIVADO
      A solicitud del usuario, la venta por carrito interactivo se encuentra pausada.
      Los pedidos se canalizan vía WhatsApp, teléfono de sucursales o mostrador.
      Descomentar la siguiente línea cuando se reactive el comercio electrónico con carrito:
      <CartDrawer />
    -->

    <!-- Host global para notificaciones flotantes (toasts) -->
    <ToastHost />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from '@/components/landing/Navbar.vue'
import Footer from '@/components/landing/Footer.vue'
import WhatsAppButton from '@/components/landing/WhatsAppButton.vue'
// import CartDrawer from '@/components/cart/CartDrawer.vue' // Pausado temporalmente
import ToastHost from '@/components/ui/ToastHost.vue'
import { useLandingContent } from '@/composables/useLandingContent'
import { useTheme } from '@/composables/useTheme'

const route = useRoute()

// Identifica si la ruta actual corresponde a una sección administrativa (/admin o /admin/login)
const isAdminRoute = computed(() => {
  return route.path.startsWith('/admin')
})

const { loadLandingContent } = useLandingContent()
const { initTheme } = useTheme()

// Al montar el componente, se restaura la preferencia de tema (claro/oscuro) y se cargan los datos iniciales
onMounted(async () => {
  initTheme()
  try {
    await loadLandingContent()
  } catch (error) {
    console.error('No se pudo cargar el contenido dinámico del landing.', error)
  }
})
</script>