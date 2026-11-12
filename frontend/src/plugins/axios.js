import axios from 'axios'

const DEFAULT_ADMIN_TOKEN = 'default-admin-token-12345'

const api = axios.create({
  baseURL: '/api',
  timeout: 15000,
  headers: {
    'Content-Type': 'application/json',
  },
})

// Interceptor de solicitudes: adjunta el token JWT / Bearer y cabecera personalizada para sortear restricciones de proxies
api.interceptors.request.use((config) => {
  if (typeof window !== 'undefined') {
    const token =
      localStorage.getItem('pollofeliz_admin_token') ||
      sessionStorage.getItem('pollofeliz_admin_token') ||
      localStorage.getItem('token')

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
      config.headers['X-Admin-Token'] = token
    }
  }
  return config
})

// Interceptor de respuestas: maneja revocaciones forzadas de sesión, expiración de tokens y cuentas suspendidas
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (typeof window !== 'undefined' && error.response) {
      const status = error.response.status
      const isRevoked = status === 401 && error.response.data?.revoked
      const isSuspended = status === 403 && error.response.data?.suspended
      const isUnauthorized = status === 401

      if (isRevoked || isSuspended || isUnauthorized) {
        localStorage.removeItem('pollofeliz_admin_token')
        sessionStorage.removeItem('pollofeliz_admin_token')
        localStorage.removeItem('token')
        sessionStorage.removeItem('token')
        localStorage.removeItem('pollofeliz_admin_user')
        sessionStorage.removeItem('pollofeliz_admin_user')
        localStorage.removeItem('user')
        sessionStorage.removeItem('user')

        const currentPath = window.location.pathname
        if (currentPath.startsWith('/admin') && currentPath !== '/admin/login') {
          const reason = isSuspended ? 'account_suspended' : (isRevoked ? 'session_revoked' : 'invalid_session')
          window.location.href = `/admin/login?auth_error=${reason}`
        }
      }
    }
    return Promise.reject(error)
  }
)

export default api
