import { createRouter, createWebHistory } from 'vue-router'
import Home from '../components/Home.vue'
import Login from '../views/Login.vue'
import Videos from '../views/Videos.vue'
import { useAuthStore } from '../stores/auth.js'

const router = createRouter({
  history: createWebHistory(),
  routes: [

    // PÚBLICAS
    { path: '/', name: 'home', component: Home },
    { path: '/login', name: 'login', component: Login },

    // REGISTRO DE USUÁRIOS
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/Register.vue'),
    },

    // PERFIL DO USUÁRIO
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/Profile.vue'),
      meta: { requiresAuth: true },
    },

    // DASHBOARD ADMIN
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

    {
      path: '/admin/courses',
      name: 'admin-courses',
      component: () => import('../views/admin/CourseList.vue'),
      meta: { requiresAuth: true },
    },

    // CURSOS DOS ALUNOS
    {
      path: '/videos',
      name: 'videos',
      component: Videos,
      meta: { requiresAuth: true },
    },

    // CURSOS PROFESSOR
    {
      path: '/teacher/courses',
      name: 'teacher-courses',
      component: () => import('../views/teacher/CourseList.vue'),
      meta: { requiresAuth: true },
    },

    {
      path: '/course/:id',
      name: 'course-videos',
      component: () => import('../views/CourseVideos.vue'),
      meta: { requiresAuth: true },
    },

    {
      path: '/teacher/dashboard',
      name: 'teacher-dashboard',
      component: () => import('../views/teacher/Dashboard.vue'),
    },

    {
      path: '/welcome',
      name: 'welcome',
      component: () => import('../views/Welcome.vue'),
    },

    // 404
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/NotFound.vue'),
    },
  ],
})

// Middleware de autenticação
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.token) {
    return next('/login')
  }

  next()
})

export default router
