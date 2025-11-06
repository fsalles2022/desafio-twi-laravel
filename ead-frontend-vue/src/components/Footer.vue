<template>
  <footer class="bg-dark text-light py-4 mt-auto sticky-footer">
    <div class="container text-center">
      <p class="mb-1">
        &copy; {{ new Date().getFullYear() }} <strong>DEVNEST</strong> — Todos os direitos reservados.
      </p>
      <p class="small mb-0">
        Desenvolvido com ❤️ por 
        <a href="https://www.linkedin.com/in/fabio-salles-47a85988/" 
           target="_blank" 
           class="text-info text-decoration-none">
           Fábio Salles
        </a>
      </p>
    </div>
  </footer>
</template>

<style scoped>
.sticky-footer {
  position: sticky;
  top: 100%;
  width: 100%;
  border-top: 2px solid rgba(255, 255, 255, 0.1);
  font-size: 0.9rem;
}
</style>
