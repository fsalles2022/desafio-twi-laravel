<template>
  <div class="home-page">
    <!-- Seção Hero -->
    <section class="hero-section text-white text-center d-flex flex-column justify-content-center align-items-center">
      <div class="overlay"></div>
      <div class="content">
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
    <section class="container py-5">
      <h2 class="text-center fw-bold mb-4 text-primary">Por que escolher a DEVNEST?</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100 text-center p-4">
            <i class="bi bi-code-slash display-5 text-primary mb-3"></i>
            <h5 class="fw-bold">Foco em Código Limpo</h5>
            <p class="text-muted">
              Aprenda a desenvolver aplicações Laravel com boas práticas e arquitetura moderna (Repository, Services, Eloquent).
            </p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100 text-center p-4">
            <i class="bi bi-lightbulb display-5 text-warning mb-3"></i>
            <h5 class="fw-bold">Aprendizado Dinâmico</h5>
            <p class="text-muted">
              Nossos cursos são objetivos e práticos — aprenda com desafios reais e projetos completos.
            </p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100 text-center p-4">
            <i class="bi bi-people-fill display-5 text-success mb-3"></i>
            <h5 class="fw-bold">Comunidade e Suporte</h5>
            <p class="text-muted">
              Tire dúvidas, troque experiências e cresça junto com outros desenvolvedores.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Seção Cursos em Destaque -->
    <section class="bg-light py-5">
      <div class="container">
        <h2 class="text-center fw-bold mb-5 text-primary">Cursos em Destaque</h2>

        <div class="row g-4">
          <div class="col-md-4" v-for="(curso, index) in cursos" :key="index">
            <div class="card shadow-sm border-0 h-100">
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

    <!-- CTA Final -->
    <section class="cta-section text-white text-center py-5">
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
</script>

<style scoped>
/* Hero */
.hero-section {
  position: relative;
  height: 80vh;
  background: url("https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=1950&q=80")
    center/cover no-repeat;
}
.hero-section .overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(13, 110, 253, 0.7);
}
.hero-section .content {
  position: relative;
  z-index: 2;
}

/* CTA */
.cta-section {
  background: linear-gradient(135deg, #0d6efd, #6610f2);
}

/* Cards */
.card img {
  height: 200px;
  object-fit: cover;
}

/* Bootstrap Icons */
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css");
</style>
