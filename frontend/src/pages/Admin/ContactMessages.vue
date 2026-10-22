<template>
  <div class="min-h-screen bg-gray-50 px-4 py-24 dark:bg-gray-950">
    <div class="mx-auto max-w-6xl rounded-4xl bg-white p-6 shadow-xl sm:p-8 dark:bg-gray-900 dark:border dark:border-gray-700">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-sm font-semibold uppercase tracking-[0.2em] text-red-600">Panel interno</p>
          <h1 class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">Mensajes de contacto</h1>
          <p class="mt-2 max-w-2xl text-sm text-gray-600 dark:text-gray-300">
            Revisa los mensajes enviados desde la landing y cambia su estado para darles seguimiento.
          </p>
        </div>

        <router-link
          to="/"
          class="inline-flex items-center justify-center rounded-full border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-red-200 hover:text-red-600 dark:border-gray-700 dark:text-gray-100"
        >
          Volver al inicio
        </router-link>
      </div>

      <div v-if="loading" class="mt-8 rounded-2xl border border-gray-200 bg-gray-50 p-6 text-center text-gray-600">
        Cargando mensajes...
      </div>

      <div v-else-if="error" class="mt-8 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
        {{ error }}
      </div>

      <div v-else-if="messages.length === 0" class="mt-8 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-gray-600">
        No hay mensajes de contacto registrados todavía.
      </div>

      <div v-else class="mt-8 overflow-x-auto">
        <table class="min-w-full border-separate border-spacing-y-3 text-sm text-gray-700">
          <thead>
            <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">
              <th class="px-4 py-2">Nombre</th>
              <th class="px-4 py-2">Correo</th>
              <th class="px-4 py-2">Mensaje</th>
              <th class="px-4 py-2">Estado</th>
              <th class="px-4 py-2">Fecha</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="message in messages" :key="message.id" class="rounded-2xl bg-gray-50 align-top">
              <td class="rounded-l-2xl px-4 py-4">
                <div class="font-semibold text-gray-900">{{ message.name }}</div>
                <div class="mt-1 text-xs text-gray-500">{{ message.source }}</div>
              </td>
              <td class="px-4 py-4">{{ message.email }}</td>
              <td class="max-w-md px-4 py-4 text-gray-600">
                <div class="line-clamp-4">{{ message.message }}</div>
                <div v-if="message.notes" class="mt-2 rounded-xl bg-white px-3 py-2 text-xs text-gray-500">
                  {{ message.notes }}
                </div>
              </td>
              <td class="px-4 py-4">
                <select
                  v-model="message.status"
                  class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm focus:border-red-500 focus:outline-none"
                  :disabled="updatingId === message.id"
                  @change="updateStatus(message, message.status)"
                >
                  <option value="pending">Pendiente</option>
                  <option value="attended">Atendido</option>
                  <option value="discarded">Descartado</option>
                </select>
              </td>
              <td class="rounded-r-2xl px-4 py-4 text-gray-500">
                <div>{{ formatDate(message.created_at) }}</div>
                <div class="mt-1 text-xs">{{ formatTime(message.created_at) }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import api from '@/plugins/axios'

const messages = ref([])
const loading = ref(true)
const error = ref('')
const updatingId = ref(null)

const formatDate = (value) => {
  if (!value) {
    return '-'
  }

  return new Date(value).toLocaleDateString('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const formatTime = (value) => {
  if (!value) {
    return '-'
  }

  return new Date(value).toLocaleTimeString('es-MX', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const fetchMessages = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await api.get('/admin/contact-messages')
    messages.value = response.data
  } catch (requestError) {
    error.value = requestError.userMessage || 'No se pudieron cargar los mensajes.'
  } finally {
    loading.value = false
  }
}

const updateStatus = async (message, nextStatus) => {
  const previousStatus = message.status
  updatingId.value = message.id

  try {
    await api.patch(`/admin/contact-messages/${message.id}`, {
      status: nextStatus,
      notes: message.notes || null
    })
  } catch (requestError) {
    message.status = previousStatus
    error.value = requestError.userMessage || 'No se pudo actualizar el estado.'
  } finally {
    updatingId.value = null
  }
}

onMounted(() => {
  fetchMessages()
})
</script>

<style scoped>
.line-clamp-4 {
  display: -webkit-box;
  -webkit-line-clamp: 4;
  line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
