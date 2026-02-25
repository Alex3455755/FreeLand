<template>
  <div class="page-flex-wrapper">
    <router-view />
  </div>
</template>

<style scoped>
.page-flex-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

#app > div {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.footer {
  margin-top: auto !important;   /* !important только для теста */
}
</style>