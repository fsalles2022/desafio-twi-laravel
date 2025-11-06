import { defineStore } from 'pinia'
import axios from 'axios'
import router from '../router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: JSON.parse(localStorage.getItem('user')) || null,
  }),

  actions: {
    async login(email, password) {
      try {
        const res = await axios.post('http://localhost:8000/api/login', {
          email,
          password,
        })

        this.token = res.data.token
        this.user = res.data.user

        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))

        // 🔹 Define o token padrão para todas as requisições
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`

        // 🔹 Redireciona para a página de vídeos
        router.push('/videos')
      } catch (err) {
        console.error('Erro no login:', err)
        alert('Login inválido')
      }
    },

    async fetchUser() {
      if (!this.token) return
      try {
        const res = await axios.get('http://localhost:8000/api/user', {
          headers: { Authorization: `Bearer ${this.token}` },
        })
        this.user = res.data
      } catch (err) {
        console.error('Erro ao buscar usuário:', err)
      }
    },

    logout() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common['Authorization']
      router.push('/')
    },
  },
})
