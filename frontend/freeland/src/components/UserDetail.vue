<!-- resources/js/components/UserDetail.vue -->
<template>
  <div class="user-detail-page">
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
      
      <!-- Частицы -->
      <div class="particles" id="particles"></div>
    </div>

    <HeaderMenu />

    <!-- Основной контент -->
    <div class="container">
      <div v-if="loading" class="loading-state">
        <div class="loader ios-glass">
          <div class="loader-spinner"></div>
          <p>Загрузка профиля...</p>
        </div>
      </div>

      <div v-else-if="user" class="user-detail-content">
        <!-- Кнопка назад -->
        <button @click="goBack" class="back-button ios-glass">
          ← Назад к фрилансерам
        </button>

        <!-- Основная информация -->
        <div class="profile-header ios-glass">
          <div class="profile-avatar-section">
            <div class="avatar-container">
              <div class="avatar-ring"></div>
              <div class="avatar-large">
                {{ getInitials(user.full_name || user.name || user.login) }}
              </div>
              <div class="rating-badge" v-if="user.rating">
                {{ user.rating }} ★
              </div>
            </div>
            
            <div class="profile-status" :class="user.online ? 'online' : 'offline'">
              {{ user.online ? 'В сети' : 'Не в сети' }}
            </div>
          </div>

          <div class="profile-info-section">
            <h1 class="profile-name">{{ user.full_name || user.name || user.login || 'Без имени' }}</h1>
            
            <div class="profile-login" v-if="user.login">
              @{{ user.login }}
            </div>

            <div class="profile-role" v-if="user.role">
              {{ formatRole(user.role) }}
            </div>

            <div class="profile-location" v-if="user.city || user.country">
              <span class="info-icon">📍</span>
              {{ user.city }}{{ user.city && user.country ? ', ' : '' }}{{ user.country }}
            </div>

            <!-- Кнопки действий -->
            <div class="action-buttons">
              <button class="action-button primary ios-glass" @click="contactUser">
                <span class="button-icon">💬</span>
                Связаться
              </button>
              <button class="action-button secondary ios-glass" @click="hireUser" v-if="isFreelancer">
                <span class="button-icon">🤝</span>
                Нанять
              </button>
              <button class="action-button secondary ios-glass" @click="addToFavorites">
                <span class="button-icon">⭐</span>
                В избранное
              </button>
            </div>
          </div>
        </div>

        <!-- Детальная информация -->
        <div class="profile-details-grid">
          <!-- Левая колонка -->
          <div class="details-column">
            <!-- О себе -->
            <div class="details-card ios-glass" v-if="user.bio || user.about">
              <h3 class="card-title">О себе</h3>
              <p class="bio-text">{{ user.bio || user.about || 'Пользователь пока не добавил информацию о себе' }}</p>
            </div>

            <!-- Навыки -->
            <div class="details-card ios-glass" v-if="user.skills && user.skills.length">
              <h3 class="card-title">Навыки</h3>
              <div class="skills-container">
                <span v-for="skill in user.skills" :key="skill" class="skill-tag">
                  {{ skill }}
                </span>
              </div>
            </div>

            <!-- Образование -->
            <div class="details-card ios-glass" v-if="user.education && user.education.length">
              <h3 class="card-title">Образование</h3>
              <div v-for="(edu, index) in user.education" :key="index" class="education-item">
                <div class="education-year">{{ edu.year }}</div>
                <div class="education-details">
                  <div class="education-institution">{{ edu.institution }}</div>
                  <div class="education-degree">{{ edu.degree }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Правая колонка -->
          <div class="details-column">
            <!-- Статистика -->
            <div class="details-card ios-glass">
              <h3 class="card-title">Статистика</h3>
              <div class="stats-grid">
                <div class="stat-item">
                  <span class="stat-value">{{ user.completed_projects || 0 }}</span>
                  <span class="stat-label">Завершенных проектов</span>
                </div>
                <div class="stat-item">
                  <span class="stat-value">{{ user.rating || '0.0' }}</span>
                  <span class="stat-label">Рейтинг</span>
                </div>
                <div class="stat-item">
                  <span class="stat-value">{{ user.experience || 0 }}</span>
                  <span class="stat-label">Лет опыта</span>
                </div>
                <div class="stat-item">
                  <span class="stat-value">{{ user.reviews_count || 0 }}</span>
                  <span class="stat-label">Отзывов</span>
                </div>
              </div>
            </div>

            <!-- Контактная информация -->
            <div class="details-card ios-glass">
              <h3 class="card-title">Контактная информация</h3>
              <div class="contact-info">
                <div class="contact-item" v-if="user.email">
                  <span class="contact-icon">📧</span>
                  <span class="contact-label">Email:</span>
                  <a :href="'mailto:' + user.email" class="contact-value">{{ user.email }}</a>
                </div>
                <div class="contact-item" v-if="user.phone">
                  <span class="contact-icon">📞</span>
                  <span class="contact-label">Телефон:</span>
                  <a :href="'tel:' + user.phone" class="contact-value">{{ user.phone }}</a>
                </div>
                <div class="contact-item" v-if="user.telegram">
                  <span class="contact-icon">✈️</span>
                  <span class="contact-label">Telegram:</span>
                  <a :href="'https://t.me/' + user.telegram" target="_blank" class="contact-value">@{{ user.telegram }}</a>
                </div>
                <div class="contact-item" v-if="user.github">
                  <span class="contact-icon">🐙</span>
                  <span class="contact-label">GitHub:</span>
                  <a :href="'https://github.com/' + user.github" target="_blank" class="contact-value">{{ user.github }}</a>
                </div>
                <div class="contact-item" v-if="user.website">
                  <span class="contact-icon">🌐</span>
                  <span class="contact-label">Сайт:</span>
                  <a :href="user.website" target="_blank" class="contact-value">{{ user.website }}</a>
                </div>
              </div>
            </div>

            <!-- Цены -->
            <div class="details-card ios-glass" v-if="isFreelancer">
              <h3 class="card-title">Цены</h3>
              <div class="pricing-info">
                <div class="price-item" v-if="user.hourly_rate">
                  <span class="price-label">Почасовая ставка:</span>
                  <span class="price-value">{{ formatPrice(user.hourly_rate) }} ₽/час</span>
                </div>
                <div class="price-item" v-if="user.min_price">
                  <span class="price-label">Минимальная цена:</span>
                  <span class="price-value">{{ formatPrice(user.min_price) }} ₽</span>
                </div>
                <div class="price-item" v-if="user.max_price">
                  <span class="price-label">Максимальная цена:</span>
                  <span class="price-value">{{ formatPrice(user.max_price) }} ₽</span>
                </div>
              </div>
            </div>

            <!-- Языки -->
            <div class="details-card ios-glass" v-if="user.languages && user.languages.length">
              <h3 class="card-title">Языки</h3>
              <div class="languages-list">
                <div v-for="lang in user.languages" :key="lang.name" class="language-item">
                  <span class="language-name">{{ lang.name }}</span>
                  <span class="language-level" :class="lang.level">{{ lang.level }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Портфолио / Проекты -->
        <div class="portfolio-section ios-glass" v-if="user.portfolio && user.portfolio.length">
          <h3 class="section-title">Портфолио</h3>
          <div class="portfolio-grid">
            <div v-for="item in user.portfolio" :key="item.id" class="portfolio-item">
              <img v-if="item.image" :src="item.image" :alt="item.title" class="portfolio-image">
              <div class="portfolio-info">
                <h4 class="portfolio-title">{{ item.title }}</h4>
                <p class="portfolio-description">{{ item.description }}</p>
                <a v-if="item.link" :href="item.link" target="_blank" class="portfolio-link">Посмотреть проект →</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Отзывы -->
        <div class="reviews-section ios-glass" v-if="reviews.length">
          <h3 class="section-title">Отзывы</h3>
          <div class="reviews-list">
            <div v-for="review in reviews" :key="review.id" class="review-item">
              <div class="review-header">
                <div class="reviewer-avatar">{{ getInitials(review.author_name) }}</div>
                <div class="reviewer-info">
                  <div class="reviewer-name">{{ review.author_name }}</div>
                  <div class="review-date">{{ formatDate(review.created_at) }}</div>
                </div>
                <div class="review-rating">
                  <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= review.rating }">★</span>
                </div>
              </div>
              <p class="review-text">{{ review.text }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Пользователь не найден -->
      <div v-else class="not-found-state ios-glass">
        <div class="not-found-icon">👤</div>
        <h3 class="not-found-title">Пользователь не найден</h3>
        <p class="not-found-text">Запрашиваемый профиль не существует или был удален</p>
        <button @click="goBack" class="reset-button ios-glass">
          Вернуться к списку
        </button>
      </div>
    </div>
    <FooterApp />
  </div>
</template>

<script>
import FooterApp from '@/elements/FooterApp.vue';
import HeaderMenu from '@/elements/HeaderMenu.vue';

export default {
  name: 'UserDetail',
  
  components: {
    HeaderMenu,
    FooterApp
  },
  
  data() {
    return {
      user: null,
      reviews: [],
      loading: true,
      apiBaseUrl: ''
    }
  },
  
  computed: {
    isFreelancer() {
      return this.user && (this.user.role === 'freelancer' || this.user.role === 'Фрилансер' || this.user.role === 2);
    }
  },
  
  created() {
    this.fetchUser();
    this.fetchReviews();
    this.initParticles();
  },
  
  methods: {
    async fetchUser() {
      try {
        const userId = this.$route.params.id;
        const response = await fetch(`${this.apiBaseUrl}/api/users/${userId}`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        this.user = data.user || data;
        console.log('Загружен пользователь:', this.user);
        
      } catch (error) {
        console.error('Ошибка при загрузке пользователя:', error);
        this.showNotification('Ошибка при загрузке профиля', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    async fetchReviews() {
      try {
        const userId = this.$route.params.id;
        const response = await fetch(`${this.apiBaseUrl}/api/comments?user_id=${userId}`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        this.reviews = data;
        
      } catch (error) {
        console.error('Ошибка при загрузке отзывов:', error);
      }
    },
    
    goBack() {
      this.$router.push('/freelancers');
    },
    
    contactUser() {
      this.$router.push(`/messages?user=${this.user.id}`);
    },
    
    hireUser() {
      this.$router.push(`/projects/create?freelancer=${this.user.id}`);
    },
    
    addToFavorites() {
      // Логика добавления в избранное
      this.showNotification('Добавлено в избранное', 'success');
    },
    
    formatRole(role) {
      const roleMap = {
        'freelancer': 'Фрилансер',
        'customer': 'Заказчик',
        'admin': 'Администратор',
        1: 'Заказчик',
        2: 'Фрилансер',
        3: 'Администратор'
      };
      return roleMap[role] || role || 'Пользователь';
    },
    
    formatPrice(price) {
      if (!price) return '0';
      return new Intl.NumberFormat('ru-RU').format(price);
    },
    
    formatDate(date) {
      if (!date) return 'Не указано';
      return new Date(date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    
    getInitials(name) {
      if (!name) return '?';
      return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    },
    
    initParticles() {
      const particlesContainer = document.getElementById('particles');
      if (!particlesContainer) return;
      
      for (let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        const size = Math.random() * 6 + 2;
        const posX = Math.random() * 100;
        const posY = Math.random() * 100;
        const duration = Math.random() * 20 + 10;
        const delay = Math.random() * -20;
        
        particle.style.cssText = `
          width: ${size}px;
          height: ${size}px;
          left: ${posX}%;
          top: ${posY}%;
          background: rgba(168, 209, 255, ${Math.random() * 0.3 + 0.1});
          box-shadow: 0 0 ${size * 2}px rgba(168, 209, 255, 0.3);
          animation: floatParticle ${duration}s linear ${delay}s infinite;
        `;
        
        particlesContainer.appendChild(particle);
      }
    },
    
    showNotification(message, type = 'info') {
      console.log(`[${type}] ${message}`);
      // Здесь можно добавить toast-уведомления
    }
  }
}
</script>

<style scoped>
.user-detail-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  position: relative;
  padding: 40px 0 80px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 20;
  width: 100%;
}

.back-button {
  padding: 12px 24px;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.3);
  border-radius: 9999px;
  color: #FFFFFF;
  font-size: 1rem;
  cursor: pointer;
  margin-bottom: 30px;
  transition: all 0.3s ease;
}

.back-button:hover {
  background: rgba(10, 77, 140, 0.3);
  border-color: rgba(168, 209, 255, 0.5);
  transform: translateX(-3px);
}

/* Профиль шапка */
.profile-header {
  padding: 40px;
  margin-bottom: 30px;
  display: flex;
  gap: 40px;
  flex-wrap: wrap;
  align-items: center;
}

.profile-avatar-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.avatar-container {
  position: relative;
  width: 150px;
  height: 150px;
}

.avatar-ring {
  position: absolute;
  top: -5px;
  left: -5px;
  right: -5px;
  bottom: -5px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(168, 209, 255, 0.6), #FFFFFF, rgba(168, 209, 255, 0.6));
  animation: rotate 4s linear infinite;
  filter: blur(3px);
  opacity: 0.7;
}

@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.avatar-large {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3.5rem;
  font-weight: 700;
  color: #FFFFFF;
  position: relative;
  z-index: 2;
  box-shadow: 0 10px 30px rgba(8, 51, 88, 0.4);
}

.rating-badge {
  position: absolute;
  bottom: 5px;
  right: 5px;
  background: #F0B90B;
  color: #000000;
  padding: 6px 12px;
  border-radius: 9999px;
  font-size: 1rem;
  font-weight: 600;
  z-index: 3;
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.profile-status {
  padding: 6px 16px;
  border-radius: 9999px;
  font-size: 0.9rem;
  font-weight: 500;
}

.profile-status.online {
  background: rgba(39, 174, 96, 0.2);
  border: 1px solid rgba(39, 174, 96, 0.4);
  color: #2ecc71;
}

.profile-status.offline {
  background: rgba(149, 165, 166, 0.2);
  border: 1px solid rgba(149, 165, 166, 0.4);
  color: #95a5a6;
}

.profile-info-section {
  flex: 1;
}

.profile-name {
  font-size: 2.5rem;
  font-weight: 700;
  color: #FFFFFF;
  margin-bottom: 5px;
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.profile-login {
  font-size: 1.2rem;
  color: #A8D1FF;
  margin-bottom: 10px;
}

.profile-role {
  display: inline-block;
  padding: 6px 16px;
  background: rgba(10, 77, 140, 0.3);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 9999px;
  font-size: 0.9rem;
  color: #F0F8FF;
  margin-bottom: 15px;
}

.profile-location {
  color: #F0F8FF;
  font-size: 1rem;
  margin-bottom: 20px;
}

.info-icon {
  margin-right: 5px;
}

.action-buttons {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.action-button {
  padding: 12px 24px;
  border-radius: 9999px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  border: 1px solid rgba(168, 209, 255, 0.3);
}

.action-button.primary {
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  color: #FFFFFF;
}

.action-button.primary:hover {
  background: linear-gradient(135deg, #1A6BB3, #2A7FC9);
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(8, 51, 88, 0.4);
}

.action-button.secondary {
  background: rgba(10, 77, 140, 0.2);
  color: #FFFFFF;
}

.action-button.secondary:hover {
  background: rgba(10, 77, 140, 0.3);
  transform: translateY(-2px);
}

.button-icon {
  font-size: 1.1rem;
}

/* Сетка деталей */
.profile-details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
  margin-bottom: 30px;
}

.details-card {
  padding: 30px;
  height: fit-content;
}

.card-title {
  font-size: 1.3rem;
  font-weight: 600;
  color: #FFFFFF;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
}

.bio-text {
  color: #F0F8FF;
  line-height: 1.8;
  font-size: 1rem;
  white-space: pre-wrap;
}

.skills-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.skill-tag {
  padding: 8px 16px;
  background: rgba(26, 107, 179, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.15);
  border-radius: 9999px;
  font-size: 0.95rem;
  color: #F0F8FF;
  transition: all 0.3s ease;
}

.skill-tag:hover {
  background: rgba(26, 107, 179, 0.3);
  transform: translateY(-2px);
}

/* Образование */
.education-item {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

.education-year {
  min-width: 80px;
  font-weight: 600;
  color: #A8D1FF;
}

.education-details {
  flex: 1;
}

.education-institution {
  font-weight: 600;
  color: #FFFFFF;
  margin-bottom: 5px;
}

.education-degree {
  color: #F0F8FF;
  font-size: 0.95rem;
}

/* Статистика */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.stat-item {
  text-align: center;
  padding: 15px;
  background: rgba(10, 77, 140, 0.1);
  border-radius: 16px;
}

.stat-value {
  display: block;
  font-size: 1.8rem;
  font-weight: 700;
  color: #FFFFFF;
  line-height: 1.2;
  margin-bottom: 5px;
}

.stat-label {
  display: block;
  font-size: 0.85rem;
  color: #A8D1FF;
}

/* Контактная информация */
.contact-info {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.contact-icon {
  font-size: 1.2rem;
  min-width: 30px;
}

.contact-label {
  color: #A8D1FF;
  font-size: 0.95rem;
  min-width: 70px;
}

.contact-value {
  color: #FFFFFF;
  text-decoration: none;
  font-size: 1rem;
  transition: color 0.3s ease;
}

.contact-value:hover {
  color: #A8D1FF;
  text-decoration: underline;
}

/* Цены */
.pricing-info {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.price-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
}

.price-item:last-child {
  border-bottom: none;
}

.price-label {
  color: #A8D1FF;
  font-size: 0.95rem;
}

.price-value {
  color: #FFFFFF;
  font-size: 1.2rem;
  font-weight: 600;
}

/* Языки */
.languages-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.language-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.language-name {
  color: #FFFFFF;
  font-size: 1rem;
}

.language-level {
  padding: 4px 12px;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 500;
}

.language-level.beginner {
  background: rgba(241, 196, 15, 0.2);
  color: #f1c40f;
}

.language-level.intermediate {
  background: rgba(52, 152, 219, 0.2);
  color: #3498db;
}

.language-level.advanced {
  background: rgba(46, 204, 113, 0.2);
  color: #2ecc71;
}

.language-level.native {
  background: rgba(155, 89, 182, 0.2);
  color: #9b59b6;
}

/* Портфолио */
.portfolio-section,
.reviews-section {
  padding: 40px;
  margin-top: 30px;
}

.section-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: #FFFFFF;
  margin-bottom: 30px;
  text-align: center;
}

.portfolio-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 25px;
}

.portfolio-item {
  background: rgba(10, 77, 140, 0.1);
  border-radius: 16px;
  overflow: hidden;
  transition: transform 0.3s ease;
}

.portfolio-item:hover {
  transform: translateY(-5px);
}

.portfolio-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.portfolio-info {
  padding: 20px;
}

.portfolio-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: #FFFFFF;
  margin-bottom: 10px;
}

.portfolio-description {
  color: #F0F8FF;
  font-size: 0.95rem;
  line-height: 1.6;
  margin-bottom: 15px;
}

.portfolio-link {
  color: #A8D1FF;
  text-decoration: none;
  font-size: 0.95rem;
  transition: color 0.3s ease;
}

.portfolio-link:hover {
  color: #FFFFFF;
}

/* Отзывы */
.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.review-item {
  padding: 25px;
  background: rgba(10, 77, 140, 0.1);
  border-radius: 16px;
}

.review-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
  flex-wrap: wrap;
}

.reviewer-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1.2rem;
  color: #FFFFFF;
}

.reviewer-info {
  flex: 1;
}

.reviewer-name {
  font-weight: 600;
  color: #FFFFFF;
  margin-bottom: 3px;
}

.review-date {
  font-size: 0.85rem;
  color: #A8D1FF;
}

.review-rating {
  display: flex;
  gap: 3px;
}

.star {
  color: #bdc3c7;
  font-size: 1.2rem;
}

.star.filled {
  color: #f1c40f;
}

.review-text {
  color: #F0F8FF;
  line-height: 1.8;
  font-size: 1rem;
}

/* Состояния */
.loading-state,
.not-found-state {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 500px;
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

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loader p {
  color: #F0F8FF;
  font-size: 1.1rem;
}

.not-found-state {
  padding: 80px 40px;
  text-align: center;
  max-width: 500px;
  margin: 60px auto;
}

.not-found-icon {
  font-size: 5rem;
  margin-bottom: 20px;
  filter: drop-shadow(0 10px 20px rgba(168, 209, 255, 0.2));
}

.not-found-title {
  font-size: 2rem;
  font-weight: 600;
  margin-bottom: 15px;
  color: #FFFFFF;
}

.not-found-text {
  color: #F0F8FF;
  font-size: 1.1rem;
  margin-bottom: 30px;
}

.reset-button {
  padding: 15px 40px;
  font-size: 1.1rem;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.3);
  border-radius: 9999px;
  color: #FFFFFF;
  cursor: pointer;
  transition: all 0.3s ease;
}

.reset-button:hover {
  background: rgba(10, 77, 140, 0.3);
  border-color: rgba(168, 209, 255, 0.5);
}

/* Адаптивность */
@media (max-width: 992px) {
  .profile-details-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    text-align: center;
    padding: 30px;
  }
  
  .profile-name {
    font-size: 2rem;
  }
  
  .action-buttons {
    justify-content: center;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .portfolio-grid {
    grid-template-columns: 1fr;
  }
  
  .review-header {
    flex-direction: column;
    text-align: center;
  }
  
  .review-rating {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 15px;
  }
  
  .profile-header {
    padding: 20px;
  }
  
  .avatar-container {
    width: 120px;
    height: 120px;
  }
  
  .avatar-large {
    font-size: 2.5rem;
  }
  
  .profile-name {
    font-size: 1.6rem;
  }
  
  .details-card {
    padding: 20px;
  }
  
  .contact-item {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .price-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }
}
</style>