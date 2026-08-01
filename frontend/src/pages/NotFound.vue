<template>
  <div class="notfound-page">
    <div class="card">
      <div class="content">
        <div class="brand">
          <img :src="logo" alt="PolloFeliz" class="brand-logo" />
          <div>
            <p class="brand-label">PolloFeliz</p>
            <p class="brand-subtitle">Tu pollo asado favorito</p>
          </div>
        </div>

        <h1>404</h1>
        <h2>Página no encontrada</h2>
        <p>La página que buscas no existe o fue movida. Regresa al inicio para continuar disfrutando de nuestro menú.</p>
 
        <div class="actions">
          <router-link to="/" class="btn">Volver al inicio</router-link>
          <router-link :to="{ path: '/', hash: '#contact' }" class="ghost">Contactar soporte</router-link>
        </div>
      </div>
    </div>
  </div>

</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'
import logo from '@/assets/images/logo.jpg'

let footerEl = null
let originalStyle = {}
let rootEl = null
let findAttempts = 0
const maxAttempts = 6
let hiddenFooters = []

function applyFix() {
  // Encontrar todos los footers en la página
  const footers = Array.from(document.querySelectorAll('footer'))
  if (!footers.length) {
    findAttempts++
    if (findAttempts < maxAttempts) {
      setTimeout(applyFix, 120)
    }
    return
  }

  // Preferir footer con clase bg-gray-900, si existe
  footerEl = footers.find(f => f.classList.contains('bg-gray-900')) || footers[0]

  // Si hay más de un footer, ocultar los duplicados (todos excepto footerEl)
  hiddenFooters = footers.filter(f => f !== footerEl).map(f => ({ el: f, display: f.style.display || '' }))
  hiddenFooters.forEach(h => { h.el.style.display = 'none' })

  // Guardar estilos inline originales para restaurarlos al salir
  originalStyle = {
    position: footerEl.style.position || '',
    bottom: footerEl.style.bottom || '',
    left: footerEl.style.left || '',
    right: footerEl.style.right || '',
    zIndex: footerEl.style.zIndex || ''
  }

  // Fijar footer en la ventana
  footerEl.style.position = 'fixed'
  footerEl.style.bottom = '0'
  footerEl.style.left = '0'
  footerEl.style.right = '0'
  footerEl.style.zIndex = '50'

  // Reservar espacio en la vista 404 ajustando la variable CSS en el root del componente
  rootEl = document.querySelector('.notfound-page')
  const height = Math.ceil(footerEl.getBoundingClientRect().height) || 220
  if (rootEl && rootEl.style) {
    rootEl.style.setProperty('--footer-height', `${height}px`)
  }
}

onMounted(() => {
  applyFix()
})

onUnmounted(() => {
  // Restaurar footer principal
  if (footerEl) {
    footerEl.style.position = originalStyle.position
    footerEl.style.bottom = originalStyle.bottom
    footerEl.style.left = originalStyle.left
    footerEl.style.right = originalStyle.right
    footerEl.style.zIndex = originalStyle.zIndex
  }

  // Restaurar footers ocultos
  hiddenFooters.forEach(h => {
    if (h && h.el) h.el.style.display = h.display
  })
  hiddenFooters = []

  if (rootEl && rootEl.style) {
    rootEl.style.removeProperty('--footer-height')
  }
})
</script>

<style scoped>
:root {
  --bg: #ffffff;
  --muted: #6b7280;
  --primary: #42b983;
  --accent1: #FF7A7A;
  --accent2: #FFB86B;
  --footer-height: 220px; /* espacio reservado para el footer fijo */
}

.notfound-page {
  min-height: calc(100vh - 64px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  padding-bottom: var(--footer-height); /* deja espacio para el footer fijo */
  background: linear-gradient(180deg, #fff 0%, #fbfbfd 100%);
}

.card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
  max-width: 880px;
  width: 100%;
  background: var(--bg);
  box-shadow: 0 8px 30px rgba(16,24,40,0.08);
  border-radius: 12px;
  padding: 2.25rem;
}

.content {
  text-align: center;
}

.brand {
  display: inline-flex;
  align-items: center;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.brand-logo {
  width: 56px;
  height: 56px;
  object-fit: cover;
  border-radius: 9999px;
  box-shadow: 0 10px 30px rgba(255, 122, 122, 0.16);
}

.brand-label {
  font-size: 1.1rem;
  font-weight: 700;
  color: #111827;
}

.brand-subtitle {
  font-size: 0.9rem;
  color: #6b7280;
  margin-top: 0.1rem;
}

.content h1 {
  font-size: 3rem;
  margin: 0;
  color: #111827;
  letter-spacing: -1px;
}

.content h2 {
  font-size: 1.25rem;
  margin: 0.25rem 0 0.75rem 0;
  color: #374151;
  font-weight: 600;
}

.content p {
  color: var(--muted);
  margin: 0 0 1.25rem 0;
  max-width: 48rem;
}

.actions {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
  flex-wrap: wrap;
}

/* Botón principal: hacerlo más visible y con mayor contraste */
.btn {
  display: inline-block;
  background: linear-gradient(135deg, #EF4444, #F97316);
  color: white !important;
  padding: 0.9rem 1.5rem;
  border-radius: 9999px;
  font-weight: 800;
  text-decoration: none;
  box-shadow: 0 16px 36px rgba(239, 68, 68, 0.24);
  font-size: 1rem;
  z-index: 20;
  border: 1px solid rgba(248, 113, 47, 0.16);
}

.btn:focus, .btn:hover {
  transform: translateY(-3px);
  transition: transform 160ms ease;
  box-shadow: 0 10px 30px rgba(66,185,131,0.22);
}

.ghost {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  padding: 0.6rem 1rem;
  border-radius: 9999px;
  text-decoration: none;
  border: 1px solid rgba(209, 213, 219, 0.75);
  background: rgba(255,255,255,0.85);
}

.ghost:hover {
  color: #111827;
  background: rgba(0,0,0,0.02);
}

/* Footer fijo solo para esta vista */

/* Si el footer tiene un alto mayor, ajustar la variable --footer-height arriba */

@media(min-width:900px) {
  .card {
    flex-direction: row;
    align-items: center;
    padding: 2.5rem;
    gap: 2rem;
  }
  .art { flex: 0 0 260px }
  .content { text-align: left }
}
</style>