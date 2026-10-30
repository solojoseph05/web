<!-- src/components/landing/Navbar.vue -->
<template>
  <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md shadow-md border-b border-gray-100 dark:bg-gray-900/95 dark:border-gray-800 transition-all">
    <nav class="container mx-auto flex h-18 sm:h-20 items-center justify-between px-4 sm:px-6 gap-3">
      <!-- Official Logo (User Provided Pollo Feliz Mascot & Ribbon) -->
      <router-link
        :to="{ path: '/', hash: '#home' }"
        class="flex items-center gap-2.5 shrink-0 select-none py-1 group"
        @click="goToHome"
        title="Pollo Feliz - No hay otro mejor"
      >
        <img
          :src="logo"
          alt="Pollo Feliz - No hay otro mejor"
          class="h-11 sm:h-13 w-auto max-w-[130px] sm:max-w-[160px] object-contain shrink-0 transition-transform duration-200 group-hover:scale-105"
          width="160"
          height="52"
          decoding="async"
        />
      </router-link>

      <!-- Desktop Navigation (lg and xl screens - No text wrapping or collision) -->
      <div class="hidden lg:flex items-center space-x-4 xl:space-x-7 shrink-0">
        <a 
          v-for="item in navigation.main" 
          :key="item.id"
          :href="item.href"
          class="relative whitespace-nowrap font-bold text-sm xl:text-[15px] transition-colors py-1 shrink-0"
          :class="activeSection === item.id 
            ? 'text-red-600 dark:text-red-500' 
            : 'text-gray-700 hover:text-red-600 dark:text-gray-200 dark:hover:text-red-400'"
          @click.prevent="scrollToSection(item.href)"
        >
          {{ item.label }}
          <span 
            v-if="activeSection === item.id"
            class="absolute -bottom-1.5 left-0 right-0 h-0.5 bg-red-600 rounded-full"
          ></span>
        </a>
      </div>

      <!-- Right Actions Container (Never overlaps) -->
      <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        <!-- 
          ACCESO ADMINISTRATIVO PRIVADO:
          Oculto de la interfaz pública para mayor seguridad.
          El personal autorizado accede directamente mediante la ruta /admin/login.
        -->

        <!-- CTA: Ordenar -->
        <button
          @click="handleOrderClick"
          class="hidden md:inline-flex items-center gap-1.5 bg-linear-to-r from-red-600 to-red-700 text-white px-5 py-2 rounded-full hover:from-red-700 hover:to-red-800 transition-all transform hover:scale-105 shadow-md hover:shadow-red-200 font-bold text-xs sm:text-sm cursor-pointer whitespace-nowrap shrink-0"
        >
          <span>Ordenar Ahora</span>
        </button>

        <!-- 
          CARRITO DE VENTA DESACTIVADO TEMPORALMENTE A SOLICITUD
          Descomentar este bloque cuando se reactive el carrito de compras:
          <button
            @click="openCart"
            class="relative p-2 sm:p-2.5 rounded-full hover:bg-gray-100 transition-colors dark:hover:bg-gray-800 cursor-pointer shrink-0"
            aria-label="Ver carrito de compras"
          >
            <ShoppingCartIcon class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-200" />
            <span
              v-if="totalCount > 0"
              class="absolute -top-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-[10px] font-extrabold text-white shadow-md animate-bounce"
            >
              {{ totalCount }}
            </span>
          </button>
        -->

        <!-- Theme Toggle Button -->
        <button
          class="p-2 sm:p-2.5 rounded-full hover:bg-gray-100 transition-colors dark:hover:bg-gray-800 cursor-pointer shrink-0"
          :aria-label="theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
          @click="toggleTheme"
        >
          <SunIcon v-if="theme === 'dark'" class="w-5 h-5 text-amber-300" />
          <MoonIcon v-else class="w-5 h-5 text-gray-700 dark:text-gray-200" />
        </button>

        <!-- Mobile / Tablet Hamburger Button -->
        <button 
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="lg:hidden p-2 rounded-xl text-gray-700 hover:bg-gray-100 transition-colors dark:text-gray-200 dark:hover:bg-gray-800 cursor-pointer shrink-0"
          aria-label="Abrir menú de navegación"
        >
          <Bars3Icon v-if="!mobileMenuOpen" class="w-6 h-6" />
          <XMarkIcon v-else class="w-6 h-6" />
        </button>
      </div>
    </nav>

    <!-- Mobile / Tablet Dropdown Menu (No text collisions) -->
    <Transition name="mobile-menu">
      <div 
        v-if="mobileMenuOpen"
        class="lg:hidden bg-white shadow-2xl border-t border-gray-100 dark:bg-gray-900 dark:border-gray-800 max-h-[85vh] overflow-y-auto"
      >
        <div class="container mx-auto px-5 py-5 flex flex-col space-y-3">
          <!-- Main Links List -->
          <a 
            v-for="item in navigation.main" 
            :key="item.id"
            :href="item.href"
            class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition-colors"
            :class="activeSection === item.id
              ? 'bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400'
              : 'text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-800'"
            @click.prevent="scrollToSection(item.href)"
          >
            <span>{{ item.label }}</span>
          </a>

          <!-- Quick Actions in Mobile Menu -->
          <div class="pt-2 border-t border-gray-100 dark:border-gray-800 flex flex-col gap-2.5">
            <button
              @click="handleOrderClick"
              class="w-full rounded-xl bg-linear-to-r from-red-600 to-red-700 px-5 py-3 text-center text-xs sm:text-sm font-black text-white shadow-md hover:from-red-700 hover:to-red-800 cursor-pointer"
            >
              ¡Ordenar Pollo Asado Ahora!
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import {
  ShoppingCartIcon,
  Bars3Icon,
  XMarkIcon,
  MoonIcon,
  SunIcon,
} from '@heroicons/vue/24/outline'
import navigation from '@/config/navigation'
import logo from '@/assets/images/logo.png'
import { useTheme } from '@/composables/useTheme'
import { useCart } from '@/composables/useCart'
import { useAdminAuth } from '@/composables/useAdminAuth'

const router = useRouter()
const { totalCount, openCart } = useCart()
const { theme, toggleTheme } = useTheme()
const { adminUser } = useAdminAuth()

const mobileMenuOpen = ref(false)
const activeSection = ref('home')
let frameId = null

// Al hacer clic en "Ordenar Ahora", dado que el carrito de compras está temporalmente pausado,
// se canaliza al usuario a explorar el menú completo o las sucursales para ordenar por teléfono/WhatsApp.
const handleOrderClick = () => {
  mobileMenuOpen.value = false
  const menuElem = document.getElementById('menu')
  if (menuElem) {
    menuElem.scrollIntoView({ behavior: 'smooth' })
  } else {
    router.push('/menu')
  }
}

const updateHash = (sectionId) => {
  if (!sectionId) return
  const newHash = `#${sectionId}`
  if (window.location.hash !== newHash) {
    window.history.replaceState(null, '', newHash)
  }
}

const handleScroll = () => {
  if (frameId !== null) return

  frameId = requestAnimationFrame(() => {
    const sections = document.querySelectorAll('section[id]')
    let current = 'home'
    const scrollPosition = window.scrollY + 160

    sections.forEach(section => {
      if (section.offsetTop <= scrollPosition) {
        current = section.id
      }
    })

    if (activeSection.value !== current) {
      activeSection.value = current
      updateHash(current)
    }

    frameId = null
  })
}

const scrollToSection = (href) => {
  mobileMenuOpen.value = false

  // If link points to another route like /historial or /menu
  if (href.startsWith('/')) {
    router.push(href)
    return
  }

  if (router.currentRoute.value.path !== '/') {
    router.push({ path: '/', hash: href })
    return
  }

  const target = document.querySelector(href)
  if (target) {
    // Leave safe 90px clearance below fixed navbar so title is NEVER overlapped!
    const headerOffset = 90
    const elementPosition = target.getBoundingClientRect().top
    const offsetPosition = elementPosition + window.pageYOffset - headerOffset

    window.scrollTo({
      top: offsetPosition,
      behavior: 'smooth'
    })
    updateHash(target.id)
  }
}

const goToHome = () => {
  mobileMenuOpen.value = false
  activeSection.value = 'home'
  if (router.currentRoute.value.path !== '/') {
    router.push('/')
  } else {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    updateHash('home')
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })

  if (window.location.hash) {
    setTimeout(() => {
      const target = document.querySelector(window.location.hash)
      if (target) {
        const headerOffset = 90
        const elementPosition = target.getBoundingClientRect().top
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset
        window.scrollTo({ top: offsetPosition, behavior: 'smooth' })
      }
    }, 150)
  }

  handleScroll()
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll)
  if (frameId !== null) {
    cancelAnimationFrame(frameId)
  }
})
</script>

<style scoped>
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>