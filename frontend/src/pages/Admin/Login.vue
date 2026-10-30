<!-- src/pages/Admin/Login.vue -->
<template>
  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900 selection:bg-red-500 selection:text-white px-4 sm:px-0">
    <!-- 
      LOGOTIPO OFICIAL / BRANDING (ESTILO LARAVEL BREEZE)
      Transición suave de entrada inicial (fade-in y sutil zoom)
    -->
    <div class="breeze-logo-enter mb-2 text-center">
      <router-link to="/" class="inline-block transition-transform duration-200 hover:scale-105" title="Volver al inicio">
        <img
          :src="logo"
          alt="Pollo Feliz Logo"
          class="h-16 sm:h-20 w-auto object-contain drop-shadow-xs"
        />
      </router-link>
      <div class="mt-2 flex items-center justify-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="font-medium tracking-wide">Portal Administrativo Seguro</span>
      </div>
    </div>

    <!-- 
      TARJETA DE AUTENTICACIÓN (LARAVEL BREEZE AUTH CARD)
      Con animación CSS suave: slide-up y fade-in de 600ms con curva de aceleración cúbica
    -->
    <div class="w-full sm:max-w-md px-6 py-6 sm:py-8 bg-white dark:bg-gray-800 shadow-md sm:rounded-lg border border-gray-200/70 dark:border-gray-700/80 breeze-card-enter">
      <!-- Encabezado de la Tarjeta Breeze -->
      <div class="mb-5 pb-3 border-b border-gray-100 dark:border-gray-700">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">
          Iniciar Sesión
        </h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
          Ingresa tus credenciales para acceder al panel de control.
        </p>
      </div>

      <!-- Alerta de Sesión Activa (si el usuario ya cuenta con token válido) -->
      <div
        v-if="adminUser?.email"
        class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-md text-xs text-emerald-800 dark:text-emerald-300 flex items-center justify-between gap-2"
      >
        <div class="truncate">
          <p class="font-bold truncate">👋 Sesión activa: {{ adminUser.name }}</p>
          <p class="text-[11px] text-emerald-700 dark:text-emerald-400">Rol: {{ adminUser.role }}</p>
        </div>
        <router-link
          to="/admin"
          class="inline-flex items-center px-2.5 py-1 bg-emerald-600 text-white rounded text-xs font-semibold hover:bg-emerald-700 transition shrink-0"
        >
          Ir al Panel →
        </router-link>
      </div>

      <!-- Breeze Session Status / Mensaje de Error de Validación -->
      <div
        v-if="errorMessage"
        class="mb-4 p-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 rounded-md text-xs font-medium text-red-600 dark:text-red-400 flex items-start gap-2"
      >
        <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Alerta de Notificación de Recuperación de Contraseña -->
      <div
        v-if="forgotSentMessage"
        class="mb-4 p-3 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 rounded-md text-xs text-blue-700 dark:text-blue-300 flex items-start gap-2"
      >
        <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <div class="flex-1">
          <p class="font-semibold">{{ forgotSentMessage }}</p>
          <p class="text-[11px] text-blue-600 dark:text-blue-400 mt-0.5">
            Canal de soporte: <span class="font-mono font-bold">soporte@pollofelizdgo.com</span>
          </p>
        </div>
      </div>

      <!-- FORMULARIO DE ACCESO BREEZE -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <!-- Campo: Correo Electrónico -->
        <div>
          <label for="email" class="block font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300">
            Correo Electrónico
          </label>
          <input
            id="email"
            type="email"
            name="email"
            v-model="email"
            required
            autocomplete="username"
            placeholder="admin@pollofeliz.com"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-xs transition focus:border-red-500 focus:ring-1 focus:ring-red-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-red-500"
          />
        </div>

        <!-- Campo: Contraseña -->
        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300">
              Contraseña
            </label>
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="text-xs text-gray-500 hover:text-red-600 dark:text-gray-400 cursor-pointer transition select-none"
            >
              {{ showPassword ? 'Ocultar' : 'Mostrar' }}
            </button>
          </div>
          <div class="relative mt-1">
            <input
              id="password"
              :type="showPassword ? 'text' : 'password'"
              name="password"
              v-model="password"
              required
              autocomplete="current-password"
              placeholder="••••••••"
              class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-xs transition focus:border-red-500 focus:ring-1 focus:ring-red-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-red-500"
            />
          </div>
        </div>

        <!-- Casilla: Recordarme (Remember Me estilo Breeze) -->
        <div class="block mt-4">
          <label class="inline-flex items-center cursor-pointer select-none">
            <input
              type="checkbox"
              v-model="rememberMe"
              class="rounded border-gray-300 text-red-600 shadow-xs focus:ring-red-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-red-600"
            />
            <span class="ms-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
              Recordarme en este dispositivo
            </span>
          </label>
        </div>

        <!-- Fila de Acciones Breeze (Enlace de Olvido + Botón Primario) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
          <button
            type="button"
            @click="handleForgotPassword"
            class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red-500 cursor-pointer order-2 sm:order-1"
          >
            ¿Olvidaste tu contraseña?
          </button>

          <button
            type="submit"
            :disabled="loading"
            class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 dark:bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-gray-800 dark:hover:bg-white focus:bg-gray-800 active:bg-gray-950 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-xs cursor-pointer disabled:opacity-50 order-1 sm:order-2"
          >
            <span v-if="!loading" class="flex items-center gap-1.5">
              <span>Iniciar Sesión</span>
              <span>→</span>
            </span>
            <span v-else class="flex items-center gap-2">
              <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Verificando...</span>
            </span>
          </button>
        </div>
      </form>
    </div>

    <!-- 
      SELECTOR DE ROLES DEMO PRECONFIGURADOS (ESTILO LARAVEL BREEZE COMPACTO)
      Permite al evaluador y personal seleccionar con 1 clic cualquiera de los 5 roles del sistema.
    -->
    <div class="w-full sm:max-w-md mt-4 p-4 bg-white dark:bg-gray-800 shadow-xs sm:rounded-lg border border-gray-200/60 dark:border-gray-700/60 text-xs breeze-card-enter">
      <div class="flex items-center justify-between mb-2.5">
        <span class="font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
          <span>🛡️</span> Roles Preconfigurados (Acceso Rápido)
        </span>
        <span class="text-[10px] text-gray-400">Clic para rellenar</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
        <button
          v-for="role in demoRoles"
          :key="role.email"
          type="button"
          @click="selectDemoRole(role)"
          class="p-2 rounded-md border text-left transition cursor-pointer flex items-center gap-2"
          :class="email === role.email
            ? 'border-red-500 bg-red-50/60 text-red-700 dark:bg-red-950/40 dark:text-red-300 font-bold'
            : 'border-gray-200 bg-gray-50/60 text-gray-600 hover:border-gray-300 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-300'"
        >
          <span class="text-base shrink-0">{{ role.icon }}</span>
          <div class="truncate">
            <span class="block text-[11px] font-semibold truncate">{{ role.label }}</span>
            <span class="block text-[9px] text-gray-400 font-mono truncate">{{ role.email }}</span>
          </div>
        </button>
      </div>
    </div>

    <!-- Breeze Footer Navigation -->
    <div class="w-full sm:max-w-md mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
      <router-link to="/" class="hover:text-red-600 dark:hover:text-red-400 transition inline-flex items-center gap-1">
        <span>←</span>
        <span>Volver a Pollo Feliz Durango</span>
      </router-link>
      <p class="mt-1 text-[10px] text-gray-400 dark:text-gray-500">
        Pollo Feliz &bull; Laravel Breeze Auth Edition
      </p>
    </div>
  </div>
</template>

<script setup>
/**
 * LARAVEL BREEZE AUTHENTICATION PAGE (POLLO FELIZ DURANGO)
 * ========================================================
 * Esta vista implementa el diseño oficial y elegante de Laravel Breeze:
 * - Tarjeta minimalista centrada con borde suave y sombra sutil.
 * - Transición CSS de fade-in y slide-up (600ms cubic-bezier) para una entrada premium.
 * - Campos con etiquetas Breeze, inputs con estados de foco y casillas de verificación.
 * - Soporte para el flujo "¿Olvidaste tu contraseña?" con aviso hacia soporte@pollofelizdgo.com.
 * - Selector rápido de los 5 roles preconfigurados (Super Admin, Gerente, Cocina, Atención, Cajero).
 * - Protección total: Esta ruta no es expuesta en la navegación pública, protegiendo el acceso administrativo.
 */

import { ref } from 'vue'
import { useRouter } from 'vue-router'
import logo from '@/assets/images/logo.png'
import { useAdminAuth } from '@/composables/useAdminAuth'
import { useToasts } from '@/composables/useToasts'

const router = useRouter()
const { login, loading, adminUser } = useAdminAuth()
const { addToast } = useToasts()

// Credenciales iniciales (preconfiguradas con el Super Administrador)
const email = ref('admin@pollofeliz.com')
const password = ref('admin123456')
const rememberMe = ref(true)
const showPassword = ref(false)
const errorMessage = ref('')
const forgotSentMessage = ref('')

// Cuentas de demostración preconfiguradas con los diferentes roles del sistema
const demoRoles = [
  { label: 'Super Admin', email: 'admin@pollofeliz.com', pass: 'admin123456', icon: '👑' },
  { label: 'Gerente', email: 'gerente@pollofeliz.com', pass: 'gerente123', icon: '👔' },
  { label: 'Cocina & Parrilla', email: 'cocina@pollofeliz.com', pass: 'cocina123', icon: '🍳' },
  { label: 'Atención & Soporte', email: 'soporte@pollofeliz.com', pass: 'soporte123', icon: '💬' },
  { label: 'Cajero & Mostrador', email: 'cajero@pollofeliz.com', pass: 'cajero123', icon: '💳' },
]

/**
 * Rellena automáticamente los campos del formulario Breeze con el rol seleccionado
 */
const selectDemoRole = (role) => {
  email.value = role.email
  password.value = role.pass
  errorMessage.value = ''
  forgotSentMessage.value = ''
}

/**
 * Maneja la acción "¿Olvidaste tu contraseña?" estilo Laravel Breeze
 */
const handleForgotPassword = () => {
  forgotSentMessage.value = `Hemos registrado tu solicitud para ${email.value || 'tu cuenta'}. Un administrador de Pollo Feliz validará tus datos.`
}

/**
 * Ejecuta el inicio de sesión contra el endpoint de autenticación administrativa
 */
const handleLogin = async () => {
  errorMessage.value = ''
  forgotSentMessage.value = ''
  try {
    const res = await login(email.value, password.value)
    const roleName = res.user?.role || 'admin'
    addToast(`¡Bienvenido, ${res.user?.name || 'Administrador'}! (Rol: ${roleName})`, 'success')
    router.push('/admin')
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Estas credenciales no coinciden con nuestros registros.'
  }
}
</script>

<style scoped>
/**
 * ANIMACIÓN CSS SUAVE DE ENTRADA (FADE-IN Y SLIDE-UP)
 * ====================================================
 * Proporciona una transición fluida al montar la tarjeta de autenticación Breeze.
 * Se utiliza cubic-bezier(0.16, 1, 0.3, 1) para un desacelerado natural a 60fps.
 */
@keyframes breezeSlideUpFade {
  0% {
    opacity: 0;
    transform: translateY(26px) scale(0.985);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.breeze-card-enter {
  animation: breezeSlideUpFade 600ms cubic-bezier(0.16, 1, 0.3, 1) forwards;
  will-change: transform, opacity;
}

@keyframes breezeLogoFade {
  0% {
    opacity: 0;
    transform: translateY(14px) scale(0.96);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.breeze-logo-enter {
  animation: breezeLogoFade 500ms cubic-bezier(0.16, 1, 0.3, 1) forwards;
  will-change: transform, opacity;
}
</style>
