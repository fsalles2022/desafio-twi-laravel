<template>
  <div class="container py-4">
    <h1 class="mb-4 text-center">📚 Todos os Cursos da Plataforma</h1>

    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <div v-else class="row g-4">
      <div class="col-md-4" v-for="c in courses" :key="c.id">
        <div class="card shadow-sm h-100">
          <img :src="`http://localhost:8000/storage/${c.course_image}`" alt="Curso"
            style="height: 180px; object-fit: cover" />

          <div class="card-body">
            <h5 class="card-title">{{ c.title }}</h5>
            <p class="text-muted">{{ c.description }}</p>
             <p class="text-muted">Id Curso: {{ c.id }}</p>

            <router-link :to="`/course/${c.id}`" class="btn btn-primary w-100">
              Acessar Aulas →
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <div v-if="!loading && courses.length === 0" class="text-center text-muted mt-5">
      Nenhum curso disponível para você.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useAuthStore } from "../../stores/auth";

const auth = useAuthStore();
const loading = ref(true);
const courses = ref([]);

const fetchCourses = async () => {
  try {
    const res = await axios.get("http://localhost:8000/api/public/courses", {
      headers: { Authorization: `Bearer ${auth.token}` },
    });

    // Verifica se é aluno
    if (res.data.my_courses) {
      courses.value = res.data.my_courses;
    }
    // Se for teacher, backend retorna lista simples
    else {
      courses.value = res.data;
    }

  } catch (err) {
    console.error("Erro carregando cursos:", err);
  } finally {
    loading.value = false;
  }
};


onMounted(fetchCourses);
</script>
