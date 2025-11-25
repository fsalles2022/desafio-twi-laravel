<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth'

// layouts
import TeacherLayout from './components/TeacherLayout.vue'
import StudentLayout from './components/StudentLayout.vue'

const auth = useAuthStore()
const loading = ref(true)

onMounted(async () => {
  await auth.fetchUser()
  loading.value = false
})

// escolhe qual layout usar
const layoutComponent = computed(() => {
  if (loading.value) return null // enquanto carrega, não renderiza nada

  if (!auth.user) return StudentLayout // não logado = layout de aluno

  return auth.user.roles?.includes('teacher')
    ? TeacherLayout
    : StudentLayout
})
</script>

<template>
  <component v-if="layoutComponent" :is="layoutComponent">
    <RouterView />
  </component>

  <div v-else class="text-center py-5">
    <span>Carregando...</span>
  </div>
</template>
