<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useAuthStore } from "../../stores/auth";

const auth = useAuthStore();

const loading = ref(true);
const courses = ref([]);
const videos = ref([]);

const showCourseModal = ref(false);
const showVideoModal = ref(false);

// Formulários
const courseForm = ref({ title: "", description: "", status: "active", course_image: null });
const videoForm = ref({ title: "", description: "", filename: null, course_id: null });

// Fetch inicial
async function loadTeacherData() {
  loading.value = true;
  try {
    const coursesResponse = await axios.get("http://localhost:8000/api/courses", {
      headers: { Authorization: `Bearer ${auth.token}` },
    });
    courses.value = coursesResponse.data.filter(c => c.user_id === auth.user.id);

    const videosResponse = await axios.get("http://localhost:8000/api/videos/user", {
      headers: { Authorization: `Bearer ${auth.token}` },
    });
    videos.value = videosResponse.data.filter(v => courses.value.some(c => c.id === v.course_id));
  } catch (err) {
    console.error("Erro ao carregar dados do professor:", err);
  } finally {
    loading.value = false;
  }
}

// CRUD Cursos
async function submitCourse() {
  try {
    const formData = new FormData();
    Object.entries(courseForm.value).forEach(([key, value]) => formData.append(key, value));
    const res = await axios.post("http://localhost:8000/api/courses", formData, {
      headers: { Authorization: `Bearer ${auth.token}` },
    });
    courses.value.push(res.data);
    showCourseModal.value = false;
    courseForm.value = { title: "", description: "", status: "active", course_image: null };
  } catch (err) {
    console.error("Erro ao criar curso:", err);
  }
}

// CRUD Vídeos
async function submitVideo() {
  try {
    const formData = new FormData();
    Object.entries(videoForm.value).forEach(([key, value]) => formData.append(key, value));
    const res = await axios.post("http://localhost:8000/api/videos", formData, {
      headers: { Authorization: `Bearer ${auth.token}` },
    });
    videos.value.push(res.data);
    showVideoModal.value = false;
    videoForm.value = { title: "", description: "", filename: null, course_id: null };
  } catch (err) {
    console.error("Erro ao criar vídeo:", err);
  }
}

onMounted(() => loadTeacherData());
</script>

<template>
  <div class="container my-4">
    <h2 class="fw-bold mb-4">👨‍🏫 Área do Administrador: {{ auth.user.name }}</h2>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-success"></div>
      <p class="mt-3">Carregando...</p>
    </div>

    <div v-else>
      <!-- BOTÕES -->
      <div class="mb-4">
        <button class="btn btn-success me-2" @click="showCourseModal = true">➕ Criar Curso</button>
        <button class="btn btn-primary" @click="showVideoModal = true">➕ Adicionar Vídeo</button>
      </div>

      <!-- LISTA DE CURSOS -->
      <div class="card shadow-sm border-0 rounded-4 mb-4 p-3">
        <h4 class="fw-bold mb-3">📚 Meus Cursos</h4>
        <ul class="list-group">
          <li v-for="course in courses" :key="course.id"
            class="list-group-item d-flex justify-content-between align-items-center">
            <span>{{ course.title }}</span>
            <span class="badge bg-success">{{ course.students?.length || 0 }} alunos</span>
          </li>
        </ul>
      </div>

      <!-- LISTA DE VÍDEOS -->
      <div class="card shadow-sm border-0 rounded-4 mb-4 p-3">
        <h4 class="fw-bold mb-3">🎬 Meus Vídeos</h4>
        <ul class="list-group">
          <li v-for="video in videos" :key="video.id" class="list-group-item">
            {{ video.title }} - Curso: {{courses.find(c => c.id === video.course_id)?.title || "N/A"}}
          </li>
        </ul>
      </div>

      <!-- MODAL CURSO -->
      <div class="modal fade" :class="{ show: showCourseModal }" style="display: block;" v-if="showCourseModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Criar Curso</h5>
              <button type="button" class="btn-close" @click="showCourseModal = false"></button>
            </div>
            <div class="modal-body">
              <input v-model="courseForm.title" class="form-control mb-2" placeholder="Título do curso" />
              <textarea v-model="courseForm.description" class="form-control mb-2" placeholder="Descrição"></textarea>
              <select v-model="courseForm.status" class="form-select mb-2">
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
              </select>
              <input type="file" @change="e => courseForm.course_image = e.target.files[0]" class="form-control" />
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="showCourseModal = false">Cancelar</button>
              <button class="btn btn-success" @click="submitCourse">Salvar</button>
            </div>
          </div>
        </div>
      </div>

      <!-- MODAL VÍDEO -->
      <div class="modal fade" :class="{ show: showVideoModal }" style="display: block;" v-if="showVideoModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Adicionar Vídeo</h5>
              <button type="button" class="btn-close" @click="showVideoModal = false"></button>
            </div>
            <div class="modal-body">
              <input v-model="videoForm.title" class="form-control mb-2" placeholder="Título do vídeo" />
              <textarea v-model="videoForm.description" class="form-control mb-2" placeholder="Descrição"></textarea>
              <select v-model="videoForm.course_id" class="form-select mb-2">
                <option disabled value="">Selecionar curso</option>
                <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
              </select>
              <input type="file" @change="e => videoForm.filename = e.target.files[0]" class="form-control" />
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="showVideoModal = false">Cancelar</button>
              <button class="btn btn-primary" @click="submitVideo">Salvar</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
.modal {
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-dialog {
  margin-top: 10%;
}
</style>
