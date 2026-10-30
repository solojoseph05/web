<!-- src/pages/Admin/Login.vue -->
<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 sm:p-6 dark:bg-gray-950">
    <div class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-10 shadow-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
      <!-- Brand Header -->
      <div class="text-center mb-6">
        <router-link to="/" class="inline-block mb-3 group" title="Volver al Sitio Web">
          <img :src="logo" alt="Pollo Feliz" class="h-16 w-auto mx-auto object-contain transition-transform group-hover:scale-105" />
        </router-link>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700 dark:bg-red-950/60 dark:text-red-300 mb-2">
          <span>🔒</span> Acceso Administrativo Seguro
        </span>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">Iniciar Sesión</h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
          Ingresa con tu cuenta asignada para gestionar pedidos, reseñas, menú y sucursales.
        </p>
      </div>

      <!-- Active Session Alert (if already logged in) -->
      <div
        v-if="adminUser?.email"
        class="mb-6 rounded-2xl bg-amber-50 border border-amber-200 p-4 text-xs dark:bg-amber-950/40 dark:border-amber-900/60 flex items-center justify-between gap-3"
      >
        <div class="flex items-center gap-2.5">
          <span class="text-xl">👋</span>
          <div>
            <span class="font-bold text-gray-800 dark:text-gray-200 block">
              Sesión activa: {{ adminUser.name }}
            </span>
            <span class="text-[11px] text-gray-500 dark:text-gray-400">
              Rol: {{ adminUser.role }} ({{ adminUser.email }})
            </span>
          </div>
        </div>
        <router-link
          to="/admin"
          class="rounded-xl bg-amber-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-amber-700 transition shrink-0"
        >
          Ir al Panel →
        </router-link>
      </div>

      <!-- Error Alert -->
      <div
        v-if="errorMessage"
        class="mb-6 rounded-2xl bg-red-50 border border-red-200 p-4 text-xs font-medium text-red-700 dark:bg-red-950/50 dark:border-red-900 dark:text-red-300"
      >
        {{ errorMessage }}
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
            Correo Electrónico
          </label>
          <div class="relative">
            <input
              v-model="email"
              type="email"
              required
              autocomplete="email"
              placeholder="admin@pollofeliz.com"
              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
              Contraseña
            </label>
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="text-[11px] text-gray-500 hover:text-red-600 dark:text-gray-400 cursor-pointer"
            >
              {{ showPassword ? 'Ocultar' : 'Mostrar clave' }}
            </button>
          </div>
          <div class="relative">
            <input
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              required
              autocomplete="current-password"
              placeholder="••••••••"
              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white pr-10"
            />
          </div>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-xl bg-linear-to-r from-red-600 to-red-700 py-3.5 text-sm font-bold text-white shadow-lg transition hover:from-red-700 hover:to-red-800 disabled:opacity-50 cursor-pointer min-h-[48px] flex items-center justify-center gap-2"
        >
          <span v-if="!loading" class="flex items-center gap-2">
            <span>Iniciar Sesión en el Panel</span>
            <span>→</span>
          </span>
          <span v-else class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span>Verificando credenciales...</span>
          </span>
        </button>
      </form>

      <!-- Demo Credentials Helper with 5-role Selector -->
      <div class="mt-6 rounded-2xl bg-gray-50 p-4 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/60 space-y-2.5">
        <div class="flex items-center justify-between">
          <p class="text-xs font-bold text-gray-700 dark:text-gray-300">
            Cuentas y Roles Preconfigurados:
          </p>
          <span class="text-[10px] text-gray-400">Clic para rellenar formulario</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
          <button
            v-for="role in demoRoles"
            :key="role.email"
            type="button"
            @click="selectDemoRole(role)"
            class="text-left p-2.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-2.5"
            :class="email === role.email
              ? 'border-red-600 bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-300 font-bold shadow-xs'
              : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300'"
          >
            <span class="text-lg">{{ role.icon }}</span>
            <div class="truncate">
              <span class="block font-bold text-[11px] truncate">{{ role.label }}</span>
              <span class="block text-[9px] text-gray-400 font-mono truncate">{{ role.email }}</span>
            </div>
          </button>
        </div>
      </div>

      <div class="mt-6 flex items-center justify-between text-xs text-gray-500">
        <router-link to="/" class="hover:text-red-600 transition flex items-center gap-1">
          <span>←</span>
          <span>Volver al Sitio Web</span>
        </router-link>
        <span class="text-[10px] text-gray-400">Pollo Feliz Durango v2.5</span>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * COMPONENTE DE INICIO DE SESIÓN ADMINISTRATIVO (LOGIN)
 * ====================================================
 * Provee acceso seguro al panel de control de Pollo Feliz Durango:
 * - Soporta autenticación con correo y contraseña.
 * - Incluye selector rápido para probar los 5 roles preconfigurados (Super Admin, Gerente, Cocina, Atención, Cajero).
 * - Notifica si ya existe una sesión activa y permite navegar directamente al panel.
 * - Alternador para ver/ocultar contraseña.
 * - Manejo robusto de errores con mensajes comprensibles para el usuario.
 */
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import logo from '@/assets/images/logo.png'
import { useAdminAuth } from '@/composables/useAdminAuth'
import { useToasts } from '@/composables/useToasts'

const router = useRouter()
const { login, loading, adminUser } = useAdminAuth()
const { addToast } = useToasts()

// Credenciales en el formulario (por defecto se pre-llenan con el Super Administrador)
const email = ref('admin@pollofeliz.com')
const password = ref('admin123456')
const showPassword = ref(false)
const errorMessage = ref('')

// Cuentas de demostración preconfiguradas con diferentes niveles de acceso
const demoRoles = [
  { label: 'Super Admin', email: 'admin@pollofeliz.com', pass: 'admin123456', icon: '👑' },
  { label: 'Gerente', email: 'gerente@pollofeliz.com', pass: 'gerente123', icon: '👔' },
  { label: 'Cocina & Parrilla', email: 'cocina@pollofeliz.com', pass: 'cocina123', icon: '🍳' },
  { label: 'Atención & Soporte', email: 'soporte@pollofeliz.com', pass: 'soporte123', icon: '💬' },
  { label: 'Cajero & Mostrador', email: 'cajero@pollofeliz.com', pass: 'cajero123', icon: '💳' },
]

/**
 * Rellena automáticamente los campos del formulario con el rol seleccionado
 */
const selectDemoRole = (role) => {
  email.value = role.email
  password.value = role.pass
  errorMessage.value = ''
}

/**
 * Ejecuta el inicio de sesión contra el endpoint /api/admin/auth/login
 * Al tener éxito, almacena el token, notifica con un Toast y redirige al panel
 */
const handleLogin = async () => {
  errorMessage.value = ''
  try {
    const res = await login(email.value, password.value)
    const roleName = res.user?.role || 'admin'
    addToast(`¡Bienvenido, ${res.user?.name || 'Administrador'}! (Rol: ${roleName})`, 'success')
    router.push('/admin')
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Error de credenciales. Por favor revisa tus datos.'
  }
}
</script>
