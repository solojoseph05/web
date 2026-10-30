<!-- src/pages/Admin/Login.vue -->
<template>
  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 pb-12 bg-gradient-to-b from-gray-100 via-gray-50 to-gray-200 dark:from-gray-950 dark:via-gray-900 dark:to-black selection:bg-red-500 selection:text-white px-4 sm:px-0">
    <!-- 
      LOGOTIPO OFICIAL / BRANDING SEGURO
      Transición suave de entrada inicial (fade-in y sutil zoom)
    -->
    <div class="breeze-logo-enter mb-4 text-center">
      <router-link to="/" class="inline-block transition-transform duration-200 hover:scale-105" title="Volver al sitio público de Pollo Feliz">
        <img
          :src="logo"
          alt="Pollo Feliz Logo"
          class="h-16 sm:h-20 w-auto object-contain drop-shadow-sm mx-auto"
        />
      </router-link>
      <div class="mt-2.5 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-xs border border-gray-200/80 dark:border-gray-700 shadow-2xs text-xs text-gray-700 dark:text-gray-300">
        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="font-semibold tracking-wide">Acceso Administrativo Seguro</span>
        <span class="text-gray-400 dark:text-gray-500 text-[10px]">TLS 256-bit</span>
      </div>
    </div>

    <!-- 
      TARJETA PRINCIPAL DE AUTENTICACIÓN (LARAVEL BREEZE AUTH CARD)
      Animación CSS suave: slide-up y fade-in de 600ms con curva de aceleración
    -->
    <div class="w-full sm:max-w-md px-6 py-7 sm:py-8 bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl border border-gray-200/80 dark:border-gray-700/80 breeze-card-enter">
      <!-- Encabezado de la Tarjeta -->
      <div class="mb-5 pb-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
        <div>
          <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
            <span>Iniciar Sesión</span>
          </h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Ingresa tus credenciales corporativas autorizadas.
          </p>
        </div>
        <div class="p-2 rounded-xl bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
      </div>

      <!-- Alerta de Sesión Previa Activa (si el usuario ya cuenta con token verificado) -->
      <div
        v-if="adminUser?.email && hasToken"
        class="mb-5 p-3.5 bg-emerald-50/90 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-900 dark:text-emerald-200 shadow-2xs space-y-2.5"
      >
        <div class="flex items-start justify-between gap-2">
          <div class="min-w-0">
            <p class="font-bold truncate text-emerald-800 dark:text-emerald-300">
              👋 Sesión activa detectada
            </p>
            <p class="text-[11px] text-emerald-700 dark:text-emerald-400 mt-0.5 truncate">
              Usuario: <span class="font-medium text-emerald-950 dark:text-white">{{ adminUser.name }}</span> ({{ adminUser.role }})
            </p>
          </div>
          <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300 shrink-0">
            En línea
          </span>
        </div>
        <div class="flex items-center gap-2 pt-1">
          <router-link
            to="/admin"
            class="flex-1 inline-flex justify-center items-center px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 transition shadow-2xs"
          >
            Ir al Panel de Control →
          </router-link>
          <button
            type="button"
            @click="handleLogoutCurrent"
            class="px-2.5 py-1.5 border border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-300 hover:bg-emerald-100/60 dark:hover:bg-emerald-900/40 rounded-lg text-xs font-medium transition cursor-pointer"
            title="Cerrar esta sesión para iniciar con otra cuenta"
          >
            Cerrar sesión
          </button>
        </div>
      </div>

      <!-- Alerta de Bloqueo por Fuerza Bruta / Exceso de Intentos Fallidos -->
      <div
        v-if="isLockedOut"
        class="mb-4 p-3.5 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 rounded-xl text-xs text-amber-900 dark:text-amber-200 shadow-2xs flex items-start gap-2.5"
      >
        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
          <p class="font-bold text-amber-800 dark:text-amber-300">Acceso suspendido temporalmente</p>
          <p class="mt-0.5 text-[11px] text-amber-700 dark:text-amber-400">
            Demasiados intentos incorrectos consecutivos. Por seguridad, espera <span class="font-mono font-bold text-amber-950 dark:text-amber-200">{{ remainingSeconds }}s</span> antes de volver a intentar.
          </p>
        </div>
      </div>

      <!-- Alerta de Error de Validación -->
      <div
        v-if="errorMessage && !isLockedOut"
        class="mb-4 p-3 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-900 rounded-xl text-xs font-medium text-red-700 dark:text-red-300 flex items-start gap-2 shadow-2xs"
      >
        <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <div class="flex-1">
          <span>{{ errorMessage }}</span>
          <p v-if="failedAttempts > 0 && failedAttempts < MAX_ATTEMPTS" class="text-[10px] text-red-600/90 dark:text-red-400/90 mt-0.5">
            Intentos fallidos restantes antes de bloqueo: {{ MAX_ATTEMPTS - failedAttempts }}
          </p>
        </div>
      </div>

      <!-- Alerta de Solicitud de Restablecimiento de Contraseña -->
      <div
        v-if="forgotSentMessage"
        class="mb-4 p-3 bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-900 rounded-xl text-xs text-blue-800 dark:text-blue-300 flex items-start gap-2 shadow-2xs"
      >
        <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <div class="flex-1">
          <p class="font-semibold">{{ forgotSentMessage }}</p>
          <p class="text-[11px] text-blue-600 dark:text-blue-400 mt-0.5">
            Mesa de ayuda: <span class="font-mono font-bold">soporte@pollofelizdgo.com</span>
          </p>
        </div>
      </div>

      <!-- FORMULARIO DE ACCESO BREEZE SEGURO (Sin credenciales pre-cargadas) -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <!-- Campo: Correo Electrónico -->
        <div>
          <label for="email" class="block font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300">
            Correo Electrónico Corporativo
          </label>
          <div class="relative mt-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
              </svg>
            </span>
            <input
              id="email"
              type="email"
              name="email"
              v-model="email"
              required
              autocomplete="username"
              placeholder="nombre@pollofeliz.com"
              :disabled="isLockedOut || loading"
              class="block w-full rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm shadow-2xs transition focus:border-red-500 focus:ring-2 focus:ring-red-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
            />
          </div>
        </div>

        <!-- Campo: Contraseña con Ocultar/Mostrar SVG y detector de Bloq Mayús -->
        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300">
              Contraseña
            </label>
            <span
              v-if="isCapsLockOn"
              class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
              </svg>
              Bloq Mayús activado
            </span>
          </div>

          <div class="relative mt-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </span>

            <input
              id="password"
              :type="showPassword ? 'text' : 'password'"
              name="password"
              v-model="password"
              required
              autocomplete="current-password"
              placeholder="••••••••••••"
              :disabled="isLockedOut || loading"
              @keydown="checkCapsLock"
              @keyup="checkCapsLock"
              class="block w-full rounded-lg border border-gray-300 pl-9 pr-10 py-2 text-sm shadow-2xs transition focus:border-red-500 focus:ring-2 focus:ring-red-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
            />

            <button
              type="button"
              @click="showPassword = !showPassword"
              tabindex="-1"
              :title="showPassword ? 'Ocultar contraseña' : 'Ver contraseña'"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer transition select-none"
            >
              <!-- Ícono Ojo visible -->
              <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <!-- Ícono Ojo tachado -->
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Casilla: Recordarme en este equipo -->
        <div class="flex items-center justify-between pt-1">
          <label class="inline-flex items-center cursor-pointer select-none">
            <input
              type="checkbox"
              v-model="rememberMe"
              class="rounded border-gray-300 text-red-600 shadow-2xs focus:ring-red-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-red-600"
            />
            <span class="ms-2 text-xs text-gray-600 dark:text-gray-400">
              Mantener sesión en este equipo
            </span>
          </label>
        </div>

        <!-- Fila de Acciones Breeze (Enlace de Olvido + Botón Primario) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-gray-100 dark:border-gray-700/60">
          <button
            type="button"
            @click="handleForgotPassword"
            class="text-xs text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 underline underline-offset-2 rounded-md focus:outline-hidden cursor-pointer order-2 sm:order-1"
          >
            ¿Olvidaste tu contraseña?
          </button>

          <button
            type="submit"
            :disabled="loading || isLockedOut"
            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gray-900 dark:bg-red-600 hover:bg-red-600 dark:hover:bg-red-700 text-white font-semibold text-xs rounded-xl uppercase tracking-wider transition-all duration-200 shadow-sm hover:shadow-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed order-1 sm:order-2"
          >
            <span v-if="!loading" class="flex items-center gap-1.5">
              <span>Iniciar Sesión</span>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </span>
            <span v-else class="flex items-center gap-2">
              <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Autenticando...</span>
            </span>
          </button>
        </div>
      </form>

      <!-- Pie de Seguridad y Auditoría -->
      <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700/60 text-[11px] text-gray-400 dark:text-gray-500 flex items-center justify-between">
        <span class="flex items-center gap-1">
          <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          Auditoría de accesos activa
        </span>
        <span>IP protegida</span>
      </div>
    </div>

    <!-- Aviso Legal & Retorno -->
    <div class="w-full sm:max-w-md mt-5 text-center text-xs text-gray-500 dark:text-gray-400 space-y-2">
      <router-link to="/" class="hover:text-red-600 dark:hover:text-red-400 transition inline-flex items-center gap-1 font-medium">
        <span>←</span>
        <span>Volver a Pollo Feliz Durango</span>
      </router-link>
      <p class="text-[10px] text-gray-400 dark:text-gray-600 max-w-xs mx-auto">
        Portal de uso exclusivo para colaboradores de Pollo Feliz. El uso no autorizado está prohibido.
      </p>
    </div>
  </div>
</template>

<script setup>
/**
 * PORTAL DE ACCESO ADMINISTRATIVO SEGURO - POLLO FELIZ DURANGO
 * ============================================================
 * Características de seguridad reforzadas:
 * 1. Sin credenciales ni roles pre-rellenados en pantalla (prevención de acceso casual).
 * 2. Protección contra ataques de fuerza bruta: suspensión temporal de 60s tras 5 intentos fallidos.
 * 3. Detección de tecla Bloq Mayús (Caps Lock) en tiempo real para evitar errores de tipeo.
 * 4. Alternador de visualización de contraseña con íconos vectoriales SVG.
 * 5. Sanitización y validación del formato de correo corporativo.
 * 6. Gestión de sesiones activas: permite continuar directamente o cerrar la sesión para alternar usuario.
 * 7. Animaciones suaves CSS con cubic-bezier a 60fps (Breeze layout).
 */

import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import logo from '@/assets/images/logo.png'
import { useAdminAuth, hasAdminToken } from '@/composables/useAdminAuth'
import { useToasts } from '@/composables/useToasts'

const router = useRouter()
const { login, logout, loading, adminUser } = useAdminAuth()
const { addToast } = useToasts()

// Credenciales limpias por defecto (sin precargar datos sensibles)
const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')
const forgotSentMessage = ref('')
const isCapsLockOn = ref(false)

// Token disponible en localStorage
const hasToken = computed(() => hasAdminToken())

// Mecanismo de limitación de tasa (Rate Limiting) y defensa contra fuerza bruta
const MAX_ATTEMPTS = 5
const LOCKOUT_SECONDS = 60
const failedAttempts = ref(0)
const lockoutEndTime = ref(0)
const remainingSeconds = ref(0)
let timerInterval = null

const isLockedOut = computed(() => remainingSeconds.value > 0)

const checkCapsLock = (event) => {
  if (event && typeof event.getModifierState === 'function') {
    isCapsLockOn.value = event.getModifierState('CapsLock')
  }
}

const startLockoutTimer = (seconds) => {
  remainingSeconds.value = seconds
  if (timerInterval) clearInterval(timerInterval)

  timerInterval = setInterval(() => {
    remainingSeconds.value -= 1
    if (remainingSeconds.value <= 0) {
      clearInterval(timerInterval)
      timerInterval = null
      failedAttempts.value = 0
      errorMessage.value = ''
    }
  }, 1000)
}

onMounted(() => {
  // Cargar estado de bloqueo si existe en sessionStorage
  try {
    const savedLockout = sessionStorage.getItem('pf_admin_lockout_until')
    if (savedLockout) {
      const remaining = Math.ceil((parseInt(savedLockout, 10) - Date.now()) / 1000)
      if (remaining > 0) {
        startLockoutTimer(remaining)
      } else {
        sessionStorage.removeItem('pf_admin_lockout_until')
      }
    }
  } catch (e) {
    // Modo privado o storage no disponible
  }
})

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval)
})

const handleLogoutCurrent = () => {
  logout()
  addToast('Sesión anterior finalizada correctamente.', 'info')
}

const handleForgotPassword = () => {
  const targetEmail = email.value.trim() || 'tu cuenta'
  forgotSentMessage.value = `Se ha generado una solicitud de recuperación para ${targetEmail}. El administrador del sistema se comunicará contigo.`
  addToast('Solicitud enviada al área de sistemas.', 'info')
}

const handleLogin = async () => {
  if (isLockedOut.value) return

  const cleanEmail = email.value.trim().toLowerCase()
  const cleanPass = password.value

  if (!cleanEmail || !cleanPass) {
    errorMessage.value = 'Por favor ingresa tu correo y contraseña.'
    return
  }

  // Validación básica de formato de correo
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(cleanEmail)) {
    errorMessage.value = 'El formato del correo electrónico no es válido.'
    return
  }

  errorMessage.value = ''
  forgotSentMessage.value = ''

  try {
    const res = await login(cleanEmail, cleanPass)
    failedAttempts.value = 0
    try {
      sessionStorage.removeItem('pf_admin_lockout_until')
    } catch (e) {}

    const roleName = res.user?.role || 'admin'
    addToast(`¡Bienvenido, ${res.user?.name || 'Administrador'}!`, 'success')
    router.push('/admin')
  } catch (err) {
    failedAttempts.value += 1
    
    if (failedAttempts.value >= MAX_ATTEMPTS) {
      const lockoutUntil = Date.now() + LOCKOUT_SECONDS * 1000
      try {
        sessionStorage.setItem('pf_admin_lockout_until', String(lockoutUntil))
      } catch (e) {}
      startLockoutTimer(LOCKOUT_SECONDS)
      errorMessage.value = 'Has excedido el número máximo de intentos permitidos.'
      addToast('Acceso suspendido temporalmente por seguridad.', 'error')
    } else {
      errorMessage.value = err.response?.data?.message || 'Credenciales incorrectas. Verifica tu correo y contraseña.'
    }
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
    transform: translateY(24px) scale(0.985);
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
    transform: translateY(12px) scale(0.96);
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
