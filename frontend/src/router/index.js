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
import { hasAdminToken, useAdminAuth } from '@/composables/useAdminAuth'

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
        component: AdminPanel,
        meta: { requiresAdmin: true }
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

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Navigation Guard 'check-auth': valida explícitamente el token y el rol del usuario en backend
router.beforeEach(async (to) => {
    const isAdminArea = to.path.startsWith('/admin')
    const isLoginPage = to.path === '/admin/login'

    if (isAdminArea && !isLoginPage) {
        if (!hasAdminToken()) {
            return {
                path: '/admin/login',
                query: {
                    auth_error: 'no_token',
                    msg: 'Debes iniciar sesión con una cuenta autorizada para acceder al panel.',
                    redirect: to.fullPath
                }
            }
        }

        const { checkAuth } = useAdminAuth()
        const auth = await checkAuth()

        if (!auth.authenticated) {
            return {
                path: '/admin/login',
                query: {
                    auth_error: 'invalid_session',
                    msg: auth.error || 'Sesión no autorizada o expirada.',
                    redirect: to.fullPath
                }
            }
        }
    }

    return true
})

export default router