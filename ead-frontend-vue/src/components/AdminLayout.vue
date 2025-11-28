<template>
  <div class="admin-layout d-flex">
    <!-- Sidebar -->
    <aside :class="['sidebar', { collapsed: isCollapsed }]">
      <div class="sidebar-header">
        <h3 v-if="!isCollapsed">ADMIN</h3>
        <h3 v-else>A</h3>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/admin/dashboard" class="nav-item" active-class="active">
          <i class="bi bi-speedometer2"></i>
          <span v-if="!isCollapsed">Dashboard</span>
        </router-link>

        <router-link to="/admin/users" class="nav-item" active-class="active">
          <i class="bi bi-people"></i>
          <span v-if="!isCollapsed">Usuários</span>
        </router-link>

        <router-link to="/admin/courses" class="nav-item" active-class="active">
          <i class="bi bi-journal-text"></i>
          <span v-if="!isCollapsed">Cursos</span>
        </router-link>

        <router-link to="/admin/videos" class="nav-item" active-class="active">
          <i class="bi bi-camera-video"></i>
          <span v-if="!isCollapsed">Vídeos</span>
        </router-link>
      </nav>
    </aside>

    <!-- Main content -->
    <div class="main">
      <header class="header d-flex align-items-center justify-content-between">
        <button class="btn btn-outline-primary btn-sm" @click="toggleSidebar">
          <i class="bi" :class="isCollapsed ? 'bi-list' : 'bi-x-lg'"></i>
        </button>

        <div class="user-info">
          <span>{{ auth.user?.name }}</span>
          <button class="btn btn-danger btn-sm ms-3" @click="logout">Sair</button>
        </div>
      </header>

      <div class="content p-4">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'

const isCollapsed = ref(false)
const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value
}

const auth = useAuthStore()
const logout = () => auth.logout()
</script>

<style scoped>
.admin-layout {
  height: 100vh;
  background: #f8f9fa;
}

.sidebar {
  width: 240px;
  background: #343a40;
  color: white;
  transition: width .3s;
  display: flex;
  flex-direction: column;
}
.sidebar.collapsed {
  width: 70px;
}

.sidebar-header {
  padding: 20px;
  font-weight: bold;
  text-align: center;
  border-bottom: 1px solid #495057;
}

.sidebar-nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding-top: 10px;
}

.nav-item {
  color: white;
  padding: 12px 18px;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 15px;
  transition: background .2s;
}

.nav-item:hover {
  background: #495057;
}

.nav-item.active {
  background: #0d6efd;
}

.main {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.header {
  height: 60px;
  background: white;
  padding: 0 20px;
  border-bottom: 1px solid #dee2e6;
}

.content {
  flex: 1;
  overflow-y: auto;
}
</style>
