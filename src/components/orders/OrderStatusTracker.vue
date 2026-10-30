<!-- src/components/orders/OrderStatusTracker.vue -->
<template>
  <div v-if="order" class="rounded-3xl bg-white border border-gray-100 shadow-md p-5 sm:p-7 dark:bg-gray-900 dark:border-gray-800 space-y-6">
    <!-- Header: Order Number & Status Badge -->
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-5 dark:border-gray-800">
      <div>
        <div class="flex items-center gap-2">
          <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            Pedido Pollo Feliz
          </span>
          <span class="rounded-md bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
            {{ order.order_type === 'delivery' ? '🛵 A Domicilio' : '🏪 Para Recoger' }}
          </span>
        </div>
        <h3 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white mt-1">
          #{{ order.id }}
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
          {{ formatDate(order.created_at) }} · {{ order.branch }}
        </p>
      </div>

      <!-- Color-Coded Status Badge -->
      <div class="flex flex-col items-end gap-1.5">
        <span
          class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs sm:text-sm font-bold border shadow-xs"
          :class="statusInfo.badgeClass"
        >
          <span class="h-2 w-2 rounded-full" :class="statusInfo.dotClass"></span>
          {{ statusInfo.label }}
        </span>
        <span v-if="order.estimated_delivery && order.status !== 'delivered' && order.status !== 'cancelled'" class="text-[11px] font-semibold text-gray-500 dark:text-gray-400">
          Estimado: {{ order.estimated_delivery }}
        </span>
      </div>
    </div>

    <!-- Visual Stepper / Progress Tracker Bar -->
    <div v-if="order.status !== 'cancelled'" class="py-2">
      <div class="relative">
        <!-- Connecting Line Background -->
        <div class="absolute top-5 left-6 right-6 h-1 bg-gray-200 dark:bg-gray-800 rounded-full z-0"></div>
        <!-- Active Connecting Line Progress -->
        <div
          class="absolute top-5 left-6 h-1 bg-linear-to-r from-red-600 to-amber-500 rounded-full z-0 transition-all duration-500"
          :style="{ width: progressPercent + '%' }"
        ></div>

        <!-- Steps Grid -->
        <div class="relative z-10 grid grid-cols-4 text-center">
          <!-- Step 1: Recibido -->
          <div class="flex flex-col items-center">
            <div
              class="h-10 w-10 rounded-full flex items-center justify-center font-bold text-xs transition-all shadow-md"
              :class="currentStep >= 1 ? 'bg-red-600 text-white ring-4 ring-red-100 dark:ring-red-950/60' : 'bg-gray-200 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
            >
              <svg v-if="currentStep > 1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
              <span v-else>1</span>
            </div>
            <span class="mt-2 text-xs font-bold text-gray-800 dark:text-gray-200">Recibido</span>
            <span class="text-[10px] text-gray-500 hidden sm:inline">Confirmado</span>
          </div>

          <!-- Step 2: En preparación -->
          <div class="flex flex-col items-center">
            <div
              class="h-10 w-10 rounded-full flex items-center justify-center font-bold text-xs transition-all shadow-md"
              :class="currentStep >= 2 ? (currentStep === 2 ? 'bg-orange-500 text-white ring-4 ring-orange-100 dark:ring-orange-950/60 animate-pulse' : 'bg-red-600 text-white') : 'bg-gray-200 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
            >
              <svg v-if="currentStep > 2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
              <span v-else>2</span>
            </div>
            <span class="mt-2 text-xs font-bold text-gray-800 dark:text-gray-200">Al carbón</span>
            <span class="text-[10px] text-gray-500 hidden sm:inline">Preparando</span>
          </div>

          <!-- Step 3: En camino / Listo -->
          <div class="flex flex-col items-center">
            <div
              class="h-10 w-10 rounded-full flex items-center justify-center font-bold text-xs transition-all shadow-md"
              :class="currentStep >= 3 ? (currentStep === 3 ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-950/60 animate-pulse' : 'bg-red-600 text-white') : 'bg-gray-200 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
            >
              <svg v-if="currentStep > 3" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
              <span v-else>3</span>
            </div>
            <span class="mt-2 text-xs font-bold text-gray-800 dark:text-gray-200">
              {{ order.order_type === 'delivery' ? 'En ruta' : 'Empacado' }}
            </span>
            <span class="text-[10px] text-gray-500 hidden sm:inline">
              {{ order.order_type === 'delivery' ? 'Repartidor' : 'Para entrega' }}
            </span>
          </div>

          <!-- Step 4: Entregado -->
          <div class="flex flex-col items-center">
            <div
              class="h-10 w-10 rounded-full flex items-center justify-center font-bold text-xs transition-all shadow-md"
              :class="currentStep >= 4 ? 'bg-emerald-600 text-white ring-4 ring-emerald-100 dark:ring-emerald-950/60' : 'bg-gray-200 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
            </div>
            <span class="mt-2 text-xs font-bold text-gray-800 dark:text-gray-200">Entregado</span>
            <span class="text-[10px] text-gray-500 hidden sm:inline">¡A disfrutar!</span>
          </div>
        </div>
      </div>

      <!-- Current Step Description Pill -->
      <div class="mt-6 rounded-2xl bg-amber-50/70 p-3.5 border border-amber-200/60 text-xs sm:text-sm text-amber-900 flex items-center gap-3 dark:bg-amber-950/40 dark:border-amber-900/60 dark:text-amber-200">
        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-500 text-white font-bold text-xs">
          🍗
        </span>
        <div>
          <strong class="font-bold">{{ statusInfo.title }}:</strong> {{ statusInfo.description }}
        </div>
      </div>
    </div>

    <!-- Items Purchased Breakdown -->
    <div class="space-y-3">
      <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
        Detalle de Productos ({{ order.items ? order.items.reduce((acc, i) => acc + (i.quantity || 1), 0) : 0 }})
      </h4>
      <div class="divide-y divide-gray-100 rounded-2xl bg-gray-50 p-4 dark:bg-gray-800/60 dark:divide-gray-800 text-sm">
        <div
          v-for="item in order.items"
          :key="item.id || item.name"
          class="flex items-center justify-between py-2 first:pt-0 last:pb-0"
        >
          <div class="flex items-center gap-3 min-w-0">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-red-100 text-xs font-bold text-red-600 dark:bg-red-950/60 dark:text-red-400">
              {{ item.quantity || 1 }}x
            </span>
            <span class="font-medium text-gray-800 dark:text-gray-200 truncate">
              {{ item.name }}
            </span>
          </div>
          <span class="font-bold text-gray-900 dark:text-white shrink-0 ml-3">
            ${{ ((item.price || 0) * (item.quantity || 1)).toFixed(0) }} MXN
          </span>
        </div>

        <!-- Totals Row -->
        <div class="pt-3 mt-2 flex flex-col gap-1.5 text-xs text-gray-500 dark:text-gray-400">
          <div class="flex justify-between">
            <span>Subtotal:</span>
            <span>${{ (order.subtotal || order.total).toFixed(0) }} MXN</span>
          </div>
          <div v-if="order.delivery_fee" class="flex justify-between">
            <span>Envío a domicilio:</span>
            <span>${{ order.delivery_fee.toFixed(0) }} MXN</span>
          </div>
          <div class="flex justify-between text-sm sm:text-base font-black text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
            <span>Total pagado / a pagar:</span>
            <span class="text-red-600 dark:text-red-400 font-extrabold">${{ order.total.toFixed(0) }} MXN</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Delivery / Pickup Info -->
    <div class="rounded-2xl border border-gray-100 bg-white p-4 text-xs text-gray-600 dark:bg-gray-800/40 dark:border-gray-800 dark:text-gray-300 grid grid-cols-1 sm:grid-cols-2 gap-3">
      <div>
        <span class="font-bold text-gray-800 dark:text-gray-200 block mb-0.5">Destino / Sucursal:</span>
        <p v-if="order.order_type === 'delivery' && order.address">
          {{ order.address }}
        </p>
        <p v-else>
          Recoger en: {{ order.branch }}
        </p>
      </div>
      <div>
        <span class="font-bold text-gray-800 dark:text-gray-200 block mb-0.5">Método de pago:</span>
        <p>{{ order.payment_method || 'Efectivo contra entrega' }}</p>
        <p v-if="order.notes" class="mt-1 text-gray-500 italic">
          "{{ order.notes }}"
        </p>
      </div>
    </div>

    <!-- Actions: Re-order / WhatsApp -->
    <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
      <button
        @click="repeatOrder(order)"
        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md hover:bg-red-700 transition cursor-pointer"
      >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
        <span>Repetir este pedido</span>
      </button>

      <a
        :href="whatsappHelpUrl"
        target="_blank"
        rel="noopener noreferrer"
        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition"
      >
        <svg class="h-4 w-4 text-green-600 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        <span>Ayuda con mi orden</span>
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useOrders } from '@/composables/useOrders'
import { useCart } from '@/composables/useCart'
import { useToasts } from '@/composables/useToasts'

const props = defineProps({
  order: {
    type: Object,
    required: true,
  },
})

const { getOrderStatusConfig } = useOrders()
const { addToCart, openCart } = useCart()
const { addToast } = useToasts()

const statusInfo = computed(() => {
  return getOrderStatusConfig(props.order?.status)
})

const currentStep = computed(() => {
  const status = props.order?.status
  if (status === 'pending') return 1
  if (status === 'preparing') return 2
  if (status === 'ready' || status === 'on_the_way') return 3
  if (status === 'delivered') return 4
  return 0
})

const progressPercent = computed(() => {
  if (currentStep.value <= 1) return 10
  if (currentStep.value === 2) return 40
  if (currentStep.value === 3) return 72
  if (currentStep.value >= 4) return 96
  return 0
})

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

const whatsappHelpUrl = computed(() => {
  const msg = `Hola Pollo Feliz, requiero información sobre mi pedido #${props.order?.id} a nombre de ${props.order?.customer_name}.`
  return `https://wa.me/526181234567?text=${encodeURIComponent(msg)}`
})

const repeatOrder = (order) => {
  if (!order || !order.items) return
  order.items.forEach((item) => {
    addToCart(item, item.quantity || 1)
  })
  addToast(`¡Los productos del pedido #${order.id} se agregaron a tu carrito!`, 'success')
  openCart()
}
</script>
