<template>
  <div class="fixed right-4 top-4 z-[200] flex w-[min(24rem,calc(100vw-2rem))] flex-col gap-2" role="status" aria-live="polite">
    <div
      v-for="toast in toasts"
      :key="toast.id"
      class="rounded-2xl border px-4 py-3 shadow-lg backdrop-blur"
      :class="toastClasses(toast.type)"
    >
      <div class="flex items-start justify-between gap-3">
        <p class="text-sm font-medium text-gray-900">{{ toast.message }}</p>
        <button
          type="button"
          class="rounded-full p-1 text-sm text-gray-500 transition hover:bg-black/5 hover:text-gray-700"
          @click="removeToast(toast.id)"
          aria-label="Cerrar notificación"
        >
          ×
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useToasts } from '@/composables/useToasts'

const { toasts, removeToast } = useToasts()

const toastClasses = (type) => {
  switch (type) {
    case 'success':
      return 'border-emerald-200 bg-emerald-50'
    case 'error':
      return 'border-red-200 bg-red-50'
    default:
      return 'border-gray-200 bg-white'
  }
}
</script>
