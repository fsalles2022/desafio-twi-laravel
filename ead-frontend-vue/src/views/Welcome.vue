<script setup>
import { useAuthStore } from "../stores/auth"

const auth = useAuthStore()
</script>

<template>
  <div class="welcome-wrapper">

    <!-- EFEITO DE FUNDO -->
    <div class="bg-orb orb1"></div>
    <div class="bg-orb orb2"></div>
    <div class="scanline"></div>

    <!-- CONTEÚDO DO CARD -->
    <div class="welcome-card">

      <div class="text-center">
        <h1 class="title">
          <span class="glow">Bem-vindo,</span> {{ auth.user?.name }}
        </h1>
        <p class="subtitle">
          O futuro da sua plataforma começa aqui.
        </p>
      </div>

      <div class="stats">
        <div class="stat-box">
          <i class="bi bi-speedometer2"></i>
          <h3>Rápido</h3>
          <p>Interface ultra responsiva</p>
        </div>

        <div class="stat-box">
          <i class="bi bi-shield-lock"></i>
          <h3>Seguro</h3>
          <p>Proteção de dados avançada</p>
        </div>

        <div class="stat-box">
          <i class="bi bi-stars"></i>
          <h3>Moderno</h3>
          <p>Tecnologia de última geração</p>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
/* ---------------------- */
/*   FUNDOS FUTURISTAS    */
/* ---------------------- */
.welcome-wrapper {
  min-height: 70vh;
  background: #050510;
  position: relative;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Orbs luminosas */
.bg-orb {
  position: absolute;
  width: 550px;
  height: 550px;
  border-radius: 50%;
  filter: blur(120px);
  opacity: 0.55;
  animation: pulse 8s infinite ease-in-out;
}

.orb1 {
  top: -100px;
  left: -120px;
  background: #0d6efd;
}

.orb2 {
  bottom: -120px;
  right: -150px;
  background: #8a2be2;
}

/* Scanline futurista */
.scanline {
  position: absolute;
  width: 100%;
  height: 2px;
  background: rgba(233, 104, 11, 0.1);
  top: 0;
  animation: scan 6s linear infinite;
  opacity: 0.6;
}

@keyframes scan {
  0% { top: 0; }
  100% { top: 100%; }
}

@keyframes pulse {
  0% { transform: scale(1); opacity: 0.6; }
  50% { transform: scale(1.15); opacity: 0.4; }
  100% { transform: scale(1); opacity: 0.6; }
}

/* ----------------------- */
/*        CARD MAIN        */
/* ----------------------- */
.welcome-card {
  width: 95%;
  max-width: 900px;
  padding: 50px;
  background: rgba(255,255,255,0.05);
  border-radius: 25px;
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255,255,255,0.1);
  box-shadow: 0 0 40px rgba(219, 219, 7, 0.4), inset 0 0 25px rgba(255,255,255,0.05);
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ----------------------- */
/*       TEXTOS TOP        */
/* ----------------------- */
.title {
  font-size: 2.8rem;
  font-weight: 700;
  color: #fff;
}

.glow {
  color: #0d6efd;
  text-shadow: 0 0 12px rgba(0, 110, 255, 0.6);
}

.subtitle {
  margin-top: 8px;
  color: #d0d0ff;
  opacity: 0.8;
  font-size: 1.2rem;
}

/* ----------------------- */
/*     CAIXAS DE STATUS    */
/* ----------------------- */
.stats {
  margin-top: 40px;
  display: flex;
  justify-content: space-between;
  gap: 20px;
}

.stat-box {
  flex: 1;
  text-align: center;
  padding: 25px;
  background: rgba(255,255,255,0.06);
  border-radius: 18px;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.1);
  transition: 0.3s ease;
  cursor: pointer;
}

.stat-box:hover {
  transform: translateY(-6px);
  box-shadow: 0 0 20px rgba(0, 110, 255, 0.3);
}

.stat-box i {
  font-size: 2.4rem;
  margin-bottom: 10px;
  color: #0d6efd;
}

.stat-box h3 {
  color: #fff;
  font-size: 1.4rem;
}

.stat-box p {
  color: #d9d9ff;
  font-size: 0.95rem;
  opacity: 0.8;
}

/* Responsivo */
@media (max-width: 768px) {
  .stats {
    flex-direction: column;
  }
}
</style>
