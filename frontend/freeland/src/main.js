import { createApp } from 'vue'
import App from './App.vue'
import router from './router'  // Путь к вашему роутеру

createApp(App)
  .use(router)  // Подключение роутера
  .mount('#app')
