<template>
  <div class="login-container">
    <div class="login-card ios-glass ios-glass-heavy">
      <h2 class="login-title">Вход в аккаунт</h2>
      
      <!-- Форма авторизации -->
      <form @submit.prevent="handleLogin" class="login-form">
        <!-- Поле логина -->
        <div class="form-group">
          <label for="login" class="form-label">Логин</label>
          <input
            type="text"
            id="login"
            v-model="form.login"
            class="form-input ios-glass"
            :class="{ 'error': errors.login }"
            placeholder="Введите логин"
            @blur="validateField('login')"
          />
          <span v-if="errors.login" class="error-message">{{ errors.login }}</span>
        </div>

        <!-- Поле пароля -->
        <div class="form-group">
          <label for="password" class="form-label">Пароль</label>
          <div class="password-input-wrapper">
            <input
              :type="showPassword ? 'text' : 'password'"
              id="password"
              v-model="form.password"
              class="form-input ios-glass"
              :class="{ 'error': errors.password }"
              placeholder="Введите пароль"
              @blur="validateField('password')"
            />
            <button 
              type="button" 
              class="password-toggle"
              @click="showPassword = !showPassword"
            >
              <span v-if="showPassword">👁️</span>
              <span v-else>👁️‍🗨️</span>
            </button>
          </div>
          <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
        </div>

        <!-- Запомнить меня -->
        <div class="form-group">
          <label class="checkbox-label">
            <input 
              type="checkbox" 
              v-model="form.remember"
              class="checkbox"
            />
            <span class="checkbox-text">Запомнить меня</span>
          </label>
        </div>

        <!-- Сообщение об ошибке -->
        <div v-if="authError" class="alert alert-error">
          {{ authError }}
        </div>

        <!-- Кнопка входа -->
        <button 
          type="submit" 
          class="login-button ios-glass ios-glass-heavy"
          :disabled="isLoading || !isFormValid"
        >
          <span v-if="!isLoading">Войти</span>
          <span v-else class="loader"></span>
        </button>
      </form>

      <!-- Ссылка на регистрацию -->
      <div class="register-link">
        <p>Ещё нет аккаунта?</p>
        <router-link to="/register" class="register-button">
          Зарегистрироваться
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const API_BASE_URL = '/api'

const form = reactive({
  login: '',
  password: '',
  remember: false
})

const showPassword = ref(false)
const isLoading = ref(false)
const authError = ref('')

const errors = reactive({
  login: '',
  password: ''
})

const validateField = () => {
  // ... ваша валидация
}

const isFormValid = computed(() => {
  return form.login && form.password && !errors.login && !errors.password
})

const handleLogin = async () => {
  validateField('login')
  validateField('password')
  
  if (!isFormValid.value) return

  isLoading.value = true
  authError.value = ''

  try {
    const response = await fetch(`${API_BASE_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        login: form.login,
        password: form.password
      })
    })

    const data = await response.json()

    if (response.ok && data.success) {
      localStorage.setItem('token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))
      const redirectPath = route.query.redirect || '/'
      await router.push(redirectPath)
      
    } else {
      authError.value = data.message || 'Ошибка авторизации'
    }

  } catch (error) {
    console.error('Ошибка входа:', error)
    authError.value = `Ошибка: ${error.message}`
  } finally {
    isLoading.value = false
  }
}

// Автозаполнение
const savedLogin = localStorage.getItem('remember_user')
if (savedLogin) {
  form.login = savedLogin
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  position: relative;
  z-index: 10;
}

.login-card {
  width: 100%;
  max-width: 450px;
  padding: 50px 40px;
  background: rgba(10, 77, 140, 0.15);
  backdrop-filter: blur(25px) saturate(180%);
  -webkit-backdrop-filter: blur(25px) saturate(180%);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 32px;
  box-shadow: 0 20px 40px rgba(8, 51, 88, 0.5);
}

.login-title {
  font-size: 2.2rem;
  font-weight: 700;
  color: #FFFFFF;
  text-align: center;
  margin-bottom: 40px;
  text-shadow: 0 2px 10px rgba(168, 209, 255, 0.3);
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-label {
  color: #F0F8FF;
  font-size: 1rem;
  font-weight: 500;
  margin-left: 5px;
}

.form-input {
  width: 100%;
  padding: 15px 20px;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 16px;
  font-size: 1rem;
  color: #FFFFFF;
  transition: all 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.5);
  box-shadow: 0 0 20px rgba(168, 209, 255, 0.2);
}

.form-input.error {
  border-color: #ff6b6b;
  background: rgba(255, 107, 107, 0.1);
}

.password-input-wrapper {
  position: relative;
  width: 100%;
}

.password-toggle {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  color: #F0F8FF;
  font-size: 1.2rem;
}

.error-message {
  color: #ff6b6b;
  font-size: 0.9rem;
  margin-left: 5px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  color: #F0F8FF;
  font-size: 0.95rem;
}

.checkbox {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #1A6BB3;
}

.checkbox-text {
  font-size: 0.95rem;
}

.alert {
  padding: 15px 20px;
  border-radius: 12px;
  font-size: 0.95rem;
  text-align: center;
  white-space: pre-line;
  line-height: 1.4;
}

.alert-error {
  background: rgba(255, 107, 107, 0.15);
  border: 1px solid rgba(255, 107, 107, 0.3);
  color: #ffb3b3;
}

.login-button {
  width: 100%;
  padding: 16px;
  margin-top: 10px;
  background: rgba(10, 77, 140, 0.4);
  border: 1px solid rgba(168, 209, 255, 0.3);
  border-radius: 9999px;
  font-size: 1.2rem;
  font-weight: 600;
  color: #FFFFFF;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(10px);
}

.login-button:hover:not(:disabled) {
  background: rgba(10, 77, 140, 0.6);
  border-color: rgba(168, 209, 255, 0.5);
  transform: translateY(-1px);
}

.login-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.loader {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: #FFFFFF;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.register-link {
  margin-top: 30px;
  text-align: center;
}

.register-link p {
  color: #F0F8FF;
  margin-bottom: 10px;
  font-size: 0.95rem;
}

.register-button {
  display: inline-block;
  padding: 12px 30px;
  background: rgba(10, 77, 140, 0.3);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 9999px;
  color: #FFFFFF;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
}

.register-button:hover {
  background: rgba(10, 77, 140, 0.5);
  border-color: rgba(168, 209, 255, 0.4);
  transform: translateY(-1px);
}

@media (max-width: 480px) {
  .login-card {
    padding: 40px 20px;
  }

  .login-title {
    font-size: 1.8rem;
  }
}
</style>
