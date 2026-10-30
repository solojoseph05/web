import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './styles/main.css'

import { createHead } from '@vueuse/head'

import 'aos/dist/aos.css'
import AOS from 'aos'

const app = createApp(App)
const pinia = createPinia()
const head = createHead()

AOS.init({
  duration: 550,
  easing: 'ease-out-cubic',
  once: true,
  offset: 70,
  disable: () => window.matchMedia('(prefers-reduced-motion: reduce)').matches
})

app.use(pinia)
app.use(router)
app.use(head)
app.mount('#app')