<template>
  <div
    class="fixed top-4 right-4 sm:top-6 sm:right-6 z-50 flex w-[calc(100%-2rem)] sm:w-auto sm:max-w-sm flex-col gap-2 pointer-events-none"
    aria-live="polite"
    aria-atomic="true"
  >
    <TransitionGroup
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="pointer-events-auto flex items-start gap-3 rounded-2xl p-4 shadow-xl border backdrop-blur-md transition"
        :class="toastClasses(toast.type)"
      >
        <!-- Icon -->
        <div class="shrink-0 mt-0.5">
          <svg
            v-if="toast.type === 'success'"
            class="h-5 w-5 text-green-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg
            v-else-if="toast.type === 'error'"
            class="h-5 w-5 text-red-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          <svg
            v-else-if="toast.type === 'warning'"
            class="h-5 w-5 text-amber-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <svg
            v-else
            class="h-5 w-5 text-blue-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>

        <div class="flex-1 text-sm font-medium leading-snug">
          {{ toast.message }}
        </div>

        <button
          type="button"
          @click="removeToast(toast.id)"
          class="shrink-0 rounded-lg p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
          aria-label="Cerrar notificación"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useToasts } from '@/composables/useToasts'

const { toasts, removeToast } = useToasts()

const toastClasses = (type) => {
  switch (type) {
    case 'success':
      return 'bg-white/95 dark:bg-gray-900/95 border-green-200 dark:border-green-800 text-gray-900 dark:text-gray-100'
    case 'error':
      return 'bg-white/95 dark:bg-gray-900/95 border-red-200 dark:border-red-800 text-gray-900 dark:text-gray-100'
    case 'warning':
      return 'bg-white/95 dark:bg-gray-900/95 border-amber-200 dark:border-amber-800 text-gray-900 dark:text-gray-100'
    default:
      return 'bg-white/95 dark:bg-gray-900/95 border-gray-200 dark:border-gray-800 text-gray-900 dark:text-gray-100'
  }
}
</script>
