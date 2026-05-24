<template>
  <div class="modal-overlay register-page">
    <SEOHead
      title="Регистрация на бирже фриланса FreeLand"
      description="Создайте бесплатный аккаунт на FreeLand: размещайте проекты как заказчик или находите заказы как фрилансер. Быстрая регистрация за пару минут."
      keywords="регистрация, создать аккаунт, фриланс, FreeLand"
    />
    <div class="modal-content register-card ios-glass ios-glass-heavy">

      <h2 class="form-title">Регистрация</h2>
      <p class="form-subtitle">Создайте аккаунт на FreeLand</p>

      <form v-show="!verificationRequired" @submit.prevent="handleRegister" class="form-grid">

        <div class="form-group">
          <label>ФИО</label>
          <input v-model="form.full_name" type="text" required />
        </div>

        <div class="form-group">
          <label>Телефон</label>
          <input v-model="form.phone" type="tel" inputmode="tel" maxlength="18" placeholder="+7XXXXXXXXXX или 8XXXXXXXXXX" required />
        </div>

        <div class="form-group">
          <label>Email</label>
          <input v-model="form.login" type="email" placeholder="name@example.com" required />
        </div>

        <!-- <div class="form-group">
          <label>Аватар (URL)</label>
          <input v-model="form.avatar" type="text" placeholder="https://..." />
        </div> -->

        <div class="form-group">
          <label>Пароль</label>
          <input v-model="form.password" type="password" required />
        </div>

        <div class="form-group">
          <label>Выберите роль</label>
          <select v-model="form.role" required>
            <option disabled value="">Выберите</option>
            <option value="фрилансер">Фрилансер</option>
            <option value="заказчик">Заказчик</option>
          </select>
        </div>

        <div class="form-group agreement-group">
          <a
            href="/api/user-agreement"
            class="agreement-download"
            download="user-agreement-freeland.pdf"
            target="_blank"
            rel="noopener noreferrer"
          >
            Скачать пользовательское соглашение
          </a>
          <label class="agreement-checkbox">
            <input v-model="form.agreed_to_terms" type="checkbox" required />
            <span>Я принимаю условия пользовательского соглашения</span>
          </label>
        </div>

        <div class="form-full">
          <button class="submit-button" :disabled="submitting">
            <span v-if="submitting" class="button-spinner"></span>
            <span class="button-text">{{ submitting ? 'Регистрация...' : 'Зарегистрироваться' }}</span>
            <span class="button-glow"></span>
          </button>
        </div>

      </form>

      <div v-if="verificationRequired" class="verification-box">
        <h3>Подтверждение email</h3>
        <p class="verification-email">Код отправлен на <strong>{{ verificationLogin }}</strong></p>
        <label class="verification-label" for="reg-verify-code">Код из письма</label>
        <input
          id="reg-verify-code"
          v-model="verificationCode"
          type="text"
          inputmode="numeric"
          autocomplete="one-time-code"
          maxlength="8"
          placeholder="000000"
          class="verification-code-input"
        />
        <div class="verification-actions">
          <button class="submit-button" :disabled="submitting" @click="verifyCode">
            {{ submitting ? 'Проверка...' : 'Подтвердить email' }}
          </button>
          <button class="submit-button resend" :disabled="submitting" @click="resendCode">
            Отправить код повторно
          </button>
        </div>
      </div>

      <div v-if="message" class="form-message" :class="messageType">
        {{ message }}
      </div>

    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import SEOHead from '@/elements/SEOHead.vue'

const API_URL = ''
const router = useRouter()

const form = reactive({
  full_name: '',
  phone: '',
  login: '',
  avatar: '',
  password: '',
  role: '',
  agreed_to_terms: false
})

const message = ref('')
const messageType = ref('info')
const submitting = ref(false)
const verificationRequired = ref(false)
const verificationCode = ref('')
const verificationLogin = ref('')

// Простая функция для получения CSRF токена (если нужен)
const getCsrfToken = () => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  return token || ''
}

// Нормализация телефона: убираем пробелы, скобки и дефисы
const normalizePhone = (raw) => String(raw || '').replace(/[\s\-()]/g, '')
// Российский формат: +7XXXXXXXXXX (12 символов) или 8XXXXXXXXXX (11 символов)
const isValidRuPhone = (raw) => /^(\+7|8)\d{10}$/.test(normalizePhone(raw))

const validateForm = () => {
  const fullName = form.full_name.trim()
  const phone = form.phone.trim()
  const login = form.login.trim()
  const password = form.password

  if (fullName.length < 2) return 'Введите корректное ФИО'
  if (!isValidRuPhone(phone)) return 'Телефон должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX (11 цифр)'
  if (login.length < 3) return 'Логин должен быть не короче 3 символов'
  if (password.length < 6) return 'Пароль должен быть не короче 6 символов'
  if (!form.role) return 'Выберите роль'
  if (!form.agreed_to_terms) return 'Необходимо принять пользовательское соглашение'

  return ''
}

const handleRegister = async () => {
  const validationError = validateForm()
  if (validationError) {
    message.value = validationError
    messageType.value = 'error'
    return
  }

  submitting.value = true
  message.value = ''
  
  try {
    console.log('Отправляемые данные:', form)
    const userData = {
      full_name: form.full_name,
      phone: normalizePhone(form.phone),
      login: form.login,
      avatar: form.avatar || null,
      password: form.password,
      role: form.role,
      agreed_to_terms: form.agreed_to_terms
    }
    const response = await fetch(`${API_URL}/api/users/add`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify(userData)
    })

    console.log('Статус ответа:', response.status)
    
    const data = await response.json()
    console.log('Ответ сервера:', data)

    if (response.ok) {
      if (data.success === true || data.message === 'Пользователь успешно создан' || data.user) {
        verificationRequired.value = data.requires_verification === true
        verificationLogin.value = data.login || form.login
        message.value = data.message || 'Регистрация успешна!'
        messageType.value = 'success'
        if (verificationRequired.value) {
          verificationCode.value = ''
        }
        if (!verificationRequired.value) {
          form.full_name = ''
          form.phone = ''
          form.login = ''
          form.avatar = ''
          form.password = ''
          form.role = ''
          setTimeout(() => {
            router.push('/login')
          }, 1500)
        }
      } else {
        throw new Error(data.message || 'Ошибка при регистрации')
      }
    } else {
      // Обработка ошибок валидации
      if (data.errors) {
        const errorMessages = Object.values(data.errors).flat().join(', ')
        throw new Error(errorMessages)
      }
      throw new Error(data.message || `Ошибка сервера: ${response.status}`)
    }

  } catch (error) {
    console.error('Детали ошибки:', error)
    message.value = error.message || 'Произошла ошибка при регистрации'
    messageType.value = 'error'
  } finally {
    submitting.value = false
  }
}

const verifyCode = async () => {
  const digits = verificationCode.value.replace(/\D/g, '')
  if (digits.length !== 6) {
    message.value = 'Введите 6-значный код'
    messageType.value = 'error'
    return
  }

  submitting.value = true
  try {
    const response = await fetch(`${API_URL}/api/verify-email-code`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify({
        login: verificationLogin.value,
        code: digits
      })
    })
    const data = await response.json()
    if (!response.ok || !data.success) {
      throw new Error(data.message || 'Не удалось подтвердить email')
    }

    message.value = data.message || 'Email подтвержден'
    messageType.value = 'success'
    verificationRequired.value = false
    setTimeout(() => router.push('/login'), 1200)
  } catch (error) {
    message.value = error.message || 'Ошибка подтверждения'
    messageType.value = 'error'
  } finally {
    submitting.value = false
  }
}

const resendCode = async () => {
  submitting.value = true
  try {
    const response = await fetch(`${API_URL}/api/resend-email-code`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify({ login: verificationLogin.value })
    })
    const data = await response.json()
    if (!response.ok || !data.success) {
      throw new Error(data.message || 'Не удалось отправить код повторно')
    }
    message.value = data.message || 'Новый код отправлен'
    messageType.value = 'success'
  } catch (error) {
    message.value = error.message || 'Ошибка отправки кода'
    messageType.value = 'error'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.register-page {
  display: flex;
  position: fixed;
  inset: 0;
  z-index: 1000;
  background: rgba(8, 51, 88, 0.8);
  backdrop-filter: blur(8px);
  justify-content: center;
  align-items: center;
  padding: 20px;
}

.modal-content {
  width: 90%;
  max-width: 520px;
  max-height: 90vh;
  overflow-y: auto;
}

.register-card {
  padding: 50px 40px;
  border-radius: 32px;
  text-align: center;
  background: rgba(10, 77, 140, 0.15);
  backdrop-filter: blur(25px) saturate(180%);
  border: 1px solid rgba(168, 209, 255, 0.2);
  box-shadow: 0 20px 40px rgba(8, 51, 88, 0.5);
}

.form-title {
  font-size: 2.2rem;
  font-weight: 700;
  margin-bottom: 8px;
  color: #FFFFFF;
  text-shadow: 0 2px 10px rgba(168, 209, 255, 0.3);
}

.form-subtitle {
  color: #F0F8FF;
  margin-bottom: 30px;
  opacity: 0.8;
  font-size: 0.95rem;
}

.form-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  text-align: left;
}

.form-group label {
  font-size: 1rem;
  color: #F0F8FF;
  font-weight: 500;
  margin-left: 5px;
}

.form-group input,
.form-group select {
  padding: 15px 20px;
  border-radius: 16px;
  border: 1px solid rgba(168, 209, 255, 0.2);
  background: rgba(10, 77, 140, 0.2);
  color: #FFFFFF;
  font-size: 1rem;
  transition: all 0.3s ease;
  width: 100%;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.5);
  box-shadow: 0 0 20px rgba(168, 209, 255, 0.2);
}

.form-group input::placeholder {
  color: rgba(240, 248, 255, 0.3);
}

.form-group select option {
  background: #0A1A2A;
  color: #FFFFFF;
}

.form-full {
  margin-top: 8px;
}

.submit-button {
  width: 100%;
  position: relative;
  padding: 16px;
  border-radius: 9999px;
  font-size: 1.2rem;
  font-weight: 600;
  border: 1px solid rgba(168, 209, 255, 0.3);
  background: rgba(10, 77, 140, 0.4);
  cursor: pointer;
  overflow: hidden;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  color: #FFFFFF;
  backdrop-filter: blur(10px);
}

.submit-button:hover:not(:disabled) {
  background: rgba(10, 77, 140, 0.6);
  border-color: rgba(168, 209, 255, 0.5);
  transform: translateY(-1px);
}

.submit-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.button-text {
  position: relative;
  z-index: 2;
}

.button-glow {
  position: absolute;
  top: 0;
  left: -100%;
  width: 200%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(168, 209, 255, 0.5),
    transparent
  );
  transition: 0.8s;
}

.submit-button:hover:not(:disabled) .button-glow {
  left: 100%;
}

.button-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  z-index: 2;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.form-message {
  margin-top: 20px;
  padding: 15px 20px;
  border-radius: 12px;
  font-size: 0.95rem;
  text-align: center;
  white-space: pre-line;
  line-height: 1.4;
}

.form-message.success {
  background: rgba(39, 174, 96, 0.2);
  border: 1px solid rgba(39, 174, 96, 0.4);
  color: #2ecc71;
}

.form-message.error {
  background: rgba(231, 76, 60, 0.2);
  border: 1px solid rgba(231, 76, 60, 0.4);
  color: #e74c3c;
}

.form-message.info {
  background: rgba(52, 152, 219, 0.2);
  border: 1px solid rgba(52, 152, 219, 0.4);
  color: #3498db;
}

.verification-box {
  margin-top: 18px;
  text-align: left;
}

.verification-box h3 {
  color: #fff;
  margin-bottom: 8px;
}

.verification-box p {
  color: #d9ecff;
  margin-bottom: 10px;
}

.verification-email {
  font-size: 0.95rem;
}

.verification-label {
  display: block;
  color: #d9ecff;
  font-size: 0.9rem;
  margin-bottom: 6px;
}

.verification-code-input {
  width: 100%;
  padding: 14px 16px;
  border-radius: 12px;
  border: 1px solid rgba(168, 209, 255, 0.3);
  background: rgba(10, 77, 140, 0.2);
  color: #fff;
  font-size: 1.25rem;
  letter-spacing: 0.35em;
  text-align: center;
  font-variant-numeric: tabular-nums;
}

.verification-actions {
  display: grid;
  grid-template-columns: 1fr;
  gap: 10px;
  margin-top: 12px;
}

.submit-button.resend {
  background: rgba(255, 255, 255, 0.08);
}

.agreement-group {
  gap: 12px;
}

.agreement-download {
  display: inline-block;
  color: #a8d1ff;
  font-size: 0.95rem;
  text-decoration: underline;
  text-underline-offset: 3px;
}

.agreement-download:hover {
  color: #ffffff;
}

.agreement-checkbox {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  cursor: pointer;
  color: #f0f8ff;
  font-size: 0.92rem;
  line-height: 1.4;
  text-align: left;
}

.agreement-checkbox input {
  margin-top: 3px;
  width: 18px;
  height: 18px;
  flex-shrink: 0;
  accent-color: #1a6bb3;
}

/* 📱 Мобильная версия */
@media (max-width: 768px) {
  .register-card {
    padding: 40px 24px;
  }

  .form-title {
    font-size: 1.9rem;
  }
}

@media (max-width: 480px) {
  .register-card {
    padding: 36px 20px;
  }

  .form-title {
    font-size: 1.8rem;
  }

  .form-group input,
  .form-group select {
    padding: 12px 16px;
  }

  .submit-button {
    padding: 15px;
  }
}
</style>