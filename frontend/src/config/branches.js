// src/config/branches.js
// Datos estáticos de sucursales para la landing page.
import polloEntero from '@/assets/images/POLLO ENTERO.webp'
import platoCompleto from '@/assets/images/PLATILLO COMPLETO.webp'
import polloPartes from '@/assets/images/POLLO PARTES1.webp'
import heroImage from '@/assets/images/3.webp'

export default [
  {
    id: 1,
    name: 'Sucursal Centro Histórico',
    address: 'Av. 5 de Mayo #123, Colonia Centro, CDMX',
    phone: '555-123-4567',
    schedule: 'Lun - Dom: 10:00 AM - 10:00 PM',
    image: polloEntero,
    mapLink: 'https://maps.google.com/?q=...', 
    isOpen: true
  },
  {
    id: 2,
    name: 'Sucursal Polanco',
    address: 'Av. Presidente Masaryk #456, Colonia Polanco, CDMX',
    phone: '555-987-6543',
    schedule: 'Lun - Dom: 11:00 AM - 11:00 PM',
    image: platoCompleto,
    mapLink: 'https://maps.google.com/?q=...',
    isOpen: false
  },
  {
    id: 3,
    name: 'Sucursal Coyoacán',
    address: 'Calle Hidalgo #789, Colonia Coyoacán, CDMX',
    phone: '555-456-7890',
    schedule: 'Lun - Vie: 12:00 PM - 9:00 PM',
    image: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
    mapLink: 'https://maps.google.com/?q=...',
    isOpen: true
  },
  {
    id: 4,
    name: 'Sucursal Santa Fe',
    address: 'Av. Vasco de Quiroga #3800, Colonia Santa Fe, CDMX',
    phone: '555-321-0987',
    schedule: 'Lun - Sab: 10:00 AM - 9:00 PM',
    image: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=600&q=80',
    mapLink: 'https://maps.google.com/?q=...',
    isOpen: true
  },
  {
    id: 5,
    name: 'Sucursal Insurgentes',
    address: 'Av. Insurgentes Sur #1580, Colonia Insurgentes, CDMX',
    phone: '555-654-3210',
    schedule: 'Lun - Dom: 9:00 AM - 11:00 PM',
    image: heroImage,
    mapLink: 'https://maps.google.com/?q=...',
    isOpen: false
  },
  {
    id: 6,
    name: 'Sucursal Tláhuac',
    address: 'Av. Tláhuac #1200, Colonia Tláhuac, CDMX',
    phone: '555-789-0123',
    schedule: 'Lun - Sab: 10:00 AM - 8:00 PM',
    image: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=600&q=80',
    mapLink: 'https://maps.google.com/?q=...',
    isOpen: true
  },
  {
    id: 7,
    name: 'Sucursal Xochimilco',
    address: 'Av. Prolongación División del Norte #2500, Xochimilco, CDMX',
    phone: '555-890-1234',
    schedule: 'Sab - Dom: 11:00 AM - 7:00 PM',
    image: 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=600&q=80',
    mapLink: 'https://maps.google.com/?q=...',
    isOpen: false
  },
  {
    id: 8,
    name: 'Sucursal Universidad',
    address: 'Av. Universidad #1800, Colonia C.U., CDMX',
    phone: '555-901-2345',
    schedule: 'Lun - Dom: 8:00 AM - 10:00 PM',
    image: polloPartes,
    mapLink: 'https://maps.google.com/?q=...',
    isOpen: true
  }
]