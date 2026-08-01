<template>
  <Teleport to="body">
    <div
      v-if="visible"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
      @click.self="cancel"
    >
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ dialogTitle }}</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ dialogMessage }}</p>

        <div class="mt-6 flex gap-3">
          <button
            class="flex-1 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
            @click="cancel"
          >
            Cancelar
          </button>
          <button
            :class="`flex-1 rounded-lg px-4 py-2 text-sm font-semibold text-white transition ${
              isDangerous ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'
            }`"
            @click="confirm"
          >
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  confirmText: {
    type: String,
    default: 'Confirmar'
  },
  isDangerous: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const visible = ref(false)
const dialogTitle = ref('Confirmar acción')
const dialogMessage = ref('¿Estás seguro de que deseas continuar?')

const show = (title = 'Confirmar acción', message = '¿Estás seguro?') => {
  dialogTitle.value = title
  dialogMessage.value = message
  visible.value = true
}

const confirm = () => {
  emit('confirm')
  visible.value = false
}

const cancel = () => {
  emit('cancel')
  visible.value = false
}

defineExpose({ show })
</script>
