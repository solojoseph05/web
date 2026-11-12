<!-- src/components/admin/AnalyticsDashboard.vue -->
<template>
  <div class="space-y-6">
    <!-- Header with Live Status & Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-5 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xs">
      <div>
        <div class="flex items-center gap-2.5">
          <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
            Panel de Ventas & Analíticas en Vivo
          </h2>
          <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50">
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span>Tiempo Real</span>
          </span>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          Métricas de facturación, comportamiento de consumo y rendimiento operativo de las sucursales de Pollo Feliz.
        </p>
      </div>

      <!-- Action Buttons & Timeframe Selector -->
      <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
        <!-- Timeframe Buttons -->
        <div class="flex items-center bg-gray-100 dark:bg-gray-800 p-1 rounded-2xl border border-gray-200 dark:border-gray-700">
          <button
            v-for="tf in timeframes"
            :key="tf.id"
            @click="setTimeframe(tf.id)"
            class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
            :class="selectedTimeframe === tf.id
              ? 'bg-white text-gray-900 dark:bg-gray-900 dark:text-white shadow-xs'
              : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'"
          >
            {{ tf.label }}
          </button>
        </div>

        <!-- Simulate Live Order Button -->
        <button
          @click="handleSimulateOrder"
          :disabled="isSimulating"
          class="rounded-2xl bg-linear-to-r from-red-600 to-amber-600 px-3.5 py-2 text-xs font-black text-white shadow-md hover:from-red-700 hover:to-amber-700 transition cursor-pointer flex items-center gap-1.5 disabled:opacity-50"
          title="Genera un pedido en vivo para ver la reacción de los gráficos en tiempo real"
        >
          <span>⚡</span>
          <span class="hidden sm:inline">{{ isSimulating ? 'Generando...' : 'Simular Pedido en Vivo' }}</span>
          <span class="sm:hidden">Simular</span>
        </button>

        <!-- Manual Refresh -->
        <button
          @click="fetchAnalytics"
          :disabled="isLoading"
          class="rounded-2xl border border-gray-200 bg-white p-2 text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 cursor-pointer transition"
          title="Recargar analíticas"
        >
          <svg class="h-4 w-4" :class="{ 'animate-spin': isLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Top 4 KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- KPI 1: Ventas Totales -->
      <div class="rounded-3xl bg-white p-5 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 relative overflow-hidden flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ventas Totales</span>
          <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400 text-sm">
            💰
          </span>
        </div>
        <div class="mt-2">
          <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
            ${{ formatMoney(kpi.total_sales) }}
            <span class="text-xs font-normal text-gray-400">MXN</span>
          </div>
          <div class="flex items-center gap-1.5 mt-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
            <span>↑ +{{ kpi.sales_growth || 15.4 }}%</span>
            <span class="text-gray-400 font-normal">vs período anterior</span>
          </div>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-[11px] text-gray-500">
          <span>Ticket Promedio</span>
          <span class="font-bold text-gray-800 dark:text-gray-200">${{ kpi.average_ticket || 0 }} MXN</span>
        </div>
      </div>

      <!-- KPI 2: Total de Pedidos -->
      <div class="rounded-3xl bg-white p-5 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 relative overflow-hidden flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pedidos Totales</span>
          <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 text-sm">
            🛵
          </span>
        </div>
        <div class="mt-2">
          <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
            {{ kpi.total_orders || 0 }}
            <span class="text-xs font-normal text-gray-400">órdenes</span>
          </div>
          <div class="flex items-center gap-1.5 mt-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
            <span>↑ +{{ kpi.orders_growth || 9.2 }}%</span>
            <span class="text-gray-400 font-normal">flujo de cocina</span>
          </div>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-[11px] text-gray-500">
          <span>Pedidos Activos Ahora</span>
          <span class="font-bold text-red-600 dark:text-red-400">{{ activeOrdersCount }} en proceso</span>
        </div>
      </div>

      <!-- KPI 3: Producto Estrella -->
      <div class="rounded-3xl bg-white p-5 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 relative overflow-hidden flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Platillo Más Vendido</span>
          <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-orange-50 text-orange-600 dark:bg-orange-950/60 dark:text-orange-400 text-sm">
            🍗
          </span>
        </div>
        <div class="mt-2">
          <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white truncate" :title="kpi.best_selling_item">
            {{ kpi.best_selling_item || 'Pollo Entero Clásico' }}
          </div>
          <div class="flex items-center gap-1.5 mt-1 text-xs font-bold text-amber-600 dark:text-amber-400">
            <span>{{ kpi.best_selling_units || 0 }} unidades vendidas</span>
          </div>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-[11px] text-gray-500">
          <span>Especialidad</span>
          <span class="font-bold text-gray-800 dark:text-gray-200">Al Carbón</span>
        </div>
      </div>

      <!-- KPI 4: Sucursal Líder -->
      <div class="rounded-3xl bg-white p-5 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 relative overflow-hidden flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sucursal Líder</span>
          <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 text-sm">
            📍
          </span>
        </div>
        <div class="mt-2">
          <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white truncate" :title="kpi.top_branch">
            {{ kpi.top_branch || 'Centro Histórico' }}
          </div>
          <div class="flex items-center gap-1.5 mt-1 text-xs font-bold text-blue-600 dark:text-blue-400">
            <span>{{ kpi.top_branch_percentage || 0 }}% de la facturación total</span>
          </div>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-[11px] text-gray-500">
          <span>Tiempo prom. entrega</span>
          <span class="font-bold text-gray-800 dark:text-gray-200">22 min</span>
        </div>
      </div>
    </div>

    <!-- MAIN CHART ROW: Ventas Totales en el Tiempo (Recharts Area Chart Aesthetic) -->
    <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
            <span>📈 Evolución de Ventas Totales</span>
            <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700 dark:bg-red-950/60 dark:text-red-300">
              Curva en Tiempo Real
            </span>
          </h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">
            Ingresos acumulados y comparativa con el período anterior.
          </p>
        </div>

        <!-- Metric Switcher -->
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
            <span class="inline-flex items-center gap-1">
              <span class="h-2.5 w-2.5 rounded-full bg-red-600"></span>
              <span class="text-gray-800 dark:text-gray-200">Período Actual ($ MXN)</span>
            </span>
            <span class="inline-flex items-center gap-1">
              <span class="h-2.5 w-2.5 rounded-full bg-gray-300 dark:bg-gray-600"></span>
              <span>Anterior</span>
            </span>
          </div>
        </div>
      </div>

      <!-- Canvas Area Chart Container -->
      <div class="relative h-72 sm:h-80 w-full">
        <canvas ref="salesChartCanvas" class="w-full h-full"></canvas>
      </div>

      <!-- Bottom Insight Bar -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-3 border-t border-gray-100 dark:border-gray-800 text-xs">
        <div class="p-2.5 rounded-2xl bg-gray-50 dark:bg-gray-800/40">
          <span class="text-[11px] text-gray-400 block">Venta Pico</span>
          <span class="font-black text-gray-900 dark:text-white text-sm">
            ${{ formatMoney(peakSales.amount) }}
          </span>
          <span class="text-[10px] text-gray-500 block">({{ peakSales.label }})</span>
        </div>
        <div class="p-2.5 rounded-2xl bg-gray-50 dark:bg-gray-800/40">
          <span class="text-[11px] text-gray-400 block">Hora / Día de Mayor Demanda</span>
          <span class="font-black text-gray-900 dark:text-white text-sm">14:00 - 16:00 hrs</span>
          <span class="text-[10px] text-emerald-600 font-bold block">+32% vs promedio</span>
        </div>
        <div class="p-2.5 rounded-2xl bg-gray-50 dark:bg-gray-800/40">
          <span class="text-[11px] text-gray-400 block">Canal Predilecto</span>
          <span class="font-black text-gray-900 dark:text-white text-sm">Delivery Online (64%)</span>
          <span class="text-[10px] text-gray-500 block">36% en Mostrador</span>
        </div>
        <div class="p-2.5 rounded-2xl bg-gray-50 dark:bg-gray-800/40">
          <span class="text-[11px] text-gray-400 block">Margen Operativo Estimado</span>
          <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm">74.2%</span>
          <span class="text-[10px] text-gray-500 block">Eficiencia óptima</span>
        </div>
      </div>
    </div>

    <!-- TWO COLUMNS ROW: Productos Más Vendidos & Rendimiento de Sucursales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- COLUMN 1: Productos Más Vendidos (Recharts-Style Bar Chart) -->
      <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
              <span>🍗 Productos Más Vendidos</span>
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              Ranking por unidades pedidas y recaudación total.
            </p>
          </div>
          <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
            Top {{ topProducts.length }}
          </span>
        </div>

        <!-- Bar Chart Canvas -->
        <div class="relative h-60 w-full">
          <canvas ref="productsChartCanvas" class="w-full h-full"></canvas>
        </div>

        <!-- Detailed Ranked List with Progress -->
        <div class="space-y-2.5 pt-2 border-t border-gray-100 dark:border-gray-800 max-h-60 overflow-y-auto pr-1">
          <div
            v-for="(prod, idx) in topProducts"
            :key="prod.name"
            class="flex items-center justify-between p-2 rounded-2xl bg-gray-50/70 hover:bg-gray-100/70 dark:bg-gray-800/40 dark:hover:bg-gray-800/70 transition"
          >
            <div class="flex items-center gap-2.5 min-w-0">
              <span
                class="flex h-6 w-6 items-center justify-center rounded-lg text-xs font-black shrink-0"
                :class="idx === 0
                  ? 'bg-amber-400 text-amber-950 shadow-xs'
                  : idx === 1
                    ? 'bg-gray-300 text-gray-800'
                    : idx === 2
                      ? 'bg-amber-700/80 text-white'
                      : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
              >
                #{{ idx + 1 }}
              </span>
              <div class="min-w-0 truncate">
                <span class="font-bold text-xs sm:text-sm text-gray-900 dark:text-white truncate block">
                  {{ prod.name }}
                </span>
                <span class="text-[10px] text-gray-400 block">
                  {{ prod.category }} • {{ prod.percentage }}% de preferencia
                </span>
              </div>
            </div>

            <div class="text-right shrink-0">
              <span class="font-black text-xs sm:text-sm text-red-600 dark:text-red-400 block">
                ${{ formatMoney(prod.revenue) }}
              </span>
              <span class="text-[10px] font-bold text-gray-500">
                {{ prod.quantity }} {{ prod.quantity === 1 ? 'unidad' : 'unidades' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- COLUMN 2: Rendimiento de Sucursales en Tiempo Real (Recharts Donut & Cards) -->
      <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
              <span>📍 Rendimiento de Sucursales</span>
              <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              Participación de mercado, facturación y estatus operativo en vivo.
            </p>
          </div>
        </div>

        <!-- Donut Chart & Side Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
          <!-- Donut Canvas -->
          <div class="relative h-56 w-full flex items-center justify-center">
            <canvas ref="branchChartCanvas" class="w-full h-full"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
              <span class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white">
                {{ branchPerformance.length }}
              </span>
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                Sucursales
              </span>
            </div>
          </div>

          <!-- Branch Summary Breakdown -->
          <div class="space-y-2">
            <div
              v-for="(b, bIdx) in branchPerformance"
              :key="b.name"
              class="p-2.5 rounded-2xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 space-y-1"
            >
              <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-gray-800 dark:text-gray-200 truncate flex items-center gap-1.5">
                  <span class="h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: getBranchColor(bIdx) }"></span>
                  <span class="truncate">{{ b.name.replace('Sucursal ', '') }}</span>
                </span>
                <span class="font-black text-gray-900 dark:text-white">${{ formatMoney(b.revenue) }}</span>
              </div>

              <!-- Progress bar -->
              <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-500"
                  :style="{ width: `${b.percentage || 10}%`, backgroundColor: getBranchColor(bIdx) }"
                ></div>
              </div>

              <div class="flex items-center justify-between text-[10px] text-gray-500">
                <span class="flex items-center gap-1">
                  <span class="h-1.5 w-1.5 rounded-full" :class="b.is_open !== false ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                  <span>{{ b.is_open !== false ? 'Abierta' : 'Cerrada' }}</span>
                </span>
                <span>{{ b.orders_count }} pedidos • {{ b.percentage }}%</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Live Branch Operational Indicators -->
        <div class="pt-3 border-t border-gray-100 dark:border-gray-800 grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
          <div class="p-2 rounded-xl bg-gray-50 dark:bg-gray-800/40 text-center">
            <span class="text-[10px] text-gray-400 block uppercase font-bold">Sucursal Más Rápida</span>
            <span class="font-bold text-gray-800 dark:text-gray-200 text-xs">Centro Histórico</span>
            <span class="text-[10px] text-emerald-600 block">18 min despacho</span>
          </div>
          <div class="p-2 rounded-xl bg-gray-50 dark:bg-gray-800/40 text-center">
            <span class="text-[10px] text-gray-400 block uppercase font-bold">Mayor Ticket Promedio</span>
            <span class="font-bold text-gray-800 dark:text-gray-200 text-xs">Jardines</span>
            <span class="text-[10px] text-amber-600 block">$420 MXN / orden</span>
          </div>
          <div class="p-2 rounded-xl bg-gray-50 dark:bg-gray-800/40 text-center col-span-2 sm:col-span-1">
            <span class="text-[10px] text-gray-400 block uppercase font-bold">Capacidad Cocina</span>
            <span class="font-bold text-emerald-600 dark:text-emerald-400 text-xs">92% Fluidez</span>
            <span class="text-[10px] text-gray-400 block">Sin cuellos de botella</span>
          </div>
        </div>
      </div>
    </div>

    <!-- BOTTOM ROW: Live Orders Stream -->
    <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
            <span>⚡ Monitor de Pedidos en Tiempo Real</span>
            <span class="rounded-full bg-red-100 text-red-700 text-xs px-2.5 py-0.5 font-bold dark:bg-red-950/60 dark:text-red-300">
              {{ recentOrders.length }} recientes
            </span>
          </h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">
            Transacciones entrantes que alimentan los gráficos en vivo.
          </p>
        </div>

        <button
          @click="handleSimulateOrder"
          class="rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 cursor-pointer transition flex items-center gap-1.5"
        >
          <span>+ Simular Transacción</span>
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
          <thead class="bg-gray-50 text-[11px] font-bold uppercase text-gray-500 dark:bg-gray-800 dark:text-gray-400">
            <tr>
              <th class="px-4 py-2.5 rounded-l-xl">ID Pedido</th>
              <th class="px-4 py-2.5">Cliente</th>
              <th class="px-4 py-2.5">Sucursal Receptora</th>
              <th class="px-4 py-2.5">Detalle Productos</th>
              <th class="px-4 py-2.5">Importe Total</th>
              <th class="px-4 py-2.5 rounded-r-xl text-right">Estatus</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
            <tr
              v-for="order in recentOrders"
              :key="order.id"
              class="hover:bg-gray-50/70 dark:hover:bg-gray-800/50 transition-colors"
            >
              <td class="px-4 py-3 font-mono font-bold text-red-600 dark:text-red-400">
                #{{ order.id }}
              </td>
              <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">
                {{ order.customer_name }}
              </td>
              <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                {{ order.branch }}
              </td>
              <td class="px-4 py-3 text-gray-500 dark:text-gray-400 max-w-xs truncate">
                {{ formatOrderItems(order.items) }}
              </td>
              <td class="px-4 py-3 font-black text-gray-900 dark:text-white">
                ${{ formatMoney(order.total) }} MXN
              </td>
              <td class="px-4 py-3 text-right">
                <span
                  class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold"
                  :class="getOrderBadgeClass(order.status)"
                >
                  <span class="h-1.5 w-1.5 rounded-full" :class="getOrderDotClass(order.status)"></span>
                  <span>{{ order.status_label || order.status }}</span>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import Chart from 'chart.js/auto'
import api from '@/plugins/axios'
import { useToasts } from '@/composables/useToasts'

const { addToast } = useToasts()

// State
const selectedTimeframe = ref('today')
const isLoading = ref(false)
const isSimulating = ref(false)

const timeframes = [
  { id: 'today', label: 'Hoy (En Vivo)' },
  { id: '7days', label: 'Últimos 7 días' },
  { id: '30days', label: 'Este Mes' },
]

const kpi = ref({
  total_sales: 0,
  sales_growth: 15.4,
  total_orders: 0,
  orders_growth: 9.2,
  average_ticket: 0,
  best_selling_item: 'Pollo Entero Clásico',
  best_selling_units: 0,
  top_branch: 'Sucursal Centro Histórico',
  top_branch_percentage: 0,
})

const timeline = ref([])
const topProducts = ref([])
const branchPerformance = ref([])
const recentOrders = ref([])

// Canvas refs
const salesChartCanvas = ref(null)
const productsChartCanvas = ref(null)
const branchChartCanvas = ref(null)

// Chart.js instances
let salesChartInstance = null
let productsChartInstance = null
let branchChartInstance = null
let autoRefreshTimer = null

// Colors palette inspired by Recharts & Pollo Feliz branding
const BRANCH_COLORS = [
  '#dc2626', // Red
  '#ea580c', // Orange
  '#f59e0b', // Amber
  '#3b82f6', // Blue
  '#10b981', // Emerald
  '#8b5cf6', // Purple
]

const getBranchColor = (idx) => {
  return BRANCH_COLORS[idx % BRANCH_COLORS.length]
}

const activeOrdersCount = computed(() => {
  return recentOrders.value.filter(o => o.status !== 'delivered' && o.status !== 'cancelled').length
})

const peakSales = computed(() => {
  if (!timeline.value || !timeline.value.length) {
    return { amount: 0, label: 'N/A' }
  }
  let max = timeline.value[0]
  for (const p of timeline.value) {
    if (p.sales > max.sales) max = p
  }
  return { amount: max.sales, label: max.label }
})

const formatMoney = (val) => {
  if (val === undefined || val === null) return '0'
  return Number(val).toLocaleString('es-MX', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const formatOrderItems = (items) => {
  if (!items || !items.length) return 'Sin detalle'
  return items.map(i => `${i.quantity}x ${i.name}`).join(', ')
}

const getOrderBadgeClass = (status) => {
  if (status === 'pending') return 'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300'
  if (status === 'preparing') return 'bg-orange-100 text-orange-800 dark:bg-orange-950/70 dark:text-orange-300'
  if (status === 'on_the_way') return 'bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300'
  if (status === 'delivered') return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300'
  return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
}

const getOrderDotClass = (status) => {
  if (status === 'pending') return 'bg-amber-500'
  if (status === 'preparing') return 'bg-orange-500 animate-pulse'
  if (status === 'on_the_way') return 'bg-blue-500 animate-ping'
  if (status === 'delivered') return 'bg-emerald-500'
  return 'bg-gray-400'
}

const setTimeframe = (tf) => {
  selectedTimeframe.value = tf
  fetchAnalytics()
}

// Fetch Analytics from API
const fetchAnalytics = async () => {
  isLoading.value = true
  try {
    const res = await api.get(`/admin/analytics?timeframe=${selectedTimeframe.value}`)
    if (res.data) {
      if (res.data.kpi) kpi.value = res.data.kpi
      if (res.data.timeline) timeline.value = res.data.timeline
      if (res.data.top_products) topProducts.value = res.data.top_products
      if (res.data.branch_performance) branchPerformance.value = res.data.branch_performance
      if (res.data.recent_orders) recentOrders.value = res.data.recent_orders
      
      await nextTick()
      renderAllCharts()
    }
  } catch (err) {
    console.error('Error cargando analíticas', err)
  } finally {
    isLoading.value = false
  }
}

// Simulate Order Trigger
const handleSimulateOrder = async () => {
  isSimulating.value = true
  try {
    const res = await api.post('/admin/analytics/simulate-order')
    if (res.data) {
      addToast(res.data.message || '¡Nuevo pedido simulado!', 'success')
      await fetchAnalytics()
    }
  } catch (err) {
    console.error('Error simulando pedido', err)
    addToast('Error al simular pedido.', 'error')
  } finally {
    isSimulating.value = false
  }
}

// Chart 1: Sales Area Chart (Recharts style)
const renderSalesChart = () => {
  if (!salesChartCanvas.value) return
  if (salesChartInstance) salesChartInstance.destroy()

  const ctx = salesChartCanvas.value.getContext('2d')
  
  // Create beautiful red gradient
  const gradient = ctx.createLinearGradient(0, 0, 0, 300)
  gradient.addColorStop(0, 'rgba(220, 38, 38, 0.45)')
  gradient.addColorStop(0.7, 'rgba(220, 38, 38, 0.08)')
  gradient.addColorStop(1, 'rgba(220, 38, 38, 0.0)')

  const labels = timeline.value.map(t => t.label)
  const salesData = timeline.value.map(t => t.sales)
  const prevSalesData = timeline.value.map(t => t.previous_sales)

  salesChartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Ventas Período Actual ($ MXN)',
          data: salesData,
          borderColor: '#dc2626',
          backgroundColor: gradient,
          borderWidth: 3,
          fill: true,
          tension: 0.4, // Smooth Recharts curve
          pointBackgroundColor: '#dc2626',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
          pointHoverBackgroundColor: '#dc2626',
          pointHoverBorderColor: '#ffffff',
          pointHoverBorderWidth: 3,
        },
        {
          label: 'Período Anterior ($ MXN)',
          data: prevSalesData,
          borderColor: '#9ca3af',
          backgroundColor: 'transparent',
          borderWidth: 2,
          borderDash: [5, 5],
          fill: false,
          tension: 0.4,
          pointRadius: 0,
          pointHoverRadius: 4,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          backgroundColor: 'rgba(17, 24, 39, 0.95)',
          titleColor: '#ffffff',
          bodyColor: '#e5e7eb',
          borderColor: '#374151',
          borderWidth: 1,
          padding: 12,
          cornerRadius: 12,
          boxPadding: 6,
          usePointStyle: true,
          callbacks: {
            label: (context) => {
              const val = Number(context.raw).toLocaleString('es-MX')
              return ` ${context.dataset.label}: $${val} MXN`
            }
          }
        }
      },
      scales: {
        x: {
          grid: {
            display: false,
          },
          ticks: {
            color: '#9ca3af',
            font: { weight: '600', size: 11 },
          }
        },
        y: {
          grid: {
            color: 'rgba(156, 163, 175, 0.15)',
          },
          ticks: {
            color: '#9ca3af',
            font: { weight: '600', size: 11 },
            callback: (val) => `$${Number(val).toLocaleString('es-MX')}`
          }
        }
      }
    }
  })
}

// Chart 2: Top Products Bar Chart (Recharts rounded bar style)
const renderProductsChart = () => {
  if (!productsChartCanvas.value) return
  if (productsChartInstance) productsChartInstance.destroy()

  const ctx = productsChartCanvas.value.getContext('2d')

  // Top 5 items for cleaner bar chart
  const items = topProducts.value.slice(0, 5)
  const labels = items.map(p => p.name.length > 18 ? p.name.substring(0, 18) + '...' : p.name)
  const quantities = items.map(p => p.quantity)

  // Gradient for bars
  const barGradient = ctx.createLinearGradient(0, 0, 0, 240)
  barGradient.addColorStop(0, '#ef4444')
  barGradient.addColorStop(1, '#f97316')

  productsChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'Unidades Vendidas',
          data: quantities,
          backgroundColor: barGradient,
          borderRadius: 10,
          borderSkipped: false,
          maxBarThickness: 36,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          backgroundColor: 'rgba(17, 24, 39, 0.95)',
          padding: 12,
          cornerRadius: 12,
          callbacks: {
            label: (ctx) => ` ${ctx.raw} órdenes despachadas`
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: {
            color: '#9ca3af',
            font: { weight: '600', size: 10 },
          }
        },
        y: {
          grid: { color: 'rgba(156, 163, 175, 0.15)' },
          ticks: {
            color: '#9ca3af',
            font: { weight: '600', size: 11 },
            precision: 0,
          }
        }
      }
    }
  })
}

// Chart 3: Branch Performance Donut Chart
const renderBranchChart = () => {
  if (!branchChartCanvas.value) return
  if (branchChartInstance) branchChartInstance.destroy()

  const ctx = branchChartCanvas.value.getContext('2d')
  const labels = branchPerformance.value.map(b => b.name.replace('Sucursal ', ''))
  const data = branchPerformance.value.map(b => b.revenue)
  const bgColors = branchPerformance.value.map((_, idx) => getBranchColor(idx))

  branchChartInstance = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels,
      datasets: [
        {
          data,
          backgroundColor: bgColors,
          borderWidth: 3,
          borderColor: '#ffffff',
          hoverOffset: 8,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '72%', // Modern donut cutout
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          backgroundColor: 'rgba(17, 24, 39, 0.95)',
          padding: 12,
          cornerRadius: 12,
          callbacks: {
            label: (context) => {
              const val = Number(context.raw).toLocaleString('es-MX')
              return ` ${context.label}: $${val} MXN`
            }
          }
        }
      }
    }
  })
}

const renderAllCharts = () => {
  renderSalesChart()
  renderProductsChart()
  renderBranchChart()
}

onMounted(() => {
  fetchAnalytics()
  // Auto-refresh every 15 seconds to keep the real-time experience active
  autoRefreshTimer = setInterval(() => {
    fetchAnalytics()
  }, 15000)
})

onUnmounted(() => {
  if (autoRefreshTimer) clearInterval(autoRefreshTimer)
  if (salesChartInstance) salesChartInstance.destroy()
  if (productsChartInstance) productsChartInstance.destroy()
  if (branchChartInstance) branchChartInstance.destroy()
})
</script>
