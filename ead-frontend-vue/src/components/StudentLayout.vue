<script setup>
import { RouterLink, RouterView } from 'vue-router'
import Footer from './Footer.vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
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

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <ul class="navbar-nav align-items-center gap-2">

            <li class="nav-item"><RouterLink to="/home" class="nav-link text-white fw-semibold">Início</RouterLink></li>
            <li class="nav-item"><RouterLink to="/videos" class="nav-link text-white fw-semibold">Vídeos</RouterLink></li>
            <li class="nav-item"><RouterLink to="/courses" class="nav-link text-white fw-semibold">Meus Cursos</RouterLink></li>
            <li class="nav-item"><RouterLink to="/sobre" class="nav-link text-white fw-semibold">Sobre</RouterLink></li>
            <li class="nav-item"><RouterLink to="/contato" class="nav-link text-white fw-semibold">Contato</RouterLink></li>

            <li class="nav-item" v-if="auth.user">
              <span class="text-white me-2">
                Olá, <strong>{{ auth.user.name }}</strong>!
              </span>
              <button @click="auth.logout" class="btn btn-danger btn-sm">Sair</button>
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
        <slot />
      </div>
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>
