<template>
  <div class="home-page">
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
          <h1>Всем зарегистрировавшимся сейчас</h1>
          <h2 class="highlight">МЕСЯЦ ПЕРЕВОДЫ БЕЗ КОМИССИИ</h2>
          <button @click="handleStartButton" class="cta-button ios-glass ios-glass-heavy">
            <span class="button-text">{{ startButtonText }}</span>
            <span class="button-glow"></span>
          </button>
        </div>
      </div>
    </section>

    <!-- Лучшие фрилансеры -->
    <section class="top-freelancers">
      <div class="container">
        <h3 class="section-title">Лучшие фрилансеры</h3>
        <div v-if="freelancersLoading" class="loading-state">
          <div class="loader-sm"></div>
          <span>Загрузка фрилансеров...</span>
        </div>
        <div v-else class="freelancers-grid">
          <div 
            v-for="(freelancer,index) in displayedFreelancers"
            :key="freelancer.id"
            class="freelancer-card ios-glass ios-glass-heavy"
            @click="openFreelancerProfile(freelancer.id)"
          >
          <div v-if="index < 3" class="ranking-badge" :class="`rank-${index + 1}`">
    <span class="rank-number">{{ index + 1 }}</span>
  </div>
            <div class="avatar-container">
              <div class="avatar-ring"></div>
              <div class="avatar">
                {{ getInitials(freelancer.full_name || freelancer.name || freelancer.login) }}
              </div>
            </div>
            <h4>{{ freelancer.full_name || freelancer.name || freelancer.login }}</h4>
            <p class="rating">
              <span class="stars">{{ getStars(freelancer.rating) }}</span>
              <span class="rating-value">({{ freelancer.rating }})</span>
            </p>
            <div class="card-glow"></div>
            <div class="card-shine"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Начать работу -->
    <section class="info-articles">
  <div class="container">
    <h2 class="section-title">Полезные статьи</h2>
    
    <div class="articles-grid">
      <!-- Основная статья (первая, более крупная) -->
      <article class="article-card article-card-featured ios-glass ios-glass-heavy">
        <div class="article-content">
          <div class="article-meta">
            <span class="article-category">Для новичков</span>
            <span class="article-date">15 марта 2024</span>
          </div>
          <h3 class="article-title">
            <router-link to="/article" class="article-link">
              Легкие проекты фриланс для новичков без портфолио
              <span class="link-arrow">→</span>
            </router-link>
          </h3>
          <p class="article-excerpt">
            С чего начать карьеру фрилансера, если у вас нет опыта и примеров работ? 
            Топ-10 проектов, которые не требуют портфолио и приносят первый доход.
          </p>
          <div class="article-stats">
            <span class="stat">5 мин чтения</span>
            <span class="stat">2.5K просмотров</span>
          </div>
        </div>
        <div class="card-shine"></div>
        <div class="card-glow"></div>
      </article>

      <!-- Дополнительные статьи (для расширения блока) -->
      <article class="article-card ios-glass">
        <div class="article-content">
          <div class="article-meta">
            <span class="article-category">Советы</span>
            <span class="article-date">10 марта 2024</span>
          </div>
          <h4 class="article-title">
            <a href="#" class="article-link">
              Как поднять рейтинг на биржах фриланса
              <span class="link-arrow">→</span>
            </a>
          </h4>
          <p class="article-excerpt">
            Проверенные стратегии для быстрого роста рейтинга и получения первых заказов.
          </p>
          <div class="article-stats">
            <span class="stat">4 мин чтения</span>
            <span class="stat">1.8K просмотров</span>
          </div>
        </div>
        <div class="card-shine"></div>
        <div class="card-glow"></div>
      </article>

      <article class="article-card ios-glass">
        <div class="article-content">
          <div class="article-meta">
            <span class="article-category">Инструменты</span>
            <span class="article-date">5 марта 2024</span>
          </div>
          <h4 class="article-title">
            <a href="#" class="article-link">
              Топ-10 бесплатных инструментов для фрилансера
              <span class="link-arrow">→</span>
            </a>
          </h4>
          <p class="article-excerpt">
            Сервисы и программы, которые помогут работать эффективнее и профессиональнее.
          </p>
          <div class="article-stats">
            <span class="stat">6 мин чтения</span>
            <span class="stat">3.2K просмотров</span>
          </div>
        </div>
        <div class="card-shine"></div>
        <div class="card-glow"></div>
      </article>
    </div>
  </div>
</section>

    <!-- Плюсы фриланса -->
    <section class="benefits">
  <div class="container">
    <h3 class="section-title">Почему выбирают наш фриланс</h3>
    
    <div class="benefits-grid">
      <div
        v-for="benefit in benefits"
        :key="benefit.id"
        class="benefit-card ios-glass ios-glass-heavy"
      >
        <div class="icon-wrapper">
          <div class="icon-glow"></div>
          <span class="benefit-icon">{{ benefit.icon }}</span>
        </div>
        
        <h4 class="benefit-title">{{ benefit.title }}</h4>
        <p class="benefit-desc">{{ benefit.description }}</p>
        
        <div class="card-shine"></div>
        <div class="card-glow"></div>
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
import '../assets/css/mainStyle.css'

const router = useRouter()
const API_URL = ''

// Состояние пользователя
const user = ref(null)
const userLoading = ref(true)

// Состояние фрилансеров
const freelancers = ref([])
const freelancersLoading = ref(true)

// Статичные преимущества
const benefits = [
  { id: 1, icon: '💰', title: 'Без комиссии', description: 'Первый месяц переводы 0%' },
  { id: 2, icon: '⚡', title: 'Быстрые выплаты', description: 'Деньги в день выполнения заказа' },
  { id: 3, icon: '🌍', title: '1000+ заказов', description: 'Постоянный поток проектов' }
]

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

// Получить инициалы для аватара
const getInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
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