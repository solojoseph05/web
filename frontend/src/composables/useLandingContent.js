import { ref } from 'vue'
import api from '@/plugins/axios'
import company from '@/config/company'
import menu from '@/config/menu'
import promotionsConfig from '@/config/promotions'
import branchesConfig from '@/config/branches'

const landingSettings = ref({
  // Hero
  hero_title: company.tagline || 'El mejor Pollo Asado de Durango',
  hero_subtitle: '¡Bienvenidos a la Familia Pollo Feliz! · Tradición al Carbón',
  hero_description: company.description || 'Descubre el auténtico sabor que reúne a la familia.',
  hero_badge: '100% Asado al Carbón',
  hero_image_url: '',
  hero_cta_primary: 'Ver Menú Completo',
  hero_cta_secondary: 'Ordenar en Línea',

  // Social Proof & Stats
  stat_1_value: '100%',
  stat_1_label: 'Asado al Carbón',
  stat_2_value: '4.9 ★',
  stat_2_label: '+2,500 Reseñas',
  stat_3_value: '3+',
  stat_3_label: 'Sucursales Activas',

  // Section Headers
  menu_section_title: 'Platos Destacados',
  menu_section_subtitle: 'Descubre nuestros platillos más populares, preparados con la receta secreta que nos hace únicos.',
  promotions_section_title: 'Promociones Imperdibles',
  promotions_section_subtitle: 'Aprovecha nuestras promociones especiales y disfruta del mejor pollo asado con descuentos exclusivos.',
  branches_section_title: 'Nuestras Sucursales',
  branches_section_subtitle: 'Visítanos o llámanos directamente para disfrutar del mejor sabor al carbón recién salido de la parrilla.',

  // Company Contact & Footer
  company_name: 'Pollo Feliz Durango',
  company_phone: company.phone || '618-123-4567',
  company_email: company.email || 'contacto@pollofeliz.com',
  company_whatsapp: company.whatsapp || '618-123-4567',
  company_address: company.address || 'Av. 5 de Mayo #123, Zona Centro, Durango, Dgo.',
  schedule_weekday: 'Lunes a Domingo: 10:00 AM - 10:00 PM',
  social_facebook: 'https://facebook.com/pollofelizdurango',
  social_instagram: 'https://instagram.com/pollofelizdurango',
})

const landingMenuItems = ref(menu.featured || [])
const landingPromotions = ref(promotionsConfig.active || [])
const landingBranches = ref(branchesConfig.active || [])
const landingJobs = ref([])
const isLoading = ref(false)
const error = ref(null)

export const useLandingContent = () => {
  const loadLandingContent = async () => {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.get('/landing/content')
      const data = response.data
      if (data) {
        if (data.settings) landingSettings.value = { ...landingSettings.value, ...data.settings }
        if (Array.isArray(data.menu_items) && data.menu_items.length > 0) {
          landingMenuItems.value = data.menu_items
        }
        if (Array.isArray(data.promotions) && data.promotions.length > 0) {
          landingPromotions.value = data.promotions
        }
        if (Array.isArray(data.branches) && data.branches.length > 0) {
          landingBranches.value = data.branches
        }
        if (Array.isArray(data.jobs)) {
          landingJobs.value = data.jobs
        }
      }
      return data
    } catch (err) {
      console.warn('Usando configuración local de respaldo para el contenido.', err)
      error.value = err
      return null
    } finally {
      isLoading.value = false
    }
  }

  const updateLandingSettings = (newSettings) => {
    landingSettings.value = {
      ...landingSettings.value,
      ...newSettings,
    }
  }

  return {
    landingSettings,
    landingMenuItems,
    landingPromotions,
    landingBranches,
    landingJobs,
    isLoading,
    error,
    loadLandingContent,
    updateLandingSettings,
  }
}
