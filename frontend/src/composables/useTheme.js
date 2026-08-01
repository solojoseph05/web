import { ref } from 'vue'

const STORAGE_KEY = 'pollofeliz-theme'
const theme = ref('light')

const applyTheme = (nextTheme) => {
  if (typeof document === 'undefined') {
    return
  }

  theme.value = nextTheme === 'dark' ? 'dark' : 'light'
  document.documentElement.classList.toggle('dark', theme.value === 'dark')
  document.documentElement.setAttribute('data-theme', theme.value)
}

const initTheme = () => {
  if (typeof window === 'undefined') {
    return
  }

  const storedTheme = window.localStorage.getItem(STORAGE_KEY)
  if (storedTheme === 'dark' || storedTheme === 'light') {
    applyTheme(storedTheme)
    return
  }

  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  applyTheme(prefersDark ? 'dark' : 'light')
}

const setTheme = (nextTheme) => {
  applyTheme(nextTheme)
  if (typeof window !== 'undefined') {
    window.localStorage.setItem(STORAGE_KEY, theme.value)
  }
}

const toggleTheme = () => {
  setTheme(theme.value === 'dark' ? 'light' : 'dark')
}

export const useTheme = () => {
  return {
    theme,
    initTheme,
    setTheme,
    toggleTheme,
  }
}

