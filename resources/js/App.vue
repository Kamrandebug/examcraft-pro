<script setup>
import { onMounted, watchEffect, watch } from 'vue';
import { useUiStore } from './stores/uiStore';
import { useExamStore } from './stores/examStore';
import { useProjectStore } from './stores/projectStore';
import { useTheme } from './composables/useTheme';
import { useTypography } from './composables/useTypography';
import { useProjectManager } from './composables/useProjectManager';
import { useBlockOperations } from './composables/useBlockOperations';
import { useToast } from './composables/useToast';
import { useDebounceFn } from '@vueuse/core';

import TopBar from './components/TopBar.vue';
import LeftPanel from './components/LeftPanel.vue';
import CanvasArea from './components/CanvasArea.vue';
import RightPanel from './components/RightPanel.vue';
import VerticalRuler from './components/ui/VerticalRuler.vue';
import ResizeHandle from './components/ui/ResizeHandle.vue';
import ProjectManagerModal from './components/modals/ProjectManagerModal.vue';
import PreviewOverlay from './components/modals/PreviewOverlay.vue';
import ToastContainer from './components/ui/ToastContainer.vue';
import HomeScreen from './views/HomeScreen.vue';
import AutoPaperGenerator from './views/AutoPaperGenerator.vue';
import AutoPaperPreview from './views/AutoPaperPreview.vue';

const uiStore = useUiStore();
const examStore = useExamStore();
const projectStore = useProjectStore();
const { initTheme } = useTheme();
const { applyTypoToPaper } = useTypography();
const { loadAllProjects, saveProject } = useProjectManager();
const { deleteBlock } = useBlockOperations();
const { showToast } = useToast();

const debouncedSave = useDebounceFn(async () => {
    await saveProject();
    uiStore.showSavedBadge = true;
    setTimeout(() => {
        uiStore.showSavedBadge = false;
    }, 2000);
}, 2000);

function togglePanel(side) {
    if (side === 'left') uiStore.leftCollapsed = !uiStore.leftCollapsed;
    if (side === 'right') uiStore.rightCollapsed = !uiStore.rightCollapsed;
}

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            saveProject();
        }
        if (e.key === 'Delete' && examStore.selectedBlockId) {
            if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;
            deleteBlock(examStore.selectedBlockId);
        }
    });
}

function setupAutoSave() {
    watch(() => examStore.pages, debouncedSave, { deep: true });
}

onMounted(() => {
    initTheme();
    applyTypoToPaper();
    loadAllProjects();
    setupKeyboardShortcuts();
    setupAutoSave();
});

watchEffect(() => {
    document.documentElement.setAttribute('data-theme', uiStore.theme);
});
</script>

<template>
  <div id="app" :data-theme="uiStore.theme" style="display:grid;grid-template-rows:auto 1fr;height:100vh;overflow:hidden;">
    <template v-if="uiStore.currentView === 'manual'">
      <TopBar />
      <div id="main">
        <div class="panel-strip left-strip" id="left-strip" :class="{ visible: uiStore.leftCollapsed }">
          <button class="strip-toggle-btn" @click="togglePanel('left')">
            <i class="fa fa-chevron-right"></i>
          </button>
          <span class="strip-label">Paper Designer</span>
        </div>

        <LeftPanel v-show="!uiStore.leftCollapsed" />
        <VerticalRuler side="left" />
        <ResizeHandle side="left" id="resize-left" />
        <CanvasArea />
        <ResizeHandle side="right" id="resize-right" />
        <VerticalRuler side="right" />
        <RightPanel v-show="!uiStore.rightCollapsed" />

        <div class="panel-strip right-strip" id="right-strip" :class="{ visible: uiStore.rightCollapsed }">
          <button class="strip-toggle-btn" @click="togglePanel('right')">
            <i class="fa fa-chevron-left"></i>
          </button>
          <span class="strip-label">Properties</span>
        </div>
      </div>

      <ProjectManagerModal />
      <PreviewOverlay />
      <ToastContainer />
      <div id="tb-tooltip"></div>
    </template>

    <HomeScreen v-else-if="uiStore.currentView === 'home'" />
    <AutoPaperGenerator v-else-if="uiStore.currentView === 'auto'" />
    <AutoPaperPreview v-else-if="uiStore.currentView === 'auto-preview'" />
  </div>
</template>

<style>
/* App-level grid layout is handled via inline style as requested */
#main {
  display: flex;
  flex-direction: row;
  height: 100%;
  overflow: hidden;
  min-width: 0;
}
</style>
