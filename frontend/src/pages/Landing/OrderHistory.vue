<!-- src/pages/Landing/OrderHistory.vue -->
<template>
  <div class="min-h-screen bg-gray-50 pt-28 pb-20 dark:bg-gray-950">
    <!-- Header Banner -->
    <div class="bg-linear-to-b from-red-600 to-red-700 py-12 text-white shadow-inner">
      <div class="container mx-auto px-4 text-center">
        <span class="inline-block rounded-full bg-white/20 px-4 py-1.5 text-xs font-semibold backdrop-blur-xs mb-3">
          🍗 Seguimiento y Compras Anteriores
        </span>
        <h1 class="text-3xl md:text-5xl font-black tracking-tight">
          Historial de Pedidos Pollo Feliz
        </h1>
        <p class="mt-3 text-sm md:text-base text-red-100 max-w-2xl mx-auto">
          Consulta en tiempo real el estado de preparación, salida al carbón y entrega de tus órdenes.
        </p>
      </div>
    </div>

    <!-- Main Container -->
    <div class="container mx-auto px-4 py-10 max-w-5xl">
      <!-- Search & Controls Bar -->
      <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex-1 w-full relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar por número de pedido (ej. #PF-7821) o teléfono..."
            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 pl-11 text-sm text-gray-800 placeholder-gray-400 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            @keyup.enter="handleSearch"
          />
          <svg class="absolute left-4 top-3.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
          <button
            v-for="filter in filters"
            :key="filter.id"
            @click="activeFilter = filter.id"
            class="flex-1 md:flex-initial rounded-xl px-4 py-2.5 text-xs font-bold transition cursor-pointer"
            :class="activeFilter === filter.id
              ? 'bg-red-600 text-white shadow-xs'
              : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'"
          >
            {{ filter.label }}
          </button>
        </div>
      </div>

      <!-- Main Grid: Active Tracker on Left / Top, Order list on Right / Bottom -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Visual Order Tracker (Main Feature) -->
        <div class="lg:col-span-7">
          <div v-if="selectedOrder">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-lg font-black text-gray-900 dark:text-white">
                Rastreo en Tiempo Real
              </h2>
              <span class="text-xs text-gray-500 dark:text-gray-400">
                Actualizado automáticamente
              </span>
            </div>
            <OrderStatusTracker :order="selectedOrder" />
          </div>

          <div v-else class="rounded-3xl bg-white p-10 text-center border border-gray-100 shadow-sm dark:bg-gray-900 dark:border-gray-800">
            <span class="text-5xl block mb-3">📦</span>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Selecciona un pedido</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              Elige un pedido del listado para ver su línea de tiempo y detalles completos.
            </p>
          </div>
        </div>

        <!-- Orders Selection List -->
        <div class="lg:col-span-5 space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
              Tus Pedidos ({{ filteredOrders.length }})
            </h3>
            <router-link
              to="/menu"
              class="text-xs font-bold text-red-600 hover:text-red-700 dark:text-red-400"
            >
              + Nuevo pedido
            </router-link>
          </div>

          <div v-if="filteredOrders.length === 0" class="rounded-3xl bg-white p-8 text-center border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
            <p class="font-bold text-gray-800 dark:text-gray-200 text-sm">No hay pedidos registrados</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-4">
              ¿Listo para saborear el auténtico pollo al carbón?
            </p>
            <router-link
              to="/menu"
              class="inline-block rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-red-700 transition"
            >
              Ir al Menú
            </router-link>
          </div>

          <div
            v-for="order in filteredOrders"
            :key="order.id"
            @click="activeOrderId = order.id"
            class="rounded-3xl border p-5 transition cursor-pointer hover:shadow-md"
            :class="selectedOrder?.id === order.id
              ? 'border-red-500 bg-red-50/30 ring-2 ring-red-100 dark:bg-red-950/20 dark:border-red-800 dark:ring-red-950/40'
              : 'border-gray-100 bg-white hover:border-gray-300 dark:bg-gray-900 dark:border-gray-800 dark:hover:border-gray-700'"
          >
            <div class="flex items-center justify-between gap-2 mb-2">
              <span class="font-black text-gray-900 dark:text-white text-base">#{{ order.id }}</span>
              <!-- Status Badge -->
              <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border"
                :class="getOrderStatusConfig(order.status).badgeClass"
              >
                <span class="h-1.5 w-1.5 rounded-full" :class="getOrderStatusConfig(order.status).dotClass"></span>
                {{ getOrderStatusConfig(order.status).label }}
              </span>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
              {{ formatDate(order.created_at) }} · {{ order.branch }}
            </p>

            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-0.5">
              <div v-for="item in (order.items || []).slice(0, 2)" :key="item.id" class="flex justify-between">
                <span>{{ item.quantity || 1 }}x {{ item.name }}</span>
                <span class="font-semibold">${{ ((item.price || 0) * (item.quantity || 1)).toFixed(0) }}</span>
              </div>
              <div v-if="(order.items || []).length > 2" class="text-[11px] text-gray-400">
                +{{ order.items.length - 2 }} productos más...
              </div>
            </div>

            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs dark:border-gray-800">
              <span class="text-gray-500">{{ order.order_type === 'delivery' ? '🛵 Domicilio' : '🏪 Sucursal' }}</span>
              <span class="text-sm font-black text-red-600 dark:text-red-400">${{ order.total }} MXN</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useOrders } from '@/composables/useOrders'
import OrderStatusTracker from '@/components/orders/OrderStatusTracker.vue'

const {
  orders,
  activeOrderId,
  loadOrders,
  selectedOrder,
  getOrderStatusConfig,
} = useOrders()

const searchQuery = ref('')
const activeFilter = ref('all')

const filters = [
  { id: 'all', label: 'Todos' },
  { id: 'active', label: 'En proceso' },
  { id: 'delivered', label: 'Entregados' },
]

onMounted(() => {
  loadOrders()
})

const filteredOrders = computed(() => {
  let list = [...orders.value]

  if (activeFilter.value === 'active') {
    list = list.filter(o => o.status !== 'delivered' && o.status !== 'cancelled')
  } else if (activeFilter.value === 'delivered') {
    list = list.filter(o => o.status === 'delivered')
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.trim().toLowerCase()
    list = list.filter(o =>
      o.id.toLowerCase().includes(q) ||
      (o.customer_phone && o.customer_phone.includes(q)) ||
      (o.customer_name && o.customer_name.toLowerCase().includes(q))
    )
  }

  return list
})

const handleSearch = () => {
  loadOrders(searchQuery.value.trim())
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('es-MX', {
      day: 'numeric',
      month: 'short',
      hour: '2-digit',
      minute: '2-digit',
    })
  } catch {
    return dateStr
  }
}
</script>
