<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
</script>

<template>
  <div class="d-flex">
    <!-- Sidebar -->
    <aside class="bg-dark text-white p-4 min-vh-100" style="width: 260px;">
      <h4 class="mb-4">Painel do Professor</h4>

      <ul class="nav flex-column gap-2">

        <li class="nav-item">
          <RouterLink to="/courses" class="nav-link text-white">📚 Gerenciar Cursos</RouterLink>
        </li>

        <li class="nav-item">
          <RouterLink to="/videos" class="nav-link text-white">🎬 Gerenciar Vídeos</RouterLink>
        </li>

      </ul>

      <div class="mt-4">
        <button @click="auth.logout" class="btn btn-danger w-100">Sair</button>
      </div>
    </aside>

    <!-- Conteúdo -->
    <main class="flex-grow-1 p-4">
      <slot />
    </main>
  </div>
</template>
