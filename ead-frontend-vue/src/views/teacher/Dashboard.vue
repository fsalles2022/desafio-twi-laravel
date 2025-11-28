<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useAuthStore } from "../../stores/auth";
// import Chart from "chart.js/auto";

const auth = useAuthStore();

const loading = ref(true);
const courses = ref([]);
const videos = ref([]);
const studentsCount = ref(0);

const chartCanvas = ref(null);

async function loadDashboard() {
  loading.value = true;

  try {
    // 1️⃣ Buscar cursos
    const coursesResponse = await axios.get("http://localhost:8000/api/courses", {
      headers: { Authorization: `Bearer ${auth.token}` },
    });

    // Para student o backend retorna my_courses
    if (coursesResponse.data.my_courses) {
      courses.value = coursesResponse.data.my_courses;
    } else {
      courses.value = coursesResponse.data;
    }

    // Filtra APENAS os cursos do professor logado
    courses.value = courses.value.filter(c => c.user_id === auth.user.id);

    // 2️⃣ Buscar vídeos do professor
    const videosResponse = await axios.get("http://localhost:8000/api/videos/user", {
      headers: { Authorization: `Bearer ${auth.token}` },
    });
    

    videos.value = videosResponse.data;

    // 3️⃣ Contar alunos dos cursos do professor
    studentsCount.value = courses.value.reduce((acc, course) => {
      return acc + (course.students?.length || 0);
    }, 0);

    // 4️⃣ Criar gráfico (se quiser)
    // createChart();

  } catch (error) {
    console.error("Erro ao carregar dashboard:", error);
  } finally {
    loading.value = false;
  }
}


function createChart() {
  if (!chartCanvas.value) return;

  new Chart(chartCanvas.value, {
    type: "bar",
    data: {
      labels: ["Cursos", "Vídeos"],
      datasets: [
        {
          label: "Quantidade",
          data: [courses.value.length, videos.value.length]
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      }
    }
  });
}

onMounted(() => {
  loadDashboard();
});
</script>

<template>
  <div class="container">

    <!-- LOADING -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-success"></div>
      <p class="mt-3">Carregando Dashboard...</p>
    </div>

    <!-- DASHBOARD -->
    <div v-else>

      <!-- Título -->
      <h2 class="fw-bold mb-4">📊 Dashboard do Professor: {{ auth.user.name }}</h2>

      <!-- CARDS -->
      <div class="row g-4 mb-5">

        <div class="col-md-4">
          <div class="card shadow-sm border-0 p-3 bg-success text-white rounded-4">
            <h5 class="fw-bold">Cursos Criados</h5>
            <h2 class="fw-bold">{{ courses.length }}</h2>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm border-0 p-3 bg-primary text-white rounded-4">
            <h5 class="fw-bold">Vídeos Enviados</h5>
            <h2 class="fw-bold">{{ videos.length }}</h2>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm border-0 p-3 bg-dark text-white rounded-4">
            <h5 class="fw-bold">Total de Alunos</h5>
            <h2 class="fw-bold">{{ studentsCount }}</h2>
          </div>
        </div>

      </div>

      <!-- GRÁFICO -->
      <div class="card shadow-sm border-0 mb-5 p-4 rounded-4">
        <h4 class="fw-bold mb-3">Atividade Geral</h4>
        <canvas ref="chartCanvas" height="120"></canvas>
      </div>

      <div class="row">

        <!-- ÚLTIMOS CURSOS -->
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <h4 class="fw-bold mb-3">📚 Últimos Cursos</h4>

            <div v-if="courses.length === 0">
              <p class="text-muted">Nenhum curso criado ainda.</p>
            </div>

            <ul class="list-group">
              <li v-for="course in courses.slice(0, 5)" :key="course.id"
                class="list-group-item d-flex justify-content-between">
                <span>{{ course.title }}</span>
                <span class="badge bg-success">{{ course.students?.length || 0 }} alunos</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- ÚLTIMOS VÍDEOS -->
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <h4 class="fw-bold mb-3">🎬 Últimos Vídeos</h4>

            <div v-if="videos.length === 0">
              <p class="text-muted">Nenhum vídeo enviado ainda.</p>
            </div>

            <ul class="list-group">
              <li v-for="video in videos.slice(0, 5)" :key="video.id" class="list-group-item">
                {{ video.title }}
              </li>
            </ul>
          </div>
        </div>

      </div>

    </div>

  </div>
</template>

<style scoped>
.card {
  transition: 0.2s;
}

.card:hover {
  transform: translateY(-3px);
}
</style>
