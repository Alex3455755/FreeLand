<!-- resources/js/components/AdminPanel.vue -->
<template>
  <div class="admin-panel">
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
      
      <div class="particles" id="particles"></div>
    </div>

    <HeaderMenu />

    <div class="container">
      <!-- Заголовок -->
      <div class="admin-header ios-glass">
        <h1 class="admin-title">Панель администратора</h1>
        <p class="admin-subtitle">Управление пользователями, проектами и контентом</p>
      </div>

      <!-- Табы навигации -->
      <div class="admin-tabs ios-glass">
        <button 
          v-for="tab in tabs" 
          :key="tab.value"
          class="tab-button" 
          :class="{ active: activeTab === tab.value }"
          @click="activeTab = tab.value"
        >
          <span class="tab-icon">{{ tab.icon }}</span>
          {{ tab.label }}
        </button>
      </div>

      <!-- Панель управления в зависимости от активного таба -->
      <div class="admin-content">
        <!-- Пользователи -->
        <div v-if="activeTab === 'users'" class="admin-section">
          <div class="section-header">
            <h2 class="section-title">Управление пользователями</h2>
            <button class="add-button ios-glass" @click="openUserModal()">
              <span class="button-icon">➕</span>
              Добавить пользователя
            </button>
          </div>

          <div class="table-container ios-glass">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Имя</th>
                  <th>Логин</th>
                  <th>Телефон</th>
                  <th>Роль</th>
                  <th>Баланс</th>
                  <th>Рейтинг</th>
                  <th>Действия</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td>{{ user.id }}</td>
                  <td>{{ user.full_name || '—' }}</td>
                  <td>{{ user.login }}</td>
                  <td>{{ user.phone || '—' }}</td>
                  <td>{{ user.role || '—' }}</td>
                  <td>{{ user.balance || '—' }}</td>
                  <td>{{ user.rating || '—' }}</td>
                  <td class="actions">
                    <button class="action-btn edit" @click="openUserModal(user)">
                      <span class="action-icon">✏️</span>
                    </button>
                    <button class="action-btn delete" @click="confirmDelete('users', user.id, user.full_name || user.login)">
                      <span class="action-icon">🗑️</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Проекты -->
        <div v-if="activeTab === 'projects'" class="admin-section">
          <div class="section-header">
            <h2 class="section-title">Управление проектами</h2>
            <button class="add-button ios-glass" @click="openProjectModal()">
              <span class="button-icon">➕</span>
              Добавить проект
            </button>
          </div>

          <div class="table-container ios-glass">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Название</th>
                  <th>Описание</th>
                  <th>Бюджет</th>
                  <th>Статус</th>
                  <th>Заказчик</th>
                  <th>Категория</th>
                  <th>Дедлайн</th>
                  <th>Действия</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="project in projects" :key="project.id">
                  <td>{{ project.id }}</td>
                  <td>{{ project.title }}</td>
                  <td>{{ truncateText(project.description, 30) }}</td>
                  <td>{{ formatBudget(project.budget) }}</td>
                  <td>
                    <span class="status-badge" :class="project.status">
                      {{ getStatusText(project.status) }}
                    </span>
                  </td>
                  <td>{{ getUserName(project.customer_id) }}</td>
                  <td>{{ getCategoryName(project.category_id) }}</td>
                  <td>{{ formatDate(project.deadline) }}</td>
                  <td class="actions">
                    <button class="action-btn edit" @click="openProjectModal(project)">
                      <span class="action-icon">✏️</span>
                    </button>
                    <button class="action-btn delete" @click="confirmDelete('projects', project.id, project.title)">
                      <span class="action-icon">🗑️</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Категории -->
        <div v-if="activeTab === 'categories'" class="admin-section">
          <div class="section-header">
            <h2 class="section-title">Управление категориями</h2>
            <button class="add-button ios-glass" @click="openCategoryModal()">
              <span class="button-icon">➕</span>
              Добавить категорию
            </button>
          </div>

          <div class="table-container ios-glass">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Название</th>
                  <th>Действия</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="category in categories" :key="category.id">
                  <td>{{ category.id }}</td>
                  <td>{{ category.name }}</td>
                  <td class="actions">
                    <button class="action-btn edit" @click="openCategoryModal(category)">
                      <span class="action-icon">✏️</span>
                    </button>
                    <button class="action-btn delete" @click="confirmDelete('categories', category.id, category.name)">
                      <span class="action-icon">🗑️</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Комментарии -->
        <div v-if="activeTab === 'comments'" class="admin-section">
          <div class="section-header">
            <h2 class="section-title">Управление комментариями</h2>
          </div>

          <div class="table-container ios-glass">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Автор</th>
                  <th>Проект</th>
                  <th>Рейтинг</th>
                  <th>Комментарий</th>
                  <th>Действия</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="comment in comments" :key="comment.id">
                  <td>{{ comment.id }}</td>
                  <td>{{ getUserName(comment.user_id) }}</td>
                  <td>{{ getProjectTitle(comment.project_id) }}</td>
                  <td>{{ comment.rating }}</td>
                  <td>{{ truncateText(comment.text, 50) }}</td>
                  <td class="actions">
                    <button class="action-btn edit" @click="openCommentModal(comment)">
                      <span class="action-icon">✏️</span>
                    </button>
                    <button class="action-btn delete" @click="confirmDelete('comments', comment.id, `Комментарий #${comment.id}`)">
                      <span class="action-icon">🗑️</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Платежи -->
        <div v-if="activeTab === 'payments'" class="admin-section">
          <div class="section-header">
            <h2 class="section-title">Управление платежами</h2>
          </div>

          <div class="table-container ios-glass">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Отправитель</th>
                  <th>Получатель</th>
                  <th>Коммиссия</th>
                  <th>Сумма</th>
                  <th>Статус</th>
                  <th>Действия</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="payment in payments" :key="payment.id">
                  <td>{{ payment.id }}</td>
                  <td>{{ getUserName(payment.customer_id) }}</td>
                  <td>{{ getUserName(payment.freelancer_id) }}</td>
                  <td>{{ payment.commission }}</td>
                  <td>{{ formatBudget(payment.amount) }}</td>
                  <td>
                    <span class="status-badge" :class="payment.status">
                      {{ payment.status }}
                    </span>
                  </td>
                  <td class="actions">
                    <button class="action-btn edit" @click="openPaymentModal(payment)">
                      <span class="action-icon">✏️</span>
                    </button>
                    <button class="action-btn delete" @click="confirmDelete('payments', payment.id, `Платеж #${payment.id}`)">
                      <span class="action-icon">🗑️</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно для добавления/редактирования -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content ios-glass">
        <div class="modal-header">
          <h3 class="modal-title">{{ modalTitle }}</h3>
          <button class="modal-close" @click="closeModal">✕</button>
        </div>

        <div class="modal-body">
          <!-- Форма в зависимости от типа -->
          <form @submit.prevent="saveItem">
            <!-- Поля для пользователя -->
            <template v-if="modalType === 'user'">
              <div class="form-group">
                <label>Полное имя</label>
                <input v-model="modalData.full_name" type="text" class="form-input ios-glass">
              </div>
              <div class="form-group">
                <label>Логин</label>
                <input v-model="modalData.login" type="text" class="form-input ios-glass" required>
              </div>
              <label>Роль</label>
              <select v-model="modalData.role" class="form-input ios-glass">
                  <option value="admin">Админ</option>
                  <option value="customer">Клиент</option>
                  <option value="freelancer">Фрилансер</option>
                </select>
              <div class="form-group">
                <label>Телефон</label>
                <input v-model="modalData.phone" type="tel" class="form-input ios-glass">
              </div>
              <div class="form-group">
                <label>Баланс</label>
                <input v-model="modalData.balance" type="number" class="form-input ios-glass">
              </div>
              <div class="form-group">
                <label>Рейтинг</label>
                <input v-model="modalData.rating" type="number" class="form-input ios-glass">
              </div>
            </template>

            <!-- Поля для проекта -->
            <template v-if="modalType === 'project'">
              <div class="form-group">
                <label>Название</label>
                <input v-model="modalData.title" type="text" class="form-input ios-glass" required>
              </div>
              <div class="form-group">
                <label>Описание</label>
                <textarea v-model="modalData.description" class="form-input ios-glass" rows="3"></textarea>
              </div>
              <div class="form-group">
                <label>Бюджет</label>
                <input v-model="modalData.budget" type="number" class="form-input ios-glass">
              </div>
              <div class="form-group">
                <label>Дедлайн</label>
                <input v-model="modalData.deadline" type="date" class="form-input ios-glass">
              </div>
              <div class="form-group">
                <label>Статус</label>
                <select v-model="modalData.status" class="form-input ios-glass">
                  <option value="open">Открыт</option>
                  <option value="in_progress">В работе</option>
                  <option value="completed">Завершен</option>
                </select>
              </div>
              <div class="form-group">
                <label>Заказчик</label>
                <select v-model="modalData.customer_id" class="form-input ios-glass" required>
                  <option value="">Выберите заказчика</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.full_name || user.login }} (ID: {{ user.id }})
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Категория</label>
                <select v-model="modalData.category_id" class="form-input ios-glass">
                  <option value="">Выберите категорию</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
            </template>

            <!-- Поля для категории -->
            <template v-if="modalType === 'categorie'">
              <div class="form-group">
                <label>Название</label>
                <input v-model="modalData.name" type="text" class="form-input ios-glass" required>
              </div>
            </template>

            <!-- Поля для комментария -->
            <template v-if="modalType === 'comment'">
              <div class="form-group">
                <label>Автор</label>
                <select v-model="modalData.user_id" class="form-input ios-glass" required>
                  <option value="">Выберите автора</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.full_name || user.login }} (ID: {{ user.id }})
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Проект</label>
                <select v-model="modalData.project_id" class="form-input ios-glass" required>
                  <option value="">Выберите проект</option>
                  <option v-for="project in projects" :key="project.id" :value="project.id">
                    {{ project.title }} (ID: {{ project.id }})
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Текст комментария</label>
                <textarea v-model="modalData.text" class="form-input ios-glass" rows="3" required></textarea>
              </div>
              <div class="form-group">
                <label>Рейтинг</label>
                <input v-model="modalData.rating" type="number" min="1" max="5" class="form-input ios-glass">
              </div>
            </template>

            <!-- Поля для платежа -->
            <template v-if="modalType === 'payment'">
              <div class="form-group">
                <label>Отправитель</label>
                <select v-model="modalData.sender_id" class="form-input ios-glass" required>
                  <option value="">Выберите отправителя</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.full_name || user.login }} (ID: {{ user.id }})
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Получатель</label>
                <select v-model="modalData.project_id" class="form-input ios-glass" required>
                  <option value="">Выберите получателя</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.full_name || user.login }} (ID: {{ user.id }})
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Сумма</label>
                <input v-model.number="modalData.amount" type="number" class="form-input ios-glass" required>
              </div>
              <div class="form-group">
                <label>Коммиссия</label>
                <input v-model.number="modalData.commission" type="number" class="form-input ios-glass" required>
              </div>
              <div class="form-group">
                <label>Статус</label>
                <select v-model="modalData.status" class="form-input ios-glass">
                  <option value="frozen">Заморожен</option>
                  <option value="paid">Оплачен</option>
                  <option value="refunded">Отменен</option>
                </select>
              </div>
            </template>

            <div class="modal-actions">
              <button type="button" class="cancel-button ios-glass" @click="closeModal">Отмена</button>
              <button type="submit" class="save-button ios-glass">Сохранить</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div v-if="showDeleteConfirm" class="modal-overlay" @click.self="cancelDelete">
      <div class="modal-content ios-glass delete-confirm">
        <div class="modal-header">
          <h3 class="modal-title">Подтверждение удаления</h3>
          <button class="modal-close" @click="cancelDelete">✕</button>
        </div>
        <div class="modal-body">
          <p>Вы уверены, что хотите удалить {{ deleteItemType }} "{{ deleteItemName }}"?</p>
          <p class="warning-text">Это действие нельзя отменить.</p>
        </div>
        <div class="modal-actions">
          <button class="cancel-button ios-glass" @click="cancelDelete">Отмена</button>
          <button class="delete-button ios-glass" @click="deleteItem">Удалить</button>
        </div>
      </div>
    </div>

    <FooterApp />
  </div>
</template>

<script>

import FooterApp from '@/elements/FooterApp.vue';
import HeaderMenu from '@/elements/HeaderMenu.vue';

export default {
  name: 'AdminPanel',
  
  components: {
    HeaderMenu,
    FooterApp
  },
  
  data() {
    return {
      activeTab: 'users',
      tabs: [
  { value: 'users', label: 'Пользователи', icon: '👥' },
  { value: 'projects', label: 'Проекты', icon: '📋' },
  { value: 'categories', label: 'Категории', icon: '📁' },
  { value: 'comments', label: 'Комментарии', icon: '💬' },
  { value: 'payments', label: 'Платежи', icon: '💰' },
],
      
      // Данные
      users: [],
      projects: [],
      categories: [],
      comments: [],
      payments: [],

      
      // Модальное окно
      showModal: false,
      modalType: null,
      modalData: {},
      isEditing: false,
      showDeleteConfirm: false,
      deleteItemType: null,
      deleteItemId: null,
      deleteItemName: '',
      apiBaseUrl: '',
      loading: true
    }
  },
  
  computed: {
    modalTitle() {
      const action = this.isEditing ? 'Редактирование' : 'Добавление';
      const typeMap = {
        user: 'пользователя',
        project: 'проекта',
        category: 'категории',
        comment: 'комментария',
        payment: 'платежа'
      };
      return `${action} ${typeMap[this.modalType] || ''}`;
    }
  },
  
  created() {
    this.fetchAllData();
    this.initParticles();
  },
  
  methods: {
    async fetchAllData() {
      this.loading = true;
      try {
        await Promise.all([
          this.fetchUsers(),
          this.fetchProjects(),
          this.fetchCategories(),
          this.fetchComments(),
          this.fetchPayments()
        ]);
      } catch (error) {
        console.error('Ошибка загрузки данных:', error);
        this.showNotification('Ошибка загрузки данных', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    async fetchUsers() {
      const response = await fetch(`${this.apiBaseUrl}/api/users`);
      const data = await response.json();
      console.log(data);
      this.users = data;
    },
    
    async fetchProjects() {
      const response = await fetch(`${this.apiBaseUrl}/api/projects`);
      const data = await response.json();
      this.projects = data;
    },
    
    async fetchCategories() {
      const response = await fetch(`${this.apiBaseUrl}/api/categories`);
      const data = await response.json();
      this.categories = data;
    },
    
    async fetchComments() {
      const response = await fetch(`${this.apiBaseUrl}/api/comments`);
      const data = await response.json();
      this.comments = data;
    },
    
    async fetchPayments() {
      const response = await fetch(`${this.apiBaseUrl}/api/payments`);
      const data = await response.json();
      this.payments = data;
    },
    
    // Модальные окна
    openUserModal(user = null) {
      this.modalType = 'user';
      this.isEditing = !!user;
      this.modalData = user ? { ...user } : {
        full_name: '',
        login: '',
        phone: '',
        role: null,
        balance: '',
        rating: '',

      };
      this.showModal = true;
    },
    
    openProjectModal(project = null) {
      this.modalType = 'project';
      this.isEditing = !!project;
      this.modalData = project ? { ...project } : {
        title: '',
        description: '',
        budget: null,
        deadline: null,
        status: 'open',
        freelancer_id: null,
        category_id: null,
        customer_id: null
      };
      this.showModal = true;
    },
    
    openCategoryModal(category = null) {
      this.modalType = 'categorie';
      this.isEditing = !!category;
      this.modalData = category ? { ...category } : {
        name: ''
      };
      this.showModal = true;
    },
    
    openCommentModal(comment = null) {
      this.modalType = 'comment';
      this.isEditing = !!comment;
      this.modalData = comment ? { ...comment } : {
        user_id: null,
        project_id: null,
        rating: null,
        text: ''
      };
      this.showModal = true;
    },
    
    openPaymentModal(payment = null) {
      this.modalType = 'payment';
      this.isEditing = !!payment;
      this.modalData = payment ? { ...payment } : {
        freelancer_id: null,
        project_id: null,
        commision: null,
        amount: null,
        status: 'pending'
      };
      this.showModal = true;
    },
    
    openMessageModal(message = null) {
      this.modalType = 'message';
      this.isEditing = !!message;
      this.modalData = message ? { ...message } : {
        sender_id: null,
        receiver_id: null,
        text: ''
      };
      this.showModal = true;
    },
    
    closeModal() {
      this.showModal = false;
      this.modalData = {};
      this.isEditing = false;
    },
    
    async saveItem() {
      try {
        let url = `${this.apiBaseUrl}/api/${this.modalType}s/add`;
        
        if (this.isEditing) {
          url = `${this.apiBaseUrl}/api/${this.modalType}s/edit`;
        }
        
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(this.modalData)
        });
        console.log(this.modalData);
        
        if (!response.ok) {
          throw new Error('Ошибка сохранения');
        }
        
        const result = await response.json();
        
        if (result.success) {
          this.showNotification(result.message, 'success');
          this.closeModal();
          await this.fetchAllData();
        }
        
      } catch (error) {
        console.error('Ошибка сохранения:', error);
        this.showNotification('Ошибка при сохранении', 'error');
      }
    },
    
    confirmDelete(type, id, name) {
      this.deleteItemType = type;
      this.deleteItemId = id;
      this.deleteItemName = name;
      this.showDeleteConfirm = true;
    },
    
    cancelDelete() {
      this.showDeleteConfirm = false;
      this.deleteItemType = null;
      this.deleteItemId = null;
      this.deleteItemName = '';
    },
    
    async deleteItem() {
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/${this.deleteItemType}/destroy/${this.deleteItemId}`);
        
        if (!response.ok) {
          throw new Error('Ошибка удаления');
        }
        
        const result = await response.json();
        
        if (result.success) {
          this.showNotification(result.message, 'success');
          this.cancelDelete();
          await this.fetchAllData();
        }
        
      } catch (error) {
        console.error('Ошибка удаления:', error);
        this.showNotification('Ошибка при удалении', 'error');
      }
    },
    
    // Вспомогательные методы
    getUserName(userId) {
      if (!userId) return '—';
      const user = this.users.find(u => u.id === userId);
      return user ? (user.full_name || user.login) : `ID: ${userId}`;
    },
    
    getCategoryName(categoryId) {
      if (!categoryId) return '—';
      const category = this.categories.find(c => c.id === categoryId);
      return category ? category.name : `ID: ${categoryId}`;
    },
    
    getProjectTitle(projectId) {
      if (!projectId) return '—';
      const project = this.projects.find(p => p.id === projectId);
      return project ? project.title : `ID: ${projectId}`;
    },
    
    formatBudget(budget) {
      if (!budget) return '—';
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 0
      }).format(budget);
    },
    
    formatDate(date) {
      if (!date) return '—';
      return new Date(date).toLocaleDateString('ru-RU');
    },
    
    getStatusText(status) {
      const statusMap = {
        'open': 'Открыт',
        'in_progress': 'В работе',
        'completed': 'Завершен'
      };
      return statusMap[status] || status || '—';
    },
    
    truncateText(text, length) {
      if (!text) return '—';
      return text.length > length ? text.substring(0, length) + '...' : text;
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

.avatar-preview {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}
/* Все стили остаются точно такими же */
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
<style scoped>
.admin-panel {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  position: relative;
  padding: 40px 0 80px;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 20;
  width: 100%;
}

/* Заголовок */
.admin-header {
  padding: 60px 40px;
  margin-bottom: 30px;
  text-align: center;
}

.admin-title {
  font-size: 3.2rem;
  font-weight: 700;
  margin-bottom: 16px;
  color: #FFFFFF;
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.admin-subtitle {
  font-size: 1.4rem;
  color: #F0F8FF;
}

/* Табы */
.admin-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  padding: 20px;
  margin-bottom: 30px;
  justify-content: center;
}

.tab-button {
  padding: 12px 24px;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 9999px;
  color: #F0F8FF;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.tab-button:hover {
  background: rgba(10, 77, 140, 0.3);
  border-color: rgba(168, 209, 255, 0.4);
  transform: translateY(-2px);
}

.tab-button.active {
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  border-color: rgba(168, 209, 255, 0.6);
  color: #FFFFFF;
  box-shadow: 0 10px 20px rgba(8, 51, 88, 0.4);
}

.tab-icon {
  font-size: 1.2rem;
}

/* Секции */
.admin-section {
  margin-bottom: 40px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 20px;
}

.section-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: #FFFFFF;
}

.add-button {
  padding: 12px 24px;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.3);
  border-radius: 9999px;
  color: #FFFFFF;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.add-button:hover {
  background: rgba(10, 77, 140, 0.3);
  border-color: rgba(168, 209, 255, 0.5);
  transform: translateY(-2px);
}

.button-icon {
  font-size: 1.1rem;
}

/* Таблицы */
.table-container {
  padding: 20px;
  overflow-x: auto;
}

.admin-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 800px;
}

.admin-table th {
  text-align: left;
  padding: 15px;
  color: #A8D1FF;
  font-weight: 600;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid rgba(168, 209, 255, 0.2);
}

.admin-table td {
  padding: 15px;
  color: #F0F8FF;
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
}

.admin-table tr:hover td {
  background: rgba(10, 77, 140, 0.1);
}

/* Бейджи */
.role-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 500;
}

.role-badge.admin {
  background: rgba(231, 76, 60, 0.2);
  color: #e74c3c;
  border: 1px solid rgba(231, 76, 60, 0.4);
}

.role-badge.freelancer {
  background: rgba(46, 204, 113, 0.2);
  color: #2ecc71;
  border: 1px solid rgba(46, 204, 113, 0.4);
}

.role-badge.customer {
  background: rgba(52, 152, 219, 0.2);
  color: #3498db;
  border: 1px solid rgba(52, 152, 219, 0.4);
}

.status-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 500;
}

.status-badge.open {
  background: rgba(46, 204, 113, 0.2);
  color: #2ecc71;
  border: 1px solid rgba(46, 204, 113, 0.4);
}

.status-badge.in_progress {
  background: rgba(241, 196, 15, 0.2);
  color: #f1c40f;
  border: 1px solid rgba(241, 196, 15, 0.4);
}

.status-badge.completed {
  background: rgba(52, 152, 219, 0.2);
  color: #3498db;
  border: 1px solid rgba(52, 152, 219, 0.4);
}

.status-badge.cancelled {
  background: rgba(231, 76, 60, 0.2);
  color: #e74c3c;
  border: 1px solid rgba(231, 76, 60, 0.4);
}

.read-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 500;
  background: rgba(149, 165, 166, 0.2);
  color: #95a5a6;
  border: 1px solid rgba(149, 165, 166, 0.4);
}

.read-badge.read {
  background: rgba(46, 204, 113, 0.2);
  color: #2ecc71;
  border: 1px solid rgba(46, 204, 113, 0.4);
}

.rating-stars {
  color: #f1c40f;
  letter-spacing: 2px;
}

/* Действия */
.actions {
  display: flex;
  gap: 8px;
}

.action-btn {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1px solid rgba(168, 209, 255, 0.2);
  background: rgba(10, 77, 140, 0.2);
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn:hover {
  transform: translateY(-2px);
}

.action-btn.edit:hover {
  background: rgba(52, 152, 219, 0.3);
  border-color: rgba(52, 152, 219, 0.4);
}

.action-btn.delete:hover {
  background: rgba(231, 76, 60, 0.3);
  border-color: rgba(231, 76, 60, 0.4);
}

.action-icon {
  font-size: 1rem;
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
}

.modal-content {
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  overflow-y: auto;
  background: rgba(10, 77, 140, 0.3);
}

.modal-content.delete-confirm {
  max-width: 400px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 30px;
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #FFFFFF;
}

.modal-close {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  color: #FFFFFF;
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  background: rgba(231, 76, 60, 0.3);
  border-color: rgba(231, 76, 60, 0.4);
  transform: rotate(90deg);
}

.modal-body {
  padding: 30px;
}

/* Формы */
.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #A8D1FF;
  font-size: 0.95rem;
}

.form-input {
  width: 100%;
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
  background: rgba(10, 77, 140, 0.25);
}

.form-input::placeholder {
  color: rgba(168, 209, 255, 0.4);
}

textarea.form-input {
  resize: vertical;
  min-height: 80px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  color: #F0F8FF;
}

.checkbox-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.modal-actions {
  display: flex;
  gap: 15px;
  margin-top: 30px;
  justify-content: flex-end;
}

.cancel-button,
.save-button,
.delete-button {
  padding: 12px 30px;
  border-radius: 9999px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(168, 209, 255, 0.3);
}

.cancel-button {
  background: rgba(149, 165, 166, 0.2);
  color: #95a5a6;
}

.cancel-button:hover {
  background: rgba(149, 165, 166, 0.3);
  transform: translateY(-2px);
}

.save-button {
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  color: #FFFFFF;
}

.save-button:hover {
  background: linear-gradient(135deg, #1A6BB3, #2A7FC9);
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(8, 51, 88, 0.4);
}

.delete-button {
  background: rgba(231, 76, 60, 0.2);
  color: #e74c3c;
}

.delete-button:hover {
  background: rgba(231, 76, 60, 0.3);
  transform: translateY(-2px);
}

.warning-text {
  color: #e74c3c;
  font-size: 0.9rem;
  margin-top: 10px;
}

/* Адаптивность */
@media (max-width: 768px) {
  .admin-header {
    padding: 40px 20px;
  }
  
  .admin-title {
    font-size: 2.5rem;
  }
  
  .admin-subtitle {
    font-size: 1.2rem;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .add-button {
    width: 100%;
    justify-content: center;
  }
  
  .modal-content {
    width: 95%;
    padding: 0;
  }
  
  .modal-body {
    padding: 20px;
  }
  
  .modal-actions {
    flex-direction: column;
  }
  
  .cancel-button,
  .save-button,
  .delete-button {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .admin-title {
    font-size: 2rem;
  }
  
  .tab-button {
    padding: 8px 16px;
    font-size: 0.9rem;
  }
  
  .section-title {
    font-size: 1.5rem;
  }
}
</style>