<template>
  <footer class="footer ios-glass ios-glass-heavy">
    <div class="container">
      <p>&copy; 2026 FreeLand. Все права защищены.</p>
      <div class="footer-links">
      </div>
    </div>
  </footer>
</template>

<script setup>
// Логика компонента футера (если нужна)
</script>

<style scoped>
/* Footer */
.footer {
  margin-top: 40px;
  flex-shrink: 0;
  padding: 80px 0 50px;
  text-align: center;
  position: relative;
  z-index: 20;
  border-radius: 40px 40px 0 0;
}

.footer p {
  color: var(--text-secondary);
}

.footer-links {
  margin-top: 30px;
}

.ios-glass-link {
  color: var(--text-secondary);
  text-decoration: none;
  margin: 0 25px;
  font-weight: 500;
  padding: 10px 20px;
  border-radius: 9999px;
  transition: color 0.3s ease, border-color 0.3s ease;
  background: rgba(10, 77, 140, 0.1);
  border: 1px solid rgba(168, 209, 255, 0.15);
}

.ios-glass-link:hover {
  color: var(--text-primary);
  border-color: rgba(168, 209, 255, 0.3);
}

/* Responsive */
@media (max-width: 480px) {
  .footer-links a {
    display: inline-block;
    margin: 10px;
    padding: 8px 16px;
  }
}
</style>