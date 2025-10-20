import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Videos from '../views/Videos.vue'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', component: Login },
    { path: '/videos', component: Videos },
    { path: '/', redirect: '/login' },
  ],
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  if (!auth.token && to.path !== '/login') next('/login')
  else next()
})

export default router
