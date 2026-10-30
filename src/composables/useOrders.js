// src/composables/useOrders.js
import { ref, computed } from 'vue'

const STORAGE_KEY = 'pollo_feliz_customer_orders'
const LAST_PHONE_KEY = 'pollo_feliz_customer_phone'

const orders = ref([])
const activeOrderId = ref(null)
const isLoading = ref(false)
const isHistoryModalOpen = ref(false)

const STATUS_CONFIG = {
  pending: {
    label: 'Pendiente',
    badgeClass: 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/70 dark:text-amber-300 dark:border-amber-800',
    dotClass: 'bg-amber-500',
    stepIndex: 1,
    title: 'Pedido recibido',
    description: 'Hemos recibido tu orden en cocina y estamos preparando los ingredientes.',
    icon: 'clock',
  },
  preparing: {
    label: 'En preparación',
    badgeClass: 'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-950/70 dark:text-orange-300 dark:border-orange-800',
    dotClass: 'bg-orange-500',
    stepIndex: 2,
    title: 'Asando al carbón',
    description: 'Tu pollo se está asando con el marinado tradicional y los complementos están calientes.',
    icon: 'fire',
  },
  ready: {
    label: 'Listo',
    badgeClass: 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/70 dark:text-purple-300 dark:border-purple-800',
    dotClass: 'bg-purple-500',
    stepIndex: 3,
    title: 'Empacado y listo',
    description: 'Tu orden está empacada con tortillas, salsas y lista para entregar.',
    icon: 'cube',
  },
  on_the_way: {
    label: 'En camino',
    badgeClass: 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/70 dark:text-blue-300 dark:border-blue-800',
    dotClass: 'bg-blue-500 animate-ping',
    stepIndex: 3,
    title: 'Repartidor en camino',
    description: 'Nuestro repartidor va en ruta a tu domicilio.',
    icon: 'truck',
  },
  delivered: {
    label: 'Entregado',
    badgeClass: 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/70 dark:text-emerald-300 dark:border-emerald-800',
    dotClass: 'bg-emerald-500',
    stepIndex: 4,
    title: '¡Entregado!',
    description: '¡Gracias por tu compra! Esperamos que disfrutes tu Pollo Feliz.',
    icon: 'check',
  },
  cancelled: {
    label: 'Cancelado',
    badgeClass: 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/70 dark:text-rose-300 dark:border-rose-800',
    dotClass: 'bg-rose-500',
    stepIndex: 0,
    title: 'Pedido cancelado',
    description: 'Esta orden fue cancelada. Si tienes dudas, contáctanos.',
    icon: 'x',
  },
}

export function useOrders() {
  const getSavedPhone = () => {
    try {
      return localStorage.getItem(LAST_PHONE_KEY) || ''
    } catch {
      return ''
    }
  }

  const setSavedPhone = (phone) => {
    try {
      localStorage.setItem(LAST_PHONE_KEY, phone)
    } catch {
      // ignore
    }
  }

  // Load orders from local storage and backend
  const loadOrders = async (phoneFilter = '') => {
    isLoading.value = true
    try {
      // 1. Load local cache
      const localRaw = localStorage.getItem(STORAGE_KEY)
      let localOrders = []
      if (localRaw) {
        try {
          localOrders = JSON.parse(localRaw)
        } catch {
          localOrders = []
        }
      }

      // 2. Query backend
      const queryPhone = phoneFilter || getSavedPhone()
      let url = '/api/orders'
      if (queryPhone) {
        url += `?phone=${encodeURIComponent(queryPhone)}`
      }

      const res = await fetch(url)
      if (res.ok) {
        const data = await res.json()
        const backendOrders = data.orders || []

        // Merge: keep latest from backend, plus any local ones not in backend
        const mergedMap = new Map()
        backendOrders.forEach(o => mergedMap.set(o.id, o))
        localOrders.forEach(o => {
          if (!mergedMap.has(o.id)) {
            mergedMap.set(o.id, o)
          }
        })

        orders.value = Array.from(mergedMap.values()).sort(
          (a, b) => new Date(b.created_at) - new Date(a.created_at)
        )
      } else if (localOrders.length > 0) {
        orders.value = localOrders
      }

      // Save merged to local storage
      if (orders.value.length > 0) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(orders.value))
      }
    } catch (err) {
      console.warn('Error fetching orders:', err)
      const localRaw = localStorage.getItem(STORAGE_KEY)
      if (localRaw) {
        orders.value = JSON.parse(localRaw)
      }
    } finally {
      isLoading.value = false
    }
  }

  const recordNewOrder = (order) => {
    if (!order || !order.id) return

    // Update in memory
    const existingIndex = orders.value.findIndex(o => o.id === order.id)
    if (existingIndex >= 0) {
      orders.value[existingIndex] = order
    } else {
      orders.value.unshift(order)
    }

    // Persist
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(orders.value))
      if (order.customer_phone) {
        setSavedPhone(order.customer_phone)
      }
    } catch (err) {
      console.warn('Could not save to localStorage', err)
    }

    activeOrderId.value = order.id
  }

  const getOrderStatusConfig = (status) => {
    return STATUS_CONFIG[status] || STATUS_CONFIG.pending
  }

  const openHistory = () => {
    isHistoryModalOpen.value = true
    loadOrders()
  }

  const closeHistory = () => {
    isHistoryModalOpen.value = false
  }

  const selectedOrder = computed(() => {
    if (!activeOrderId.value) return orders.value[0] || null
    return orders.value.find(o => o.id === activeOrderId.value) || orders.value[0] || null
  })

  const activeOrdersCount = computed(() => {
    return orders.value.filter(o => o.status !== 'delivered' && o.status !== 'cancelled').length
  })

  return {
    orders,
    activeOrderId,
    isLoading,
    isHistoryModalOpen,
    selectedOrder,
    activeOrdersCount,
    STATUS_CONFIG,
    loadOrders,
    recordNewOrder,
    getOrderStatusConfig,
    openHistory,
    closeHistory,
    getSavedPhone,
    setSavedPhone,
  }
}
