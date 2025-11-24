import { defineStore } from 'pinia'
import axios from 'axios'
import router from '../router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: (() => {
      try {
        return JSON.parse(localStorage.getItem('user'))
      } catch {
        return null
      }
    })(),
  }),

  actions: {
    // Inicializa token no axios (chame no main.js ou App.vue)
    init() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      }
    },

    async login(email, password) {
      try {
        const res = await axios.post('http://localhost:8000/api/login', { email, password })

        this.token = res.data.token
        this.user = res.data.user

        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))

        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`

        router.push('/courses')
      } catch (err) {
        console.error('Erro no login:', err)
        alert('Login inválido')
      }
    },

    async fetchUser() {
      if (!this.token) return
      try {
        const res = await axios.get('http://localhost:8000/api/user')
        this.user = res.data
        localStorage.setItem('user', JSON.stringify(this.user)) // mantém atualizado
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
