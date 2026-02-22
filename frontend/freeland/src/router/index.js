import { createRouter, createWebHistory } from 'vue-router'


import HomePage from '@/components/HomePage.vue'
import RegisterUser from '@/components/RegisterUser.vue'

const routes = [
    { path: '/', component: HomePage },
    {path: '/register', component: RegisterUser}
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
