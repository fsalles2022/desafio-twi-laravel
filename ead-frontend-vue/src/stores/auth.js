import { defineStore } from 'pinia'
import axios from 'axios'
import router from '../router'


export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    refreshToken: localStorage.getItem('refresh_token') || null,
    user: (() => {
      try {
        return JSON.parse(localStorage.getItem('user'))
      } catch {
        return null
      }
    })(),
  }),

  actions: {
    // Inicializa token no axios
    init() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      }

      // Interceptor para tentar refresh token em 401
      axios.interceptors.response.use(
        response => response,
        async error => {
          if (error.response && error.response.status === 401 && this.refreshToken) {
            const refreshed = await this.refreshTokenRequest()
            if (refreshed) {
              error.config.headers['Authorization'] = `Bearer ${this.token}`
              return axios(error.config)
            } else {
              this.logout()
            }
          }
          return Promise.reject(error)
        }
      )
    },

    async login(email, password) {
      try {
        const res = await axios.post('http://localhost:8000/api/auth/login', { email, password })
        this.setAuth(res.data)
        router.push('/welcome')
      } catch (err) {
        console.error('Erro no login:', err)
        alert('Login inválido')
      }
    },

    async refreshTokenRequest() {
      if (!this.refreshToken) return false
      try {
        const res = await axios.post('http://localhost:8000/api/auth/refresh', {
          refresh_token: this.refreshToken,
        })

        this.token = res.data.token
        localStorage.setItem('token', this.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        return true
      } catch (err) {
        console.error('Erro no refresh token:', err)
        this.logout()
        return false
      }
    },

    setAuth(data) {
      this.token = data.token
      this.refreshToken = data.refresh_token
      this.user = data.user

      localStorage.setItem('token', this.token)
      localStorage.setItem('refresh_token', this.refreshToken)
      localStorage.setItem('user', JSON.stringify(this.user))

      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
    },

    async fetchUser() {
      if (!this.token) return
      try {
        const res = await axios.get('http://localhost:8000/api/user')
        this.user = res.data
        localStorage.setItem('user', JSON.stringify(this.user))
      } catch (err) {
        console.error('Erro ao buscar usuário:', err)
        // refresh token automático já tratado no interceptor
      }
    },

    logout() {
      this.token = null
      this.refreshToken = null
      this.user = null
      localStorage.removeItem('token')
      localStorage.removeItem('refresh_token')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common['Authorization']
      router.push('/')
    },
  },
})
