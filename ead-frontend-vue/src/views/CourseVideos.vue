<template>
  <div class="container py-4">
    <h2 class="mb-3">{{ course?.title }}</h2>
    <p class="text-muted">{{ course?.description }}</p>

    <div v-if="loading" class="text-center">
      <div class="spinner-border text-primary"></div>
    </div>

    <div v-else-if="videos.length">
      <div class="row">
        <MediaCardAdvanced v-for="v in videos" :key="v.id" :media="formatVideo(v)" />
      </div>
    </div>

    <div v-else class="text-center text-muted">
      Nenhum vídeo encontrado para este curso.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRoute } from "vue-router";
import MediaCardAdvanced from "../components/MediaCardAdvanced.vue";
import { useAuthStore } from "../stores/auth";

const route = useRoute();
const auth = useAuthStore();

const course = ref(null);
const videos = ref([]);
const loading = ref(true);

const fetchCourseVideos = async () => {
  try {
    const res = await axios.get(
      `http://localhost:8000/api/courses/${route.params.id}/videos`,
      { headers: { Authorization: `Bearer ${auth.token}` } }
    );

    course.value = res.data.course;
    videos.value = res.data.videos;
  } finally {
    loading.value = false;
  }
};

// monta o caminho completo do arquivo vindo do Node
const formatVideo = (v) => ({
  ...v,
  type: getMimeType(v.filename),
  url: `http://localhost:4000/stream/${v.filename}`
});


const getMimeType = (file) => {
  const ext = file.split(".").pop().toLowerCase();
  if (["mp4", "mov"].includes(ext)) return "video/mp4";
  if (["mp3"].includes(ext)) return "audio/mp3";
  return "application/octet-stream";
};

onMounted(fetchCourseVideos);
</script>
