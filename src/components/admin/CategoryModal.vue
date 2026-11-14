<!-- src/components/admin/CategoryModal.vue -->
<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
      @click.self="emit('close')"
    >
      <div
        class="admin-panel relative w-full max-w-md rounded-3xl bg-white p-6 sm:p-7 shadow-2xl border border-gray-100 dark:bg-[#0f172a] dark:border-slate-800 my-8 transition-all"
        role="dialog"
        aria-modal="true"
      >
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 text-lg">
              📁
            </span>
            <div>
              <h3 class="text-base font-black text-gray-900 dark:text-white">
                {{ isEditing ? 'Editar Categoría' : 'Nueva Categoría de Menú' }}
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                Organiza y clasifica los platillos en la carta digital
              </p>
            </div>
          </div>
          <button
            @click="emit('close')"
            class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200 cursor-pointer"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="mt-4 space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Nombre de la Categoría *
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="Ej. Combos Familiares, Bebidas Frías, Especialidades..."
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 font-semibold transition"
              @input="handleNameInput"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Slug / Identificador URL
            </label>
            <input
              v-model="form.slug"
              type="text"
              placeholder="ej. combos-familiares"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs font-mono text-gray-700 placeholder-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 dark:placeholder-slate-400 transition"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Orden de Visualización
            </label>
            <input
              v-model.number="form.sort_order"
              type="number"
              min="1"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-900 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-hidden dark:border-slate-700 dark:bg-[#161e2e] dark:focus:bg-[#161e2e] dark:text-slate-100 transition"
            />
          </div>

          <div class="pt-1">
            <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4 rounded text-red-600 focus:ring-red-500"
              />
              <div>
                <span class="block text-xs font-bold text-gray-800 dark:text-gray-200">Categoría Activa</span>
                <span class="block text-[10px] text-gray-400">Visible como pestaña de filtrado en el menú</span>
              </div>
            </label>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
            <button
              type="button"
              @click="emit('close')"
              class="rounded-xl border border-gray-200 px-4 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 cursor-pointer transition"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:from-red-700 hover:to-red-800 disabled:opacity-50 transition cursor-pointer flex items-center gap-2"
            >
              <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <span>{{ isEditing ? 'Guardar Cambios' : 'Crear Categoría' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  categoryToEdit: {
    type: Object,
    default: null,
  },
  nextSortOrder: {
    type: Number,
    default: 1,
  },
})

const emit = defineEmits(['close', 'save'])

const isEditing = ref(false)
const isSubmitting = ref(false)

const form = ref({
  name: '',
  slug: '',
  sort_order: 1,
  is_active: true,
})

const handleNameInput = () => {
  if (!isEditing.value) {
    form.value.slug = form.value.name
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)/g, '')
  }
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      if (props.categoryToEdit) {
        isEditing.value = true
        form.value = {
          name: props.categoryToEdit.name || '',
          slug: props.categoryToEdit.slug || '',
          sort_order: props.categoryToEdit.sort_order ?? 1,
          is_active: props.categoryToEdit.is_active ?? true,
        }
      } else {
        isEditing.value = false
        form.value = {
          name: '',
          slug: '',
          sort_order: props.nextSortOrder || 1,
          is_active: true,
        }
      }
    }
  }
)

const handleSubmit = async () => {
  if (!form.value.name.trim()) return
  isSubmitting.value = true
  try {
    emit('save', {
      ...form.value,
      name: form.value.name.trim(),
      slug: form.value.slug.trim() || form.value.name.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-'),
      id: props.categoryToEdit?.id,
    })
  } finally {
    isSubmitting.value = false
  }
}
</script>
