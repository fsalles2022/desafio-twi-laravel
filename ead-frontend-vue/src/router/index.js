import { createRouter, createWebHistory } from 'vue-router'
import Home from '../components/Home.vue'
import Login from '../views/Login.vue'
import Videos from '../views/Videos.vue'
import { useAuthStore } from '../stores/auth.js'

const router = createRouter({
  history: createWebHistory(),
  routes: [

    // =========================
    // PÚBLICAS
    // =========================
    { path: '/', name: 'home', component: Home },
    { path: '/login', name: 'login', component: Login },

    {
      path: '/register',
      name: 'register',
      component: () => import('../views/Register.vue'),
    },

    {
      path: '/welcome',
      name: 'welcome',
      component: () => import('../views/Welcome.vue'),
    },

    // =========================
    // PERFIL
    // =========================
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/Profile.vue'),
      meta: { requiresAuth: true },
    },

    // =========================
    // ADMIN
    // =========================
    {
      path: '/admin/dashboard',
      name: 'admin-dashboard',
      component: () => import('../views/admin/Dashboard.vue'),
      meta: { requiresAuth: true },
    },

    {
      path: '/admin/profile',
      name: 'admin-profile',
      component: () => import('../views/admin/Profile.vue'),
      meta: { requiresAuth: true },
    },

    {
      path: '/admin/courses',
      name: 'admin-courses',
      component: () => import('../views/admin/CourseList.vue'),
      meta: { requiresAuth: true },
    },

    // =========================
    // PROFESSOR
    // =========================
    {
      path: '/teacher/dashboard',
      name: 'teacher-dashboard',
      component: () => import('../views/teacher/Dashboard.vue'),
      meta: { requiresAuth: true },
    },

    {
      path: '/teacher/courses',
      name: 'teacher-courses',
      component: () => import('../views/teacher/CourseList.vue'),
      meta: { requiresAuth: true },
    },

    // =========================
    // ALUNO (STUDENT)
    // =========================
    {
      path: '/student/dashboard',
      name: 'student-dashboard',
      component: () => import('../views/student/Dashboard.vue'),
      meta: { requiresAuth: true },
    },

    {
      path: '/student/courses',
      name: 'student-courses',
      component: () => import('../views/student/CourseList.vue'),
      meta: { requiresAuth: true },
    },

    {
      path: '/student/my-courses',
      name: 'student-my-courses',
      component: () => import('../views/student/MyCourses.vue'),
      meta: { requiresAuth: true },
    },

    // =========================
    // VÍDEOS / CURSO
    // =========================
    {
      path: '/videos',
      name: 'videos',
      component: Videos,
      meta: { requiresAuth: true },
    },

    {
      path: '/course/:id',
      name: 'course-videos',
      component: () => import('../views/CourseVideos.vue'),
      meta: { requiresAuth: true },
    },

    // =========================
    // 404
    // =========================
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/NotFound.vue'),
    },
  ],
})

// =========================
// GUARD DE AUTENTICAÇÃO
// =========================
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.token) {
    return next('/login')
  }

  next()
})

export default router
