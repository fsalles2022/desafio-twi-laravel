<template>
  <div class="container py-4">
    <h1 class="mb-4 text-center">🎥 Video Aulas</h1>

    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <div v-else-if="videos.length" class="row g-4">
      <MediaCardAdvanced v-for="v in videos" :key="v.id" :media="v" @toggleWatched="toggleWatched" />

    </div>

    <div v-else class="text-muted text-center">
      Nenhum vídeo ou áudio disponível
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../stores/auth'
import MediaCardAdvanced from '../components/MediaCardAdvanced.vue'

const videos = ref([])
const loading = ref(true)
const auth = useAuthStore()

const fetchVideos = async () => {
  try {
    const res = await axios.get(
      'http://localhost:8000/api/videos/user',
      { headers: { Authorization: `Bearer ${auth.token}` } }
    )

    videos.value = res.data.map(v => ({
      ...v,
      type: getMimeType(v.filename),
      url: `http://localhost:4000/stream/${v.filename}`
    }))
  } catch (err) {
    console.error('Erro ao buscar vídeos:', err)
  } finally {
    loading.value = false
  }
}

// ✅ FUNÇÃO DE MARCAR / DESMARCAR ASSISTIDO
const toggleWatched = async (video) => {
  try {
    if (!video.watched) {
      await axios.post(
        `http://localhost:8000/api/videos/${video.id}/watched`,
        {},
        { headers: { Authorization: `Bearer ${auth.token}` } }
      )
      video.watched = true
    } else {
      await axios.delete(
        `http://localhost:8000/api/videos/${video.id}/watched`,
        { headers: { Authorization: `Bearer ${auth.token}` } }
      )
      video.watched = false
    }
  } catch (err) {
    console.error(err)
    alert('Erro ao atualizar status do vídeo')
  }
}

// Função para definir tipo MIME do arquivo
const getMimeType = (filename) => {
  const ext = filename.split('.').pop().toLowerCase()
  if (['mp4', 'mov', 'avi'].includes(ext)) return 'video/mp4'
  if (['mp3'].includes(ext)) return 'audio/mp3'
  if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) return 'image/' + ext
  return 'application/octet-stream'
}

onMounted(fetchVideos)
</script>
