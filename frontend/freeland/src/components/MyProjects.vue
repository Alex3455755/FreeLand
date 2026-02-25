<!-- resources/js/components/MyProjects.vue -->
<template>
  <div class="my-projects-page">
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

    <HeaderMenu />

    <div class="container">
      <div class="page-header ios-glass">
        <h1 class="page-title">Мои проекты</h1>
        <p class="page-subtitle">Управление вашими проектами и чатами</p>
      </div>

      <!-- Показываем loader пока загружается пользователь -->
      <div v-if="userLoading" class="loading-state">
        <div class="loader ios-glass">
          <div class="loader-spinner"></div>
          <p>Загрузка...</p>
        </div>
      </div>

      <template v-else-if="user">
        <!-- Табы для переключения между проектами и чатами -->
        <div class="tabs-container ios-glass">
          <button 
            class="tab-button" 
            :class="{ active: activeTab === 'projects' }"
            @click="activeTab = 'projects'"
          >
            📋 Проекты
          </button>
        </div>

        <!-- Вкладка с проектами -->
        <div v-if="activeTab === 'projects'" class="projects-tab">
          <!-- Состояние загрузки проектов -->
          <div v-if="projectsLoading" class="loading-state">
            <div class="loader ios-glass">
              <div class="loader-spinner"></div>
              <p>Загрузка проектов...</p>
            </div>
          </div>

          <!-- Список проектов -->
          <div v-else-if="projects.length > 0" class="projects-list">
            <div 
              v-for="project in projects" 
              :key="project.id" 
              class="project-card ios-glass"
            >
              <div class="project-header">
                <h3 class="project-title">{{ project.title }}</h3>
                <div class="project-status" :class="project.status">
                  {{ getStatusText(project.status) }}
                </div>
              </div>

              <p class="project-description">{{ truncateDescription(project.description) }}</p>

              <div class="project-meta">
                <div class="meta-item">
                  <span class="meta-icon">💰</span>
                  <span class="meta-value">{{ formatBudget(project.budget) }}</span>
                </div>
                
                <div class="meta-item" v-if="project.deadline">
                  <span class="meta-icon">⏰</span>
                  <span class="meta-value">{{ formatDate(project.deadline) }}</span>
                </div>
                
                <div class="meta-item">
                  <span class="meta-icon">📁</span>
                  <span class="meta-value">{{ getCategoryName(project.category_id) }}</span>
                </div>
              </div>

              <div class="project-footer">
                <!-- Информация о второй стороне -->
                <div class="partner-info">
                  <template v-if="isCustomer">
                    <span class="partner-label">Исполнитель:</span>
                    <div v-if="project.freelancer" class="partner-details">
                      <div class="partner-avatar">
                        {{ getInitials(project.freelancer.full_name || project.freelancer.login) }}
                      </div>
                      <span class="partner-name">{{ project.freelancer.full_name || project.freelancer.login }}</span>
                    </div>
                    <span v-else class="no-partner">Исполнитель не назначен</span>
                  </template>

                  <template v-else-if="isFreelancer">
                    <span class="partner-label">Заказчик:</span>
                    <div v-if="project.customer" class="partner-details">
                      <div class="partner-avatar">
                        {{ getInitials(project.customer.full_name || project.customer.login) }}
                      </div>
                      <span class="partner-name">{{ project.customer.full_name || project.customer.login }}</span>
                    </div>
                  </template>
                </div>

                <div class="project-actions">
                  <!-- Кнопка чата (если есть исполнитель) -->
                  <button 
                    v-if="project.freelancer_id && (isCustomer || (isFreelancer && project.freelancer_id === user.id))"
                    @click="openChat(project)"
                    class="action-button chat-button"
                  >
                    💬 Чат
                  </button>

                  <!-- Кнопка редактирования (только для заказчика и если проект открыт) -->
                  <button 
                    v-if="isCustomer && project.status === 'open' && project.customer_id === user.id"
                    @click="editProject(project)"
                    class="action-button edit-button"
                  >
                    ✏️ Редактировать
                  </button>

                  <!-- Кнопка просмотра -->
                  <button 
                    @click="viewProject(project.id)"
                    class="action-button view-button"
                  >
                    👁️ Просмотр
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Пустое состояние проектов -->
          <div v-else class="empty-state ios-glass">
            <div class="empty-icon">📋</div>
            <h3 class="empty-title">У вас пока нет проектов</h3>
            <p class="empty-text">
              {{ isCustomer ? 'Создайте свой первый проект' : 'Откликнитесь на интересный проект' }}
            </p>
            <button @click="goToProjects" class="action-button primary">
              {{ isCustomer ? 'Создать проект' : 'Найти проекты' }}
            </button>
          </div>
        </div>

        <!-- Вкладка с чатами -->
        <div v-if="activeTab === 'chats'" class="chats-tab">
          <div v-if="chatsLoading" class="loading-state">
            <div class="loader ios-glass">
              <div class="loader-spinner"></div>
              <p>Загрузка чатов...</p>
            </div>
          </div>

          <div v-else-if="chats.length > 0" class="chats-list">
            <div 
              v-for="chat in chats" 
              :key="chat.project.id" 
              class="chat-card ios-glass"
              @click="openChat(chat.project)"
            >
              <div class="chat-header">
                <h4 class="chat-project-title">{{ chat.project.title }}</h4>
                <div class="chat-project-status" :class="chat.project.status">
                  {{ getStatusText(chat.project.status) }}
                </div>
              </div>

              <div class="chat-partner">
                <div class="partner-avatar large">
                  {{ getInitials(chat.partner?.full_name || chat.partner?.login) }}
                </div>
                <div class="partner-info">
                  <div class="partner-name">{{ chat.partner?.full_name || chat.partner?.login || 'Неизвестно' }}</div>
                  <div class="partner-role">{{ getRoleText(chat.partner?.role) }}</div>
                </div>
              </div>

              <div class="chat-last-message" v-if="chat.lastMessage">
                <span class="message-time">{{ formatTime(chat.lastMessage.time) }}</span>
                <p class="message-preview">{{ truncateMessage(chat.lastMessage.message) }}</p>
              </div>
              <div v-else class="chat-last-message empty">
                <p class="message-preview">Нет сообщений</p>
              </div>

              <div class="chat-actions">
                <button class="action-button chat-open">Открыть чат →</button>
              </div>
            </div>
          </div>

          <div v-else class="empty-state ios-glass">
            <div class="empty-icon">💬</div>
            <h3 class="empty-title">Нет активных чатов</h3>
            <p class="empty-text">Начните общение, когда у проекта появится исполнитель</p>
          </div>
        </div>
      </template>

      <!-- Если пользователь не авторизован -->
      <div v-else class="empty-state ios-glass">
        <div class="empty-icon">🔒</div>
        <h3 class="empty-title">Требуется авторизация</h3>
        <p class="empty-text">Войдите в систему, чтобы просматривать свои проекты</p>
        <button @click="goToLogin" class="action-button primary">
          Войти
        </button>
      </div>
    </div>

    <!-- Модальное окно чата -->
    <div v-if="showChatModal" class="modal-overlay" @click.self="closeChatModal">
      <div class="chat-modal ios-glass">
        <div class="chat-modal-header">
          <div class="chat-modal-project">
            <h3>{{ currentChatProject?.title }}</h3>
            <div class="chat-modal-partner">
              <div class="partner-avatar small">
                {{ getInitials(currentChatPartner?.full_name || currentChatPartner?.login) }}
              </div>
              <span>{{ currentChatPartner?.full_name || currentChatPartner?.login }}</span>
              <span class="partner-role-badge">{{ getRoleText(currentChatPartner?.role) }}</span>
            </div>
          </div>
          <button @click="closeChatModal" class="modal-close">×</button>
        </div>

        <div class="chat-messages" ref="messagesContainer">
          <div v-if="messagesLoading" class="messages-loading">
            <div class="loader-sm"></div>
          </div>

          <div v-else-if="messages.length > 0" class="messages-list">
            <div 
              v-for="message in messages" 
              :key="message.id" 
              class="message-item"
              :class="{ 'my-message': message.author_id === user?.id }"
            >
              <div class="message-avatar">
                {{ getInitials(getMessageAuthor(message).full_name || getMessageAuthor(message).login) }}
              </div>
              <div class="message-content">
                <div class="message-header">
                  <span class="message-author">
                    {{ getMessageAuthor(message).full_name || getMessageAuthor(message).login }}
                  </span>
                  <span class="message-time">{{ formatTime(message.time) }}</span>
                </div>
                <p class="message-text">{{ message.text }}</p>
              </div>
            </div>
          </div>
          <div v-else class="no-messages">
            <p>Нет сообщений. Напишите первое сообщение!</p>
          </div>
        </div>

        <div class="chat-input-area">
          <textarea 
            v-model="newMessage" 
            @keydown.enter.prevent="sendMessage"
            placeholder="Введите сообщение..."
            class="chat-input"
            rows="2"
          ></textarea>
          <button 
            @click="sendMessage" 
            class="send-button"
            :disabled="!newMessage.trim() || messageSending"
          >
            <span v-if="messageSending" class="button-spinner"></span>
            <span v-else>Отправить</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Модальное окно редактирования проекта -->
    <div v-if="showEditModal" class="modal-overlay" @click.self="closeEditModal">
      <div class="modal-container ios-glass">
        <div class="modal-header">
          <h2 class="modal-title">Редактирование проекта</h2>
          <button @click="closeEditModal" class="modal-close">×</button>
        </div>
        
        <form @submit.prevent="updateProject" class="modal-form">
          <div class="form-group">
            <label>Название проекта <span class="required">*</span></label>
            <input 
              v-model="editingProject.title" 
              type="text" 
              class="form-input ios-glass" 
              required
            >
          </div>
          
          <div class="form-group">
            <label>Описание</label>
            <textarea 
              v-model="editingProject.description" 
              class="form-input ios-glass" 
              rows="4"
            ></textarea>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Бюджет (₽)</label>
              <input 
                v-model.number="editingProject.budget" 
                type="number" 
                class="form-input ios-glass"
                min="0"
              >
            </div>
            
            <div class="form-group">
              <label>Дедлайн</label>
              <input 
                v-model="editingProject.deadline" 
                type="date" 
                class="form-input ios-glass"
                :min="today"
              >
            </div>
          </div>
          
          <div class="form-group">
            <label>Категория</label>
            <select v-model="editingProject.category_id" class="form-input ios-glass">
              <option value="">Выберите категорию</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Статус</label>
            <select v-model="editingProject.status" class="form-input ios-glass">
              <option value="open">Открыт</option>
              <option value="in_progress">В работе</option>
              <option value="completed">Завершен</option>
            </select>
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="closeEditModal" class="modal-button cancel">
              Отмена
            </button>
            <button type="submit" class="modal-button submit" :disabled="projectUpdating">
              <span v-if="projectUpdating" class="button-spinner"></span>
              {{ projectUpdating ? 'Сохранение...' : 'Сохранить изменения' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <FooterApp />
  </div>
</template>

<script>
import FooterApp from '@/elements/FooterApp.vue'
import HeaderMenu from '@/elements/HeaderMenu.vue'

export default {
  name: 'MyProjects',
  
  components: {
    HeaderMenu,
    FooterApp
  },
  
  data() {
    return {
      // Данные пользователя - КАК В HEADER MENU
      user: null,
      userLoading: true,
      
      // Данные проектов
      projects: [],
      projectsLoading: false,
      categories: [],
      
      // Данные для чатов
      chats: [],
      chatsLoading: false,
      messages: [],
      messagesLoading: false,
      messageSending: false,
      newMessage: '',
      
      // Состояние UI
      activeTab: 'projects',
      showChatModal: false,
      showEditModal: false,
      
      // Текущий чат
      currentChatProject: null,
      currentChatPartner: null,
      
      // Редактирование проекта
      editingProject: null,
      projectUpdating: false,
      
      apiBaseUrl: '',
      
      // Таймер для обновления сообщений
      messagesInterval: null
    }
  },
  
  computed: {
    isCustomer() {
      return this.user && (this.user.role === 'customer' || this.user.role === 'заказчик')
    },
    
    isFreelancer() {
      return this.user && (this.user.role === 'freelancer' || this.user.role === 'фрилансер')
    },
    
    today() {
      const today = new Date()
      const yyyy = today.getFullYear()
      const mm = String(today.getMonth() + 1).padStart(2, '0')
      const dd = String(today.getDate()).padStart(2, '0')
      return `${yyyy}-${mm}-${dd}`
    }
  },
  
  created() {
    this.fetchUser() // КАК В HEADER MENU
    this.fetchCategories()
  },
  
  mounted() {
    // Запускаем периодическое обновление сообщений
    this.messagesInterval = setInterval(() => {
      if (this.showChatModal && this.currentChatProject) {
        this.fetchMessages(this.currentChatProject.id)
      }
    }, 5000)
  },
  
  beforeUnmount() {
    if (this.messagesInterval) {
      clearInterval(this.messagesInterval)
    }
  },
  
  methods: {
    // Получение текущего пользователя - ТОЧНО КАК В HEADER MENU
    async fetchUser() {
      const token = localStorage.getItem('token')
      
      console.log('MyProjects - Токен:', token) // Для отладки
      
      if (!token) {
        this.user = null
        this.userLoading = false
        return
      }

      try {
        const response = await fetch(`${this.apiBaseUrl}/api/user`, {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        })

        const data = await response.json()
        console.log('MyProjects - Ответ от сервера:', data)
        
        if (response.ok && data.success) {
          this.user = data.user
          console.log('MyProjects - Пользователь загружен:', this.user)
          
          // После успешной загрузки пользователя, загружаем проекты
          await this.fetchMyProjects()
        } else {
          // Токен невалидный
          localStorage.removeItem('token')
          localStorage.removeItem('user')
          this.user = null
        }
      } catch (error) {
        console.error('MyProjects - Ошибка получения пользователя:', error)
        this.user = null
      } finally {
        this.userLoading = false
      }
    },
    
    // Получение проектов пользователя
    async fetchMyProjects() {
      const token = localStorage.getItem('token')
      
      if (!token || !this.user) {
        console.log('MyProjects - Нет токена или пользователя для загрузки проектов')
        return
      }

      this.projectsLoading = true
      
      try {
        console.log('MyProjects - Загрузка проектов для пользователя:', this.user.id)
        
        const response = await fetch(`${this.apiBaseUrl}/api/my-projects/${this.user.id}`, {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        })
        
        console.log('MyProjects - Статус ответа проектов:', response.status)
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const data = await response.json()
        console.log('MyProjects - Данные проектов:', data)
        
        if (data.success && data.projects) {
          this.projects = data.projects
        } else if (Array.isArray(data)) {
          this.projects = data
        } else {
          this.projects = []
        }
        
        console.log('MyProjects - Установлены проекты:', this.projects)
        
      } catch (error) {
        console.error('MyProjects - Ошибка при загрузке проектов:', error)
      } finally {
        this.projectsLoading = false
      }
    },
    
    // Получение категорий
    async fetchCategories() {
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/categories`)
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const data = await response.json()
        this.categories = data
      } catch (error) {
        console.error('Ошибка при загрузке категорий:', error)
      }
    },
    
    // Получение чатов
    async fetchChats() {
      const token = localStorage.getItem('token')
      
      if (!token) {
        return
      }
      
      this.chatsLoading = true
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/my-chats`, {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        })
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const data = await response.json()
        console.log('Мои чаты:', data)
        
        if (data.success && data.chats) {
          this.chats = data.chats
        } else {
          this.chats = []
        }
        
      } catch (error) {
        console.error('Ошибка при загрузке чатов:', error)
      } finally {
        this.chatsLoading = false
      }
    },
    
    // Открыть чат
    async openChat(project) {
      this.currentChatProject = project
      
      // Определяем партнера по чату
      if (this.isCustomer) {
        this.currentChatPartner = project.freelancer
      } else {
        this.currentChatPartner = project.customer
      }
      
      this.showChatModal = true
      document.body.style.overflow = 'hidden'
      
      await this.fetchMessages(project.id)
    },
    
    // Получение сообщений для проекта
    async fetchMessages(projectId) {
      const token = localStorage.getItem('token')
      
      if (!token) {
        return
      }
      
      this.messagesLoading = true
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/messages?project_id=${projectId}`, {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        })
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const data = await response.json()
        console.log('Сообщения:', data)
        
        if (data.success && data.messages) {
          this.messages = data.messages
        } else if (Array.isArray(data)) {
          this.messages = data
        } else {
          this.messages = []
        }
        
        // Прокручиваем вниз
        this.$nextTick(() => {
          const container = this.$refs.messagesContainer
          if (container) {
            container.scrollTop = container.scrollHeight
          }
        })
        
      } catch (error) {
        console.error('Ошибка при загрузке сообщений:', error)
      } finally {
        this.messagesLoading = false
      }
    },
    
    // Отправка сообщения
    async sendMessage() {
      if (!this.newMessage.trim() || !this.user || !this.currentChatProject) {
        return
      }
      
      const token = localStorage.getItem('token')
      
      if (!token) {
        return
      }
      
      this.messageSending = true
      
      try {
        const messageData = {
          author_id: this.user.id,
          project_id: this.currentChatProject.id,
          text: this.newMessage.trim(),
          time: new Date().toISOString()
        }
        
        const response = await fetch(`${this.apiBaseUrl}/api/messages/add`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(messageData)
        })
        
        const result = await response.json()
        console.log('Результат отправки:', result)
        
        if (response.ok && result.success) {
          // Добавляем сообщение в список
          this.messages.push({
            ...messageData,
            id: result.message_id || Date.now()
          })
          
          this.newMessage = ''
          
          // Прокручиваем вниз
          this.$nextTick(() => {
            const container = this.$refs.messagesContainer
            if (container) {
              container.scrollTop = container.scrollHeight
            }
          })
        } else {
          throw new Error(result.message || 'Ошибка при отправке сообщения')
        }
        
      } catch (error) {
        console.error('Ошибка при отправке сообщения:', error)
        alert('Ошибка при отправке сообщения: ' + error.message)
      } finally {
        this.messageSending = false
      }
    },
    
    // Получить автора сообщения
    getMessageAuthor(message) {
      if (message.author_id === this.user?.id) {
        return this.user
      } else if (message.author_id === this.currentChatPartner?.id) {
        return this.currentChatPartner
      } else if (message.author_id === this.currentChatProject?.customer_id) {
        return this.currentChatProject.customer
      } else if (message.author_id === this.currentChatProject?.freelancer_id) {
        return this.currentChatProject.freelancer
      }
      
      return { full_name: 'Пользователь', login: 'user' }
    },
    
    // Закрыть чат
    closeChatModal() {
      this.showChatModal = false
      this.currentChatProject = null
      this.currentChatPartner = null
      this.messages = []
      this.newMessage = ''
      document.body.style.overflow = ''
    },
    
    // Редактирование проекта
    editProject(project) {
      this.editingProject = { ...project }
      this.showEditModal = true
      document.body.style.overflow = 'hidden'
    },
    
    // Обновление проекта
    async updateProject() {
      if (!this.editingProject.title) {
        alert('Введите название проекта')
        return
      }
      
      const token = localStorage.getItem('token')
      
      if (!token) {
        return
      }
      
      this.projectUpdating = true
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/projects/edit`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(this.editingProject)
        })
        
        const result = await response.json()
        console.log('Результат обновления:', result)
        
        if (response.ok && result.success) {
          alert('Проект успешно обновлен')
          this.closeEditModal()
          await this.fetchMyProjects()
        } else {
          throw new Error(result.message || 'Ошибка при обновлении проекта')
        }
        
      } catch (error) {
        console.error('Ошибка при обновлении проекта:', error)
        alert('Ошибка при обновлении проекта: ' + error.message)
      } finally {
        this.projectUpdating = false
      }
    },
    
    // Закрыть модалку редактирования
    closeEditModal() {
      this.showEditModal = false
      this.editingProject = null
      document.body.style.overflow = ''
    },
    
    // Перейти к просмотру проекта
    viewProject(id) {
      this.$router.push(`/projects/${id}`)
    },
    
    // Перейти на страницу проектов
    goToProjects() {
      this.$router.push('/projects')
    },
    
    // Перейти на страницу логина
    goToLogin() {
      this.$router.push('/login')
    },
    
    // Переключение табов
    onTabChange(tab) {
      this.activeTab = tab
      if (tab === 'chats' && this.chats.length === 0) {
        this.fetchChats()
      }
    },
    
    // Форматирование бюджета
    formatBudget(budget) {
      if (!budget) return 'Не указан'
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 0
      }).format(budget)
    },
    
    // Форматирование даты
    formatDate(date) {
      if (!date) return 'Не указан'
      return new Date(date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },
    
    // Форматирование времени
    formatTime(time) {
      if (!time) return ''
      const date = new Date(time)
      const now = new Date()
      const diffMs = now - date
      const diffMins = Math.floor(diffMs / (1000 * 60))
      const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
      const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))
      
      if (diffMins < 1) return 'Только что'
      if (diffMins < 60) return `${diffMins} мин назад`
      if (diffHours < 24) return `${diffHours} ч назад`
      if (diffDays === 1) return 'Вчера'
      if (diffDays < 7) return `${diffDays} дн назад`
      
      return date.toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'short'
      })
    },
    
    // Статус проекта
    getStatusText(status) {
      const statusMap = {
        'open': 'Открыт',
        'in_progress': 'В работе',
        'completed': 'Завершен',
        'cancelled': 'Отменен'
      }
      return statusMap[status] || status || 'Открыт'
    },
    
    // Роль пользователя
    getRoleText(role) {
      const roles = {
        'freelancer': 'Фрилансер',
        'customer': 'Заказчик',
        'фрилансер': 'Фрилансер',
        'заказчик': 'Заказчик'
      }
      return roles[role] || role
    },
    
    // Категория проекта
    getCategoryName(categoryId) {
      if (!categoryId) return 'Без категории'
      const category = this.categories.find(c => c.id === categoryId)
      return category ? category.name : 'Без категории'
    },
    
    // Обрезка описания
    truncateDescription(description) {
      if (!description) return 'Нет описания'
      return description.length > 150 
        ? description.substring(0, 150) + '...' 
        : description
    },
    
    // Обрезка сообщения
    truncateMessage(message) {
      if (!message) return ''
      return message.length > 50 
        ? message.substring(0, 50) + '...' 
        : message
    },
    
    // Инициалы
    getInitials(name) {
      if (!name) return '?'
      return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
    }
  },
  
  watch: {
    activeTab: {
      handler(tab) {
        this.onTabChange(tab)
      },
      immediate: true
    }
  }
}
</script>

<style scoped>
/* Все стили остаются без изменений */
.my-projects-page {
  min-height: 100vh;
  padding: 40px 0 80px;
  position: relative;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 20;
  width: 100%;
}

.page-header {
  padding: 40px;
  margin-bottom: 30px;
  text-align: center;
}

.page-title {
  font-size: 2.8rem;
  font-weight: 700;
  margin-bottom: 10px;
  color: #FFFFFF;
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.page-subtitle {
  font-size: 1.2rem;
  color: #F0F8FF;
  opacity: 0.9;
}

.tabs-container {
  display: flex;
  gap: 10px;
  padding: 10px;
  margin-bottom: 30px;
  border-radius: 100px;
  background: rgba(10, 77, 140, 0.2);
}

.tab-button {
  flex: 1;
  padding: 16px 30px;
  border: none;
  border-radius: 100px;
  font-size: 1.1rem;
  font-weight: 600;
  color: #F0F8FF;
  background: transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.tab-button:hover {
  background: rgba(168, 209, 255, 0.1);
}

.tab-button.active {
  background: rgba(168, 209, 255, 0.2);
  box-shadow: 0 4px 15px rgba(168, 209, 255, 0.2);
  color: #FFFFFF;
}

.projects-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.project-card {
  padding: 30px;
  transition: all 0.3s ease;
}

.project-card:hover {
  transform: translateY(-2px);
  border-color: rgba(168, 209, 255, 0.4);
  box-shadow: 0 20px 40px rgba(8, 51, 88, 0.6);
}

.project-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  flex-wrap: wrap;
  gap: 15px;
}

.project-title {
  font-size: 1.6rem;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0;
}

.project-status {
  padding: 6px 16px;
  border-radius: 9999px;
  font-size: 0.9rem;
  font-weight: 600;
  text-transform: uppercase;
  background: rgba(10, 77, 140, 0.3);
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

.project-description {
  color: #F0F8FF;
  line-height: 1.6;
  margin-bottom: 20px;
  font-size: 1rem;
}

.project-meta {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 15px;
  margin-bottom: 20px;
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
}

.meta-value {
  color: #FFFFFF;
  font-size: 1rem;
}

.project-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

.partner-info {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.partner-label {
  color: #A8D1FF;
  font-size: 0.9rem;
}

.partner-details {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(10, 77, 140, 0.3);
  padding: 5px 12px;
  border-radius: 20px;
}

.partner-avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.9rem;
  color: #FFFFFF;
  border: 2px solid rgba(168, 209, 255, 0.3);
}

.partner-avatar.large {
  width: 50px;
  height: 50px;
  font-size: 1.2rem;
}

.partner-avatar.small {
  width: 30px;
  height: 30px;
  font-size: 0.9rem;
}

.partner-name {
  color: #FFFFFF;
  font-weight: 500;
}

.no-partner {
  color: #F0F8FF;
  opacity: 0.6;
  font-style: italic;
}

.project-actions {
  display: flex;
  gap: 10px;
}

.action-button {
  padding: 10px 20px;
  border: none;
  border-radius: 9999px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 5px;
}

.action-button.chat-button {
  background: rgba(52, 152, 219, 0.2);
  border: 1px solid rgba(52, 152, 219, 0.4);
  color: #3498db;
}

.action-button.chat-button:hover {
  background: rgba(52, 152, 219, 0.4);
  color: #FFFFFF;
}

.action-button.edit-button {
  background: rgba(241, 196, 15, 0.2);
  border: 1px solid rgba(241, 196, 15, 0.4);
  color: #f1c40f;
}

.action-button.edit-button:hover {
  background: rgba(241, 196, 15, 0.4);
  color: #FFFFFF;
}

.action-button.view-button {
  background: rgba(168, 209, 255, 0.1);
  border: 1px solid rgba(168, 209, 255, 0.3);
  color: #F0F8FF;
}

.action-button.view-button:hover {
  background: rgba(168, 209, 255, 0.2);
}

.action-button.primary {
  background: rgba(10, 77, 140, 0.6);
  border: 1px solid rgba(168, 209, 255, 0.4);
  color: #FFFFFF;
  padding: 15px 40px;
  font-size: 1.1rem;
}

.action-button.primary:hover {
  background: rgba(10, 77, 140, 0.8);
  transform: translateY(-2px);
}

.chats-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.chat-card {
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.chat-card:hover {
  transform: translateY(-2px);
  border-color: rgba(168, 209, 255, 0.4);
}

.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  flex-wrap: wrap;
  gap: 10px;
}

.chat-project-title {
  font-size: 1.3rem;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0;
}

.chat-project-status {
  padding: 4px 12px;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
  background: rgba(10, 77, 140, 0.3);
  border: 1px solid rgba(168, 209, 255, 0.2);
  color: #F0F8FF;
}

.chat-project-status.open {
  background: rgba(39, 174, 96, 0.2);
  border-color: rgba(39, 174, 96, 0.4);
  color: #2ecc71;
}

.chat-project-status.in_progress {
  background: rgba(241, 196, 15, 0.2);
  border-color: rgba(241, 196, 15, 0.4);
  color: #f1c40f;
}

.chat-partner {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
  padding: 10px;
  background: rgba(10, 77, 140, 0.2);
  border-radius: 15px;
}

.partner-info {
  flex: 1;
}

.partner-name {
  font-size: 1.2rem;
  font-weight: 600;
  color: #FFFFFF;
  margin-bottom: 4px;
}

.partner-role {
  font-size: 0.9rem;
  color: #A8D1FF;
}

.chat-last-message {
  background: rgba(255, 255, 255, 0.05);
  padding: 12px;
  border-radius: 12px;
  margin-bottom: 15px;
}

.chat-last-message.empty {
  opacity: 0.6;
}

.message-time {
  font-size: 0.8rem;
  color: #A8D1FF;
  display: block;
  margin-bottom: 4px;
}

.message-preview {
  color: #F0F8FF;
  font-size: 0.95rem;
  margin: 0;
  opacity: 0.9;
}

.chat-actions {
  display: flex;
  justify-content: flex-end;
}

.chat-open {
  background: transparent;
  border: 1px solid rgba(168, 209, 255, 0.3);
  color: #A8D1FF;
  padding: 8px 16px;
  border-radius: 9999px;
  font-size: 0.9rem;
}

.chat-modal {
  width: 100%;
  max-width: 800px;
  height: 80vh;
  display: flex;
  flex-direction: column;
  background: rgba(10, 25, 45, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 28px;
  overflow: hidden;
}

.chat-modal-header {
  padding: 20px 30px;
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chat-modal-project h3 {
  font-size: 1.3rem;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0 0 10px 0;
}

.chat-modal-partner {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #F0F8FF;
}

.partner-role-badge {
  background: rgba(168, 209, 255, 0.2);
  padding: 2px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
  color: #A8D1FF;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 30px;
  display: flex;
  flex-direction: column;
}

.messages-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.message-item {
  display: flex;
  gap: 15px;
  max-width: 70%;
}

.message-item.my-message {
  align-self: flex-end;
  flex-direction: row-reverse;
}

.message-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: #FFFFFF;
  flex-shrink: 0;
}

.message-content {
  background: rgba(10, 77, 140, 0.2);
  padding: 12px 16px;
  border-radius: 18px;
  border: 1px solid rgba(168, 209, 255, 0.1);
}

.message-item.my-message .message-content {
  background: rgba(52, 152, 219, 0.3);
}

.message-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 5px;
  gap: 15px;
}

.message-author {
  font-weight: 600;
  color: #FFFFFF;
}

.message-time {
  font-size: 0.8rem;
  color: #A8D1FF;
}

.message-text {
  color: #F0F8FF;
  line-height: 1.5;
  margin: 0;
  word-break: break-word;
}

.no-messages {
  text-align: center;
  color: #F0F8FF;
  opacity: 0.6;
  padding: 40px;
}

.chat-input-area {
  padding: 20px 30px;
  border-top: 1px solid rgba(168, 209, 255, 0.1);
  display: flex;
  gap: 15px;
  background: rgba(10, 25, 45, 0.8);
}

.chat-input {
  flex: 1;
  padding: 12px 18px;
  border-radius: 20px;
  border: 1px solid rgba(168, 209, 255, 0.2);
  background: rgba(10, 77, 140, 0.2);
  color: #FFFFFF;
  font-size: 1rem;
  resize: none;
  font-family: inherit;
}

.chat-input:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.4);
}

.send-button {
  padding: 0 30px;
  border-radius: 9999px;
  border: 1px solid rgba(168, 209, 255, 0.3);
  background: rgba(10, 77, 140, 0.6);
  color: #FFFFFF;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 120px;
}

.send-button:hover:not(:disabled) {
  background: rgba(10, 77, 140, 0.8);
  border-color: rgba(168, 209, 255, 0.6);
}

.send-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
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

.loader-sm {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
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
  opacity: 0.8;
}

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

@media (max-width: 1024px) {
  .page-title {
    font-size: 2.4rem;
  }
  
  .project-card {
    padding: 25px;
  }
}

@media (max-width: 768px) {
  .page-header {
    padding: 30px 20px;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .tabs-container {
    border-radius: 50px;
  }
  
  .tab-button {
    padding: 12px 20px;
    font-size: 1rem;
  }
  
  .project-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .project-meta {
    grid-template-columns: 1fr;
  }
  
  .project-footer {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .project-actions {
    width: 100%;
    flex-wrap: wrap;
  }
  
  .action-button {
    flex: 1;
    justify-content: center;
  }
  
  .chat-modal {
    height: 90vh;
  }
  
  .message-item {
    max-width: 85%;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .page-header {
    padding: 20px 15px;
  }
  
  .page-title {
    font-size: 1.8rem;
  }
  
  .project-card {
    padding: 20px;
  }
  
  .chat-modal-header {
    padding: 15px 20px;
  }
  
  .chat-messages {
    padding: 20px 15px;
  }
  
  .chat-input-area {
    padding: 15px 20px;
    flex-direction: column;
  }
  
  .send-button {
    padding: 12px;
    width: 100%;
  }
  
  .message-item {
    max-width: 95%;
  }
}
</style>