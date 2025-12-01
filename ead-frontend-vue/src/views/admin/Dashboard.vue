<template>
  <div class="admin-dashboard container py-4">

    <!-- TOP BAR (botões grandes) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold">Painel de Administradorss</h2>
        <p class="text-muted m-0">Bem-vindo, <strong>{{ auth.user.name }}</strong></p>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-success px-4 py-2 fw-semibold rounded-3" @click="openNewCourseModal">
          + Criar Curso
        </button>
        <button class="btn btn-primary px-4 py-2 fw-semibold rounded-3" @click="openNewVideoModal">
          + Enviar Vídeo
        </button>
      </div>
    </div>

    <!-- LOADING -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
      <p class="mt-3 fw-semibold text-muted">Carregando Dashboard...</p>
    </div>

    <div v-else>
      <!-- CARDS -->
      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="dash-card shadow-sm border-0 p-4 rounded-4 bg-gradient-1 text-white">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="fw-semibold">Cursos Criados</h5>
              <i class="bi bi-journal-bookmark fs-2 opacity-75"></i>
            </div>
            <h1 class="fw-bold mt-3">{{ courses.length }}</h1>
          </div>
        </div>

        <div class="col-md-4">
          <div class="dash-card shadow-sm border-0 p-4 rounded-4 bg-gradient-2 text-white">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="fw-semibold">Vídeos Enviados</h5>
              <i class="bi bi-camera-video fs-2 opacity-75"></i>
            </div>
            <h1 class="fw-bold mt-3">{{ videos.length }}</h1>
          </div>
        </div>

        <div class="col-md-4">
          <div class="dash-card shadow-sm border-0 p-4 rounded-4 bg-gradient-3 text-white">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="fw-semibold">Total de Alunos</h5>
              <i class="bi bi-people fs-2 opacity-75"></i>
            </div>
            <h1 class="fw-bold mt-3">{{ studentsCount }}</h1>
          </div>
        </div>
      </div>

      <!-- GRÁFICO -->
      <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-3">Visão Geral</h5>
        <canvas ref="chartCanvas" height="130"></canvas>
      </div>

      <!-- LISTAS + botões por seção -->
      <div class="row">
        <!-- Cursos -->
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold m-0">📚 Últimos Cursos</h5>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-success btn-sm" @click="openNewCourseModal">Novo Curso</button>
              </div>
            </div>

            <div v-if="courses.length === 0">
              <p class="text-muted">Nenhum curso criado ainda.</p>
            </div>

            <ul class="list-group list-group-flush">
              <li v-for="course in courses.slice(0, 20)" :key="course.id"
                class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">{{ course.title }}</div>
                  <div class="text-muted small">{{ course.description || '' }}</div>
                </div>

                <div class="d-flex gap-2 align-items-center">
                  <span class="badge bg-primary rounded-pill me-2">{{ course.students?.length || 0 }} alunos</span>

                  <button class="btn btn-sm btn-outline-primary" @click="openEditCourseModal(course)">Editar</button>
                  <button class="btn btn-sm btn-outline-danger" @click="confirmDeleteCourse(course)">Excluir</button>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Vídeos -->
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold m-0">🎬 Últimos Vídeos</h5>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" @click="openNewVideoModal">Novo Vídeo</button>
              </div>
            </div>

            <div v-if="videos.length === 0">
              <p class="text-muted">Nenhum vídeo enviado ainda.</p>
            </div>

            <ul class="list-group list-group-flush">
              <li v-for="video in videos.slice(0, 20)" :key="video.id"
                class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">{{ video.title }}</div>
                  <div class="text-muted small">Curso: {{ courseTitle(video.course_id) || '—' }}</div>
                </div>

                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-outline-primary" @click="openEditVideoModal(video)">Editar</button>
                  <button class="btn btn-sm btn-outline-danger" @click="confirmDeleteVideo(video)">Excluir</button>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== COURSE MODAL (custom modern modal) ===== -->
    <div v-if="showCourseModal" class="modal-backdrop" @click="closeCourseModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>{{ editingCourse ? 'Editar Curso' : 'Criar Curso' }}</h3>
          <button class="btn-close" @click="closeCourseModal">×</button>
        </div>

        <div class="modal-body">
          <div class="form-group mb-2">
            <label class="form-label">Título</label>
            <input v-model="courseForm.title" class="form-control" placeholder="Título do curso" />
          </div>

          <div class="form-group mb-2">
            <label class="form-label">Descrição</label>
            <textarea v-model="courseForm.description" class="form-control" rows="4"></textarea>
          </div>

          <div class="form-group mb-2">
            <label class="form-label">Imagem do curso (opcional)</label>
            <input type="file" @change="onCourseImageChange" class="form-control" />
          </div>

          <div class="form-group mb-2">
            <label class="form-label">Status</label>
            <select v-model="courseForm.status" class="form-select">
              <option value="active">Ativo</option>
              <option value="inactive">Inativo</option>
            </select>
          </div>

          <div v-if="courseError" class="alert alert-danger small">{{ courseError }}</div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" @click="closeCourseModal">Cancelar</button>
          <button class="btn btn-success" :disabled="courseLoading" @click="saveCourse">
            <span v-if="courseLoading" class="spinner-border spinner-border-sm me-2"></span>
            {{ editingCourse ? 'Salvar' : 'Criar' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ===== VIDEO MODAL ===== -->
    <div v-if="showVideoModal" class="modal-backdrop" @click="closeVideoModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>{{ editingVideo ? 'Editar Vídeo' : 'Adicionar Vídeo' }}</h3>
          <button class="btn-close" @click="closeVideoModal">×</button>
        </div>

        <div class="modal-body">

          <div class="form-group mb-2">
            <label class="form-label">Título</label>
            <input v-model="videoForm.title" class="form-control" placeholder="Título do vídeo" />
          </div>

          <div class="form-group mb-2">
            <label class="form-label">Descrição</label>
            <textarea v-model="videoForm.description" class="form-control" rows="3"></textarea>
          </div>

          <div class="form-group mb-2">
            <label class="form-label">Curso</label>
            <select v-model="videoForm.course_id" class="form-select">
              <option value="" disabled>Selecionar curso</option>
              <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
            </select>
          </div>

          <!-- NOVO CAMPO URL ✔️ -->
          <div class="form-group mb-2">
            <label class="form-label">URL do Vídeo</label>
            <input v-model="videoForm.url" class="form-control" placeholder="https://youtube.com/..." />
          </div>

          <!-- CAMPO ARQUIVO (opcional) -->
          <div class="form-group mb-2">
            <label class="form-label">Arquivo do vídeo (opcional)</label>
            <input type="file" @change="onVideoFileChange" class="form-control" />
            <div v-if="editingVideo && editingVideo.filename" class="small text-muted mt-1">
              Arquivo atual: {{ editingVideo.filename }}
            </div>
          </div>

          <div v-if="videoError" class="alert alert-danger small">{{ videoError }}</div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" @click="closeVideoModal">Cancelar</button>
          <button class="btn btn-primary" :disabled="videoLoading" @click="saveVideo">
            <span v-if="videoLoading" class="spinner-border spinner-border-sm me-2"></span>
            {{ editingVideo ? 'Salvar' : 'Enviar' }}
          </button>
        </div>

      </div>
    </div>


  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../../stores/auth';

const auth = useAuthStore();

const loading = ref(true);
const courses = ref([]);
const videos = ref([]);
const studentsCount = ref(0);
const chartCanvas = ref(null);

// modal states
const showCourseModal = ref(false);
const editingCourse = ref(null);
const courseForm = ref({ title: '', description: '', status: 'active', course_image: null });
const courseLoading = ref(false);
const courseError = ref('');

const showVideoModal = ref(false);
const editingVideo = ref(null);
const videoForm = ref({ title: '', description: '', course_id: '', file: null });
const videoLoading = ref(false);
const videoError = ref('');

// helper to build auth header
function authHeaders() {
  return { headers: { Authorization: `Bearer ${auth.token}` } };
}

/** LOAD DATA */
async function loadDashboard() {
  loading.value = true;
  try {
    // courses
    const coursesResponse = await axios.get('http://localhost:8000/api/courses', authHeaders());

    let fetched = coursesResponse.data.my_courses || coursesResponse.data || [];
    // ensure array
    if (!Array.isArray(fetched)) fetched = [];

    // only Admin's courses
    courses.value = fetched.filter(c => c.user_id === auth.user.id);

    // videos (all) then filter by course ids
    const videosResponse = await axios.get('http://localhost:8000/api/videos', authHeaders());
    const vids = Array.isArray(videosResponse.data) ? videosResponse.data : [];
    const courseIds = courses.value.map(c => c.id);
    videos.value = vids.filter(v => courseIds.includes(v.course_id));

    // students count
    studentsCount.value = courses.value.reduce((acc, course) => acc + (course.students?.length || 0), 0);

  } catch (err) {
    console.error('Erro ao carregar dashboard:', err);
  } finally {
    loading.value = false;
  }
}

/** COURSE CRUD */
function openNewCourseModal() {
  editingCourse.value = null;
  courseForm.value = { title: '', description: '', status: 'active', course_image: null };
  courseError.value = '';
  showCourseModal.value = true;
}

function openEditCourseModal(course) {
  editingCourse.value = course;
  courseForm.value = {
    title: course.title || '',
    description: course.description || '',
    status: course.status || 'active',
    course_image: null,
  };
  courseError.value = '';
  showCourseModal.value = true;
}

function closeCourseModal() {
  showCourseModal.value = false;
}

function onCourseImageChange(e) {
  const file = e.target.files?.[0] || null;
  courseForm.value.course_image = file;
}

async function saveCourse() {
  courseError.value = '';
  courseLoading.value = true;

  try {
    const fd = new FormData();
    fd.append('title', courseForm.value.title);
    fd.append('description', courseForm.value.description || '');
    fd.append('status', courseForm.value.status || 'active');
    if (courseForm.value.course_image) fd.append('course_image', courseForm.value.course_image);

    if (editingCourse.value) {
      // use _method=PUT to support file upload via POST
      fd.append('_method', 'PUT');
      await axios.post(`http://localhost:8000/api/courses/${editingCourse.value.id}`, fd, authHeaders());
    } else {
      await axios.post('http://localhost:8000/api/courses', fd, authHeaders());
    }

    // reload lists
    await loadDashboard();
    closeCourseModal();
  } catch (err) {
    console.error('Erro ao salvar curso:', err);
    courseError.value = err?.response?.data?.message || 'Falha ao salvar curso';
  } finally {
    courseLoading.value = false;
  }
}

async function confirmDeleteCourse(course) {
  const ok = confirm(`Confirma exclusão do curso "${course.title}"?`);
  if (!ok) return;
  try {
    await axios.delete(`http://localhost:8000/api/courses/${course.id}`, authHeaders());
    await loadDashboard();
  } catch (err) {
    console.error('Erro ao deletar curso:', err);
    alert('Falha ao deletar curso');
  }
}

/** VIDEO CRUD */
function openNewVideoModal() {
  editingVideo.value = null;
  videoForm.value = { title: '', description: '', course_id: courses.value[0]?.id || '', file: null };
  videoError.value = '';
  showVideoModal.value = true;
}

function openEditVideoModal(video) {
  editingVideo.value = video;
  videoForm.value = {
    title: video.title || '',
    description: video.description || '',
    course_id: video.course_id || (courses.value[0]?.id || ''),
    file: null,
  };
  videoError.value = '';
  showVideoModal.value = true;
}

function closeVideoModal() {
  showVideoModal.value = false;
}

function onVideoFileChange(e) {
  const file = e.target.files?.[0] || null;
  videoForm.value.file = file;
}

async function saveVideo() {
  videoError.value = '';
  videoLoading.value = true;

  try {
    if (!videoForm.value.course_id) {
      videoError.value = 'Selecione um curso';
      return;
    }
    const fd = new FormData();
    fd.append('title', videoForm.value.title);
    fd.append('description', videoForm.value.description || '');
    fd.append('course_id', videoForm.value.course_id);
    if (videoForm.value.file) fd.append('file', videoForm.value.file);

    if (editingVideo.value) {
      fd.append('_method', 'PUT');
      await axios.post(`http://localhost:8000/api/videos/${editingVideo.value.id}`, fd, authHeaders());
    } else {
      await axios.post('http://localhost:8000/api/videos', fd, authHeaders());
    }

    await loadDashboard();
    closeVideoModal();
  } catch (err) {
    console.error('Erro ao salvar vídeo:', err);
    videoError.value = err?.response?.data?.message || 'Falha ao salvar vídeo';
  } finally {
    videoLoading.value = false;
  }
}

async function confirmDeleteVideo(video) {
  const ok = confirm(`Confirma exclusão do vídeo "${video.title}"?`);
  if (!ok) return;
  try {
    await axios.delete(`http://localhost:8000/api/videos/${video.id}`, authHeaders());
    await loadDashboard();
  } catch (err) {
    console.error('Erro ao deletar vídeo:', err);
    alert('Falha ao deletar vídeo');
  }
}

/** Helpers */
function courseTitle(id) {
  const c = courses.value.find(x => x.id === id);
  return c ? c.title : null;
}

onMounted(() => {
  loadDashboard();
});
</script>

<style scoped>
.admin-dashboard {
  max-width: 1200px;
}

/* Gradientes modernos */
.bg-gradient-1 {
  background: linear-gradient(135deg, #4caf50, #2e7d32);
}

.bg-gradient-2 {
  background: linear-gradient(135deg, #2196f3, #1565c0);
}

.bg-gradient-3 {
  background: linear-gradient(135deg, #424242, #000000);
}

.dash-card {
  transition: 0.2s ease;
}

.dash-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

/* modal custom */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal-container {
  width: 680px;
  max-width: calc(100% - 32px);
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid #eee;
}

.modal-body {
  padding: 16px 20px;
  max-height: 60vh;
  overflow: auto;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 12px 20px;
  border-top: 1px solid #eee;
  background: #fafafa;
}

.btn-close {
  border: none;
  background: transparent;
  font-size: 22px;
  cursor: pointer;
}

/* small UI tweaks */
.list-group-item {
  border: 0;
  padding: 12px 0;
}

.card {
  transition: .18s ease;
}

.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
}

/* responsive */
@media (max-width: 768px) {
  .modal-container {
    width: 94%;
  }

  .dash-card h1 {
    font-size: 28px;
  }
}
</style>
