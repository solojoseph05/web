<template>
  <section id="about" class="py-16 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
        <!-- Visual Column -->
        <div class="relative">
          <div class="relative mx-auto max-w-md lg:max-w-none rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-tr from-amber-500/20 to-red-500/20 p-2 sm:p-4">
            <div class="aspect-4/3 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 relative">
              <img
                src="https://images.unsplash.com/photo-1598103442097-8b74394b95c6?auto=format&fit=crop&w=1000&q=80"
                alt="Pollo Asado al Carbón Tradición"
                class="w-full h-full object-cover object-center transform hover:scale-105 transition-transform duration-700"
                loading="lazy"
                decoding="async"
              />
              <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent"></div>
              <div class="absolute bottom-4 left-4 right-4 text-white">
                <span class="inline-block rounded-full bg-red-600 px-3 py-1 text-xs font-bold uppercase tracking-wider mb-1">
                  100% Carbón de Mezquite
                </span>
                <p class="text-sm font-medium text-white/90">Fuego lento y sazón que conquista generaciones.</p>
              </div>
            </div>
          </div>

          <!-- Experience Card (Cleanly placed below image, no overlap) -->
          <div class="mt-4 rounded-2xl bg-white p-4 shadow-md border border-gray-100 dark:bg-gray-800 dark:border-gray-700 flex items-center gap-4 max-w-md mx-auto lg:max-w-none">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-600 text-white font-black text-xl">
              15+
            </div>
            <div>
              <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Años de Sabor Ininterrumpido</p>
              <p class="text-sm font-bold text-gray-900 dark:text-white">Tradición y Calidad Familiar en Durango</p>
            </div>
          </div>
        </div>

        <!-- Text Content Column -->
        <div class="space-y-6">
          <div class="inline-block">
            <span class="bg-red-100 text-red-600 text-xs sm:text-sm font-semibold px-4 py-1.5 rounded-full dark:bg-red-950/60 dark:text-red-300">
              🍗 Nuestra Historia y Compromiso
            </span>
          </div>

          <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white leading-tight">
            El secreto detrás del <span class="text-red-600">pollo más consentido</span> de la ciudad
          </h2>

          <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
            En Pollo Feliz nos levantamos temprano cada mañana con un solo objetivo: poner en tu mesa un pollo asado dorado a la perfección, jugoso por dentro y con esa piel crujiente que despierta todos tus sentidos.
          </p>

          <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
            Nuestra marinada artesanal con cítricos seleccionados, achiote fresco y especias naturales se cocina lentamente sobre carbón vegetal de mezquite, garantizando ese toque ahumado auténticamente mexicano que ninguna freidora puede igualar.
          </p>

          <!-- Core Value Bullets -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div class="flex items-start gap-3">
              <div class="rounded-lg bg-red-100 p-2 text-red-600 dark:bg-red-950/60 dark:text-red-400 shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Ingredientes Frescos</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Pollo de granjas certificadas y tortillas calientes hechas a mano.</p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="rounded-lg bg-red-100 p-2 text-red-600 dark:bg-red-950/60 dark:text-red-400 shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Salsas Hechas en Casa</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Recetas tradicionales tatemadas al comal todos los días.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
// Presentational component for Pollo Feliz story
</script>
