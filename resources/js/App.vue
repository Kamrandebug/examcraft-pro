<script setup>
import { onMounted, watchEffect, watch } from 'vue';
import { useUiStore } from './stores/uiStore';
import { useExamStore } from './stores/examStore';
import { useAutoPaperStore } from './stores/autoPaperStore';
import { useTypoStore } from './stores/typoStore';
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
const autoPaperStore = useAutoPaperStore();
const typoStore = useTypoStore();
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

// Handle SPA launcher query params (?mode=manual|auto|auto-preview&paper_id=N)
function initFromLauncher() {
    const mode = window.initialMode;
    if (mode && ['manual', 'auto', 'auto-preview'].includes(mode)) {
        uiStore.setView(mode);
    }

    if (window.initialPaperId) {
        loadPaperFromServer(window.initialPaperId);
    }
}

async function loadPaperFromServer(paperId) {
    try {
        const { data } = await window.axios.get(`/api/user/papers/${paperId}`);
        const paper = data.paper || {};
        const pd = data.paper_data || {};

        if (paper.type === 'auto') {
            autoPaperStore.setPaperMeta({
                title: pd.paperTitle || paper.title || '',
                school: pd.schoolName || paper.school_name || '',
                date: pd.paperDate || '',
                grade: pd.grade || paper.grade || '',
                subject: pd.subject || paper.subject || '',
            });
            autoPaperStore.setSelectedMcqs(pd.selectedMcqs || []);
            uiStore.setView('auto-preview');
        } else {
            if (pd.pages) examStore.pages = pd.pages;
            if (pd.paperMeta) examStore.paperMeta = { ...examStore.paperMeta, ...pd.paperMeta };
            if (pd.styleState) examStore.styleState = { ...examStore.styleState, ...pd.styleState };
            if (pd.coverFooter) examStore.coverFooter = { ...examStore.coverFooter, ...pd.coverFooter };
            if (pd.pageFooter) examStore.pageFooter = { ...examStore.pageFooter, ...pd.pageFooter };

            // Restore typography so the designer shows the saved fonts/sizes.
            if (pd.typoState) {
                typoStore.typoState = { ...typoStore.typoState, ...pd.typoState };
                applyTypoToPaper();
            }

            // Restore global layout overrides (numbering, columns, answer boxes).
            if (pd.globalOpts) {
                const g = pd.globalOpts;
                if (g.qNumberStart !== undefined) examStore.qNumberStart = g.qNumberStart;
                if (g.globalOptsLayout !== undefined) examStore.globalOptsLayout = g.globalOptsLayout;
                if (g.showAnswerBoxes !== undefined) examStore.showAnswerBoxes = g.showAnswerBoxes;
                if (g.showMarks !== undefined) examStore.showMarks = g.showMarks;
                if (g.twoColumn !== undefined) examStore.twoColumn = g.twoColumn;
            }

            uiStore.setView('manual');
        }
    } catch (err) {
        console.error('Failed to load paper from server:', err);
    }
}

onMounted(() => {
    initTheme();
    applyTypoToPaper();
    loadAllProjects();
    setupKeyboardShortcuts();
    setupAutoSave();
    initFromLauncher();
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
