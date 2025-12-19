import { defineStore } from 'pinia'
import axios from 'axios'
import router from '../router'

let isRefreshing = false

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token'),
    refreshToken: localStorage.getItem('refresh_token'),
    user: (() => {
      try {
        return JSON.parse(localStorage.getItem('user'))
      } catch {
        return null
      }
    })(),
  }),

  actions: {
    init() {
      if (this.token) {
        axios.defaults.headers.common.Authorization = `Bearer ${this.token}`
      }

      axios.interceptors.response.use(
        response => response,
        async error => {
          if (
            error.response?.status === 401 &&
            this.refreshToken &&
            !isRefreshing
          ) {
            isRefreshing = true
            const refreshed = await this.refreshTokenRequest()
            isRefreshing = false

            if (refreshed) {
              error.config.headers.Authorization = `Bearer ${this.token}`
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
        const res = await axios.post('http://localhost:8000/api/auth/login', {
          email,
          password,
        })

        this.setAuth(res.data)
        router.replace('/welcome')
      } catch {
        alert('Login inválido')
      }
    },

    async refreshTokenRequest() {
      try {
        const res = await axios.post('http://localhost:8000/api/auth/refresh', {
          refresh_token: this.refreshToken,
        })

        this.token = res.data.token
        localStorage.setItem('token', this.token)
        axios.defaults.headers.common.Authorization = `Bearer ${this.token}`

        return true
      } catch {
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

      axios.defaults.headers.common.Authorization = `Bearer ${this.token}`
    },

    async fetchUser() {
      if (!this.token) return
      try {
        const res = await axios.get('http://localhost:8000/api/user')
        this.user = res.data
        localStorage.setItem('user', JSON.stringify(this.user))
      } catch { }
    },

    logout() {
      this.token = null
      this.refreshToken = null
      this.user = null

      localStorage.removeItem('token')
      localStorage.removeItem('refresh_token')
      localStorage.removeItem('user')

      delete axios.defaults.headers.common['Authorization']

      // força layout público
      router.replace('/')
    }
    ,
  },
})
