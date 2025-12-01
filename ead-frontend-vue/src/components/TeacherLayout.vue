<script setup>
import { RouterLink, RouterView, useRoute } from 'vue-router'
import Footer from './Footer.vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const route = useRoute()
</script>

<template>
  <div class="d-flex flex-column min-vh-100 bg-light text-dark">

    <!-- LOADING -->
    <div v-if="auth.user === null" class="loading-screen">
      <div class="spinner-border text-success" role="status"></div>
    </div>

    <!-- LAYOUT PRINCIPAL -->
    <template v-else>
      
      <!-- HEADER -->
      <header class="navbar navbar-expand-lg shadow-sm teacher-header">
        <div class="container">

          <!-- LOGO -->
          <RouterLink to="/courses" class="navbar-brand fw-bold d-flex align-items-center text-white">
            <i class="bi bi-easel2-fill me-2 fs-4"></i>
            <span>DEVNEST • Professor</span>
          </RouterLink>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navTeacher">
            <span class="navbar-toggler-icon"></span>
          </button>

          <!-- MENU -->
          <div class="collapse navbar-collapse justify-content-end" id="navTeacher">
            <ul class="navbar-nav align-items-center gap-3">

              <!-- DASHBOARD -->
              <li class="nav-item">
                <RouterLink
                  to="/teacher/dashboard"
                  class="nav-link teacher-nav"
                  :class="{ active: route.path === '/teacher/dashboard' }"
                >
                  <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </RouterLink>
              </li>

              <!-- CURSOS -->
              <li class="nav-item">
                <RouterLink
                  to="/teacher/courses"
                  class="nav-link teacher-nav"
                  :class="{ active: route.path.includes('/teacher/courses') }"
                >
                  <i class="bi bi-journal-code me-1"></i> Meus Cursos
                </RouterLink>
              </li>

              <!-- VÍDEOS -->
              <li class="nav-item">
                <RouterLink
                  to="/videos"
                  class="nav-link teacher-nav"
                  :class="{ active: route.path.includes('/videos') }"
                >
                  <i class="bi bi-camera-reels me-1"></i> Meus Vídeos
                </RouterLink>
              </li>

              <!-- USER DROPDOWN -->
              <li class="nav-item dropdown" v-if="auth.user">
                <a class="nav-link dropdown-toggle text-white fw-semibold" data-bs-toggle="dropdown" href="#">
                  <img
                    :src="auth.user.image ? `http://localhost:8000/storage/${auth.user.image}` : 'https://via.placeholder.com/35?text=U'"
                    class="rounded-circle me-2"
                    width="35" height="35"
                  />
                  {{ auth.user.name }}
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow">
                  <li><RouterLink to="/profile" class="dropdown-item">Meu Perfil</RouterLink></li>
                  <li><hr class="dropdown-divider" /></li>
                  <li><button @click="auth.logout" class="dropdown-item text-danger">Sair</button></li>
                </ul>
              </li>

            </ul>
          </div>

        </div>
      </header>

      <!-- CONTEÚDO -->
      <main class="flex-grow-1 py-5">
        <div class="container">
          <RouterView />
        </div>
      </main>

      <Footer />

    </template>
  </div>
</template>

<style scoped>
/* HEADER */
.teacher-header {
  background: linear-gradient(90deg, #198754, #157347);
  padding: 15px 0;
}

/* NAV LINKS */
.teacher-nav {
  color: #e8f5e9 !important;
  font-weight: 600;
  position: relative;
  padding-bottom: 6px;
  transition: 0.25s ease;
}

.teacher-nav:hover {
  color: #ffffff !important;
  transform: translateY(-1px);
}

.teacher-nav.active {
  color: #fff !important;
}

.teacher-nav.active::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: #ffffff;
  border-radius: 10px;
}

/* LOADING */
.loading-screen {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* DROPDOWN */
.dropdown-menu {
  border-radius: 10px;
  overflow: hidden;
}
</style>
