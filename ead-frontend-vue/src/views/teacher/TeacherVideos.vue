<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const videos = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/videos/teacher')
    videos.value = res.data
  } catch (err) {
    console.error('Erro ao carregar vídeos do professor:', err)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <h3 class="fw-bold mb-4">Meus Vídeos</h3>

    <div v-if="loading" class="text-center py-4">
      <div class="spinner-border"></div>
    </div>

    <div v-else-if="videos.length === 0" class="alert alert-info">
      Você ainda não cadastrou nenhum vídeo.
    </div>

    <div v-else class="row g-3">
      <div v-for="video in videos" :key="video.id" class="col-md-4">
        <div class="card shadow-sm h-100">

          <video
            class="card-img-top"
            controls
            style="height: 180px; object-fit: cover;"
          >
            <source :src="`http://localhost:8000/api/video/stream/${video.filename}`" type="video/mp4" />
          </video>

          <div class="card-body">
            <h5 class="card-title">{{ video.title }}</h5>
            <p class="card-text small text-muted">{{ video.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
