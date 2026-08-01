<template>
  <Teleport to="body">
    <div
      v-if="visible"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto"
      @click.self="closeEditor"
    >
      <div class="w-full max-w-2xl rounded-2xl bg-white p-8 shadow-2xl dark:bg-gray-900 my-8">
        <div class="flex items-start justify-between mb-6">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ title }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ subtitle }}</p>
          </div>
          <button
            class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800"
            @click="closeEditor"
          >
            ✕
          </button>
        </div>

        <div class="space-y-6 max-h-[calc(100vh-200px)] overflow-y-auto">
          <slot></slot>
        </div>

        <div class="mt-8 flex gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
          <button
            class="flex-1 rounded-lg border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
            @click="closeEditor"
          >
            Cancelar
          </button>
          <button
            class="flex-1 rounded-lg bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-700 disabled:bg-red-300 disabled:cursor-not-allowed dark:hover:bg-red-700"
            :disabled="isSaving"
            @click="saveEditor"
          >
            <span v-if="isSaving" class="inline-flex items-center gap-2">
              <span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
              Guardando...
            </span>
            <span v-else>Guardar cambios</span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  title: {
    type: String,
    default: 'Editar'
  },
  subtitle: {
    type: String,
    default: ''
  },
  isSaving: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['save', 'close'])

const visible = ref(false)

const show = () => {
  visible.value = true
  document.body.style.overflow = 'hidden'
}

const closeEditor = () => {
  visible.value = false
  document.body.style.overflow = ''
  emit('close')
}

const saveEditor = () => {
  emit('save')
}

defineExpose({ show, closeEditor })
</script>
