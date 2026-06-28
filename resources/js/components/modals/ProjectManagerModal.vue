<template>
  <div id="proj-modal" class="modal-overlay" v-show="uiStore.showProjectModal" @click.self="uiStore.showProjectModal = false">
    <div class="modal-box">
      <div class="modal-header">
        <h5 class="m-0">Project Manager</h5>
        <button class="panel-collapse-btn" @click="uiStore.showProjectModal = false">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="modal-body">
        <div class="d-flex gap-2 mb-3">
          <button class="topbar-btn accent flex-grow-1" @click="createNewProject">
            <i class="fa-solid fa-plus"></i> New Project
          </button>
          <button class="topbar-btn flex-grow-1" @click="triggerImport">
            <i class="fa-solid fa-file-import"></i> Import JSON
          </button>
          <input type="file" ref="fileInput" style="display: none" @change="handleImport" accept=".json" />
        </div>
        
        <div class="projects-list">
          <div v-for="proj in projectStore.projects" :key="proj.id" class="project-item">
            <div class="project-info">
              <div class="project-name">{{ proj.name }}</div>
              <div class="project-date">{{ formatDate(proj.savedAt || proj.updatedAt) }}</div>
            </div>
            <div class="project-actions">
              <button class="topbar-btn blue btn-sm" @click="handleLoad(proj.id)">Load</button>
              <button class="topbar-btn danger btn-sm" @click="handleDelete(proj.id)"><i class="fa-solid fa-trash"></i></button>
            </div>
          </div>
          <div v-if="projectStore.projects.length === 0" class="empty-hint p-4 text-center">No projects saved yet</div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="topbar-btn" @click="exportJSON">Export Current Project</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useUiStore } from '../../stores/uiStore';
import { useProjectStore } from '../../stores/projectStore';
import { useProjectManager } from '../../composables/useProjectManager';
import { useExamStore } from '../../stores/examStore';

const uiStore = useUiStore();
const projectStore = useProjectStore();
const examStore = useExamStore();
const { loadAllProjects, loadProject, deleteProject, importJSON, exportJSON } = useProjectManager();

const fileInput = ref(null);

onMounted(() => {
  loadAllProjects();
});

// Refresh projects when modal opens
watch(() => uiStore.showProjectModal, (val) => {
  if (val) loadAllProjects();
});

function createNewProject() {
  if (confirm('Create new project? Current unsaved changes will be lost.')) {
    examStore.pages = [{ id: 'page-1', blocks: [] }];
    examStore.paperMeta.title = '';
    projectStore.currentProjectId = null;
    uiStore.showProjectModal = false;
  }
}

function triggerImport() {
  fileInput.value.click();
}

async function handleImport(e) {
  const file = e.target.files[0];
  if (file) {
    await importJSON(file);
    await loadAllProjects();
  }
  e.target.value = '';
}

async function handleLoad(id) {
  await loadProject(id);
  uiStore.showProjectModal = false;
}

async function handleDelete(id) {
  if (confirm('Are you sure you want to delete this project?')) {
    await deleteProject(id);
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleString();
}
</script>
