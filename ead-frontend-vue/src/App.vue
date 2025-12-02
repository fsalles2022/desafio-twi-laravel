<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth'

// layouts
import AdminLayout from './components/AdminLayout.vue'
import TeacherLayout from './components/TeacherLayout.vue'
import StudentLayout from './components/StudentLayout.vue'
import PublicLayout from './components/PublicLayout.vue'

const auth = useAuthStore()
const loading = ref(true)

onMounted(async () => {
  try {
    await auth.fetchUser()
  } finally {
    loading.value = false
  }
})

const layoutComponent = computed(() => {
  if (loading.value) return null

  const user = auth.user
  if (!user) return PublicLayout

  const roles = user.roles || []

  if (roles.includes('admin')) return AdminLayout
  if (roles.includes('teacher')) return TeacherLayout
  return StudentLayout
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
