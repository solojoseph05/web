// src/config/menu.js
// Centraliza el catálogo del menú para reutilizarlo en la landing y en la página /menu.
import polloEntero from '@/assets/images/POLLO ENTERO.webp'
import polloPartes from '@/assets/images/POLLO PARTES1.webp'
import platoCompleto from '@/assets/images/PLATILLO COMPLETO.webp'
import polloNegro from '@/assets/images/POLLO PLATO NEGRO.webp'
import pollo1 from '@/assets/images/3.webp'
import nuggets from '@/assets/images/nuggets.webp'
import papasFranc from '@/assets/images/PAPAS FRANC.webp'
import papasGajo from '@/assets/images/PAPAS GAJO.webp'
import papaAsada from '@/assets/images/papa asada.webp'
import ensalada1 from '@/assets/images/ENSALADA 1.webp'
import ensalada4 from '@/assets/images/ENSALADA 4.webp'
import ensalada5 from '@/assets/images/ENSALADA 5.webp'
import quesadilla from '@/assets/images/QUESADILLA.webp'
import tacosCartera from '@/assets/images/TACOS CARTERA.webp'
import tacosServidos from '@/assets/images/TACOS SERVIDOS.webp'
import tortillas from '@/assets/images/TORTILLAS.webp'
import salsas from '@/assets/images/salsas.webp'
import spaguetti from '@/assets/images/SPAGUETTI.webp'
import hamburguesa from '@/assets/images/HAMBURGUESA.webp'

export default {
  featured: [
    { id: 1, name: "Pollo Entero Clásico", description: "Pollo asado con especias tradicionales, acompañado de salsa de la casa.", price: 180, image: polloEntero, badge: "Más vendido", category: "Pollo Entero" },
    { id: 2, name: "Pollo Entero Especial", description: "Con salsa especial de la casa y especias secretas.", price: 220, image: polloPartes, badge: "Premium", category: "Pollo Entero" },
    { id: 3, name: "Paquete Familiar", description: "Pollo entero + 2 complementos + refresco familiar.", price: 280, image: platoCompleto, badge: "Oferta", category: "Paquetes" },
    { id: 4, name: "Medio Pollo Clásico", description: "Perfecto para una persona, con guarniciones incluidas.", price: 95, image: polloNegro, badge: "Popular", category: "Medio Pollo" },
    { id: 5, name: "Papas a la Francesa", description: "Crujientes y doradas, ideales para acompañar cualquier pedido.", price: 55, image: papasFranc, badge: "Complemento", category: "Complementos" },
    { id: 6, name: "Papas Gajo", description: "Sabrosas papas gajo con toque de especias.", price: 65, image: papasGajo, badge: "Nuevo", category: "Complementos" },
    { id: 7, name: "Papas Asadas", description: "Papas asadas con mantequilla y especias.", price: 60, image: papaAsada, badge: "Recomendado", category: "Complementos" },
    { id: 8, name: "Ensalada Fresca", description: "Ensalada fresca con lechuga, tomate y aderezo ligero.", price: 70, image: ensalada1, badge: "Saludable", category: "Ensaladas" },
    { id: 9, name: "Ensalada Mixta", description: "Ensalada con pollo, aguacate y vegetales frescos.", price: 95, image: ensalada4, badge: "Popular", category: "Ensaladas" },
    { id: 10, name: "Ensalada de la Casa", description: "Ensalada tropical con pollo y aderezo especial.", price: 100, image: ensalada5, badge: "Favorita", category: "Ensaladas" },
    { id: 11, name: "Quesadilla Especial", description: "Quesadilla rellena de queso, pollo y salsa especial.", price: 85, image: quesadilla, badge: "Oferta", category: "Tacos" },
    { id: 12, name: "Tacos de Pollo", description: "Tacos servidos con cebolla, cilantro y salsa.", price: 75, image: tacosServidos, badge: "Clásico", category: "Tacos" },
    { id: 13, name: "Taco Carreta", description: "Tacos estilo carreta con mucho sabor.", price: 80, image: tacosCartera, badge: "Especial", category: "Tacos" },
    { id: 14, name: "Salsas Variadas", description: "Cuatro salsas caseras para acompañar tu pedido.", price: 30, image: salsas, badge: "Complemento", category: "Complementos" },
    { id: 15, name: "Spaguetti con Pollo", description: "Spaguetti con pollo desmenuzado y salsa cremosa.", price: 110, image: spaguetti, badge: "Nuevo", category: "Paquetes" },
    { id: 16, name: "Tortillas Calientes", description: "Tortillas recién hechas para tus tacos o complementar tu orden.", price: 20, image: tortillas, badge: "Complemento", category: "Complementos" },
    { id: 17, name: "Nuggets Dorados", description: "Nuggets crujientes perfectos para compartir.", price: 95, image: nuggets, badge: "Popular", category: "Complementos" },
    { id: 18, name: "Hamburguesa de Pollo", description: "Hamburguesa jugosa con pollo, lechuga y aderezo especial.", price: 120, image: hamburguesa, badge: "Recomendado", category: "Paquetes" },
    { id: 19, name: "Pollo Grande con Guarnición", description: "Pollo entero con papas y ensalada.", price: 240, image: pollo1, badge: "Promoción", category: "Paquetes" },
    { id: 20, name: "Pollo a la Brasa", description: "Porción de pollo a la brasa con salsa especial.", price: 160, image: polloEntero, badge: "Clásico", category: "Pollo Entero" }
  ],
  categories: [
    { id: "all", name: "Todos" },
    { id: "whole", name: "Pollo Entero" },
    { id: "half", name: "Medio Pollo" },
    { id: "combos", name: "Paquetes" },
    { id: "sides", name: "Complementos" },
    { id: "drinks", name: "Bebidas" },
    { id: "tacos", name: "Tacos" },
    { id: "ensaladas", name: "Ensaladas" },
    { id: "postres", name: "Postres" }
  ]
}
