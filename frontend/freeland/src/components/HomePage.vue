<template>
  <div class="home-page">
    <SEOHead
      title="FreeLand — биржа фриланса: поиск исполнителей и заказов"
      description="FreeLand — платформа для фрилансеров и заказчиков. Публикуйте проекты, находите проверенных специалистов и проводите безопасные сделки через эскроу."
      keywords="фриланс, биржа фриланса, найти фрилансера, заказы для фрилансеров, удалённая работа"
      :canonical="canonicalUrl"
    />
    <!-- Усиленный динамический фон с частицами в синих тонах -->
    <div class="dynamic-background">
      <div class="gradient-sphere sphere-1"></div>
      <div class="gradient-sphere sphere-2"></div>
      <div class="gradient-sphere sphere-3"></div>
      
      <div class="grid-overlay"></div>
      <div class="noise-overlay"></div>
      
      <div class="particles">
        <div v-for="n in 50" :key="n" class="particle" :style="getParticleStyle(n)"></div>
      </div>
      
      <div class="light-spot spot-1"></div>
      <div class="light-spot spot-2"></div>
      <div class="light-spot spot-3"></div>
      <div class="light-spot spot-4"></div>
    </div>

    <!-- Навигационное меню -->
    <HeaderMenu />

    <!-- Баннер -->
    <section class="hero-banner">
      <div class="container">
        <div class="banner-content ios-glass ios-glass-heavy">
          <h1>У нас самые выгодные условия работы</h1>
          <h2 class="highlight">комиссия не более 5% и никаких скрытых платежей</h2>
          <button @click="handleStartButton" class="cta-button ios-glass ios-glass-heavy">
            <span class="button-text">{{ startButtonText }}</span>
            <span class="button-glow"></span>
          </button>
        </div>
      </div>
    </section>

    <!-- Лучшие фрилансеры — пьедестал -->
    <section class="top-freelancers">
      <div class="container">
        <h3 class="section-title">Лучшие фрилансеры</h3>
        <p class="section-subtitle">Топ-3 специалиста по рейтингу платформы</p>

        <div v-if="freelancersLoading" class="loading-state">
          <div class="loader ios-glass">
            <div class="loader-spinner"></div>
            <p>Загрузка фрилансеров...</p>
          </div>
        </div>

        <div v-else-if="podium.length" class="podium">
          <div
            v-for="item in podium"
            :key="item.id"
            class="podium-place"
            :class="`place-${item.place}`"
            @click="openFreelancerProfile(item.id)"
          >
            <div class="podium-card">
              <div v-if="item.place === 1" class="podium-crown">👑</div>
              <div class="podium-avatar">
                <img :src="avatarSrc(item, API_URL)" :alt="'Аватар фрилансера ' + (item.full_name || item.name || item.login)" loading="lazy" />
              </div>
              <h4 class="podium-name">{{ item.full_name || item.name || item.login }}</h4>
              <p class="podium-rating">
                <span class="stars">{{ getStars(item.rating) }}</span>
                <span class="rating-value">{{ Number(item.rating || 0).toFixed(1) }}</span>
              </p>
            </div>
            <div class="podium-base">
              <span class="podium-rank">{{ item.place }}</span>
            </div>
          </div>
        </div>

        <div v-else class="empty-freelancers">Пока нет фрилансеров для рейтинга</div>
      </div>
    </section>

    <!-- Как это работает -->
    <section class="how-it-works">
      <div class="container">
        <h3 class="section-title">Как это работает</h3>
        <p class="section-subtitle">Всего несколько шагов от идеи до результата</p>

        <div class="how-toggle ios-glass">
          <button
            class="how-toggle-btn"
            :class="{ active: howRole === 'customer' }"
            @click="howRole = 'customer'"
          >
            Я заказчик
          </button>
          <button
            class="how-toggle-btn"
            :class="{ active: howRole === 'freelancer' }"
            @click="howRole = 'freelancer'"
          >
            Я фрилансер
          </button>
        </div>

        <div class="how-steps">
          <div
            v-for="(step, i) in howSteps"
            :key="i"
            class="how-step ios-glass ios-glass-heavy"
          >
            <span class="how-step-num">{{ i + 1 }}</span>
            <div class="how-step-illustration" v-html="howIcons[step.icon]"></div>
            <h4 class="how-step-title">{{ step.title }}</h4>
            <p class="how-step-desc">{{ step.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Плюсы фриланса -->
    <section class="benefits">
      <div class="container">
        <h3 class="section-title">Почему выбирают наш фриланс</h3>
        <p class="section-subtitle">Прозрачные условия и забота о каждом участнике</p>

        <div class="benefits-grid">
          <div
            v-for="(benefit, i) in benefits"
            :key="benefit.id"
            class="benefit-card ios-glass ios-glass-heavy"
          >
            <span class="benefit-index">{{ String(i + 1).padStart(2, '0') }}</span>
            <span class="benefit-watermark">{{ String(i + 1).padStart(2, '0') }}</span>
            <div class="benefit-accent"></div>
            <h4 class="benefit-title">{{ benefit.title }}</h4>
            <p class="benefit-desc">{{ benefit.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Футер -->
    <FooterApp />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import HeaderMenu from '@/elements/HeaderMenu.vue'
import FooterApp from '@/elements/FooterApp.vue'
import SEOHead from '@/elements/SEOHead.vue'
import { avatarSrc } from '@/utils/avatar'
import '../assets/css/mainStyle.css'

const router = useRouter()
const API_URL = ''

// Canonical-ссылка для SEO
const canonicalUrl = computed(() => (typeof window !== 'undefined' ? window.location.origin : '') + '/')

// Состояние пользователя
const user = ref(null)
const userLoading = ref(true)

// Состояние фрилансеров
const freelancers = ref([])
const freelancersLoading = ref(true)

// Статичные преимущества
const benefits = [
  { id: 1, title: 'Низкая комиссия', description: 'Всего до 5% за сделку и никаких скрытых платежей — вы зарабатываете больше.' },
  { id: 2, title: 'Быстрые выплаты', description: 'Деньги поступают на баланс в день приёмки работы по проекту.' },
  { id: 3, title: 'Безопасные сделки', description: 'Бюджет резервируется через безопасную сделку, поэтому защищены и заказчик, и исполнитель.' }
]

// Блок «Как это работает»
const howRole = ref('customer')

const howStepsData = {
  customer: [
    { icon: 'create', title: 'Создайте проект', description: 'Опишите задачу, укажите бюджет и срок выполнения.' },
    { icon: 'choose', title: 'Выберите исполнителя', description: 'Получайте отклики фрилансеров и выбирайте лучшего по рейтингу.' },
    { icon: 'shield', title: 'Безопасная сделка', description: 'Бюджет резервируется на вашем балансе и хранится до приёмки работы.' },
    { icon: 'wallet', title: 'Примите и оплатите', description: 'Средства уходят исполнителю, комиссия сервиса — всего до 5%.' }
  ],
  freelancer: [
    { icon: 'search', title: 'Найдите заказ', description: 'Просматривайте проекты в каталоге и фильтруйте по категориям.' },
    { icon: 'send', title: 'Откликнитесь', description: 'Отправьте заявку заказчику и обсудите детали в чате.' },
    { icon: 'work', title: 'Выполните работу', description: 'Сделайте задачу в срок и сдайте результат через платформу.' },
    { icon: 'wallet', title: 'Получите оплату', description: 'Деньги поступают на ваш баланс в день приёмки работы.' }
  ]
}

// Inline SVG-иллюстрации для шагов (в синей гамме сайта)
const howIcons = {
  create: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M12 12v5M9.5 14.5h5"/></svg>',
  choose: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
  shield: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>',
  wallet: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg>',
  search: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>',
  send: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4z"/><path d="M22 2 11 13"/></svg>',
  work: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>'
}

const howSteps = computed(() => howStepsData[howRole.value])

// Текст кнопки в зависимости от авторизации
const startButtonText = computed(() => {
  return user.value ? 'Перейти к заказам' : 'Начать работу бесплатно'
})

const displayedFreelancers = computed(() => {
  return [...freelancers.value]
    .filter((elem) => elem.role === 'freelancer')
    .sort((a, b) => b.rating - a.rating)
    .slice(0, 3)
})

// Топ-3 с проставленными местами для пьедестала
const podium = computed(() =>
  displayedFreelancers.value.map((f, i) => ({ ...f, place: i + 1 }))
)

// Получение текущего пользователя
const fetchCurrentUser = async () => {
  const token = localStorage.getItem('token')
  
  if (!token) {
    user.value = null
    userLoading.value = false
    return
  }

  try {
    const response = await fetch(`${API_URL}/api/user`, {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      }
    })

    const data = await response.json()
    
    if (response.ok && data.success) {
      user.value = data.user
    } else {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      user.value = null
    }
  } catch (error) {
    console.error('Ошибка получения пользователя:', error)
    user.value = null
  } finally {
    userLoading.value = false
  }
}

// Получение списка фрилансеров из БД
const fetchFreelancers = async () => {
  freelancersLoading.value = true
  
  try {
    const response = await fetch(`${API_URL}/api/users`)
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
    
    // Обрабатываем разные форматы ответа
    if (data.users) {
      freelancers.value = data.users
    } else if (Array.isArray(data)) {
      freelancers.value = data
    } else {
      freelancers.value = []
    }
    
    console.log('Загружены фрилансеры:', freelancers.value)
    
  } catch (error) {
    console.error('Ошибка при загрузке фрилансеров:', error)
    freelancers.value = []
  } finally {
    freelancersLoading.value = false
  }
}

// Обработка нажатия на кнопку "Начать работу"
const handleStartButton = () => {
  if (user.value) {
    // Авторизованный пользователь - на страницу проектов
    router.push('/projects')
  } else {
    // Неавторизованный пользователь - на регистрацию
    router.push('/register')
  }
}

// Открыть профиль фрилансера
const openFreelancerProfile = (id) => {
  router.push(`/freelancers/${id}`)
}

// Получить звезды рейтинга
const getStars = (rating) => {
  if (!rating) return '☆☆☆☆☆'
  const fullStars = Math.floor(rating)
  const halfStar = rating % 1 >= 0.5 ? 1 : 0
  const emptyStars = 5 - fullStars - halfStar
  
  return '★'.repeat(fullStars) + (halfStar ? '½' : '') + '☆'.repeat(emptyStars)
}

// Стили для частиц
const getParticleStyle = () => {
  const size = Math.random() * 4 + 2
  const duration = Math.random() * 20 + 10
  const delay = Math.random() * 10
  const left = Math.random() * 100
  const top = Math.random() * 100
  
  return {
    width: `${size}px`,
    height: `${size}px`,
    left: `${left}%`,
    top: `${top}%`,
    animation: `floatParticle ${duration}s infinite linear`,
    animationDelay: `${delay}s`,
    opacity: Math.random() * 0.3 + 0.1,
    background: `rgba(168, 209, 255, ${Math.random() * 0.4 + 0.2})`
  }
}

// Загружаем данные при монтировании компонента
onMounted(() => {
  fetchCurrentUser()
  fetchFreelancers()
})
</script>

<style scoped>
/* ===== Лоадер (как на странице проектов) ===== */
.loading-state {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 320px;
}

.loader {
  padding: 40px 60px;
  text-align: center;
}

.loader-spinner {
  width: 50px;
  height: 50px;
  border: 3px solid rgba(168, 209, 255, 0.1);
  border-top-color: #F0F8FF;
  border-radius: 50%;
  margin: 0 auto 20px;
  animation: spin 1s linear infinite;
}

.loader p {
  color: #F0F8FF;
  font-size: 1.1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ===== Подзаголовок секций ===== */
.section-subtitle {
  text-align: center;
  color: var(--text-secondary);
  opacity: 0.85;
  font-size: 1.1rem;
  margin: 16px auto 0;
  max-width: 560px;
}

/* Небольшой отступ вниз для подзаголовка секции преимуществ */
.benefits .section-subtitle {
  margin-bottom: 40px;
}

/* ===== Как это работает ===== */
.how-it-works {
  padding: 40px 0;
}

.how-toggle {
  display: inline-flex;
  gap: 6px;
  padding: 6px;
  margin: 28px auto 40px;
  border-radius: 9999px;
}

.how-it-works .container {
  text-align: center;
}

.how-toggle-btn {
  padding: 10px 28px;
  border: none;
  border-radius: 9999px;
  background: transparent;
  color: var(--text-secondary);
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.how-toggle-btn.active {
  background: linear-gradient(135deg, var(--blue-dark), var(--blue-medium));
  color: #FFFFFF;
  box-shadow: 0 6px 18px rgba(8, 51, 88, 0.4);
}

.how-steps {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 22px;
  text-align: left;
}

.how-step {
  position: relative;
  padding: 30px 26px;
  border-radius: 24px;
  transition: transform 0.3s ease;
}

.how-step:hover {
  transform: translateY(-6px);
}

.how-step-num {
  position: absolute;
  top: 16px;
  right: 18px;
  font-size: 2.4rem;
  font-weight: 800;
  line-height: 1;
  color: rgba(168, 209, 255, 0.18);
}

.how-step-illustration {
  width: 64px;
  height: 64px;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(42, 127, 201, 0.35), rgba(58, 148, 223, 0.18));
  border: 1px solid rgba(168, 209, 255, 0.3);
  box-shadow: 0 6px 18px rgba(8, 51, 88, 0.35);
  margin-bottom: 18px;
}

.how-step-illustration :deep(svg) {
  width: 32px;
  height: 32px;
  color: var(--blue-very-pale);
  filter: drop-shadow(0 0 6px var(--rating-glow));
}

.how-step-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #FFFFFF;
  margin-bottom: 10px;
}

.how-step-desc {
  color: var(--text-secondary);
  opacity: 0.85;
  line-height: 1.55;
  font-size: 0.95rem;
}

@media (max-width: 900px) {
  .how-steps {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 520px) {
  .how-steps {
    grid-template-columns: 1fr;
  }

  .how-toggle-btn {
    padding: 10px 20px;
    font-size: 0.95rem;
  }
}

/* ===== Пьедестал лучших фрилансеров ===== */
.podium {
  display: flex;
  justify-content: center;
  align-items: flex-end;
  gap: 26px;
  margin-top: 64px;
  flex-wrap: nowrap;
}

.podium-place {
  display: flex;
  flex-direction: column;
  align-items: stretch;
  width: 260px;
  cursor: pointer;
}

/* Порядок: 2-е слева, 1-е по центру, 3-е справа */
.place-1 { order: 2; }
.place-2 { order: 1; }
.place-3 { order: 3; }

/* Карточка фрилансера сверху пьедестала */
.podium-card {
  position: relative;
  text-align: center;
  padding: 30px 22px 26px;
  border-radius: 28px 28px 0 0;
  background: rgba(10, 77, 140, 0.18);
  backdrop-filter: blur(25px) saturate(180%);
  -webkit-backdrop-filter: blur(25px) saturate(180%);
  border: 1px solid rgba(168, 209, 255, 0.22);
  border-bottom: none;
  transition: transform 0.35s ease, box-shadow 0.35s ease;
}

.podium-place:hover .podium-card {
  transform: translateY(-10px);
  box-shadow: 0 26px 55px rgba(8, 51, 88, 0.5);
}

.place-1 .podium-card {
  background: linear-gradient(180deg, rgba(255, 215, 0, 0.14), rgba(10, 77, 140, 0.2));
  border-color: rgba(255, 215, 0, 0.4);
}

.podium-crown {
  position: absolute;
  top: -30px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 2.6rem;
  filter: drop-shadow(0 4px 12px rgba(255, 215, 0, 0.55));
}

.podium-avatar {
  width: 96px;
  height: 96px;
  margin: 0 auto 16px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid rgba(168, 209, 255, 0.45);
  box-shadow: 0 10px 26px rgba(8, 51, 88, 0.4);
}

.podium-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.place-1 .podium-avatar {
  width: 124px;
  height: 124px;
  border-color: #FFD700;
  box-shadow: 0 0 30px rgba(255, 215, 0, 0.5);
}

.podium-name {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 8px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.place-1 .podium-name { font-size: 1.35rem; }

.podium-rating .stars {
  color: var(--rating);
  font-size: 1rem;
  margin-right: 6px;
  text-shadow: 0 0 8px var(--rating-glow);
  -webkit-text-fill-color: var(--rating);
}

.podium-rating .rating-value {
  color: var(--text-tertiary);
  font-weight: 600;
}

/* Основание пьедестала */
.podium-base {
  display: flex;
  align-items: flex-start;
  justify-content: center;
  border-radius: 0 0 10px 10px;
  border: 1px solid rgba(168, 209, 255, 0.22);
  border-top: none;
  position: relative;
  overflow: hidden;
}

.place-1 .podium-base {
  height: 150px;
  background: linear-gradient(180deg, rgba(255, 215, 0, 0.32), rgba(218, 165, 32, 0.1));
  border-color: rgba(255, 215, 0, 0.35);
}

.place-2 .podium-base {
  height: 108px;
  background: linear-gradient(180deg, rgba(224, 224, 255, 0.3), rgba(168, 168, 224, 0.1));
}

.place-3 .podium-base {
  height: 78px;
  background: linear-gradient(180deg, rgba(205, 127, 50, 0.3), rgba(161, 92, 34, 0.1));
}

.podium-rank {
  margin-top: 16px;
  font-size: 3.4rem;
  font-weight: 800;
  color: #ffffff;
  text-shadow: 0 2px 16px rgba(0, 0, 0, 0.45);
  letter-spacing: -2px;
}

/* Чуть больше воздуха вокруг цифры 3 (низкое основание) */
.place-3 .podium-rank {
  margin-top: 12px;
  padding: 0 8px 6px;
}

.empty-freelancers {
  text-align: center;
  color: var(--text-secondary);
  opacity: 0.8;
  margin-top: 40px;
}

/* ===== Преимущества (без иконок) ===== */
.benefit-card {
  position: relative;
  overflow: hidden;
  text-align: left;
  padding: 44px 38px;
  border-radius: 28px;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.benefit-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 30px 70px rgba(8, 51, 88, 0.5);
}

.benefit-index {
  display: block;
  font-size: 2.6rem;
  font-weight: 800;
  line-height: 1;
  letter-spacing: -1px;
  background: linear-gradient(135deg, #A8D1FF, #6BB2F0);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 22px;
  position: relative;
  z-index: 2;
}

/* Крупный полупрозрачный номер-водяной знак */
.benefit-watermark {
  position: absolute;
  top: -18px;
  right: 6px;
  font-size: 9rem;
  font-weight: 800;
  line-height: 1;
  color: rgba(168, 209, 255, 0.06);
  pointer-events: none;
  z-index: 1;
}

.benefit-accent {
  width: 48px;
  height: 4px;
  border-radius: 2px;
  background: linear-gradient(90deg, #A8D1FF, transparent);
  margin-bottom: 22px;
  transition: width 0.4s ease;
  position: relative;
  z-index: 2;
}

.benefit-card:hover .benefit-accent { width: 84px; }

.benefit-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 14px;
  color: var(--text-primary);
  position: relative;
  z-index: 2;
}

.benefit-desc {
  font-size: 1.05rem;
  line-height: 1.65;
  color: var(--text-secondary);
  opacity: 0.92;
  position: relative;
  z-index: 2;
}

/* ===== Адаптивность ===== */
@media (max-width: 760px) {
  .podium {
    flex-direction: column;
    align-items: center;
    gap: 18px;
  }

  .podium-place {
    order: 0 !important;
    width: 100%;
    max-width: 340px;
  }

  .podium-base {
    height: 64px !important;
  }

  .podium-rank {
    font-size: 2.4rem;
    margin-top: 10px;
  }
}
</style>