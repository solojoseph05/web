import { createRouter, createWebHistory } from 'vue-router'

import Home from '@/pages/Landing/Home.vue'
import Menu from '@/pages/Landing/Menu.vue'
import VisionMission from '@/pages/Landing/VisionMission.vue'
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

    if (isLoginPage && tokenAvailable) {
        return { path: '/admin' }
    }

    return true
})

export default router