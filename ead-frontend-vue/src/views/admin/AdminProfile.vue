<template>
  <div class="profile card shadow-sm p-4 mx-auto" style="max-width: 500px;">
    <h2 class="mb-4 text-center">Meu Perfil</h2>

    <form @submit.prevent="updateProfile" class="d-flex flex-column gap-3">

      <!-- Preview da imagem -->
      <div class="text-center mb-3">
        <img v-if="previewImage" :src="previewImage" class="rounded-circle border"
          style="width:120px; height:120px; object-fit:cover;" alt="Preview" />
        <img v-else-if="auth.user?.image" :src="auth.user.image_url" class="rounded-circle border"
          style="width:120px; height:120px; object-fit:cover;" alt="Avatar" />
        <div class="mt-2">
          <input type="file" @change="onFileChange" accept="image/*" />
        </div>
      </div>

      <!-- Nome -->
      <input v-model="form.name" type="text" placeholder="Nome" class="form-control" required />

      <!-- E-mail -->
      <input v-model="form.email" type="email" placeholder="E-mail" class="form-control" required />

      <!-- Botão de atualizar -->
      <button type="submit" class="btn btn-primary mt-2">
        Atualizar Perfil
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../../stores/auth'
import { useRouter } from "vue-router";

const router = useRouter();
const auth = useAuthStore()

// Formulário
const form = ref({
  name: auth.user?.name || '',
  email: auth.user?.email || '',
  image: null
})

// Preview da imagem
const previewImage = ref(null)

const onFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.image = file
    previewImage.value = URL.createObjectURL(file)
  }
}

// Atualizar perfil
const updateProfile = async () => {
  const data = new FormData();
  data.append("name", form.value.name);
  data.append("email", form.value.email);
  if (form.value.image) data.append("image", form.value.image);

  try {
    const res = await axios.post("http://localhost:8000/api/profile", data, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    auth.user = res.data;
    localStorage.setItem("user", JSON.stringify(res.data));

    alert("Perfil atualizado com sucesso!");

    // 🔥 Mantém na mesma página
    router.push("/admin/dashboard");

  } catch (err) {
    alert("Erro ao atualizar perfil");
  }
};

</script>

<style scoped>
.profile input[type="file"] {
  cursor: pointer;
}
</style>
