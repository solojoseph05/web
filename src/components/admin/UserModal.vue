<!-- src/components/admin/UserModal.vue -->
<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 my-8 transition-all"
        role="dialog"
        aria-modal="true"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-purple-100 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400 text-lg">
              👤
            </span>
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">
                {{ isEditing ? 'Editar Usuario y Rol' : 'Nuevo Usuario Administrativo' }}
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ isEditing ? 'Modifica los permisos, rol o contraseña de este usuario' : 'Crea una cuenta con acceso por rol al panel' }}
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

        <!-- Form Body -->
        <form @submit.prevent="handleSubmit" class="mt-5 space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Nombre Completo *
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="Ej. Roberto Garza"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white font-semibold"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Correo Electrónico *
            </label>
            <input
              v-model="form.email"
              type="email"
              required
              placeholder="usuario@pollofeliz.com"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              {{ isEditing ? 'Nueva Contraseña (dejar en blanco para mantener la actual)' : 'Contraseña de Acceso *' }}
            </label>
            <input
              v-model="form.password"
              type="password"
              :required="!isEditing"
              placeholder="••••••••"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-red-500 focus:bg-white focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white font-mono"
            />
          </div>

          <!-- Role Selector -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
              Rol de Usuario y Nivel de Acceso *
            </label>
            <div class="space-y-2">
              <label
                v-for="r in ROLES_CONFIG"
                :key="r.id"
                class="flex items-start gap-3 p-3 rounded-2xl border cursor-pointer transition"
                :class="form.role === r.id
                  ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30 dark:border-red-800 shadow-xs'
                  : 'border-gray-200 bg-gray-50/50 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800/40 dark:hover:bg-gray-800'"
              >
                <input
                  v-model="form.role"
                  type="radio"
                  :value="r.id"
                  name="user_role"
                  class="mt-0.5 h-4 w-4 text-red-600 focus:ring-red-500"
                />
                <div class="flex-1">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-black text-gray-900 dark:text-white">
                      {{ r.icon }} {{ r.label }}
                    </span>
                    <span
                      class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
                      :class="r.badgeClass"
                    >
                      {{ r.id }}
                    </span>
                  </div>
                  <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 leading-snug">
                    {{ r.description }}
                  </p>
                </div>
              </label>
            </div>
          </div>

          <!-- Action Buttons -->
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
              <span>{{ isEditing ? 'Guardar Cambios' : 'Crear Usuario' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import { ROLES_CONFIG } from '@/composables/useAdminAuth'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  userToEdit: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'save'])

const isEditing = ref(false)
const isSubmitting = ref(false)

const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'gerente',
})

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      if (props.userToEdit) {
        isEditing.value = true
        form.value = {
          name: props.userToEdit.name || '',
          email: props.userToEdit.email || '',
          password: '',
          role: props.userToEdit.role || 'gerente',
        }
      } else {
        isEditing.value = false
        form.value = {
          name: '',
          email: '',
          password: '',
          role: 'gerente',
        }
      }
    }
  }
)

const handleSubmit = async () => {
  if (!form.value.name.trim() || !form.value.email.trim()) return
  if (!isEditing.value && !form.value.password.trim()) return

  isSubmitting.value = true
  try {
    const payload = {
      name: form.value.name.trim(),
      email: form.value.email.trim(),
      role: form.value.role,
    }
    if (form.value.password.trim()) {
      payload.password = form.value.password.trim()
    }
    if (props.userToEdit?.id) {
      payload.id = props.userToEdit.id
    }
    emit('save', payload)
  } finally {
    isSubmitting.value = false
  }
}
</script>
