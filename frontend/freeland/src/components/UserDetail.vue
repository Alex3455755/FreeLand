<!-- resources/js/components/UserDetail.vue -->
<template>
  <div class="user-detail-page">
    <SEOHead
      :title="seoData.title"
      :description="seoData.description"
      :keywords="seoData.keywords"
      :og-title="seoData.title"
      :og-description="seoData.description"
      :canonical="seoData.canonical"
    />
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
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
          Назад к фрилансерам
        </button>

        <!-- Основная информация -->
        <div class="profile-header ios-glass">
          <div class="profile-avatar-section">
            <div class="avatar-container">
              <div class="avatar-ring"></div>
              <div class="avatar-large">
                <img :src="userAvatar(user)" :alt="user.full_name || user.login || ''" />
              </div>
              <div class="rating-badge" v-if="user.rating || averageRating">
                {{ Number(user.rating || averageRating || 0).toFixed(1) }} ★
              </div>
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
              {{ user.city }}{{ user.city && user.country ? ', ' : '' }}{{ user.country }}
            </div>

            <!-- Кнопки действий -->
            <div class="action-buttons">
              <button v-if="canLeaveReview" class="action-button secondary ios-glass" @click="openReviewModal">
                Оставить отзыв
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

                        <div class="details-card ios-glass">
              <h3 class="card-title">Контакты</h3>
              <div class="contact-info">
                <div class="contact-item" v-if="user.email">
                  <span class="contact-label">Email:</span>
                  <a :href="'mailto:' + user.email" class="contact-value">{{ user.email }}</a>
                </div>
                <div class="contact-item" v-if="user.phone">
                  <span class="contact-label">Телефон:</span>
                  <a :href="'tel:' + user.phone" class="contact-value">{{ user.phone }}</a>
                </div>
                <div class="contact-item" v-if="user.telegram">
                  <span class="contact-label">Telegram:</span>
                  <a :href="telegramHref(user.telegram)" target="_blank" rel="noopener noreferrer" class="contact-value">{{ telegramHandle(user.telegram) }}</a>
                </div>
                <div class="contact-item" v-if="user.github">
                  <span class="contact-label">GitHub:</span>
                  <a :href="user.github" target="_blank" rel="noopener noreferrer" class="contact-value">{{ user.github }}</a>
                </div>
                <div class="contact-item" v-if="user.portfolio_url">
                  <span class="contact-label">Портфолио:</span>
                  <a :href="user.portfolio_url" target="_blank" rel="noopener noreferrer" class="contact-value">{{ user.portfolio_url }}</a>
                </div>
                <div class="contact-item" v-if="user.website">
                  <span class="contact-label">Сайт:</span>
                  <a :href="user.website" target="_blank" rel="noopener noreferrer" class="contact-value">{{ user.website }}</a>
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
                  <span class="stat-value">{{ Number(user.rating || averageRating || 0).toFixed(1) }}</span>
                  <span class="stat-label">Рейтинг</span>
                </div>
                <div class="stat-item">
                  <span class="stat-value">{{ reviews.length }}</span>
                  <span class="stat-label">Отзывов</span>
                </div>
                <div class="stat-item">
                  <span class="stat-value">{{ user.projects_completed || 0 }}</span>
                  <span class="stat-label">Проектов</span>
                </div>
                <div class="stat-item">
                  <span class="stat-value">{{ user.registered_days || 0 }}</span>
                  <span class="stat-label">Дней на сайте</span>
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

        <!-- Отзывы об этом пользователе -->
        <div class="reviews-section ios-glass">
          <div class="reviews-header">
            <h3 class="section-title">Отзывы о {{ user.full_name || user.login }} ({{ reviews.length }})</h3>
            <div class="reviews-filter" v-if="reviews.length > 0">
              <span class="filter-label">Сортировка:</span>
              <select v-model="sortOrder" class="filter-select ios-glass">
                <option value="newest">Сначала новые</option>
                <option value="oldest">Сначала старые</option>
                <option value="highest">Сначала высокий рейтинг</option>
                <option value="lowest">Сначала низкий рейтинг</option>
              </select>
            </div>
          </div>

          <div v-if="reviewsLoading" class="reviews-loading">
            <div class="loader-sm"></div>
            <span>Загрузка отзывов...</span>
          </div>

          <div v-else-if="sortedReviews.length > 0" class="reviews-list">
            <div v-for="review in sortedReviews" :key="review.id" class="review-item">
              <div class="review-header">
                <img class="reviewer-avatar" :src="reviewerAvatar(review)" alt="" />
                <div class="reviewer-info">
                  <div class="reviewer-name">{{ getAuthorName(review.author_id) }}</div>
                  <div class="review-date">{{ formatDate(review.created_at) }}</div>
                </div>
                <div class="review-rating">
                  <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= review.rating }">★</span>
                  <span class="rating-value">{{ review.rating }}</span>
                </div>
              </div>
              <p class="review-text">{{ review.text }}</p>
              
              <!-- Кнопка удаления для автора отзыва или админа -->
              <div v-if="review && review.author_id && canDeleteReview(review)" class="review-actions">
                <button @click="deleteReview(review.id)" class="delete-review-button">
                  <span class="button-icon">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
                  </span>
                  Удалить
                </button>
              </div>
            </div>
          </div>

          <div v-else class="no-reviews">
            <p>У пользователя пока нет отзывов</p>
            <button v-if="canLeaveReview" @click="openReviewModal" class="write-review-button ios-glass">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path></svg>
              Написать первый отзыв
            </button>
          </div>
        </div>
      </div>

      <!-- Пользователь не найден -->
      <div v-else class="not-found-state ios-glass">
        <div class="not-found-icon">
          <svg viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </div>
        <h3 class="not-found-title">Пользователь не найден</h3>
        <p class="not-found-text">Запрашиваемый профиль не существует или был удален</p>
        <button @click="goBack" class="reset-button ios-glass">
          Вернуться к списку
        </button>
      </div>
    </div>

    <!-- Модальное окно для отзыва -->
    <div v-if="showReviewModal" class="modal-overlay" @click.self="closeReviewModal">
      <div class="modal-container ios-glass">
        <div class="modal-header">
          <h2 class="modal-title">Оставить отзыв о {{ user?.full_name || user?.login }}</h2>
          <button @click="closeReviewModal" class="modal-close">×</button>
        </div>
        
        <form @submit.prevent="submitReview" class="modal-form">
          <div class="form-group">
            <label>Оценка <span class="required">*</span></label>
            <div class="rating-input">
              <span 
                v-for="n in 5" 
                :key="n" 
                class="rating-star" 
                :class="{ active: n <= reviewForm.rating }"
                @click="reviewForm.rating = n"
              >★</span>
              <span class="rating-text">{{ reviewForm.rating }}/5</span>
            </div>
          </div>
          
          <div class="form-group">
            <label>Текст отзыва <span class="required">*</span></label>
            <textarea 
              v-model="reviewForm.text" 
              class="form-input ios-glass" 
              rows="5"
              placeholder="Напишите ваш отзыв..."
              required
            ></textarea>
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="closeReviewModal" class="modal-button cancel">
              Отмена
            </button>
            <button type="submit" class="modal-button submit" :disabled="reviewSubmitting">
              <span v-if="reviewSubmitting" class="button-spinner"></span>
              {{ reviewSubmitting ? 'Отправка...' : 'Оставить отзыв' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import HeaderMenu from '@/elements/HeaderMenu.vue';
import SEOHead from '@/elements/SEOHead.vue';
import { avatarSrc } from '@/utils/avatar';

export default {
  name: 'UserDetail',

  components: {
    HeaderMenu,
    SEOHead
  },
  
  data() {
    return {
      user: null,
      reviews: [],
      reviewsLoading: false,
      loading: true,
      apiBaseUrl: '',
      
      // Текущий пользователь (авторизованный)
      currentUser: null,
      
      // Кэш для авторов отзывов
      reviewAuthors: {},
      loadingAuthors: {},
      
      // Сортировка отзывов
      sortOrder: 'newest',
      
      // Модальное окно отзыва
      showReviewModal: false,
      reviewSubmitting: false,
      reviewForm: {
        rating: 5,
        text: ''
      }
    }
  },
  
  computed: {
    seoData() {
      const origin = typeof window !== 'undefined' ? window.location.origin : ''
      const displayName = this.user?.full_name || this.user?.login
      return {
        title: displayName
          ? `${displayName} — профиль специалиста на FreeLand`
          : 'Профиль специалиста — FreeLand',
        description: displayName
          ? `Профиль специалиста ${displayName} на бирже FreeLand: рейтинг, отзывы и информация для сотрудничества.`
          : 'Просмотр профиля специалиста на FreeLand: рейтинг, отзывы и контактная информация.',
        keywords: 'профиль фрилансера, специалист, отзывы, рейтинг, портфолио',
        canonical: origin + (this.user?.id ? `/freelancers/${this.user.id}` : '/freelancer')
      }
    },
    isFreelancer() {
      return this.user && (this.user.role === 'freelancer' || this.user.role === 'Фрилансер' || this.user.role === 2);
    },
    
    // Средний рейтинг из отзывов
    averageRating() {
      if (!this.reviews || !this.reviews.length) return 0;
      const sum = this.reviews.reduce((acc, review) => {
        const rating = Number(review.rating) || 0;
        return acc + rating;
      }, 0);
      return sum / this.reviews.length;
    },
    
    // Может ли текущий пользователь оставить отзыв
    canLeaveReview() {
      if (!this.currentUser || !this.user) return false;
      if (this.currentUser.id === this.user.id) return false;
      if (this.reviews.some(r => r.author_id === this.currentUser.id)) return false;

      const authorRole = this.normalizeUserRole(this.currentUser.role);
      const targetRole = this.normalizeUserRole(this.user.role);

      if (authorRole === 'freelancer' && targetRole === 'customer') return true;
      if (authorRole === 'customer' && targetRole === 'freelancer') return true;

      return false;
    },
    
    // Отсортированные отзывы
    sortedReviews() {
      const reviews = [...this.reviews];
      
      switch (this.sortOrder) {
        case 'newest':
          return reviews.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        case 'oldest':
          return reviews.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        case 'highest':
          return reviews.sort((a, b) => b.rating - a.rating);
        case 'lowest':
          return reviews.sort((a, b) => a.rating - b.rating);
        default:
          return reviews;
      }
    }
  },
  
  created() {
    this.fetchCurrentUser();
    this.fetchUser();
    this.initParticles();
  },
  
  methods: {
    userAvatar(u) {
      return avatarSrc(u, this.apiBaseUrl);
    },
    reviewerAvatar(review) {
      const a = this.reviewAuthors[review.author_id];
      if (a) return avatarSrc(a, this.apiBaseUrl);
      return avatarSrc({ id: review.author_id }, this.apiBaseUrl);
    },
    // Получение текущего пользователя
    async fetchCurrentUser() {
      const token = localStorage.getItem('token');
      
      if (!token) {
        this.currentUser = null;
        return;
      }

      try {
        const response = await fetch(`${this.apiBaseUrl}/api/user`, {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        });

        const data = await response.json();
        
        if (response.ok && data.success) {
          this.currentUser = data.user;
        } else {
          this.currentUser = null;
        }
      } catch (error) {
        console.error('Ошибка получения пользователя:', error);
        this.currentUser = null;
      }
    },
    
    async fetchUser() {
      try {
        const userId = this.$route.params.id;
        const response = await fetch(`${this.apiBaseUrl}/api/users/${userId}`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        this.user = data.user || data;
        
        // Приводим рейтинг к числу
        if (this.user.rating) {
          this.user.rating = Number(this.user.rating);
        }
        
        console.log('Загружен пользователь:', this.user);
        
        // После загрузки пользователя загружаем отзывы о нем
        await this.fetchReviews();
        
      } catch (error) {
        console.error('Ошибка при загрузке пользователя:', error);
        this.showNotification('Ошибка при загрузке профиля', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    async fetchReviews() {
      if (!this.user) return;
      
      this.reviewsLoading = true;
      
      try {
        const userId = this.user.id;
        const response = await fetch(`${this.apiBaseUrl}/api/comments/user/${userId}`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        // Обрабатываем разные форматы ответа
        let reviews = [];
        if (data.comments) {
          reviews = data.comments;
        } else if (Array.isArray(data)) {
          reviews = data;
        } else {
          reviews = [];
        }
        
        // Убеждаемся, что каждый отзыв имеет author_id
        this.reviews = reviews.map(review => ({
          ...review,
          author_id: review.author_id || review.author?.id
        }));
        
        console.log('Загружены отзывы о пользователе:', this.reviews);
        
        // Инициализируем объекты для кэша
        if (!this.reviewAuthors) {
          this.reviewAuthors = {};
        }
        if (!this.loadingAuthors) {
          this.loadingAuthors = {};
        }
        
        // Загружаем авторов отзывов
        const authorIds = [...new Set(this.reviews.map(r => r.author_id).filter(id => id))];
        for (const authorId of authorIds) {
          await this.fetchAuthor(authorId);
        }
        
        // Обновляем рейтинг пользователя на основе отзывов
        this.updateUserRating();
        
      } catch (error) {
        console.error('Ошибка при загрузке отзывов:', error);
        this.reviews = [];
      } finally {
        this.reviewsLoading = false;
      }
    },
    
    // Загрузить данные автора
    async fetchAuthor(authorId) {
      if (!authorId) return;
      
      // Проверяем, не загружается ли уже
      if (this.loadingAuthors && this.loadingAuthors[authorId]) return;
      
      // Помечаем как загружающийся
      if (!this.loadingAuthors) {
        this.loadingAuthors = {};
      }
      this.loadingAuthors[authorId] = true;
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/users/${authorId}`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        const author = data.user || data;
        
        // Сохраняем в кэш
        if (!this.reviewAuthors) {
          this.reviewAuthors = {};
        }
        this.reviewAuthors[authorId] = author;
        this.$forceUpdate();
        
      } catch (error) {
        console.error('Ошибка при загрузке автора:', error);
        // Сохраняем заглушку
        if (!this.reviewAuthors) {
          this.reviewAuthors = {};
        }
        this.reviewAuthors[authorId] = { full_name: 'Пользователь', login: 'user' };
        this.$forceUpdate();
      } finally {
        // Снимаем пометку о загрузке
        if (this.loadingAuthors) {
          this.loadingAuthors[authorId] = false;
        }
      }
    },
    
    // Получить имя автора по ID
    getAuthorName(authorId) {
      if (!authorId) return 'Пользователь';
      
      // Если автор - текущий пользователь
      if (this.currentUser && this.currentUser.id === authorId) {
        return this.currentUser.full_name || this.currentUser.login || 'Пользователь';
      }
      
      // Ищем автора в загруженных данных
      if (this.reviewAuthors && this.reviewAuthors[authorId]) {
        const author = this.reviewAuthors[authorId];
        return author.full_name || author.login || 'Пользователь';
      }
      
      // Если автора нет в кэше, загружаем его данные (асинхронно)
      this.fetchAuthor(authorId);
      return 'Загрузка...';
    },
    
    // Получить инициалы автора по ID
    getAuthorInitials(authorId) {
      const name = this.getAuthorName(authorId);
      return this.getInitials(name);
    },
    
    // Обновление рейтинга пользователя
    updateUserRating() {
      if (this.reviews.length > 0 && this.user) {
        const avgRating = this.averageRating;
        this.user.rating = avgRating;
      }
    },
    
    // Открыть модальное окно отзыва
    openReviewModal() {
      if (!this.currentUser) {
        this.showNotification('Необходимо авторизоваться', 'error');
        this.$router.push('/login?redirect=' + encodeURIComponent(this.$route.fullPath));
        return;
      }
      
      this.reviewForm = {
        rating: 5,
        text: ''
      };
      this.showReviewModal = true;
      document.body.style.overflow = 'hidden';
    },
    
    // Закрыть модальное окно
    closeReviewModal() {
      this.showReviewModal = false;
      document.body.style.overflow = '';
    },
    
    // Отправить отзыв
    async submitReview() {
      if (!this.currentUser) {
        this.showNotification('Необходимо авторизоваться', 'error');
        this.closeReviewModal();
        this.$router.push('/login?redirect=' + encodeURIComponent(this.$route.fullPath));
        return;
      }
      
      if (!this.reviewForm.rating || this.reviewForm.rating < 1 || this.reviewForm.rating > 5) {
        this.showNotification('Выберите оценку от 1 до 5', 'error');
        return;
      }
      
      if (!this.reviewForm.text.trim()) {
        this.showNotification('Введите текст отзыва', 'error');
        return;
      }

      if (this.reviewForm.text.trim().length < 10) {
        this.showNotification('Текст отзыва должен быть не короче 10 символов', 'error');
        return;
      }
      
      this.reviewSubmitting = true;
      const token = localStorage.getItem('token');
      
      try {
        const reviewData = {
          author_id: this.currentUser.id,
          user_id: this.user.id,
          rating: this.reviewForm.rating,
          text: this.reviewForm.text.trim(),
          created_at: new Date().toISOString()
        };
        
        console.log('Отправка отзыва:', reviewData);
        
        const response = await fetch(`${this.apiBaseUrl}/api/comments/add`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(reviewData)
        });
        
        const data = await response.json();
        console.log('Ответ сервера:', data);
        
        if (response.ok && data.success) {
          // Добавляем автора в кэш
          if (!this.reviewAuthors) {
            this.reviewAuthors = {};
          }
          this.reviewAuthors[this.currentUser.id] = this.currentUser;
          
          // Добавляем отзыв в список
          const newReview = {
            id: data.comment_id || Date.now(),
            author_id: this.currentUser.id,
            user_id: this.user.id,
            rating: this.reviewForm.rating,
            text: this.reviewForm.text.trim(),
            created_at: new Date().toISOString()
          };
          
          this.reviews.unshift(newReview);
          
          // Обновляем рейтинг пользователя
          this.updateUserRating();
          
          this.showNotification('Отзыв успешно добавлен', 'success');
          this.closeReviewModal();
        } else {
          throw new Error(data.message || 'Ошибка при добавлении отзыва');
        }
        
      } catch (error) {
        console.error('Ошибка при отправке отзыва:', error);
        this.showNotification(error.message || 'Ошибка при добавлении отзыва', 'error');
      } finally {
        this.reviewSubmitting = false;
      }
    },
    
    // Может ли пользователь удалить отзыв
    canDeleteReview(review) {
      if (!review || !review.author_id) return false;
      return this.currentUser && (
        this.currentUser.id === review.author_id ||
        this.currentUser.role === 'admin'
      );
    },
    
    // Удалить отзыв
    async deleteReview(reviewId) {
      if (!confirm('Вы уверены, что хотите удалить этот отзыв?')) {
        return;
      }
      
      const token = localStorage.getItem('token');
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/comments/destroy/${reviewId}`, {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
          this.reviews = this.reviews.filter(r => r.id !== reviewId);
          
          // Обновляем рейтинг пользователя после удаления
          this.updateUserRating();
          
          this.showNotification('Отзыв удален', 'success');
        } else {
          throw new Error(data.message || 'Ошибка при удалении отзыва');
        }
        
      } catch (error) {
        console.error('Ошибка при удалении отзыва:', error);
        this.showNotification(error.message || 'Ошибка при удалении отзыва', 'error');
      }
    },
    
    goBack() {
      this.$router.push('/freelancers');
    },
    
    contactUser() {
      if (!this.currentUser) {
        this.showNotification('Необходимо авторизоваться', 'error');
        this.$router.push('/login?redirect=' + encodeURIComponent(this.$route.fullPath));
        return;
      }
      this.$router.push(`/my-chats?user_id=${this.user.id}`);
    },
    
    normalizeUserRole(role) {
      if (role === null || role === undefined || role === '') return null;
      const r = String(role).trim().toLowerCase();
      if (['freelancer', 'фрилансер', '2'].includes(r) || role === 2) return 'freelancer';
      if (['customer', 'заказчик', '1'].includes(r) || role === 1) return 'customer';
      if (['admin', 'администратор', '3'].includes(r) || role === 3) return 'admin';
      return null;
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

    telegramHref(value) {
      const v = String(value || '').trim();
      if (!v) return '#';
      if (/^https?:\/\//i.test(v)) return v;
      return 'https://t.me/' + v.replace(/^@/, '');
    },

    telegramHandle(value) {
      const v = String(value || '').trim();
      if (/^https?:\/\//i.test(v)) {
        const parts = v.replace(/\/+$/, '').split('/');
        return '@' + parts[parts.length - 1];
      }
      return '@' + v.replace(/^@/, '');
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
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
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
    
    showNotification() {
      // Здесь можно добавить красивые toast-уведомления
      return;
    }
  },
  
  watch: {
    // Следим за изменениями отзывов для обновления рейтинга
    reviews: {
      handler() {
        this.updateUserRating();
      },
      deep: true
    }
  }
}
</script>

<style scoped>
/* Все стили остаются без изменений */
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
  display: inline-flex;
  align-items: center;
  gap: 8px;
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
  overflow: hidden;
}

.avatar-large img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  display: block;
}

.rating-badge {
  position: absolute;
  bottom: 5px;
  right: 5px;
  background: linear-gradient(135deg, var(--blue-soft), var(--blue-light));
  color: #FFFFFF;
  padding: 6px 12px;
  border-radius: 9999px;
  font-size: 1rem;
  font-weight: 600;
  z-index: 3;
  border: 1px solid rgba(168, 209, 255, 0.4);
  box-shadow: 0 2px 12px var(--rating-glow);
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
  display: inline-flex;
  align-items: center;
}

.back-button svg,
.button-icon svg,
.write-review-button svg {
  display: block;
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
.reviews-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 20px;
}

.reviews-filter {
  display: flex;
  align-items: center;
  gap: 10px;
}

.filter-label {
  color: #A8D1FF;
  font-size: 0.95rem;
}

.filter-select {
  padding: 8px 16px;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 12px;
  color: #FFFFFF;
  font-size: 0.95rem;
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.4);
}

.filter-select option {
  background: #0A1A2A;
  color: #FFFFFF;
}

.reviews-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  padding: 40px;
  color: #F0F8FF;
}

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
  object-fit: cover;
  flex-shrink: 0;
  border: 2px solid rgba(168, 209, 255, 0.25);
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
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
  align-items: center;
  gap: 8px;
}

.star {
  color: var(--rating-empty);
  font-size: 1.2rem;
}

.star.filled {
  color: var(--rating);
  text-shadow: 0 0 8px var(--rating-glow);
}

.rating-value {
  color: #A8D1FF;
  font-size: 0.95rem;
  font-weight: 500;
}

.review-text {
  color: #F0F8FF;
  line-height: 1.8;
  font-size: 1rem;
}

.review-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 15px;
}

.delete-review-button {
  padding: 6px 12px;
  background: rgba(231, 76, 60, 0.1);
  border: 1px solid rgba(231, 76, 60, 0.3);
  border-radius: 9999px;
  color: #e74c3c;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 5px;
}

.delete-review-button:hover {
  background: rgba(231, 76, 60, 0.2);
  border-color: rgba(231, 76, 60, 0.5);
}

.no-reviews {
  text-align: center;
  padding: 40px;
  color: #F0F8FF;
  opacity: 0.8;
}

.write-review-button {
  margin-top: 20px;
  padding: 12px 24px;
  background: rgba(10, 77, 140, 0.3);
  border: 1px solid rgba(168, 209, 255, 0.3);
  border-radius: 9999px;
  color: #FFFFFF;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.write-review-button:hover {
  background: rgba(10, 77, 140, 0.5);
  border-color: rgba(168, 209, 255, 0.5);
}

/* Модальное окно */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(8, 51, 88, 0.8);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-container {
  max-width: 560px;
  width: 90%;
  max-height: 85vh;
  overflow-y: auto;
  padding: 30px;
  background: rgba(10, 77, 140, 0.3);
  border: 1px solid rgba(168, 209, 255, 0.2);
  animation: modalFadeIn 0.3s ease;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0;
}

.modal-close {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  color: #FFFFFF;
  font-size: 1.4rem;
  line-height: 1;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.modal-close:hover {
  background: rgba(231, 76, 60, 0.3);
  border-color: rgba(231, 76, 60, 0.4);
  transform: rotate(90deg);
}

.modal-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  color: #F0F8FF;
  font-size: 0.95rem;
  font-weight: 500;
}

.required {
  color: #e74c3c;
  margin-left: 4px;
}

.rating-input {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 10px 0;
}

.rating-star {
  font-size: 2rem;
  color: var(--rating-empty);
  cursor: pointer;
  transition: all 0.2s ease;
}

.rating-star.active {
  color: var(--rating);
  text-shadow: 0 0 15px var(--rating-glow);
}

.rating-star:hover {
  transform: scale(1.2);
}

.rating-text {
  color: #FFFFFF;
  font-size: 1rem;
  font-weight: 600;
  margin-left: 10px;
}

.form-input {
  padding: 12px 16px;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 12px;
  color: #FFFFFF;
  font-size: 1rem;
  transition: all 0.3s ease;
  width: 100%;
  font-family: inherit;
}

.form-input:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.4);
  background: rgba(10, 77, 140, 0.3);
}

textarea.form-input {
  resize: vertical;
  min-height: 120px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  margin-top: 28px;
}

.modal-button {
  padding: 12px 30px;
  border-radius: 9999px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(168, 209, 255, 0.3);
}

.modal-button.cancel {
  background: rgba(149, 165, 166, 0.2);
  color: #95a5a6;
}

.modal-button.cancel:hover {
  background: rgba(149, 165, 166, 0.3);
  transform: translateY(-2px);
}

.modal-button.submit {
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  color: #FFFFFF;
  border: 1px solid rgba(168, 209, 255, 0.3);
}

.modal-button.submit:hover:not(:disabled) {
  background: linear-gradient(135deg, #1A5D9C, #2A7BC3);
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(10, 77, 140, 0.4);
}

.modal-button.submit:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.button-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-right: 8px;
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

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.loader p {
  color: #F0F8FF;
  font-size: 1.1rem;
}

.loader-sm {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.not-found-state {
  padding: 80px 40px;
  text-align: center;
  max-width: 500px;
  margin: 60px auto;
}

.not-found-icon {
  margin-bottom: 20px;
  color: var(--text-secondary);
  filter: drop-shadow(0 10px 20px rgba(168, 209, 255, 0.2));
}

.not-found-icon svg {
  display: inline-block;
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
  
  .reviews-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .reviews-filter {
    width: 100%;
  }
  
  .filter-select {
    flex: 1;
  }
  
  .rating-input {
    justify-content: center;
  }
  
  .modal-actions {
    flex-direction: column;
  }
  
  .modal-button {
    width: 100%;
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