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

     // REGISTRO DE USUÁRIOS
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/Register.vue'),
    },

     // PERFIL DE USUÁRIO
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/Profile.vue'),
      meta: { requiresAuth: true },
    },

    // DASHBOARD DOS ADMINISTRADORES
    {
      path: '/admin/dashboard',
      name: 'admin-dashboard',
      component: () => import('../views/admin/Dashboard.vue'),
    },


     // PERFIL DO ADMINISTRADOR
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

       // LISTA DE TODOS OS CURSOS NA DASHBOARD DO ADMINISTRADOR
    {
      path: '/admin/courses',
      name: 'admin-courses',
      component: () => import('../views/admin/CourseList.vue'),
      meta: { requiresAuth: true },
    },

    // LISTA DE VIDEOS PARA ALUNOS
    {
      path: '/videos',
      name: 'videos',
      component: Videos,
      meta: { requiresAuth: true },
    },


       // LISTA DE CURSOS DOS PROFESSORES
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
    
   

   

    // DASHBOARD DOS PROFESSORES
    {
      path: '/teacher/dashboard',
      name: 'teacher-dashboard',
      component: () => import('../views/teacher/Dashboard.vue'),
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
