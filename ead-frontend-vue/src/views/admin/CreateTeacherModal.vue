<template>
  <!-- BACKDROP -->
  <div v-if="show" class="modal-backdrop" @click="close">

    <!-- MODAL CONTAINER -->
    <div class="modal-container" @click.stop>

      <!-- HEADER -->
      <div class="modal-header">
        <h3 class="title">
          Criar Novo Professor
        </h3>

        <button class="btn-close" @click="close">×</button>
      </div>

      <!-- BODY -->
      <div class="modal-body">

        <div v-if="error" class="alert alert-danger mb-3">
          {{ error }}
        </div>

        <div class="form-group">
          <label>Nome</label>
          <input v-model="form.name" type="text" class="input" placeholder="Nome do professor" />
        </div>

        <div class="form-group">
          <label>E-mail</label>
          <input v-model="form.email" type="email" class="input" placeholder="email@teste.com" />
        </div>

        <div class="form-group">
          <label>Senha</label>
          <input v-model="form.password" type="password" class="input" placeholder="Senha" />
        </div>

        <div class="form-group">
          <label>Confirmar Senha</label>
          <input v-model="form.password_confirmation" type="password" class="input"
                 placeholder="Confirmar senha" />
        </div>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer">
        <button class="btn-cancel" @click="close">Cancelar</button>

        <button class="btn-save" :disabled="loading" @click="save">
          <span v-if="loading" class="spinner"></span>
          Criar Professor
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import { useAuthStore } from "../../stores/auth";

const props = defineProps({
  show: Boolean,
});

const emit = defineEmits(["close", "saved"]);

const auth = useAuthStore();
const loading = ref(false);
const error = ref(null);

const form = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

// Limpa ao abrir
watch(
  () => props.show,
  (val) => {
    if (val) {
      form.value = {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
      };
      error.value = null;
    }
  }
);

// Fechar modal
function close() {
  emit("close");
}

// Salvar professor
async function save() {
  error.value = null;
  loading.value = true;

  try {
    await axios.post(
      "http://localhost:8000/api/admin/create-teacher",
      form.value,
      {
        headers: {
          Authorization: `Bearer ${auth.token}`,
          Accept: "application/json",
        },
      }
    );

    emit("saved");
    close();

  } catch (err) {
    console.error("Erro ao criar professor:", err);
    error.value =
      err.response?.data?.message ||
      err.response?.data?.error ||
      "Erro ao criar professor";
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
/* BACKDROP */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.65);
  backdrop-filter: blur(6px);
  display: flex;
  justify-content: center;
  align-items: center;
  animation: fadeIn 0.2s ease;
  z-index: 9999;
}

/* MODAL */
.modal-container {
  width: 480px;
  background: #1f1f1f;
  padding: 25px;
  border-radius: 16px;
  box-shadow: 0 0 40px rgba(0, 0, 0, 0.6);
  animation: pop 0.25s ease;
  color: #fff;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.title {
  font-size: 22px;
  font-weight: 700;
}

.btn-close {
  background: transparent;
  border: none;
  color: #aaa;
  font-size: 24px;
  cursor: pointer;
}

.btn-close:hover {
  color: #fff;
}

.modal-body {
  margin-top: 20px;
}

.form-group {
  margin-bottom: 18px;
}

.input {
  width: 100%;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #444;
  background: #2a2a2a;
  color: #fff;
  font-size: 15px;
  outline: none;
}

.input:focus {
  border-color: #6c5ce7;
}

/* FOOTER */
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 25px;
}

.btn-cancel {
  padding: 8px 18px;
  background: #444;
  border: none;
  border-radius: 8px;
  color: #ddd;
  cursor: pointer;
}

.btn-cancel:hover {
  background: #555;
}

.btn-save {
  padding: 8px 18px;
  background: #6c5ce7;
  border: none;
  border-radius: 8px;
  color: white;
  cursor: pointer;
  font-weight: 600;
}

.btn-save:hover {
  background: #5a48d1;
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.spinner {
  border: 3px solid #fff;
  border-bottom-color: transparent;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  display: inline-block;
  margin-right: 6px;
  animation: spin 0.7s linear infinite;
}

/* ANIMAÇÕES */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes pop {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
