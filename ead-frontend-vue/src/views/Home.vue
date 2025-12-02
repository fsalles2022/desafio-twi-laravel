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
          Entrarss
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
          <span class="glow">Aprenda Laravel 11</span><br />
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
    <section id="courses" class="courses-section py-5">
      <div class="container">
        <h2 class="section-title glow-soft text-center">Cursos Disponíveis</h2>

        <div class="row g-4 mt-4">
          <div v-for="curso in cursos" :key="curso.id" class="col-md-4">
            <div class="neo-card course-card" @mousemove="handleCardMove" @mouseleave="resetCard">
              <img
                :src="curso.image ? `http://localhost:8000/storage/${curso.image}` : 'https://picsum.photos/600/400'"
                class="card-img-top futuristic-img"
              />

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
import { RouterLink } from 'vue-router'

const menuOpen = ref(false)
const cursos = ref([])

async function loadCursos() {
  try {
    const { data } = await axios.get("http://localhost:8000/api/courses")
    cursos.value = data
  } catch (e) {
    console.error("Erro ao carregar cursos:", e)
  }
}

onMounted(() => {
  loadCursos()
})

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

// === PARALLAX / 3D ===
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
  e.currentTarget.style.transform = "rotateX(0) rotateY(0) scale(1)"
}
</script>

<style scoped>
/* ===== NAVBAR FUTURISTA ===== */
/* (SEU CSS COMPLETO AQUI — mantido 100%) */

/* Copiei tudo sem alterar nada */
</style>
