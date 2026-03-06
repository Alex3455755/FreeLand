<!-- src/components/DynamicBackground.vue -->
<template>
  <div class="dynamic-background">
    <div class="gradient-sphere sphere-1"></div>
    <div class="gradient-sphere sphere-2"></div>
    <div class="gradient-sphere sphere-3"></div>
    
    <div class="light-spot spot-1"></div>
    <div class="light-spot spot-2"></div>
    <div class="light-spot spot-3"></div>
    <div class="light-spot spot-4"></div>
    
    <div class="grid-overlay"></div>
    <div class="noise-overlay"></div>
    
    <div class="particles" ref="particlesContainer"></div>
  </div>
</template>

<script>
export default {
  name: 'DynamicBackground',
  mounted() {
    this.createParticles();
  },
  methods: {
    createParticles() {
      const container = this.$refs.particlesContainer;
      if (!container) return;
      
      const particleCount = 50;
      
      for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Random size
        const size = Math.random() * 4 + 1;
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        
        // Random position
        particle.style.left = `${Math.random() * 100}%`;
        particle.style.top = `${Math.random() * 100}%`;
        
        // Random color
        const colors = ['#A8D1FF', '#6BB2F0', '#3A94DF', '#FFFFFF'];
        particle.style.background = colors[Math.floor(Math.random() * colors.length)];
        particle.style.opacity = (Math.random() * 0.3 + 0.1).toString();
        
        // Random animation
        const duration = Math.random() * 30 + 20;
        const delay = Math.random() * 10;
        particle.style.animation = `floatParticle ${duration}s linear ${delay}s infinite`;
        
        container.appendChild(particle);
      }
    }
  }
}
</script>

<style scoped>
.dynamic-background {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
  overflow: hidden;
  background: linear-gradient(135deg, #0A1428 0%, #132238 50%, #0C1A2F 100%);
}

/* Градиентные сферы в синих тонах */
.gradient-sphere {
  position: absolute;
  border-radius: 50%;
  filter: blur(100px);
  opacity: 0.6;
  animation: float 25s infinite ease-in-out;
}

.sphere-1 {
  width: 800px;
  height: 800px;
  background: radial-gradient(circle at 30% 30%, #0A4D8C, #083358, #05203B);
  top: -300px;
  right: -300px;
  animation-delay: 0s;
}

.sphere-2 {
  width: 700px;
  height: 700px;
  background: radial-gradient(circle at 70% 70%, #1A6BB3, #0A4D8C, #083358);
  bottom: -250px;
  left: -250px;
  animation-delay: -8s;
}

.sphere-3 {
  width: 600px;
  height: 600px;
  background: radial-gradient(circle at 50% 50%, #2A7FC9, #1A6BB3, #0A4D8C);
  top: 40%;
  left: 50%;
  transform: translate(-50%, -50%);
  animation-delay: -15s;
}

/* Световые пятна в синих тонах */
.light-spot {
  position: absolute;
  border-radius: 50%;
  filter: blur(60px);
  animation: pulse 8s infinite alternate;
}

.spot-1 {
  width: 400px;
  height: 400px;
  top: 20%;
  left: 10%;
  background: radial-gradient(circle at center, rgba(10, 77, 140, 0.3), transparent);
  animation-delay: 0s;
}

.spot-2 {
  width: 500px;
  height: 500px;
  bottom: 10%;
  right: 5%;
  background: radial-gradient(circle at center, rgba(26, 107, 179, 0.3), transparent);
  animation-delay: -2s;
}

.spot-3 {
  width: 300px;
  height: 300px;
  top: 60%;
  left: 20%;
  background: radial-gradient(circle at center, rgba(42, 127, 201, 0.3), transparent);
  animation-delay: -4s;
}

.spot-4 {
  width: 450px;
  height: 450px;
  bottom: 30%;
  right: 15%;
  background: radial-gradient(circle at center, rgba(168, 209, 255, 0.3), transparent);
  animation-delay: -6s;
}

/* Сетка для текстуры */
.grid-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: 
    linear-gradient(rgba(168, 209, 255, 0.02) 1px, transparent 1px),
    linear-gradient(90deg, rgba(168, 209, 255, 0.02) 1px, transparent 1px);
  background-size: 50px 50px;
  pointer-events: none;
  z-index: 2;
}

/* Шум */
.noise-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle at 20% 30%, rgba(168, 209, 255, 0.03) 0%, transparent 30%),
              radial-gradient(circle at 80% 70%, rgba(168, 209, 255, 0.03) 0%, transparent 30%);
  opacity: 0.5;
  pointer-events: none;
  z-index: 3;
}

/* Частицы */
.particles {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 4;
  pointer-events: none;
}

.particle {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  filter: blur(2px);
}

@keyframes floatParticle {
  from {
    transform: translateY(0) translateX(0);
  }
  to {
    transform: translateY(-100vh) translateX(100px);
  }
}

@keyframes pulse {
  0%, 100% { opacity: 0.25; transform: scale(1); }
  50% { opacity: 0.45; transform: scale(1.2); }
}

@keyframes float {
  0%, 100% { transform: translate(0, 0) scale(1); }
  25% { transform: translate(80px, -50px) scale(1.2); }
  50% { transform: translate(-50px, 80px) scale(0.9); }
  75% { transform: translate(-80px, -30px) scale(1.1); }
}
</style>