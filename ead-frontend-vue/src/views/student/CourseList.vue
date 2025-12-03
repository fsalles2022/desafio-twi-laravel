<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
const courses = ref([])
const loading = ref(true)

const fetchCourses = async () => {
  try {
    const response = await axios.get(
      'http://localhost:8000/api/public/courses',
      {
        headers: {
          Authorization: `Bearer ${auth.token}`
        }
      }
    )

    courses.value = response.data
  } catch (err) {
    alert('Erro ao carregar cursos')
  } finally {
    loading.value = false
  }
}

const enroll = async (id) => {
  try {
    await axios.post(
      `http://localhost:8000/api/courses/${id}/enroll`,
      {},
      {
        headers: {
          Authorization: `Bearer ${auth.token}`
        }
      }
    )

    alert('✅ Matrícula realizada!')
    fetchCourses()
  } catch (err) {
    alert('Erro ao se matricular')
  }
}

onMounted(fetchCourses)
</script>

<template>
  <div class="container py-5">
    <h2 class="text-center fw-bold mb-4">📚 Cursos Disponíveis</h2>

    <div v-if="loading" class="text-center">Carregando...</div>

    <div v-else class="row g-4">
      <div v-for="course in courses" :key="course.id" class="col-md-4">
        <div class="card h-100 shadow-sm border-0">

          <img
            :src="course.image
              ? `http://localhost:8000/storage/${course.image}`
              : 'https://via.placeholder.com/400x200?text=Curso'"
            class="card-img-top"
            style="height:180px; object-fit:cover;"
          />

          <div class="card-body d-flex flex-column">
            <h5 class="fw-bold">{{ course.title }}</h5>
            <p class="small text-muted">{{ course.description }}</p>

            <button
              class="btn btn-primary mt-auto"
              @click="enroll(course.id)"
            >
              📌 Matricular-se
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>
