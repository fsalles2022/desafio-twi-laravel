<template>
  <div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card p-4 shadow-sm" style="width: 400px;">
      <h3 class="mb-3 text-center">Criar Conta</h3>
      <form @submit.prevent="register">

        <div class="mb-3">
          <label for="name" class="form-label">Nome</label>
          <input v-model="form.name" type="text" class="form-control" id="name" required />
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input v-model="form.email" type="email" class="form-control" id="email" required />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Senha</label>
          <input v-model="form.password" type="password" class="form-control" id="password" required />
        </div>

        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Confirme a Senha</label>
          <input v-model="form.password_confirmation" type="password" class="form-control" id="password_confirmation" required />
        </div>

        <button type="submit" class="btn btn-primary w-100">Registrar</button>

        <p class="text-center mt-3">
          Já tem uma conta?
          <RouterLink to="/login">Entrar</RouterLink>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const register = async () => {
  try {
    // envia para a rota do Laravel
    await axios.post('http://localhost:8000/api/auth/register', form)
    alert('Conta criada com sucesso! Você será redirecionado para o login.')
    router.push('/login')
  } catch (err) {
    console.error(err)
    if (err.response && err.response.data.errors) {
      // mostra mensagens de validação do Laravel
      const messages = Object.values(err.response.data.errors).flat()
      alert(messages.join('\n'))
    } else {
      alert('Erro ao registrar. Verifique os dados.')
    }
  }
}
</script>

<style scoped>
body {
  margin: 0;
}
</style>
