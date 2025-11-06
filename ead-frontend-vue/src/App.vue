<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import { RouterView, RouterLink } from 'vue-router'

const auth = useAuthStore()

onMounted(() => {
  auth.fetchUser()
})
</script>

<template>
  <header class="bg-primary text-white py-3 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
      <h1 class="h4 m-0 fw-bold text-uppercase">
        🎬 DEVNEST — Cursos Profissionalizantes em Laravel
      </h1>

      <div v-if="auth.user" class="d-flex align-items-center gap-2">
        <span>Olá, <strong>{{ auth.user.name }}</strong>!</span>
        <button @click="auth.logout" class="btn btn-danger btn-sm px-3">
          Sair
        </button>
      </div>

      <div v-else>
        <RouterLink to="/home" class="btn btn-light btn-sm px-3 fw-semibold">
          Entrar
        </RouterLink>
      </div>
    </div>
  </header>

  <main class="container my-5">
    <RouterView />
  </main>
</template>

<style scoped>
h1 {
  letter-spacing: 0.5px;
}
</style>
