<!-- resources/js/components/ProjectDetail.vue -->
<template>
  <div class="project-detail-page">
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
    </div>

    <div class="container">
      <div v-if="loading" class="loading-state">
        <div class="loader ios-glass">
          <div class="loader-spinner"></div>
          <p>Загрузка проекта...</p>
        </div>
      </div>

      <div v-else-if="project" class="project-detail ios-glass">
        <button @click="goBack" class="back-button ios-glass">
          ← Назад к проектам
        </button>

        <div class="project-header">
          <h1 class="project-title">{{ project.title }}</h1>
          <div class="project-status" :class="project.status">
            {{ getStatusText(project.status) }}
          </div>
        </div>

        <div class="project-info">
          <div class="info-section">
            <h3>Описание</h3>
            <p>{{ project.description || 'Нет описания' }}</p>
          </div>

          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">💰 Бюджет:</span>
              <span class="info-value">{{ formatBudget(project.budget) }}</span>
            </div>
            
            <div class="info-item" v-if="project.deadline">
              <span class="info-label">⏰ Дедлайн:</span>
              <span class="info-value">{{ formatDate(project.deadline) }}</span>
            </div>
            
            <div class="info-item">
              <span class="info-label">📁 Категория:</span>
              <span class="info-value">{{ getCategoryName(project.category_id) }}</span>
            </div>
            
            <div class="info-item" v-if="project.created_at">
              <span class="info-label">📅 Дата создания:</span>
              <span class="info-value">{{ formatDate(project.created_at) }}</span>
            </div>
          </div>

          <!-- Информация о заказчике -->
          <div class="customer-section">
            <h3>Заказчик</h3>
            <div v-if="customerLoading" class="customer-loading">
              <div class="customer-loading-spinner"></div>
              <span>Загрузка данных заказчика...</span>
            </div>
            <div v-else-if="customer" class="customer-card ios-glass">
              <div class="customer-avatar-large">
                {{ getInitials(customer.full_name || customer.name || customer.login) }}
              </div>
              <div class="customer-details">
                <!-- Полное имя -->
                <div class="customer-name-large">
                  {{ customer.full_name || customer.name || customer.login || 'Без имени' }}
                </div>
                
                <!-- Логин (email) -->
                <div class="customer-login" v-if="customer.login">
                  <span class="info-label">🔑 Логин:</span>
                  <span class="info-value">{{ customer.login }}</span>
                </div>
                
                <!-- Телефон -->
                <div class="customer-phone" v-if="customer.phone">
                  <span class="info-label">📞 Телефон:</span>
                  <span class="info-value">{{ customer.phone }}</span>
                </div>
                
                <!-- Дата регистрации -->
                <div class="customer-joined" v-if="customer.created_at">
                  <span class="info-label">📅 На платформе с:</span>
                  <span class="info-value">{{ formatDate(customer.created_at) }}</span>
                </div>
                
                <!-- Аватар (если есть) -->
                <div class="customer-avatar-info" v-if="customer.avatar">
                  <span class="info-label">🖼️ Аватар:</span>
                  <span class="info-value">{{ customer.avatar }}</span>
                </div>
              </div>
            </div>
            <div v-else class="customer-not-found">
              <p>Информация о заказчике недоступна</p>
            </div>
          </div>

          <!-- Информация об исполнителе (если есть) -->
          <div class="additional-info" v-if="project.freelancer_id">
            <h3>Исполнитель</h3>
            <div v-if="freelancerLoading" class="customer-loading">
              <div class="customer-loading-spinner"></div>
              <span>Загрузка данных исполнителя...</span>
            </div>
            <div v-else-if="freelancer" class="customer-card ios-glass">
              <div class="customer-avatar-large">
                {{ getInitials(freelancer.full_name || freelancer.name || freelancer.login) }}
              </div>
              <div class="customer-details">
                <div class="customer-name-large">
                  {{ freelancer.full_name || freelancer.name || freelancer.login || 'Без имени' }}
                </div>
                <div class="customer-login" v-if="freelancer.login">
                  <span class="info-label">🔑 Логин:</span>
                  <span class="info-value">{{ freelancer.login }}</span>
                </div>
                <div class="customer-phone" v-if="freelancer.phone">
                  <span class="info-label">📞 Телефон:</span>
                  <span class="info-value">{{ freelancer.phone }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Кнопка отклика для фрилансеров -->
          <div v-if="canRespond" class="action-section">
            <button 
              @click="respondToProject" 
              class="respond-button ios-glass"
              :disabled="responding || project.freelancer_id || project.status !== 'open'"
            >
              <span v-if="responding" class="button-spinner"></span>
              <span class="button-text">
                {{ getButtonText }}
              </span>
              <span class="button-glow"></span>
            </button>
            
            <div v-if="respondMessage" class="respond-message" :class="respondMessageType">
              {{ respondMessage }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProjectDetail',
  
  data() {
    return {
      project: null,
      categories: [],
      customer: null,
      freelancer: null,
      customerLoading: false,
      freelancerLoading: false,
      loading: true,
      apiBaseUrl: '',
      
      // Данные пользователя
      user: null,
      userLoading: true,
      
      // Состояние отклика
      responding: false,
      respondMessage: '',
      respondMessageType: ''
    }
  },
  
  computed: {
    // Проверка, может ли пользователь откликнуться
    canRespond() {
      return this.user && 
             this.user.role === 'freelancer' && 
             this.project && 
             this.project.status === 'open' && 
             !this.project.freelancer_id;
    },
    
    // Текст кнопки в зависимости от состояния
    getButtonText() {
      if (this.responding) return 'Отправка...';
      if (this.project?.freelancer_id) return 'Проект уже занят';
      if (this.project?.status !== 'open') return 'Проект закрыт';
      return 'Откликнуться на проект';
    }
  },
  
  created() {
    this.fetchCurrentUser();
    this.fetchProject();
    this.fetchCategories();
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
    
    async fetchProject() {
      try {
        const projectId = this.$route.params.id;
        const response = await fetch(`${this.apiBaseUrl}/api/projects/${projectId}`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.project) {
          this.project = data.project;
        } else {
          this.project = data;
        }
        
        console.log('Загружен проект:', this.project);
        
        if (this.project.customer_id) {
          await this.fetchCustomer(this.project.customer_id);
        }
        
        if (this.project.freelancer_id) {
          await this.fetchFreelancer(this.project.freelancer_id);
        }
        
      } catch (error) {
        console.error('Ошибка при загрузке проекта:', error);
        this.showNotification('Проект не найден', 'error');
        this.$router.push('/projects');
      } finally {
        this.loading = false;
      }
    },
    
    async fetchCategories() {
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/categories`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        this.categories = data;
        console.log('Загружены категории:', this.categories);
      } catch (error) {
        console.error('Ошибка при загрузке категорий:', error);
      }
    },
    
    async fetchCustomer(customerId) {
      this.customerLoading = true;
      console.log('Загрузка заказчика с ID:', customerId);
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/users/${customerId}`);
        
        if (!response.ok) {
          if (response.status === 404) {
            console.log('Заказчик не найден');
            this.customer = null;
            return;
          }
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Ответ от сервера (заказчик):', data);
        
        if (data.user) {
          this.customer = data.user;
        } else {
          this.customer = data;
        }
        
        console.log('Загружен заказчик:', this.customer);
        
      } catch (error) {
        console.error('Ошибка при загрузке данных заказчика:', error);
        this.customer = null;
      } finally {
        this.customerLoading = false;
      }
    },
    
    async fetchFreelancer(freelancerId) {
      this.freelancerLoading = true;
      console.log('Загрузка исполнителя с ID:', freelancerId);
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/users/${freelancerId}`);
        
        if (!response.ok) {
          if (response.status === 404) {
            console.log('Исполнитель не найден');
            this.freelancer = null;
            return;
          }
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Ответ от сервера (исполнитель):', data);
        
        if (data.user) {
          this.freelancer = data.user;
        } else {
          this.freelancer = data;
        }
        
        console.log('Загружен исполнитель:', this.freelancer);
        
      } catch (error) {
        console.error('Ошибка при загрузке данных исполнителя:', error);
        this.freelancer = null;
      } finally {
        this.freelancerLoading = false;
      }
    },
    
    // Метод для отклика на проект
    async respondToProject() {
      if (!this.user || this.user.role !== 'freelancer') {
        this.showMessage('Только фрилансеры могут откликаться на проекты', 'error');
        return;
      }
      
      if (this.project.freelancer_id) {
        this.showMessage('На этот проект уже есть исполнитель', 'error');
        return;
      }
      
      if (this.project.status !== 'open') {
        this.showMessage('Этот проект уже закрыт для откликов', 'error');
        return;
      }
      
      this.responding = true;
      this.respondMessage = '';
      
      const token = localStorage.getItem('token');
      
      if (!token) {
        this.showMessage('Необходимо авторизоваться', 'error');
        this.responding = false;
        return;
      }
      
      try {
        // Подготавливаем данные для обновления проекта
        const projectData = {
          id: this.project.id,
          freelancer_id: this.user.id,
          status: 'in_progress' // Меняем статус на "в работе"
        };
        
        console.log('Отправка данных:', projectData);
        
        const response = await fetch(`${this.apiBaseUrl}/api/projects/edit`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(projectData)
        });
        
        const result = await response.json();
        console.log('Ответ сервера:', result);
        
        if (response.ok && result.success) {
          // Обновляем данные проекта
          this.project.freelancer_id = this.user.id;
          this.project.status = 'in_progress';
          
          // Загружаем данные фрилансера
          await this.fetchFreelancer(this.user.id);
          
          this.showMessage('Вы успешно откликнулись на проект!', 'success');
        } else {
          throw new Error(result.message || 'Ошибка при отклике на проект');
        }
        
      } catch (error) {
        console.error('Ошибка при отклике:', error);
        this.showMessage(error.message || 'Ошибка при отклике на проект', 'error');
      } finally {
        this.responding = false;
      }
    },
    
    showMessage(message, type = 'info') {
      this.respondMessage = message;
      this.respondMessageType = type;
      
      // Автоматически скрываем сообщение через 5 секунд
      setTimeout(() => {
        this.respondMessage = '';
        this.respondMessageType = '';
      }, 5000);
    },
    
    getCategoryName(categoryId) {
      if (!categoryId) return 'Без категории';
      const category = this.categories.find(c => c.id === categoryId);
      return category ? category.name : 'Без категории';
    },
    
    goBack() {
      this.$router.push('/projects');
    },
    
    formatBudget(budget) {
      if (!budget) return 'Не указан';
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 0
      }).format(budget);
    },
    
    formatDate(date) {
      if (!date) return 'Не указан';
      return new Date(date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    
    getStatusText(status) {
      const statusMap = {
        'open': 'Открыт',
        'in_progress': 'В работе',
        'completed': 'Завершен',
        'cancelled': 'Отменен'
      };
      return statusMap[status] || status || 'Открыт';
    },
    
    getInitials(name) {
      if (!name) return '?';
      return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    },
    
    showNotification(message, type) {
      console.log(`[${type}] ${message}`);
    }
  }
}
</script>

<style scoped>
/* Добавьте новые стили для дополнительных полей */
.customer-login,
.customer-phone,
.customer-avatar-info {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 8px;
}

.customer-login .info-label,
.customer-phone .info-label,
.customer-avatar-info .info-label {
  font-size: 0.9rem;
  color: #A8D1FF;
}

.customer-login .info-value,
.customer-phone .info-value,
.customer-avatar-info .info-value {
  font-size: 1rem;
  color: #F0F8FF;
}

/* Стили для секции действий */
.action-section {
  margin-top: 40px;
  padding-top: 40px;
  border-top: 2px solid rgba(168, 209, 255, 0.2);
  text-align: center;
}

.respond-button {
  padding: 18px 50px;
  font-size: 1.2rem;
  font-weight: 600;
  background: linear-gradient(135deg, rgba(39, 174, 96, 0.3), rgba(46, 204, 113, 0.3));
  border: 2px solid rgba(46, 204, 113, 0.4);
  border-radius: 9999px;
  color: #FFFFFF;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  min-width: 280px;
  position: relative;
  overflow: hidden;
}

.respond-button:hover:not(:disabled) {
  background: linear-gradient(135deg, rgba(39, 174, 96, 0.5), rgba(46, 204, 113, 0.5));
  border-color: rgba(46, 204, 113, 0.8);
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(46, 204, 113, 0.3);
}

.respond-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.2);
}

.respond-button .button-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.respond-button .button-glow {
  position: absolute;
  top: 0;
  left: -100%;
  width: 200%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.3),
    transparent
  );
  transition: 0.8s;
}

.respond-button:hover:not(:disabled) .button-glow {
  left: 100%;
}

.respond-message {
  margin-top: 20px;
  padding: 12px 20px;
  border-radius: 12px;
  font-size: 1rem;
  text-align: center;
}

.respond-message.success {
  background: rgba(46, 204, 113, 0.2);
  border: 1px solid rgba(46, 204, 113, 0.4);
  color: #2ecc71;
}

.respond-message.error {
  background: rgba(231, 76, 60, 0.2);
  border: 1px solid rgba(231, 76, 60, 0.4);
  color: #e74c3c;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Остальные стили остаются без изменений */
.project-detail-page {
  min-height: 100vh;
  padding: 40px 0;
  position: relative;
}

.container {
  max-width: 900px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 20;
}

.project-detail {
  padding: 60px;
  position: relative;
}

.back-button {
  padding: 12px 24px;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.3);
  border-radius: 9999px;
  color: #FFFFFF;
  font-size: 1rem;
  cursor: pointer;
  margin-bottom: 40px;
  transition: all 0.3s ease;
}

.back-button:hover {
  background: rgba(10, 77, 140, 0.3);
  border-color: rgba(168, 209, 255, 0.5);
  transform: translateX(-3px);
}

.project-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
  flex-wrap: wrap;
  gap: 20px;
}

.project-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #FFFFFF;
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
  margin: 0;
}

.project-status {
  padding: 8px 24px;
  border-radius: 9999px;
  font-size: 1rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background: rgba(10, 77, 140, 0.3);
  backdrop-filter: blur(5px);
  border: 1px solid rgba(168, 209, 255, 0.2);
  color: #F0F8FF;
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

.info-section {
  margin-bottom: 40px;
}

.info-section h3 {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 20px;
  color: #FFFFFF;
}

.info-section p {
  color: #F0F8FF;
  line-height: 1.8;
  font-size: 1.1rem;
  white-space: pre-wrap;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 30px;
  padding: 30px 0;
  border-top: 1px solid rgba(168, 209, 255, 0.1);
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
  margin-bottom: 40px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.info-label {
  color: #A8D1FF;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.info-value {
  color: #FFFFFF;
  font-size: 1.2rem;
  font-weight: 500;
}

.customer-section,
.additional-info {
  margin-bottom: 40px;
}

.customer-section h3,
.additional-info h3 {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 20px;
  color: #FFFFFF;
}

.customer-card {
  padding: 30px;
  display: flex;
  align-items: center;
  gap: 30px;
  background: rgba(10, 77, 140, 0.2);
  border-radius: 24px;
  flex-wrap: wrap;
}

.customer-avatar-large {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 2rem;
  color: #FFFFFF;
  border: 3px solid rgba(168, 209, 255, 0.3);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
}

.customer-details {
  flex: 1;
}

.customer-name-large {
  font-size: 1.8rem;
  font-weight: 600;
  color: #FFFFFF;
  margin-bottom: 10px;
}

.customer-loading {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 30px;
  background: rgba(10, 77, 140, 0.1);
  border-radius: 24px;
  color: #F0F8FF;
}

.customer-loading-spinner {
  width: 24px;
  height: 24px;
  border: 2px solid rgba(168, 209, 255, 0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.customer-not-found {
  padding: 30px;
  background: rgba(10, 77, 140, 0.1);
  border-radius: 24px;
  color: #F0F8FF;
  text-align: center;
  font-size: 1.1rem;
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

@media (max-width: 768px) {
  .project-detail {
    padding: 30px;
  }
  
  .project-title {
    font-size: 2rem;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .customer-card {
    flex-direction: column;
    text-align: center;
    padding: 20px;
  }
  
  .customer-avatar-large {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }
  
  .customer-name-large {
    font-size: 1.5rem;
  }
  
  .customer-login,
  .customer-phone,
  .customer-avatar-info {
    justify-content: center;
  }
  
  .respond-button {
    width: 100%;
    min-width: auto;
    padding: 15px 30px;
    font-size: 1.1rem;
  }
}

@media (max-width: 480px) {
  .project-detail {
    padding: 20px;
  }
  
  .project-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .project-title {
    font-size: 1.8rem;
  }
  
  .customer-card {
    padding: 15px;
  }
}
</style>