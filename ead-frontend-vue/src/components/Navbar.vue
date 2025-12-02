<template>
  <nav class="neo-navbar">
    <div class="container d-flex align-items-center justify-content-between">

      <!-- Logo -->
      <div class="logo">
        <i class="bi bi-layers-half"></i>
        DEVNEST
      </div>

      <!-- Navegação Desktop -->
      <ul class="nav-links d-none d-md-flex">
        <li><RouterLink to="/" class="nav-item">Home</RouterLink></li>
        <li><a href="#courses" class="nav-item">Cursos</a></li>
        <li><a href="#destaques" class="nav-item">Destaques</a></li>

        <li>
          <RouterLink to="/login" class="btn-neo-nav">
            Entrar
          </RouterLink>
        </li>
      </ul>

      <!-- Botão Mobile -->
      <div class="menu-btn d-md-none" @click="menuOpen = !menuOpen">
        <i :class="menuOpen ? 'bi bi-x' : 'bi bi-list'"></i>
      </div>
    </div>

    <!-- MENU MOBILE -->
    <div class="mobile-menu" v-if="menuOpen">
      <RouterLink to="/" class="mobile-item" @click="menuOpen = false">Home</RouterLink>
      <a class="mobile-item" href="#courses" @click="menuOpen = false">Cursos</a>
      <a class="mobile-item" href="#destaques" @click="menuOpen = false">Destaques</a>
      <RouterLink to="/login" class="mobile-item" @click="menuOpen = false">Login</RouterLink>
    </div>

  </nav>
</template>

<script setup>
import { ref } from "vue"
import { RouterLink } from "vue-router"

const menuOpen = ref(false)
</script>

<style scoped>
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
  transition: 0.3s;
  text-decoration: none;
}

.nav-item:hover {
  color: #00eaff;
  text-shadow: 0 0 8px #00eaff;
}

.btn-neo-nav {
  padding: 8px 18px;
  border-radius: 10px;
  border: 1px solid #00eaff;
  color: #00eaff !important;
  transition: 0.3s;
}

.btn-neo-nav:hover {
  background: #00eaff;
  color: #000 !important;
}

.mobile-menu {
  background: rgba(20, 20, 30, 0.9);
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 18px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-item {
  font-size: 1.2rem;
  color: #e2e2e2;
  text-decoration: none;
}
.mobile-item:hover {
  color: #00eaff;
}

.menu-btn {
  font-size: 1.8rem;
  color: #e2e2e2;
  cursor: pointer;
}
</style>
