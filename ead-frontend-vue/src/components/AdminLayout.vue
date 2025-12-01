<script setup>
import { RouterLink, RouterView, useRoute } from "vue-router";
import Footer from "./Footer.vue";
import { useAuthStore } from "../stores/auth";

const auth = useAuthStore();
const route = useRoute();
</script>

<template>
  <div class="layout-wrapper">

    <!-- LOADING -->
    <div v-if="auth.user === null" class="loading-screen">
      <div class="cyber-loader"></div>
    </div>

    <!-- LAYOUT -->
    <template v-else>

      <!-- ==== HEADER FUTURISTA ==== -->
      <header class="admin-header glass-nav shadow-neon">
        <div class="container d-flex justify-content-between align-items-center">

          <!-- LOGO -->
          <RouterLink to="/admin/dashboard" class="logo">
            <i class="bi bi-cpu me-2"></i> DEVNEST • ADMIN
          </RouterLink>

          <!-- MENU -->
          <nav class="menu">
            <RouterLink
              class="menu-item"
              to="/admin/dashboard"
              :class="{ active: route.path === '/admin/dashboard' }"
            >
              <i class="bi bi-speedometer2"></i> Dashboard
            </RouterLink>

            <RouterLink
              class="menu-item"
              to="/admin/users"
              :class="{ active: route.path.includes('/admin/users') }"
            >
              <i class="bi bi-people"></i> Usuários
            </RouterLink>

            <RouterLink
              class="menu-item"
              to="/admin/courses"
              :class="{ active: route.path.includes('/admin/courses') }"
            >
              <i class="bi bi-journal-code"></i> Cursos
            </RouterLink>

            <RouterLink
              class="menu-item"
              to="/admin/videos"
              :class="{ active: route.path.includes('/admin/videos') }"
            >
              <i class="bi bi-camera-reels"></i> Vídeos
            </RouterLink>

            <!-- USER -->
            <div class="user-area dropdown" v-if="auth.user">
              <a class="user-btn" data-bs-toggle="dropdown">
                <img
                  :src="auth.user.image
                      ? 'http://localhost:8000/storage/' + auth.user.image
                      : 'https://via.placeholder.com/40?text=U'"
                  class="avatar"
                />
                {{ auth.user.name }}
              </a>

              <ul class="dropdown-menu dropdown-menu-end glass-dropdown shadow-neon">
                <li><RouterLink to="/admin/profile" class="dropdown-item">Meu Perfil</RouterLink></li>
                <li><hr class="dropdown-divider"></li>
                <li><button @click="auth.logout" class="dropdown-item text-danger">Sair</button></li>
              </ul>
            </div>
          </nav>

        </div>
      </header>

      <!-- CONTEÚDO -->
      <main class="content-area">
        <div class="container">
          <RouterView />
        </div>
      </main>

      <Footer />
    </template>
  </div>
</template>

<style scoped>
/* ===========================
    TEMA FUTURISTA
=========================== */
.layout-wrapper {
  min-height: 100vh;
  background: radial-gradient(circle at top, #0d0d1a, #000);
  color: #d9eaff;
  overflow-x: hidden;
}

/* ===========================
    HEADER (GLASS + NEON)
=========================== */
.admin-header {
  padding: 18px 0;
  position: sticky;
  top: 0;
  z-index: 50;
  backdrop-filter: blur(15px);
}

.glass-nav {
  background: rgba(20, 20, 40, 0.55);
  border-bottom: 1px solid rgba(90, 120, 255, 0.2);
}

.shadow-neon {
  box-shadow: 0 0 20px #3b82f6aa, 0 0 40px #9333ea55 inset;
}

/* LOGO */
.logo {
  font-size: 1.4rem;
  font-weight: 700;
  letter-spacing: 1px;
  color: #71c9ff;
  text-decoration: none;
  text-shadow: 0 0 10px #00c8ff, 0 0 20px #0099ff;
  transition: 0.3s ease;
}

.logo:hover {
  transform: scale(1.05);
}

/* ===========================
    MENU
=========================== */
.menu {
  display: flex;
  align-items: center;
  gap: 22px;
}

.menu-item {
  color: #b9ceff;
  font-weight: 600;
  padding-bottom: 4px;
  position: relative;
  text-decoration: none;
  transition: 0.22s ease-out;
}

.menu-item:hover {
  color: #fff;
  text-shadow: 0 0 8px #3b82f6;
  transform: translateY(-2px);
}

.menu-item.active {
  color: #ffffff;
  text-shadow: 0 0 12px #60a5fa;
}

.menu-item.active::after {
  content: "";
  position: absolute;
  bottom: -3px;
  left: 0;
  width: 100%;
  height: 3px;
  border-radius: 3px;
  background: linear-gradient(90deg, #3b82f6, #9333ea);
}

/* ===========================
    USER AREA
=========================== */
.user-area {
  position: relative;
}

.user-btn {
  color: #e2eafd;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  text-decoration: none;
  font-weight: 600;
}

.avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  box-shadow: 0 0 8px #3b82f6;
}

.glass-dropdown {
  background: rgba(20, 20, 40, 0.75);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(100, 100, 255, 0.25);
}

/* ===========================
    LOADING FUTURISTA
=========================== */
.loading-screen {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.cyber-loader {
  width: 60px;
  height: 60px;
  border: 4px solid #0ff;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  box-shadow: 0 0 15px #0ff, 0 0 30px #00f5ff inset;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* ===========================
    CONTEÚDO
=========================== */
.content-area {
  padding: 60px 0;
}
</style>
