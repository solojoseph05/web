// src/router/index.js
/**
 * CONFIGURACIÓN DE ENRUTAMIENTO (VUE ROUTER 4)
 * ============================================
 * Define todas las rutas públicas y privadas de la aplicación:
 * - Rutas públicas: Home (/), Menú (/menu), Promociones (/promociones), Sucursales (/sucursales), Contacto (/contacto), etc.
 * - Rutas administrativas: Login (/admin/login) y Panel (/admin).
 * - Guard de navegación (beforeEach): Protege las rutas administrativas para usuarios no autenticados.
 */

import { createRouter, createWebHistory } from 'vue-router'

import Home from '@/pages/Landing/Home.vue'
import Menu from '@/pages/Landing/Menu.vue'
import VisionMission from '@/pages/Landing/VisionMission.vue'
import Branches from '@/pages/Landing/Branches.vue'
import Promotions from '@/pages/Landing/Promotions.vue'
import About from '@/pages/Landing/About.vue'
import Contact from '@/pages/Landing/Contact.vue'
import OrderHistory from '@/pages/Landing/OrderHistory.vue'
import AdminLogin from '@/pages/Admin/Login.vue'
import AdminPanel from '@/pages/Admin/Panel.vue'
import NotFound from '@/pages/NotFound.vue'
import { hasAdminToken } from '@/composables/useAdminAuth'

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/menu',
        name: 'menu',
        component: Menu
    },
    {
        path: '/promociones',
        name: 'promociones',
        component: Promotions
    },
    {
        path: '/sucursales',
        name: 'sucursales',
        component: Branches
    },
    {
        path: '/nosotros',
        name: 'nosotros',
        component: About
    },
    {
        path: '/contacto',
        name: 'contacto',
        component: Contact
    },
    {
        path: '/historial',
        name: 'historial',
        component: OrderHistory
    },
    {
        path: '/pedidos',
        redirect: '/historial'
    },
    {
        path: '/vision-mision',
        name: 'vision-mision',
        component: VisionMission
    },
    {
        path: '/admin/login',
        name: 'admin-login',
        component: AdminLogin
    },
    {
        path: '/admin',
        name: 'admin-panel',
        component: AdminPanel
    },
    {
        path: '/admin/contact-messages',
        redirect: '/admin'
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'notfound',
        component: NotFound
    }
]

const router=createRouter({

    history:createWebHistory(),

    routes

})

router.beforeEach((to) => {
    const isAdminArea = to.path.startsWith('/admin')
    const isLoginPage = to.path === '/admin/login'
    const tokenAvailable = hasAdminToken()

    if (isAdminArea && !isLoginPage && !tokenAvailable) {
        return { path: '/admin/login' }
    }

    return true
})

export default router