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

        <div class="form-group">
          <label>Аватар (URL)</label>
          <input v-model="form.avatar" type="text" placeholder="https://..." />
        </div>

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
          <button class="submit-button">
            <span class="button-text">Зарегистрироваться</span>
            <span class="button-glow"></span>
          </button>
        </div>

      </form>

      <p v-if="message" class="form-message">
        {{ message }}
      </p>

    </div>
  </section>
</template>

<script setup>
import { reactive, ref } from 'vue'

const API_URL = 'http://localhost:8000'

const form = reactive({
  full_name: '',
  phone: '',
  login: '',
  avatar: '',
  password: '',
  role: ''
})

const message = ref('')

const getCookie = (name) => {
  const value = `; ${document.cookie}`
  const parts = value.split(`; ${name}=`)
  if (parts.length === 2) return parts.pop().split(';').shift()
}

const handleRegister = async () => {
  await fetch(`${API_URL}/sanctum/csrf-cookie`, {
    credentials: 'include'
  })
  console.log(form);
  const response = await fetch(`http://localhost:8000/api/registred`, {
    method: 'POST',
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': getCookie('XSRF-TOKEN')
    },
    body: JSON.stringify(form)
  })

  const data = await response.json()

  if (response.ok) {
    message.value = data.message
    window.location.href = '/'
  } else {
    message.value = data.message || 'Ошибка регистрации'
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
}

.form-title {
  font-size: 2.6rem;
  font-weight: 700;
  margin-bottom: 10px;
  text-shadow: 0 4px 30px rgba(168, 209, 255, 0.35);
}

.form-subtitle {
  color: var(--text-secondary);
  margin-bottom: 45px;
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
  color: var(--text-secondary);
}

.form-group input,
.form-group select {
  padding: 15px 20px;
  border-radius: 18px;
  border: 1px solid rgba(168, 209, 255, 0.25);
  background: rgba(10, 77, 140, 0.25);
  backdrop-filter: blur(14px);
  color: var(--text-primary);
  font-size: 1rem;
  transition: 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.6);
  box-shadow: 0 0 25px rgba(168, 209, 255, 0.4);
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
  background: rgba(10, 77, 140, 0.45);
  cursor: pointer;
  overflow: hidden;
  transition: 0.3s ease;
}

.submit-button:hover {
  background: rgba(10, 77, 140, 0.65);
  border-color: rgba(168, 209, 255, 0.6);
  transform: translateY(-2px);
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

.submit-button:hover .button-glow {
  left: 100%;
}

.form-message {
  margin-top: 25px;
  color: var(--text-secondary);
}

/* 📱 Мобильная версия */
@media (max-width: 768px) {
  .register-card {
    padding: 50px 25px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-full {
    grid-column: span 1;
  }
}

</style>