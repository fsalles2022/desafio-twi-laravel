<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  media: Object
})

const emit = defineEmits(['toggleWatched'])


const hover = ref(false)

const isAudio = computed(() =>
  props.media.filename.endsWith('.mp3') || props.media.filename.endsWith('.wav')
)

const mediaType = computed(() => {
  if (props.media.filename.endsWith('.mp3')) return 'audio/mpeg'
  if (props.media.filename.endsWith('.wav')) return 'audio/wav'
  return 'video/mp4'
})
</script>

<template>
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card shadow-sm h-100 media-card-advanced">

      <!-- PLAYER DE ÁUDIO OU VÍDEO -->
      <component :is="isAudio ? 'audio' : 'video'" controls class="card-img-top" :width="isAudio ? undefined : 720"
        :height="isAudio ? undefined : 360" :muted="!isAudio" preload="metadata" @mouseover="hover = true"
        @mouseleave="hover = false">
        <!-- 👉 AQUI AGORA É SEMPRE media.url -->
        <source :src="media.url" :type="mediaType" />
        Seu navegador não suporta este formato.
      </component>

      <div class="card-body d-flex flex-column">
        <h5 class="card-title">{{ media.title }}</h5>

        <div class="mt-auto">
          <!-- BOTÃO DE DOWNLOAD -->
          <a :href="media.url" download class="btn btn-sm btn-primary w-100">
            ⬇️ Baixar
          </a>
        </div>
        <button class="btn btn-sm w-100 mt-2" :class="media.watched ? 'btn-success' : 'btn-outline-primary'"
          @click="emit('toggleWatched', media)">
          {{ media.watched ? '✅ Assistido' : '▶ Marcar como Assistido' }}
        </button>

      </div>

    </div>
  </div>
</template>

<style scoped>
.media-card-advanced:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
}
</style>
