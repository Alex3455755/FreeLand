import { createRouter, createWebHistory } from 'vue-router'


import HomePage from '@/components/HomePage.vue'
import RegisterUser from '@/components/RegisterUser.vue'
import LoginUser from '@/components/LoginUser.vue'
import ProjectPage from '@/components/ProjectPage.vue'
import ProjectDetail from '@/components/ProjectDetail.vue'
import FreelancersPage from '@/components/FreelancersPage.vue'
import UserDetail from '@/components/UserDetail.vue'
import AdminPage from '@/components/AdminPage.vue'

const routes = [
    { path: '/', component: HomePage },
    {path: '/register', component: RegisterUser},
    {path: '/login', component: LoginUser},
    {path: '/projects', component: ProjectPage},
    {path: '/projectDetail',component:ProjectDetail},
      {
    path: '/projects/:id',
    name: 'project-detail',
    component: ProjectDetail,
    meta: {
      title: 'Детали проекта | Freelance Platform'
    }
  },
  {path: '/freelancer', component: FreelancersPage},
    {
    path: '/freelancers/:id',
    name: 'user-detail',
    component: UserDetail
  },
  {
    path: '/users/:id',
    name: 'user-detail-alt',
    component: UserDetail
  },
  {
    path: '/admin',
    component: AdminPage
  }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
