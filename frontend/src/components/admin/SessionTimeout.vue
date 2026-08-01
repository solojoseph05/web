<template>
  <div v-if="showWarning" class="fixed top-4 left-4 right-4 z-50 rounded-xl bg-yellow-50 border-2 border-yellow-300 shadow-lg p-4 md:max-w-md md:left-auto md:right-4">
    <div class="flex items-start gap-4">
      <span class="text-3xl">⏰</span>
      <div class="flex-1">
        <h3 class="font-bold text-yellow-900">Tu sesión está por expirar</h3>
        <p class="text-sm text-yellow-800 mt-1">Se cerrará automáticamente en <strong>{{ minutesLeft }}:{{ secondsLeft }}</strong>.</p>
        <div class="flex gap-2 mt-3">
          <button
            class="flex-1 rounded-lg bg-yellow-600 px-3 py-2 text-sm font-semibold text-white hover:bg-yellow-700"
            @click="extendSession"
          >
            Continuar sesión
          </button>
          <button
            class="rounded-lg border border-yellow-300 px-3 py-2 text-sm font-semibold text-yellow-900 hover:bg-yellow-100"
            @click="logout"
          >
            Cerrar sesión
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import api from '@/plugins/axios'

const emit = defineEmits(['logout'])

defineProps({
  timeoutMinutes: {
    type: Number,
    default: 30
  },
  warningMinutes: {
    type: Number,
    default: 5
  }
})

const showWarning = ref(false)
const minutesLeft = ref(0)
const secondsLeft = ref(0)
let timeoutId = null
let warningTimeoutId = null
let countdownId = null
let lastActivityTime = Date.now()

const resetActivity = () => {
  lastActivityTime = Date.now()
  clearTimeout(timeoutId)
  clearTimeout(warningTimeoutId)
  clearInterval(countdownId)
  showWarning.value = false
  startTimer()
}

const startTimer = () => {
  const timeoutMs = 30 * 60 * 1000
  const warningMs = 5 * 60 * 1000

  warningTimeoutId = setTimeout(() => {
    showWarning.value = true
    startCountdown()
  }, timeoutMs - warningMs)

  timeoutId = setTimeout(() => {
    logout()
  }, timeoutMs)
}

const startCountdown = () => {
  countdownId = setInterval(() => {
    const elapsed = Math.floor((Date.now() - lastActivityTime) / 1000)
    const remaining = 30 * 60 - elapsed
    const mins = Math.floor(remaining / 60)
    const secs = remaining % 60

    minutesLeft.value = Math.max(0, mins)
    secondsLeft.value = Math.max(0, secs)

    if (remaining <= 0) {
      clearInterval(countdownId)
      logout()
    }
  }, 1000)
}

const extendSession = async () => {
  try {
    await api.get('/admin/auth/me')
    resetActivity()
  } catch (error) {
    logout()
  }
}

const logout = () => {
  emit('logout')
}

const handleActivity = () => {
  if (!showWarning.value) {
    resetActivity()
  }
}

onMounted(() => {
  startTimer()
  document.addEventListener('mousedown', handleActivity)
  document.addEventListener('keydown', handleActivity)
  document.addEventListener('scroll', handleActivity, { passive: true })
  document.addEventListener('touchstart', handleActivity, { passive: true })
})

onBeforeUnmount(() => {
  clearTimeout(timeoutId)
  clearTimeout(warningTimeoutId)
  clearInterval(countdownId)
  document.removeEventListener('mousedown', handleActivity)
  document.removeEventListener('keydown', handleActivity)
  document.removeEventListener('scroll', handleActivity)
  document.removeEventListener('touchstart', handleActivity)
})
</script>
