<template>
  <!-- BACKDROP -->
  <div v-if="show" class="modal-backdrop" @click="close">
    
    <!-- MODAL CONTAINER -->
    <div class="modal-container" @click.stop>
      
      <!-- HEADER -->
      <div class="modal-header">
        <h3 class="title">
          {{ isEdit ? "Editar Curso" : "Criar Novo Curso" }}
        </h3>

        <button class="btn-close" @click="close">×</button>
      </div>

      <!-- BODY -->
      <div class="modal-body">

        <div class="form-group">
          <label>Título do Curso</label>
          <input v-model="form.title" type="text" class="input" placeholder="Ex: JavaScript para Iniciantes" />
        </div>

        <div class="form-group">
          <label>Descrição</label>
          <textarea v-model="form.description" class="textarea" rows="4"
            placeholder="Descreva sobre o curso..."></textarea>
        </div>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer">
        <button class="btn-cancel" @click="close">Cancelar</button>

        <button class="btn-save" :disabled="loading" @click="save">
          <span v-if="loading" class="spinner"></span>
          {{ isEdit ? "Salvar Alterações" : "Criar Curso" }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import axios from "axios";
import { useAuthStore } from "@/stores/auth";

const props = defineProps({
  show: Boolean,
  course: { type: Object, default: null },
});

const emit = defineEmits(["close", "saved"]);

const auth = useAuthStore();
const loading = ref(false);

const form = ref({
  title: "",
  description: "",
});

const isEdit = computed(() => !!props.course);

// Preenche ao abrir
watch(
  () => props.show,
  (val) => {
    if (val && props.course) {
      form.value = {
        title: props.course.title,
        description: props.course.description,
      };
    } else if (val && !props.course) {
      form.value = { title: "", description: "" };
    }
  }
);

// Fechar modal
function close() {
  emit("close");
}

// Salvar curso
async function save() {
  loading.value = true;

  try {
    if (isEdit.value) {
      await axios.put(
        `http://localhost:8000/api/courses/${props.course.id}`,
        form.value,
        { headers: { Authorization: `Bearer ${auth.token}` } }
      );
    } else {
      await axios.post(
        "http://localhost:8000/api/courses",
        form.value,
        { headers: { Authorization: `Bearer ${auth.token}` } }
      );
    }

    emit("saved");
    close();

  } catch (error) {
    console.error("Erro ao salvar curso:", error);
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

.label {
  font-weight: 600;
}

.input,
.textarea {
  width: 100%;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #444;
  background: #2a2a2a;
  color: #fff;
  font-size: 15px;
  outline: none;
}

.input:focus,
.textarea:focus {
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
