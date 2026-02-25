<template>
  <header class="header">
    <div class="container">
      <nav class="navbar ios-glass ios-glass-heavy">
        <div class="logo">
          <span class="logo-text">FreeLand</span>
        </div>
        
        <div class="nav-menu">
          <router-link to="/" class="nav-link" :class="{ active: $route.path === '/' }">Главная</router-link>
          <router-link to="/freelancer" class="nav-link" :class="{ active: $route.path === '/freelancer' }">Фрилансеры</router-link>
          <router-link to="/projects" class="nav-link" :class="{ active: $route.path === '/projects' }">Заказы</router-link>
          
          <!-- Мои проекты для заказчиков и фрилансеров -->
          <router-link 
            v-if="user && (user.role === 'customer' || user.role === 'freelancer' || user.role === 'заказчик' || user.role === 'фрилансер')" 
            to="/my-projects" 
            class="nav-link" 
            :class="{ active: $route.path === '/my-projects' }"
          >
            Мои проекты
          </router-link>
          
          <!-- Админ панель только для админов -->
          <router-link v-if="user?.role === 'admin'" to="/admin" class="nav-link" :class="{ active: $route.path.startsWith('/admin') }">
            Админ панель
          </router-link>
        </div>
        
        <!-- Показываем loader -->
        <div v-if="loading" class="nav-actions">
          <div class="loader-sm"></div>
        </div>

        <!-- НЕ авторизован -->
        <div v-else-if="!user" class="nav-actions">
          <router-link class="nav-button login" to="/login">Войти</router-link>
          <router-link class="nav-button register ios-glass" to="/register">Регистрация</router-link>
        </div>

        <!-- АВТОРИЗОВАН -->
        <div v-else class="nav-actions">
          <!-- Аватар/имя пользователя с ссылкой на профиль -->
          <router-link to="/profile" class="user-info-link">
            <div class="user-info">
              <img 
                v-if="user.avatar" 
                :src="user.avatar" 
                :alt="user.full_name" 
                class="user-avatar"
              />
              <span v-else class="user-avatar-placeholder">
                {{ getInitials(user.full_name) }}
              </span>
              <span class="user-name">{{ user.full_name || user.login }}</span>
            </div>
          </router-link>
          
          <!-- Роль (не для админа) -->
          <span v-if="user.role !== 'admin'" class="user-role">
            {{ getRoleText(user.role) }}
          </span>
          
          <!-- Кнопка выхода -->
          <button class="nav-button logout" @click="logout">Выйти</button>
        </div>
        
        <button class="mobile-menu-toggle" @click="toggleMobileMenu">
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
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const user = ref(null)
const loading = ref(true)
const API_URL = '/api'

// Получить текущего пользователя
const fetchUser = async () => {
  const token = localStorage.getItem('token')
  
  if (!token) {
    user.value = null
    loading.value = false
    return
  }

  try {
    const response = await fetch(`${API_URL}/user`, {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      }
    })

    const data = await response.json()
    
    if (response.ok && data.success) {
      user.value = data.user
    } else {
      // Токен невалидный
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      user.value = null
    }
  } catch (error) {
    console.error('Ошибка получения пользователя:', error)
    user.value = null
  } finally {
    loading.value = false
  }
}

// Логаут
const logout = async () => {
  const token = localStorage.getItem('token')
  
  try {
    if (token) {
      await fetch(`${API_URL}/logout`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      })
    }
  } catch (error) {
    console.error('Ошибка выхода:', error)
  } finally {
    // Очищаем данные
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    user.value = null
    
    await router.push('/login?redirect=' + encodeURIComponent(route.fullPath))
  }
}

const getInitials = (name) => {
  if (!name) return '?'
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const getRoleText = (role) => {
  const roles = {
    'freelancer': 'Фрилансер',
    'customer': 'Заказчик',
    'admin': 'Админ',
    'фрилансер': 'Фрилансер',
    'заказчик': 'Заказчик'
  }
  return roles[role] || role
}

// Мобильное меню
const toggleMobileMenu = () => {
  // Здесь можно добавить логику для мобильного меню
  console.log('Toggle mobile menu')
}

onMounted(() => {
  fetchUser()
})
</script>

<style scoped>
.header {
  padding: 20px 0;
  position: sticky;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  backdrop-filter: blur(20px);
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 32px;
  min-height: 70px;
}

.logo-text {
  font-size: 1.8rem;
  font-weight: 700;
  color: #FFFFFF;
  letter-spacing: 1px;
  text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.nav-menu {
  display: flex;
  gap: 32px;
  align-items: center;
}

.nav-link {
  color: rgba(255,255,255,0.8);
  text-decoration: none;
  font-size: 1.1rem;
  font-weight: 500;
  padding: 12px 20px;
  border-radius: 20px;
  transition: all 0.3s ease;
  position: relative;
}

.nav-link:hover {
  color: #FFFFFF;
  background: rgba(255,255,255,0.1);
  transform: translateY(-1px);
}

.nav-link.active {
  color: #FFFFFF;
  background: rgba(168, 209, 255, 0.2);
  box-shadow: 0 4px 12px rgba(168, 209, 255, 0.3);
}

.nav-actions {
  display: flex;
  gap: 16px;
  align-items: center;
}

/* Стили для ссылки на профиль */
.user-info-link {
  text-decoration: none;
  transition: transform 0.3s ease;
}

.user-info-link:hover {
  transform: translateY(-2px);
}

.user-info-link:hover .user-info {
  background: rgba(255,255,255,0.15);
  border-color: rgba(168, 209, 255, 0.3);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  background: rgba(255,255,255,0.1);
  padding: 8px 16px;
  border-radius: 20px;
  backdrop-filter: blur(10px);
  border: 1px solid transparent;
  transition: all 0.3s ease;
  cursor: pointer;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(255,255,255,0.3);
}

.user-avatar-placeholder {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(168, 209, 255, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  font-weight: 600;
  color: #FFFFFF;
  border: 2px solid rgba(255,255,255,0.3);
}

.user-name {
  color: #FFFFFF;
  font-weight: 600;
  font-size: 0.95rem;
  max-width: 150px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.nav-button {
  padding: 12px 28px;
  border: none;
  border-radius: 9999px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 44px;
}

.nav-button.login {
  background: transparent;
  border: 1px solid rgba(255,255,255,0.3);
  color: rgba(255,255,255,0.9);
}

.nav-button.login:hover {
  background: rgba(255,255,255,0.1);
  border-color: rgba(255,255,255,0.5);
  transform: translateY(-1px);
}

.nav-button.register {
  background: rgba(10, 77, 140, 0.6);
  border: 1px solid rgba(168, 209, 255, 0.4);
  color: #FFFFFF;
  backdrop-filter: blur(10px);
}

.nav-button.register:hover {
  background: rgba(10, 77, 140, 0.8);
  border-color: rgba(168, 209, 255, 0.6);
  transform: translateY(-1px);
}

.nav-button.logout {
  background: rgba(255, 107, 107, 0.2);
  border: 1px solid rgba(255, 107, 107, 0.4);
  color: #FFB3B3;
}

.nav-button.logout:hover {
  background: rgba(255, 107, 107, 0.4);
  border-color: rgba(255, 107, 107, 0.6);
  color: #FFFFFF;
}

.user-role {
  background: rgba(10, 77, 140, 0.3);
  color: rgba(255,255,255,0.9);
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 500;
  border: 1px solid rgba(168, 209, 255, 0.2);
}

.loader-sm {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
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
  background: #FFFFFF;
  border-radius: 3px;
  transition: all 0.3s ease;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 1024px) {
  .nav-menu {
    gap: 20px;
  }
  
  .nav-link {
    padding: 8px 16px;
    font-size: 1rem;
  }
  
  .nav-actions {
    gap: 12px;
  }
  
  .nav-button {
    padding: 10px 20px;
    font-size: 0.95rem;
  }
  
  .user-name {
    max-width: 100px;
  }
}

@media (max-width: 768px) {
  .navbar {
    padding: 12px 20px;
  }

  .nav-menu,
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

/* Для очень маленьких экранов */
@media (max-width: 480px) {
  .nav-link {
    padding: 6px 12px;
    font-size: 0.9rem;
  }
  
  .user-info {
    padding: 4px 8px;
  }
  
  .user-avatar,
  .user-avatar-placeholder {
    width: 28px;
    height: 28px;
  }
  
  .user-name {
    max-width: 80px;
    font-size: 0.85rem;
  }
}
</style>