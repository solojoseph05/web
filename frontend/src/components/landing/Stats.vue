<!-- src/components/landing/Stats.vue -->
<template>
  <section id="stats" class="py-16 bg-linear-to-br from-red-50 to-orange-50 dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4">
      <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 dark:text-gray-100">
          Números que <span class="text-red-600">hablan por sí solos</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto dark:text-gray-300">
          Más de 15 años llevando el mejor sabor a las familias duranguenses
        </p>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
        <div 
          v-for="(stat, index) in statsData" 
          :key="index"
          class="bg-white rounded-xl shadow-lg p-6 text-center transform hover:scale-105 transition-all duration-300 hover:shadow-xl dark:bg-gray-900 dark:border dark:border-gray-700"
          data-aos="fade-up"
          :data-aos-delay="index * 100"
        >
          <!-- Icono -->
          <div class="inline-flex items-center justify-center w-14 h-14 bg-red-100 rounded-full mb-4 dark:bg-red-900/40">
            <component 
              :is="stat.icon" 
              class="w-7 h-7 text-red-600"
            />
          </div>
          
          <!-- Número -->
          <div class="text-3xl md:text-4xl font-bold text-red-600 mb-2">
            {{ stat.number }}
          </div>
          
          <!-- Label -->
          <div class="text-sm md:text-base text-gray-600 dark:text-gray-300">
            {{ stat.label }}
          </div>
        </div>
      </div>

      <!-- Sello de calidad -->
      <div class="mt-12 text-center">
        <div class="inline-flex items-center gap-2 bg-white px-6 py-3 rounded-full shadow-md dark:bg-gray-900 dark:border dark:border-gray-700">
          <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
            ¡Calidad garantizada por más de 15 años!
          </span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { 
  StarIcon, 
  UsersIcon, 
  CheckBadgeIcon, 
  HeartIcon 
} from '@heroicons/vue/24/solid'

// Datos de estadísticas con iconos importados
const statsData = [
  { 
    number: "15+", 
    label: "Años de experiencia", 
    icon: StarIcon 
  },
  { 
    number: "50K+", 
    label: "Clientes felices", 
    icon: UsersIcon 
  },
  { 
    number: "100%", 
    label: "Ingredientes frescos", 
    icon: CheckBadgeIcon 
  },
  { 
    number: "4.9", 
    label: "Calificación promedio", 
    icon: HeartIcon 
  }
]
</script>