<template>
  <div>
    <h2>📤 Enviar vídeo</h2>
    <form @submit.prevent="handleUpload">
      <input v-model="title" type="text" placeholder="Título" required />
      <input ref="fileInput" type="file" accept="video/*" required />
      <button type="submit">Enviar</button>
    </form>
    <p v-if="message">{{ message }}</p>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "../stores/auth";
import axios from "axios";

const auth = useAuthStore();
const title = ref("");
const fileInput = ref(null);
const message = ref("");

async function handleUpload() {
  if (!fileInput.value.files.length) return;
  const formData = new FormData();
  formData.append("title", title.value);
  formData.append("file", fileInput.value.files[0]);

  try {
    await axios.post("http://localhost:8000/api/videos", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
        Authorization: `Bearer ${auth.token}`,
      },
    });
    message.value = "Upload realizado com sucesso!";
    title.value = "";
    fileInput.value.value = "";
  } catch (err) {
    console.error(err);
    message.value = "Falha ao enviar vídeo";
  }
}
</script>
