<!-- src/components/admin/UserModal.vue -->
<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-xl rounded-3xl bg-white shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 my-6 transition-all max-h-[92vh] flex flex-col overflow-hidden"
        role="dialog"
        aria-modal="true"
      >
        <!-- Modal Header (Pinned) -->
        <div class="flex items-center justify-between p-6 pb-4 border-b border-gray-100 dark:border-gray-800 shrink-0">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-purple-100 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400 text-lg">
              👥
            </span>
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">
                {{ isEditing ? 'Editar Cuenta & Accesos' : 'Nueva Cuenta Administrativa' }}
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ isEditing ? 'Modifica credenciales, estado y permisos del usuario' : 'Crea un nuevo acceso con credenciales, rol y permisos a medida' }}
              </p>
            </div>
          </div>
          <button
            @click="emit('close')"
            class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200 cursor-pointer transition"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Form Body (Scrollable) -->
        <div ref="formContainer" class="p-6 space-y-4 overflow-y-auto flex-1">
          <!-- Validation Warning Banner -->
          <div
            v-if="validationError"
            class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 dark:bg-rose-950/60 dark:border-rose-900 dark:text-rose-300 text-xs flex items-center gap-2 animate-pulse"
          >
            <span class="text-base">⚠️</span>
            <span class="font-bold">{{ validationError }}</span>
          </div>

          <!-- Name & Email Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Nombre Completo *
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Ej. Roberto Garza"
                class="w-full rounded-xl border px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-hidden dark:bg-gray-800 dark:text-white font-semibold transition"
                :class="errors.name
                  ? 'border-red-500 bg-red-50/50 dark:border-red-500 dark:bg-red-950/30 ring-1 ring-red-500'
                  : 'border-gray-200 bg-gray-50 focus:border-red-500 focus:bg-white dark:border-gray-700 dark:bg-gray-800 dark:focus:bg-gray-800 dark:focus:border-red-500'"
                @input="errors.name = ''"
              />
              <p v-if="errors.name" class="text-[11px] text-red-600 dark:text-red-400 font-bold mt-1">
                {{ errors.name }}
              </p>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                Correo Electrónico *
              </label>
              <input
                v-model="form.email"
                type="email"
                placeholder="usuario@pollofeliz.com"
                class="w-full rounded-xl border px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-hidden dark:bg-gray-800 dark:text-white transition"
                :class="errors.email
                  ? 'border-red-500 bg-red-50/50 dark:border-red-500 dark:bg-red-950/30 ring-1 ring-red-500'
                  : 'border-gray-200 bg-gray-50 focus:border-red-500 focus:bg-white dark:border-gray-700 dark:bg-gray-800 dark:focus:bg-gray-800 dark:focus:border-red-500'"
                @input="errors.email = ''"
              />
              <p v-if="errors.email" class="text-[11px] text-red-600 dark:text-red-400 font-bold mt-1">
                {{ errors.email }}
              </p>
            </div>
          </div>

          <!-- Password with Generator and Toggle -->
          <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 space-y-2">
            <div class="flex items-center justify-between">
              <label class="text-xs font-bold text-gray-700 dark:text-gray-300">
                {{ isEditing ? 'Nueva Contraseña (opcional)' : 'Contraseña de Acceso *' }}
              </label>
              <button
                type="button"
                @click="generateStrongPassword"
                class="text-[11px] font-bold text-purple-600 hover:text-purple-700 dark:text-purple-400 hover:underline cursor-pointer flex items-center gap-1"
              >
                <span>🎲 Generar Contraseña Segura</span>
              </button>
            </div>

            <div class="relative flex items-center">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••••••"
                class="w-full rounded-xl border px-3.5 py-2.5 pr-20 text-xs sm:text-sm text-gray-900 focus:outline-hidden dark:bg-gray-900 dark:text-white font-mono transition"
                :class="errors.password
                  ? 'border-red-500 bg-red-50/50 dark:border-red-500 ring-1 ring-red-500'
                  : 'border-gray-200 bg-white focus:border-red-500 dark:border-gray-600'"
                @input="errors.password = ''"
              />
              <div class="absolute right-2 flex items-center gap-1">
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg text-xs cursor-pointer"
                  :title="showPassword ? 'Ocultar' : 'Mostrar'"
                >
                  {{ showPassword ? '🙈' : '👁️' }}
                </button>
                <button
                  v-if="form.password"
                  type="button"
                  @click="copyPassword"
                  class="p-1.5 text-purple-600 hover:text-purple-800 dark:text-purple-400 rounded-lg text-xs cursor-pointer"
                  title="Copiar contraseña"
                >
                  📋
                </button>
              </div>
            </div>
            <p v-if="errors.password" class="text-[11px] text-red-600 dark:text-red-400 font-bold">
              {{ errors.password }}
            </p>
            <p v-else-if="generatedPasswordNotice" class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold">
              {{ generatedPasswordNotice }}
            </p>
          </div>

          <!-- Account Security Status -->
          <div class="p-3.5 rounded-2xl bg-gray-50/70 dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700">
            <div>
              <span class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                Estado de la Cuenta
              </span>
              <div class="flex items-center gap-2">
                <label
                  class="flex-1 flex items-center justify-center gap-1.5 py-1.5 px-2.5 rounded-xl border text-xs font-bold cursor-pointer transition"
                  :class="form.status === 'active'
                    ? 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-700 shadow-2xs'
                    : 'bg-white text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700'"
                >
                  <input type="radio" v-model="form.status" value="active" class="hidden" />
                  <span>✅ Activa</span>
                </label>
                <label
                  class="flex-1 flex items-center justify-center gap-1.5 py-1.5 px-2.5 rounded-xl border text-xs font-bold cursor-pointer transition"
                  :class="form.status === 'suspended'
                    ? 'bg-rose-50 text-rose-800 border-rose-300 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-700 shadow-2xs'
                    : 'bg-white text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700'"
                >
                  <input type="radio" v-model="form.status" value="suspended" class="hidden" />
                  <span>🚫 Suspendida</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Role Selector -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
              Rol Administrativo Principal *
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <label
                v-for="r in ROLES_CONFIG"
                :key="r.id"
                class="flex items-start gap-2.5 p-2.5 rounded-2xl border cursor-pointer transition"
                :class="form.role === r.id
                  ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30 dark:border-red-800 shadow-2xs'
                  : 'border-gray-200 bg-gray-50/50 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800/40 dark:hover:bg-gray-800'"
              >
                <input
                  v-model="form.role"
                  type="radio"
                  :value="r.id"
                  name="user_role"
                  @change="handleRoleChange(r.id)"
                  class="mt-0.5 h-3.5 w-3.5 text-red-600 focus:ring-red-500"
                />
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-1.5">
                    <span class="text-xs font-black text-gray-900 dark:text-white truncate">
                      {{ r.icon }} {{ r.label }}
                    </span>
                  </div>
                  <p class="text-[10px] text-gray-500 dark:text-gray-400 line-clamp-1 mt-0.5">
                    {{ r.description }}
                  </p>
                </div>
              </label>
            </div>
          </div>

          <!-- Fine-Grained Modular Permissions -->
          <div class="space-y-2.5 pt-2 border-t border-gray-100 dark:border-gray-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <label class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                <span>🔐</span>
                <span>Permisos Modulares Asignados ({{ form.permissions.length }}/{{ AVAILABLE_PERMISSIONS.length }})</span>
              </label>
              <div class="flex items-center gap-2 text-[11px]">
                <button
                  type="button"
                  @click="applyPermissionPreset('all')"
                  class="text-purple-600 dark:text-purple-400 hover:underline font-bold cursor-pointer"
                >
                  Marcar todos
                </button>
                <span>·</span>
                <button
                  type="button"
                  @click="applyPermissionPreset('clear')"
                  class="text-rose-500 hover:underline font-semibold cursor-pointer"
                >
                  Limpiar
                </button>
                <span>·</span>
                <button
                  type="button"
                  @click="resetPermissionsToRoleDefault"
                  class="text-gray-500 hover:underline cursor-pointer"
                >
                  Por rol
                </button>
              </div>
            </div>

            <!-- Quick Presets Row -->
            <div class="flex items-center gap-1.5 flex-wrap text-[11px]">
              <span class="text-[10px] font-bold uppercase text-gray-400 mr-0.5">Atajos:</span>
              <button
                type="button"
                @click="applyPermissionPreset('kitchen')"
                class="px-2 py-0.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-800 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-300 font-semibold cursor-pointer transition"
              >
                🍳 Cocina & Pedidos
              </button>
              <button
                type="button"
                @click="applyPermissionPreset('sales')"
                class="px-2 py-0.5 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 font-semibold cursor-pointer transition"
              >
                🍗 Menú & Promos
              </button>
              <button
                type="button"
                @click="applyPermissionPreset('support')"
                class="px-2 py-0.5 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-800 dark:border-blue-800 dark:bg-blue-950/40 dark:text-blue-300 font-semibold cursor-pointer transition"
              >
                💬 Atención & Reseñas
              </button>
              <button
                type="button"
                @click="applyPermissionPreset('web')"
                class="px-2 py-0.5 rounded-lg border border-pink-200 bg-pink-50 hover:bg-pink-100 text-pink-800 dark:border-pink-800 dark:bg-pink-950/40 dark:text-pink-300 font-semibold cursor-pointer transition"
              >
                🎨 Portada Web
              </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-52 overflow-y-auto p-1.5 bg-gray-50/50 dark:bg-gray-800/30 rounded-2xl border border-gray-200 dark:border-gray-700">
              <label
                v-for="perm in AVAILABLE_PERMISSIONS"
                :key="perm.id"
                class="flex items-start gap-2 p-2 rounded-xl border cursor-pointer transition text-xs"
                :class="form.permissions.includes(perm.id)
                  ? 'border-purple-300 bg-purple-50/50 dark:border-purple-800 dark:bg-purple-950/30 shadow-2xs'
                  : 'border-transparent hover:bg-gray-100 dark:hover:bg-gray-800'"
              >
                <input
                  type="checkbox"
                  :value="perm.id"
                  v-model="form.permissions"
                  class="mt-0.5 h-3.5 w-3.5 text-purple-600 rounded focus:ring-purple-500 border-gray-300"
                />
                <div class="min-w-0 flex-1">
                  <div class="font-bold text-gray-800 dark:text-gray-200 flex items-center justify-between">
                    <span class="flex items-center gap-1">
                      <span>{{ perm.icon }}</span>
                      <span>{{ perm.label }}</span>
                    </span>
                    <span v-if="form.permissions.includes(perm.id)" class="text-[10px] text-purple-600 dark:text-purple-400 font-bold">
                      ✓ Activo
                    </span>
                  </div>
                  <p class="text-[10px] text-gray-400 leading-tight mt-0.5">
                    {{ perm.desc }}
                  </p>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- Action Buttons (Pinned Sticky Footer) -->
        <div class="p-4 sm:p-5 border-t border-gray-100 bg-white dark:bg-gray-900 dark:border-gray-800 flex items-center justify-between gap-3 shrink-0">
          <div class="text-xs text-gray-500 dark:text-gray-400">
            <span class="text-red-500 font-bold">*</span> Campos requeridos
          </div>
          <div class="flex items-center gap-2.5">
            <button
              type="button"
              @click="emit('close')"
              class="rounded-xl border border-gray-200 px-4 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 cursor-pointer transition"
            >
              Cancelar
            </button>
            <button
              type="button"
              :disabled="isSubmitting"
              @click="handleSubmit"
              class="rounded-xl bg-linear-to-r from-red-600 to-red-700 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:from-red-700 hover:to-red-800 disabled:opacity-50 transition cursor-pointer flex items-center gap-2"
            >
              <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <span>{{ isEditing ? 'Guardar Cambios' : 'Crear Usuario' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, reactive, watch, nextTick } from 'vue'
import { ROLES_CONFIG } from '@/composables/useAdminAuth'
import { useToasts } from '@/composables/useToasts'

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
const { addToast } = useToasts()

const formContainer = ref(null)
const isEditing = ref(false)
const isSubmitting = ref(false)
const showPassword = ref(false)
const generatedPasswordNotice = ref('')
const validationError = ref('')

const errors = reactive({
  name: '',
  email: '',
  password: '',
})

const AVAILABLE_PERMISSIONS = [
  { id: 'menu.manage', label: 'Gestión de Menú', icon: '🍗', desc: 'Crear, editar precios y disponibilidad de platillos' },
  { id: 'orders.manage', label: 'Control de Pedidos', icon: '🛒', desc: 'Ver pedidos en tiempo real y cambiar estados' },
  { id: 'promotions.manage', label: 'Ofertas & Promociones', icon: '🏷️', desc: 'Crear cupones de descuento y paquetes especiales' },
  { id: 'branches.manage', label: 'Sucursales & Teléfonos', icon: '📍', desc: 'Gestionar direcciones, horarios y teléfonos' },
  { id: 'messages.manage', label: 'Bandeja de Consultas', icon: '📨', desc: 'Atender y responder mensajes de clientes' },
  { id: 'reviews.manage', label: 'Moderación de Reseñas', icon: '⭐', desc: 'Aprobar testimonios y responder opiniones de Google' },
  { id: 'jobs.manage', label: 'Bolsa de Trabajo & Vacantes', icon: '💼', desc: 'Gestionar puestos de empleo y revisar postulaciones' },
  { id: 'landing.manage', label: 'Editor de Landing Page', icon: '🎨', desc: 'Modificar textos, imágenes y diseño de la portada' },
  { id: 'users.manage', label: 'Gestión de Usuarios', icon: '👥', desc: 'Crear cuentas y cambiar roles del personal' },
  { id: 'security.manage', label: 'Auditoría & Seguridad', icon: '🛡️', desc: 'Revocar sesiones y monitorear accesos' },
]

const ROLE_PERMISSIONS_MAP = {
  super_admin: AVAILABLE_PERMISSIONS.map(p => p.id),
  gerente: ['menu.manage', 'orders.manage', 'promotions.manage', 'branches.manage', 'messages.manage', 'reviews.manage', 'jobs.manage'],
  reclutador: ['jobs.manage'],
  cocina: ['orders.manage', 'menu.manage'],
  atencion: ['messages.manage', 'branches.manage', 'reviews.manage'],
  cajero: ['orders.manage', 'menu.manage'],
}

const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'gerente',
  status: 'active',
  permissions: [...ROLE_PERMISSIONS_MAP.gerente],
})

const generateStrongPassword = () => {
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*'
  let result = ''
  for (let i = 0; i < 14; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length))
  }
  form.value.password = result
  showPassword.value = true
  errors.password = ''
  validationError.value = ''
  generatedPasswordNotice.value = '✓ Contraseña segura generada automáticamente'
  if (navigator?.clipboard) {
    navigator.clipboard.writeText(result).catch(() => {})
  }
}

const copyPassword = async () => {
  if (!form.value.password) return
  if (navigator?.clipboard) {
    try {
      await navigator.clipboard.writeText(form.value.password)
      addToast('Contraseña copiada al portapapeles', 'info')
    } catch (e) {
      // Fallback
    }
  }
}

const handleRoleChange = (roleId) => {
  const defaults = ROLE_PERMISSIONS_MAP[roleId] || []
  form.value.permissions = [...defaults]
}

const applyPermissionPreset = (presetKey) => {
  switch (presetKey) {
    case 'all':
      form.value.permissions = AVAILABLE_PERMISSIONS.map(p => p.id)
      break
    case 'kitchen':
      form.value.permissions = ['orders.manage', 'menu.manage']
      break
    case 'sales':
      form.value.permissions = ['menu.manage', 'promotions.manage', 'orders.manage']
      break
    case 'support':
      form.value.permissions = ['messages.manage', 'reviews.manage', 'branches.manage']
      break
    case 'web':
      form.value.permissions = ['landing.manage', 'promotions.manage']
      break
    case 'clear':
      form.value.permissions = []
      break
    default:
      resetPermissionsToRoleDefault()
  }
}

const resetPermissionsToRoleDefault = () => {
  handleRoleChange(form.value.role)
}

watch(
  () => props.isOpen,
  (val) => {
    generatedPasswordNotice.value = ''
    validationError.value = ''
    showPassword.value = false
    errors.name = ''
    errors.email = ''
    errors.password = ''

    if (val) {
      nextTick(() => {
        if (formContainer.value) {
          formContainer.value.scrollTop = 0
        }
      })

      if (props.userToEdit) {
        isEditing.value = true
        form.value = {
          name: props.userToEdit.name || '',
          email: props.userToEdit.email || '',
          password: '',
          role: props.userToEdit.role || 'gerente',
          status: props.userToEdit.status || 'active',
          permissions: Array.isArray(props.userToEdit.permissions)
            ? [...props.userToEdit.permissions]
            : [...(ROLE_PERMISSIONS_MAP[props.userToEdit.role] || ROLE_PERMISSIONS_MAP.gerente)],
        }
      } else {
        isEditing.value = false
        // Pre-generate a password so the field is ready
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*'
        let initialPass = ''
        for (let i = 0; i < 12; i++) {
          initialPass += chars.charAt(Math.floor(Math.random() * chars.length))
        }

        form.value = {
          name: '',
          email: '',
          password: initialPass,
          role: 'gerente',
          status: 'active',
          permissions: [...ROLE_PERMISSIONS_MAP.gerente],
        }
        showPassword.value = true
        generatedPasswordNotice.value = '✓ Contraseña segura sugerida automáticamente (puedes cambiarla)'
      }
    }
  }
)

const handleSubmit = async () => {
  errors.name = ''
  errors.email = ''
  errors.password = ''
  validationError.value = ''

  let hasError = false

  if (!form.value.name || !form.value.name.trim()) {
    errors.name = 'El nombre completo es requerido.'
    hasError = true
  }

  if (!form.value.email || !form.value.email.trim()) {
    errors.email = 'El correo electrónico es requerido.'
    hasError = true
  } else if (!form.value.email.includes('@') || !form.value.email.includes('.')) {
    errors.email = 'Por favor ingresa un correo electrónico válido.'
    hasError = true
  }

  if (!isEditing.value && (!form.value.password || !form.value.password.trim())) {
    errors.password = 'La contraseña es requerida para nuevos usuarios.'
    hasError = true
  }

  if (hasError) {
    validationError.value = 'Por favor completa los campos obligatorios (*) marcados arriba.'
    addToast('Por favor completa todos los campos requeridos antes de guardar.', 'warning')
    if (formContainer.value) {
      formContainer.value.scrollTo({ top: 0, behavior: 'smooth' })
    }
    return
  }

  isSubmitting.value = true
  try {
    const payload = {
      name: form.value.name.trim(),
      email: form.value.email.trim().toLowerCase(),
      role: form.value.role,
      status: form.value.status,
      permissions: form.value.permissions,
    }
    if (form.value.password && form.value.password.trim()) {
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
