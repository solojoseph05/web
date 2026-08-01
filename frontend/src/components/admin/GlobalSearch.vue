<template>
  <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5">
    <div class="flex items-center gap-3">
      <span class="text-2xl">🔍</span>
      <input
        v-model="query"
        type="search"
        placeholder="Buscar en todo el admin... (vacantes, productos, promos, sucursales, mensajes)"
        class="flex-1 rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100"
      />
    </div>

    <div v-if="query.trim() && results.length" class="mt-4 max-h-64 overflow-y-auto space-y-2">
      <div
        v-for="result in results"
        :key="`${result.type}-${result.id}`"
        class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 cursor-pointer hover:bg-gray-100 transition"
        @click="selectResult(result)"
      >
        <p class="text-sm font-semibold text-gray-900">{{ result.title }}</p>
        <p class="text-xs text-gray-600">{{ result.description }}</p>
        <span class="inline-block mt-1 rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-700">
          {{ result.typeLabel }}
        </span>
      </div>
    </div>

    <div v-else-if="query.trim() && !results.length" class="mt-4 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-600">
      No se encontraron resultados para "{{ query }}"
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  jobs: {
    type: Array,
    default: () => []
  },
  menuItems: {
    type: Array,
    default: () => []
  },
  promotions: {
    type: Array,
    default: () => []
  },
  branches: {
    type: Array,
    default: () => []
  },
  messages: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['select'])

const query = ref('')

const results = computed(() => {
  const q = query.value.trim().toLowerCase()
  if (!q) return []

  const searchResults = []

  props.jobs.forEach(job => {
    if ([job.title, job.description, job.location, job.schedule, job.employment_type]
      .filter(Boolean)
      .some(v => String(v).toLowerCase().includes(q))) {
      searchResults.push({
        type: 'job',
        typeLabel: '📋 Vacante',
        id: job.id,
        title: job.title,
        description: job.location || job.schedule || 'Sin ubicación',
        data: job
      })
    }
  })

  props.menuItems.forEach(item => {
    if ([item.name, item.description, item.category, item.badge]
      .filter(Boolean)
      .some(v => String(v).toLowerCase().includes(q))) {
      searchResults.push({
        type: 'menuItem',
        typeLabel: '🍗 Producto',
        id: item.id,
        title: item.name,
        description: `${item.category} - $${Number(item.price || 0).toFixed(2)}`,
        data: item
      })
    }
  })

  props.promotions.forEach(promo => {
    if ([promo.title, promo.description, promo.code, promo.badge]
      .filter(Boolean)
      .some(v => String(v).toLowerCase().includes(q))) {
      searchResults.push({
        type: 'promotion',
        typeLabel: '🎉 Promoción',
        id: promo.id,
        title: promo.title,
        description: promo.code || 'Sin código',
        data: promo
      })
    }
  })

  props.branches.forEach(branch => {
    if ([branch.name, branch.phone, branch.address]
      .filter(Boolean)
      .some(v => String(v).toLowerCase().includes(q))) {
      searchResults.push({
        type: 'branch',
        typeLabel: '🏪 Sucursal',
        id: branch.id,
        title: branch.name,
        description: branch.phone || branch.address || 'Sin datos',
        data: branch
      })
    }
  })

  props.messages.forEach(msg => {
    if ([msg.name, msg.email, msg.message, msg.whatsapp]
      .filter(Boolean)
      .some(v => String(v).toLowerCase().includes(q))) {
      searchResults.push({
        type: 'message',
        typeLabel: '💬 Mensaje',
        id: msg.id,
        title: msg.name || msg.email || 'Sin nombre',
        description: msg.message?.substring(0, 40) + '...' || 'Sin mensaje',
        data: msg
      })
    }
  })

  return searchResults.slice(0, 10)
})

const selectResult = (result) => {
  emit('select', result)
  query.value = ''
}

defineExpose({ clearSearch: () => { query.value = '' } })
</script>
