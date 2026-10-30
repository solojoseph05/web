import { ref } from 'vue'

const toasts = ref([])
let nextToastId = 1

export const useToasts = () => {
  const addToast = (message, type = 'info', duration = 3000) => {
    const id = nextToastId++
    const toast = { id, message, type }
    toasts.value.push(toast)

    setTimeout(() => {
      removeToast(id)
    }, duration)

    return id
  }

  const removeToast = (id) => {
    const idx = toasts.value.findIndex((t) => t.id === id)
    if (idx > -1) {
      toasts.value.splice(idx, 1)
    }
  }

  return {
    toasts,
    addToast,
    removeToast,
  }
}
