<template>
  <div class="not-found-page">
    <header-menu />
    <!-- Динамический фон -->
    <div class="dynamic-background">
      <div class="gradient-sphere sphere-1"></div>
      <div class="gradient-sphere sphere-2"></div>
      <div class="gradient-sphere sphere-3"></div>
      <div class="light-spot spot-1"></div>
      <div class="light-spot spot-2"></div>
      <div class="light-spot spot-3"></div>
      <div class="light-spot spot-4"></div>
      <div class="grid-overlay"></div>
      <div class="noise-overlay"></div>
      <div class="particles" ref="particlesContainer"></div>
    </div>

    <div class="container">
      <!-- Основной контент 404 -->
      <div class="error-content ios-glass">
        <!-- Астронавт и 404 -->
        <div class="error-illustration">
          <div class="astronaut">
            <div class="astronaut-helmet">
              <div class="helmet-glass"></div>
              <div class="astronaut-face">👨‍🚀</div>
            </div>
            <div class="astronaut-body">
              <div class="body-main"></div>
              <div class="backpack"></div>
            </div>
            <div class="floating-line line-1"></div>
            <div class="floating-line line-2"></div>
            <div class="floating-line line-3"></div>
          </div>
          
          <div class="error-number">
            <span class="digit">4</span>
            <span class="digit zero">0</span>
            <span class="digit">4</span>
          </div>
        </div>

        <!-- Текст ошибки -->
        <h1 class="error-title">Эта страница потерялась в космосе</h1>

        <!-- Кнопки действий -->
        <div class="error-actions">
          <button @click="goBack" class="action-btn primary">
            <span class="btn-icon">←</span>
            <span class="btn-text">Вернуться назад</span>
            <span class="button-glow"></span>
          </button>
          
          <router-link to="/" class="action-btn secondary">
            <span class="btn-icon">🏠</span>
            <span class="btn-text">На главную</span>
            <span class="button-glow"></span>
          </router-link>
        </div>

        <!-- Поиск (на случай, если пользователь хочет найти что-то конкретное) -->
        
      </div>
    </div>
    <footer-app />
  </div>
</template>

<script>
import FooterApp from '@/elements/FooterApp.vue';
import HeaderMenu from '@/elements/HeaderMenu.vue';
export default {
  name: 'NotFound',
    components: {
    HeaderMenu,
    FooterApp
  },
  
  data() {
    return {
      searchQuery: '',
      
      // Коллекция креативных историй для 404 (вдохновлено Kapwing)
      stories: [
        {
          icon: '🌌',
          text: 'Корабль дрейфует в бесконечности. Ваша собака ждет вас дома, а учитель математики уже готовит торжественную речь.'
        },
        {
          icon: '🎈',
          text: 'Красный воздушный шарик улетел в небо, когда вы проезжали мимо на велосипеде. Теперь он застрял на этой странице.'
        },
        {
          icon: '🏜️',
          text: 'Вы так и не увидели голову страуса. Он прячет её в песок каждый раз, когда вы рядом.'
        },
        {
          icon: '⏰',
          text: 'Сломанные часы дважды в сутки показывают правильное время. Но вы не знаете, когда именно. Как и не знаете, как сюда попали.'
        },
        {
          icon: '🚧',
          text: 'Велосипедная дорожка разрушена землетрясением. Три велосипедиста уже упали в пропасть. Дальше проезда нет.'
        },
        {
          icon: '☕',
          text: 'Семейная реликвия — любимая кружка соседа. Он уехал и оставил её. Теперь она разбита, как и ссылка, по которой вы перешли.'
        },
        {
          icon: '🍞',
          text: 'Вы отвлеклись на стирку, и тост подгорел. Как и эта страница — до хрустящей корочки.'
        },
        {
          icon: '🐕',
          text: 'Ваша собака очень милая, но где ваши тапки? Где диплом? И где эта страница?'
        },
        {
          icon: '🥛',
          text: 'Пролитое молоко — не повод плакать. Но ссылка действительно потеряна безвозвратно.'
        },
        {
          icon: '👻',
          text: 'Эта страница — призрак. Когда-то она была жива, но теперь осталась только память.'
        },
        {
          icon: '🗺️',
          text: 'Карта 2005 года показывает здесь продуктовый магазин. Но вы стоите в чистом поле.'
        },
        {
          icon: '🚤',
          text: 'Лодка дала течь. Вы держитесь за буй, а вода поднимается всё выше.'
        },
        {
          icon: '🗑️',
          text: 'Король енотов предложил сделку: вы забываете вынести мусор, а он платит половину аренды.'
        },
        {
          icon: '🍦',
          text: 'Клубничное мороженое упало в грязь. Честно говоря, оно было не очень вкусным.'
        }
      ],
      
      currentStory: {}
    }
  },

  methods: {
    // Выбор случайной истории
    getRandomStory() {
      const randomIndex = Math.floor(Math.random() * this.stories.length)
      return this.stories[randomIndex]
    },

    // Навигация назад
    goBack() {
      this.$router.go(-1)
    },

    // Обработка поиска
    handleSearch() {
      if (this.searchQuery.trim()) {
        // Перенаправляем на страницу поиска
        this.$router.push({
          path: '/search',
          query: { q: this.searchQuery.trim() }
        })
      }
    },

    // Создание декоративных частиц
    createParticles() {
      const container = this.$refs.particlesContainer
      if (!container) return

      for (let i = 0; i < 30; i++) {
        const p = document.createElement('div')
        p.className = 'particle'
        const size = Math.random() * 6 + 2
        p.style.width = size + 'px'
        p.style.height = size + 'px'
        p.style.left = Math.random() * 100 + '%'
        p.style.bottom = '0px'
        p.style.background = `rgba(168, 209, 255, ${Math.random() * 0.4 + 0.2})`
        p.style.boxShadow = `0 0 10px rgba(168, 209, 255, 0.5)`
        p.style.animation = `floatParticle ${Math.random() * 15 + 10}s linear infinite`
        p.style.animationDelay = Math.random() * 5 + 's'
        container.appendChild(p)
      }
    }
  },

  mounted() {
    // Устанавливаем случайную историю при загрузке
    this.currentStory = this.getRandomStory()
    this.createParticles()
  }
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --blue-deep: #083358;
  --blue-dark: #0A4D8C;
  --blue-medium: #1A6BB3;
  --blue-soft: #2A7FC9;
  --blue-light: #3A94DF;
  --blue-pale: #6BB2F0;
  --blue-very-pale: #A8D1FF;
  --text-primary: #FFFFFF;
  --text-secondary: #F0F8FF;
  --text-tertiary: #E6F0FA;
  --glass-bg: rgba(10, 77, 140, 0.15);
  --glass-border: rgba(168, 209, 255, 0.25);
  --glass-shadow: 0 8px 32px rgba(8, 51, 88, 0.5);
}

/* Динамический фон */
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

@keyframes float {
  0%, 100% { transform: translate(0, 0) scale(1); }
  25% { transform: translate(80px, -50px) scale(1.2); }
  50% { transform: translate(-50px, 80px) scale(0.9); }
  75% { transform: translate(-80px, -30px) scale(1.1); }
}

@keyframes pulse {
  0%, 100% { opacity: 0.25; transform: scale(1); }
  50% { opacity: 0.45; transform: scale(1.2); }
}

@keyframes floatParticle {
  0% { transform: translateY(0) translateX(0); opacity: 0; }
  10% { opacity: 1; }
  90% { opacity: 1; }
  100% { transform: translateY(-100vh) translateX(100px); opacity: 0; }
}

/* Стеклянные эффекты */
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

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  position: relative;
  z-index: 20;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.error-content {
  max-width: 800px;
  margin: 0 auto;
  padding: 60px 40px;
  text-align: center;
}

/* Иллюстрация астронавта */
.error-illustration {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 20px;
  margin-bottom: 40px;
  flex-wrap: wrap;
}

.astronaut {
  position: relative;
  width: 120px;
  height: 160px;
  animation: floatAstronaut 6s ease-in-out infinite;
}

@keyframes floatAstronaut {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}

.astronaut-helmet {
  position: relative;
  width: 70px;
  height: 70px;
  background: rgba(255, 255, 255, 0.1);
  border: 2px solid rgba(168, 209, 255, 0.4);
  border-radius: 50%;
  margin: 0 auto 5px;
  overflow: hidden;
  backdrop-filter: blur(5px);
}

.helmet-glass {
  position: absolute;
  top: 15px;
  left: 15px;
  width: 30px;
  height: 30px;
  background: rgba(168, 209, 255, 0.3);
  border-radius: 50%;
  filter: blur(3px);
}

.astronaut-face {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 2rem;
  z-index: 2;
}

.astronaut-body {
  position: relative;
  width: 80px;
  height: 70px;
  margin: 0 auto;
}

.body-main {
  width: 70px;
  height: 60px;
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(168, 209, 255, 0.3);
  border-radius: 30px 30px 15px 15px;
  margin: 0 auto;
  backdrop-filter: blur(5px);
}

.backpack {
  position: absolute;
  top: 10px;
  right: -10px;
  width: 25px;
  height: 35px;
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(168, 209, 255, 0.3);
  border-radius: 10px;
  backdrop-filter: blur(5px);
}

.floating-line {
  position: absolute;
  width: 2px;
  height: 30px;
  background: linear-gradient(to bottom, rgba(168, 209, 255, 0.8), transparent);
  animation: pulseLine 2s ease-in-out infinite;
}

.line-1 {
  top: 100%;
  left: 30%;
  animation-delay: 0s;
}

.line-2 {
  top: 100%;
  left: 50%;
  animation-delay: 0.5s;
}

.line-3 {
  top: 100%;
  left: 70%;
  animation-delay: 1s;
}

@keyframes pulseLine {
  0%, 100% { opacity: 0.2; transform: scaleY(0.8); }
  50% { opacity: 1; transform: scaleY(1.2); }
}

.error-number {
  display: flex;
  gap: 10px;
  font-size: 6rem;
  font-weight: 800;
  line-height: 1;
}

.digit {
  display: inline-block;
  color: var(--text-primary);
  text-shadow: 0 0 30px rgba(168, 209, 255, 0.5);
  animation: glow 3s ease-in-out infinite;
}

.zero {
  position: relative;
  display: inline-block;
  animation: rotate 10s linear infinite;
}

.zero::before {
  content: '0';
  position: absolute;
  top: 0;
  left: 0;
  color: var(--blue-very-pale);
  filter: blur(10px);
  opacity: 0.5;
}

@keyframes glow {
  0%, 100% { text-shadow: 0 0 20px rgba(168, 209, 255, 0.3); }
  50% { text-shadow: 0 0 40px rgba(168, 209, 255, 0.8); }
}

@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.error-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 20px;
  color: var(--text-primary);
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.error-description {
  margin-bottom: 30px;
  color: var(--text-secondary);
  font-size: 1.1rem;
  line-height: 1.7;
}

.subtle-hint {
  margin-top: 15px;
  font-style: italic;
  color: var(--text-tertiary);
  opacity: 0.8;
}

/* Случайная история */
.random-story {
  padding: 25px;
  margin-bottom: 40px;
  text-align: left;
  display: flex;
  align-items: flex-start;
  gap: 15px;
}

.story-icon {
  font-size: 2.5rem;
  filter: drop-shadow(0 0 10px rgba(168, 209, 255, 0.5));
}

.story-text {
  color: var(--text-secondary);
  font-size: 1rem;
  line-height: 1.6;
  flex: 1;
}

/* Кнопки */
.error-actions {
  display: flex;
  gap: 20px;
  justify-content: center;
  margin-bottom: 40px;
  flex-wrap: wrap;
}

.action-btn {
  position: relative;
  padding: 16px 32px;
  border: none;
  border-radius: 50px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  overflow: hidden;
  transition: transform 0.2s;
}

.action-btn:hover {
  transform: translateY(-2px);
}

.primary {
  background: rgba(10, 77, 140, 0.4);
  border: 1px solid rgba(168, 209, 255, 0.3);
  color: var(--text-primary);
  backdrop-filter: blur(10px);
}

.secondary {
  background: rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(168, 209, 255, 0.2);
  color: var(--text-primary);
  backdrop-filter: blur(10px);
}

.btn-icon {
  font-size: 1.2rem;
  position: relative;
  z-index: 2;
}

.btn-text {
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
    rgba(168, 209, 255, 0.2), 
    rgba(255, 255, 255, 0.3), 
    rgba(168, 209, 255, 0.2), 
    transparent
  );
  transform: translateX(-100%);
  transition: transform 0.8s ease;
}

.action-btn:hover .button-glow {
  transform: translateX(100%);
}

/* Поиск */
.search-section {
  max-width: 400px;
  margin: 0 auto;
}

.search-hint {
  color: var(--text-tertiary);
  margin-bottom: 15px;
  font-size: 0.95rem;
}

.search-box {
  display: flex;
  gap: 10px;
}

.search-input {
  flex: 1;
  padding: 15px 20px;
  border-radius: 50px;
  border: 1px solid rgba(168, 209, 255, 0.3);
  background: rgba(10, 77, 140, 0.2);
  backdrop-filter: blur(8px);
  color: white;
  font-size: 1rem;
  transition: all 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: var(--blue-very-pale);
  box-shadow: 0 0 20px rgba(168, 209, 255, 0.4);
}

.search-input::placeholder {
  color: rgba(255, 255, 255, 0.5);
}

.search-btn {
  width: 55px;
  height: 55px;
  border-radius: 50%;
  border: 1px solid rgba(168, 209, 255, 0.3);
  background: rgba(10, 77, 140, 0.4);
  backdrop-filter: blur(8px);
  color: white;
  cursor: pointer;
  font-size: 1.2rem;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.search-btn:hover {
  background: rgba(10, 77, 140, 0.6);
  border-color: var(--blue-very-pale);
  transform: scale(1.05);
}

/* Адаптивность */
@media (max-width: 768px) {
  .error-content {
    padding: 40px 20px;
  }

  .error-number {
    font-size: 4rem;
  }

  .error-title {
    font-size: 1.8rem;
  }

  .error-illustration {
    gap: 10px;
  }

  .astronaut {
    width: 80px;
    height: 120px;
  }

  .astronaut-helmet {
    width: 50px;
    height: 50px;
  }

  .astronaut-face {
    font-size: 1.5rem;
  }

  .body-main {
    width: 50px;
    height: 45px;
  }

  .backpack {
    width: 18px;
    height: 25px;
    right: -8px;
  }

  .action-btn {
    padding: 12px 24px;
    font-size: 1rem;
  }

  .random-story {
    padding: 20px;
  }
}

@media (max-width: 480px) {
  .error-number {
    font-size: 3rem;
  }

  .error-actions {
    flex-direction: column;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .story-icon {
    font-size: 2rem;
  }

  .story-text {
    font-size: 0.95rem;
  }
}
</style>