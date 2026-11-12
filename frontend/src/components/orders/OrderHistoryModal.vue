<!-- src/components/orders/OrderHistoryModal.vue -->
<template>
  <Teleport to="body">
    <!-- Backdrop with smooth fade -->
    <Transition name="history-fade">
      <div
        v-if="isHistoryModalOpen"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs"
        @click="closeHistory"
      />
    </Transition>

    <!-- Slide-over Drawer with smooth cubic transition -->
    <Transition name="history-slide">
      <aside
        v-if="isHistoryModalOpen"
        class="fixed inset-y-0 right-0 z-50 flex w-full max-w-xl flex-col bg-white shadow-2xl dark:bg-gray-900"
        role="dialog"
        aria-modal="true"
        aria-label="Historial de Pedidos Pollo Feliz"
      >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4.5 dark:border-gray-800 shrink-0">
          <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-900 dark:text-white">Historial de Pedidos</h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                Rastrea tus compras anteriores y su estado en tiempo real
              </p>
            </div>
          </div>
          <button
            @click="closeHistory"
            class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200 cursor-pointer"
            aria-label="Cerrar historial"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Search & Filter Controls -->
        <div class="p-4 bg-gray-50/80 border-b border-gray-100 dark:bg-gray-800/40 dark:border-gray-800 space-y-3 shrink-0">
          <div class="flex gap-2">
            <div class="relative flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar por # Orden o Teléfono..."
                class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2 pl-9 text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:border-red-500 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                @keyup.enter="handleSearch"
              />
              <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <button
              @click="handleSearch"
              class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700 transition cursor-pointer"
            >
              Buscar
            </button>
          </div>

          <!-- Filter Pills -->
          <div class="flex items-center gap-2">
            <button
              v-for="filter in filters"
              :key="filter.id"
              @click="activeFilter = filter.id"
              class="rounded-lg px-3 py-1 text-xs font-semibold transition cursor-pointer"
              :class="activeFilter === filter.id
                ? 'bg-red-600 text-white shadow-xs'
                : 'bg-white text-gray-600 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'"
            >
              {{ filter.label }}
              <span class="ml-1 text-[10px] opacity-80">({{ getFilterCount(filter.id) }})</span>
            </button>
          </div>
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6">
          <!-- Active / Selected Order Tracker View -->
          <div v-if="currentSelectedOrder" class="space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-gray-500 uppercase tracking-wider px-1">
              <span>Seguimiento en Vivo</span>
              <button
                v-if="filteredOrders.length > 1"
                @click="showAllList = !showAllList"
                class="text-red-600 hover:underline dark:text-red-400 cursor-pointer"
              >
                {{ showAllList ? 'Ocultar otros pedidos' : 'Ver todos los pedidos (' + filteredOrders.length + ')' }}
              </button>
            </div>

            <!-- Visual Status Tracker Component -->
            <OrderStatusTracker :order="currentSelectedOrder" />
          </div>

          <!-- Orders List -->
          <div v-if="showAllList || !currentSelectedOrder" class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 px-1">
              {{ activeFilter === 'all' ? 'Todos los Pedidos' : (activeFilter === 'active' ? 'Pedidos en Curso' : 'Pedidos Entregados') }}
            </h4>

            <div v-if="filteredOrders.length === 0" class="text-center py-12 rounded-3xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 p-6">
              <span class="text-4xl block mb-2">🍗</span>
              <p class="font-bold text-gray-800 dark:text-gray-200">No se encontraron pedidos</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Realiza tu primer pedido desde el menú para empezar el seguimiento.
              </p>
            </div>

            <div
              v-for="order in filteredOrders"
              :key="order.id"
              @click="selectOrder(order)"
              class="rounded-2xl border p-4 transition cursor-pointer hover:shadow-md"
              :class="currentSelectedOrder?.id === order.id
                ? 'border-red-500 bg-red-50/20 dark:bg-red-950/20 dark:border-red-800'
                : 'border-gray-100 bg-white hover:border-gray-300 dark:bg-gray-800/60 dark:border-gray-800 dark:hover:border-gray-700'"
            >
              <div class="flex items-center justify-between gap-2 mb-2">
                <div class="flex items-center gap-2">
                  <span class="font-black text-gray-900 dark:text-white text-sm">#{{ order.id }}</span>
                  <span class="text-[10px] text-gray-400">· {{ formatDateShort(order.created_at) }}</span>
                </div>
                <!-- Status Badge -->
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border"
                  :class="getOrderStatusConfig(order.status).badgeClass"
                >
                  <span class="h-1.5 w-1.5 rounded-full" :class="getOrderStatusConfig(order.status).dotClass"></span>
                  {{ getOrderStatusConfig(order.status).label }}
                </span>
              </div>

              <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-1">
                {{ formatOrderItems(order.items) }}
              </p>

              <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100 text-xs text-gray-500 dark:border-gray-700/60">
                <span>{{ order.branch }}</span>
                <span class="font-black text-gray-900 dark:text-white">${{ order.total }} MXN</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-100 p-4 bg-gray-50 dark:bg-gray-800/80 dark:border-gray-800 flex items-center justify-between shrink-0">
          <router-link
            to="/menu"
            @click="closeHistory"
            class="text-xs font-bold text-red-600 hover:text-red-700 dark:text-red-400"
          >
            + Ver menú para nuevo pedido
          </router-link>

          <button
            @click="refresh"
            class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 cursor-pointer"
          >
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            <span>Actualizar</span>
          </button>
        </div>
      </aside>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useOrders } from '@/composables/useOrders'
import OrderStatusTracker from '@/components/orders/OrderStatusTracker.vue'

const {
  orders,
  activeOrderId,
  isHistoryModalOpen,
  closeHistory,
  loadOrders,
  getOrderStatusConfig,
} = useOrders()

const searchQuery = ref('')
const activeFilter = ref('all')
const showAllList = ref(false)

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

const currentSelectedOrder = computed(() => {
  if (activeOrderId.value) {
    const match = orders.value.find(o => o.id === activeOrderId.value)
    if (match) return match
  }
  return filteredOrders.value[0] || orders.value[0] || null
})

const getFilterCount = (filterId) => {
  if (filterId === 'all') return orders.value.length
  if (filterId === 'active') return orders.value.filter(o => o.status !== 'delivered' && o.status !== 'cancelled').length
  if (filterId === 'delivered') return orders.value.filter(o => o.status === 'delivered').length
  return 0
}

const selectOrder = (order) => {
  activeOrderId.value = order.id
}

const handleSearch = () => {
  loadOrders(searchQuery.value.trim())
}

const refresh = () => {
  loadOrders(searchQuery.value.trim())
}

const formatOrderItems = (items) => {
  if (!items || !items.length) return 'Sin productos'
  return items.map(i => `${i.quantity || 1}x ${i.name}`).join(', ')
}

const formatDateShort = (dateStr) => {
  if (!dateStr) return ''
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('es-MX', { day: 'numeric', month: 'short' })
  } catch {
    return dateStr
  }
}
</script>

<style scoped>
.history-fade-enter-active,
.history-fade-leave-active {
  transition: opacity 0.3s ease;
}
.history-fade-enter-from,
.history-fade-leave-to {
  opacity: 0;
}

.history-slide-enter-active,
.history-slide-leave-active {
  transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.history-slide-enter-from,
.history-slide-leave-to {
  transform: translateX(100%);
}
</style>
