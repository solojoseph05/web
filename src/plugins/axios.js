import axios from 'axios'

const DEFAULT_ADMIN_TOKEN = 'default-admin-token-12345'

const api = axios.create({
  baseURL: '/api',
  timeout: 15000,
  headers: {
    'Content-Type': 'application/json',
  },
})

// Interceptor de solicitudes: adjunta el token JWT / Bearer si el usuario ha iniciado sesión
api.interceptors.request.use((config) => {
  if (typeof window !== 'undefined') {
    const token =
      localStorage.getItem('pollofeliz_admin_token') ||
      sessionStorage.getItem('pollofeliz_admin_token') ||
      localStorage.getItem('token')

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
  }
  return config
})

// Interceptor de respuestas: maneja 401 sin bucles infinitos ni inyecciones de tokens fantasmas
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const isLoginEndpoint = error.config?.url?.includes('/admin/auth/login')
    if (error.response?.status === 401 && !isLoginEndpoint && typeof window !== 'undefined') {
      // Limpiar almacenamiento si la sesión expiró o es inválida
      localStorage.removeItem('pollofeliz_admin_token')
      sessionStorage.removeItem('pollofeliz_admin_token')
      localStorage.removeItem('pollofeliz_admin_user')
      sessionStorage.removeItem('pollofeliz_admin_user')
    }
    return Promise.reject(error)
  }
)

export default api
