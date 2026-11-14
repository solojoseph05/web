<!-- src/pages/Landing/Home.vue -->
<template>
  <main class="min-h-screen bg-white dark:bg-gray-950 overflow-x-hidden">
    <!-- Hero Component -->
    <Hero />

    <!-- Why Us / El Secreto de Nuestro Sabor Component -->
    <WhyUs />

    <!-- Featured Menu Component -->
    <FeaturedMenu />

    <!-- Promotions Section Component -->
    <Promotions />

    <!-- Branches Section Component -->
    <Branches />

    <!-- Job Vacancies / Bolsa de Trabajo Component -->
    <Vacancies />

    <!-- Customer Reviews Section Component -->
    <Reviews />

    <!-- Contact & Feedback Section Component -->
    <Contact />
  </main>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useHead } from '@vueuse/head'
import Hero from '@/components/landing/Hero.vue'
import WhyUs from '@/components/landing/WhyUs.vue'
import FeaturedMenu from '@/components/landing/FeaturedMenu.vue'
import Promotions from '@/components/landing/Promotions.vue'
import Branches from '@/components/landing/Branches.vue'
import Vacancies from '@/components/landing/Vacancies.vue'
import Reviews from '@/components/landing/Reviews.vue'
import Contact from '@/components/landing/Contact.vue'
import { useLandingContent } from '@/composables/useLandingContent'

const { landingSettings, landingBranches, landingMenuItems, loadLandingContent } = useLandingContent()

onMounted(async () => {
  try {
    await loadLandingContent(true)
  } catch (err) {
    console.warn('Error al cargar contenido de inicio:', err)
  }
})

const seoTitle = computed(() => {
  const brand = landingSettings.value.company_name || 'Pollo Feliz Durango'
  const tagline = landingSettings.value.hero_title || 'El Mejor Pollo Asado al Carbón'
  return `${brand} | ${tagline}`
})

const seoDescription = computed(() =>
  landingSettings.value.hero_description ||
  'Disfruta del mejor pollo asado al carbón en Durango. Receta tradicional, paquetes familiares, servicio a domicilio y entrega rápida.'
)

const structuredDataJson = computed(() => {
  const departments = (landingBranches.value || [])
    .filter((b) => b && !b.is_coming_soon)
    .map((b) => ({
      '@type': 'Restaurant',
      name: `${landingSettings.value.company_name || 'Pollo Feliz Durango'} - ${b.name}`,
      address: {
        '@type': 'PostalAddress',
        streetAddress: b.address || '',
        addressLocality: 'Victoria de Durango',
        addressRegion: 'Dgo.',
        addressCountry: 'MX',
      },
      telephone: b.phone || landingSettings.value.company_phone || '618-123-4567',
      openingHours: b.schedule || landingSettings.value.schedule_weekday || 'Mo-Su 10:00-22:00',
      ...(b.latitude && b.longitude
        ? {
            geo: {
              '@type': 'GeoCoordinates',
              latitude: Number(b.latitude),
              longitude: Number(b.longitude),
            },
            hasMap: `https://www.google.com/maps/dir/?api=1&destination=${b.latitude},${b.longitude}`,
          }
        : {}),
    }))

  const menuSectionsMap = new Map()
  ;(landingMenuItems.value || [])
    .filter((item) => item && item.is_active !== false)
    .slice(0, 12)
    .forEach((item) => {
      const cat = item.category || 'Especialidades'
      if (!menuSectionsMap.has(cat)) menuSectionsMap.set(cat, [])
      menuSectionsMap.get(cat).push({
        '@type': 'MenuItem',
        name: item.name,
        description: item.description || undefined,
        offers: {
          '@type': 'Offer',
          price: Number(item.price || 0),
          priceCurrency: 'MXN',
        },
      })
    })

  return JSON.stringify({
    '@context': 'https://schema.org',
    '@type': 'FastFoodRestaurant',
    name: landingSettings.value.company_name || 'Pollo Feliz Durango',
    description: seoDescription.value,
    servesCuisine: 'Mexicana, Pollo Asado al Carbón',
    priceRange: '$$',
    telephone: landingSettings.value.company_phone || '618-123-4567',
    email: landingSettings.value.company_email || 'soporte@pollofelizdgo.com',
    address: {
      '@type': 'PostalAddress',
      streetAddress: landingSettings.value.company_address || 'Av. 5 de Mayo #123, Zona Centro',
      addressLocality: 'Victoria de Durango',
      addressRegion: 'Durango',
      addressCountry: 'MX',
    },
    ...(departments.length ? { department: departments } : {}),
    hasMenu: {
      '@type': 'Menu',
      hasMenuSection: Array.from(menuSectionsMap.entries()).map(([name, hasMenuItem]) => ({
        '@type': 'MenuSection',
        name,
        hasMenuItem,
      })),
    },
  })
})

useHead(
  computed(() => ({
    title: seoTitle.value,
    meta: [
      { name: 'description', content: seoDescription.value },
      { property: 'og:title', content: seoTitle.value },
      { property: 'og:description', content: seoDescription.value },
      { property: 'og:type', content: 'website' },
      { name: 'twitter:card', content: 'summary_large_image' },
      { name: 'twitter:title', content: seoTitle.value },
      { name: 'twitter:description', content: seoDescription.value },
    ],
    script: [
      {
        key: 'restaurant-jsonld',
        type: 'application/ld+json',
        children: structuredDataJson.value,
      },
    ],
  }))
)
</script>
