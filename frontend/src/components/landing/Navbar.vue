<template>
  <header class="fixed top-0 left-0 right-0 z-50 bg-white shadow-lg border-b border-gray-100 dark:bg-gray-900 dark:border-gray-700">
    <nav class="container mx-auto flex items-center justify-between px-4 py-3 sm:px-6">
      <!-- Logo -->
      <router-link
        :to="{ path: '/', hash: '#home' }"
        class="flex items-center space-x-2"
        @click="goToHome"
      >
        <img
          :src="logo"
          alt="PolloFeliz"
          class="w-12 h-12 object-cover rounded-full"
          width="48"
          height="48"
          decoding="async"
        />
        <span class="text-xl font-bold text-gray-800 dark:text-gray-100">
          PolloFeliz
        </span>
      </router-link>

      <!-- Desktop Navigation -->
      <div class="hidden md:flex items-center space-x-8">
        <a 
          v-for="item in navigation.main" 
          :key="item.id"
          :href="item.href"
          class="relative font-medium text-gray-700 hover:text-red-600 transition-colors duration-200 dark:text-gray-200"
          :class="activeSection === item.id ? 'text-red-600' : ''"
          @click.prevent="scrollToSection(item.href)"
        >
          {{ item.label }}
          <span 
            v-if="activeSection === item.id"
            class="absolute -bottom-1 left-0 right-0 h-0.5 bg-red-600 rounded-full"
          ></span>
        </a>
      </div>

      <!-- Actions -->
      <div class="hidden md:flex items-center space-x-4">
        <button class="bg-red-600 text-white px-6 py-2 rounded-full hover:bg-red-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-red-200">
          Ordenar
        </button>
        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors dark:hover:bg-gray-800">
          <ShoppingCartIcon class="w-6 h-6 text-gray-700 dark:text-gray-200" />
        </button>
        <button
          class="p-2 rounded-full hover:bg-gray-100 transition-colors dark:hover:bg-gray-800"
          :aria-label="theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
          @click="toggleTheme"
        >
          <SunIcon v-if="theme === 'dark'" class="w-6 h-6 text-amber-300" />
          <MoonIcon v-else class="w-6 h-6 text-gray-700 dark:text-gray-200" />
        </button>
      </div>

      <!-- Mobile Menu Button -->
      <button 
        @click="mobileMenuOpen = !mobileMenuOpen"
        class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors dark:hover:bg-gray-800"
      >
        <Bars3Icon v-if="!mobileMenuOpen" class="w-6 h-6 text-gray-700 dark:text-gray-200" />
        <XMarkIcon v-else class="w-6 h-6 text-gray-700 dark:text-gray-200" />
      </button>
    </nav>

    <!-- Mobile Menu -->
    <div 
      v-if="mobileMenuOpen"
      class="md:hidden bg-white shadow-lg border-t border-gray-100 dark:bg-gray-900 dark:border-gray-700"
    >
      <div class="container mx-auto px-4 py-4 flex flex-col space-y-4">
        <a 
          v-for="item in navigation.main" 
          :key="item.id"
          :href="item.href"
          class="text-gray-700 hover:text-red-600 transition-colors font-medium dark:text-gray-200"
          @click.prevent="scrollToSection(item.href)"
        >
          {{ item.label }}
        </a>
        <button
          class="rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-100"
          @click="toggleTheme"
        >
          {{ theme === 'dark' ? 'Modo claro' : 'Modo oscuro' }}
        </button>
        <button class="bg-red-600 text-white px-6 py-2 rounded-full hover:bg-red-700 transition-colors w-full">
          Ordenar
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { ShoppingCartIcon, Bars3Icon, XMarkIcon, MoonIcon, SunIcon } from '@heroicons/vue/24/outline'
import navigation from '@/config/navigation'
import logo from '@/assets/images/logo.jpg'
import { useTheme } from '@/composables/useTheme'

// El navbar usa los IDs definidos en navigation.js para resaltar la sección activa.
// Si cambias un id o href aquí, también debes mantenerlos coherentes con los section[id] del landing.
const mobileMenuOpen = ref(false)
const activeSection = ref('home')
const { theme, toggleTheme } = useTheme()
let frameId = null

const updateHash = (sectionId) => {
  if (!sectionId) {
    return
  }

  const newHash = `#${sectionId}`
  if (window.location.hash !== newHash) {
    window.history.replaceState(null, '', newHash)
  }
}

// El scroll se procesa con requestAnimationFrame para no saturar el navegador.
// Esto mantiene la UI más fluida mientras cambia la sección activa y el hash de la URL.
const handleScroll = () => {
  if (frameId !== null) {
    return
  }

  frameId = requestAnimationFrame(() => {
    const sections = document.querySelectorAll('section[id]')
    let current = 'home'
    const scrollPosition = window.scrollY + 140

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

// Al hacer clic en un enlace del navbar se desplaza a la sección correspondiente.
// El hash de la URL se actualiza para que el navegador y la navegación compartan la misma referencia.
const scrollToSection = (href) => {
  mobileMenuOpen.value = false
  const target = document.querySelector(href)
  if (target) {
    target.scrollIntoView({ behavior: 'smooth' })
    updateHash(target.id)
  }
}

const closeMobileMenu = () => {
  mobileMenuOpen.value = false
}

// El logo vuelve al inicio y limpia la selección activa para que vuelva el estado inicial.
const goToHome = () => {
  closeMobileMenu()
  activeSection.value = 'home'
  updateHash('home')
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })

  if (window.location.hash) {
    const target = document.querySelector(window.location.hash)
    if (target) {
      target.scrollIntoView({ behavior: 'auto' })
    }
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