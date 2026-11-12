<template>
  <div ref="root">
    <!-- Si la sección ya está cerca del viewport, se monta; si no, se retrasa para ahorrar recursos. -->
    <component :is="component" v-if="isVisible" />
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'

const props = defineProps({
  component: {
    type: [Object, Function],
    required: true
  }
})

const root = ref(null)
const isVisible = ref(false)
let observer = null

const loadWhenVisible = () => {
  if (!root.value) {
    return
  }

  if (typeof IntersectionObserver === 'undefined') {
    isVisible.value = true
    return
  }

  observer = new IntersectionObserver((entries) => {
    const [entry] = entries

    if (entry?.isIntersecting) {
      isVisible.value = true
      observer?.disconnect()
    }
  }, {
    rootMargin: '220px 0px'
  })

  observer.observe(root.value)
}

onMounted(() => {
  loadWhenVisible()
})

onBeforeUnmount(() => {
  observer?.disconnect()
})
</script>
