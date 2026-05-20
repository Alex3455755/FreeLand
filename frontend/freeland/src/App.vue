<template>
  <div class="page-flex-wrapper">
    <router-view />
  </div>
</template>

<style>
html,
body,
#app {
  height: 100%;
  margin: 0;
  padding: 0;
}

/* Главный wrapper */
.page-flex-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* Страница внутри router-view */
.page-flex-wrapper > * {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-height: 100%;
}

/* Основной контент */
main,
.page-content,
.page-container {
  flex: 1;
  width: 100%;
  padding-bottom: 40px; /* расстояние до футера */
  box-sizing: border-box;
}

/* Футер */
.footer {
  margin-top: auto;
  flex-shrink: 0;

  padding-top: 24px;
  padding-bottom: 24px;

  width: 100%;
  box-sizing: border-box;
}

/* Чтобы секции не липли */
section:last-child,
.card:last-child,
.project-card:last-child {
  margin-bottom: 24px;
}

/* ---------------- DESKTOP ---------------- */

.container {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  box-sizing: border-box;
}

/* ---------------- TABLET ---------------- */

@media (max-width: 1200px) {
  .container {
    max-width: 100%;
    padding-left: 16px !important;
    padding-right: 16px !important;
  }
}

/* ---------------- MOBILE ---------------- */

@media (max-width: 768px) {
  .container {
    padding-left: 12px !important;
    padding-right: 12px !important;
  }

  .page-title,
  .section-title,
  .projects-title,
  .admin-title,
  .profile-name {
    font-size: clamp(1.4rem, 6vw, 2rem) !important;
    line-height: 1.2;
  }

  .ios-glass,
  .ios-glass-heavy {
    border-radius: 20px !important;
  }

  .modal-container,
  .modal-content {
    width: min(96vw, 640px) !important;
    max-height: 92vh !important;
  }

  .form-row {
    grid-template-columns: 1fr !important;
  }

  .project-actions,
  .modal-actions,
  .balance-actions {
    flex-wrap: wrap;
    gap: 12px;
  }

  .footer {
    padding-top: 20px;
    padding-bottom: 20px;
  }
}

/* ---------------- SMALL MOBILE ---------------- */

@media (max-width: 480px) {
  .nav-button,
  .action-button,
  .submit-button,
  .modal-button {
    width: 100%;
  }

  .footer {
    text-align: center;
  }
}
</style>