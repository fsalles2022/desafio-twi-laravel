<template>
  <div class="login-wrapper">
    <div class="background"></div>

    <div class="login-card animate__animated animate__fadeInUp">
      <h2 class="text-center mb-4 fw-bold text-primary">Acesse sua conta</h2>

      <form @submit.prevent="login">
        <input v-model="email" type="email" placeholder="E-mail" required />
        <input v-model="password" type="password" placeholder="Senha" required />
        <button type="submit">Entrar</button>
      </form>
    </div>
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
/* Fundo com Parallax e leve animação */
.login-wrapper {
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
  overflow: hidden;
  position: relative;
}

.background {
  position: absolute;
  inset: 0;
  background: url("https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=1500&q=80")
    center/cover no-repeat fixed;
  filter: brightness(0.6);
  transform: scale(1.1);
  z-index: 0;
  animation: subtleZoom 20s ease-in-out infinite alternate;
}

/* Card de Login */
.login-card {
  position: relative;
  z-index: 2;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 2rem;
  border-radius: 16px;
  width: 100%;
  max-width: 380px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

/* Inputs */
input {
  display: block;
  width: 100%;
  margin-bottom: 15px;
  padding: 12px;
  border: 1px solid #ced4da;
  border-radius: 8px;
  transition: all 0.3s ease;
}

input:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 6px rgba(13, 110, 253, 0.3);
  outline: none;
}

/* Botão */
button {
  width: 100%;
  padding: 12px;
  background: linear-gradient(135deg, #0d6efd, #6610f2);
  border: none;
  border-radius: 8px;
  color: #fff;
  font-weight: 600;
  transition: all 0.3s ease;
}

button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(13, 110, 253, 0.3);
}

/* Animação sutil de fundo */
@keyframes subtleZoom {
  from {
    transform: scale(1);
  }
  to {
    transform: scale(1.1);
  }
}

/* Responsividade */
@media (max-width: 576px) {
  .login-card {
    padding: 1.5rem;
    border-radius: 12px;
    max-width: 90%;
  }
}

@import url("https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css");
</style>
