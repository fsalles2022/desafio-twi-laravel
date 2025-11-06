<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../stores/auth'
import MediaCardAdvanced from './MediaCardAdvanced.vue'

const videos = ref([])
const loading = ref(true)
const filter = ref('all')
const search = ref('')
const page = ref(1)
const pageSize = 6

const auth = useAuthStore()

const fetchVideos = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/videos/user', {
      headers: { Authorization: `Bearer ${auth.token}` }
    })
    videos.value = res.data
  } catch (err) {
    console.error('Erro ao carregar vídeos:', err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchVideos)

const filteredVideos = computed(() => {
  return videos.value.filter(v => {
    const matchesFilter = filter.value === 'all' ||
      (filter.value === 'video' && !v.filename.endsWith('.mp3') && !v.filename.endsWith('.wav')) ||
      (filter.value === 'audio' && (v.filename.endsWith('.mp3') || v.filename.endsWith('.wav')))
    const matchesSearch = v.title.toLowerCase().includes(search.value.toLowerCase())
    return matchesFilter && matchesSearch
  })
})

const totalPages = computed(() => Math.ceil(filteredVideos.value.length / pageSize))
const paginatedVideos = computed(() => {
  const start = (page.value - 1) * pageSize
  return filteredVideos.value.slice(start, start + pageSize)
})
</script>

<template>
  <div class="container py-4">

    <!-- Barra de busca e filtros -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-2">
      <input v-model="search" type="text" class="form-control" placeholder="Buscar por título..."
        style="max-width: 300px;" />
      <div class="btn-group">
        <button class="btn btn-outline-primary" :class="{ active: filter === 'all' }"
          @click="filter = 'all'">Todos</button>
        <button class="btn btn-outline-primary" :class="{ active: filter === 'video' }"
          @click="filter = 'video'">Vídeos</button>
        <button class="btn btn-outline-primary" :class="{ active: filter === 'audio' }"
          @click="filter = 'audio'">Áudios</button>
      </div>
    </div>

    <!-- Loader -->
    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
    </div>

    <!-- Lista de vídeos -->
    <div v-else-if="filteredVideos.length" class="row g-3">
      <div class="col-12 col-sm-6 col-lg-4" v-for="video in paginatedVideos" :key="video.id">
        <MediaCardAdvanced :media="video" />
      </div>
    </div>

    <!-- Mensagem quando não há vídeos -->
    <div v-else class="text-center text-muted mt-5">
      Nenhum vídeo ou áudio disponível
    </div>

    <!-- Paginação -->
    <div v-if="filteredVideos.length > pageSize" class="d-flex justify-content-center align-items-center mt-4 gap-3">
      <button class="btn btn-secondary" :disabled="page === 1" @click="page--">⬅️</button>
      <span>Página {{ page }} de {{ totalPages }}</span>
      <button class="btn btn-secondary" :disabled="page === totalPages" @click="page++">➡️</button>
    </div>

  </div>
</template>

<style scoped>
/* Melhora o visual do botão ativo */
.btn-group .btn.active {
  background-color: #0d6efd;
  color: white;
  border-color: #0d6efd;
}

/* Pequena animação nos cards ao passar o mouse */
.col-12.col-sm-6.col-lg-4:hover {
  transform: translateY(-5px);
  transition: all 0.3s ease;
}
</style>
