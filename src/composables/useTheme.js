import { ref } from 'vue'

const theme = ref('light')

export const useTheme = () => {
  const initTheme = () => {
    if (typeof window === 'undefined') return
    const saved = localStorage.getItem('pollofeliz_theme')
    if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      theme.value = 'dark'
      document.documentElement.classList.add('dark')
    } else {
      theme.value = 'light'
      document.documentElement.classList.remove('dark')
    }
  }

  const toggleTheme = () => {
    theme.value = theme.value === 'dark' ? 'light' : 'dark'
    if (typeof window !== 'undefined') {
      localStorage.setItem('pollofeliz_theme', theme.value)
      if (theme.value === 'dark') {
        document.documentElement.classList.add('dark')
      } else {
        document.documentElement.classList.remove('dark')
      }
    }
  }

  return {
    theme,
    initTheme,
    toggleTheme,
  }
}
