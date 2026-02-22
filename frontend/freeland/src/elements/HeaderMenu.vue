<template>
  <header class="header">
    <div class="container">
      <nav class="navbar ios-glass ios-glass-heavy">
        <div class="logo">
          <span class="logo-text">FreeLand</span>
        </div>
        
        <div class="nav-menu">
          <a href="#" class="nav-link active">Главная</a>
          <a href="#" class="nav-link">Фрилансеры</a>
          <a href="#" class="nav-link">Заказы</a>
          <a href="#" class="nav-link">О нас</a>
          <a href="#" class="nav-link">Контакты</a>
        </div>
        
        <div class="nav-actions" v-if="!loading">

  <!-- Если НЕ авторизован -->
  <template v-if="!user">
    <button class="nav-button login">Войти</button>
    <router-link class="nav-button register ios-glass" to="/register">Регистрация</router-link>
  </template>

  <!-- Если авторизован -->
  <template v-else>
    <span class="user-name">{{ user.name }}</span>
    <button class="nav-button login" @click="logout">Выйти</button>
  </template>

</div>
        
        <button class="mobile-menu-toggle">
          <span class="bar"></span>
          <span class="bar"></span>
          <span class="bar"></span>
        </button>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const user = ref(null)
const loading = ref(true)

const API_URL = 'http://localhost:8000'

// Получить текущего пользователя
const fetchUser = async () => {
  try {
    const response = await fetch(`${API_URL}/api/user`, {
      credentials: 'include'
    })

    if (response.ok) {
      user.value = await response.json()
    } else {
      user.value = null
    }
  } catch (error) {
    user.value = null
  } finally {
    loading.value = false
  }
}

// Логаут
const logout = async () => {
  await fetch(`${API_URL}/logout`, {
    method: 'POST',
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': getCookie('XSRF-TOKEN')
    }
  })

  user.value = null
}

// Получение CSRF токена из cookie
const getCookie = (name) => {
  const value = `; ${document.cookie}`
  const parts = value.split(`; ${name}=`)
  if (parts.length === 2) return parts.pop().split(';').shift()
}

onMounted(() => {
  fetchUser()
})
</script>

<style scoped>
/* Header & Navigation */
.header {
  padding: 20px 0;
  position: relative;
  z-index: 30;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 32px;
  position: relative;
}

.logo-text {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--text-primary);
  letter-spacing: 1px;
}

.nav-menu {
  display: flex;
  gap: 32px;
}

.nav-link {
  color: var(--text-secondary);
  text-decoration: none;
  font-size: 1.1rem;
  font-weight: 500;
  padding: 8px 16px;
  border-radius: 9999px;
  transition: color 0.3s ease;
}

.nav-link:hover {
  color: var(--text-primary);
}

.nav-link.active {
  color: var(--text-primary);
  background: rgba(168, 209, 255, 0.1);
}

.nav-actions {
  display: flex;
  gap: 12px;
}

.nav-button {
  padding: 10px 24px;
  border: none;
  border-radius: 9999px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  color: var(--text-primary);
}

.nav-button.login {
  background: transparent;
  border: 1px solid rgba(168, 209, 255, 0.3);
  color: var(--text-secondary);
}

.nav-button.login:hover {
  background: rgba(168, 209, 255, 0.1);
  border-color: rgba(168, 209, 255, 0.5);
  color: var(--text-primary);
}

.nav-button.register {
  background: rgba(10, 77, 140, 0.4);
  border: 1px solid rgba(168, 209, 255, 0.3);
}

.nav-button.register:hover {
  background: rgba(10, 77, 140, 0.6);
  border-color: rgba(168, 209, 255, 0.5);
}

.mobile-menu-toggle {
  display: none;
  flex-direction: column;
  gap: 6px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 8px;
}

.bar {
  width: 30px;
  height: 3px;
  background: var(--text-primary);
  border-radius: 3px;
  transition: all 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
  .navbar {
    padding: 12px 20px;
  }

  .nav-menu {
    display: none;
  }

  .nav-actions {
    display: none;
  }

  .mobile-menu-toggle {
    display: flex;
  }

  .logo-text {
    font-size: 1.5rem;
  }
}
</style>