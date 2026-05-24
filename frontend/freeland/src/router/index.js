import { createRouter, createWebHistory } from 'vue-router'


import HomePage from '@/components/HomePage.vue'
import RegisterUser from '@/components/RegisterUser.vue'
import LoginUser from '@/components/LoginUser.vue'
import ProjectPage from '@/components/ProjectPage.vue'
import ProjectDetail from '@/components/ProjectDetail.vue'
import FreelancersPage from '@/components/FreelancersPage.vue'
import UserDetail from '@/components/UserDetail.vue'
import AdminPage from '@/components/AdminPage.vue'
import ProfilPage from '@/components/ProfilPage.vue'
import MyProjects from '@/components/MyProjects.vue'
import MyChats from '@/components/MyChats.vue'
import EmploeyPage from '@/components/EmploeyPage.vue'
import NotFound from '@/components/NotFound.vue'
import StatePage from '@/components/StatePage.vue'

const routes = [
    {
    path: '/',
    component: HomePage,
    meta: {
      title: 'FreeLand - Фриланс платформа',
      description: 'Платформа для поиска фрилансеров и публикации проектов.',
      keywords: 'фриланс, проекты, заказчики, исполнители'
    }
  },
    {
    path: '/register',
    component: RegisterUser,
    meta: {
      title: 'Регистрация - FreeLand',
      description: 'Создайте аккаунт на FreeLand и начните работу.',
      keywords: 'регистрация, аккаунт, freelance'
    }
  },
    {
    path: '/login',
    component: LoginUser,
    meta: {
      title: 'Вход - FreeLand',
      description: 'Войдите в аккаунт FreeLand.',
      keywords: 'вход, авторизация, email'
    }
  },
    {
    path: '/projects',
    component: ProjectPage,
    meta: {
      title: 'Проекты - FreeLand',
      description: 'Актуальные проекты для фрилансеров.',
      keywords: 'проекты, вакансии, заказ'
    }
  },
    {
    path: '/projectDetail',
    component: ProjectDetail,
    meta: {
      title: 'Детали проекта - FreeLand',
      description: 'Подробная информация о проекте.',
      keywords: 'проект, детали, отклик'
    }
  },
      {
    path: '/projects/:id',
    name: 'project-detail',
    component: ProjectDetail,
    meta: {
      title: 'Детали проекта - FreeLand',
      description: 'Подробная информация о выбранном проекте.',
      keywords: 'проект, фриланс, описание'
    }
  },
  {
    path: '/freelancer',
    component: FreelancersPage,
    meta: {
      title: 'Фрилансеры - FreeLand',
      description: 'Каталог фрилансеров платформы.',
      keywords: 'фрилансеры, специалисты, портфолио'
    }
  },
    {
    path: '/freelancers/:id',
    name: 'user-detail',
    component: UserDetail,
    meta: {
      title: 'Профиль специалиста - FreeLand',
      description: 'Просмотр профиля и отзывов специалиста.',
      keywords: 'профиль, отзывы, специалист'
    }
  },
  {
    path: '/users/:id',
    name: 'user-detail-alt',
    component: UserDetail,
    meta: {
      title: 'Профиль пользователя - FreeLand',
      description: 'Детальная информация о пользователе.',
      keywords: 'пользователь, профиль'
    }
  },
  {
    path: '/admin',
    component: AdminPage,
    meta: {
      title: 'Админ панель - FreeLand',
      description: 'Панель управления платформой.',
      keywords: 'админ, управление, модерация',
      noindex: true
    }
  },{
    path: '/my-projects',
    component: MyProjects,
    meta: {
      title: 'Мои проекты - FreeLand',
      description: 'Управление вашими проектами и заявками.',
      keywords: 'мои проекты, заявки, управление',
      noindex: true
    }
  },{
    path: '/my-chats',
    component: MyChats,
    meta: {
      title: 'Мои чаты - FreeLand',
      description: 'Общение с заказчиками и исполнителями.',
      keywords: 'чаты, сообщения, проект',
      noindex: true
    }
  },{
    path: '/profile',
    component: ProfilPage,
    meta: {
      title: 'Мой профиль - FreeLand',
      description: 'Личный кабинет пользователя FreeLand.',
      keywords: 'профиль, кабинет, баланс',
      noindex: true
    }
  },{
    path: '/emploee',
    component: EmploeyPage,
    meta: {
      title: 'Команда и отзывы - FreeLand',
      description: 'Информация о команде и отзывы пользователей.',
      keywords: 'команда, отзывы, контакты'
    }
  },{
    path: '/article',
    name: 'ArticlePage',
    component: StatePage,
    meta: {
      title: 'Статьи - FreeLand',
      description: 'Полезные статьи и материалы.',
      keywords: 'статьи, блог, советы'
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound,
    meta: {
      title: 'Страница не найдена - FreeLand',
      description: 'Запрошенная страница не найдена.',
      keywords: '404, not found',
      noindex: true
    }
  }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

const setOrCreateMeta = (name, content) => {
  let meta = document.querySelector(`meta[name="${name}"]`)
  if (!meta) {
    meta = document.createElement('meta')
    meta.setAttribute('name', name)
    document.head.appendChild(meta)
  }
  meta.setAttribute('content', content)
}

router.afterEach((to) => {
  const title = to.meta?.title || 'FreeLand'
  const description = to.meta?.description || 'Платформа для фрилансеров и заказчиков.'
  const keywords = to.meta?.keywords || 'freeland, freelance'

  document.title = title
  setOrCreateMeta('description', description)
  setOrCreateMeta('keywords', keywords)
  setOrCreateMeta('robots', to.meta?.noindex ? 'noindex, nofollow' : 'index, follow')
})

export default router
