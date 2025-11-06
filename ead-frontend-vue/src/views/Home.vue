<template>
  <div class="home-page">
    <!-- Hero Section com Parallax -->
    <section class="hero-section text-white text-center d-flex flex-column justify-content-center align-items-center">
      <div class="overlay"></div>
      <div class="content" :style="{ transform: `translateY(${scrollY * 0.3}px)` }">
        <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInDown">
          Aprenda Laravel 11 do Zero ao Avançado 🚀
        </h1>
        <p class="lead mb-4 animate__animated animate__fadeInUp">
          Cursos práticos, projetos reais e suporte direto dos instrutores.
        </p>
        <router-link to="/login" class="btn btn-light btn-lg fw-semibold shadow-sm animate__animated animate__zoomIn">
          Acesse nossos cursos
        </router-link>
      </div>
    </section>

    <!-- Seção Destaques -->
    <section class="container py-5 reveal fade-bottom">
      <h2 class="text-center fw-bold mb-4 text-primary">Por que escolher a DEVNEST?</h2>
      <div class="row g-4">
        <div
          class="col-md-4"
          v-for="(card, i) in destaques"
          :key="i"
        >
          <div
            class="card border-0 shadow-sm h-100 text-center p-4 parallax-card reveal fade-up"
            @mousemove="handleCardMove($event)"
            @mouseleave="resetCard($event)"
          >
            <i :class="card.icon" class="display-5 mb-3" :style="{ color: card.color }"></i>
            <h5 class="fw-bold">{{ card.title }}</h5>
            <p class="text-muted">{{ card.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Cursos -->
    <section class="bg-light py-5 parallax-section">
      <div class="container">
        <h2 class="text-center fw-bold mb-5 text-primary">Cursos em Destaque</h2>

        <div class="row g-4">
          <div
            class="col-md-4"
            v-for="(curso, index) in cursos"
            :key="index"
          >
            <div
              class="card shadow-sm border-0 h-100 reveal fade-up parallax-card"
              @mousemove="handleCardMove($event)"
              @mouseleave="resetCard($event)"
            >
              <img :src="curso.img" class="card-img-top" :alt="curso.titulo" />
              <div class="card-body">
                <h5 class="card-title fw-bold">{{ curso.titulo }}</h5>
                <p class="card-text text-muted">{{ curso.desc }}</p>
              </div>
              <div class="card-footer bg-white border-0 text-center pb-4">
                <router-link to="/login" class="btn btn-outline-primary">
                  Acessar Curso
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="cta-section text-white text-center py-5 reveal fade-up">
      <div class="container">
        <h2 class="fw-bold mb-3">Pronto para dar o próximo passo?</h2>
        <p class="lead mb-4">
          Cadastre-se e comece hoje mesmo a dominar o Laravel 11.
        </p>
        <router-link to="/login" class="btn btn-light btn-lg fw-semibold shadow-sm">
          Começar Agora
        </router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref } from "vue"

const scrollY = ref(0)

const handleScroll = () => {
  scrollY.value = window.scrollY
}

onMounted(() => {
  window.addEventListener("scroll", handleScroll)
  revealElements()
})

onBeforeUnmount(() => {
  window.removeEventListener("scroll", handleScroll)
})

const cursos = [
  {
    titulo: "Laravel 11 Completo",
    desc: "Aprenda desde o básico até a criação de APIs e autenticação avançada.",
    img: "https://picsum.photos/600/400?random=1",
  },
  {
    titulo: "Vue.js 3 + Laravel",
    desc: "Desenvolva SPAs modernas com Vue.js integrado ao backend Laravel.",
    img: "https://picsum.photos/600/400?random=2",
  },
  {
    titulo: "APIs RESTful com Laravel",
    desc: "Crie APIs robustas, seguras e escaláveis do zero.",
    img: "https://picsum.photos/600/400?random=3",
  },
]

const destaques = [
  {
    icon: "bi bi-code-slash",
    color: "#0d6efd",
    title: "Foco em Código Limpo",
    desc: "Aprenda a desenvolver aplicações Laravel com boas práticas e arquitetura moderna.",
  },
  {
    icon: "bi bi-lightbulb",
    color: "#ffc107",
    title: "Aprendizado Dinâmico",
    desc: "Cursos práticos e diretos ao ponto — aprenda com desafios reais e projetos completos.",
  },
  {
    icon: "bi bi-people-fill",
    color: "#198754",
    title: "Comunidade e Suporte",
    desc: "Tire dúvidas, troque experiências e cresça junto com outros desenvolvedores.",
  },
]

// === PARALLAX NAS CARDS ===
function handleCardMove(e) {
  const card = e.currentTarget
  const rect = card.getBoundingClientRect()
  const x = e.clientX - rect.left
  const y = e.clientY - rect.top

  const rotateX = ((y / rect.height - 0.5) * 10).toFixed(2)
  const rotateY = ((x / rect.width - 0.5) * -10).toFixed(2)

  card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.05)`
}

function resetCard(e) {
  const card = e.currentTarget
  card.style.transform = "rotateX(0deg) rotateY(0deg) scale(1)"
}

// === SCROLL REVEAL ===
function revealElements() {
  const reveals = document.querySelectorAll(".reveal")

  function revealOnScroll() {
    reveals.forEach((el) => {
      const windowHeight = window.innerHeight
      const revealTop = el.getBoundingClientRect().top
      const revealPoint = 150

      if (revealTop < windowHeight - revealPoint) {
        el.classList.add("active")
      }
    })
  }

  window.addEventListener("scroll", revealOnScroll)
  revealOnScroll()
}
</script>

<style scoped>
/* Hero com Parallax */
.hero-section {
  position: relative;
  height: 90vh;
  background: url("https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=1950&q=80")
    center/cover no-repeat fixed;
  overflow: hidden;
}
.hero-section .overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(13, 110, 253, 0.65);
  backdrop-filter: blur(2px);
}
.hero-section .content {
  position: relative;
  z-index: 2;
  transition: transform 0.3s ease-out;
}

/* Cards com Parallax Interativo */
.parallax-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  transform-style: preserve-3d;
  perspective: 1000px;
}
.parallax-card:hover {
  box-shadow: 0 20px 35px rgba(0, 0, 0, 0.15);
}

/* Efeito Parallax de Fundo */
.parallax-section {
  background-attachment: fixed;
  background-image: linear-gradient(to bottom, #ffffff, #f8f9fa);
}

/* CTA */
.cta-section {
  background: linear-gradient(135deg, #0d6efd, #6610f2);
}

/* Efeito de Aparecer (reveal) */
.reveal {
  opacity: 0;
  transform: translateY(40px);
  transition: all 0.8s ease;
}
.reveal.active {
  opacity: 1;
  transform: translateY(0);
}

/* Bootstrap Icons */
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css");
</style>
