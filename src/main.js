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
  disable: () => typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches
})

router.afterEach(() => {
  if (typeof window !== 'undefined') {
    setTimeout(() => {
      AOS.refresh()
    }, 120)
  }
})

app.use(pinia)
app.use(router)
app.use(head)

router.isReady().then(() => {
  app.mount('#app')
})