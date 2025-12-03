<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../../stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()
const courses = ref([])
const loading = ref(true)

const fetchMyCourses = async () => {
  try {
    const res = await axios.get(
      'http://localhost:8000/api/courses',
      {
        headers: {
          Authorization: `Bearer ${auth.token}`
        }
      }
    )

    courses.value = res.data.my_courses
  } catch (err) {
    alert('Erro ao carregar meus cursos')
  } finally {
    loading.value = false
  }
}

onMounted(fetchMyCourses)
</script>

<template>
  <div class="container py-5">
    <h2 class="fw-bold text-center mb-4">🎓 Meus Cursos</h2>

    <div v-if="loading" class="text-center">Carregando...</div>

    <div v-else-if="courses.length === 0" class="text-center text-muted">
      Você ainda não está matriculado em nenhum curso.
    </div>

    <div v-else class="row g-4">
      <div class="col-md-4" v-for="c in courses" :key="c.id">
        <div class="card shadow-sm h-100">
          <img :src="`http://localhost:8000/storage/${c.course_image}`" alt="Curso"
            style="height: 180px; object-fit: cover" />

          <div class="card-body">
            <h5 class="card-title">{{ c.title }}</h5>
            <p class="text-muted">{{ c.description }}</p>
            <p class="text-muted">Id Curso: {{ c.id }}</p>

            <router-link :to="`/course/${c.id}`" class="btn btn-primary w-100">
              Acessar Aulas →
            </router-link>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
