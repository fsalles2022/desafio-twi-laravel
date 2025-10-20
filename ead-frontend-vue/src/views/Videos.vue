<template>
  <div>
    <h1>Lista de Vídeos</h1>
    <div v-if="videos.length">
      <div v-for="v in videos" :key="v.id" style="margin-bottom:20px;">
        <h3>{{ v.title }}</h3>
        <video width="720" height="360" controls>
          <source :src="v.url" type="video/mp4">
        </video>
      </div>
    </div>
    <div v-else>
      Nenhum vídeo disponível
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../stores/auth'

export default {
  setup() {
    const videos = ref([])
    const auth = useAuthStore()

    onMounted(async () => {
      try {
        const res = await axios.get('http://localhost:8000/api/videos/user', {
          headers: { Authorization: `Bearer ${auth.token}` }
        })
        videos.value = res.data
      } catch (err) {
        console.error(err)
      }
    })

    return { videos }
  }
}
</script>
