<!-- resources/js/components/ProjectsPage.vue -->
<template>
  <div class="projects-page">
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
      <div class="projects-header ios-glass">
        <h1 class="projects-title">Доступные проекты</h1>
        <p class="projects-subtitle">Найдите идеальный проект для ваших навыков</p>
        
        <!-- Фильтры и поиск -->
        <div class="filters-wrapper">
          <div class="search-box">
            <span class="search-icon">🔍</span>
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Поиск проектов..."
              class="search-input"
              @input="handleSearch"
            >
          </div>
          
          <div class="filter-group">
            <select v-model="selectedCategory" class="filter-select ios-glass" @change="fetchProjects">
              <option value="">Все категории</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Состояние загрузки -->
      <div v-if="loading" class="loading-state">
        <div class="loader ios-glass">
          <div class="loader-spinner"></div>
          <p>Загрузка проектов...</p>
        </div>
      </div>

      <!-- Сетка проектов -->
      <div v-else-if="projects.length > 0" class="projects-grid">
        <div 
          v-for="project in projects" 
          :key="project.id" 
          class="project-card-wrapper"
          @click="openProject(project.id)"
        >
          <div class="project-card ios-glass">
            <div class="card-glow"></div>
            <div class="card-shine"></div>
            
            <div class="project-status" :class="project.status">
              {{ getStatusText(project.status) }}
            </div>
            
            <h3 class="project-title">{{ project.title }}</h3>
            
            <p class="project-description">{{ truncateDescription(project.description) }}</p>
            
            <div class="project-meta">
              <div class="meta-item">
                <span class="meta-icon">💰</span>
                <span class="meta-value">{{ formatBudget(project.budget) }}</span>
              </div>
              
              <div class="meta-item" v-if="project.deadline">
                <span class="meta-icon">⏰</span>
                <span class="meta-value">{{ formatDeadline(project.deadline) }}</span>
              </div>
              
              <div class="meta-item">
                <span class="meta-icon">📁</span>
                <span class="meta-value">{{ getCategoryName(project.category_id) }}</span>
              </div>
            </div>
            
            <div class="project-footer">
              <!-- Отображаем информацию о заказчике -->
              <div class="customer-info">
                <div v-if="customers[project.customer_id]" class="customer-avatar">
                  {{ getInitials(customers[project.customer_id].name) }}
                </div>
                <div v-else class="customer-avatar loading-avatar">
                  <div class="avatar-spinner"></div>
                </div>
                
                <span class="customer-name">
                  <template v-if="customers[project.customer_id]">
                    {{ customers[project.customer_id].name }}
                  </template>
                  <template v-else-if="loadingCustomers[project.customer_id]">
                    Загрузка...
                  </template>
                  <template v-else>
                    Заказчик
                  </template>
                </span>
              </div>
              
              <button class="view-button" @click.stop="openProject(project.id)">
                Подробнее
                <span class="button-arrow">→</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Пустое состояние -->
      <div v-else class="empty-state ios-glass">
        <div class="empty-icon">📋</div>
        <h3 class="empty-title">Проекты не найдены</h3>
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
  name: 'ProjectsPage',
   components: {
    HeaderMenu,
    FooterApp
  },
  data() {
    return {
      projects: [],
      categories: [],
      customers: {}, // Объект для хранения данных заказчиков по их ID
      loadingCustomers: {}, // Объект для отслеживания загрузки каждого заказчика
      loading: true,
      searchQuery: '',
      selectedCategory: '',
      apiBaseUrl: 'http://127.0.0.1:8000'
    }
  },
  
  created() {
    this.fetchProjects();
    this.fetchCategories();
    this.initParticles();
  },
  
  methods: {
    // Получение проектов
    async fetchProjects() {
      this.loading = true;
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/projects`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        // Фильтрация на клиенте
        let filteredProjects = data;
        
        if (this.searchQuery) {
          const query = this.searchQuery.toLowerCase();
          filteredProjects = filteredProjects.filter(project => 
            project.title.toLowerCase().includes(query) || 
            (project.description && project.description.toLowerCase().includes(query))
          );
        }
        
        if (this.selectedCategory) {
          filteredProjects = filteredProjects.filter(project => 
            project.category_id == this.selectedCategory
          );
        }
        
        this.projects = filteredProjects;
        
        // После получения проектов, загружаем данные заказчиков
        this.loadCustomersData();
        
      } catch (error) {
        console.error('Ошибка при загрузке проектов:', error);
        this.showNotification('Ошибка при загрузке проектов', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    // Загрузка данных всех заказчиков
    loadCustomersData() {
      // Собираем уникальные ID заказчиков из проектов
      const customerIds = [...new Set(this.projects.map(p => p.customer_id).filter(id => id))];
      
      // Загружаем данные для каждого заказчика
      customerIds.forEach(customerId => {
        // Проверяем, не загружаем ли мы уже этого заказчика и нет ли уже его данных
        if (!this.customers[customerId] && !this.loadingCustomers[customerId]) {
          this.fetchCustomerData(customerId);
        }
      });
    },
    
    // Получение данных конкретного заказчика
    async fetchCustomerData(customerId) {
      // Устанавливаем флаг загрузки для этого заказчика (для Vue 2 используем this.$set или прямое присвоение)
      this.loadingCustomers = {
        ...this.loadingCustomers,
        [customerId]: true
      };
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/users/${customerId}`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const userData = await response.json();
        
        // Сохраняем данные заказчика (для Vue 2 создаем новый объект)
        this.customers = {
          ...this.customers,
          [customerId]: userData
        };
        
      } catch (error) {
        console.error(`Ошибка при загрузке данных заказчика ${customerId}:`, error);
        
        // В случае ошибки создаем базовый объект с именем
        this.customers = {
          ...this.customers,
          [customerId]: {
            id: customerId,
            name: 'Заказчик',
            email: ''
          }
        };
        
      } finally {
        // Убираем флаг загрузки
        this.loadingCustomers = {
          ...this.loadingCustomers,
          [customerId]: false
        };
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
        this.showNotification('Ошибка при загрузке категорий', 'error');
      }
    },
    
    // Получение названия категории по ID
    getCategoryName(categoryId) {
      if (!categoryId) return 'Без категории';
      const category = this.categories.find(c => c.id === categoryId);
      return category ? category.name : 'Без категории';
    },
    
    // Debounced поиск
    handleSearch: _.debounce(function() {
      this.fetchProjects();
    }, 500),
    
    // Сброс фильтров
    resetFilters() {
      this.searchQuery = '';
      this.selectedCategory = '';
      this.fetchProjects();
    },
    
    // Открыть проект
    openProject(id) {
      this.$router.push(`/projects/${id}`);
    },
    
    // Форматирование бюджета
    formatBudget(budget) {
      if (!budget) return 'Не указан';
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 0
      }).format(budget);
    },
    
    // Форматирование дедлайна
    formatDeadline(deadline) {
      if (!deadline) return 'Не указан';
      const date = new Date(deadline);
      const now = new Date();
      const diffTime = date - now;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      if (diffDays < 0) return 'Просрочен';
      if (diffDays === 0) return 'Сегодня';
      if (diffDays === 1) return 'Завтра';
      if (diffDays < 7) return `Осталось ${diffDays} дней`;
      return date.toLocaleDateString('ru-RU');
    },
    
    // Текст статуса
    getStatusText(status) {
      const statusMap = {
        'open': 'Открыт',
        'in_progress': 'В работе',
        'completed': 'Завершен',
        'cancelled': 'Отменен'
      };
      return statusMap[status] || status || 'Открыт';
    },
    
    // Обрезка описания
    truncateDescription(description) {
      if (!description) return 'Нет описания';
      return description.length > 120 
        ? description.substring(0, 120) + '...' 
        : description;
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
      // Здесь можно добавить toast-уведомления
    }
  },
  
  watch: {
    selectedCategory() {
      this.fetchProjects();
    }
  }
}
</script>

<style scoped>
.projects-page {
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

.projects-header {
  padding: 60px 40px;
  margin-bottom: 60px;
  text-align: center;
}

.projects-title {
  font-size: 3.2rem;
  font-weight: 700;
  margin-bottom: 16px;
  color: var(--text-primary);
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.projects-subtitle {
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

.projects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 30px;
  margin-bottom: 60px;
}

.project-card-wrapper {
  cursor: pointer;
  transition: transform 0.3s ease;
}

.project-card-wrapper:hover {
  transform: translateY(-5px);
}

.project-card {
  padding: 30px;
  height: 100%;
  display: flex;
  flex-direction: column;
  position: relative;
  transition: all 0.3s ease;
}

.project-card:hover {
  border-color: rgba(168, 209, 255, 0.4);
  box-shadow: 0 30px 60px rgba(8, 51, 88, 0.6);
}

.project-status {
  position: absolute;
  top: 20px;
  right: 20px;
  padding: 6px 16px;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background: rgba(10, 77, 140, 0.3);
  backdrop-filter: blur(5px);
  border: 1px solid rgba(168, 209, 255, 0.2);
  color: var(--text-secondary);
}

.project-status.open {
  background: rgba(39, 174, 96, 0.2);
  border-color: rgba(39, 174, 96, 0.4);
  color: #2ecc71;
}

.project-status.in_progress {
  background: rgba(241, 196, 15, 0.2);
  border-color: rgba(241, 196, 15, 0.4);
  color: #f1c40f;
}

.project-status.completed {
  background: rgba(52, 152, 219, 0.2);
  border-color: rgba(52, 152, 219, 0.4);
  color: #3498db;
}

.project-title {
  font-size: 1.6rem;
  font-weight: 600;
  margin: 10px 0 15px;
  color: var(--text-primary);
  padding-right: 80px;
}

.project-description {
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 25px;
  flex-grow: 1;
}

.project-meta {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 15px;
  margin-bottom: 25px;
  padding: 15px 0;
  border-top: 1px solid rgba(168, 209, 255, 0.1);
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.meta-icon {
  font-size: 1.2rem;
  filter: drop-shadow(0 2px 5px rgba(168, 209, 255, 0.3));
}

.meta-value {
  color: var(--text-tertiary);
  font-size: 0.95rem;
  font-weight: 500;
}

.project-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.customer-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.customer-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1rem;
  color: #FFFFFF;
  border: 2px solid rgba(168, 209, 255, 0.3);
}

.loading-avatar {
  background: linear-gradient(135deg, #1A4D6C, #2A5F8C);
  animation: pulse 1.5s ease-in-out infinite;
}

.avatar-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(168, 209, 255, 0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes pulse {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 1; }
}

.customer-name {
  color: #F0F8FF;
  font-size: 0.95rem;
}

.view-button {
  padding: 10px 20px;
  background: rgba(168, 209, 255, 0.1);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 9999px;
  color: #FFFFFF;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 5px;
}

.view-button:hover {
  background: rgba(168, 209, 255, 0.2);
  border-color: rgba(168, 209, 255, 0.4);
  transform: translateX(3px);
}

.button-arrow {
  transition: transform 0.3s ease;
}

.view-button:hover .button-arrow {
  transform: translateX(3px);
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
  .projects-header {
    padding: 40px 20px;
  }
  
  .projects-title {
    font-size: 2.5rem;
  }
  
  .projects-subtitle {
    font-size: 1.2rem;
  }
  
  .filter-group {
    flex-direction: column;
  }
  
  .filter-select {
    width: 100%;
  }
  
  .projects-grid {
    grid-template-columns: 1fr;
  }
  
  .project-meta {
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
  
  .projects-title {
    font-size: 2rem;
  }
  
  .project-title {
    font-size: 1.4rem;
  }
  
  .customer-info {
    max-width: 150px;
  }
  
  .customer-name {
    font-size: 0.85rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}
</style>