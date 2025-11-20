<template>
  <div class="container py-4">
    <h1 class="mb-4">📚 Meus Cursoss</h1>

    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <div v-else class="row">
      <div class="col-md-4 mb-3" v-for="c in courses" :key="c.id">
        <div class="card h-100 shadow-sm bg-warning" @click="openCourse(c.id)" style="cursor:pointer">
          <img :src="c.course_image || 'https://via.placeholder.com/400x200'" class="card-img-top">
          <div class="card-body">
            <h5 class="card-title">{{ c.title }}</h5>
            <p class="card-text text-muted">{{ c.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const router = useRouter();
const auth = useAuthStore();
const courses = ref([]);
const loading = ref(true);

const fetchCourses = async () => {
  try {
    const res = await axios.get("http://localhost:8000/api/courses", {
      headers: { Authorization: `Bearer ${auth.token}` }
    });
    courses.value = res.data;
  } finally {
    loading.value = false;
  }
};

const openCourse = (id) => {
  router.push(`/course/${id}`);
};

onMounted(fetchCourses);
</script>
