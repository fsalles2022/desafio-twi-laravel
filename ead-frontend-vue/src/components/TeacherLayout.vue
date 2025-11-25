<script setup>
import { RouterLink, RouterView, useRoute } from 'vue-router'
import Footer from './Footer.vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const route = useRoute()
</script>

<template>
  <div class="d-flex flex-column min-vh-100 bg-light text-dark">

    <!-- Spinner de carregamento do usuário -->
    <div v-if="auth.user === null" class="d-flex justify-content-center align-items-center flex-grow-1">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
    </div>

    <!-- Layout principal -->
    <template v-else>

      <!-- Header -->
      <header class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
          <RouterLink to="/" class="navbar-brand fw-bold text-uppercase d-flex align-items-center">
            🎬 DEVNEST
            <span class="fs-6 ms-2 fw-normal">Painel do Professor</span>
          </RouterLink>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavTeacher">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse justify-content-end" id="navbarNavTeacher">
            <ul class="navbar-nav align-items-center gap-2">

              <!-- Links do professor -->
              <li class="nav-item">
                <RouterLink
                  to="/teacher/dashboard"
                  class="nav-link fw-semibold"
                  :class="{ 'text-white active': route.path === '/teacher/dashboard', 'text-light': route.path !== '/teacher/dashboard' }"
                >
                  Dashboard
                </RouterLink>
              </li>

              <li class="nav-item">
                <RouterLink
                  to="/teacher/courses"
                  class="nav-link fw-semibold"
                  :class="{ 'text-white active': route.path.includes('/teacher/courses'), 'text-light': !route.path.includes('/teacher/courses') }"
                >
                  Meus Cursos
                </RouterLink>
              </li>

              <li class="nav-item">
                <RouterLink
                  to="/teacher/videos"
                  class="nav-link fw-semibold"
                  :class="{ 'text-white active': route.path.includes('/teacher/videos'), 'text-light': !route.path.includes('/teacher/videos') }"
                >
                  Meus Vídeos
                </RouterLink>
              </li>

              <!-- Dropdown do usuário logado -->
              <li class="nav-item dropdown" v-if="auth.user">
                <a
                  class="nav-link dropdown-toggle text-white fw-semibold"
                  href="#"
                  id="navbarDropdownTeacher"
                  role="button"
                  data-bs-toggle="dropdown"
                >
                  Olá, <strong>{{ auth.user.name }}</strong>!
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownTeacher">
                  <li>
                    <RouterLink to="/profile" class="dropdown-item">Meu Perfil</RouterLink>
                  </li>
                  <li><hr class="dropdown-divider" /></li>
                  <li>
                    <button @click="auth.logout" class="dropdown-item text-danger">Sair</button>
                  </li>
                </ul>
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

    </template>
  </div>
</template>

<style scoped>
.nav-link.active {
  border-bottom: 2px solid #fff;
}
</style>
