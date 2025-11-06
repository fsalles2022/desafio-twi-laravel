<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import { RouterView, RouterLink, useRouter } from 'vue-router'
import Footer from './components/Footer.vue'

const auth = useAuthStore()
const router = useRouter()

onMounted(() => {
  auth.fetchUser()
})
</script>

<template>
  <div class="d-flex flex-column min-vh-100 bg-light text-dark">
    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
      <div class="container">
        <RouterLink to="/" class="navbar-brand fw-bold text-uppercase d-flex align-items-center">
          🎬 DEVNEST
          <span class="fs-6 ms-2 fw-normal">Cursos Profissionalizantes em Laravel</span>
        </RouterLink>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <ul class="navbar-nav align-items-center gap-2">
            <li class="nav-item">
              <RouterLink to="/home" class="nav-link text-white fw-semibold">
                Início
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink to="/videos" class="nav-link text-white fw-semibold">
                Meus Cursos
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink to="/sobre" class="nav-link text-white fw-semibold">
                Sobre
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink to="/contato" class="nav-link text-white fw-semibold">
                Contato
              </RouterLink>
            </li>

            <li class="nav-item" v-if="auth.user">
              <span class="text-white me-2">
                Olá, <strong>{{ auth.user.name }}</strong>!
              </span>
              <button @click="auth.logout" class="btn btn-danger btn-sm">
                Sair
              </button>
            </li>

            <li class="nav-item" v-else>
              <RouterLink to="/login" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                Entrar
              </RouterLink>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <!-- Conteúdo -->
    <main class="flex-grow-1 py-5">
      <div class="container">
        <RouterView />
      </div>
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<style scoped>
.navbar-brand span {
  font-size: 0.9rem;
  opacity: 0.9;
}

.nav-link:hover {
  text-decoration: underline;
}

main {
  background-color: #f8f9fa;
  color: #212529;
}

h1,
h2,
h3 {
  color: #0d6efd;
}

.btn-outline-light:hover {
  color: #0d6efd;
  background-color: white;
  border-color: white;
}
</style>
