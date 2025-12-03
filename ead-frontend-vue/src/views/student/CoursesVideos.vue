<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '../../stores/auth'

const route = useRoute()
const auth = useAuthStore()

const course = ref(null)
const videos = ref([])
const loading = ref(true)
const error = ref(null)

const fetchCourseVideos = async () => {
  try {
    const res = await axios.get(
      `http://localhost:8000/api/courses/${route.params.id}/videos`,
      {
        headers: {
          Authorization: `Bearer ${auth.token}`
        }
      }
    )

    course.value = res.data.course
    videos.value = res.data.videos
  } catch (err) {
    error.value = 'Você não está matriculado neste curso.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchCourseVideos)
</script>

<template>
  <div class="container py-5">

    <div v-if="loading" class="text-center">Carregando...</div>

    <div v-else-if="error" class="alert alert-danger text-center">
      {{ error }}
    </div>

    <div v-else>

      <h2 class="fw-bold text-center">{{ course.title }}</h2>
      <p class="text-muted text-center">{{ course.description }}</p>

      <div class="row g-4 mt-3">
        <div v-for="video in videos" :key="video.id" class="col-md-4">
          <div class="card shadow-sm p-3">

            <h6 class="fw-bold">🎬 {{ video.title }}</h6>
            <p class="small">{{ video.description }}</p>

            <RouterLink
              :to="`/student/video/${video.id}`"
              class="btn btn-outline-primary btn-sm"
            >
              Assistir
            </RouterLink>

          </div>
        </div>
      </div>

    </div>
  </div>
</template>
