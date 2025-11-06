import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Videos from '../views/Videos.vue'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    // 🔹 Páginas públicas
    { path: '/', name: 'home', component: Home },
    { path: '/home', redirect: '/' },
    { path: '/login', name: 'login', component: Login },

    // 🔹 Páginas protegidas
    {
      path: '/videos',
      name: 'videos',
      component: Videos,
      meta: { requiresAuth: true },
    },

    // 🔹 Rota 404 (opcional, mas recomendada)
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/NotFound.vue'),
    },
  ],
})

// 🧠 Guard global de autenticação
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.token) {
    // Se precisa de login e o token não existe → redireciona pra login
    next('/login')
  } else if (to.path === '/login' && auth.token) {
    // Se o usuário já está logado, evita voltar pro login
    next('/videos')
  } else {
    next()
  }
})

export default router
