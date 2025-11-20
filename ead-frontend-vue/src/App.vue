<script setup>
import { computed, onMounted } from 'vue'
import { RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth'

// layouts
import TeacherLayout from './components/TeacherLayout.vue'
import StudentLayout from './components/StudentLayout.vue'

const auth = useAuthStore()

onMounted(() => {
  auth.fetchUser()
})

// escolhe qual layout usar
const layoutComponent = computed(() => {
  if (!auth.user) return StudentLayout // não logado = layout de aluno

  return auth.user.roles?.includes('teacher')
    ? TeacherLayout
    : StudentLayout
})
</script>

<template>
  <component :is="layoutComponent">
    <RouterView />
  </component>
</template>
