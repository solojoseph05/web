// src/config/promotions.js
// Contiene las promociones activas que se muestran en la landing page.
// Se mantienen aquí para que el contenido sea fácil de actualizar sin tocar componentes.
import polloEntero from '@/assets/images/POLLO ENTERO.webp'
import platoCompleto from '@/assets/images/PLATILLO COMPLETO.webp'
import polloPartes from '@/assets/images/POLLO PARTES1.webp'
import heroImage from '@/assets/images/3.webp'

export default {
  active: [
    {
      id: 1,
      title: "2x1 en Pollo Entero",
      description: "Lleva 2 pollos enteros por el precio de 1. ¡Válido todos los martes!",
      image: polloEntero,
      validUntil: "2024-12-31",
      code: "POLLO2X1",
      badge: "Oferta Especial"
    },
    {
      id: 2,
      title: "Paquete Familiar Especial",
      description: "Pollo entero + 4 complementos + refresco familiar a precio especial",
      image: platoCompleto,
      validUntil: "2024-12-31",
      code: "FAMILIA30",
      badge: "Más Vendido"
    },
    {
      id: 3,
      title: "Combo Estudiante",
      description: "Medio pollo + papas + refresco. ¡Presenta tu credencial!",
      image: polloPartes,
      validUntil: "2024-12-31",
      code: "ESTUDIANTE",
      badge: "Promoción Especial"
    },
    {
      id: 4,
      title: "Promoción de Fin de Semana",
      description: "Ordena 2 pollos enteros y llévate un postre gratis",
      image: heroImage,
      validUntil: "2024-12-31",
      code: "FINDE20",
      badge: "Solo Finde"
    }
  ]
}