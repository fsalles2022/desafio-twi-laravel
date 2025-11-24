<template>
  <div style="max-width: 400px; margin: 50px auto;">
    <h2>Cadastro</h2>

    <form @submit.prevent="register">
      <input v-model="name" type="text" placeholder="Nome" required />
      <input v-model="email" type="email" placeholder="E-mail" required />
      <input v-model="password" type="password" placeholder="Senha" required />
      <input v-model="password_confirmation" type="password" placeholder="Confirme a senha" required />
      <button type="submit">Cadastrar</button>
    </form>

    <p v-if="error" style="color: red">{{ error }}</p>
    <p v-if="success" style="color: green">{{ success }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')

const error = ref('')
const success = ref('')

const register = async () => {
  error.value = ''
  success.value = ''
  try {
    await auth.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    })
    success.value = 'Cadastro realizado com sucesso! Faça login.'
    name.value = ""
    email.value = ""
    password.value = ""
    password_confirmation.value = ""
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro no cadastro.'
  }
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
