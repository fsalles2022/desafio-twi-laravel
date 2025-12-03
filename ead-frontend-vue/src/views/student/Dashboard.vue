<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useAuthStore } from "../../stores/auth";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();

const loading = ref(true);
const myCourses = ref([]);
const watchedVideos = ref(0);

/* ======================
        AUTH HEADERS
====================== */
function authHeaders() {
  return { headers: { Authorization: `Bearer ${auth.token}` } };
}

/* ======================
     LOAD DASHBOARD
====================== */
async function loadStudentDashboard() {
  loading.value = true;

  try {
    // 🔹 Meus cursos
    const res = await axios.get(
      "http://localhost:8000/api/courses",
      authHeaders()
    );

    myCourses.value = res.data.my_courses || res.data || [];

    // 🔹 Contar vídeos assistidos (opcional)
    let totalWatched = 0;
    myCourses.value.forEach(course => {
      totalWatched += course.watched_videos?.length || 0;
    });

    watchedVideos.value = totalWatched;

  } catch (err) {
    console.error("Erro ao carregar dashboard do aluno:", err);
  } finally {
    loading.value = false;
  }
}

/* ======================
        MOUNT
====================== */
onMounted(loadStudentDashboard);
</script>

<template>
  <div class="student-dashboard container py-4">

    <!-- TOPO -->
    <div class="mb-4">
      <h2 class="fw-bold">🎓 Painel do Aluno</h2>
      <p class="text-muted m-0">
        Bem-vindo, <strong>{{ auth.user.name }}</strong>
      </p>
    </div>

    <!-- LOADING -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
      <p class="mt-3 fw-semibold text-muted">Carregando painel...</p>
    </div>

    <div v-else>

      <!-- CARDS -->
      <div class="row g-4 mb-4">

        <div class="col-md-6">
          <div class="dash-card shadow-sm p-4 rounded-4 bg-gradient-1 text-white">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="fw-semibold">Meus Cursos</h5>
              <i class="bi bi-journal-bookmark fs-2 opacity-75"></i>
            </div>
            <h1 class="fw-bold mt-3">{{ myCourses.length }}</h1>
          </div>
        </div>

        <div class="col-md-6">
          <div class="dash-card shadow-sm p-4 rounded-4 bg-gradient-2 text-white">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="fw-semibold">Vídeos Assistidos</h5>
              <i class="bi bi-play-circle fs-2 opacity-75"></i>
            </div>
            <h1 class="fw-bold mt-3">{{ watchedVideos }}</h1>
          </div>
        </div>

      </div>

      <!-- AÇÕES RÁPIDAS -->
      <div class="row g-4 mb-4">

        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-4 p-4 h-100 text-center">
            <h5 class="fw-bold mb-3">📚 Cursos Disponíveis</h5>
            <p class="text-muted">
              Veja todos os cursos e faça sua matrícula.
            </p>

            <button
              class="btn btn-success fw-semibold"
              @click="router.push('/student/courses')"
            >
              Ver Cursos
            </button>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-4 p-4 h-100 text-center">
            <h5 class="fw-bold mb-3">🎯 Meus Cursos</h5>
            <p class="text-muted">
              Continue assistindo seus cursos.
            </p>

            <button
              class="btn btn-primary fw-semibold"
              @click="router.push('/student/my-courses')"
            >
              Acessar
            </button>
          </div>
        </div>

      </div>

      <!-- LISTA RESUMIDA DOS CURSOS -->
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-bold mb-3">📌 Últimos Cursos Matriculados</h5>

        <div v-if="myCourses.length === 0" class="text-muted">
          Você ainda não está matriculado em nenhum curso.
        </div>

        <ul class="list-group list-group-flush">
          <li
            v-for="course in myCourses.slice(0, 5)"
            :key="course.id"
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <div>
              <div class="fw-semibold">{{ course.title }}</div>
              <div class="text-muted small">
                {{ course.description || "Sem descrição" }}
              </div>
            </div>

            <button
              class="btn btn-sm btn-outline-primary"
              @click="router.push(`/student/my-courses`)"
            >
              Acessar Curso
            </button>
          </li>
        </ul>
      </div>

    </div>
  </div>
</template>

<style scoped>
.student-dashboard {
  max-width: 1200px;
}

/* Gradientes */
.bg-gradient-1 {
  background: linear-gradient(135deg, #4caf50, #2e7d32);
}

.bg-gradient-2 {
  background: linear-gradient(135deg, #2196f3, #1565c0);
}

.dash-card {
  transition: 0.2s ease;
}

.dash-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}
</style>
