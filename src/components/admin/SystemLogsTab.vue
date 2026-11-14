<!-- src/components/admin/SystemLogsTab.vue -->
<template>
  <div class="admin-panel space-y-6">
    <!-- Header with status and actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xs">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-950/70 dark:text-purple-300 text-lg">
            🪵
          </span>
          <h2 class="text-xl font-black text-gray-900 dark:text-white">
            Logs del Sistema & Diagnóstico del Servidor
          </h2>
          <span class="ml-2 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Servidor Activo</span>
          </span>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Monitoreo en tiempo real de excepciones HTTP 500, advertencias de rate limiting, intentos de acceso y mutaciones administrativas.
        </p>
      </div>

      <!-- Quick Action Buttons -->
      <div class="flex items-center gap-2 flex-wrap">
        <button
          @click="fetchLogs"
          :disabled="isLoading"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition cursor-pointer disabled:opacity-50"
          title="Actualizar lista de logs"
        >
          <svg class="h-4 w-4" :class="{ 'animate-spin': isLoading }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span>Actualizar</span>
        </button>

        <button
          @click="handleSimulateTestLog('error')"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800 transition cursor-pointer"
          title="Genera un log simulado de prueba para verificar captura de excepciones"
        >
          <span>🧪 Probar Diagnóstico</span>
        </button>

        <button
          @click="handleExportLogs"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition cursor-pointer"
          title="Descargar logs en formato JSON"
        >
          <span>📥 Exportar JSON</span>
        </button>

        <button
          @click="handleClearLogs"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900 transition cursor-pointer"
          title="Limpiar historial de logs"
        >
          <span>🧹 Limpiar</span>
        </button>
      </div>
    </div>

    <!-- Access Denied Banner for Non-SuperAdmins -->
    <div
      v-if="!isSuperAdmin"
      class="p-8 rounded-3xl bg-amber-50 border border-amber-200 dark:bg-amber-950/40 dark:border-amber-900 text-center space-y-3"
    >
      <span class="text-4xl block">🛡️</span>
      <h3 class="text-base font-black text-amber-900 dark:text-amber-200">
        Consola de Logs Restringida
      </h3>
      <p class="text-xs text-amber-700 dark:text-amber-300 max-w-lg mx-auto">
        Esta pestaña contiene información de bajo nivel del servidor y trazas de depuración de Node.js. Está reservada exclusivamente para la cuenta del <strong>Super Administrador</strong> por políticas corporativas de seguridad.
      </p>
    </div>

    <!-- Main Logs Dashboard Content (Only for SuperAdmin) -->
    <div v-else class="space-y-6">
      <!-- Metric Counters -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Total Logs -->
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs">
          <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Total de Eventos</span>
          <div class="mt-1 flex items-baseline justify-between">
            <span class="text-2xl font-black text-gray-900 dark:text-white">{{ summary.total || logs.length }}</span>
            <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">En búfer</span>
          </div>
        </div>

        <!-- 500 Server Errors -->
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs">
          <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Errores de Servidor (500)</span>
          <div class="mt-1 flex items-baseline justify-between">
            <span class="text-2xl font-black" :class="summary.error_count > 0 ? 'text-red-600 animate-pulse' : 'text-emerald-600'">
              {{ summary.error_count }}
            </span>
            <span class="text-[11px] font-bold" :class="summary.error_count > 0 ? 'text-red-500' : 'text-emerald-500'">
              {{ summary.error_count === 0 ? '✓ Cero fallos' : 'Atención requerida' }}
            </span>
          </div>
        </div>

        <!-- Warnings / Rate Limiting -->
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs">
          <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Advertencias & 429</span>
          <div class="mt-1 flex items-baseline justify-between">
            <span class="text-2xl font-black text-amber-600">{{ summary.warn_count }}</span>
            <span class="text-[11px] font-semibold text-amber-500">Rate limit / Auth</span>
          </div>
        </div>

        <!-- System Uptime & Memory -->
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs">
          <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Uptime & Memoria</span>
          <div class="mt-1 flex items-baseline justify-between">
            <span class="text-lg font-black text-gray-900 dark:text-white">
              {{ formatUptime(summary.server_uptime_seconds) }}
            </span>
            <span class="text-xs font-mono text-gray-500">{{ summary.memory_usage_mb }} MB RSS</span>
          </div>
        </div>
      </div>

      <!-- Filters & Search Toolbar -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
        <!-- Level Pills -->
        <div class="flex items-center gap-1.5 flex-wrap">
          <button
            v-for="lvl in levelFilters"
            :key="lvl.id"
            @click="selectedLevel = lvl.id; fetchLogs()"
            class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer border"
            :class="selectedLevel === lvl.id
              ? 'bg-gray-900 text-white border-gray-900 dark:bg-white dark:text-gray-900 shadow-xs'
              : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 dark:bg-gray-700/60 dark:text-gray-300 dark:border-gray-600'"
          >
            {{ lvl.label }}
          </button>
        </div>

        <!-- Search Input & Auto-refresh Toggle -->
        <div class="flex items-center gap-3">
          <div class="relative flex-1 sm:w-64">
            <span class="absolute left-3 top-2.5 text-xs text-gray-400">🔍</span>
            <input
              v-model="searchQuery"
              @input="handleSearchInput"
              type="text"
              placeholder="Buscar en mensaje, ruta, IP..."
              class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-8 pr-3 py-1.5 text-xs text-gray-900 placeholder-gray-400 dark:placeholder-slate-400 focus:border-purple-500 focus:bg-white dark:focus:bg-[#161e2e] focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:text-slate-100"
            />
          </div>

          <label class="flex items-center gap-2 text-xs font-bold text-gray-600 dark:text-gray-300 cursor-pointer select-none">
            <input
              v-model="autoRefresh"
              type="checkbox"
              class="rounded text-purple-600 focus:ring-purple-500"
            />
            <span class="hidden sm:inline">Auto (10s)</span>
          </label>
        </div>
      </div>

      <!-- Logs List Table -->
      <div class="rounded-3xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs overflow-hidden">
        <div v-if="isLoading && logs.length === 0" class="py-16 text-center">
          <svg class="animate-spin h-8 w-8 text-purple-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
          <p class="text-xs text-gray-500">Consultando registros del servidor...</p>
        </div>

        <div v-else-if="logs.length === 0" class="py-16 text-center text-gray-500 dark:text-gray-400">
          <span class="text-4xl block mb-2">📜</span>
          <p class="text-sm font-bold">No hay logs registrados con los filtros actuales.</p>
          <p class="text-xs text-gray-400 mt-1">El servidor está funcionando con normalidad sin excepciones registradas.</p>
        </div>

        <div v-else class="divide-y divide-gray-100 dark:divide-gray-700/60 font-sans">
          <div
            v-for="log in logs"
            :key="log.id"
            class="p-4 hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition text-xs"
            :class="{ 'bg-rose-50/30 dark:bg-rose-950/10': log.level === 'error' }"
          >
            <!-- Log Main Row -->
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-start gap-3 min-w-0 flex-1">
                <!-- Level Badge -->
                <span
                  class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider"
                  :class="getLevelBadgeClass(log.level)"
                >
                  {{ log.level }}
                </span>

                <!-- Category Badge -->
                <span class="shrink-0 px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                  {{ log.category }}
                </span>

                <!-- HTTP Method & Code if present -->
                <span
                  v-if="log.method"
                  class="shrink-0 px-1.5 py-0.5 rounded text-[10px] font-black font-mono"
                  :class="getMethodClass(log.method)"
                >
                  {{ log.method }}
                </span>
                <span
                  v-if="log.status_code"
                  class="shrink-0 px-1.5 py-0.5 rounded text-[10px] font-bold font-mono"
                  :class="getStatusCodeClass(log.status_code)"
                >
                  {{ log.status_code }}
                </span>

                <!-- Message -->
                <span class="font-medium text-gray-900 dark:text-gray-100 truncate">
                  {{ log.message }}
                </span>
              </div>

              <!-- Timestamp & Expand Button -->
              <div class="shrink-0 flex items-center gap-2 text-[11px] text-gray-400">
                <span :title="log.timestamp">{{ formatRelativeTime(log.timestamp) }}</span>
                <button
                  @click="toggleExpand(log.id)"
                  class="p-1 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition cursor-pointer font-bold"
                  :title="expandedLogIds.includes(log.id) ? 'Ocultar detalles técnicos' : 'Ver detalles técnicos y traza'"
                >
                  {{ expandedLogIds.includes(log.id) ? '▲ Menos' : '▼ Detalles' }}
                </button>
              </div>
            </div>

            <!-- Expanded Technical Details Accordion -->
            <div
              v-if="expandedLogIds.includes(log.id)"
              class="mt-3 p-3.5 rounded-2xl bg-gray-900 text-gray-100 font-mono text-[11px] space-y-2.5 border border-gray-800"
            >
              <div class="flex items-center justify-between text-[10px] text-gray-400 border-b border-gray-800 pb-2">
                <span>Log ID: {{ log.id }}</span>
                <span>Hora exacta: {{ log.timestamp }}</span>
                <button
                  @click="copyLogToClipboard(log)"
                  class="text-purple-400 hover:text-purple-300 font-bold underline cursor-pointer"
                >
                  📋 Copiar detalles
                </button>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                <div><span class="text-gray-400">Ruta:</span> {{ log.path || 'N/A' }}</div>
                <div><span class="text-gray-400">IP Cliente:</span> {{ log.ip || 'Local / 127.0.0.1' }}</div>
                <div><span class="text-gray-400">Usuario:</span> {{ log.user_email || 'Anónimo / Sistema' }}</div>
                <div><span class="text-gray-400">Duración:</span> {{ log.duration_ms ? log.duration_ms + ' ms' : 'N/A' }}</div>
              </div>

              <!-- Stack Trace if error -->
              <div v-if="log.stack" class="pt-2 border-t border-gray-800">
                <span class="text-rose-400 font-bold block mb-1">Traza de Error (Stack Trace):</span>
                <pre class="bg-black/60 p-2.5 rounded-xl overflow-x-auto text-[10px] text-rose-300 leading-relaxed font-mono whitespace-pre-wrap">{{ log.stack }}</pre>
              </div>

              <!-- Context Details -->
              <div v-if="log.details && Object.keys(log.details).length > 0" class="pt-2 border-t border-gray-800">
                <span class="text-purple-400 font-bold block mb-1">Carga & Parámetros (Context):</span>
                <pre class="bg-black/60 p-2.5 rounded-xl overflow-x-auto text-[10px] text-gray-300 leading-relaxed font-mono">{{ JSON.stringify(log.details, null, 2) }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import api from '@/plugins/axios'
import { useAdminAuth } from '@/composables/useAdminAuth'
import { useToasts } from '@/composables/useToasts'

const { isSuperAdmin } = useAdminAuth()
const { addToast } = useToasts()

const logs = ref([])
const summary = ref({
  total: 0,
  error_count: 0,
  warn_count: 0,
  info_count: 0,
  last_error_at: null,
  server_uptime_seconds: 0,
  memory_usage_mb: 0,
})

const isLoading = ref(false)
const selectedLevel = ref('all')
const searchQuery = ref('')
const autoRefresh = ref(true)
const expandedLogIds = ref([])
let timer = null

const levelFilters = [
  { id: 'all', label: 'Todos los Niveles' },
  { id: 'error', label: '🚨 Errores (500)' },
  { id: 'warn', label: '⚠️ Advertencias (429)' },
  { id: 'info', label: 'ℹ️ Info / Mutaciones' },
]

const fetchLogs = async () => {
  if (!isSuperAdmin.value) return
  isLoading.value = true
  try {
    const params = {}
    if (selectedLevel.value !== 'all') params.level = selectedLevel.value
    if (searchQuery.value && searchQuery.value.trim()) params.search = searchQuery.value.trim()

    const res = await api.get('/admin/system-logs', { params })
    logs.value = res.data.logs || []
    if (res.data.summary) {
      summary.value = res.data.summary
    }
  } catch (err) {
    console.error('Error al consultar logs del sistema', err)
  } finally {
    isLoading.value = false
  }
}

let searchDebounce = null
const handleSearchInput = () => {
  if (searchDebounce) clearTimeout(searchDebounce)
  searchDebounce = setTimeout(() => {
    fetchLogs()
  }, 350)
}

const toggleExpand = (id) => {
  const idx = expandedLogIds.value.indexOf(id)
  if (idx > -1) {
    expandedLogIds.value.splice(idx, 1)
  } else {
    expandedLogIds.value.push(id)
  }
}

const handleSimulateTestLog = async (type = 'error') => {
  try {
    const res = await api.post('/admin/system-logs/test-simulate', { type })
    addToast('Evento de diagnóstico simulado registrado en los logs del servidor.', 'info')
    await fetchLogs()
  } catch (err) {
    addToast('No fue posible simular el evento.', 'error')
  }
}

const handleClearLogs = async () => {
  if (!confirm('¿Deseas vaciar el historial de logs del servidor?')) return
  try {
    await api.post('/admin/system-logs/clear')
    addToast('Historial de logs limpiado correctamente.', 'success')
    await fetchLogs()
  } catch (err) {
    addToast('Error al limpiar logs.', 'error')
  }
}

const handleExportLogs = async () => {
  try {
    const res = await api.get('/admin/system-logs/export?format=json')
    const blob = new Blob([JSON.stringify(res.data, null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `pollo-feliz-server-logs-${new Date().toISOString().substring(0, 10)}.json`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
    addToast('Archivo de logs exportado con éxito.', 'success')
  } catch (err) {
    addToast('Error al exportar logs.', 'error')
  }
}

const copyLogToClipboard = (log) => {
  const content = JSON.stringify(log, null, 2)
  navigator.clipboard.writeText(content).then(() => {
    addToast('Detalles del log copiados al portapapeles.', 'success')
  })
}

const getLevelBadgeClass = (level) => {
  switch (level) {
    case 'error':
      return 'bg-red-600 text-white shadow-xs'
    case 'warn':
      return 'bg-amber-500 text-white shadow-xs'
    case 'success':
      return 'bg-emerald-600 text-white'
    default:
      return 'bg-blue-600 text-white'
  }
}

const getMethodClass = (method) => {
  switch (method) {
    case 'POST':
      return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300'
    case 'PATCH':
    case 'PUT':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300'
    case 'DELETE':
      return 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
  }
}

const getStatusCodeClass = (code) => {
  if (code >= 500) return 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300'
  if (code >= 400) return 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'
  return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'
}

const formatRelativeTime = (isoString) => {
  if (!isoString) return ''
  const diffSec = Math.floor((Date.now() - new Date(isoString).getTime()) / 1000)
  if (diffSec < 5) return 'justo ahora'
  if (diffSec < 60) return `hace ${diffSec}s`
  const diffMin = Math.floor(diffSec / 60)
  if (diffMin < 60) return `hace ${diffMin}m`
  const diffHours = Math.floor(diffMin / 60)
  if (diffHours < 24) return `hace ${diffHours}h`
  return new Date(isoString).toLocaleDateString('es-MX', { hour: '2-digit', minute: '2-digit' })
}

const formatUptime = (sec) => {
  if (!sec || sec < 60) return `${sec || 0}s`
  const min = Math.floor(sec / 60)
  if (min < 60) return `${min}m`
  const hours = Math.floor(min / 60)
  const remMin = min % 60
  return `${hours}h ${remMin}m`
}

onMounted(() => {
  fetchLogs()
  timer = setInterval(() => {
    if (autoRefresh.value) {
      fetchLogs()
    }
  }, 10000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>
