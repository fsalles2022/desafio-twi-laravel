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
  <header>
    <h1>🎬 EAD</h1>
    <div v-if="auth.user">
      Olá, {{ auth.user.name }}!
      <button @click="auth.logout()">Sair</button>
    </div>
    <div v-else>
      <RouterLink to="/login">Entrar</RouterLink>
    </div>
  </header>

  <RouterView />
</template>

<style scoped>
header {
  background: #f4f4f4;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

button {
  margin-left: 10px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  cursor: pointer;
}
</style>
