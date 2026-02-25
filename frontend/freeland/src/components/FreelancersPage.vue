<!-- resources/js/components/FreelancersPage.vue -->
<template>
  <div class="freelancers-page">
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
      <!-- Заголовок страницы -->
      <div class="freelancers-header ios-glass">
        <h1 class="freelancers-title">Наши фрилансеры</h1>
        <p class="freelancers-subtitle">Профессионалы своего дела готовые помочь вам</p>
        
        <!-- Фильтры и поиск -->
        <div class="filters-wrapper">
          <div class="search-box">
            <span class="search-icon">🔍</span>
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Поиск фрилансеров..."
              class="search-input"
              @input="handleSearch"
            >
          </div>
          
          <div class="filter-group">
            <select v-model="selectedCategory" class="filter-select ios-glass">
              <option value="">Все категории</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            
            <select v-model="sortBy" class="filter-select ios-glass">
              <option value="rating_desc">По рейтингу (высокий)</option>
              <option value="rating_asc">По рейтингу (низкий)</option>
              <option value="name_asc">По имени (А-Я)</option>
              <option value="name_desc">По имени (Я-А)</option>
              <option value="newest">Сначала новые</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Состояние загрузки -->
      <div v-if="loading" class="loading-state">
        <div class="loader ios-glass">
          <div class="loader-spinner"></div>
          <p>Загрузка фрилансеров...</p>
        </div>
      </div>

      <!-- Сетка фрилансеров -->
      <div v-else-if="filteredFreelancers.length > 0" class="freelancers-grid">
        <div 
          v-for="freelancer in filteredFreelancers" 
          :key="freelancer.id" 
          class="freelancer-card-wrapper"
          @click="openFreelancerProfile(freelancer.id)"
        >
          <div class="freelancer-card ios-glass">
            <div class="card-glow"></div>
            <div class="card-shine"></div>
            
            <!-- Аватар с рейтингом -->
            <div class="avatar-container">
              <div class="avatar-ring"></div>
              <div class="avatar">
                {{ getInitials(freelancer.full_name || freelancer.name || freelancer.login) }}
              </div>
              <div class="rating-badge" v-if="freelancer.rating">
                {{ freelancer.rating }} ★
              </div>
            </div>
            
            <!-- Информация о фрилансере -->
            <h3 class="freelancer-name">
              {{ freelancer.full_name || freelancer.name || freelancer.login || 'Без имени' }}
            </h3>
            
            <div class="freelancer-login" v-if="freelancer.login">
              @{{ freelancer.login }}
            </div>
            
            <!-- Специализация (если есть) -->
            <div class="specialization" v-if="freelancer.specialization">
              {{ freelancer.specialization }}
            </div>
            
            <!-- Категории (если есть) -->
            <div class="categories" v-if="freelancer.categories && freelancer.categories.length">
              <span 
                v-for="category in freelancer.categories.slice(0, 3)" 
                :key="category.id"
                class="category-tag"
              >
                {{ category.name }}
              </span>
              <span v-if="freelancer.categories.length > 3" class="category-tag more">
                +{{ freelancer.categories.length - 3 }}
              </span>
            </div>
            
            <!-- Статистика -->
            <div class="stats-grid">
              <div class="stat-item">
                <span class="stat-value">{{ freelancer.completed_projects || 0 }}</span>
                <span class="stat-label">Проектов</span>
              </div>
              <div class="stat-item">
                <span class="stat-value">{{ freelancer.rating || '0.0' }}</span>
                <span class="stat-label">Рейтинг</span>
              </div>
              <div class="stat-item">
                <span class="stat-value">{{ freelancer.experience || '0' }}</span>
                <span class="stat-label">Лет опыта</span>
              </div>
            </div>
            
            <!-- Навыки -->
            <div class="skills" v-if="freelancer.skills && freelancer.skills.length">
              <span 
                v-for="skill in freelancer.skills.slice(0, 4)" 
                :key="skill"
                class="skill-tag"
              >
                {{ skill }}
              </span>
              <span v-if="freelancer.skills.length > 4" class="skill-tag more">
                +{{ freelancer.skills.length - 4 }}
              </span>
            </div>
            
            <!-- Цена и кнопка -->
            <div class="freelancer-footer">
              <div class="price-info">
                <span class="price-label">От</span>
                <span class="price-value">{{ formatPrice(freelancer.hourly_rate || freelancer.min_price) }}</span>
                <span class="price-period">/час</span>
              </div>
              
              <button class="contact-button" @click.stop="contactFreelancer(freelancer.id)">
                Связаться
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Пустое состояние -->
      <div v-else class="empty-state ios-glass">
        <div class="empty-icon">👥</div>
        <h3 class="empty-title">Фрилансеры не найдены</h3>
        <p class="empty-text">Попробуйте изменить параметры поиска</p>
        <button @click="resetFilters" class="reset-button ios-glass">
          Сбросить фильтры
        </button>
      </div>
    </div>
    <FooterApp />
  </div>
</template>

<script>
import _ from 'lodash';
import FooterApp from '@/elements/FooterApp.vue';
import HeaderMenu from '@/elements/HeaderMenu.vue';

export default {
  name: 'FreelancersPage',
  
  components: {
    HeaderMenu,
    FooterApp
  },
  
  data() {
    return {
      allUsers: [], // Все пользователи
      categories: [],
      loading: true,
      searchQuery: '',
      selectedCategory: '',
      sortBy: 'rating_desc',
      apiBaseUrl: ''
    }
  },
  
  computed: {
    // Фильтруем только фрилансеров
    freelancers() {
      return this.allUsers.filter(user => 
        user.role === 'freelancer' || user.role === 'Фрилансер' || user.role === 2
      );
    },
    
    // Применяем поиск и сортировку
    filteredFreelancers() {
      let result = [...this.freelancers];
      
      // Поиск по имени, логину, специализации
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        result = result.filter(f => 
          (f.full_name && f.full_name.toLowerCase().includes(query)) ||
          (f.name && f.name.toLowerCase().includes(query)) ||
          (f.login && f.login.toLowerCase().includes(query)) ||
          (f.specialization && f.specialization.toLowerCase().includes(query)) ||
          (f.skills && f.skills.some(skill => skill.toLowerCase().includes(query)))
        );
      }
      
      // Фильтр по категории
      if (this.selectedCategory) {
        result = result.filter(f => 
          f.categories && f.categories.some(c => c.id == this.selectedCategory)
        );
      }
      
      // Сортировка
      switch (this.sortBy) {
        case 'rating_desc':
          result.sort((a, b) => (b.rating || 0) - (a.rating || 0));
          break;
        case 'rating_asc':
          result.sort((a, b) => (a.rating || 0) - (b.rating || 0));
          break;
        case 'name_asc':
          result.sort((a, b) => {
            const nameA = (a.full_name || a.name || a.login || '').toLowerCase();
            const nameB = (b.full_name || b.name || b.login || '').toLowerCase();
            return nameA.localeCompare(nameB);
          });
          break;
        case 'name_desc':
          result.sort((a, b) => {
            const nameA = (a.full_name || a.name || a.login || '').toLowerCase();
            const nameB = (b.full_name || b.name || b.login || '').toLowerCase();
            return nameB.localeCompare(nameA);
          });
          break;
        case 'newest':
          result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
          break;
      }
      
      return result;
    }
  },
  
  created() {
    this.fetchUsers();
    this.fetchCategories();
    this.initParticles();
  },
  
  methods: {
    // Получение всех пользователей
    async fetchUsers() {
      this.loading = true;
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/users`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        this.allUsers = data;
        console.log('Загружены пользователи:', this.allUsers);
        console.log('Фрилансеры:', this.freelancers);
        
      } catch (error) {
        console.error('Ошибка при загрузке пользователей:', error);
        this.showNotification('Ошибка при загрузке фрилансеров', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    // Получение категорий
    async fetchCategories() {
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/categories`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        this.categories = data;
      } catch (error) {
        console.error('Ошибка при загрузке категорий:', error);
      }
    },
    
    // Debounced поиск
    handleSearch: _.debounce(function() {
      // Фильтрация происходит в computed, ничего не нужно делать
    }, 500),
    
    // Сброс фильтров
    resetFilters() {
      this.searchQuery = '';
      this.selectedCategory = '';
      this.sortBy = 'rating_desc';
    },
    
    // Открыть профиль фрилансера
    openFreelancerProfile(id) {
      this.$router.push(`/freelancers/${id}`);
    },
    
    // Связаться с фрилансером
    contactFreelancer(id) {
      this.$router.push(`/messages?user=${id}`);
    },
    
    // Форматирование цены
    formatPrice(price) {
      if (!price) return '0';
      return new Intl.NumberFormat('ru-RU').format(price);
    },
    
    // Инициалы пользователя
    getInitials(name) {
      if (!name) return '?';
      return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    },
    
    // Инициализация частиц
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
    
    // Уведомление
    showNotification(message, type = 'info') {
      console.log(`[${type}] ${message}`);
    }
  },
  
  watch: {
    selectedCategory() {
      // Фильтрация происходит в computed
    },
    sortBy() {
      // Сортировка происходит в computed
    }
  }
}
</script>

<style scoped>
.freelancers-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  position: relative;
  padding: 40px 0 80px;
  padding-bottom: 0; 
}
.container {
  flex: 1 0 auto;          /* ← ключевой момент */
  display: flex;
  flex-direction: column;
  /* padding: 0 20px; уже есть — можно оставить */
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 20;
  width: 100%;
}

.freelancers-header {
  padding: 60px 40px;
  margin-bottom: 60px;
  text-align: center;
}

.freelancers-title {
  font-size: 3.2rem;
  font-weight: 700;
  margin-bottom: 16px;
  color: var(--text-primary);
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.freelancers-subtitle {
  font-size: 1.4rem;
  color: var(--text-secondary);
  margin-bottom: 40px;
}

.filters-wrapper {
  max-width: 900px;
  margin: 0 auto;
}

.search-box {
  position: relative;
  margin-bottom: 20px;
}

.search-icon {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.2rem;
  color: var(--text-tertiary);
  z-index: 2;
}

.search-input {
  width: 100%;
  padding: 18px 20px 18px 50px;
  font-size: 1.1rem;
  background: rgba(10, 77, 140, 0.15);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 9999px;
  color: var(--text-primary);
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.4);
  background: rgba(10, 77, 140, 0.25);
  box-shadow: 0 0 30px rgba(168, 209, 255, 0.2);
}

.search-input::placeholder {
  color: var(--text-tertiary);
  opacity: 0.7;
}

.filter-group {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  justify-content: center;
}

.filter-select {
  padding: 14px 30px;
  font-size: 1rem;
  background: rgba(10, 77, 140, 0.15);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 9999px;
  color: var(--text-primary);
  cursor: pointer;
  min-width: 200px;
  transition: all 0.3s ease;
}

.filter-select:hover {
  background: rgba(10, 77, 140, 0.25);
  border-color: rgba(168, 209, 255, 0.3);
}

.filter-select:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.4);
}

.filter-select option {
  background: #0A1428;
  color: var(--text-primary);
}

.freelancers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 30px;
  margin-bottom: 60px;
}

.freelancer-card-wrapper {
  cursor: pointer;
  transition: transform 0.3s ease;
}

.freelancer-card-wrapper:hover {
  transform: translateY(-5px);
}

.freelancer-card {
  padding: 30px 25px;
  height: 100%;
  display: flex;
  flex-direction: column;
  position: relative;
  transition: all 0.3s ease;
}

.freelancer-card:hover {
  border-color: rgba(168, 209, 255, 0.4);
  box-shadow: 0 30px 60px rgba(8, 51, 88, 0.6);
}

.avatar-container {
  position: relative;
  width: 120px;
  height: 120px;
  margin: 0 auto 20px;
}

.avatar-ring {
  position: absolute;
  top: -4px;
  left: -4px;
  right: -4px;
  bottom: -4px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(168, 209, 255, 0.6), #FFFFFF, rgba(168, 209, 255, 0.6));
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
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  font-weight: 700;
  color: #FFFFFF;
  position: relative;
  z-index: 2;
  box-shadow: 0 10px 30px rgba(8, 51, 88, 0.4);
}

.rating-badge {
  position: absolute;
  bottom: 0;
  right: 0;
  background: #F0B90B;
  color: #000000;
  padding: 4px 8px;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 600;
  z-index: 3;
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.freelancer-name {
  font-size: 1.4rem;
  font-weight: 600;
  margin-bottom: 5px;
  color: #FFFFFF;
  text-align: center;
}

.freelancer-login {
  color: #A8D1FF;
  font-size: 0.95rem;
  margin-bottom: 10px;
  text-align: center;
}

.specialization {
  color: #F0F8FF;
  font-size: 1rem;
  margin-bottom: 15px;
  text-align: center;
  font-style: italic;
}

.categories {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: center;
  margin-bottom: 20px;
}

.category-tag {
  padding: 4px 12px;
  background: rgba(10, 77, 140, 0.3);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 9999px;
  font-size: 0.85rem;
  color: #F0F8FF;
}

.category-tag.more {
  background: rgba(168, 209, 255, 0.1);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  padding: 15px 0;
  border-top: 1px solid rgba(168, 209, 255, 0.1);
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
  margin-bottom: 20px;
}

.stat-item {
  text-align: center;
}

.stat-value {
  display: block;
  font-size: 1.3rem;
  font-weight: 700;
  color: #FFFFFF;
  line-height: 1.2;
}

.stat-label {
  display: block;
  font-size: 0.8rem;
  color: #A8D1FF;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.skills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: center;
  margin-bottom: 20px;
}

.skill-tag {
  padding: 4px 10px;
  background: rgba(26, 107, 179, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.15);
  border-radius: 9999px;
  font-size: 0.8rem;
  color: #F0F8FF;
}

.skill-tag.more {
  background: rgba(168, 209, 255, 0.05);
}

.freelancer-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: auto;
}

.price-info {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.price-label {
  color: #A8D1FF;
  font-size: 0.85rem;
}

.price-value {
  font-size: 1.3rem;
  font-weight: 700;
  color: #FFFFFF;
}

.price-period {
  color: #A8D1FF;
  font-size: 0.8rem;
}

.contact-button {
  padding: 10px 20px;
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  border: none;
  border-radius: 9999px;
  color: #FFFFFF;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(168, 209, 255, 0.3);
}

.contact-button:hover {
  background: linear-gradient(135deg, #1A6BB3, #2A7FC9);
  transform: scale(1.05);
  box-shadow: 0 10px 20px rgba(8, 51, 88, 0.4);
}

.loading-state {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
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

.empty-state {
  padding: 80px 40px;
  text-align: center;
  max-width: 500px;
  margin: 60px auto;
}

.empty-icon {
  font-size: 5rem;
  margin-bottom: 20px;
  filter: drop-shadow(0 10px 20px rgba(168, 209, 255, 0.2));
}

.empty-title {
  font-size: 2rem;
  font-weight: 600;
  margin-bottom: 15px;
  color: #FFFFFF;
}

.empty-text {
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

@media (max-width: 768px) {
  .freelancers-header {
    padding: 40px 20px;
  }
  
  .freelancers-title {
    font-size: 2.5rem;
  }
  
  .freelancers-subtitle {
    font-size: 1.2rem;
  }
  
  .filter-group {
    flex-direction: column;
  }
  
  .filter-select {
    width: 100%;
  }
  
  .freelancers-grid {
    grid-template-columns: 1fr;
  }
  
  .empty-state {
    padding: 60px 20px;
  }
  
  .empty-title {
    font-size: 1.8rem;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 15px;
  }
  
  .freelancers-title {
    font-size: 2rem;
  }
  
  .avatar-container {
    width: 100px;
    height: 100px;
  }
  
  .avatar {
    font-size: 2rem;
  }
  
  .freelancer-name {
    font-size: 1.2rem;
  }
  
  .stats-grid {
    gap: 5px;
  }
  
  .stat-value {
    font-size: 1.1rem;
  }
  
  .price-value {
    font-size: 1.1rem;
  }
}
</style>