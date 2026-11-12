import { ref, computed } from 'vue'
import { useToasts } from '@/composables/useToasts'

const STORAGE_KEY = 'pollofeliz_favorite_dishes'

// Shared reactive state across components
const favoriteIds = ref([])

// Initialize from localStorage
if (typeof window !== 'undefined') {
  try {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved) {
      favoriteIds.value = JSON.parse(saved)
    }
  } catch (err) {
    console.warn('Error reading favorites from localStorage', err)
  }
}

const persistFavorites = () => {
  if (typeof window === 'undefined') return
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(favoriteIds.value))
  } catch (err) {
    console.warn('Error saving favorites to localStorage', err)
  }
}

export const useFavorites = () => {
  const { addToast } = useToasts()

  const favoritesCount = computed(() => favoriteIds.value.length)

  const isFavorite = (itemId) => {
    if (!itemId) return false
    return favoriteIds.value.includes(itemId)
  }

  const toggleFavorite = (item) => {
    if (!item || !item.id) return

    const index = favoriteIds.value.indexOf(item.id)
    if (index > -1) {
      favoriteIds.value.splice(index, 1)
      persistFavorites()
      addToast(`"${item.name}" eliminado de tus favoritos.`, 'info')
      return false
    } else {
      favoriteIds.value.push(item.id)
      persistFavorites()
      addToast(`¡"${item.name}" guardado en tus favoritos! ❤️`, 'success')
      return true
    }
  }

  const removeFavorite = (itemId) => {
    const index = favoriteIds.value.indexOf(itemId)
    if (index > -1) {
      favoriteIds.value.splice(index, 1)
      persistFavorites()
    }
  }

  const clearFavorites = () => {
    favoriteIds.value = []
    persistFavorites()
    addToast('Se eliminaron todos tus platillos favoritos.', 'info')
  }

  return {
    favoriteIds,
    favoritesCount,
    isFavorite,
    toggleFavorite,
    removeFavorite,
    clearFavorites,
  }
}
