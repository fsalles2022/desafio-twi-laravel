<template>
  <div class="login-wrapper">
    <div class="background"></div>

    <div class="login-card">
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
import { ref } from "vue"
import { useAuthStore } from "../stores/auth"

const auth = useAuthStore()
const email = ref("")
const password = ref("")

const login = async () => {
  await auth.login(email.value, password.value)
}
</script>

<style scoped>
/* Wrapper geral */
.login-wrapper {
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: #0d6efd;
  position: relative;
}

/* Fundo com efeito suave */
.background {
  position: absolute;
  inset: 0;
  background: url("https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1600&q=80")
    center/cover no-repeat fixed;
  z-index: 0;
  filter: brightness(0.6);
  animation: moveBg 20s ease-in-out infinite alternate;
}

/* Card central */
.login-card {
  position: relative;
  z-index: 2;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-radius: 15px;
  padding: 2rem;
  width: 100%;
  max-width: 380px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
  animation: floaty 6s ease-in-out infinite;
}

/* Inputs e botão */
input {
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

/* Animações */
@keyframes moveBg {
  from {
    transform: scale(1);
  }
  to {
    transform: scale(1.1);
  }
}

@keyframes floaty {
  0% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
  100% {
    transform: translateY(0);
  }
}

/* Responsivo */
@media (max-width: 576px) {
  .login-card {
    padding: 1.5rem;
    border-radius: 10px;
    max-width: 90%;
  }
}
</style>
