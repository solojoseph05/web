<!-- src/components/admin/DeleteConfirmModal.vue -->
<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      @click.self="emit('cancel')"
    >
      <div
        class="w-full max-w-md rounded-3xl bg-white p-6 sm:p-7 shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 text-center space-y-4"
        role="dialog"
        aria-modal="true"
      >
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400">
          <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
        </div>

        <div>
          <h3 class="text-lg font-black text-gray-900 dark:text-white">
            {{ title || (isBulk ? `¿Eliminar ${itemsCount} platillos del menú?` : '¿Eliminar elemento?') }}
          </h3>

          <div v-if="!isBulk" class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
            {{ message || `Estás a punto de eliminar "${itemName}". Ya no estará disponible en el sistema.` }}
          </div>

          <div v-else class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400 space-y-2">
            <p>
              {{ message || `Estás a punto de eliminar ${itemsCount} elementos seleccionados en lote.` }}
            </p>
            <div v-if="itemsList.length > 0" class="max-h-28 overflow-y-auto rounded-xl bg-gray-50 p-2 text-left text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
              <ul class="list-disc list-inside space-y-0.5">
                <li v-for="item in itemsList" :key="item" class="truncate font-semibold">
                  {{ item }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-center gap-3 pt-2">
          <button
            type="button"
            @click="emit('cancel')"
            class="flex-1 rounded-xl border border-gray-200 py-2.5 text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition cursor-pointer"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="emit('confirm')"
            class="flex-1 rounded-xl bg-red-600 py-2.5 text-xs font-bold text-white shadow-md hover:bg-red-700 transition cursor-pointer flex items-center justify-center gap-2"
          >
            <span>{{ isBulk ? `Sí, Eliminar (${itemsCount})` : 'Sí, Eliminar' }}</span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  itemName: {
    type: String,
    default: '',
  },
  itemsList: {
    type: Array,
    default: () => [],
  },
  isBulk: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: '',
  },
  message: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['confirm', 'cancel'])

const itemsCount = computed(() => {
  if (props.itemsList && props.itemsList.length > 0) {
    return props.itemsList.length
  }
  return 1
})
</script>
