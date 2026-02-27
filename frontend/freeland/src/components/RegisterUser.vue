<template>
  <section class="register-page">
    <div class="register-card ios-glass ios-glass-heavy">

      <h2 class="form-title">Регистрация</h2>
      <p class="form-subtitle">Создайте аккаунт на FreeLand</p>

      <form @submit.prevent="handleRegister" class="form-grid">

        <div class="form-group">
          <label>ФИО</label>
          <input v-model="form.full_name" type="text" required />
        </div>

        <div class="form-group">
          <label>Телефон</label>
          <input v-model="form.phone" type="text" required />
        </div>

        <div class="form-group">
          <label>Логин</label>
          <input v-model="form.login" type="text" required />
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

        <div class="form-full">
          <button class="submit-button" :disabled="submitting">
            <span v-if="submitting" class="button-spinner"></span>
            <span class="button-text">{{ submitting ? 'Регистрация...' : 'Зарегистрироваться' }}</span>
            <span class="button-glow"></span>
          </button>
        </div>

      </form>

      <div v-if="message" class="form-message" :class="messageType">
        {{ message }}
      </div>

    </div>
  </section>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'

const API_URL = ''
const router = useRouter()

const form = reactive({
  full_name: '',
  phone: '',
  login: '',
  avatar: '',
  password: '',
  role: ''
})

const message = ref('')
const messageType = ref('info')
const submitting = ref(false)

// Простая функция для получения CSRF токена (если нужен)
const getCsrfToken = () => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  return token || ''
}

const handleRegister = async () => {
  submitting.value = true
  message.value = ''
  
  try {
    console.log('Отправляемые данные:', form)
    const userData = {
      full_name: form.full_name,
      phone: form.phone,
      login: form.login,
      avatar: form.avatar || null,
      password: form.password,
      role: form.role
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
        message.value = data.message || 'Регистрация успешна!'
        messageType.value = 'success'
        
        form.full_name = ''
        form.phone = ''
        form.login = ''
        form.avatar = ''
        form.password = ''
        form.role = ''
        setTimeout(() => {
          router.push('/login')
        }, 2000)
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
</script>

<style scoped>
.register-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  background: radial-gradient(
    circle at top center,
    rgba(168, 209, 255, 0.08),
    transparent 60%
  );
}

.register-card {
  width: 100%;
  max-width: 900px;
  padding: 70px 60px;
  border-radius: 28px;
  text-align: center;
  background: rgba(10, 25, 45, 0.7);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(168, 209, 255, 0.2);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.form-title {
  font-size: 2.6rem;
  font-weight: 700;
  margin-bottom: 10px;
  color: #FFFFFF;
  text-shadow: 0 4px 30px rgba(168, 209, 255, 0.35);
}

.form-subtitle {
  color: #F0F8FF;
  margin-bottom: 45px;
  opacity: 0.8;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 25px 30px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  text-align: left;
}

.form-group label {
  font-size: 0.9rem;
  color: #F0F8FF;
  opacity: 0.9;
  font-weight: 500;
}

.form-group input,
.form-group select {
  padding: 15px 20px;
  border-radius: 18px;
  border: 1px solid rgba(168, 209, 255, 0.25);
  background: rgba(10, 77, 140, 0.25);
  backdrop-filter: blur(14px);
  color: #FFFFFF;
  font-size: 1rem;
  transition: 0.3s ease;
  width: 100%;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.6);
  box-shadow: 0 0 25px rgba(168, 209, 255, 0.4);
  background: rgba(10, 77, 140, 0.35);
}

.form-group input::placeholder {
  color: rgba(240, 248, 255, 0.3);
}

.form-group select option {
  background: #0A1A2A;
  color: #FFFFFF;
}

.form-full {
  grid-column: span 2;
}

.submit-button {
  width: 100%;
  position: relative;
  padding: 18px;
  border-radius: 9999px;
  font-size: 1.1rem;
  font-weight: 600;
  border: 1px solid rgba(168, 209, 255, 0.35);
  background: linear-gradient(135deg, rgba(10, 77, 140, 0.6), rgba(26, 107, 179, 0.6));
  cursor: pointer;
  overflow: hidden;
  transition: 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  color: #FFFFFF;
  backdrop-filter: blur(10px);
}

.submit-button:hover:not(:disabled) {
  background: linear-gradient(135deg, rgba(10, 77, 140, 0.8), rgba(26, 107, 179, 0.8));
  border-color: rgba(168, 209, 255, 0.6);
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(10, 77, 140, 0.3);
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
  margin-top: 25px;
  padding: 15px 20px;
  border-radius: 12px;
  font-size: 0.95rem;
  text-align: left;
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

/* 📱 Мобильная версия */
@media (max-width: 768px) {
  .register-card {
    padding: 50px 25px;
  }

  .form-title {
    font-size: 2rem;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-full {
    grid-column: span 1;
  }
}

@media (max-width: 480px) {
  .register-card {
    padding: 30px 20px;
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