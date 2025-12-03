<template>
  <div class="page-wrapper">
    <!-- NAVBAR FUTURISTA -->
    <nav class="neo-navbar">
      <div class="container d-flex align-items-center justify-content-between">

        <!-- Logo -->
        <div class="logo">
          <i class="bi bi-layers-half"></i>
          DEVNEST
        </div>

        <!-- Navegação Desktop -->
        <ul class="nav-links d-none d-md-flex">
          <li>
            <RouterLink to="/" class="nav-item">Home</RouterLink>
          </li>
          <li>
            <RouterLink to="/login" class="btn-neo-nav active">
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

    <!-- LOGIN PAGE -->
    <div class="login-wrapper">
      <div class="background"></div>

      <div class="login-card">
        <h2 class="text-center mb-4 fw-bold text-primary">Acesse sua conta</h2>

        <form @submit.prevent="login">
          <input v-model="email" type="email" placeholder="E-mail" required />
          <input v-model="password" type="password" placeholder="Senha" required />
          <button type="submit">Entrar</button>
        </form>

        <div class="text-center mt-3">
          <small class="text-muted">
            Não possui uma conta?
            <RouterLink to="/register">Cadastre-se</RouterLink>
          </small>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
    <Footer />
  </div>
</template>

<script setup>
import { ref } from "vue"
import { useAuthStore } from "../stores/auth"
import Footer from "../components/Footer.vue"

const menuOpen = ref(false)

const auth = useAuthStore()
const email = ref("")
const password = ref("")

const login = async () => {
  await auth.login(email.value, password.value)
}
</script>

<style scoped>
/* TRAVA ABSOLUTA DE SCROLL LATERAL */
:global(html, body, #app) {
  margin: 0;
  padding: 0;
  overflow-x: hidden !important;
  width: 100%;
}

/* Wrapper geral */
.page-wrapper {
  overflow-x: hidden;
  width: 100%;
}

/* NAVBAR */
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

.btn-neo-nav:hover,
.btn-neo-nav.active {
  background: #00eaff;
  color: #000 !important;
}

/* MOBILE */
.mobile-menu {
  background: rgba(20, 20, 30, 0.95);
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.mobile-item {
  font-size: 1.2rem;
  color: #e2e2e2;
  text-decoration: none;
}

/* LOGIN */
.login-wrapper {
  padding-top: 120px;
  height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  /* <- MATADOR DAS DUAS BARRAS */
}

.background {
  position: absolute;
  inset: 0;
  background: url("https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1600&q=80") center/cover no-repeat;
  filter: brightness(0.6);
  animation: moveBg 20s ease-in-out infinite alternate;
  z-index: 0;
}

.login-card {
  position: relative;
  z-index: 2;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 15px;
  padding: 2rem;
  width: 100%;
  max-width: 380px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
  animation: floaty 6s ease-in-out infinite;
}

input {
  width: 100%;
  margin-bottom: 15px;
  padding: 12px;
  border-radius: 8px;
}

button {
  width: 100%;
  padding: 12px;
  background: linear-gradient(135deg, #0d6efd, #6610f2);
  border: none;
  border-radius: 8px;
  color: white;
  font-weight: 600;
}

/* ANIMAÇÕES */
@keyframes moveBg {
  from {
    transform: scale(1);
  }

  to {
    transform: scale(1.1);
  }
}

@keyframes floaty {
  0% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-10px);
  }

  100% {
    transform: translateY(0);
  }
}
</style>
