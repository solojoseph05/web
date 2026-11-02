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

// Interceptor de respuestas: delega el control de sesión a useAdminAuth para evitar deslogueos falsos
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    return Promise.reject(error)
  }
)

export default api
