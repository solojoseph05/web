import { reactive } from 'vue'

const toasts = reactive([])

let idCounter = 0

export const useToasts = () => {
  const addToast = (message, type = 'info') => {
    const id = ++idCounter
    toasts.push({ id, message, type })
    return id
  }

  const removeToast = (id) => {
    const index = toasts.findIndex((toast) => toast.id === id)
    if (index !== -1) {
      toasts.splice(index, 1)
    }
  }

  return {
    toasts,
    addToast,
    removeToast,
  }
}
