import { storeToRefs } from 'pinia'
import { useCartStore } from '@/stores/cart'

export const useCart = () => {
  const store = useCartStore()
  const {
    items,
    activePromo,
    orderType,
    selectedBranch,
    customer,
    isCartOpen,
    totalCount,
    subtotal,
    discount,
    deliveryFee,
    total,
    formattedSubtotal,
    formattedDiscount,
    formattedDeliveryFee,
    formattedTotal,
    whatsappOrderUrl,
    whatsappMessage,
  } = storeToRefs(store)

  return {
    // Reactive State & Getters from Pinia
    cartItems: items,
    items,
    activePromo,
    orderType,
    selectedBranch,
    customerDetails: customer,
    customer,
    isCartOpen,
    totalCount,
    subtotal,
    discount,
    deliveryFee,
    total,
    formattedSubtotal,
    formattedDiscount,
    formattedDeliveryFee,
    formattedTotal,
    whatsappOrderUrl,
    whatsappMessage,

    // Pinia Store Actions
    addToCart: (product, quantity, notes) => store.addItem(product, quantity, notes),
    addItem: (product, quantity, notes) => store.addItem(product, quantity, notes),
    updateQuantity: (index, delta) => store.updateQuantity(index, delta),
    removeItem: (index) => store.removeItem(index),
    clearCart: () => store.clearCart(),
    applyPromoCode: (code) => store.applyPromoCode(code),
    removePromo: () => store.removePromo(),
    openCart: () => store.openCart(),
    closeCart: () => store.closeCart(),
    toggleCart: () => store.toggleCart(),
    checkoutViaWhatsApp: () => store.checkoutViaWhatsApp(),
    store,
  }
}
