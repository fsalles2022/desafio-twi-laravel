import axios from 'axios'
import router from '../router'
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
})

// Inicializa token se existir
if (authStore.token) {
  api.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`
}

// Interceptor para refresh token automático
api.interceptors.response.use(
  response => response,
  async error => {
    const originalRequest = error.config

    // se 401 e ainda não tentou refresh
    if (error.response && error.response.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true

      const refreshed = await authStore.refreshTokenRequest()
      if (refreshed) {
        // atualiza header e repete a requisição
        originalRequest.headers['Authorization'] = `Bearer ${authStore.token}`
        return api(originalRequest)
      } else {
        authStore.logout()
        router.push('/')
      }
    }

    return Promise.reject(error)
  }
)

export default api
