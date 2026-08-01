import axios from 'axios'
import { clearAdminToken, getAdminToken } from '@/composables/useAdminAuth'

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || '/api',
    timeout: 15000,
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
    }
})

api.interceptors.response.use(
    (response) => response,
    (error) => {
        const requestUrl = error.config?.url || ''

        if (error.response?.status === 401 && requestUrl.includes('/admin/')) {
            clearAdminToken()
        }

        const validationErrors = error.response?.data?.errors
        const firstValidationError = validationErrors && Object.values(validationErrors).flat().find(Boolean)
        const message =
            error.response?.data?.message ||
            error.response?.data?.error ||
            firstValidationError ||
            error.message ||
            'No se pudo completar la solicitud.'

        error.userMessage = message
        return Promise.reject(error)
    }
)

api.interceptors.request.use((config) => {
    const requestUrl = config.url || ''
    const adminToken = getAdminToken()

    if (adminToken && requestUrl.includes('/admin/')) {
        config.headers = config.headers || {}
        config.headers.Authorization = `Bearer ${adminToken}`
    }

    if (typeof FormData !== 'undefined' && config.data instanceof FormData && config.headers) {
        delete config.headers['Content-Type']
        delete config.headers['content-type']
    }

    return config
})

export default api
