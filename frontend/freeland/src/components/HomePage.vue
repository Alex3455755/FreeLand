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

    <!-- Навигационное меню (вынесено в компонент) -->
    <HeaderMenu />

    <!-- Баннер -->
    <section class="hero-banner">
      <div class="container">
        <div class="banner-content ios-glass ios-glass-heavy">
          <h1>Всем зарегистрировавшимся сейчас</h1>
          <h2 class="highlight">МЕСЯЦ ПЕРЕВОДЫ БЕЗ КОМИССИИ</h2>
          <button class="cta-button ios-glass ios-glass-heavy">
            <span class="button-text">Начать работу бесплатно</span>
            <span class="button-glow"></span>
          </button>
        </div>
      </div>
    </section>

    <!-- Лучшие фрилансеры -->
    <section class="top-freelancers">
      <div class="container">
        <h3 class="section-title">Лучшие фрилансеры</h3>
        <div class="freelancers-grid">
          <div 
            v-for="freelancer in freelancers" 
            :key="freelancer.id"
            class="freelancer-card ios-glass ios-glass-heavy"
          >
            <div class="avatar-container">
              <div class="avatar-ring"></div>
              <div class="avatar" :style="{ background: freelancer.gradient }"></div>
            </div>
            <h4>{{ freelancer.name }}</h4>
            <p class="rating">
              <span class="stars">★★★★★</span>
              <span class="rating-value">({{ freelancer.rating }})</span>
            </p>
            <span class="price">{{ freelancer.price }}/час</span>
            <div class="card-glow"></div>
            <div class="card-shine"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Начать работу -->
    <section class="start-work">
      <div class="container">
        <div class="start-content ios-glass ios-glass-heavy">
          <h3>Начните зарабатывать уже сегодня</h3>
          <p>Зарегистрируйтесь и получите первые заказы в течение 24 часов</p>
          <button class="cta-button ios-glass ios-glass-heavy">
            <span class="button-text">Создать профиль</span>
            <span class="button-glow"></span>
          </button>
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
            <div class="icon-container">
              <div class="icon-glow"></div>
              <span class="icon">{{ benefit.icon }}</span>
            </div>
            <h4>{{ benefit.title }}</h4>
            <p>{{ benefit.description }}</p>
            <div class="card-glow"></div>
            <div class="card-shine"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Футер (вынесен в компонент) -->
    <FooterApp />
  </div>
</template>

<script setup>
import HeaderMenu from '@/elements/HeaderMenu.vue'
import FooterApp from '@/elements/FooterApp.vue'
import '../assets/css/mainStyle.css'

const freelancers = [
  { 
    id: 1, 
    name: 'Иван Петров', 
    rating: 4.9, 
    price: 2500, 
    gradient: 'linear-gradient(135deg, #0A4D8C, #1A6BB3, #083358)'
  },
  { 
    id: 2, 
    name: 'Анна Смирнова', 
    rating: 5.0, 
    price: 3200, 
    gradient: 'linear-gradient(135deg, #1A6BB3, #2A7FC9, #0A4D8C)'
  },
  { 
    id: 3, 
    name: 'Михаил Козлов', 
    rating: 4.8, 
    price: 1800, 
    gradient: 'linear-gradient(135deg, #0A4D8C, #1A6BB3, #083358)'
  },
  { 
    id: 4, 
    name: 'Елена Иванова', 
    rating: 4.9, 
    price: 2800, 
    gradient: 'linear-gradient(135deg, #1A6BB3, #2A7FC9, #0A4D8C)'
  }
]

const benefits = [
  { id: 1, icon: '💰', title: 'Без комиссии', description: 'Первый месяц переводы 0%' },
  { id: 2, icon: '⚡', title: 'Быстрые выплаты', description: 'Деньги в день выполнения заказа' },
  { id: 3, icon: '🛡️', title: 'Безопасные сделки', description: 'Гарантия возврата средств' },
  { id: 4, icon: '🌍', title: '1000+ заказов', description: 'Постоянный поток проектов' }
]

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
</script>
