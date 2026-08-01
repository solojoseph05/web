<template>
  <Teleport to="body">
    <div class="fixed top-5 right-5 z-40 space-y-3 max-w-sm">
      <transition-group name="toast">
        <div
          v-for="notification in notifications"
          :key="notification.id"
          :class="`rounded-lg px-4 py-3 text-sm font-medium text-white shadow-lg transition ${
            notification.type === 'success' ? 'bg-green-600' :
            notification.type === 'error' ? 'bg-red-600' :
            notification.type === 'warning' ? 'bg-yellow-600' :
            'bg-blue-600'
          }`"
        >
          <div class="flex items-center justify-between gap-4">
            <span>{{ notification.message }}</span>
            <div class="flex gap-2">
              <button
                v-if="notification.action"
                class="rounded px-2 py-1 text-xs font-semibold hover:bg-white/20 transition"
                @click="handleAction(notification.id, notification.action)"
              >
                {{ notification.actionLabel }}
              </button>
              <button
                class="rounded px-2 py-1 text-xs hover:bg-white/20 transition"
                @click="removeNotification(notification.id)"
              >
                ✕
              </button>
            </div>
          </div>
        </div>
      </transition-group>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'

const notifications = ref([])
let nextId = 0

const show = (message, type = 'info', duration = 3000, action = null, actionLabel = 'Deshacer') => {
  const id = nextId++
  notifications.value.push({ id, message, type, action, actionLabel })
  
  setTimeout(() => {
    removeNotification(id)
  }, duration)
}

const removeNotification = (id) => {
  notifications.value = notifications.value.filter(n => n.id !== id)
}

const handleAction = (id, action) => {
  removeNotification(id)
  action()
}

const success = (message, action = null, actionLabel = 'Deshacer') => show(message, 'success', 3000, action, actionLabel)
const error = (message, action = null, actionLabel = 'Reintentar') => show(message, 'error', 5000, action, actionLabel)
const warning = (message) => show(message, 'warning', 3000)
const info = (message) => show(message, 'info', 3000)

defineExpose({ show, success, error, warning, info, removeNotification })
</script>

<style scoped>
.toast-enter-active, .toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  transform: translateX(400px);
  opacity: 0;
}

.toast-leave-to {
  transform: translateX(400px);
  opacity: 0;
}
</style>

