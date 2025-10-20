<template>
  <div style="max-width: 400px; margin: 50px auto;">
    <h2>Login</h2>

    <form @submit.prevent="login">
      <input v-model="email" type="email" placeholder="E-mail" required />
      <input v-model="password" type="password" placeholder="Senha" required />
      <button type="submit">Entrar</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const email = ref('')
const password = ref('')

const login = async () => {
  await auth.login(email.value, password.value)
}
</script>

<style scoped>
input, button {
  display: block;
  width: 100%;
  margin-bottom: 10px;
  padding: 8px;
}
</style>
