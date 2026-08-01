<!-- src/components/landing/About.vue -->
<template>
  <section id="about" class="py-16 bg-white scroll-mt-24 dark:bg-gray-950">
    <div class="container mx-auto px-4">
      <div class="grid md:grid-cols-2 gap-12 items-center">
        <!-- Imagen -->
        <div data-aos="fade-right">
          <div class="relative">
            <div class="absolute -top-4 -left-4 w-24 h-24 bg-red-200 rounded-full blur-2xl opacity-50 dark:bg-red-900/50"></div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-orange-200 rounded-full blur-2xl opacity-50 dark:bg-orange-900/40"></div>
            <img
              :src="aboutImage"
              :srcset="`${aboutImageSm} 800w, ${aboutImage} 1400w`"
              sizes="(max-width: 768px) 92vw, (max-width: 1280px) 50vw, 560px"
              alt="Sobre Pollo Feliz"
              class="rounded-2xl shadow-2xl w-full h-auto relative z-10 object-cover aspect-4/3"
              loading="lazy"
              decoding="async"
              width="560"
              height="420"
              @error="handleImageError"
            />
            <div class="absolute -bottom-6 -right-6 bg-red-600 text-white px-6 py-3 rounded-xl shadow-lg z-20">
              <div class="text-2xl font-bold">15+</div>
              <div class="text-sm">Años de tradición</div>
            </div>
          </div>
        </div>

        <!-- Contenido -->
        <div data-aos="fade-left">
          <div class="space-y-6">
            <div class="inline-flex items-center bg-red-100 text-red-700 px-4 py-2 rounded-full dark:bg-red-900/40 dark:text-red-200">
              <span class="text-sm font-medium">✨ Conoce nuestra historia</span>
            </div>

            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-gray-100">
              Más que un pollo asado,
              <span class="text-red-600">una tradición familiar</span>
            </h2>

            <p class="text-gray-600 leading-relaxed dark:text-gray-300">
              En <strong>Pollo Feliz</strong>, llevamos más de 15 años perfeccionando nuestra receta secreta,
              transmitida de generación en generación. Cada pollo es preparado con ingredientes frescos
              y el cariño que solo una familia puede dar.
            </p>

            <div class="grid grid-cols-2 gap-4 pt-4">
              <div class="flex items-start space-x-3">
                <div class="shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center dark:bg-red-900/40">
                  <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h4 class="font-semibold text-gray-800 dark:text-gray-100">Calidad</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-300">Ingredientes 100% frescos</p>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <div class="shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center dark:bg-red-900/40">
                  <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h4 class="font-semibold text-gray-800 dark:text-gray-100">Tradición</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-300">Receta familiar secreta</p>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <div class="shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center dark:bg-red-900/40">
                  <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h4 class="font-semibold text-gray-800 dark:text-gray-100">Sabor</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-300">Inigualable y auténtico</p>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <div class="shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center dark:bg-red-900/40">
                  <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h4 class="font-semibold text-gray-800 dark:text-gray-100">Precio</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-300">Accesible para todos</p>
                </div>
              </div>
            </div>

            <router-link
              to="/vision-mision"
              class="mt-4 inline-flex items-center rounded-full bg-red-600 px-8 py-3 font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:bg-red-700"
            >
              Conoce más
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </section>
 </template>

  <script setup>
  import aboutImage from '@/assets/images/PLATILLO COMPLETO.webp'
  import aboutImageSm from '@/assets/images/PLATILLO COMPLETO-sm.webp'

  const handleImageError = (event) => {
    event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZWVlIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyNHB4IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+Tm9zb3Ryb3MgPC90ZXh0Pjwvc3ZnPg=='
  }
  </script>
