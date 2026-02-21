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
    <header class="header">
      <div class="container">
        <nav class="navbar ios-glass ios-glass-heavy">
          <div class="logo">
            <span class="logo-text">FreeLand</span>
          </div>
          
          <div class="nav-menu">
            <a href="#" class="nav-link active">Главная</a>
            <a href="#" class="nav-link">Фрилансеры</a>
            <a href="#" class="nav-link">Заказы</a>
            <a href="#" class="nav-link">О нас</a>
            <a href="#" class="nav-link">Контакты</a>
          </div>
          
          <div class="nav-actions">
            <button class="nav-button login">Войти</button>
            <button class="nav-button register ios-glass">Регистрация</button>
          </div>
          
          <button class="mobile-menu-toggle">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
          </button>
        </nav>
      </div>
    </header>

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

    <!-- Футер -->
    <footer class="footer ios-glass ios-glass-heavy">
      <div class="container">
        <p>&copy; 2026 FreeLand. Все права защищены.</p>
        <div class="footer-links">
          <a href="#" class="ios-glass-link">Помощь</a>
          <a href="#" class="ios-glass-link">Контакты</a>
          <a href="#" class="ios-glass-link">Политика</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
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

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  color: white;
  font-family: Arial, Helvetica, sans-serif;
}

:root {
  /* Синяя палитра */
  --blue-deep: #083358;
  --blue-dark: #0A4D8C;
  --blue-medium: #1A6BB3;
  --blue-soft: #2A7FC9;
  --blue-light: #3A94DF;
  --blue-pale: #6BB2F0;
  --blue-very-pale: #A8D1FF;
  
  /* Текст - светлые оттенки */
  --text-primary: #FFFFFF;
  --text-secondary: #F0F8FF;
  --text-tertiary: #E6F0FA;
  --text-light: #D9ECFF;
  
  /* Стеклянные эффекты */
  --glass-bg: rgba(10, 77, 140, 0.15);
  --glass-border: rgba(168, 209, 255, 0.25);
  --glass-shadow: 0 8px 32px rgba(8, 51, 88, 0.5);
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  background: #0A1428;
  min-height: 100vh;
  overflow-x: hidden;
  color: var(--text-primary);
}

.home-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  position: relative;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 20;
}

/* Усиленный динамический фон в синих тонах */
.dynamic-background {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
  overflow: hidden;
  background: linear-gradient(135deg, #0A1428 0%, #132238 50%, #0C1A2F 100%);
}

/* Градиентные сферы в синих тонах */
.gradient-sphere {
  position: absolute;
  border-radius: 50%;
  filter: blur(100px);
  opacity: 0.6;
  animation: float 25s infinite ease-in-out;
}

.sphere-1 {
  width: 800px;
  height: 800px;
  background: radial-gradient(circle at 30% 30%, #0A4D8C, #083358, #05203B);
  top: -300px;
  right: -300px;
  animation-delay: 0s;
}

.sphere-2 {
  width: 700px;
  height: 700px;
  background: radial-gradient(circle at 70% 70%, #1A6BB3, #0A4D8C, #083358);
  bottom: -250px;
  left: -250px;
  animation-delay: -8s;
}

.sphere-3 {
  width: 600px;
  height: 600px;
  background: radial-gradient(circle at 50% 50%, #2A7FC9, #1A6BB3, #0A4D8C);
  top: 40%;
  left: 50%;
  transform: translate(-50%, -50%);
  animation-delay: -15s;
}

/* Световые пятна в синих тонах */
.light-spot {
  position: absolute;
  border-radius: 50%;
  filter: blur(60px);
  animation: pulse 8s infinite alternate;
}

.spot-1 {
  width: 400px;
  height: 400px;
  top: 20%;
  left: 10%;
  background: radial-gradient(circle at center, rgba(10, 77, 140, 0.3), transparent);
  animation-delay: 0s;
}

.spot-2 {
  width: 500px;
  height: 500px;
  bottom: 10%;
  right: 5%;
  background: radial-gradient(circle at center, rgba(26, 107, 179, 0.3), transparent);
  animation-delay: -2s;
}

.spot-3 {
  width: 300px;
  height: 300px;
  top: 60%;
  left: 20%;
  background: radial-gradient(circle at center, rgba(42, 127, 201, 0.3), transparent);
  animation-delay: -4s;
}

.spot-4 {
  width: 450px;
  height: 450px;
  bottom: 30%;
  right: 15%;
  background: radial-gradient(circle at center, rgba(168, 209, 255, 0.3), transparent);
  animation-delay: -6s;
}

/* Сетка для текстуры */
.grid-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: 
    linear-gradient(rgba(168, 209, 255, 0.02) 1px, transparent 1px),
    linear-gradient(90deg, rgba(168, 209, 255, 0.02) 1px, transparent 1px);
  background-size: 50px 50px;
  pointer-events: none;
  z-index: 2;
}

/* Шум */
.noise-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle at 20% 30%, rgba(168, 209, 255, 0.03) 0%, transparent 30%),
              radial-gradient(circle at 80% 70%, rgba(168, 209, 255, 0.03) 0%, transparent 30%);
  opacity: 0.5;
  pointer-events: none;
  z-index: 3;
}

/* Частицы */
.particles {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 4;
  pointer-events: none;
}

.particle {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  filter: blur(2px);
}

@keyframes floatParticle {
  from {
    transform: translateY(0) translateX(0);
  }
  to {
    transform: translateY(-100vh) translateX(100px);
  }
}

@keyframes pulse {
  0%, 100% { opacity: 0.25; transform: scale(1); }
  50% { opacity: 0.45; transform: scale(1.2); }
}

@keyframes float {
  0%, 100% { transform: translate(0, 0) scale(1); }
  25% { transform: translate(80px, -50px) scale(1.2); }
  50% { transform: translate(-50px, 80px) scale(0.9); }
  75% { transform: translate(-80px, -30px) scale(1.1); }
}

/* Header & Navigation */
.header {
  padding: 20px 0;
  position: relative;
  z-index: 30;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 32px;
  position: relative;
}

.logo-text {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--text-primary);
  letter-spacing: 1px;
}

.nav-menu {
  display: flex;
  gap: 32px;
}

.nav-link {
  color: var(--text-secondary);
  text-decoration: none;
  font-size: 1.1rem;
  font-weight: 500;
  padding: 8px 16px;
  border-radius: 9999px;
  transition: color 0.3s ease;
}

.nav-link:hover {
  color: var(--text-primary);
}

.nav-link.active {
  color: var(--text-primary);
  background: rgba(168, 209, 255, 0.1);
}

.nav-actions {
  display: flex;
  gap: 12px;
}

.nav-button {
  padding: 10px 24px;
  border: none;
  border-radius: 9999px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  color: var(--text-primary);
}

.nav-button.login {
  background: transparent;
  border: 1px solid rgba(168, 209, 255, 0.3);
  color: var(--text-secondary);
}

.nav-button.login:hover {
  background: rgba(168, 209, 255, 0.1);
  border-color: rgba(168, 209, 255, 0.5);
  color: var(--text-primary);
}

.nav-button.register {
  background: rgba(10, 77, 140, 0.4);
  border: 1px solid rgba(168, 209, 255, 0.3);
}

.nav-button.register:hover {
  background: rgba(10, 77, 140, 0.6);
  border-color: rgba(168, 209, 255, 0.5);
}

.mobile-menu-toggle {
  display: none;
  flex-direction: column;
  gap: 6px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 8px;
}

.bar {
  width: 30px;
  height: 3px;
  background: var(--text-primary);
  border-radius: 3px;
  transition: all 0.3s ease;
}

/* Усиленный iOS Glass Effect в синих тонах */
.ios-glass {
  background: rgba(10, 77, 140, 0.15);
  backdrop-filter: blur(25px) saturate(180%);
  -webkit-backdrop-filter: blur(25px) saturate(180%);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 32px;
  box-shadow: 0 20px 40px rgba(8, 51, 88, 0.5),
              inset 0 0 0 1px rgba(168, 209, 255, 0.1),
              0 0 30px rgba(10, 77, 140, 0.3);
  position: relative;
  overflow: hidden;
}

.ios-glass-heavy {
  background: rgba(10, 77, 140, 0.2);
  backdrop-filter: blur(30px) saturate(200%);
  -webkit-backdrop-filter: blur(30px) saturate(200%);
  border: 1px solid rgba(168, 209, 255, 0.25);
  box-shadow: 0 25px 50px rgba(8, 51, 88, 0.6),
              inset 0 0 0 1px rgba(168, 209, 255, 0.15),
              0 0 40px rgba(10, 77, 140, 0.35);
}

.ios-glass::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 20% 20%, rgba(168, 209, 255, 0.2) 0%, transparent 60%),
              radial-gradient(circle at 80% 80%, rgba(168, 209, 255, 0.15) 0%, transparent 60%);
  pointer-events: none;
  z-index: 1;
}

.ios-glass::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(168, 209, 255, 0.1) 0%, transparent 50%);
  pointer-events: none;
  z-index: 1;
}

/* Hero Banner */
.hero-banner {
  padding: 60px 0 120px;
  position: relative;
  z-index: 20;
}

.banner-content {
  text-align: center;
  padding: 100px 60px;
}

.banner-content h1 {
  font-size: 3.2rem;
  font-weight: 700;
  margin-bottom: 24px;
  color: var(--text-primary);
  letter-spacing: -0.5px;
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.highlight {
  font-size: 4rem;
  font-weight: 800;
  color: var(--text-secondary);
  margin-bottom: 60px;
  letter-spacing: -1px;
  text-shadow: 0 0 30px rgba(168, 209, 255, 0.4);
}

/* Buttons */
.cta-button {
  position: relative;
  padding: 24px 56px;
  font-size: 1.3rem;
  font-weight: 600;
  color: var(--text-primary);
  border: none;
  border-radius: 9999px;
  cursor: pointer;
  background: rgba(10, 77, 140, 0.4);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(168, 209, 255, 0.3);
  overflow: hidden;
  transition: background 0.3s ease, border-color 0.3s ease;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.button-text {
  position: relative;
  z-index: 2;
}

.button-glow {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, 
    transparent, 
    rgba(168, 209, 255, 0.3), 
    rgba(255, 255, 255, 0.4), 
    rgba(168, 209, 255, 0.3), 
    transparent
  );
  transform: translateX(-100%);
  transition: transform 0.8s ease;
}

.cta-button:hover .button-glow {
  transform: translateX(100%);
}

.cta-button:hover {
  background: rgba(10, 77, 140, 0.5);
  border-color: rgba(168, 209, 255, 0.4);
}

/* Section Titles */
.section-title {
  font-size: 2.8rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 80px;
  color: var(--text-primary);
  letter-spacing: -0.5px;
  position: relative;
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -20px;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 4px;
  background: linear-gradient(90deg, rgba(168, 209, 255, 0.5), var(--text-primary), rgba(168, 209, 255, 0.5));
  border-radius: 2px;
  box-shadow: 0 0 20px rgba(168, 209, 255, 0.4);
}

/* Freelancers Grid */
.top-freelancers {
  padding: 120px 0;
  position: relative;
  z-index: 20;
}

.freelancers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 40px;
}

.freelancer-card {
  padding: 50px 40px;
  text-align: center;
  position: relative;
}

.avatar-container {
  position: relative;
  width: 140px;
  height: 140px;
  margin: 0 auto 30px;
}

.avatar-ring {
  position: absolute;
  top: -6px;
  left: -6px;
  right: -6px;
  bottom: -6px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(168, 209, 255, 0.6), var(--text-primary), rgba(168, 209, 255, 0.6));
  animation: rotate 4s linear infinite;
  filter: blur(2px);
  opacity: 0.6;
}

@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.avatar {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  position: relative;
  z-index: 2;
  box-shadow: 0 10px 30px rgba(8, 51, 88, 0.4);
}

.freelancer-card h4 {
  font-size: 1.6rem;
  font-weight: 600;
  margin-bottom: 12px;
  color: var(--text-primary);
}

.rating {
  margin-bottom: 20px;
}

.stars {
  color: var(--text-secondary);
  font-size: 1.2rem;
  margin-right: 4px;
}

.rating-value {
  color: var(--text-tertiary);
  font-size: 1rem;
  font-weight: 500;
}

.price {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-secondary);
  display: block;
}

/* Benefits */
.benefits {
  padding: 120px 0;
  position: relative;
  z-index: 20;
}

.benefits-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 40px;
}

.benefit-card {
  padding: 60px 40px;
  text-align: center;
  position: relative;
}

.icon-container {
  position: relative;
  width: 100px;
  height: 100px;
  margin: 0 auto 35px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-glow {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: radial-gradient(circle at center, rgba(168, 209, 255, 0.4), transparent);
  animation: pulse 2s infinite;
  filter: blur(5px);
}

.icon {
  font-size: 3.5rem;
  position: relative;
  z-index: 2;
  filter: drop-shadow(0 4px 15px rgba(168, 209, 255, 0.4));
  color: var(--text-primary);
}

.benefit-card h4 {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 20px;
  color: var(--text-primary);
}

.benefit-card p {
  color: var(--text-secondary);
  line-height: 1.7;
  font-size: 1.1rem;
}

/* Card Shine Effect */
.card-shine {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, 
    rgba(168, 209, 255, 0.15) 0%, 
    transparent 20%, 
    transparent 80%, 
    rgba(255, 255, 255, 0.1) 100%
  );
  opacity: 0;
  transition: opacity 0.5s ease;
  pointer-events: none;
  z-index: 2;
}

.freelancer-card:hover .card-shine,
.benefit-card:hover .card-shine {
  opacity: 1;
}

/* Card Glow Effect */
.card-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 50% 50%, rgba(168, 209, 255, 0.2), transparent 70%);
  opacity: 0;
  transition: opacity 0.5s ease;
  pointer-events: none;
  z-index: 1;
}

.freelancer-card:hover .card-glow,
.benefit-card:hover .card-glow {
  opacity: 1;
}

/* Start Work Section */
.start-work {
  padding: 120px 0;
  position: relative;
  z-index: 20;
}

.start-content {
  max-width: 800px;
  margin: 0 auto;
  text-align: center;
  padding: 100px 60px;
}

.start-content h3 {
  font-size: 3.2rem;
  font-weight: 700;
  margin-bottom: 30px;
  color: var(--text-primary);
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.start-content p {
  font-size: 1.4rem;
  color: var(--text-secondary);
  margin-bottom: 50px;
  line-height: 1.7;
}

/* Footer */
.footer {
  margin-top: auto;
  padding: 80px 0 50px;
  text-align: center;
  position: relative;
  z-index: 20;
  border-radius: 40px 40px 0 0;
}

.footer p {
  color: var(--text-secondary);
}

.footer-links {
  margin-top: 30px;
}

.ios-glass-link {
  color: var(--text-secondary);
  text-decoration: none;
  margin: 0 25px;
  font-weight: 500;
  padding: 10px 20px;
  border-radius: 9999px;
  transition: color 0.3s ease, border-color 0.3s ease;
  background: rgba(10, 77, 140, 0.1);
  border: 1px solid rgba(168, 209, 255, 0.15);
}

.ios-glass-link:hover {
  color: var(--text-primary);
  border-color: rgba(168, 209, 255, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
  .navbar {
    padding: 12px 20px;
  }

  .nav-menu {
    display: none;
  }

  .nav-actions {
    display: none;
  }

  .mobile-menu-toggle {
    display: flex;
  }

  .banner-content h1 { 
    font-size: 2.5rem; 
  }
  
  .highlight { 
    font-size: 3rem; 
  }
  
  .hero-banner {
    padding: 40px 0 80px;
  }
  
  .banner-content {
    padding: 70px 30px;
  }
  
  .freelancers-grid, 
  .benefits-grid {
    grid-template-columns: 1fr;
    gap: 30px;
  }
  
  .section-title {
    font-size: 2.2rem;
    margin-bottom: 60px;
  }
  
  .start-content {
    padding: 70px 30px;
  }
  
  .start-content h3 {
    font-size: 2.5rem;
  }
  
  .start-content p {
    font-size: 1.2rem;
  }

  .logo-text {
    font-size: 1.5rem;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 16px;
  }
  
  .cta-button {
    padding: 20px 40px;
    font-size: 1.1rem;
  }
  
  .footer-links a {
    display: inline-block;
    margin: 10px;
    padding: 8px 16px;
  }

  .banner-content {
    padding: 50px 20px;
  }

  .banner-content h1 {
    font-size: 2rem;
  }

  .highlight {
    font-size: 2.2rem;
  }
}
</style>