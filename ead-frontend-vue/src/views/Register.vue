<template>
  <div class="register-wrapper">
    <Navbar />

    <div class="background"></div>

    <div class="register-card">
      <h2 class="text-center mb-4 fw-bold text-primary">Criar Conta</h2>

      <form @submit.prevent="register" class="d-flex flex-column gap-3">

        <!-- Preview da imagem -->
        <div class="text-center">
          <img 
            v-if="previewImage" 
            :src="previewImage" 
            class="rounded-circle border mb-2" 
            style="width:100px; height:100px; object-fit:cover;" 
            alt="Preview"
          />
          <div>
            <input type="file" @change="onFileChange" accept="image/*" class="form-control" />
          </div>
        </div>

        <div>
          <label class="form-label">Nome</label>
          <input v-model="form.name" type="text" class="form-control" required />
        </div>

        <div>
          <label class="form-label">E-mail</label>
          <input v-model="form.email" type="email" class="form-control" required />
        </div>

        <div>
          <label class="form-label">Senha</label>
          <input v-model="form.password" type="password" class="form-control" required />
        </div>

        <div>
          <label class="form-label">Confirme a Senha</label>
          <input v-model="form.password_confirmation" type="password" class="form-control" required />
        </div>

        <button type="submit" class="btn btn-primary w-100">Registrar</button>

        <p class="text-center mt-2">
          Já tem uma conta? <RouterLink to="/login">Entrar</RouterLink>
        </p>
      </form>
    </div>

    <Footer />
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'

const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  image: null
})

const previewImage = ref(null)

const onFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.image = file
    previewImage.value = URL.createObjectURL(file)
  }
}

const register = async () => {
  try {
    const data = new FormData()
    data.append('name', form.name)
    data.append('email', form.email)
    data.append('password', form.password)
    data.append('password_confirmation', form.password_confirmation)

    if (form.image) data.append('image', form.image)

    await axios.post('http://localhost:8000/api/auth/register', data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    alert('Conta criada com sucesso!')
    router.push('/login')

  } catch (err) {
    console.error(err)
    if (err.response?.data?.errors) {
      const messages = Object.values(err.response.data.errors).flat()
      alert(messages.join('\n'))
    } else {
      alert('Erro ao registrar.')
    }
  }
}
</script>

<style scoped>
.register-wrapper {
  min-height: 100vh;
  position: relative;
  background: #0d6efd;
  display: flex;
  flex-direction: column;
}

/* Fundo */
.background {
  position: absolute;
  inset: 0;
  background: url("https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1600&q=80")
    center/cover no-repeat fixed;
  z-index: 0;
  filter: brightness(0.6);
  animation: moveBg 20s ease-in-out infinite alternate;
}

/* Card */
.register-card {
  position: relative;
  z-index: 2;
  margin: auto;
  margin-top: 90px;
  background: rgba(255,255,255,0.9);
  padding: 2rem;
  width: 100%;
  max-width: 420px;
  border-radius: 15px;
  backdrop-filter: blur(12px);
  box-shadow: 0 6px 25px rgba(0,0,0,0.3);
  animation: floaty 6s ease-in-out infinite;
}

@keyframes moveBg {
  from { transform: scale(1); }
  to   { transform: scale(1.1); }
}

@keyframes floaty {
  0% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(0); }
}

@media (max-width: 576px) {
  .register-card {
    padding: 1.5rem;
    max-width: 90%;
    margin-top: 120px;
  }
}

:global(body, html, #app) {
  margin: 0 !important;
  padding: 0 !important;
  height: 100% !important;
  overflow: hidden !important;
}

</style>
