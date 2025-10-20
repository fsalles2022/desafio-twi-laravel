<template>
  <header class="header">
    <img src="./assets/logo.svg" alt="Logo" class="logo" />

    <nav>
      <div v-if="auth.user">
        Olá, {{ auth.user.name }}
        <button @click="auth.logout">Logout</button>
      </div>
      <div v-else>
        <router-link to="/">Login</router-link>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
const auth = useAuthStore()
</script>

<style scoped>
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  background-color: #1976d2;
  color: white;
}
.logo {
  height: 50px;
}
button {
  margin-left: 1rem;
  padding: 0.3rem 0.6rem;
  background: #fff;
  color: #1976d2;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
button:hover {
  background: #f0f0f0;
}
nav a {
  color: white;
  margin-right: 1rem;
  text-decoration: none;
}
</style>
