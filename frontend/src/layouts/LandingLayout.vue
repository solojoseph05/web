<template>
  <div class="min-h-screen">
    <Navbar />
    <main class="pt-16">
      <slot />
    </main>
  </div>
</template>

<script setup>
import Navbar from '@/components/landing/Navbar.vue'
</script>