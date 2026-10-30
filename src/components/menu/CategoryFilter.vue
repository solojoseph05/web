<!-- src/components/menu/CategoryFilter.vue -->
<template>
  <div class="w-full space-y-3">
    <!-- Primary Category Groups (Combos, Bebidas, Extras, Pollo, etc.) -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1 -mx-2 px-2 sm:mx-0 sm:px-0 scroll-smooth">
      <button
        v-for="group in dynamicCategoryGroups"
        :key="group.id"
        @click="selectGroup(group.id)"
        class="inline-flex shrink-0 items-center gap-2 whitespace-nowrap rounded-2xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer border active:scale-95"
        :class="activeGroup === group.id
          ? 'bg-red-600 text-white border-red-600 shadow-md shadow-red-600/25 scale-[1.02]'
          : 'bg-white text-gray-700 border-gray-200 hover:border-red-200 hover:bg-red-50/50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700/80'"
      >
        <span class="text-base">{{ group.icon }}</span>
        <span>{{ group.name }}</span>
        <span
          class="rounded-full px-2 py-0.5 text-[11px] font-extrabold"
          :class="activeGroup === group.id ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
        >
          {{ getGroupCount(group) }}
        </span>
      </button>
    </div>

    <!-- Secondary Sub-categories Bar (Shown when active group has multiple subcategories) -->
    <Transition name="fade">
      <div
        v-if="currentSubcategories.length > 1"
        class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1 -mx-2 px-2 sm:mx-0 sm:px-0 scroll-smooth"
      >
        <button
          v-for="sub in currentSubcategories"
          :key="sub.id"
          @click="selectSubcategory(sub.id)"
          class="inline-flex shrink-0 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold transition-all cursor-pointer active:scale-95"
          :class="activeSubcategory === sub.id
            ? 'bg-gray-900 text-white shadow-xs dark:bg-white dark:text-gray-900 font-bold'
            : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800/80 dark:text-gray-300 dark:hover:bg-gray-700'"
        >
          {{ sub.name }}
          <span class="text-[10px] opacity-75 ml-0.5">({{ getSubcategoryCount(sub.id) }})</span>
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useFavorites } from '@/composables/useFavorites'

const props = defineProps({
  modelValue: {
    type: String,
    default: 'all',
  },
  items: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['update:modelValue', 'change'])
const { favoriteIds } = useFavorites()

const BASE_GROUPS = [
  {
    id: 'all',
    name: 'Todos',
    icon: '🍗',
    categories: [],
  },
  {
    id: 'pollos',
    name: 'Pollos',
    icon: '🍗',
    categories: ['Pollo Entero', 'Medio Pollo', 'Pollo', 'Pollos', 'Pollo Asado'],
  },
  {
    id: 'entradas',
    name: 'Entradas',
    icon: '🍟',
    categories: ['Entradas', 'Complementos', 'Extras', 'Guarniciones', 'Papas'],
  },
  {
    id: 'combos',
    name: 'Combos',
    icon: '🍱',
    categories: ['Combos', 'Paquetes', 'Combos / Paquetes', 'Combo'],
  },
  {
    id: 'bebidas',
    name: 'Bebidas',
    icon: '🥤',
    categories: ['Bebidas', 'Drinks', 'Refrescos', 'Aguas'],
  },
  {
    id: 'tacos',
    name: 'Tacos',
    icon: '🌮',
    categories: ['Tacos', 'Quesadillas'],
  },
  {
    id: 'ensaladas',
    name: 'Ensaladas',
    icon: '🥗',
    categories: ['Ensaladas'],
  },
  {
    id: 'postres',
    name: 'Postres',
    icon: '🍮',
    categories: ['Postres'],
  },
  {
    id: 'favorites',
    name: 'Favoritos',
    icon: '❤️',
    categories: [],
  },
]

const activeGroup = ref('all')
const activeSubcategory = ref('all')

// Generate dynamic groups including any custom categories created by the admin
const dynamicCategoryGroups = computed(() => {
  const groups = [...BASE_GROUPS]
  const knownCategories = new Set(
    BASE_GROUPS.flatMap((g) => g.categories.map((c) => c.toLowerCase()))
  )

  // Find custom categories present in items
  const customCategories = new Set()
  props.items.forEach((item) => {
    if (item.category && !knownCategories.has(item.category.toLowerCase().trim())) {
      customCategories.add(item.category.trim())
    }
  })

  // Append any extra custom category groups
  customCategories.forEach((cat) => {
    groups.push({
      id: `custom_${cat.toLowerCase().replace(/\s+/g, '_')}`,
      name: cat,
      icon: '✨',
      categories: [cat],
      isCustom: true,
    })
  })

  return groups.filter((g) => {
    if (g.id === 'all' || g.id === 'favorites') return true
    return getGroupCount(g) > 0
  })
})

// Calculate count of items for a specific group
const getGroupCount = (groupOrId) => {
  const group = typeof groupOrId === 'string'
    ? dynamicCategoryGroups.value.find((g) => g.id === groupOrId)
    : groupOrId

  if (!group) return 0
  if (group.id === 'all') return props.items.length
  if (group.id === 'favorites') {
    return props.items.filter((item) => favoriteIds.value.includes(item.id)).length
  }

  return props.items.filter((item) => {
    const itemCat = (item.category || '').toLowerCase().trim()
    return group.categories.some((c) => c.toLowerCase().trim() === itemCat)
  }).length
}

// Current subcategories based on active group
const currentSubcategories = computed(() => {
  if (activeGroup.value === 'all') return []

  const group = dynamicCategoryGroups.value.find((g) => g.id === activeGroup.value)
  if (!group || group.categories.length <= 1) return []

  const availableSubs = group.categories.filter((c) =>
    props.items.some((i) => (i.category || '').toLowerCase().trim() === c.toLowerCase().trim())
  )

  if (availableSubs.length <= 1) return []

  return [
    { id: 'all', name: `Todos en ${group.name}` },
    ...availableSubs.map((c) => ({ id: c, name: c })),
  ]
})

const getSubcategoryCount = (subId) => {
  if (subId === 'all') return getGroupCount(activeGroup.value)
  return props.items.filter(
    (i) => (i.category || '').toLowerCase().trim() === subId.toLowerCase().trim()
  ).length
}

const selectGroup = (groupId) => {
  activeGroup.value = groupId
  activeSubcategory.value = 'all'
  emitSelection()
}

const selectSubcategory = (subId) => {
  activeSubcategory.value = subId
  emitSelection()
}

const emitSelection = () => {
  let filterCategory = 'all'

  if (activeGroup.value === 'all') {
    filterCategory = 'all'
  } else if (activeSubcategory.value !== 'all') {
    filterCategory = activeSubcategory.value
  } else {
    filterCategory = `group:${activeGroup.value}`
  }

  emit('update:modelValue', filterCategory)
  emit('change', {
    group: activeGroup.value,
    subcategory: activeSubcategory.value,
    value: filterCategory,
  })
}

// Sync external modelValue changes
watch(
  () => props.modelValue,
  (val) => {
    if (!val || val === 'all') {
      activeGroup.value = 'all'
      activeSubcategory.value = 'all'
      return
    }

    if (val.startsWith('group:')) {
      const g = val.replace('group:', '')
      activeGroup.value = g
      activeSubcategory.value = 'all'
      return
    }

    // Check if it's a known group
    const matchingGroup = dynamicCategoryGroups.value.find((g) => g.id === val)
    if (matchingGroup) {
      activeGroup.value = matchingGroup.id
      activeSubcategory.value = 'all'
      return
    }

    // Check if it's a subcategory
    const valLower = val.toLowerCase().trim()
    for (const group of dynamicCategoryGroups.value) {
      if (group.categories.some((c) => c.toLowerCase().trim() === valLower)) {
        activeGroup.value = group.id
        activeSubcategory.value = val
        return
      }
    }
  },
  { immediate: true }
)
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
