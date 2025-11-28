<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"
import { useAuthStore } from "../../stores/auth"

const auth = useAuthStore()

const stats = ref({
  courses: 0,
  videos: 0,
  students: 0,
})

const latestVideos = ref([])

onMounted(async () => {
  await fetchDashboard()
})

const fetchDashboard = async () => {
  try {
    const token = auth.token

    // Buscar estatísticas
    const s = await axios.get("http://localhost:8000/api/teacher/stats", {
      headers: { Authorization: `Bearer ${token}` },
    })

    stats.value = s.data

    // Buscar últimos vídeos
    const v = await axios.get("http://localhost:8000/api/videos/user", {
      headers: { Authorization: `Bearer ${token}` },
    })

    latestVideos.value = v.data.slice(0, 5) // últimos 5
  } catch (e) {
    console.error("Erro carregando dashboard:", e)
  }
}
</script>

<template>
  <div>

    <!-- TÍTULO -->
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h2 class="fw-bold text-success">Painel do Professor</h2>
    </div>

    <!-- CARDS -->
    <div class="row g-4 mb-5">

      <!-- Cursos -->
      <div class="col-md-4">
        <div class="card shadow-sm rounded-4 border-0">
          <div class="card-body d-flex align-items-center">
            <div class="icon-box bg-success bg-opacity-25 text-success rounded-4 me-3 p-3 fs-3">
              <i class="bi bi-journal-bookmark"></i>
            </div>
            <div>
              <h6 class="text-secondary mb-0">Cursos</h6>
              <h3 class="fw-bold">{{ stats.courses }}</h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Vídeos -->
      <div class="col-md-4">
        <div class="card shadow-sm rounded-4 border-0">
          <div class="card-body d-flex align-items-center">
            <div class="icon-box bg-primary bg-opacity-25 text-primary rounded-4 me-3 p-3 fs-3">
              <i class="bi bi-camera-video-fill"></i>
            </div>
            <div>
              <h6 class="text-secondary mb-0">Vídeos</h6>
              <h3 class="fw-bold">{{ stats.videos }}</h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Alunos -->
      <div class="col-md-4">
        <div class="card shadow-sm rounded-4 border-0">
          <div class="card-body d-flex align-items-center">
            <div class="icon-box bg-warning bg-opacity-25 text-warning rounded-4 me-3 p-3 fs-3">
              <i class="bi bi-people-fill"></i>
            </div>
            <div>
              <h6 class="text-secondary mb-0">Alunos</h6>
              <h3 class="fw-bold">{{ stats.students }}</h3>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- ÚLTIMOS VÍDEOS -->
    <div class="card shadow-sm rounded-4 border-0">
      <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">🎬 Últimos vídeos enviados</h5>
      </div>

      <div class="card-body">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Título</th>
              <th>Curso</th>
              <th>Criado em</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="v in latestVideos" :key="v.id">
              <td>{{ v.title }}</td>
              <td>{{ v.course?.title || "Sem curso" }}</td>
              <td>{{ new Date(v.created_at).toLocaleDateString() }}</td>
            </tr>

            <tr v-if="latestVideos.length === 0">
              <td colspan="3" class="text-center text-muted py-4">
                Nenhum vídeo encontrado.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<style scoped>
.icon-box {
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
