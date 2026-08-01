<template>
  <div v-if="visible" class="fixed inset-0 z-40 flex items-center justify-center bg-black/20 backdrop-blur-sm">
    <div class="rounded-2xl bg-white p-8 shadow-2xl dark:bg-gray-900 flex flex-col items-center">
      <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-red-600 dark:border-gray-700 dark:border-t-red-500"></div>
      <p class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">{{ message }}</p>
      <p v-if="subtext" class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ subtext }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  message: {
    type: String,
    default: 'Guardando cambios...'
  },
  subtext: {
    type: String,
    default: ''
  }
})

const visible = ref(false)

const show = () => {
  visible.value = true
}

const hide = () => {
  visible.value = false
}

defineExpose({ show, hide })
</script>
