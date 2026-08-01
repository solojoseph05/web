import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './styles/main.css'

// 1. AGREGA ESTA IMPORTACIÓN DE @vueuse/head
import { createHead } from '@vueuse/head'

// Importar AOS
import 'aos/dist/aos.css'
import AOS from 'aos'

const app = createApp(App)

// 2. CREA LA INSTANCIA DE HEAD ANTES DE USARLA
const head = createHead()

// Inicializar AOS
AOS.init({
  duration: 550,
  easing: 'ease-out-cubic',
  once: true,
  offset: 70,
  disable: () => window.matchMedia('(prefers-reduced-motion: reduce)').matches
})

// 3. AGREGA app.use(head) ANTES DE MONTAR LA APP
app.use(router)
app.use(head) // <--- Justo aquí, antes del mount
app.mount('#app')