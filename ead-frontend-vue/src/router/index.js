import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Videos from '../views/Videos.vue'
import { useAuthStore } from '../stores/auth.js'


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
      component: () => import('../views/admin/CourseList.vue'),
      meta: { requiresAuth: true },
    },

       // LISTA DE CURSOS
    {
      path: '/teacher/courses',
      name: 'teacher-courses',
      component: () => import('../views/teacher/CourseList.vue'),
      meta: { requiresAuth: true },
    },
    // VÍDEOS DE UM CURSO
    {
      path: '/course/:id',
      name: 'course-videos',
      component: () => import('../views/CourseVideos.vue'),
      meta: { requiresAuth: true },
    },

    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/Profile.vue'),
      meta: { requiresAuth: true },
    },

    {
      path: '/register',
      name: 'register',
      component: () => import('../views/Register.vue'),
    },

    {
      path: '/teacher/dashboard',
      name: 'teacher-dashboard',
      component: () => import('../views/teacher/Dashboard.vue'),
    },

    {
      path: '/admin/dashboard',
      name: 'admin-dashboard',
      component: () => import('../views/admin/Dashboard.vue'),
    },

    {
      path: '/admin/profile',
      name: 'admin-profile',
      component: () => import('../views/admin/Profile.vue'),
    },

      // LISTA DE CURSOS DO ADMINISTRADOR
    {
      path: '/admin/courses',
      name: 'courses',
      component: () => import('../views/admin/CourseList.vue'),
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

  next()
})

export default router
