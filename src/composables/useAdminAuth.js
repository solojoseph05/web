// src/composables/useAdminAuth.js
/**
 * MÓDULO DE AUTENTICACIÓN Y CONTROL DE ACCESO BASADO EN ROLES (RBAC)
 * =================================================================
 * Este composable gestiona la sesión de los colaboradores y directivos de Pollo Feliz:
 * 1. Almacenamiento seguro del token de sesión y datos del usuario en localStorage.
 * 2. Matriz de roles y permisos con pestañas permitidas (ROLES_CONFIG).
 * 3. Métodos reactivos para verificar permisos: canAccessTab, isSuperAdmin, isGerente, etc.
 * 4. Inicio de sesión (login), cierre de sesión (logout) y sincronización con el backend (/admin/auth/me).
 */

import { ref, computed } from 'vue'
import api from '@/plugins/axios'

// Claves de almacenamiento local para persistencia de sesión
const TOKEN_KEY = 'pollofeliz_admin_token'
const USER_KEY = 'pollofeliz_admin_user'

// Usuario por defecto en caso de no existir datos guardados
const defaultAdminUser = {
  id: 1,
  name: 'Administrador General',
  email: 'admin@pollofeliz.com',
  role: 'super_admin',
}

// Estado reactivo global compartido entre todos los componentes
const savedToken = typeof window !== 'undefined' ? localStorage.getItem(TOKEN_KEY) : null
const savedUserRaw = typeof window !== 'undefined' && savedToken ? localStorage.getItem(USER_KEY) : null

let initialUser = null
if (savedUserRaw) {
  try {
    initialUser = JSON.parse(savedUserRaw)
  } catch (e) {
    initialUser = null
  }
}

const token = ref(savedToken)
const adminUser = ref(initialUser)
const loading = ref(false)
const error = ref(null)

/**
 * Comprueba si existe un token administrativo almacenado en el navegador
 */
export const hasAdminToken = () => {
  if (typeof window === 'undefined') return false
  return !!localStorage.getItem(TOKEN_KEY)
}

export const ROLES_CONFIG = [
  {
    id: 'super_admin',
    label: 'Super Administrador',
    icon: '👑',
    description: 'Acceso total: métricas, menú, diseño de landing, sucursales, pedidos, reseñas y gestión de usuarios.',
    badgeClass: 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/70 dark:text-purple-300 dark:border-purple-800',
    allowedTabs: ['summary', 'menu', 'landing', 'orders', 'promotions', 'branches', 'messages', 'reviews', 'users'],
  },
  {
    id: 'gerente',
    label: 'Gerente de Sucursal',
    icon: '👔',
    description: 'Gestión de operaciones: menú, pedidos, promociones, sucursales, analíticas, reseñas y atención al cliente.',
    badgeClass: 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/70 dark:text-blue-300 dark:border-blue-800',
    allowedTabs: ['summary', 'menu', 'orders', 'promotions', 'branches', 'messages', 'reviews'],
  },
  {
    id: 'cocina',
    label: 'Cocina & Parrilla',
    icon: '🍳',
    description: 'Pantalla operativa: preparación de pedidos, cambio de estados y consulta del menú.',
    badgeClass: 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/70 dark:text-amber-300 dark:border-amber-800',
    allowedTabs: ['orders', 'menu'],
  },
  {
    id: 'atencion',
    label: 'Atención al Cliente',
    icon: '💬',
    description: 'Atención de consultas web (soporte@pollofelizdgo.com), WhatsApp, reseñas de clientes y sucursales.',
    badgeClass: 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/70 dark:text-emerald-300 dark:border-emerald-800',
    allowedTabs: ['messages', 'branches', 'reviews'],
  },
  {
    id: 'cajero',
    label: 'Caja & Mostrador',
    icon: '💳',
    description: 'Cobro y despacho de pedidos, entrega a repartidores y catálogo de platillos.',
    badgeClass: 'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-950/70 dark:text-orange-300 dark:border-orange-800',
    allowedTabs: ['orders', 'menu'],
  },
]

export const getRoleMeta = (roleId) => {
  return ROLES_CONFIG.find((r) => r.id === roleId) || ROLES_CONFIG[0]
}

export const useAdminAuth = () => {
  const userRole = computed(() => adminUser.value?.role || 'super_admin')
  const isSuperAdmin = computed(() => userRole.value === 'super_admin')
  const isGerente = computed(() => userRole.value === 'gerente' || isSuperAdmin.value)
  const isCocina = computed(() => userRole.value === 'cocina')
  const isAtencion = computed(() => userRole.value === 'atencion')
  const isCajero = computed(() => userRole.value === 'cajero')

  const canAccessTab = (tabId) => {
    const meta = getRoleMeta(userRole.value)
    return meta.allowedTabs.includes(tabId)
  }

  const login = async (email, password) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/admin/auth/login', { email, password })
      const data = response.data
      token.value = data.token
      adminUser.value = data.user
      if (typeof window !== 'undefined') {
        localStorage.setItem(TOKEN_KEY, data.token)
        localStorage.setItem(USER_KEY, JSON.stringify(data.user))
      }
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al iniciar sesión como administrador.'
      throw err
    } finally {
      loading.value = false
    }
  }

  const logout = () => {
    token.value = null
    adminUser.value = null
    if (typeof window !== 'undefined') {
      localStorage.removeItem(TOKEN_KEY)
      localStorage.removeItem(USER_KEY)
    }
  }

  const fetchProfile = async () => {
    if (!token.value) return null
    try {
      const response = await api.get('/admin/auth/me')
      if (response.data?.user) {
        adminUser.value = response.data.user
        if (typeof window !== 'undefined') {
          localStorage.setItem(USER_KEY, JSON.stringify(response.data.user))
        }
      }
      return response.data?.user
    } catch (err) {
      if (err.response?.status === 401) {
        logout()
      }
      return null
    }
  }

  return {
    token,
    adminUser,
    userRole,
    isSuperAdmin,
    isGerente,
    isCocina,
    isAtencion,
    isCajero,
    canAccessTab,
    loading,
    error,
    login,
    logout,
    fetchProfile,
    hasAdminToken,
    ROLES_CONFIG,
    getRoleMeta,
  }
}

