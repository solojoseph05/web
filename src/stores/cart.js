import { defineStore } from 'pinia'

const STORAGE_KEYS = {
  CART: 'pollofeliz_pinia_cart',
  PROMO: 'pollofeliz_pinia_promo',
  DETAILS: 'pollofeliz_pinia_details',
}

export const useCartStore = defineStore('cart', {
  state: () => {
    let savedItems = []
    let savedPromo = null
    let savedDetails = {
      orderType: 'pickup',
      selectedBranch: 'Sucursal Centro Histórico',
      name: '',
      phone: '',
      address: '',
      notes: '',
    }

    if (typeof window !== 'undefined') {
      try {
        const rawItems = localStorage.getItem(STORAGE_KEYS.CART)
        if (rawItems) savedItems = JSON.parse(rawItems)

        const rawPromo = localStorage.getItem(STORAGE_KEYS.PROMO)
        if (rawPromo) savedPromo = JSON.parse(rawPromo)

        const rawDetails = localStorage.getItem(STORAGE_KEYS.DETAILS)
        if (rawDetails) savedDetails = { ...savedDetails, ...JSON.parse(rawDetails) }
      } catch (err) {
        console.warn('Error reading cart from localStorage', err)
      }
    }

    return {
      items: savedItems,
      activePromo: savedPromo,
      orderType: savedDetails.orderType,
      selectedBranch: savedDetails.selectedBranch,
      customer: {
        name: savedDetails.name,
        phone: savedDetails.phone,
        address: savedDetails.address,
        notes: savedDetails.notes,
      },
      isCartOpen: false,
      whatsappNumber: '521551234567',
    }
  },

  getters: {
    totalCount: (state) => {
      return state.items.reduce((sum, item) => sum + (Number(item.quantity) || 1), 0)
    },

    subtotal: (state) => {
      return state.items.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 1), 0)
    },

    discount(state) {
      if (!state.activePromo) return 0
      if (state.activePromo.percentage) {
        return (this.subtotal * state.activePromo.percentage) / 100
      }
      if (state.activePromo.fixed) {
        return Math.min(this.subtotal, state.activePromo.fixed)
      }
      return 0
    },

    deliveryFee(state) {
      if (state.orderType !== 'delivery') return 0
      // Free delivery on orders over $300 MXN
      return this.subtotal > 300 ? 0 : 35
    },

    total() {
      return Math.max(0, this.subtotal - this.discount + this.deliveryFee)
    },

    formattedSubtotal() {
      return `$${this.subtotal.toFixed(0)} MXN`
    },

    formattedDiscount() {
      return `-$${this.discount.toFixed(0)} MXN`
    },

    formattedDeliveryFee() {
      return this.deliveryFee === 0 ? 'GRATIS' : `$${this.deliveryFee.toFixed(0)} MXN`
    },

    formattedTotal() {
      return `$${this.total.toFixed(0)} MXN`
    },

    whatsappMessage(state) {
      const lines = [
        `🍗 *NUEVO PEDIDO - POLLO FELIZ*`,
        `================================`,
        `*Modalidad:* ${state.orderType === 'delivery' ? '🛵 Entrega a Domicilio' : '🏪 Recoger en Sucursal'}`,
        `*Sucursal:* ${state.selectedBranch}`,
      ]

      if (state.customer.name) {
        lines.push(`*Cliente:* ${state.customer.name}`)
      }
      if (state.customer.phone) {
        lines.push(`*Teléfono:* ${state.customer.phone}`)
      }
      if (state.orderType === 'delivery' && state.customer.address) {
        lines.push(`*Dirección de Entrega:* ${state.customer.address}`)
      }
      if (state.customer.notes) {
        lines.push(`*Indicaciones:* ${state.customer.notes}`)
      }

      lines.push(`================================`)
      lines.push(`*DETALLE DEL PEDIDO:*`)
      state.items.forEach((item, idx) => {
        lines.push(`${idx + 1}. ${item.quantity}x ${item.name} — $${item.price * item.quantity} MXN`)
        if (item.notes) {
          lines.push(`   ↳ _Nota: ${item.notes}_`)
        }
      })

      lines.push(`================================`)
      lines.push(`*Subtotal:* $${this.subtotal.toFixed(0)} MXN`)
      if (this.discount > 0) {
        lines.push(`*Descuento (${state.activePromo?.name}):* -$${this.discount.toFixed(0)} MXN`)
      }
      if (state.orderType === 'delivery') {
        lines.push(`*Costo de Envío:* ${this.formattedDeliveryFee}`)
      }
      lines.push(`*TOTAL FINAL:* $${this.total.toFixed(0)} MXN`)
      lines.push(`================================`)
      lines.push(`_¡Gracias por su preferencia en Pollo Feliz!_`)

      return lines.join('\n')
    },

    whatsappOrderUrl(state) {
      return `https://wa.me/${state.whatsappNumber}?text=${encodeURIComponent(this.whatsappMessage)}`
    },
  },

  actions: {
    persist() {
      if (typeof window === 'undefined') return
      try {
        localStorage.setItem(STORAGE_KEYS.CART, JSON.stringify(this.items))
        if (this.activePromo) {
          localStorage.setItem(STORAGE_KEYS.PROMO, JSON.stringify(this.activePromo))
        } else {
          localStorage.removeItem(STORAGE_KEYS.PROMO)
        }
        localStorage.setItem(
          STORAGE_KEYS.DETAILS,
          JSON.stringify({
            orderType: this.orderType,
            selectedBranch: this.selectedBranch,
            ...this.customer,
          })
        )
      } catch (err) {
        console.warn('Error saving cart to localStorage', err)
      }
    },

    addItem(product, quantity = 1, notes = '') {
      const existing = this.items.find(
        (i) => i.id === product.id && i.notes === notes
      )

      if (existing) {
        existing.quantity += quantity
      } else {
        this.items.push({
          id: product.id,
          name: product.name,
          price: Number(product.price) || 0,
          quantity: quantity > 0 ? quantity : 1,
          image: product.image_url || product.image || '',
          category: product.category || 'Platillos',
          badge: product.badge || '',
          notes,
        })
      }

      this.persist()
    },

    updateQuantity(index, delta) {
      if (!this.items[index]) return
      const nextQty = this.items[index].quantity + delta
      if (nextQty <= 0) {
        this.items.splice(index, 1)
      } else {
        this.items[index].quantity = nextQty
      }
      this.persist()
    },

    removeItem(index) {
      if (this.items[index]) {
        this.items.splice(index, 1)
        this.persist()
      }
    },

    clearCart() {
      this.items = []
      this.activePromo = null
      this.persist()
    },

    applyPromoCode(code) {
      const clean = String(code).trim().toUpperCase()
      if (!clean) return { success: false, message: 'Ingresa un código promocional.' }

      if (clean === 'FELIZCLUB10' || clean === 'POLLOFELIZ10' || clean === 'FELIZ10' || clean === 'BIENVENIDO') {
        this.activePromo = { code: clean, name: 'Club Pollo Feliz VIP', percentage: 10 }
        this.persist()
        return { success: true, message: '¡Cupón de bienvenida aplicado! (10% descuento)' }
      }
      if (clean === 'POLLO2X1' || clean === 'MARTES2X1') {
        this.activePromo = { code: clean, name: '2x1 Martes de Pollo', percentage: 25 }
        this.persist()
        return { success: true, message: '¡Promoción 2x1 aplicada! (25% descuento)' }
      }
      if (clean === 'FAMILIA30' || clean === 'FAMILIAR') {
        this.activePromo = { code: clean, name: 'Paquete Familiar Especial', fixed: 50 }
        this.persist()
        return { success: true, message: '¡Descuento familiar aplicado! ($50 MXN desc.)' }
      }
      if (clean === 'ESTUDIANTE' || clean === 'JOVEN') {
        this.activePromo = { code: clean, name: 'Combo Estudiante', percentage: 15 }
        this.persist()
        return { success: true, message: '¡Descuento estudiante aplicado! (15% desc.)' }
      }
      if (clean === 'CARBON' || clean === 'DURANGO') {
        this.activePromo = { code: clean, name: 'Tradición al Carbón', fixed: 40 }
        this.persist()
        return { success: true, message: '¡Cupón Tradición aplicado! ($40 MXN desc.)' }
      }

      return { success: false, message: 'Código promocional no válido o expirado.' }
    },

    removePromo() {
      this.activePromo = null
      this.persist()
    },

    setOrderType(type) {
      this.orderType = type
      this.persist()
    },

    setSelectedBranch(branch) {
      this.selectedBranch = branch
      this.persist()
    },

    setCustomerDetails(details) {
      this.customer = { ...this.customer, ...details }
      this.persist()
    },

    openCart() {
      this.isCartOpen = true
    },

    closeCart() {
      this.isCartOpen = false
    },

    toggleCart() {
      this.isCartOpen = !this.isCartOpen
    },

    checkoutViaWhatsApp() {
      if (this.items.length === 0) return null

      const targetUrl = this.whatsappOrderUrl

      // Safely launch WhatsApp in new tab
      if (typeof window !== 'undefined') {
        const link = document.createElement('a')
        link.href = targetUrl
        link.target = '_blank'
        link.rel = 'noopener noreferrer'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
      }

      return targetUrl
    },
  },
})
