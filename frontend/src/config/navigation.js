// src/config/navigation.js
// Este archivo centraliza los enlaces del navbar y sus IDs de sección.
// Si cambias un id o un href, también debes mantener los elementos section[id] del landing coherentes.
export default {
  main: [
    { id: 'home', label: 'Inicio', href: '#home' },
    { id: 'about', label: 'Nosotros', href: '#about' },
    { id: 'menu', label: 'Menú', href: '#menu' },
    { id: 'promotions', label: 'Promociones', href: '#promotions' },
    { id: 'branches', label: 'Sucursales', href: '#branches' },
    { id: 'jobs', label: 'Vacantes', href: '#jobs' },
    { id: 'contact', label: 'Contacto', href: '#contact' }
  ]
}