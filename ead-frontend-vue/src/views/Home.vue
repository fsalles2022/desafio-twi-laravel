<template>
  <!-- MENU FUTURISTA FIXO -->
  <nav class="neo-navbar">
    <div class="container d-flex align-items-center justify-content-between">

      <!-- Logo -->
      <div class="logo">
        <i class="bi bi-layers-half"></i>
        DEVNESTss
      </div>

      <!-- Navegação -->
      <ul class="nav-links d-none d-md-flex">
        <li><RouterLink to="/" class="nav-item">Home</RouterLink></li>
        <li><RouterLink to="/login" class="nav-item">Login</RouterLink></li>
        <RouterLink to="/login" class="btn btn-outline-light btn-sm px-3 fw-semibold">
          Entrar
        </RouterLink>
      </ul>

      <!-- Botão Mobile -->
      <div class="menu-btn d-md-none" @click="menuOpen = !menuOpen">
        <i class="bi bi-list"></i>
      </div>
    </div>

    <!-- MENU MOBILE -->
    <div class="mobile-menu" v-if="menuOpen">
      <RouterLink to="/" class="mobile-item" @click="menuOpen = false">Home</RouterLink>
      <a href="#courses" class="mobile-item" @click="menuOpen = false">Cursos</a>
      <a href="#destaques" class="mobile-item" @click="menuOpen = false">Destaques</a>
      <RouterLink to="/login" class="mobile-item" @click="menuOpen = false">Login</RouterLink>
    </div>
  </nav>

  <div class="home-page">

    <!-- HERO FUTURISTA -->
    <section class="hero-section d-flex justify-content-center align-items-center text-white">
      <div class="overlay"></div>

      <div class="hero-content">
        <h1 class="title">
          <span class="glow">Aprenda Laravel 11</span>
          <br />
          <span class="subtitle glow-soft">Do Zero ao Avançado</span>
        </h1>

        <p class="lead-text">Cursos imersivos • Projetos reais • Comunidade ativa</p>

        <RouterLink to="/login" class="btn-neo">
          Acessar Cursos
        </RouterLink>
      </div>
    </section>

    <!-- DESTAQUES FUTURISTAS -->
    <section class="container py-5">
      <h2 class="section-title glow-soft text-center">Destaques DEVNEST</h2>

      <div class="row g-4 mt-4">
        <div v-for="(card, i) in destaques" :key="i" class="col-md-4">
          <div class="neo-card" @mousemove="handleCardMove" @mouseleave="resetCard">
            <i :class="card.icon" class="display-5 neon-icon" :style="{ color: card.color }"></i>
            <h5 class="fw-bold mt-3">{{ card.title }}</h5>
            <p class="text-muted">{{ card.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CURSOS DINÂMICOS -->
    <section class="courses-section py-5">
      <div class="container">
        <h2 class="section-title glow-soft text-center">Cursos Disponíveis</h2>

        <div class="row g-4 mt-4">
          <div v-for="curso in cursos" :key="curso.id" class="col-md-4">
            <div class="neo-card course-card" @mousemove="handleCardMove" @mouseleave="resetCard">
              <img :src="curso.image ? 'http://localhost:8000/storage/' + curso.image : 'https://picsum.photos/600/400'"
                class="card-img-top futuristic-img" />

              <div class="p-3 text-center">
                <h5 class="fw-bold">{{ curso.title }}</h5>
                <p class="text-muted small">{{ curso.description }}</p>
              </div>

              <div class="text-center pb-3">
                <RouterLink to="/login" class="btn-neo-small">Acessar</RouterLink>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- CTA -->
    <section class="cta-section text-center text-white py-5">
      <h2 class="fw-bold glow-soft">Pronto para Começar?</h2>
      <p class="lead mt-2">Entre agora e transforme sua carreira na programação.</p>

      <RouterLink to="/login" class="btn-neo">Vamos lá 🚀</RouterLink>
    </section>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"
const menuOpen = ref(false)
import { RouterLink, RouterView, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const route = useRoute()

// === Cursos reais da API ===
const cursos = ref([])

async function loadCursos() {
  try {
    const { data } = await axios.get("http://localhost:8000/api/public/courses")
    cursos.value = data
  } catch (e) {
    console.error("Erro ao carregar cursos:", e)
  }
}

onMounted(() => {
  loadCursos()
})

// === Destaques ===
const destaques = [
  {
    icon: "bi bi-cpu",
    color: "#00e1ff",
    title: "Tecnologia Avançada",
    desc: "Aprenda com padrões modernos, arquiteturas limpas e recursos atualizados.",
  },
  {
    icon: "bi bi-lightning-charge-fill",
    color: "#c77dff",
    title: "Aprendizado Acelerado",
    desc: "Cursos diretos ao ponto, com projetos reais e desafios progressivos.",
  },
  {
    icon: "bi bi-shield-lock-fill",
    color: "#5cff7a",
    title: "Segurança & Boas Práticas",
    desc: "Aprenda desenvolvimento seguro com autenticação, permissões e APIs robustas.",
  },
]

// === PARALLAX / 3D HOVER ===
function handleCardMove(e) {
  const card = e.currentTarget
  const rect = card.getBoundingClientRect()

  const x = e.clientX - rect.left
  const y = e.clientY - rect.top

  const rotX = ((y / rect.height - 0.5) * 15).toFixed(2)
  const rotY = ((x / rect.width - 0.5) * -15).toFixed(2)

  card.style.transform = `rotateX(${rotX}deg) rotateY(${rotY}deg) scale(1.06)`
}

function resetCard(e) {
  const card = e.currentTarget
  card.style.transform = "rotateX(0) rotateY(0) scale(1)"
}
</script>

<style scoped>
/* ===== NAVBAR FUTURISTA ===== */
.neo-navbar {
  width: 100%;
  position: fixed;
  top: 0;
  z-index: 999;
  padding: 12px 0;
  background: rgba(15, 15, 25, 0.45);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 0 18px rgba(0, 238, 255, 0.15);
}

.logo {
  font-size: 1.4rem;
  font-weight: 800;
  color: #00eaff;
  letter-spacing: 1px;
  display: flex;
  align-items: center;
  gap: 6px;
  text-shadow: 0 0 8px #00eaff;
}

.nav-links {
  list-style: none;
  display: flex;
  gap: 25px;
}

.nav-item {
  font-weight: 500;
  color: #e2e2e2;
  letter-spacing: 0.5px;
  transition: 0.3s;
  text-decoration: none;
}

.nav-item:hover {
  color: #00eaff;
  text-shadow: 0 0 8px #00eaff;
}

/* Botão Mobile */
.menu-btn i {
  font-size: 1.8rem;
  color: white;
  cursor: pointer;
}

/* MENU MOBILE */
.mobile-menu {
  background: rgba(20, 20, 30, 0.9);
  backdrop-filter: blur(12px);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.mobile-item {
  font-size: 1.2rem;
  color: #e2e2e2;
  text-decoration: none;
  transition: 0.3s;
}

.mobile-item:hover {
  color: #00eaff;
  margin-left: 5px;
  text-shadow: 0 0 10px #00eaff;
}

/* ===== HERO FUTURISTA ===== */
.hero-section {
  position: relative;
  height: 90vh;
  background: url("https://images.unsplash.com/photo-1535223289827-42f1e9919769?auto=format&fit=crop&w=1950&q=80") center/cover no-repeat fixed;
  text-align: center;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(12, 12, 20, 0.65);
  backdrop-filter: blur(3px);
}

.hero-content {
  position: relative;
  z-index: 2;
  animation: fadeIn 1.4s ease;
}

.title {
  font-size: 3rem;
  font-weight: 800;
}

.subtitle {
  font-size: 2rem;
  opacity: 0.9;
}

/* ===== GLOW TEXT ===== */
.glow {
  text-shadow: 0 0 10px #00eaff;
}

.glow-soft {
  text-shadow: 0 0 6px #b37dff;
}

/* ===== NEO BUTTON ===== */
.btn-neo {
  background: rgba(255, 255, 255, 0.1);
  padding: 12px 35px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(4px);
  transition: 0.3s;
  color: white;
}

.btn-neo:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.05);
}

.btn-neo-small {
  padding: 8px 20px;
  background: transparent;
  border: 1px solid #0d6efd;
  color: #0d6efd;
  border-radius: 8px;
  transition: 0.3s;
}

.btn-neo-small:hover {
  background: #0d6efd;
  color: white;
}

/* ===== NEON CARDS ===== */
.neo-card {
  background: rgba(255, 255, 255, 0.07);
  border-radius: 16px;
  padding: 25px;
  backdrop-filter: blur(8px);
  transition: 0.3s ease;
  border: 1px solid rgba(255, 255, 255, 0.1);
  cursor: pointer;
}

.neo-card:hover {
  box-shadow: 0 0 25px rgba(0, 200, 255, 0.4);
}

/* ===== FUTURISTIC IMAGES ===== */
.futuristic-img {
  height: 200px;
  object-fit: cover;
  border-radius: 16px 16px 0 0;
}

/* ===== SECTION TITLES ===== */
.section-title {
  font-size: 2.2rem;
  font-weight: 800;
}

/* ===== CTA SECTION ===== */
.cta-section {
  background: linear-gradient(to right, #0d6efd, #6f42c1);
}

/* ANIMAÇÃO */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(25px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Bootstrap Icons */
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css");
</style>
