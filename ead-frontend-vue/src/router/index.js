import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Videos from '../views/Videos.vue'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    // PÚBLICAS
    { path: '/', name: 'home', component: Home },
    { path: '/login', name: 'login', component: Login },

    // PROTEGIDAS
    {
      path: '/videos',
      name: 'videos',
      component: Videos,
      meta: { requiresAuth: true },
    },

    // LISTA DE CURSOS
    {
      path: '/courses',
      name: 'courses',
      component: () => import('../views/CourseList.vue'),
      meta: { requiresAuth: true },
    },

    // VÍDEOS DE UM CURSO
    {
      path: '/course/:id',
      name: 'course-videos',
      component: () => import('../views/CourseVideos.vue'),
      meta: { requiresAuth: true },
    },

    // 404
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/NotFound.vue'),
    },
  ],
})

// 🔥 Middleware de autenticação + definição de Layout
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()

  // se a rota precisa de login
  if (to.meta.requiresAuth && !auth.token) {
    return next('/login')
  }

  // define dinamicamente o layout APÓS logado
  if (auth.user) {
    const isTeacher = auth.user.roles?.includes('teacher')

    auth.currentLayout = isTeacher
      ? 'TeacherLayout'
      : 'StudentLayout'
  }

  next()
})

export default router
