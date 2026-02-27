<!-- resources/js/components/Profile.vue -->
<template>
  <div class="profile-page">
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
      <div v-if="loading" class="loading-state">
        <div class="loader ios-glass">
          <div class="loader-spinner"></div>
          <p>Загрузка профиля...</p>
        </div>
      </div>

      <div v-else-if="user" class="profile-content">
        <!-- Заголовок профиля -->
        <div class="profile-header ios-glass">
          <div class="profile-avatar-section">
            <div class="profile-avatar-large">
              <img v-if="user.avatar" :src="user.avatar" :alt="user.full_name" />
              <span v-else class="avatar-placeholder">{{ getInitials(user.full_name) }}</span>
            </div>
            <div class="profile-info">
              <h1 class="profile-name">{{ user.full_name || user.login }}</h1>
              <div class="profile-role">{{ getRoleText(user.role) }}</div>
              <div class="profile-login">@{{ user.login }}</div>
              <div class="profile-phone" v-if="user.phone">{{ user.phone }}</div>
            </div>
          </div>

          <!-- Баланс -->
          <div class="profile-balance-card ios-glass">
            <div class="balance-label">Текущий баланс</div>
            <div class="balance-amount">{{ formatBalance(user.balance || 0) }}</div>
            <div class="balance-actions">
              <button @click="showDepositModal = true" class="balance-button deposit">
                <span class="button-icon">💰</span>
                Пополнить
              </button>
              <button @click="showWithdrawModal = true" class="balance-button withdraw">
                <span class="button-icon">💸</span>
                Вывести
              </button>
              <button @click="openSendModal" class="balance-button send">
                <span class="button-icon">📤</span>
                Отправить
              </button>
            </div>
          </div>
        </div>

        <!-- Табы -->
        <div class="tabs-container ios-glass">
          <button 
            class="tab-button" 
            :class="{ active: activeTab === 'info' }"
            @click="activeTab = 'info'"
          >
            📋 Информация
          </button>
          <button 
            class="tab-button" 
            :class="{ active: activeTab === 'edit' }"
            @click="activeTab = 'edit'"
          >
            ✏️ Редактировать
          </button>
          <button 
            class="tab-button" 
            :class="{ active: activeTab === 'payments' }"
            @click="activeTab = 'payments'"
          >
            💳 Платежи
          </button>
        </div>

        <!-- Вкладка с информацией -->
        <div v-if="activeTab === 'info'" class="info-tab ios-glass">
          <div class="info-section">
            <h3>Личная информация</h3>
            <div class="info-grid">
              <div class="info-item">
                <span class="info-label">Полное имя:</span>
                <span class="info-value">{{ user.full_name || 'Не указано' }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Логин:</span>
                <span class="info-value">{{ user.login }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Телефон:</span>
                <span class="info-value">{{ user.phone || 'Не указан' }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Роль:</span>
                <span class="info-value">{{ getRoleText(user.role) }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Дата регистрации:</span>
                <span class="info-value">{{ formatDate(user.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Вкладка с редактированием -->
        <div v-if="activeTab === 'edit'" class="edit-tab ios-glass">
          <h3>Редактирование профиля</h3>
          
          <form @submit.prevent="updateProfile" class="edit-form">
            <div class="form-group">
              <label>Полное имя</label>
              <input 
                v-model="editForm.full_name" 
                type="text" 
                class="form-input ios-glass"
                placeholder="Введите полное имя"
              />
            </div>
            
            <div class="form-group">
              <label>Телефон</label>
              <input 
                v-model="editForm.phone" 
                type="text" 
                class="form-input ios-glass"
                placeholder="Введите номер телефона"
              />
            </div>
            
            <!-- <div class="form-group">
              <label>Аватар (URL)</label>
              <input 
                v-model="editForm.avatar" 
                type="text" 
                class="form-input ios-glass"
                placeholder="https://example.com/avatar.jpg"
              />
            </div> -->
            
            <div class="form-group">
              <label>Новый пароль (оставьте пустым, если не хотите менять)</label>
              <input 
                v-model="editForm.password" 
                type="password" 
                class="form-input ios-glass"
                placeholder="Введите новый пароль"
              />
            </div>
            
            <div class="form-group">
              <label>Подтверждение пароля</label>
              <input 
                v-model="editForm.password_confirmation" 
                type="password" 
                class="form-input ios-glass"
                placeholder="Подтвердите новый пароль"
              />
            </div>
            
            <div class="form-actions">
              <button type="submit" class="submit-button" :disabled="updating">
                <span v-if="updating" class="button-spinner"></span>
                {{ updating ? 'Сохранение...' : 'Сохранить изменения' }}
              </button>
            </div>
            
            <div v-if="updateMessage" class="form-message" :class="updateMessageType">
              {{ updateMessage }}
            </div>
          </form>
        </div>

        <!-- Вкладка с платежами -->
        <div v-if="activeTab === 'payments'" class="payments-tab">
          <!-- <div class="payments-summary ios-glass">
            <div class="summary-item">
              <span class="summary-label">Отправлено:</span>
              <span class="summary-value">{{ formatBalance(totalSent) }}</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Получено:</span>
              <span class="summary-value">{{ formatBalance(totalReceived) }}</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Комиссия (5%):</span>
              <span class="summary-value">{{ formatBalance(totalCommission) }}</span>
            </div>
          </div> -->

          <div class="payments-lists">
            <div class="payments-section ios-glass">
              <h4>📤 Исходящие платежи</h4>
              <div v-if="sentPayments.length > 0" class="payments-list">
                <div v-for="payment in sentPayments" :key="payment.id" class="payment-item">
                  <div class="payment-icon">📤</div>
                  <div class="payment-details">
                    <div class="payment-header">
                      <span class="payment-recipient">{{ payment.freelancer?.full_name || payment.freelancer?.login }}</span>
                      <span class="payment-amount">-{{ formatBalance(payment.amount) }}</span>
                    </div>
                    <div class="payment-meta">
                      <span class="payment-date">{{ formatDate(payment.created_at) }}</span>
                      <span class="payment-commission">Комиссия: {{ formatBalance(payment.commission) }}</span>
                      <span class="payment-status" :class="payment.status">{{ getPaymentStatus(payment.status) }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="no-payments">
                Нет исходящих платежей
              </div>
            </div>

            <div class="payments-section ios-glass">
              <h4>📥 Входящие платежи</h4>
              <div v-if="receivedPayments.length > 0" class="payments-list">
                <div v-for="payment in receivedPayments" :key="payment.id" class="payment-item">
                  <div class="payment-icon">📥</div>
                  <div class="payment-details">
                    <div class="payment-header">
                      <span class="payment-sender">{{ payment.customer?.full_name || payment.customer?.login }}</span>
                      <span class="payment-amount positive">+{{ formatBalance(payment.amount) }}</span>
                    </div>
                    <div class="payment-meta">
                      <span class="payment-date">{{ formatDate(payment.created_at) }}</span>
                      <span class="payment-status" :class="payment.status">{{ getPaymentStatus(payment.status) }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="no-payments">
                Нет входящих платежей
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно пополнения -->
    <div v-if="showDepositModal" class="modal-overlay" @click.self="closeModals">
      <div class="modal-container ios-glass">
        <div class="modal-header">
          <h2 class="modal-title">Пополнение баланса</h2>
          <button @click="closeModals" class="modal-close">×</button>
        </div>
        
        <form @submit.prevent="depositMoney" class="modal-form">
          <div class="form-group">
            <label>Сумма пополнения (₽)</label>
            <input 
              v-model.number="transactionAmount" 
              type="number" 
              class="form-input ios-glass"
              placeholder="Введите сумму"
              min="1"
              required
            />
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="closeModals" class="modal-button cancel">
              Отмена
            </button>
            <button type="submit" class="modal-button submit" :disabled="transactionProcessing">
              <span v-if="transactionProcessing" class="button-spinner"></span>
              {{ transactionProcessing ? 'Обработка...' : 'Пополнить' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Модальное окно вывода -->
    <div v-if="showWithdrawModal" class="modal-overlay" @click.self="closeModals">
      <div class="modal-container ios-glass">
        <div class="modal-header">
          <h2 class="modal-title">Вывод средств</h2>
          <button @click="closeModals" class="modal-close">×</button>
        </div>
        
        <form @submit.prevent="withdrawMoney" class="modal-form">
          <div class="form-group">
            <label>Сумма вывода (₽)</label>
            <input 
              v-model.number="transactionAmount" 
              type="number" 
              class="form-input ios-glass"
              placeholder="Введите сумму"
              min="1"
              :max="user?.balance"
              required
            />
          </div>
          <div class="form-hint">Доступно: {{ formatBalance(user?.balance || 0) }}</div>
          
          <div class="modal-actions">
            <button type="button" @click="closeModals" class="modal-button cancel">
              Отмена
            </button>
            <button type="submit" class="modal-button submit" :disabled="transactionProcessing">
              <span v-if="transactionProcessing" class="button-spinner"></span>
              {{ transactionProcessing ? 'Обработка...' : 'Вывести' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Модальное окно отправки с выпадающим списком -->
    <div v-if="showSendModal" class="modal-overlay" @click.self="closeModals">
      <div class="modal-container ios-glass">
        <div class="modal-header">
          <h2 class="modal-title">Отправка денег</h2>
          <button @click="closeModals" class="modal-close">×</button>
        </div>
        
        <form @submit.prevent="sendMoney" class="modal-form">
          <div class="form-group">
            <label>Получатель</label>
            <div class="select-wrapper">
              <select v-model="selectedReceiver" class="form-input ios-glass" required>
                <option value="" disabled selected>Выберите получателя</option>
                <option v-for="receiver in availableUsers" :key="receiver.id" :value="receiver">
                  {{ receiver.full_name || receiver.login }} (ID: {{ receiver.id }}) - {{ getRoleText(receiver.role) }}
                </option>
              </select>
              <span class="select-arrow">▼</span>
            </div>
            <div v-if="usersLoading" class="select-loading">
              <span class="loader-sm"></span> Загрузка пользователей...
            </div>
          </div>
          
          <div class="form-group">
            <label>Сумма перевода (₽)</label>
            <input 
              v-model.number="transactionAmount" 
              type="number" 
              class="form-input ios-glass"
              placeholder="Введите сумму"
              min="1"
              required
            />
          </div>
          
          <div class="commission-info" v-if="selectedReceiver && transactionAmount > 0">
            <div class="info-row">
              <span>Получатель:</span>
              <span class="receiver-name">{{ selectedReceiver.full_name || selectedReceiver.login }}</span>
            </div>
            <div class="info-row">
              <span>Сумма перевода:</span>
              <span>{{ formatBalance(transactionAmount) }}</span>
            </div>
            <div class="info-row">
              <span>Комиссия (5%):</span>
              <span>{{ formatBalance(transactionAmount * 0.05) }}</span>
            </div>
            <div class="info-row total">
              <span>Итого к списанию:</span>
              <span>{{ formatBalance(transactionAmount * 1.05) }}</span>
            </div>
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="closeModals" class="modal-button cancel">
              Отмена
            </button>
            <button type="submit" class="modal-button submit" :disabled="transactionProcessing || !selectedReceiver">
              <span v-if="transactionProcessing" class="button-spinner"></span>
              {{ transactionProcessing ? 'Обработка...' : 'Отправить' }}
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
  name: 'ProfilePage',
  
  components: {
    HeaderMenu,
    FooterApp
  },
  
  data() {
    return {
      user: null,
      users: [], // Список всех пользователей для выпадающего списка
      availableUsers: [], // Отфильтрованный список (без текущего пользователя)
      usersLoading: false,
      loading: true,
      updating: false,
      activeTab: 'info',
      
      editForm: {
        full_name: '',
        phone: '',
        avatar: '',
        password: '',
        password_confirmation: ''
      },
      
      updateMessage: '',
      updateMessageType: '',
      
      // Платежи
      sentPayments: [],
      receivedPayments: [],
      
      // Модальные окна
      showDepositModal: false,
      showWithdrawModal: false,
      showSendModal: false,
      
      transactionAmount: null,
      selectedReceiver: null, // Выбранный получатель (объект пользователя)
      transactionProcessing: false,
      
      apiBaseUrl: ''
    }
  },
  
  computed: {
    totalSent() {
      return this.sentPayments.reduce((sum, p) => sum + p.amount, 0)
    },
    
    totalReceived() {
      return this.receivedPayments.reduce((sum, p) => sum + p.amount, 0)
    },
    
    totalCommission() {
      return this.sentPayments.reduce((sum, p) => sum + p.commission, 0)
    }
  },
  
  created() {
    this.fetchProfile()
  },
  
  methods: {
    async fetchProfile() {
      const token = localStorage.getItem('token')
      
      if (!token) {
        this.$router.push('/login')
        return
      }
      
      try {
        let response = await fetch(`${this.apiBaseUrl}/api/user`, {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        })

        
        
        let data1 = await response.json()
        response = await fetch(`${this.apiBaseUrl}/api/profil/${data1.user.id}`, {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        })
        const data2 = await response.json()
        console.log(data2)
        if (response.ok && data2.success) {
          this.user = data1.user
          this.sentPayments = data2.sent_payments || []
          this.receivedPayments = data2.received_payments || []
          
          this.editForm = {
            full_name: this.user.full_name || '',
            phone: this.user.phone || '',
            avatar: this.user.avatar || '',
            password: '',
            password_confirmation: ''
          }
        } else {
          localStorage.removeItem('token')
          this.$router.push('/login')
        }
      } catch (error) {
        console.error('Ошибка загрузки профиля:', error)
      } finally {
        this.loading = false
      }
    },
    
    // Загрузка списка пользователей для выпадающего списка
    async fetchUsers() {
      this.usersLoading = true
      const token = localStorage.getItem('token')
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/users`, {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        })
        
        const data = await response.json()
        
        if (response.ok) {
          // Сохраняем всех пользователей
          this.users = Array.isArray(data) ? data : (data.users || [])
          
          // Фильтруем, исключая текущего пользователя
          this.availableUsers = this.users.filter(u => u.id !== this.user?.id)
          
          console.log('Загружены пользователи:', this.users)
          console.log('Доступные получатели:', this.availableUsers)
        }
      } catch (error) {
        console.error('Ошибка загрузки пользователей:', error)
      } finally {
        this.usersLoading = false
      }
    },
    
    // Открыть модальное окно отправки и загрузить пользователей
    openSendModal() {
      this.showSendModal = true
      this.selectedReceiver = null
      this.transactionAmount = null
      this.fetchUsers() // Загружаем список пользователей
    },
    
    async updateProfile() {
      this.updating = true
      this.updateMessage = ''
      
      const token = localStorage.getItem('token')
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/profile/update`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(this.editForm)
        })
        
        const data = await response.json()
        
        if (response.ok && data.success) {
          this.user = data.user
          this.updateMessage = data.message
          this.updateMessageType = 'success'
          
          // Очищаем поля пароля
          this.editForm.password = ''
          this.editForm.password_confirmation = ''
        } else {
          throw new Error(data.message || 'Ошибка обновления')
        }
      } catch (error) {
        console.error('Ошибка обновления:', error)
        this.updateMessage = error.message
        this.updateMessageType = 'error'
      } finally {
        this.updating = false
      }
    },
    
    async depositMoney() {
      if (!this.transactionAmount || this.transactionAmount < 1) {
        alert('Введите корректную сумму')
        return
      }
      
      this.transactionProcessing = true
      const token = localStorage.getItem('token')
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/profile/deposit`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({ amount: this.transactionAmount })
        })
        
        const data = await response.json()
        
        if (response.ok && data.success) {
          this.user.balance = data.balance
          alert(data.message)
          this.closeModals()
        } else {
          throw new Error(data.message || 'Ошибка пополнения')
        }
      } catch (error) {
        console.error('Ошибка:', error)
        alert(error.message)
      } finally {
        this.transactionProcessing = false
      }
    },
    
    async withdrawMoney() {
      if (!this.transactionAmount || this.transactionAmount < 1) {
        alert('Введите корректную сумму')
        return
      }
      
      if (this.transactionAmount > this.user.balance) {
        alert('Недостаточно средств')
        return
      }
      
      this.transactionProcessing = true
      const token = localStorage.getItem('token')
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/profile/withdraw`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({ amount: this.transactionAmount })
        })
        
        const data = await response.json()
        
        if (response.ok && data.success) {
          this.user.balance = data.balance
          alert(data.message)
          this.closeModals()
        } else {
          throw new Error(data.message || 'Ошибка вывода')
        }
      } catch (error) {
        console.error('Ошибка:', error)
        alert(error.message)
      } finally {
        this.transactionProcessing = false
      }
    },
    
    async sendMoney() {
      if (!this.selectedReceiver) {
        alert('Выберите получателя')
        return
      }
      
      if (!this.transactionAmount || this.transactionAmount < 1) {
        alert('Введите корректную сумму')
        return
      }
      
      const total = this.transactionAmount * 1.05
      if (total > this.user.balance) {
        alert(`Недостаточно средств. Нужно: ${this.formatBalance(total)}`)
        return
      }
      
      this.transactionProcessing = true
      const token = localStorage.getItem('token')
      
      // Отправляем данные с receiver_id (ID получателя)
      const requestData = {
        receiver_id: this.selectedReceiver.id, // Используем ID выбранного пользователя
        amount: this.transactionAmount
      }
      
      console.log('Отправка запроса:', requestData)
      
      try {
        const response = await fetch(`${this.apiBaseUrl}/api/profile/send-money/${this.user.id}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(requestData)
        })
        
        const data = await response.json()
        console.log('Ответ сервера:', data)
        
        if (response.ok && data.success) {
          this.user.balance = data.balance
          
          if (data.payment) {
            this.sentPayments.unshift(data.payment)
          }
          
          alert(data.message)
          this.closeModals()
          await this.fetchProfile()
        } else {
          throw new Error(data.message || 'Ошибка перевода')
        }
      } catch (error) {
        console.error('Ошибка:', error)
        alert('Ошибка: ' + error.message)
      } finally {
        this.transactionProcessing = false
      }
    },
    
    closeModals() {
      this.showDepositModal = false
      this.showWithdrawModal = false
      this.showSendModal = false
      this.transactionAmount = null
      this.selectedReceiver = null
    },
    
    formatBalance(balance) {
      if (balance === null || balance === undefined) return '0 ₽'
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 2
      }).format(balance)
    },
    
    formatDate(date) {
      if (!date) return 'Не указано'
      return new Date(date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },
    
    getInitials(name) {
      if (!name) return '?'
      return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
    },
    
    getRoleText(role) {
      const roles = {
        'freelancer': 'Фрилансер',
        'customer': 'Заказчик',
        'admin': 'Админ',
        'фрилансер': 'Фрилансер',
        'заказчик': 'Заказчик'
      }
      return roles[role] || role
    },
    
    getPaymentStatus(status) {
      const statuses = {
        'frozen': 'Заморожен',
        'paid': 'Выплачен',
        'refunded': 'Возвращен'
      }
      return statuses[status] || status
    }
  }
}
</script>

<style scoped>
.select-wrapper {
  position: relative;
  width: 100%;
}
.container {
  flex: 1 0 auto;          /* ← ключевой момент */
  display: flex;
  flex-direction: column;
  /* padding: 0 20px; уже есть — можно оставить */
}

.select-wrapper select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  width: 100%;
  padding: 14px 18px;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 12px;
  color: #FFFFFF;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.select-wrapper select:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.4);
  background: rgba(10, 77, 140, 0.3);
}

.select-wrapper select option {
  background: #0A1A2A;
  color: #FFFFFF;
  padding: 10px;
}

.select-arrow {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(168, 209, 255, 0.6);
  font-size: 0.8rem;
  pointer-events: none;
}

.select-loading {
  margin-top: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
  color: #A8D1FF;
  font-size: 0.9rem;
}

.receiver-name {
  font-weight: 600;
  color: #FFFFFF;
}
.profile-page {
  min-height: 100vh;
  padding: 40px 0 80px;
  position: relative;
    padding-bottom: 0; 
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 20;
  width: 100%;
}

.profile-header {
  padding: 40px;
  margin-bottom: 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 30px;
}

.profile-avatar-section {
  display: flex;
  align-items: center;
  gap: 30px;
  flex-wrap: wrap;
}

.profile-avatar-large {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  overflow: hidden;
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  border: 4px solid rgba(168, 209, 255, 0.3);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.profile-avatar-large img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  font-weight: 700;
  color: #FFFFFF;
}

.profile-info {
  flex: 1;
}

.profile-name {
  font-size: 2.5rem;
  font-weight: 700;
  color: #FFFFFF;
  margin: 0 0 10px 0;
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.profile-role {
  display: inline-block;
  background: rgba(10, 77, 140, 0.3);
  color: #A8D1FF;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 0.9rem;
  font-weight: 500;
  margin-bottom: 8px;
}

.profile-login {
  color: #F0F8FF;
  font-size: 1.1rem;
  opacity: 0.9;
  margin-bottom: 4px;
}

.profile-phone {
  color: #F0F8FF;
  font-size: 1rem;
  opacity: 0.8;
}

.profile-balance-card {
  padding: 25px 30px;
  min-width: 300px;
  background: rgba(10, 77, 140, 0.3);
  border: 1px solid rgba(168, 209, 255, 0.2);
}

.balance-label {
  color: #A8D1FF;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 8px;
}

.balance-amount {
  font-size: 2.5rem;
  font-weight: 700;
  color: #FFFFFF;
  margin-bottom: 20px;
  text-shadow: 0 2px 10px rgba(168, 209, 255, 0.3);
}

.balance-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.balance-button {
  flex: 1;
  padding: 12px 15px;
  border: none;
  border-radius: 9999px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  min-width: 100px;
}

.balance-button.deposit {
  background: rgba(46, 204, 113, 0.2);
  border: 1px solid rgba(46, 204, 113, 0.4);
  color: #2ecc71;
}

.balance-button.deposit:hover {
  background: rgba(46, 204, 113, 0.4);
  color: #FFFFFF;
}

.balance-button.withdraw {
  background: rgba(241, 196, 15, 0.2);
  border: 1px solid rgba(241, 196, 15, 0.4);
  color: #f1c40f;
}

.balance-button.withdraw:hover {
  background: rgba(241, 196, 15, 0.4);
  color: #FFFFFF;
}

.balance-button.send {
  background: rgba(52, 152, 219, 0.2);
  border: 1px solid rgba(52, 152, 219, 0.4);
  color: #3498db;
}

.balance-button.send:hover {
  background: rgba(52, 152, 219, 0.4);
  color: #FFFFFF;
}

.button-icon {
  font-size: 1.1rem;
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
}

.tab-button:hover {
  background: rgba(168, 209, 255, 0.1);
}

.tab-button.active {
  background: rgba(168, 209, 255, 0.2);
  box-shadow: 0 4px 15px rgba(168, 209, 255, 0.2);
  color: #FFFFFF;
}

.info-tab,
.edit-tab {
  padding: 40px;
}

.info-tab h3,
.edit-tab h3 {
  font-size: 1.8rem;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0 0 30px 0;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
  padding: 20px;
  background: rgba(10, 77, 140, 0.1);
  border-radius: 16px;
  border: 1px solid rgba(168, 209, 255, 0.1);
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

.edit-form {
  max-width: 600px;
  margin: 0 auto;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  color: #F0F8FF;
  font-size: 0.95rem;
  font-weight: 500;
  margin-bottom: 8px;
}

.form-input {
  width: 100%;
  padding: 14px 18px;
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

.form-actions {
  display: flex;
  justify-content: center;
  margin-top: 30px;
}

.submit-button {
  padding: 16px 40px;
  border: none;
  border-radius: 9999px;
  font-size: 1.1rem;
  font-weight: 600;
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  color: #FFFFFF;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  min-width: 250px;
}

.submit-button:hover:not(:disabled) {
  background: linear-gradient(135deg, #1A5D9C, #2A7BC3);
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(10, 77, 140, 0.4);
}

.submit-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.form-message {
  margin-top: 20px;
  padding: 12px 20px;
  border-radius: 12px;
  text-align: center;
}

.form-message.success {
  background: rgba(46, 204, 113, 0.2);
  border: 1px solid rgba(46, 204, 113, 0.4);
  color: #2ecc71;
}

.form-message.error {
  background: rgba(231, 76, 60, 0.2);
  border: 1px solid rgba(231, 76, 60, 0.4);
  color: #e74c3c;
}

.payments-summary {
  padding: 30px;
  margin-bottom: 30px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.summary-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 20px;
  background: rgba(10, 77, 140, 0.2);
  border-radius: 16px;
}

.summary-label {
  color: #A8D1FF;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.summary-value {
  color: #FFFFFF;
  font-size: 1.6rem;
  font-weight: 700;
}

.payments-lists {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

.payments-section {
  padding: 30px;
}

.payments-section h4 {
  font-size: 1.3rem;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0 0 20px 0;
  padding-bottom: 10px;
  border-bottom: 1px solid rgba(168, 209, 255, 0.2);
}

.payments-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
  max-height: 500px;
  overflow-y: auto;
  padding-right: 10px;
}

.payment-item {
  display: flex;
  gap: 15px;
  padding: 15px;
  background: rgba(10, 77, 140, 0.1);
  border-radius: 12px;
  border: 1px solid rgba(168, 209, 255, 0.1);
  transition: all 0.3s ease;
}

.payment-item:hover {
  background: rgba(10, 77, 140, 0.2);
  border-color: rgba(168, 209, 255, 0.2);
}

.payment-icon {
  font-size: 1.5rem;
  line-height: 1;
}

.payment-details {
  flex: 1;
}

.payment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 5px;
}

.payment-recipient,
.payment-sender {
  color: #FFFFFF;
  font-weight: 600;
  font-size: 1rem;
}

.payment-amount {
  color: #e74c3c;
  font-weight: 700;
}

.payment-amount.positive {
  color: #2ecc71;
}

.payment-meta {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  font-size: 0.85rem;
}

.payment-date {
  color: #A8D1FF;
  opacity: 0.8;
}

.payment-commission {
  color: #f1c40f;
}

.payment-status {
  padding: 2px 8px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.1);
}

.payment-status.frozen {
  color: #f1c40f;
}

.payment-status.paid {
  color: #2ecc71;
}

.payment-status.refunded {
  color: #e74c3c;
}

.no-payments {
  text-align: center;
  color: #F0F8FF;
  opacity: 0.6;
  padding: 30px;
  font-style: italic;
}

.modal-container {
  max-width: 500px;
  width: 100%;
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
  margin: 0;
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

.form-hint {
  color: #A8D1FF;
  font-size: 0.9rem;
  margin-top: -10px;
}

.commission-info {
  padding: 15px;
  background: rgba(10, 77, 140, 0.2);
  border-radius: 12px;
  border: 1px solid rgba(168, 209, 255, 0.2);
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  color: #F0F8FF;
}

.info-row:not(:last-child) {
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
}

.info-row.total {
  font-weight: 700;
  color: #FFFFFF;
  font-size: 1.1rem;
  margin-top: 5px;
  padding-top: 10px;
  border-top: 2px solid rgba(168, 209, 255, 0.2);
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

.button-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
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
  .profile-header {
    padding: 30px;
  }
  
  .profile-name {
    font-size: 2rem;
  }
  
  .balance-amount {
    font-size: 2rem;
  }
}

@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    align-items: stretch;
  }
  
  .profile-avatar-section {
    justify-content: center;
    text-align: center;
  }
  
  .profile-balance-card {
    width: 100%;
  }
  
  .tabs-container {
    border-radius: 50px;
  }
  
  .tab-button {
    padding: 12px 20px;
    font-size: 1rem;
  }
  
  .info-tab,
  .edit-tab {
    padding: 30px 20px;
  }
  
  .payments-lists {
    grid-template-columns: 1fr;
  }
  
  .balance-actions {
    flex-direction: column;
  }
  
  .balance-button {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .profile-header {
    padding: 20px;
  }
  
  .profile-avatar-large {
    width: 100px;
    height: 100px;
  }
  
  .profile-name {
    font-size: 1.6rem;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .payment-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }
  
  .payment-meta {
    flex-direction: column;
    gap: 5px;
  }
  
  .modal-actions {
    flex-direction: column;
  }
  
  .modal-button {
    width: 100%;
  }
}
</style>