<template>
  <div class="team-page">
    <HeaderMenu />
    <!-- Динамический фон (декоративный) -->
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
      <!-- 1. СЕКЦИЯ КОМАНДА / ЭКСПЕРТЫ -->
      <section class="page-section">
        <h2 class="section-title">Ведущие эксперты платформы</h2>
        <div class="team-grid">
          <div 
            v-for="(expert, index) in experts" 
            :key="index" 
            class="team-card ios-glass"
          >
            <div class="card-shine"></div>
            <div class="card-glow"></div>
            <div class="avatar-container">
              <div class="avatar-ring"></div>
              <div class="avatar">{{ expert.avatar }}</div>
            </div>
            <h3 class="team-name">{{ expert.name }}</h3>
            <div class="team-position">{{ expert.position }}</div>
            <div class="team-contacts">
              <a 
                v-for="(contact, idx) in expert.contacts" 
                :key="idx"
                :href="contact.link" 
                class="icon-symbol"
                :title="contact.type"
              >{{ contact.icon }}</a>
            </div>
            <div class="team-quote">«{{ expert.quote }}»</div>
          </div>
        </div>
      </section>

      <!-- 2. БЛОК КОНТАКТОВ И СОЦСЕТЕЙ КОМПАНИИ -->
      <div class="company-contacts-row ios-glass-heavy">
        <a 
          v-for="(contact, idx) in companyContacts" 
          :key="idx"
          :href="contact.link" 
          class="contact-item"
        >
          <span class="icon-symbol">{{ contact.icon }}</span> 
          {{ contact.label }}
        </a>
      </div>

      <!-- 3. КОММЕРЧЕСКИЕ ФАКТОРЫ (ЭКСПЕРТНОСТЬ/ГАРАНТИИ) -->
      <section class="page-section">
        <h2 class="section-title">Почему нам доверяют</h2>
        <div class="expertise-grid">
          <div 
            v-for="(factor, idx) in trustFactors" 
            :key="idx" 
            class="factor-card ios-glass"
          >
            <div class="card-shine"></div>
            <div class="card-glow"></div>
            <div class="factor-icon">{{ factor.icon }}</div>
            <div class="factor-title">{{ factor.title }}</div>
            <div class="factor-desc">{{ factor.desc }}</div>
          </div>
        </div>
      </section>

      <!-- 4. ФОРМА ОБРАТНОЙ СВЯЗИ + ОТЗЫВЫ -->
      <section class="page-section" style="padding-top: 0;">
        <h2 class="section-title">Обратная связь и отзывы</h2>
        
        <!-- Индикатор загрузки -->
        <div v-if="loading" class="loading-indicator">
          <div class="spinner"></div>
          <p>Загрузка отзывов...</p>
        </div>

        <!-- Сообщение об ошибке -->
        <div v-if="error" class="error-message ios-glass">
          <span class="error-icon">⚠️</span>
          <p>{{ error }}</p>
          <button @click="fetchReviews" class="retry-btn">Повторить</button>
        </div>

        <div class="feedback-row" v-if="!loading">
          <!-- колонка с формой -->
          <div class="feedback-form ios-glass">
            <form @submit.prevent="addReview">
              <div class="form-group">
                <label>Ваше имя</label>
                <input 
                  type="text" 
                  class="form-control" 
                  v-model="newReview.name" 
                  placeholder="Александр"
                  required
                  :disabled="submitting"
                >
              </div>
              <div class="form-group">
                <label>Электронная почта (для ответа)</label>
                <input 
                  type="email" 
                  class="form-control" 
                  v-model="newReview.email" 
                  placeholder="name@example.com"
                  required
                  :disabled="submitting"
                >
              </div>
              <div class="form-group">
                <label>Ваш отзыв или вопрос</label>
                <textarea 
                  class="form-control" 
                  v-model="newReview.text" 
                  rows="4" 
                  placeholder="Поделитесь опытом работы или задайте вопрос команде..."
                  required
                  :disabled="submitting"
                ></textarea>
              </div>
              <button type="submit" class="submit-btn" :disabled="submitting">
                <span v-if="!submitting" class="button-text">Отправить</span>
                <span v-else class="button-text">Отправка...</span>
                <span class="button-glow"></span>
              </button>
            </form>
            <p class="form-hint">* Отзывы сохраняются в базе данных и проходят модерацию</p>
          </div>

          <!-- колонка с отзывами из БД -->
          <div class="reviews-list">
            <div 
              v-for="(review, index) in reviews" 
              :key="review.id || index" 
              class="review-card ios-glass"
            >
              <div class="card-shine"></div>
              <div class="card-glow"></div>
              <div class="review-header">
                <div class="review-avatar">{{ review.name.charAt(0).toUpperCase() }}</div>
                <div>
                  <div class="review-author">{{ review.name }}</div>
                  <div class="review-date">{{ formatDate(review.created_at) || review.date }}</div>
                </div>
              </div>
              <div class="review-text">{{ review.text }}</div>
            </div>
            
            <!-- Сообщение если нет отзывов -->
            <div v-if="reviews.length === 0" class="no-reviews ios-glass">
              <p>Пока нет отзывов. Будьте первым!</p>
            </div>
          </div>
        </div>
      </section>
    </div>
    <FooterApp />
  </div>
</template>

<script>
import HeaderMenu from '@/elements/HeaderMenu.vue';
import FooterApp from '@/elements/FooterApp.vue';
export default {
  name: 'TeamPage',
    components: {
    HeaderMenu,
    FooterApp
  },
  data() {
    return {
      // Данные команды
      experts: [
        {
          avatar: '👩‍💻',
          name: 'Екатерина Волкова',
          position: 'Senior full‑stack разработчик',
          quote: '15+ лет в веб‑разработке. Топ‑1% на бирже, 500+ успешных проектов. Гарантирую чистый код и дедлайны.',
          contacts: [
            { type: 'email', icon: '📧', link: '#' },
            { type: 'phone', icon: '📱', link: '#' },
            { type: 'telegram', icon: '💬', link: '#' }
          ]
        },
        {
          avatar: '👨‍🎨',
          name: 'Дмитрий Белов',
          position: 'Art-директор / UX-аналитик',
          quote: 'Трехкратный лауреат премии Awwwards. Помогаю стартапам находить визуальный язык, который продаёт.',
          contacts: [
            { type: 'email', icon: '📧', link: '#' },
            { type: 'phone', icon: '📱', link: '#' },
            { type: 'telegram', icon: '💬', link: '#' }
          ]
        },
        {
          avatar: '👨‍🔧',
          name: 'Артём Ковалёв',
          position: 'Системный архитектор / DevOps',
          quote: 'Инфраструктура, которая не падает. Настраивал кластеры для банков и EdTech, 99.9% аптайм.',
          contacts: [
            { type: 'email', icon: '📧', link: '#' },
            { type: 'phone', icon: '📱', link: '#' },
            { type: 'telegram', icon: '💬', link: '#' }
          ]
        },
        {
          avatar: '👩‍⚖️',
          name: 'Алина Савельева',
          position: 'Специалист по кибербезопасности',
          quote: 'Этичный хакер, сертификаты CISSP, CEH. Провожу аудит и учу защищать код на всех этапах.',
          contacts: [
            { type: 'email', icon: '📧', link: '#' },
            { type: 'phone', icon: '📱', link: '#' },
            { type: 'telegram', icon: '💬', link: '#' }
          ]
        }
      ],

      // Контакты компании
      companyContacts: [
        { icon: '📘', label: 'fb.expert', link: 'https://facebook.com' },
        { icon: '📱', label: '+7 (800) 555-35-35', link: 'tel:+78005553535' },
        { icon: '✉️', label: 'team@freelancehub.ru', link: 'mailto:team@freelancehub.ru' },
        { icon: '📢', label: 'tg.experts', link: 'https://t.me/experts' },
        { icon: '📸', label: '@freelance_life', link: 'https://instagram.com' }
      ],

      // Коммерческие факторы
      trustFactors: [
        {
          icon: '⏳',
          title: '10+ лет опыта',
          desc: 'Средний стаж экспертов — более 10 лет в индустрии. Каждый пятый — бывший топ‑менеджер крупных IT‑компаний.'
        },
        {
          icon: '🏆',
          title: 'Топ‑1% специалистов',
          desc: 'Мы отбираем только 1% кандидатов. Все члены команды имеют подтверждённые сертификаты и портфолио.'
        },
        {
          icon: '⚖️',
          title: 'Гарантия результата',
          desc: 'Юридически закреплённые гарантии: возврат средств при срыве сроков или несоответствии ТЗ.'
        },
        {
          icon: '🔒',
          title: 'Безопасность сделок',
          desc: 'Все платежи через защищённый escrow-счёт. NDA подписывается автоматически с каждым клиентом.'
        }
      ],

      // Отзывы из БД
      reviews: [],
      
      // Модель для нового отзыва
      newReview: {
        name: '',
        email: '',
        text: ''
      },

      // Состояния загрузки
      loading: false,
      submitting: false,
      error: null,

      // API endpoint (замените на ваш реальный URL)
      apiUrl: process.env.VUE_APP_API_URL || '/api'
    }
  },

  methods: {
    // Форматирование даты из БД
    formatDate(dateString) {
      if (!dateString) return ''
      
      try {
        const date = new Date(dateString)
        return date.toLocaleDateString('ru-RU', { 
          day: '2-digit', 
          month: '2-digit', 
          year: 'numeric' 
        })
      } catch (e) {
        return dateString
      }
    },

    // Получение всех отзывов из БД
    async fetchReviews() {
      this.loading = true
      this.error = null
      
      try {
        const response = await fetch(`${this.apiUrl}/reviews`)
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const data = await response.json()
        this.reviews = data.reverse() // Показываем новые первыми
      } catch (error) {
        console.error('Ошибка загрузки отзывов:', error)
        this.error = 'Не удалось загрузить отзывы. Пожалуйста, попробуйте позже.'
        
        // Для демонстрации показываем тестовые данные если API недоступен
        this.loadDemoReviews()
      } finally {
        this.loading = false
      }
    },

    // Демо-данные для разработки (когда API недоступен)
    loadDemoReviews() {
      this.reviews = [
        {
          id: 1,
          name: 'Михаил Орлов',
          created_at: '2025-02-12T10:30:00',
          text: 'Работали с Екатериной над финтех-панелью. Всё чётко, код идеальный, даже после передачи поддерживает. Очень доволен!'
        },
        {
          id: 2,
          name: 'Анна Ветрова',
          created_at: '2025-02-03T15:45:00',
          text: 'Заказывали дизайн-концепцию у Дмитрия. Стиль, скорость, вежливость — на высоте! Уже получили первые дивиденды от нового дизайна.'
        },
        {
          id: 3,
          name: 'Константин Кравцов',
          created_at: '2025-01-21T09:15:00',
          text: 'Срочно нужен был аудит сервера после взлома. Артём за пару часов всё восстановил и усилил защиту. Спасибо команде!'
        },
        {
          id: 4,
          name: 'Дарья Соколова',
          created_at: '2025-01-15T14:20:00',
          text: 'Провели пентест с Алиной — нашли уязвимости, о которых мы даже не догадывались. Очень профессионально и безопасно.'
        }
      ]
    },

    // Добавление нового отзыва (с fetch запросом к БД)
    async addReview() {
  // Валидация
  if (!this.newReview.name.trim() || !this.newReview.email.trim() || !this.newReview.text.trim()) {
    alert('Пожалуйста, заполните все поля.')
    return
  }

  this.submitting = true
  this.error = null

  try {
    // Отправляем отзыв на Laravel backend
    const response = await fetch(`${this.apiUrl}/reviews`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken() // Если используете CSRF защиту
      },
      body: JSON.stringify({
        name: this.newReview.name.trim(),
        email: this.newReview.email.trim(),
        text: this.newReview.text.trim()
      })
    })

    const data = await response.json()

    if (!response.ok) {
      // Обработка ошибок валидации
      if (response.status === 422 && data.errors) {
        const errorMessages = Object.values(data.errors).flat().join('\n')
        throw new Error(errorMessages)
      }
      throw new Error(data.message || 'Ошибка при отправке отзыва')
    }

    // Успешная отправка
    if (data.success) {
      // Обновляем список отзывов из БД
      await this.fetchReviews()

      // Очищаем форму
      this.newReview.name = ''
      this.newReview.email = ''
      this.newReview.text = ''
    }

  } catch (error) {
    console.error('Ошибка при отправке отзыва:', error)
    this.error = error.message || 'Не удалось отправить отзыв. Пожалуйста, попробуйте позже.'
    alert(this.error)
  } finally {
    this.submitting = false
  }
},

// Получение CSRF токена (если используете)
getCsrfToken() {
  const token = document.querySelector('meta[name="csrf-token"]')
  return token ? token.getAttribute('content') : ''
},

    // Имитация сохранения при отсутствии интернета (для разработки)
    simulateOfflineSave() {
      // Формируем дату
      const now = new Date()

      // Добавляем в начало списка (локально)
      this.reviews.unshift({
        id: Date.now(),
        name: this.newReview.name,
        created_at: now.toISOString(),
        text: this.newReview.text
      })

      // Очищаем форму
      this.newReview.name = ''
      this.newReview.email = ''
      this.newReview.text = ''

      alert('Спасибо! Ваш отзыв сохранён локально (режим офлайн).')
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
    this.createParticles()
    this.fetchReviews() // Загружаем отзывы при монтировании компонента
  }
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
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
  --text-light: #D9ECFF;
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

.card-shine {
  position: absolute;
  inset: 0;
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

.card-glow {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 50% 50%, rgba(168, 209, 255, 0.2), transparent 70%);
  opacity: 0;
  transition: opacity 0.5s ease;
  pointer-events: none;
  z-index: 1;
}

.team-card:hover .card-shine,
.factor-card:hover .card-shine,
.review-card:hover .card-shine {
  opacity: 1;
}

.team-card:hover .card-glow,
.factor-card:hover .card-glow,
.review-card:hover .card-glow {
  opacity: 1;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 20;
}

/* Секции */
.page-section {
  padding: 80px 0;
  position: relative;
  z-index: 20;
}

.section-title {
  font-size: 2.8rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 80px;
  color: var(--text-primary);
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
  letter-spacing: -0.5px;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -20px;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 4px;
  background: linear-gradient(90deg, rgba(168,209,255,0.5), var(--text-primary), rgba(168,209,255,0.5));
  border-radius: 2px;
  box-shadow: 0 0 20px rgba(168,209,255,0.4);
}

/* Команда */
.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 40px;
  margin-top: 20px;
}

.team-card {
  padding: 40px 30px 35px;
  text-align: center;
  transition: transform 0.3s ease;
}

.team-card:hover {
  transform: translateY(-8px);
}

.avatar-container {
  position: relative;
  width: 130px;
  height: 130px;
  margin: 0 auto 25px;
}

.avatar-ring {
  position: absolute;
  top: -6px;
  left: -6px;
  right: -6px;
  bottom: -6px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(168,209,255,0.6), var(--text-primary), rgba(168,209,255,0.6));
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
  background: var(--blue-dark);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  color: white;
  position: relative;
  z-index: 2;
  box-shadow: 0 10px 30px rgba(8,51,88,0.4);
  border: 2px solid rgba(255,255,255,0.15);
}

.team-name {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 8px;
  color: var(--text-primary);
}

.team-position {
  font-size: 1.2rem;
  color: var(--blue-very-pale);
  margin-bottom: 20px;
  font-weight: 400;
  letter-spacing: 0.3px;
}

.team-contacts {
  display: flex;
  justify-content: center;
  gap: 25px;
  margin: 22px 0 18px;
}

.team-contacts a {
  color: var(--text-secondary);
  font-size: 1.4rem;
  transition: color 0.2s, transform 0.2s;
  text-decoration: none;
}

.team-contacts a:hover {
  color: white;
  transform: scale(1.15);
}

.team-quote {
  font-style: italic;
  color: var(--text-tertiary);
  font-size: 1rem;
  border-top: 1px solid rgba(168,209,255,0.3);
  padding-top: 20px;
  margin-top: 10px;
  line-height: 1.6;
}

/* Контакты компании */
.company-contacts-row {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 30px;
  background: rgba(10, 77, 140, 0.2);
  backdrop-filter: blur(20px);
  border-radius: 80px;
  padding: 30px 50px;
  border: 1px solid rgba(168,209,255,0.25);
  margin: 60px auto;
  max-width: 900px;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 14px;
  color: var(--text-primary);
  text-decoration: none;
  font-size: 1.25rem;
  transition: all 0.2s;
  padding: 6px 20px;
  border-radius: 60px;
  background: rgba(0,0,0,0.2);
  border: 1px solid transparent;
}

.contact-item .icon-symbol {
  font-size: 2.2rem;
  margin-right: 5px;
}

.contact-item:hover {
  border-color: var(--blue-very-pale);
  background: rgba(168,209,255,0.1);
  transform: translateY(-3px);
}

/* Коммерческие факторы */
.expertise-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 30px;
  margin: 50px 0;
}

.factor-card {
  padding: 50px 30px;
  text-align: center;
  transition: transform 0.3s;
}

.factor-card:hover {
  transform: translateY(-8px);
}

.factor-icon {
  font-size: 4rem;
  margin-bottom: 20px;
  background: linear-gradient(135deg, #A8D1FF, #6BB2F0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  filter: drop-shadow(0 6px 16px rgba(168,209,255,0.5));
}

.factor-title {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 20px;
  color: var(--text-primary);
}

.factor-desc {
  color: var(--text-secondary);
  font-size: 1.1rem;
  line-height: 1.6;
}

/* Форма обратной связи и отзывы */
.feedback-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  margin-top: 40px;
}

.feedback-form {
  padding: 45px 40px;
  border-radius: 40px;
}

.form-group {
  margin-bottom: 25px;
}

.form-group label {
  display: block;
  margin-bottom: 10px;
  color: var(--text-tertiary);
  font-size: 1.1rem;
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 16px 22px;
  border-radius: 50px;
  border: 1px solid rgba(168,209,255,0.3);
  background: rgba(10,77,140,0.2);
  backdrop-filter: blur(8px);
  color: white;
  font-size: 1.1rem;
  transition: border 0.2s, box-shadow 0.2s;
}

.form-control:focus {
  outline: none;
  border-color: var(--blue-very-pale);
  box-shadow: 0 0 20px rgba(168,209,255,0.4);
}

.form-control:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

textarea.form-control {
  border-radius: 30px;
  resize: vertical;
}

.submit-btn {
  background: rgba(10,77,140,0.4);
  border: 1px solid rgba(168,209,255,0.3);
  color: white;
  padding: 18px 40px;
  border-radius: 60px;
  font-size: 1.3rem;
  font-weight: 600;
  cursor: pointer;
  backdrop-filter: blur(10px);
  width: 100%;
  transition: background 0.3s, border-color 0.3s, transform 0.2s;
  position: relative;
  overflow: hidden;
}

.submit-btn:hover:not(:disabled) {
  background: rgba(10,77,140,0.6);
  border-color: var(--blue-very-pale);
  transform: scale(1.02);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.button-glow {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, 
    transparent, 
    rgba(168,209,255,0.3), 
    rgba(255,255,255,0.4), 
    rgba(168,209,255,0.3), 
    transparent
  );
  transform: translateX(-100%);
  transition: transform 0.8s ease;
}

.submit-btn:hover:not(:disabled) .button-glow {
  transform: translateX(100%);
}

.form-hint {
  color: var(--blue-pale);
  margin-top: 20px;
  font-size: 0.95rem;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 25px;
  max-height: 600px;
  overflow-y: auto;
  padding-right: 10px;
}

.reviews-list::-webkit-scrollbar {
  width: 8px;
}

.reviews-list::-webkit-scrollbar-track {
  background: rgba(10,77,140,0.2);
  border-radius: 10px;
}

.reviews-list::-webkit-scrollbar-thumb {
  background: var(--blue-pale);
  border-radius: 10px;
}

.review-card {
  padding: 30px;
  border-radius: 30px;
  transition: transform 0.2s;
}

.review-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
}

.review-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: var(--blue-dark);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: bold;
  text-transform: uppercase;
  border: 2px solid rgba(255,255,255,0.2);
}

.review-author {
  font-size: 1.3rem;
  font-weight: 700;
  color: white;
}

.review-date {
  color: var(--blue-pale);
  font-size: 0.9rem;
  margin-top: 4px;
}

.review-text {
  color: var(--text-secondary);
  line-height: 1.6;
  font-size: 1rem;
}

.icon-symbol {
  font-size: 1.9rem;
  line-height: 1;
  display: inline-block;
  filter: drop-shadow(0 2px 6px #6BB2F0);
}

/* Индикатор загрузки */
.loading-indicator {
  text-align: center;
  padding: 60px;
  color: var(--text-secondary);
}

.spinner {
  width: 50px;
  height: 50px;
  border: 3px solid rgba(168,209,255,0.3);
  border-top-color: var(--blue-very-pale);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Сообщение об ошибке */
.error-message {
  padding: 30px;
  text-align: center;
  margin: 20px 0;
  color: #ff6b6b;
}

.error-icon {
  font-size: 3rem;
  display: block;
  margin-bottom: 15px;
}

.retry-btn {
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  color: white;
  padding: 10px 30px;
  border-radius: 30px;
  margin-top: 15px;
  cursor: pointer;
  transition: all 0.2s;
}

.retry-btn:hover {
  background: rgba(255,255,255,0.2);
  border-color: rgba(255,255,255,0.3);
}

/* Нет отзывов */
.no-reviews {
  padding: 40px;
  text-align: center;
  color: var(--text-secondary);
}

/* Адаптивность */
@media (max-width: 900px) {
  .feedback-row {
    grid-template-columns: 1fr;
  }
  
  .company-contacts-row {
    flex-direction: column;
    align-items: center;
    border-radius: 50px;
    padding: 30px;
  }
}

@media (max-width: 600px) {
  .section-title {
    font-size: 2.2rem;
  }
  
  .team-card {
    padding: 30px 20px;
  }
}
</style>