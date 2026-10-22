<template>
  <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-700">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">📊 Resumen de estado</p>
    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
      <div class="rounded-lg bg-red-50 p-3 text-center dark:bg-red-900">
        <div class="text-2xl font-bold text-red-600 dark:text-red-300">{{ summary.active_jobs }}</div>
        <p class="mt-1 text-xs text-red-700 dark:text-red-200">Vacantes</p>
        <p class="mt-1 text-[11px] font-semibold" :class="trendClass(summary.trends?.active_jobs?.delta)">
          {{ trendLabel(summary.trends?.active_jobs?.delta) }}
        </p>
      </div>
      <div class="rounded-lg bg-green-50 p-3 text-center dark:bg-green-900">
        <div class="text-2xl font-bold text-green-600 dark:text-green-300">{{ summary.active_menu_items }}</div>
        <p class="mt-1 text-xs text-green-700 dark:text-green-200">Productos</p>
        <p class="mt-1 text-[11px] font-semibold" :class="trendClass(summary.trends?.active_menu_items?.delta)">
          {{ trendLabel(summary.trends?.active_menu_items?.delta) }}
        </p>
      </div>
      <div class="rounded-lg bg-blue-50 p-3 text-center dark:bg-blue-900">
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ summary.contact_messages }}</div>
        <p class="mt-1 text-xs text-blue-700 dark:text-blue-200">Mensajes</p>
        <p class="mt-1 text-[11px] font-semibold" :class="trendClass(summary.trends?.contact_messages?.delta)">
          {{ trendLabel(summary.trends?.contact_messages?.delta) }}
        </p>
      </div>
      <div class="rounded-lg bg-yellow-50 p-3 text-center dark:bg-yellow-900">
        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-300">{{ summary.pending_contact_messages }}</div>
        <p class="mt-1 text-xs text-yellow-700 dark:text-yellow-200">Pendientes</p>
      </div>
      <div class="rounded-lg bg-purple-50 p-3 text-center dark:bg-purple-900">
        <div class="text-2xl font-bold text-purple-600 dark:text-purple-300">{{ summary.admin_users }}</div>
        <p class="mt-1 text-xs text-purple-700 dark:text-purple-200">Admin</p>
        <p class="mt-1 text-[11px] font-semibold" :class="trendClass(summary.trends?.admin_users?.delta)">
          {{ trendLabel(summary.trends?.admin_users?.delta) }}
        </p>
      </div>
      <div class="rounded-lg bg-orange-50 p-3 text-center dark:bg-orange-900">
        <div class="text-2xl font-bold text-orange-600 dark:text-orange-300">{{ summary.change_logs }}</div>
        <p class="mt-1 text-xs text-orange-700 dark:text-orange-200">Cambios</p>
        <p class="mt-1 text-[11px] font-semibold" :class="trendClass(summary.trends?.change_logs?.delta)">
          {{ trendLabel(summary.trends?.change_logs?.delta) }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  summary: {
    type: Object,
    required: true,
  },
})

const trendClass = (delta) => {
  if (delta > 0) {
    return 'text-emerald-700 dark:text-emerald-300'
  }
  if (delta < 0) {
    return 'text-rose-700 dark:text-rose-300'
  }
  return 'text-gray-500 dark:text-gray-300'
}

const trendLabel = (delta = 0) => {
  if (delta > 0) {
    return `+${delta} vs 7 días`
  }
  if (delta < 0) {
    return `${delta} vs 7 días`
  }
  return 'Sin cambio semanal'
}
</script>
