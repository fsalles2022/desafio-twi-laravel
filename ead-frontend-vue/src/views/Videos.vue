<template>
  <div class="container py-4">
    <h1 class="mb-4 text-center">🎥 Curso Laravel 11</h1>

    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <div v-else-if="videos.length" class="row">
      <MediaCardAdvanced v-for="v in videos" :key="v.id" :media="v" />
    </div>

    <div v-else class="text-muted text-center">Nenhum vídeo ou áudio disponível</div>
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
    const res = await axios.get('http://localhost:8000/api/videos/user', { headers: { Authorization: `Bearer ${auth.token}` } })
    videos.value = res.data.map(v => ({ ...v, type: getMimeType(v.filename) }))
  } catch (err) { console.error(err) }
  finally { loading.value = false }
}

const getMimeType = (filename) => {
  const ext = filename.split('.').pop().toLowerCase()
  if (['mp4','mov','avi'].includes(ext)) return 'video/mp4'
  if (['mp3'].includes(ext)) return 'audio/mp3'
  if (['jpg','jpeg','png','webp'].includes(ext)) return 'image/' + ext
  return 'application/octet-stream'
}

onMounted(fetchVideos)
</script>
