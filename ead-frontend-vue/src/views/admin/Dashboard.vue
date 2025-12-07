<template>
  <div class="teacher-dashboard container py-4">

    <!-- TOP BAR (botões grandes) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold">Painel de Administrador</h2>
        <p class="text-white m-0">Bem-vindo, <strong>{{ auth.user.name }}</strong></p>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-success px-4 py-2 fw-semibold rounded-3" @click="openNewCourseModal">
          + Criar Curso
        </button>
        <button class="btn btn-primary px-4 py-2 fw-semibold rounded-3" @click="openNewVideoModal">
          + Enviar Vídeo
        </button>
        <button class="btn btn-warning px-4 py-2 fw-semibold rounded-3" @click="openTeacherModal">
          + Cadastrar Professor
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
          <div class="dash-card shadow-sm border-0 p-4 rounded-4 bg-gradient-3 text-white">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="fw-semibold">Usuários do Sistema</h5>
              <i class="bi bi-people-fill fs-2 opacity-75"></i>
            </div>
            <h1 class="fw-bold mt-3">{{ usersCount }}</h1>
          </div>
        </div>

        <!-- NOVO CARD: PROFESSORES -->
        <div class="col-md-4">
          <div class="dash-card shadow-sm border-0 p-4 rounded-4 bg-gradient-4 text-white">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="fw-semibold">Professores</h5>
              <i class="bi bi-person-badge fs-2 opacity-75"></i>
            </div>
            <h1 class="fw-bold mt-3">{{ teachersCount }}</h1>
          </div>
        </div>

        <!-- NOVO CARD: ADMINS -->
        <div class="col-md-4">
          <div class="dash-card shadow-sm border-0 p-4 rounded-4 bg-gradient-admin text-white">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="fw-semibold">Administradores</h5>
              <i class="bi bi-shield-lock-fill fs-2 opacity-75"></i>
            </div>
            <h1 class="fw-bold mt-3">{{ adminsCount }}</h1>
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
                  <span class="badge bg-primary rounded-pill me-2">
                    {{ course.students?.length || 0 }} alunos
                  </span>

                  <span class="badge bg-warning rounded-pill">
                    Prof: {{ course.teacher?.name || 'Não definido' }}
                  </span>

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

      <!-- === NOVA SEÇÃO: LISTA DE PROFESSORES (com editar e deletar) === -->
      <div class="row mt-3">
        <div class="col-12">
          <div class="card shadow-sm border-0 rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold m-0">👩‍🏫 Lista de Professores</h5>
              <div>
                <!-- você pode adicionar filtros ou botões aqui -->
              </div>
            </div>

            <div v-if="teachers.length === 0" class="text-muted mb-3">Nenhum professor encontrado.</div>

            <div v-else class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Registro</th>
                    <th class="text-end">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="teacher in teachers" :key="teacher.id">
                    <td style="width: 60px;">
                      <img :src="teacher.image_url || defaultAvatar" alt="avatar" class="rounded-circle" width="48" height="48" />
                    </td>
                    <td>{{ teacher.name }}</td>
                    <td>{{ teacher.email }}</td>
                    <td class="text-muted small">{{ formatDate(teacher.created_at) }}</td>
                    <td class="text-end">
                      <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-sm btn-outline-primary" @click="openEditTeacher(teacher)">Editar</button>
                        <button class="btn btn-sm btn-outline-danger" @click="deleteTeacher(teacher.id)">Excluir</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
      <!-- === FIM LISTA DE PROFESSORES === -->

      <!-- === NOVA SEÇÃO: LISTA DE ADMINS (com editar e deletar) === -->
      <div class="row mt-3">
        <div class="col-12">
          <div class="card shadow-sm border-0 rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold m-0">🛡️ Lista de Administradores</h5>
              <div></div>
            </div>

            <div v-if="admins.length === 0" class="text-muted mb-3">Nenhum administrador encontrado.</div>

            <div v-else class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Registro</th>
                    <th class="text-end">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="admin in admins" :key="admin.id">
                    <td style="width: 60px;">
                      <img :src="admin.image_url || defaultAvatar" alt="avatar" class="rounded-circle" width="48" height="48" />
                    </td>
                    <td>{{ admin.name }}</td>
                    <td>{{ admin.email }}</td>
                    <td class="text-muted small">{{ formatDate(admin.created_at) }}</td>
                    <td class="text-end">
                      <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-sm btn-outline-primary" @click="openEditAdmin(admin)">Editar</button>
                        <button class="btn btn-sm btn-outline-danger" @click="deleteAdmin(admin.id)">Excluir</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
      <!-- === FIM LISTA DE ADMINS === -->

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

    <!-- ===== TEACHER MODAL (Criar) ===== -->
    <div v-if="showTeacherModal" class="modal-backdrop" @click="closeTeacherModal">
      <div class="modal-container" @click.stop>

        <div class="modal-header">
          <h3>Cadastrar Professor</h3>
          <button class="btn-close" @click="closeTeacherModal">×</button>
        </div>

        <div class="modal-body">

          <div class="form-group mb-2">
            <label class="form-label">Nome</label>
            <input v-model="teacherForm.name" class="form-control" placeholder="Nome do professor" />
          </div>

          <div class="form-group mb-2">
            <label class="form-label">E-mail</label>
            <input v-model="teacherForm.email" class="form-control" placeholder="email@email.com" />
          </div>

          <div class="form-group mb-2">
            <label class="form-label">Senha</label>
            <input type="password" v-model="teacherForm.password" class="form-control" />
          </div>

          <div class="form-group mb-2">
            <label class="form-label">Confirmar Senha</label>
            <input type="password" v-model="teacherForm.password_confirmation" class="form-control" />
          </div>

          <div v-if="teacherError" class="alert alert-danger small">{{ teacherError }}</div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" @click="closeTeacherModal">Cancelar</button>
          <button class="btn btn-warning" :disabled="teacherLoading" @click="saveTeacher">
            <span v-if="teacherLoading" class="spinner-border spinner-border-sm me-2"></span>
            Criar Professor
          </button>
        </div>

      </div>
    </div>

    <!-- ===== EDIT TEACHER MODAL (Editar professor existente) ===== -->
    <div v-if="showEditTeacherModal" class="modal-backdrop" @click="() => showEditTeacherModal = false">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>Editar Professor</h3>
          <button class="btn-close" @click="() => showEditTeacherModal = false">×</button>
        </div>

        <div class="modal-body">
          <div v-if="selectedTeacher">
            <div class="form-group mb-2">
              <label class="form-label">Nome</label>
              <input v-model="selectedTeacher.name" class="form-control" />
            </div>

            <div class="form-group mb-2">
              <label class="form-label">E-mail</label>
              <input v-model="selectedTeacher.email" class="form-control" />
            </div>

            <div class="form-group mb-2">
              <label class="form-label">Senha (opcional)</label>
              <input type="password" v-model="selectedTeacher.password" class="form-control" placeholder="Deixe em branco para não alterar" />
            </div>

            <div v-if="editTeacherError" class="alert alert-danger small">{{ editTeacherError }}</div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showEditTeacherModal = false">Cancelar</button>
          <button class="btn btn-success" :disabled="editTeacherLoading" @click="updateTeacher">
            <span v-if="editTeacherLoading" class="spinner-border spinner-border-sm me-2"></span>
            Salvar
          </button>
        </div>
      </div>
    </div>

    <!-- ===== EDIT ADMIN MODAL (Editar admin existente) ===== -->
    <div v-if="showEditAdminModal" class="modal-backdrop" @click="() => showEditAdminModal = false">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>Editar Administrador</h3>
          <button class="btn-close" @click="() => showEditAdminModal = false">×</button>
        </div>

        <div class="modal-body">
          <div v-if="selectedAdmin">
            <div class="form-group mb-2">
              <label class="form-label">Nome</label>
              <input v-model="selectedAdmin.name" class="form-control" />
            </div>

            <div class="form-group mb-2">
              <label class="form-label">E-mail</label>
              <input v-model="selectedAdmin.email" class="form-control" />
            </div>

            <div class="form-group mb-2">
              <label class="form-label">Senha (opcional)</label>
              <input type="password" v-model="selectedAdmin.password" class="form-control" placeholder="Deixe em branco para não alterar" />
            </div>

            <div v-if="editAdminError" class="alert alert-danger small">{{ editAdminError }}</div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showEditAdminModal = false">Cancelar</button>
          <button class="btn btn-success" :disabled="editAdminLoading" @click="updateAdmin">
            <span v-if="editAdminLoading" class="spinner-border spinner-border-sm me-2"></span>
            Salvar
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../../stores/auth';

const auth = useAuthStore();

const loading = ref(true);
const courses = ref([]);
const videos = ref([]);
const studentsCount = ref(0);
const users = ref([]);        // ✅ TODOS OS USUÁRIOS
const usersCount = ref(0);   // ✅ TOTAL DE USUÁRIOS
const teachers = ref([]); // ✅ lista de professores
const teachersCount = ref(0); // ✅ total de professores
const admins = ref([]); // ✅ lista de admins
const adminsCount = ref(0); // ✅ total de admins

const chartCanvas = ref(null);
const showTeacherModal = ref(false)

const teacherForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const teacherLoading = ref(false)
const teacherError = ref('')

// editar professor
const selectedTeacher = ref(null)
const showEditTeacherModal = ref(false)
const editTeacherLoading = ref(false)
const editTeacherError = ref('')

// editar admin
const selectedAdmin = ref(null)
const showEditAdminModal = ref(false)
const editAdminLoading = ref(false)
const editAdminError = ref('')

const defaultAvatar = 'http://localhost:8000/default-avatar.png' // ajuste se precisar

/** modal states **/
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

function onTeacherCreated() {
  alert("Professor criado com sucesso!")
}

/** UTILITIES */
function formatDate(date) {
  if (!date) return '-';
  try {
    return new Date(date).toLocaleString();
  } catch (e) {
    return date;
  }
}

/** LOAD DATA */
async function loadDashboard() {
  loading.value = true;

  try {
    // ✅ Cursos (funciona para ADMIN e TEACHER)
    const coursesResponse = await axios.get(
      'http://localhost:8000/api/courses',
      authHeaders()
    );

    // compatível com:
    // { my_courses: [...] }  OU  [ ... ]
    let fetchedCourses =
      coursesResponse.data?.my_courses ||
      coursesResponse.data ||
      [];

    if (!Array.isArray(fetchedCourses)) fetchedCourses = [];

    courses.value = fetchedCourses;

    // ✅ Todos os vídeos
    const videosResponse = await axios.get(
      'http://localhost:8000/api/videos',
      authHeaders()
    );

    const fetchedVideos = Array.isArray(videosResponse.data)
      ? videosResponse.data
      : [];

    videos.value = fetchedVideos;

    // ✅ BUSCA TODOS OS USUÁRIOS (USANDO SUA ROTA allusers)
    const usersResponse = await axios.get(
      'http://localhost:8000/api/users/allusers',
      authHeaders()
    );

    users.value = Array.isArray(usersResponse.data)
      ? usersResponse.data
      : usersResponse.data?.data || [];

    usersCount.value = users.value.length;

    console.log('USUÁRIOS:', users.value);

    // ✅ BUSCA SOMENTE PROFESSORES
    try {
      const teachersResponse = await axios.get(
        'http://localhost:8000/api/users/teachers',
        authHeaders()
      );

      teachers.value = Array.isArray(teachersResponse.data)
        ? teachersResponse.data
        : teachersResponse.data?.data || [];

      teachersCount.value = teachers.value.length;
      console.log('PROFESSORES:', teachers.value);
    } catch (errTeachers) {
      // se houver erro nesta rota, vamos logar mas não interromper o dashboard
      console.error('Erro ao carregar professores:', errTeachers);
      teachers.value = [];
      teachersCount.value = 0;
    }

    // ✅ BUSCA SOMENTE ADMINS
    try {
      const adminsResponse = await axios.get(
        'http://localhost:8000/api/users/admins',
        authHeaders()
      );

      admins.value = Array.isArray(adminsResponse.data)
        ? adminsResponse.data
        : adminsResponse.data?.data || [];

      adminsCount.value = admins.value.length;
      console.log('ADMINS:', admins.value);
    } catch (errAdmins) {
      console.error('Erro ao carregar admins:', errAdmins);
      admins.value = [];
      adminsCount.value = 0;
    }

    // ✅ Soma total de alunos corretamente
    studentsCount.value = courses.value.reduce(
      (acc, course) => acc + (course.students?.length || 0),
      0
    );

    console.log('Cursos:', courses.value.length);
    console.log('Vídeos:', videos.value.length);
    console.log('Alunos:', studentsCount.value);

  } catch (err) {
    console.error('Erro ao carregar dashboard:', err);
  } finally {
    loading.value = false;
  }
}

/** FUNÇÕES DE PROFESSORES (LISTAR, EDITAR, DELETAR) */
async function loadTeachers() {
  try {
    const res = await axios.get('http://localhost:8000/api/users/teachers', authHeaders());
    teachers.value = Array.isArray(res.data) ? res.data : res.data?.data || [];
    teachersCount.value = teachers.value.length;
  } catch (err) {
    console.error('Erro ao carregar professores:', err);
    teachers.value = [];
    teachersCount.value = 0;
  }
}

function openEditTeacher(teacher) {
  selectedTeacher.value = { ...teacher, password: '' }; // clone e limpa senha
  editTeacherError.value = '';
  showEditTeacherModal.value = true;
}

async function updateTeacher() {
  if (!selectedTeacher.value?.id) return;
  editTeacherLoading.value = true;
  editTeacherError.value = '';

  try {
    // Preparar payload (se senha vazia, remover)
    const payload = { ...selectedTeacher.value };
    if (!payload.password) delete payload.password;

    // usar post para rota existente (/api/users/{id})
    await axios.post(`http://localhost:8000/api/users/${payload.id}`, payload, authHeaders());

    showEditTeacherModal.value = false;
    selectedTeacher.value = null;

    // recarregar listas
    await loadTeachers();
    await loadDashboard();
  } catch (err) {
    console.error('Erro ao atualizar professor:', err);
    editTeacherError.value = err?.response?.data?.message || 'Falha ao atualizar professor';
  } finally {
    editTeacherLoading.value = false;
  }
}

async function deleteTeacher(id) {
  if (!confirm('Tem certeza que deseja excluir este professor?')) return;
  try {
    await axios.delete(`http://localhost:8000/api/users/${id}`, authHeaders());
    // recarrega
    await loadTeachers();
    await loadDashboard();
  } catch (err) {
    console.error('Erro ao deletar professor:', err);
    alert('Falha ao deletar professor');
  }
}

/** FUNÇÕES DE ADMINS (LISTAR, EDITAR, DELETAR) */
async function loadAdmins() {
  try {
    const res = await axios.get('http://localhost:8000/api/users/admins', authHeaders());
    admins.value = Array.isArray(res.data) ? res.data : res.data?.data || [];
    adminsCount.value = admins.value.length;
  } catch (err) {
    console.error('Erro ao carregar admins:', err);
    admins.value = [];
    adminsCount.value = 0;
  }
}

function openEditAdmin(admin) {
  selectedAdmin.value = { ...admin, password: '' }; // clone e limpa senha
  editAdminError.value = '';
  showEditAdminModal.value = true;
}

async function updateAdmin() {
  if (!selectedAdmin.value?.id) return;
  editAdminLoading.value = true;
  editAdminError.value = '';

  try {
    const payload = { ...selectedAdmin.value };
    if (!payload.password) delete payload.password;

    await axios.post(`http://localhost:8000/api/users/${payload.id}`, payload, authHeaders());

    showEditAdminModal.value = false;
    selectedAdmin.value = null;

    // recarregar listas
    await loadAdmins();
    await loadDashboard();
  } catch (err) {
    console.error('Erro ao atualizar admin:', err);
    editAdminError.value = err?.response?.data?.message || 'Falha ao atualizar admin';
  } finally {
    editAdminLoading.value = false;
  }
}

async function deleteAdmin(id) {
  if (!confirm('Tem certeza que deseja excluir este administrador?')) return;
  try {
    await axios.delete(`http://localhost:8000/api/users/${id}`, authHeaders());
    // recarrega
    await loadAdmins();
    await loadDashboard();
  } catch (err) {
    console.error('Erro ao deletar admin:', err);
    alert('Falha ao deletar admin');
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

function openTeacherModal() {
  teacherForm.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  }
  teacherError.value = ''
  showTeacherModal.value = true
}

function closeTeacherModal() {
  showTeacherModal.value = false
}

async function saveTeacher() {
  teacherError.value = ''
  teacherLoading.value = true

  try {
    await axios.post(
      'http://localhost:8000/api/admin/create-teacher',
      teacherForm.value,
      authHeaders()
    )

    alert('Professor criado com sucesso!')
    closeTeacherModal()
    // recarrega professores
    await loadTeachers()
    await loadDashboard()
  } catch (err) {
    console.error('Erro ao criar professor:', err)
    teacherError.value =
      err?.response?.data?.message ||
      err?.response?.data?.error ||
      'Erro ao criar professor'
  } finally {
    teacherLoading.value = false
  }
}

onMounted(async () => {
  await loadDashboard();
  await loadTeachers();
  await loadAdmins();
});
</script>

<style scoped>
.teacher-dashboard {
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

/* novo gradiente para professores */
.bg-gradient-4 {
  background: linear-gradient(135deg, #8e44ad, #6f42c1);
}

/* gradiente admin */
.bg-gradient-admin {
  background: linear-gradient(135deg, #e53935, #c62828);
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
