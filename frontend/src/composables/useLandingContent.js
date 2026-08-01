import { ref } from 'vue'
import api from '@/plugins/axios'

const landingSettings = ref({})
const landingJobs = ref([])
const landingMenuItems = ref([])
const landingPromotions = ref([])
const landingBranches = ref([])
const landingLoaded = ref(false)
const CACHE_KEY = 'pollofeliz-landing-content'
const CACHE_TTL_MS = 5 * 60 * 1000

const readCache = () => {
  if (typeof window === 'undefined') {
    return null
  }

  try {
    const cached = window.localStorage.getItem(CACHE_KEY)
    if (!cached) {
      return null
    }

    const parsed = JSON.parse(cached)
    if (!parsed?.timestamp || !parsed?.payload) {
      return null
    }

    const age = Date.now() - parsed.timestamp
    if (age > CACHE_TTL_MS) {
      window.localStorage.removeItem(CACHE_KEY)
      return null
    }

    return parsed.payload
  } catch (error) {
    return null
  }
}

const writeCache = (payload) => {
  if (typeof window === 'undefined') {
    return
  }

  try {
    window.localStorage.setItem(CACHE_KEY, JSON.stringify({ timestamp: Date.now(), payload }))
  } catch (error) {
    console.warn('No se pudo guardar el caché del landing.', error)
  }
}

export const useLandingContent = () => {
  const loadLandingContent = async (force = false) => {
    if (!force) {
      const cachedPayload = readCache()
      if (cachedPayload) {
        landingSettings.value = cachedPayload.settings || {}
        landingJobs.value = cachedPayload.jobs || []
        landingMenuItems.value = cachedPayload.menu_items || []
        landingPromotions.value = cachedPayload.promotions || []
        landingBranches.value = cachedPayload.branches || []
        landingLoaded.value = true
        return cachedPayload
      }
    }

    const response = await api.get('/landing/content')
    const payload = {
      settings: response.data?.settings || {},
      jobs: response.data?.jobs || [],
      menu_items: response.data?.menu_items || [],
      promotions: response.data?.promotions || [],
      branches: response.data?.branches || [],
    }

    landingSettings.value = payload.settings
    landingJobs.value = payload.jobs
    landingMenuItems.value = payload.menu_items
    landingPromotions.value = payload.promotions
    landingBranches.value = payload.branches
    landingLoaded.value = true
    writeCache(payload)
    return payload
  }

  return {
    landingSettings,
    landingJobs,
    landingMenuItems,
    landingPromotions,
    landingBranches,
    landingLoaded,
    loadLandingContent,
  }
}
