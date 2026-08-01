<!-- src/components/landing/WhyUs.vue -->
<template>
  <section id="why-us" class="py-16 bg-white dark:bg-gray-950">
    <div class="container mx-auto px-4">
      <!-- Encabezado -->
      <div class="text-center mb-12" data-aos="fade-up">
        <span class="inline-block bg-red-100 text-red-600 text-sm font-semibold px-4 py-2 rounded-full mb-4 dark:bg-red-900/40 dark:text-red-200">
          ¿Por qué elegirnos?
        </span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 dark:text-gray-100">
          Razones para <span class="text-red-600">preferirnos</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto dark:text-gray-300">
          Descubre por qué Pollo Feliz es la mejor opción para ti y tu familia
        </p>
      </div>

      <!-- Grid de características -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div 
          v-for="(feature, index) in features" 
          :key="index"
          class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-8 border border-gray-100 hover:border-red-200 dark:bg-gray-900 dark:border-gray-700 dark:hover:border-red-800"
          :data-aos="index % 2 === 0 ? 'fade-right' : 'fade-left'"
          :data-aos-delay="index * 100"
        >
          <!-- Número -->
          <div class="absolute -top-4 -right-4 w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
            {{ index + 1 }}
          </div>

          <!-- Icono -->
          <div class="w-16 h-16 bg-red-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-red-600 transition-colors duration-300 dark:bg-red-900/40">
            <component 
              :is="feature.icon" 
              class="w-8 h-8 text-red-600 group-hover:text-white transition-colors duration-300"
            />
          </div>

          <!-- Título -->
          <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-red-600 transition-colors duration-300 dark:text-gray-100">
            {{ feature.title }}
          </h3>

          <!-- Descripción -->
          <p class="text-gray-600 leading-relaxed dark:text-gray-300">
            {{ feature.description }}
          </p>

          <!-- Línea decorativa -->
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-red-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left rounded-b-2xl"></div>
        </div>
      </div>

      <!-- CTA -->
      <div class="text-center mt-12" data-aos="fade-up">
        <button class="bg-red-600 text-white px-8 py-3 rounded-full hover:bg-red-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-red-200">
          Conoce más sobre nosotros
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { 
  FireIcon, 
  StarIcon, 
  ClockIcon, 
  HeartIcon,
  ShieldCheckIcon,
  TruckIcon
} from '@heroicons/vue/24/solid'

const features = [
  {
    icon: FireIcon,
    title: 'Sabor Único',
    description: 'Nuestra receta secreta con especias especiales que nos hace únicos en Durango.'
  },
  {
    icon: StarIcon,
    title: 'Calidad Premium',
    description: 'Seleccionamos los mejores ingredientes frescos para garantizar la mejor calidad.'
  },
  {
    icon: ClockIcon,
    title: 'Servicio Rápido',
    description: 'Tu pedido listo en menos de 30 minutos sin sacrificar la calidad.'
  },
  {
    icon: HeartIcon,
    title: 'Hecho con Amor',
    description: 'Preparamos cada pollo con la pasión y el cariño que solo una familia puede dar.'
  },
  {
    icon: ShieldCheckIcon,
    title: 'Higiene Garantizada',
    description: 'Cumplimos con los más altos estándares de limpieza y calidad.'
  },
  {
    icon: TruckIcon,
    title: 'Delivery Express',
    description: 'Llevamos el sabor de Pollo Feliz directamente a tu puerta.'
  }
]
</script>

<style scoped>
.group:hover .w-16 {
  background-color: #dc2626;
}
</style>