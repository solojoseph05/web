<!-- src/components/landing/Footer.vue -->
<template>
  <footer class="bg-gray-900 text-white dark:bg-black">
    <div class="container mx-auto px-4 py-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Columna 1: Logo y descripción -->
        <div>
          <div class="flex items-center space-x-2 mb-3">
            <img
              :src="logo"
              alt="PolloFeliz"
              class="w-8 h-8 object-cover rounded-full"
              width="32"
              height="32"
              loading="lazy"
              decoding="async"
            />
            <span class="text-lg font-bold text-white">PolloFeliz</span>
          </div>
          <p class="text-gray-400 text-sm">
            El mejor pollo asado de Durango. Sabor y tradición en cada bocado.
          </p>
          <div class="flex space-x-4 mt-3">
            <a href="#" class="text-gray-400 hover:text-red-500 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-red-500 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-red-500 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21.543 6.498C22 8.28 22 12 22 12s0 3.72-.457 5.502c-.254.985-.997 1.728-1.982 1.982-1.782.457-8.561.457-8.561.457s-6.779 0-8.561-.457c-.985-.254-1.728-.997-1.982-1.982C0 15.72 0 12 0 12s0-3.72.457-5.502c.254-.985.997-1.728 1.982-1.982C4.221 4.059 11 4.059 11 4.059s6.779 0 8.561.457c.985.254 1.728.997 1.982 1.982zM9.5 15.5L15.5 12l-6-3.5v7z"/>
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-red-500 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.617 11.186-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.244 3.768-5.487 0-2.866-2.063-4.869-5.008-4.869-3.414 0-5.42 2.557-5.42 5.198 0 1.03.397 2.133.893 2.732.098.119.112.223.084.344-.091.378-.293 1.189-.333 1.356-.053.218-.175.265-.404.16-1.499-.7-2.432-2.885-2.432-4.64 0-3.777 2.747-7.237 7.908-7.237 4.146 0 7.37 2.955 7.37 6.911 0 4.119-2.597 7.431-6.199 7.431-1.211 0-2.348-.63-2.738-1.373 0 0-.599 2.282-.745 2.84-.27 1.041-1.002 2.35-1.49 3.148C9.668 23.84 10.87 24 12.017 24c6.621 0 11.988-5.367 11.988-11.987C23.985 5.367 18.638 0 12.017 0z"/>
              </svg>
            </a>
          </div>
        </div>

        <!-- Columna 2: Enlaces rápidos -->
        <div>
          <h3 class="text-sm font-semibold mb-3">Enlaces rápidos</h3>
          <ul class="space-y-2">
            <li v-for="item in navigation.main" :key="item.id">
              <a 
                :href="item.href" 
                class="text-gray-400 hover:text-red-500 transition-colors text-sm"
                @click.prevent="scrollToSection(item.href)"
              >
                {{ item.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Columna 3: Contacto -->
        <div>
          <h3 class="text-sm font-semibold mb-3">Contacto</h3>
          <ul class="space-y-2 text-sm">
            <li class="flex items-start space-x-3 text-gray-400">
              <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
              </svg>
              <span>{{ landingAddress }}</span>
            </li>
            <li class="flex items-center space-x-3 text-gray-400">
              <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
              </svg>
              <span>{{ company.phone }}</span>
            </li>
            <li class="flex items-center space-x-3 text-gray-400">
              <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
              </svg>
              <span>{{ company.email }}</span>
            </li>
          </ul>
        </div>

        <!-- Columna 4: Horario -->
        <div>
          <h3 class="text-sm font-semibold mb-3">Horario</h3>
          <p class="text-gray-400 text-sm">
            Lunes - Domingo
            <span class="block mt-1 font-semibold text-white">{{ company.workingHours.monday }}</span>
          </p>
        </div>
      </div>

      <!-- Copyright -->
      <div class="border-t border-gray-800 mt-6 pt-6 text-center text-sm text-gray-400 dark:border-gray-700">
        <p>&copy; {{ new Date().getFullYear() }} {{ company.name }}. Todos los derechos reservados.</p>
        <div class="flex justify-center space-x-4 mt-2">
          <a href="#" class="hover:text-red-500 transition-colors">Aviso de privacidad</a>
          <a href="#" class="hover:text-red-500 transition-colors">Términos y condiciones</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'
import company from '@/config/company'
import navigation from '@/config/navigation'
import logo from '@/assets/images/logo.jpg'
import { useLandingContent } from '@/composables/useLandingContent'

const { landingSettings } = useLandingContent()
const landingAddress = computed(() => landingSettings.value.company_address || company.address)

const scrollToSection = (href) => {
  const target = document.querySelector(href)
  if (target) {
    target.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>