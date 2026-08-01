<template>
  <div 
    class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-2 flex flex-col dark:bg-gray-800 dark:border dark:border-gray-700"
    data-aos="fade-up"
  >
    <!-- Imagen de la sucursal -->
    <div class="relative h-48 overflow-hidden shrink-0">
      <img 
        :src="branch.image" 
        :alt="branch.name" 
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
        loading="lazy"
        decoding="async"
        width="640"
        height="384"
        @error="handleImageError"
      />
      <!-- Indicador de Abierto/Cerrado -->
      <div 
        class="absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm"
        :class="branch.isOpen ? 'bg-green-500/90 text-white' : 'bg-red-500/90 text-white'"
      >
        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
        {{ branch.isOpen ? 'Abierto' : 'Cerrado' }}
      </div>
    </div>

    <!-- Contenido -->
    <div class="p-6 flex flex-col grow">
      <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-red-600 transition-colors dark:text-gray-100">
        {{ branch.name }}
      </h3>
      
      <div class="space-y-2.5 text-sm text-gray-600 mb-6 grow dark:text-gray-300">
        <!-- Dirección -->
        <div class="flex items-start gap-3">
          <span class="text-red-500 text-lg mt-0.5">📍</span>
          <span class="leading-relaxed">{{ branch.address }}</span>
        </div>
        <!-- Teléfono -->
        <div class="flex items-start gap-3">
          <span class="text-red-500 text-lg mt-0.5">📞</span>
          <span class="leading-relaxed">{{ branch.phone }}</span>
        </div>
        <!-- Horario -->
        <div class="flex items-start gap-3">
          <span class="text-red-500 text-lg mt-0.5">🕐</span>
          <span class="leading-relaxed">{{ branch.schedule }}</span>
        </div>
      </div>

      <!-- Botones de acción -->
      <div class="flex flex-col sm:flex-row gap-3 mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
        <a 
          :href="branch.mapLink" 
          target="_blank"
          class="flex-1 bg-red-600 text-white text-center px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-red-700 transition-colors shadow-md flex items-center justify-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Cómo llegar
        </a>
        <a 
          :href="`tel:${branch.phone}`" 
          class="flex-1 bg-gray-100 text-gray-700 text-center px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors flex items-center justify-center gap-2 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          Llamar
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  branch: {
    type: Object,
    required: true
  }
})

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZWVlIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyNHB4IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+U3VjdXJzYWw8L3RleHQ+PC9zdmc+'
}
</script>