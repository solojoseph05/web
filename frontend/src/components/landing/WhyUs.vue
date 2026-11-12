<template>
  <section id="calidad" class="py-20 bg-gradient-to-b from-white via-orange-50/30 to-white dark:from-gray-950 dark:via-gray-900/60 dark:to-gray-950 border-y border-orange-100/60 dark:border-gray-800">
    <div class="container mx-auto px-4 sm:px-6">
      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-red-100 text-red-700 text-xs sm:text-sm font-bold mb-3 dark:bg-red-950/60 dark:text-red-300">
          <span>🔥 Tradición & Excelencia</span>
        </div>
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
          El Secreto de Nuestro <span class="text-red-600">Sabor Inconfundible</span>
        </h2>
        <p class="mt-4 text-base sm:text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
          Cada pollo que servimos en Durango es preparado con el mismo amor, paciencia y receta secreta que ha reunido a familias mexicanas por generaciones.
        </p>
      </div>

      <!-- 3-Column Visual Showcase Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Feature 1: Asado al Carbón -->
        <div class="group relative rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col">
          <div class="relative h-56 overflow-hidden">
            <img
              :src="heroChickenImg"
              alt="Pollo Asado al Carbón de Mezquite"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
              decoding="async"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
            <span class="absolute top-3 left-3 bg-red-600 text-white text-xs font-black uppercase px-3 py-1 rounded-full shadow-md">
              100% Carbón Real
            </span>
          </div>
          <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
            <div>
              <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 group-hover:text-red-600 transition-colors">
                Asado al Carbón de Mezquite
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                Sin hornos eléctricos ni atajos. Nuestro pollo se marina por 24 horas con finas hierbas y se asa pacientemente a las brasas para lograr esa piel dorada crujiente y carne jugosa.
              </p>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs font-bold text-gray-500 dark:text-gray-400">
              <span>Marinado Tradicional</span>
              <span class="text-red-600">Piel Dorada Crujiente</span>
            </div>
          </div>
        </div>

        <!-- Feature 2: Paquetes Familiares -->
        <div class="group relative rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col">
          <div class="relative h-56 overflow-hidden">
            <img
              :src="paqueteFamiliarImg"
              alt="Paquetes Familiares Pollo Feliz"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
              decoding="async"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
            <span class="absolute top-3 left-3 bg-amber-500 text-gray-950 text-xs font-black uppercase px-3 py-1 rounded-full shadow-md">
              Hecho para Compartir
            </span>
          </div>
          <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
            <div>
              <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 group-hover:text-red-600 transition-colors">
                Paquetes Familiares Abundantes
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                Porciones generosas con pollo entero, papas fritas crujientes, tortillas calientes, salsas y refresco frío. La solución perfecta y deliciosa para comer rico en familia sin cocinar.
              </p>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs font-bold text-gray-500 dark:text-gray-400">
              <span>Para 4 a 6 Personas</span>
              <span class="text-amber-500">Ahorro & Calidad</span>
            </div>
          </div>
        </div>

        <!-- Feature 3: Salsas y Guarniciones -->
        <div class="group relative rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col">
          <div class="relative h-56 overflow-hidden">
            <img
              :src="salsasImg"
              alt="Salsas y Guarniciones Artesanales"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
              decoding="async"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
            <span class="absolute top-3 left-3 bg-green-600 text-white text-xs font-black uppercase px-3 py-1 rounded-full shadow-md">
              Ingredientes Frescos
            </span>
          </div>
          <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
            <div>
              <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 group-hover:text-red-600 transition-colors">
                Salsas & Guarniciones del Día
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                Nuestras salsas roja martajada y verde tatemada se preparan cada mañana con chiles y jitomates asados. Acompañadas de cebollitas cambray y chiles toreados que despiertan el paladar.
              </p>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs font-bold text-gray-500 dark:text-gray-400">
              <span>Salsas en Molcajete</span>
              <span class="text-green-600">Cebollitas & Chiles</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Ordering Callout Strip -->
      <div class="mt-14 p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-red-600 to-red-700 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="text-center md:text-left">
          <p class="text-xs font-black uppercase tracking-wider text-red-200">¿Tienes antojo de Pollo Feliz?</p>
          <h3 class="text-2xl sm:text-3xl font-black mt-1">¡Pide ahora y te lo tenemos listo!</h3>
          <p class="text-sm text-red-100 mt-1">Servicio para recoger en sucursal o entrega rápida a tu domicilio en Durango.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
          <a
            :href="`tel:${company.phone}`"
            class="inline-flex items-center gap-2 bg-white text-red-700 hover:bg-orange-50 font-black text-sm px-6 py-3.5 rounded-full shadow-md transition transform hover:scale-105 active:scale-95 cursor-pointer whitespace-nowrap"
          >
            <span>Llamar: {{ company.phone }}</span>
          </a>
          <router-link
            to="/menu"
            class="inline-flex items-center gap-2 bg-red-800/80 hover:bg-red-900 text-white font-bold text-sm px-5 py-3.5 rounded-full border border-red-400/40 transition transform hover:scale-105 active:scale-95 cursor-pointer whitespace-nowrap"
          >
            <span>Ver Menú</span>
            <span>→</span>
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import company from '@/config/company'
import heroChickenImg from '@/assets/images/hero_pollo_asado_1794076709512.jpg'
import paqueteFamiliarImg from '@/assets/images/paquete_familiar_1794076710078.jpg'
import salsasImg from '@/assets/images/salsas_artesanales_1794076710181.jpg'
</script>
