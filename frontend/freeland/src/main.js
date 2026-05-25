import { createApp } from 'vue'
import App from './App.vue'
import router from './router'  // Путь к вашему роутеру
import './assets/css/mainStyle.css' // Глобальные стили (загрузчик, скроллбар, переменные)

createApp(App)
  .use(router)  // Подключение роутера
  .mount('#app')
