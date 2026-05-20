<template>
  <div class="page-flex-wrapper">
    <router-view />
  </div>
</template>

<style>
html,
body,
#app {
  min-height: 100%;
  margin: 0;
}

.page-flex-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.page-flex-wrapper > * {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.footer {
  margin-top: auto !important;
}

/* Отступ контента от футера на страницах с общим layout */
.my-projects-page,
.projects-page,
.profile-page,
.admin-page,
.freelancers-page,
.project-detail-page,
.my-chats-page,
.team-page,
.easy-projects-page,
.home-page,
.user-detail-page,
.not-found-page {
  padding-bottom: 64px !important;
}

.page-flex-wrapper .container {
  margin-bottom: 24px;
}

@media (max-width: 1200px) {
  .container {
    max-width: 100%;
    padding-left: 16px !important;
    padding-right: 16px !important;
  }
}

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
  }
}

@media (max-width: 480px) {
  .nav-button,
  .action-button,
  .submit-button,
  .modal-button {
    width: 100%;
  }
}
</style>