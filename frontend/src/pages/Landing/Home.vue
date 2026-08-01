<!-- src/pages/Landing/Home.vue -->
<template>
  <div class="min-h-screen">
    <!-- Hero y resumen se cargan de inmediato porque son la primera impresión del sitio. -->
    <Hero />
    <Stats />

    <!-- El resto de las secciones se enlazan desde esta página y se activan cuando entran al viewport. -->
    <LazySection :component="About" />
    <LazySection :component="WhyUs" />
    <LazySection :component="FeaturedMenu" />
    <LazySection :component="Promotions" />
    <LazySection :component="Branches" />
    <LazySection :component="Jobs" />
    <LazySection :component="Contact" />
  </div>
</template>

<script setup>
import { defineAsyncComponent } from 'vue'
import { useHead } from '@vueuse/head'

import Hero from '@/components/landing/Hero.vue'
import Stats from '@/components/landing/Stats.vue'
import FeaturedMenu from '@/components/landing/FeaturedMenu.vue'
import LazySection from '@/components/common/LazySection.vue'

// Estas secciones se cargan de forma diferida para reducir el peso inicial del home.
// Si una sección cambia de nombre o se mueve de lugar, la ruta de importación también debe actualizarse.
const About = defineAsyncComponent(() => import('@/components/landing/About.vue'))
const WhyUs = defineAsyncComponent(() => import('@/components/landing/WhyUs.vue'))
const Promotions = defineAsyncComponent(() => import('@/components/landing/Promotions.vue'))
const Branches = defineAsyncComponent(() => import('@/components/landing/Branches.vue'))
const Jobs = defineAsyncComponent(() => import('@/components/landing/Jobs.vue'))
const Contact = defineAsyncComponent(() => import('@/components/landing/Contact.vue'))

// SEO de la página de inicio: cualquier cambio en el título o descripción impacta la vista previa compartida.
useHead({
  title: 'Inicio | Pollo Feliz - El mejor pollo asado',
  meta: [
    { name: 'description', content: 'Descubre el sabor inigualable del Pollo Feliz. Disfruta nuestras promociones, conoce nuestras sucursales y ordena el mejor pollo asado.' }
  ]
})
</script>