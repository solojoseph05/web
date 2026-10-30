<template>
  <div
    class="flex flex-col justify-between rounded-3xl border border-gray-100 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-gray-800 dark:bg-gray-800/80"
  >
    <div>
      <!-- Header with Status Badge -->
      <div class="flex items-start justify-between gap-3 mb-4">
        <div>
          <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
            :class="branch.isOpen !== false || branch.is_open !== false
              ? 'bg-green-100 text-green-700 dark:bg-green-950/60 dark:text-green-300'
              : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
          >
            <span
              class="h-2 w-2 rounded-full"
              :class="branch.isOpen !== false || branch.is_open !== false ? 'bg-green-500 animate-pulse' : 'bg-gray-400'"
            ></span>
            {{ branch.isOpen !== false || branch.is_open !== false ? 'Abierto hoy' : 'Cerrado' }}
          </span>
        </div>
        <span class="text-xs font-medium text-gray-400">Sucursal #{{ branch.id }}</span>
      </div>

      <!-- Branch Name -->
      <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
        {{ branch.name }}
      </h3>

      <!-- Info Items -->
      <div class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
        <!-- Address -->
        <div class="flex items-start gap-3">
          <div class="shrink-0 mt-0.5 rounded-lg bg-red-50 p-1.5 text-red-600 dark:bg-red-950/50 dark:text-red-400">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <span class="leading-relaxed">{{ branch.address }}</span>
        </div>

        <!-- Schedule -->
        <div class="flex items-start gap-3">
          <div class="shrink-0 mt-0.5 rounded-lg bg-orange-50 p-1.5 text-orange-600 dark:bg-orange-950/50 dark:text-orange-400">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span class="leading-relaxed">{{ branch.schedule || 'Lun - Dom: 10:00 AM - 10:00 PM' }}</span>
        </div>

        <!-- Phone -->
        <div class="flex items-start gap-3">
          <div class="shrink-0 mt-0.5 rounded-lg bg-blue-50 p-1.5 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
          </div>
          <a :href="`tel:${branch.phone}`" class="font-medium text-gray-800 hover:text-red-600 dark:text-gray-200 transition">
            {{ branch.phone }}
          </a>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center gap-3">
      <a
        :href="branch.mapLink || branch.map_link || 'https://maps.google.com/?q=Durango'"
        target="_blank"
        rel="noopener noreferrer"
        class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-2.5 text-xs font-bold text-gray-800 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 min-h-[44px]"
      >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
        <span>Ver en Mapa</span>
      </a>

      <a
        :href="`tel:${branch.phone}`"
        class="flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-red-700 shadow-sm min-h-[44px]"
        aria-label="Llamar a sucursal"
      >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
        </svg>
      </a>
    </div>
  </div>
</template>

<script setup>
defineProps({
  branch: {
    type: Object,
    required: true,
  },
})
</script>
