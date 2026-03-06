<!-- EasyProjectsPage.vue -->
<template>
  <div class="easy-projects-page">
        <SEOHead 
      :title="seoData.title"
      :description="seoData.description"
      :keywords="seoData.keywords"
      :og-title="seoData.ogTitle"
      :og-description="seoData.ogDescription"
      :og-image="seoData.ogImage"
      :canonical="seoData.canonical"
    />
    <HeaderMenu />
    <DynamicBackground />
    
    <main class="page-content">
      <div class="container">
        <!-- Хлебные крошки (SEO) -->
        <div class="breadcrumbs ios-glass">
          <router-link to="/" class="breadcrumb-link">Главная</router-link>
          <span class="breadcrumb-separator">→</span>
          <span class="breadcrumb-current">Легкие проекты для новичков без портфолио</span>
        </div>

        <!-- Основной контент статьи -->
        <article class="article-main ios-glass ios-glass-heavy">
          <!-- H1 с основным коммерческим запросом -->
          <header class="article-header">
            <h1 class="article-title">Легкие проекты фриланс для новичков без портфолио</h1>
            <div class="article-meta">
              <span class="article-category">Руководство для начинающих</span>
              <span class="article-date">Обновлено: 15 марта 2024</span>
            </div>
          </header>

          <!-- Изображение 1 с ALT и подписью -->
          <figure class="article-figure">
            <div class="image-container ios-glass">
              <img 
                src="/img/EasyProject.png" 
                alt="Пример легкого проекта фриланс для новичков - правка CSS за 30 минут с бюджетом 1500 рублей"
                class="article-image"
                @error="handleImageError"
              >
            </div>
            <figcaption class="image-caption">
              Рис. 1: Пример простого задания по верстке — правка CSS для начинающего фрилансера
            </figcaption>
          </figure>

          <!-- Введение с ключевыми словами -->
          <p class="article-intro">
            Ищете <strong>простые задания верстка</strong> или доработку сайтов? На нашей бирже каждый день появляются 
            <strong>легкие проекты для начинающих фрилансеров</strong> — от правок CSS до HTML-лендингов. 
            Подходит студентам и новичкам без опыта. Средний бюджет таких задач — от 500 до 3000 рублей.
          </p>

          <!-- H2: Категории проектов -->
          <h2>Какие простые задания доступны новичкам без опыта</h2>
          
          <!-- Маркированный список характеристик -->
          <ul class="projects-list">
            <li><strong>Правка CSS</strong> — исправление стилей, адаптация под мобильные устройства (2-4 часа работы)</li>
            <li><strong>HTML верстка лендингов</strong> — создание одностраничных сайтов по готовому макету из Figma (1-2 дня)</li>
            <li><strong>Доработка WordPress</strong> — добавление новых блоков, настройка плагинов (3-5 часов)</li>
            <li><strong>Интеграция форм обратной связи</strong> — подключение и настройка форм на готовых сайтах (1-3 часа)</li>
            <li><strong>Базовые правки Laravel/Vue.js</strong> — мелкие доработки в коде под руководством заказчика (2-4 часа)</li>
          </ul>

          <!-- H2: Преимущества для новичков -->
          <h2>Почему стоит начинать с простых проектов на фрилансе</h2>
          
          <p>
            <strong>Работа на фрилансе для новичков</strong> начинается именно с небольших заказов. 
            Это позволяет получить первые отзывы, собрать портфолио и понять специфику удаленной работы. 
            Все проекты на нашей бирже защищены <strong>безопасной сделкой</strong> — деньги замораживаются на счету 
            до момента сдачи работы, что гарантирует оплату.
          </p>

          <!-- H3: Детали процесса -->
          <h3>Как происходит работа над простыми заданиями</h3>
          
          <!-- Нумерованный список -->
          <ol class="steps-list">
            <li>Заказчик публикует задание с подробным ТЗ и бюджетом</li>
            <li>Вы откликаетесь и предлагаете свои сроки выполнения</li>
            <li>После утверждения приступаете к работе</li>
            <li>Сдаете результат и получаете оплату</li>
          </ol>

          <!-- Изображение 2 - инфографика -->
          <figure class="article-figure">
            <div class="image-container ios-glass">
              <img 
                src="/img/diogramm.png" 
                alt="Статистика распределения легких проектов фриланс по времени выполнения для начинающих"
                class="article-image"
                @error="handleImageError"
              >
            </div>
            <figcaption class="image-caption">
              Рис. 2: Распределение простых заданий по времени выполнения (данные за март 2024)
            </figcaption>
          </figure>

          <!-- H2: Инструкция для новичков -->
          <h2>Как получить первый заказ на фрилансе без портфолио</h2>
          
          <p>
            Чтобы начать зарабатывать на <strong>простых заданиях верстка</strong> и других легких проектах, 
            следуйте простой инструкции:
          </p>

          <ol class="steps-list">
            <li>
              <strong>Зарегистрируйтесь на бирже</strong> — заполните профиль, укажите свои навыки (HTML, CSS, JavaScript)
            </li>
            <li>
              <strong>Найдите подходящие проекты</strong> — используйте фильтр "Для начинающих" в разделе 
              <router-link to="/projects" class="inline-link">каталога проектов</router-link>
            </li>
            <li>
              <strong>Отправьте грамотный отклик</strong> — напишите, как планируете выполнить задачу и в какие сроки
            </li>
            <li>
              <strong>Выполните работу качественно</strong> — даже небольшой проект может стать началом долгосрочного сотрудничества
            </li>
          </ol>

          <!-- Блок с преимуществами (H3) -->
          <h3>Преимущества работы через нашу биржу</h3>
          
          <ul class="benefits-mini-list">
            <li>Более 500 активных проектов ежедневно</li>
            <li>Средний чек на простые задания: 1200 рублей</li>
            <li>Безопасные сделки с защитой обеих сторон</li>
            <li>Возможность расти до крупных заказов</li>
          </ul>

          <!-- Коммерческий блок с CTA -->
          <div class="commercial-block ios-glass">
            <h3>Готовы найти свою первую работу на фрилансе?</h3>
            <p>
              В разделе <router-link to="/projects" class="commercial-link">проекты</router-link> 
              сейчас открыто 342 задания. Средний бюджет — 1500 рублей. 
              <strong>Простые задания верстка</strong> доступны даже без опыта!
            </p>
            <div class="cta-buttons">
              <router-link to="/register" class="cta-button primary">
                <span class="button-text">Начать зарабатывать</span>
                <span class="button-glow"></span>
              </router-link>
              <router-link to="/projects" class="cta-button secondary">
                <span class="button-text">Смотреть проекты</span>
                <span class="button-glow"></span>
              </router-link>
            </div>
          </div>

          <!-- Заключение -->
          <p class="article-conclusion">
            <strong>Фриланс для новичков</strong> открывает большие возможности. Начинайте с простых задач, 
            получайте опыт и постепенно переходите к более сложным и дорогим проектам. 
            Регистрируйтесь на бирже и откликайтесь на первые заказы уже сегодня!
          </p>

          <!-- Мета-информация для SEO -->
          <div class="seo-meta">
            <p class="keywords">
              Ключевые теги: 
              <router-link to="/projects?tag=easy" class="tag-link">легкие проекты фриланс</router-link>,
              <router-link to="/projects?tag=beginner" class="tag-link">простые задания верстка</router-link>,
              <router-link to="/projects?tag=css" class="tag-link">правка CSS для новичка</router-link>,
              <router-link to="/projects?tag=html" class="tag-link">HTML верстка для начинающих</router-link>
            </p>
          </div>
        </article>
      </div>
    </main>
  </div>
</template>

<script>
import DynamicBackground from '@/elements/DynamicBackground.vue';
import SEOHead from '@/elements/SEOHead.vue';
import HeaderMenu from '@/elements/HeaderMenu.vue';

export default {
  name: 'EasyProjectsPage',
  components: {
    DynamicBackground,
    SEOHead,
    HeaderMenu,
  },
  data() {
    return {
              seoData: {
        // Title: 54 символа - ключ в начале + призыв
        title: "Легкие проекты фриланс для новичков — цены и отзывы",
        
        // Description: 158 символов - 2 ключевых запроса
        description: "Простые задания верстка и правка CSS для начинающих фрилансеров. 500+ легких проектов без портфолио. Безопасные сделки и быстрый старт на бирже.",
        
        keywords: "легкие проекты фриланс, простые задания верстка, правка CSS для новичка",
        
        ogTitle: "Легкие проекты фриланс для новичков — первый заработок без опыта",
        ogDescription: "Начните зарабатывать на простых заданиях уже сегодня. Правка CSS, HTML верстка.",
        ogImage: "https://freeland.ru/img/EasyProject.png",
        
        // ЧПУ - читаемый URL
        canonical: "https://freeland.ru/article"
      },
      imageError: false,
      // Метаданные для SEO (можно подтягивать из БД)
      metaData: {
        title: "Легкие проекты фриланс для новичков | Простые задания верстка без портфолио",
        description: "HTML верстка, правка CSS, доработка WordPress для начинающих. 500+ легких проектов без портфолио с бюджетом от 500₽. Безопасные сделки и быстрый старт.",
        keywords: "легкие проекты фриланс, простые задания верстка, правка CSS для новичка, фриланс для новичков, проекты для начинающих фрилансеров"
      }
    }
  },
  mounted() {
    // Обновляем meta-теги (для SPA)
    document.title = this.metaData.title;
    
    // Обновляем meta description
    let metaDescription = document.querySelector('meta[name="description"]');
    if (metaDescription) {
      metaDescription.setAttribute('content', this.metaData.description);
    } else {
      metaDescription = document.createElement('meta');
      metaDescription.name = 'description';
      metaDescription.content = this.metaData.description;
      document.head.appendChild(metaDescription);
    }
    
    // Обновляем meta keywords
    let metaKeywords = document.querySelector('meta[name="keywords"]');
    if (metaKeywords) {
      metaKeywords.setAttribute('content', this.metaData.keywords);
    } else {
      metaKeywords = document.createElement('meta');
      metaKeywords.name = 'keywords';
      metaKeywords.content = this.metaData.keywords;
      document.head.appendChild(metaKeywords);
    }
  },
  methods: {
    handleImageError() {
      this.imageError = true;
    }
  }
}
</script>

<style scoped>
.easy-projects-page {
  min-height: 100vh;
  position: relative;
}

.page-content {
  position: relative;
  z-index: 20;
  padding: 40px 0 100px;
}

/* Хлебные крошки */
.breadcrumbs {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 12px 24px;
  margin-bottom: 40px;
  border-radius: 100px;
  font-size: 0.95rem;
  background: rgba(10, 77, 140, 0.15);
  backdrop-filter: blur(25px);
}

.breadcrumb-link {
  color: var(--text-secondary);
  text-decoration: none;
  transition: color 0.3s ease;
}

.breadcrumb-link:hover {
  color: var(--text-primary);
}

.breadcrumb-separator {
  color: var(--text-tertiary);
  opacity: 0.5;
}

.breadcrumb-current {
  color: var(--text-primary);
  font-weight: 500;
}

/* Основная статья */
.article-main {
  max-width: 900px;
  margin: 0 auto;
  padding: 50px;
}

.article-header {
  margin-bottom: 40px;
  border-bottom: 1px solid rgba(168, 209, 255, 0.15);
  padding-bottom: 30px;
}

.article-meta {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  font-size: 0.95rem;
}

.article-category {
  background: rgba(168, 209, 255, 0.15);
  padding: 4px 12px;
  border-radius: 30px;
  color: var(--text-secondary);
  font-weight: 500;
}

.article-date,
.article-reading-time {
  color: var(--text-tertiary);
  opacity: 0.8;
  padding: 4px 0;
}

.article-title {
  font-size: 3rem;
  font-weight: 800;
  margin-bottom: 20px;
  line-height: 1.2;
  color: var(--text-primary);
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.article-stats {
  display: flex;
  gap: 25px;
  color: var(--text-tertiary);
  font-size: 0.95rem;
}

.stat {
  position: relative;
}

.stat:not(:last-child)::after {
  content: '';
  position: absolute;
  right: -12.5px;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 3px;
  border-radius: 50%;
  background: var(--text-tertiary);
  opacity: 0.5;
}

/* Контент статьи */
.article-content {
  color: var(--text-secondary);
  line-height: 1.8;
  font-size: 1.1rem;
  margin-bottom: 50px;
}

.article-intro {
  font-size: 1.25rem;
  color: var(--text-primary);
  margin-bottom: 40px;
  padding: 20px;
  background: rgba(168, 209, 255, 0.05);
  border-radius: 20px;
  border-left: 4px solid var(--text-secondary);
}

.article-content h2 {
  font-size: 2rem;
  font-weight: 700;
  margin: 50px 0 25px;
  color: var(--text-primary);
  position: relative;
}

.article-content h2::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, var(--text-secondary), transparent);
  border-radius: 2px;
}

.projects-list,
.steps-list {
  list-style: none;
  margin: 25px 0;
}

.projects-list li {
  padding: 12px 0 12px 30px;
  position: relative;
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
}

.projects-list li::before {
  content: '•';
  position: absolute;
  left: 0;
  color: var(--text-secondary);
  font-size: 1.5rem;
  line-height: 1;
}

.steps-list {
  counter-reset: step-counter;
}

.steps-list li {
  display: flex;
  align-items: flex-start;
  gap: 20px;
  margin-bottom: 20px;
  padding: 20px;
  background: rgba(168, 209, 255, 0.03);
  border-radius: 16px;
  border: 1px solid rgba(168, 209, 255, 0.1);
}

.step-number {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, var(--blue-medium), var(--blue-dark));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.2rem;
  color: var(--text-primary);
  flex-shrink: 0;
}

.step-content {
  flex: 1;
  line-height: 1.6;
}

/* Пример проекта */
.project-example {
  display: flex;
  gap: 30px;
  padding: 30px;
  margin: 40px 0;
  background: rgba(168, 209, 255, 0.05);
  border-radius: 24px;
  border: 1px solid rgba(168, 209, 255, 0.15);
}

.example-image {
  flex: 0 0 200px;
}

.image-placeholder {
  width: 100%;
  height: 150px;
  background: linear-gradient(135deg, var(--blue-dark), var(--blue-medium));
  border-radius: 16px;
  border: 1px solid rgba(168, 209, 255, 0.2);
}

.image-placeholder.large {
  height: 200px;
}

.example-content {
  flex: 1;
}

.example-title {
  font-size: 1.3rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 10px 0;
}

.example-meta {
  display: flex;
  gap: 20px;
  margin-top: 15px;
  font-size: 0.95rem;
  color: var(--text-tertiary);
}

/* Инфографика */
.infographic {
  padding: 30px;
  margin: 40px 0;
  text-align: center;
}

.infographic-caption {
  margin-top: 20px;
  font-size: 0.95rem;
  color: var(--text-tertiary);
  font-style: italic;
}

/* Инлайн ссылки */
.inline-link {
  color: var(--text-secondary);
  text-decoration: none;
  border-bottom: 1px dashed var(--text-tertiary);
  transition: all 0.3s ease;
}

.inline-link:hover {
  color: var(--text-primary);
  border-bottom-color: var(--text-primary);
}

/* CTA блок */
.article-cta {
  padding: 40px;
  margin: 50px 0;
  text-align: center;
  background: rgba(10, 77, 140, 0.25);
}

.article-cta h3 {
  font-size: 2rem;
  margin-bottom: 15px;
  color: var(--text-primary);
}

.article-cta p {
  font-size: 1.2rem;
  color: var(--text-secondary);
  margin-bottom: 30px;
}

.cta-buttons {
  display: flex;
  gap: 20px;
  justify-content: center;
}

.cta-button {
  position: relative;
  padding: 16px 32px;
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-primary);
  border: none;
  border-radius: 9999px;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
  overflow: hidden;
  transition: all 0.3s ease;
}

.cta-button.primary {
  background: rgba(26, 107, 179, 0.5);
  border: 1px solid rgba(168, 209, 255, 0.3);
}

.cta-button.secondary {
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
}

.cta-button:hover {
  transform: translateY(-2px);
  background: rgba(42, 127, 201, 0.5);
  box-shadow: 0 15px 30px rgba(8, 51, 88, 0.4);
}

.cta-inline {
  margin: 40px 0;
  padding: 25px;
  background: linear-gradient(135deg, rgba(168, 209, 255, 0.1), transparent);
  border-radius: 16px;
  text-align: center;
}

.cta-text {
  font-size: 1.2rem;
}

.cta-link {
  color: var(--text-primary);
  font-weight: 700;
  text-decoration: none;
  border-bottom: 2px solid var(--text-secondary);
  padding-bottom: 2px;
}

/* Похожие статьи */
.related-articles {
  margin-top: 60px;
  padding-top: 40px;
  border-top: 1px solid rgba(168, 209, 255, 0.15);
}

.related-title {
  font-size: 1.8rem;
  margin-bottom: 30px;
  color: var(--text-primary);
}

.related-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.related-card {
  padding: 25px;
  transition: transform 0.3s ease;
}

.related-card:hover {
  transform: translateY(-5px);
}

.related-card-title {
  font-size: 1.2rem;
  margin-bottom: 10px;
}

.related-link {
  color: var(--text-primary);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: gap 0.3s ease;
}

.related-link:hover {
  gap: 10px;
  color: var(--text-secondary);
}

.link-arrow {
  font-size: 1.2rem;
  transition: transform 0.3s ease;
}

.related-link:hover .link-arrow {
  transform: translateX(3px);
}

.related-excerpt {
  color: var(--text-tertiary);
  font-size: 0.95rem;
  line-height: 1.5;
  opacity: 0.9;
}

/* Responsive */
@media (max-width: 768px) {
  .article-main {
    padding: 30px 20px;
  }

  .article-title {
    font-size: 2.2rem;
  }

  .article-meta {
    flex-wrap: wrap;
    gap: 10px;
  }

  .article-stats {
    flex-wrap: wrap;
    gap: 15px;
  }

  .stat:not(:last-child)::after {
    display: none;
  }

  .project-example {
    flex-direction: column;
  }

  .example-image {
    flex: 0 0 auto;
  }

  .cta-buttons {
    flex-direction: column;
  }

  .related-grid {
    grid-template-columns: 1fr;
  }

  .breadcrumbs {
    font-size: 0.85rem;
    padding: 10px 16px;
    flex-wrap: wrap;
  }
}

@media (max-width: 480px) {
  .article-title {
    font-size: 1.8rem;
  }

  .steps-list li {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .example-meta {
    flex-direction: column;
    gap: 5px;
  }
}
.article-figure {
  margin: 40px 0;
  text-align: center;
}

.image-container {
  padding: 20px;
  border-radius: 24px;
  display: inline-block;
  max-width: 100%;
}

.article-image {
  max-width: 100%;
  height: auto;
  border-radius: 16px;
  display: block;
}

.image-caption {
  margin-top: 15px;
  color: var(--text-tertiary);
  font-size: 0.95rem;
  font-style: italic;
  text-align: center;
}

/* Коммерческий блок */
.commercial-block {
  padding: 40px;
  margin: 50px 0;
  text-align: center;
  background: rgba(10, 77, 140, 0.25);
  border-radius: 32px;
}

.commercial-block h3 {
  font-size: 1.8rem;
  margin-bottom: 20px;
  color: var(--text-primary);
}

.commercial-link {
  color: var(--text-primary);
  font-weight: 700;
  text-decoration: none;
  border-bottom: 2px solid var(--text-secondary);
  padding-bottom: 2px;
}

/* Мини-список преимуществ */
.benefits-mini-list {
  list-style: none;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 15px;
  margin: 30px 0;
}

.benefits-mini-list li {
  padding: 12px 20px;
  background: rgba(168, 209, 255, 0.05);
  border-radius: 12px;
  border-left: 3px solid var(--text-secondary);
  font-size: 1rem;
}

/* Заключение */
.article-conclusion {
  margin: 40px 0;
  padding: 25px;
  background: rgba(168, 209, 255, 0.03);
  border-radius: 20px;
  font-size: 1.15rem;
  border: 1px solid rgba(168, 209, 255, 0.1);
}

/* SEO мета-блок */
.seo-meta {
  margin-top: 50px;
  padding-top: 30px;
  border-top: 1px solid rgba(168, 209, 255, 0.15);
  font-size: 0.9rem;
  color: var(--text-tertiary);
}

.keywords {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: center;
}

.tag-link {
  color: var(--text-secondary);
  text-decoration: none;
  padding: 4px 12px;
  background: rgba(168, 209, 255, 0.1);
  border-radius: 30px;
  font-size: 0.85rem;
  transition: all 0.3s ease;
}

.tag-link:hover {
  background: rgba(168, 209, 255, 0.2);
  color: var(--text-primary);
}

/* Стили для списков */
.projects-list,
.steps-list {
  margin: 25px 0 35px;
}

.projects-list li {
  padding: 10px 0 10px 25px;
  position: relative;
  line-height: 1.6;
}

.steps-list li {
  padding: 8px 0;
  line-height: 1.6;
}

/* Медиа-запросы */
@media (max-width: 768px) {
  .benefits-mini-list {
    grid-template-columns: 1fr;
  }
  
  .commercial-block {
    padding: 30px 20px;
  }
  
  .commercial-block h3 {
    font-size: 1.5rem;
  }
  
  .article-conclusion {
    padding: 20px;
    font-size: 1rem;
  }
}

@media (max-width: 480px) {
  .keywords {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>