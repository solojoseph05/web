<template>
  <div class="space-y-3">
    <!-- Filtros y Controles -->
    <div class="flex flex-wrap items-center gap-3 rounded-xl bg-gray-50 p-4">
      <input
        v-model="localSearch"
        type="search"
        placeholder="Buscar por nombre, categoría..."
        class="flex-1 min-w-64 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:outline-none"
      />
      <select
        v-model="categoryFilter"
        class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:outline-none"
      >
        <option value="">Todas las categorías</option>
        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
      </select>
      <select
        v-model="statusFilter"
        class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:outline-none"
      >
        <option value="">Todos los estados</option>
        <option value="active">Activos</option>
        <option value="inactive">Inactivos</option>
        <option value="featured">Destacados</option>
      </select>
      <button
        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
        @click="$emit('toggleBulkMode')"
      >
        {{ bulkMode ? '✓ Selección' : '☐ Seleccionar' }}
      </button>
    </div>

    <!-- Grid de Cards Mejorado -->
    <div v-if="filteredItems.length" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <div
        v-for="item in filteredItems"
        :key="item.id"
        class="group relative overflow-hidden rounded-xl border-2 bg-white shadow-sm transition hover:shadow-md hover:border-red-300"
        :class="[
          selectedId === item.id ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-200',
          bulkMode && selectedIds.includes(item.id) ? 'ring-2 ring-blue-500 bg-blue-50' : ''
        ]"
      >
        <!-- Checkbox para Bulk Actions -->
        <input
          v-if="bulkMode"
          type="checkbox"
          :checked="selectedIds.includes(item.id)"
          @change="$emit('toggleBulkSelect', item.id)"
          class="absolute top-2 left-2 z-10 h-4 w-4 cursor-pointer"
        />

        <!-- Imagen -->
        <div class="relative h-32 overflow-hidden bg-gray-100">
          <img
            :src="item.image_url || fallbackImage"
            :alt="item.name"
            class="h-full w-full object-cover transition group-hover:scale-110"
            loading="lazy"
            decoding="async"
          />
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition" />
        </div>

        <!-- Info Compacta -->
        <div class="p-3">
          <div class="flex items-start justify-between gap-2 mb-2">
            <div class="min-w-0 flex-1">
              <p class="truncate text-sm font-bold text-gray-900">{{ item.name }}</p>
              <p class="truncate text-xs text-gray-500">{{ item.category }}</p>
            </div>
            <span class="shrink-0 rounded-full bg-red-100 px-2 py-1 text-xs font-bold text-red-700">
              ${{ Number(item.price || 0).toFixed(2) }}
            </span>
          </div>

          <!-- Badges Compactos -->
          <div class="flex flex-wrap gap-1 mb-3 text-[10px]">
            <span
              :class="[
                'rounded-full px-2 py-0.5 font-medium',
                item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'
              ]"
            >
              {{ item.is_active ? '✓ Activo' : '✗ Inactivo' }}
            </span>
            <span v-if="item.is_featured" class="rounded-full bg-yellow-100 px-2 py-0.5 font-medium text-yellow-700">
              ⭐ Destacado
            </span>
            <span v-if="item.badge" class="rounded-full bg-red-100 px-2 py-0.5 font-medium text-red-700">
              {{ item.badge }}
            </span>
          </div>

          <!-- Acciones Rápidas -->
          <div class="flex gap-1.5">
            <button
              class="flex-1 rounded-lg bg-gray-100 px-2 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200 transition"
              @click="$emit('select', item.id)"
              v-if="!bulkMode"
            >
              Editar
            </button>
            <button
              class="rounded-lg bg-gray-100 px-2 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200 transition"
              :title="item.is_active ? 'Desactivar' : 'Activar'"
              @click="$emit('toggleActive', item.id)"
            >
              {{ item.is_active ? '◉' : '◯' }}
            </button>
            <button
              class="rounded-lg px-2 py-1.5 text-xs font-semibold transition"
              :class="item.is_featured ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
              :title="item.is_featured ? 'Quitar destaque' : 'Destacar'"
              @click="$emit('toggleFeatured', item.id)"
            >
              ⭐
            </button>
            <button
              class="rounded-lg bg-red-100 px-2 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200 transition"
              title="Eliminar"
              @click="$emit('delete', item.id)"
            >
              🗑️
            </button>
          </div>
        </div>

        <!-- Overlay Info Extra (hover) -->
        <div v-if="item.description" class="absolute inset-0 bg-black/80 text-white p-3 rounded-xl hidden group-hover:flex flex-col justify-center text-center opacity-0 group-hover:opacity-100 transition">
          <p class="text-xs line-clamp-3">{{ item.description }}</p>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
      <p class="text-sm text-gray-600">No hay productos que coincidan con los filtros.</p>
    </div>

    <!-- Info -->
    <div class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 rounded-lg px-4 py-3">
      <span>{{ filteredItems.length }} de {{ items.length }} productos</span>
      <span v-if="bulkMode && selectedIds.length">{{ selectedIds.length }} seleccionados</span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  items: {
    type: Array,
    required: true
  },
  selectedId: {
    type: [Number, String],
    default: null
  },
  bulkMode: {
    type: Boolean,
    default: false
  },
  selectedIds: {
    type: Array,
    default: () => []
  },
  fallbackImage: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['select', 'delete', 'toggleActive', 'toggleFeatured', 'toggleBulkMode', 'toggleBulkSelect'])

const localSearch = ref('')
const categoryFilter = ref('')
const statusFilter = ref('')

const categories = computed(() => {
  const cats = new Set(props.items.map(item => item.category).filter(Boolean))
  return Array.from(cats).sort()
})

const filteredItems = computed(() => {
  return props.items.filter(item => {
    const matchSearch = !localSearch.value || 
      item.name.toLowerCase().includes(localSearch.value.toLowerCase()) ||
      item.description?.toLowerCase().includes(localSearch.value.toLowerCase()) ||
      item.category?.toLowerCase().includes(localSearch.value.toLowerCase())
    
    const matchCategory = !categoryFilter.value || item.category === categoryFilter.value
    
    const matchStatus = !statusFilter.value ||
      (statusFilter.value === 'active' && item.is_active) ||
      (statusFilter.value === 'inactive' && !item.is_active) ||
      (statusFilter.value === 'featured' && item.is_featured)

    return matchSearch && matchCategory && matchStatus
  })
})

defineExpose({ filteredItems })
</script>
