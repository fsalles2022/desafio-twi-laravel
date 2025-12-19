<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth'

// layouts
import AdminLayout from './layouts/AdminLayout.vue'
import TeacherLayout from './layouts/TeacherLayout.vue'
import StudentLayout from './layouts/StudentLayout.vue'
import PublicLayout from './layouts/PublicLayout.vue'

const auth = useAuthStore()
const loading = ref(true)

onMounted(async () => {
  if (auth.token && !auth.user) {
    await auth.fetchUser()
  }
  loading.value = false
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
  <component v-if="layoutComponent" :is="layoutComponent" :key="layoutComponent">
    <RouterView />
  </component>


  <div v-else class="text-center py-5">
    <span>Carregando...</span>
  </div>
</template>
