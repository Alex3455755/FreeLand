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
        
        <!-- Кнопка добавления проекта для заказчиков -->
        <div v-if="isCustomer" class="add-project-wrapper">
          <button @click="openAddProjectModal" class="add-project-button ios-glass">
            <span class="add-icon">+</span>
            Добавить проект
          </button>
        </div>
        
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

    <!-- Модальное окно добавления проекта -->
    <div v-if="showAddProjectModal" class="modal-overlay" @click.self="closeAddProjectModal">
      <div class="modal-container ios-glass">
        <div class="modal-header">
          <h2 class="modal-title">Создание нового проекта</h2>
          <button @click="closeAddProjectModal" class="modal-close">×</button>
        </div>
        
        <form @submit.prevent="submitNewProject" class="modal-form">
          <div class="form-group">
            <label>Название проекта <span class="required">*</span></label>
            <input 
              v-model="newProject.title" 
              type="text" 
              class="form-input ios-glass" 
              required
              placeholder="Введите название проекта"
            >
          </div>
          
          <div class="form-group">
            <label>Описание</label>
            <textarea 
              v-model="newProject.description" 
              class="form-input ios-glass" 
              rows="4"
              placeholder="Опишите ваш проект"
            ></textarea>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Бюджет (₽)</label>
              <input 
                v-model.number="newProject.budget" 
                type="number" 
                class="form-input ios-glass"
                placeholder="Например: 50000"
                min="0"
              >
            </div>
            
            <div class="form-group">
              <label>Дедлайн</label>
              <input 
                v-model="newProject.deadline" 
                type="date" 
                class="form-input ios-glass"
                :min="today"
              >
            </div>
          </div>
          
          <div class="form-group">
            <label>Категория</label>
            <select v-model="newProject.category_id" class="form-input ios-glass">
              <option value="">Выберите категорию</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Статус</label>
            <select v-model="newProject.status" class="form-input ios-glass">
              <option value="open">Открыт</option>
              <option value="in_progress">В работе</option>
              <option value="completed">Завершен</option>
            </select>
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="closeAddProjectModal" class="modal-button cancel">
              Отмена
            </button>
            <button type="submit" class="modal-button submit" :disabled="submitting">
              <span v-if="submitting" class="button-spinner"></span>
              {{ submitting ? 'Создание...' : 'Создать проект' }}
            </button>
          </div>
        </form>
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
      customers: {},
      users: [], // Для списка всех пользователей (если нужно)
      loadingCustomers: {},
      loading: true,
      searchQuery: '',
      selectedCategory: '',
      apiBaseUrl: '',
      
      // Данные пользователя
      user: null,
      userLoading: true,
      
      // Модальное окно добавления проекта
      showAddProjectModal: false,
      submitting: false,
      newProject: {
        title: '',
        description: '',
        budget: null,
        deadline: '',
        status: 'open',
        category_id: '',
        customer_id: null
      }
    }
  },
  
  computed: {
    // Проверка, авторизован ли пользователь и является ли заказчиком
    isCustomer() {
      return this.user && this.user.role === 'customer';
    },
    
    // Сегодняшняя дата для min атрибута в input date
    today() {
      const today = new Date();
      const yyyy = today.getFullYear();
      const mm = String(today.getMonth() + 1).padStart(2, '0');
      const dd = String(today.getDate()).padStart(2, '0');
      return `${yyyy}-${mm}-${dd}`;
    }
  },
  
  created() {
    this.fetchProjects();
    this.fetchCategories();
    this.fetchCurrentUser();
    this.initParticles();
  },
  
  methods: {
    // Получение текущего пользователя
    async fetchCurrentUser() {
      this.userLoading = true;
      const token = localStorage.getItem('token');
      
      if (!token) {
        this.user = null;
        this.userLoading = false;
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
          this.user = data.user;
        } else {
          localStorage.removeItem('token');
          localStorage.removeItem('user');
          this.user = null;
        }
      } catch (error) {
        console.error('Ошибка получения пользователя:', error);
        this.user = null;
      } finally {
        this.userLoading = false;
      }
    },
    
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
    
    // Получение всех пользователей (если нужно для выбора заказчика в админке)
    async fetchUsers() {
      const token = localStorage.getItem('token');
      if (!token) return;
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/users`, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          this.users = data;
        }
      } catch (error) {
        console.error('Ошибка при загрузке пользователей:', error);
      }
    },
    
    // Загрузка данных всех заказчиков
    loadCustomersData() {
      const customerIds = [...new Set(this.projects.map(p => p.customer_id).filter(id => id))];
      
      customerIds.forEach(customerId => {
        if (!this.customers[customerId] && !this.loadingCustomers[customerId]) {
          this.fetchCustomerData(customerId);
        }
      });
    },
    
    // Получение данных конкретного заказчика
    async fetchCustomerData(customerId) {
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
        
        this.customers = {
          ...this.customers,
          [customerId]: userData
        };
        
      } catch (error) {
        console.error(`Ошибка при загрузке данных заказчика ${customerId}:`, error);
        
        this.customers = {
          ...this.customers,
          [customerId]: {
            id: customerId,
            name: 'Заказчик',
            email: ''
          }
        };
        
      } finally {
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
    
    // Открытие модального окна добавления проекта
    openAddProjectModal() {
      if (!this.isCustomer) {
        this.showNotification('Только заказчики могут создавать проекты', 'error');
        return;
      }
      
      // Сбрасываем форму
      this.newProject = {
        title: '',
        description: '',
        budget: null,
        deadline: '',
        status: 'open',
        category_id: '',
        customer_id: this.user ? this.user.id : null
      };
      
      this.showAddProjectModal = true;
      // Блокируем прокрутку страницы
      document.body.style.overflow = 'hidden';
    },
    
    // Закрытие модального окна
    closeAddProjectModal() {
      this.showAddProjectModal = false;
      // Возвращаем прокрутку
      document.body.style.overflow = '';
    },
    
    // Отправка нового проекта
    async submitNewProject() {
      if (!this.newProject.title) {
        this.showNotification('Введите название проекта', 'error');
        return;
      }
      
      this.submitting = true;
      const token = localStorage.getItem('token');
      
      if (!token) {
        this.showNotification('Необходима авторизация', 'error');
        this.submitting = false;
        return;
      }
      
      // Убеждаемся, что customer_id установлен
      const projectData = {
        ...this.newProject,
        customer_id: this.user.id // Принудительно устанавливаем ID текущего пользователя
      };
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/projects/add`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(projectData)
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
          this.showNotification('Проект успешно создан', 'success');
          this.closeAddProjectModal();
          // Обновляем список проектов
          this.fetchProjects();
        } else {
          throw new Error(data.message || 'Ошибка при создании проекта');
        }
      } catch (error) {
        console.error('Ошибка при создании проекта:', error);
        this.showNotification(error.message || 'Ошибка при создании проекта', 'error');
      } finally {
        this.submitting = false;
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
  position: relative;
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

.add-project-wrapper {
  margin-bottom: 30px;
}

.add-project-button {
  padding: 16px 40px;
  font-size: 1.2rem;
  background: linear-gradient(135deg, rgba(10, 77, 140, 0.3), rgba(26, 107, 179, 0.3));
  border: 1px solid rgba(168, 209, 255, 0.3);
  border-radius: 9999px;
  color: #FFFFFF;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-weight: 600;
}

.add-project-button:hover {
  background: linear-gradient(135deg, rgba(10, 77, 140, 0.5), rgba(26, 107, 179, 0.5));
  border-color: rgba(168, 209, 255, 0.6);
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(168, 209, 255, 0.2);
}

.add-icon {
  font-size: 1.5rem;
  font-weight: 300;
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

/* Стили для модального окна */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-container {
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  padding: 30px;
  border: 1px solid rgba(168, 209, 255, 0.2);
  animation: modalFadeIn 0.3s ease;
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

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.modal-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: #FFFFFF;
}

.modal-close {
  font-size: 2.5rem;
  background: none;
  border: none;
  color: rgba(168, 209, 255, 0.5);
  cursor: pointer;
  transition: all 0.3s ease;
  line-height: 1;
}

.modal-close:hover {
  color: #FFFFFF;
  transform: scale(1.1);
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

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.form-input {
  padding: 12px 16px;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 12px;
  color: #FFFFFF;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.4);
  background: rgba(10, 77, 140, 0.3);
  box-shadow: 0 0 20px rgba(168, 209, 255, 0.1);
}

.form-input::placeholder {
  color: rgba(240, 248, 255, 0.3);
}

textarea.form-input {
  resize: vertical;
  min-height: 100px;
}

select.form-input {
  cursor: pointer;
}

select.form-input option {
  background: #0A1428;
  color: #FFFFFF;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid rgba(168, 209, 255, 0.1);
}

.modal-button {
  padding: 12px 30px;
  border-radius: 9999px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
}

.modal-button.cancel {
  background: rgba(255, 255, 255, 0.1);
  color: #F0F8FF;
  border: 1px solid rgba(168, 209, 255, 0.2);
}

.modal-button.cancel:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(168, 209, 255, 0.3);
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
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .modal-container {
    padding: 20px;
  }
  
  .modal-title {
    font-size: 1.5rem;
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