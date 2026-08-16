<template>
  <div id="topbar">
    <div class="topbar-scroll">
    <!-- Back to Home -->
    <button class="tb-icon home-btn" data-tip="Back to Home" @click="goHome">
      <i class="fa fa-arrow-left"></i>
    </button>

    <!-- Brand -->
    <div class="brand">ExamCraft <span>Pro v36</span></div>
    <span class="autosave-badge" :class="{ show: uiStore.showSavedBadge }" id="autosave-badge">
      <i class="fa fa-circle-check"></i> Saved
    </span>

    <div class="topbar-sep"></div>

    <!-- Paper title -->
    <input id="paper-title-input" type="text" placeholder="Paper title..." v-model="examStore.paperMeta.title"/>
    <div class="topbar-sep"></div>

    <!-- ── ADD BLOCKS group ── -->
    <div class="tb-add-group">
      <button class="tb-icon" data-tip="Add MCQ" @click="addBlock('mcq')">
        <i class="fa fa-list-ol"></i>
      </button>
      <button class="tb-icon" data-tip="Add Section" @click="addBlock('section')">
        <i class="fa fa-heading"></i>
      </button>
      <button class="tb-icon" data-tip="Add Text Block" @click="addBlock('text')">
        <i class="fa fa-align-left"></i>
      </button>
      <button class="tb-icon" data-tip="Add Image" @click="addBlock('image')">
        <i class="fa fa-image"></i>
      </button>
      <button class="tb-icon" data-tip="Add Table" @click="addBlock('table')">
        <i class="fa fa-table"></i>
      </button>
    </div>
    <div class="topbar-sep"></div>

    <!-- Cambridge Template -->
    <button class="topbar-btn tb-tip" data-tip="Load Cambridge MCQ template" @click="loadCambridgeTemplate()" style="gap:5px;padding:5px 8px;">
      <i class="fa fa-graduation-cap"></i> <span style="font-size:11px;">Template</span>
    </button>

    <!-- New Page -->
    <button class="tb-icon green" data-tip="Add New Page" @click="addPage()">
      <i class="fa fa-file-circle-plus"></i>
    </button>

    <!-- Clear -->
    <button class="tb-icon danger" data-tip="Clear Paper" @click="clearPaper()">
      <i class="fa fa-trash-alt"></i>
    </button>

    <div class="topbar-spacer"></div>

    <!-- ── THEME DROPDOWN ── -->
    <div id="theme-switcher" style="position:relative;flex-shrink:0;">
      <button id="theme-drop-btn" @click="toggleThemeDropdown($event)" title="Switch Theme"
        style="display:flex;align-items:center;gap:5px;padding:4px 8px;border-radius:var(--radius-sm);border:1px solid var(--border-light);background:var(--bg-card);color:var(--text-secondary);font-size:11px;font-family:var(--font-body);font-weight:500;cursor:pointer;transition:var(--transition);white-space:nowrap;height:32px;">
        <span id="theme-drop-icon">{{ currentThemeIcon }}</span>
        <span id="theme-drop-label" style="font-size:11px;">{{ currentThemeLabel }}</span>
        <i class="fa fa-chevron-down" style="font-size:8px;opacity:0.6;"></i>
      </button>
      <ThemeDropdown />
    </div>
    <div class="topbar-sep"></div>

    <!-- ── PROJECT MANAGEMENT group ── -->
    <button class="tb-icon purple" data-tip="Project Manager" @click="openProjectManager()">
      <i class="fa fa-folder-tree"></i>
    </button>
    <button class="tb-icon" data-tip="Save (Ctrl+S)" @click="saveCurrentProject(true)">
      <i class="fa fa-floppy-disk"></i>
    </button>
    <button class="tb-icon" data-tip="Export JSON" @click="exportProjectJSON()">
      <i class="fa fa-file-arrow-down"></i>
    </button>
    <button class="tb-icon" data-tip="Import JSON" @click="$refs.importInput.click()">
      <i class="fa fa-file-arrow-up"></i>
    </button>
    <input type="file" ref="importInput" accept=".json" style="display:none" @change="importProjectJSON($event)"/>
    <div class="topbar-sep"></div>

    <!-- Preview + Print -->
    <button class="tb-icon blue" data-tip="Preview Paper" @click="showPreview()">
      <i class="fa fa-eye"></i>
    </button>
    <button class="topbar-btn accent tb-tip" data-tip="Print / Export PDF" @click="printPaper()" style="padding:5px 10px;gap:5px;">
      <i class="fa fa-print"></i> <span style="font-size:11px;">Print</span>
    </button>

    <!-- Save to My Papers (server) -->
    <button class="tb-icon green" data-tip="Save to My Papers (server)" @click="saveManualPaper" :disabled="isSavingManual">
      <i class="fa" :class="isSavingManual ? 'fa-spinner fa-spin' : (manualPaperId ? 'fa-cloud-check' : 'fa-cloud-upload-alt')"></i>
    </button>

    <div class="topbar-sep"></div>
    </div><!-- end topbar-scroll -->

    <!-- ── USER PROFILE ── -->
    <div class="user-profile-dropdown" ref="userDropdownRef">
      <button class="user-avatar" @click="toggleUserDropdown" :title="userName" type="button">
        {{ userInitials }}
      </button>

      <Teleport to="body">
        <div class="user-dropdown-menu" v-if="showUserDropdown" :style="dropdownStyle">
          <div class="user-info">
            <div class="user-name">{{ userName }}</div>
            <div class="user-email">{{ userEmail }}</div>
          </div>
          <div class="dropdown-divider"></div>
          <button class="logout-btn" @click="logout">
            <i class="fa fa-sign-out-alt"></i> Logout
          </button>
        </div>
      </Teleport>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, reactive, onMounted, onUnmounted, nextTick } from 'vue';
import { useExamStore } from '../stores/examStore';
import { useUiStore } from '../stores/uiStore';
import { useTypoStore } from '../stores/typoStore';
import { useBlockOperations } from '../composables/useBlockOperations';
import { useProjectManager } from '../composables/useProjectManager';
import { usePrint } from '../composables/usePrint';
import { useTheme } from '../composables/useTheme';
import { useToast } from '../composables/useToast';
import ThemeDropdown from './ui/ThemeDropdown.vue';

const examStore = useExamStore();
const uiStore = useUiStore();
const typoStore = useTypoStore();
const { addBlock, addPage, clearAllBlocks: clearPaper } = useBlockOperations();
const { saveProject: saveCurrentProject, exportJSON: exportProjectJSON, importJSON } = useProjectManager();
const { showPreview, printPaper } = usePrint();
const { THEMES } = useTheme();
const { showToast } = useToast();

const importInput = ref(null);
const userDropdownRef = ref(null);
const showUserDropdown = ref(false);
const isSavingManual = ref(false);
const manualPaperId = ref(window.initialPaperId || null);

const userName = window.authUser?.name || 'User';
const userEmail = window.authUser?.email || '';

const dropdownStyle = reactive({
    position: 'fixed',
    top: '0px',
    right: '0px',
});

function recalcDropdownPosition() {
    if (userDropdownRef.value) {
        const rect = userDropdownRef.value.getBoundingClientRect();
        dropdownStyle.top = (rect.bottom + 4) + 'px';
        dropdownStyle.right = (window.innerWidth - rect.right) + 'px';
    }
}

const userInitials = computed(() => {
    if (!userName) return 'U';
    const parts = userName.trim().split(/\s+/);
    if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return parts[0].substring(0, 2).toUpperCase();
});

const currentThemeIcon = computed(() => THEMES[uiStore.theme]?.icon || '🌙');
const currentThemeLabel = computed(() => THEMES[uiStore.theme]?.label || 'Night');

function toggleThemeDropdown(event) {
    uiStore.showThemeDropdown = !uiStore.showThemeDropdown;
}

function goHome() {
    if (window.authUser?.role === 'admin') {
        uiStore.setView('home');
    } else {
        window.location.href = '/user/dashboard';
    }
}

function toggleUserDropdown() {
    showUserDropdown.value = !showUserDropdown.value;
    if (showUserDropdown.value) {
        nextTick(() => recalcDropdownPosition());
    }
}

function openProjectManager() {
    uiStore.showProjectModal = true;
}

function loadCambridgeTemplate() {
    console.log('Loading template...');
}

async function importProjectJSON(event) {
    const file = event.target.files[0];
    if (file) {
        await importJSON(file);
    }
    event.target.value = '';
}

async function saveManualPaper() {
    isSavingManual.value = true;
    try {
        const paperMeta = examStore.paperMeta || {};
        const paperData = {
            pages: JSON.parse(JSON.stringify(examStore.pages)),
            paperMeta: { ...paperMeta },
            typoState: { ...(typoStore.typoState || {}) },
            styleState: { ...(examStore.styleState || {}) },
            globalOpts: {
                qNumberStart: examStore.qNumberStart,
                globalOptsLayout: examStore.globalOptsLayout,
                showAnswerBoxes: examStore.showAnswerBoxes,
                showMarks: examStore.showMarks,
                twoColumn: examStore.twoColumn,
            },
            coverFooter: { ...(examStore.coverFooter || {}) },
            pageFooter: { ...(examStore.pageFooter || {}) },
        };

        const payload = {
            title: paperMeta.title || 'Untitled Paper',
            type: 'manual',
            grade: paperMeta.subject || null,
            subject: paperMeta.subject || null,
            school_name: paperMeta.organization || null,
            status: 'draft',
            paper_data: paperData,
        };

        if (manualPaperId.value) {
            await window.axios.put(`/api/user/papers/${manualPaperId.value}`, payload);
        } else {
            const { data } = await window.axios.post('/api/user/papers', payload);
            manualPaperId.value = data.paper.id;
        }

        showToast('Paper saved to your account!', 'success');
    } catch (err) {
        console.error('Save manual paper failed:', err);
        showToast('Failed to save paper. Please try again.', 'error');
    } finally {
        isSavingManual.value = false;
    }
}

const handleOutsideClick = (event) => {
    if (!showUserDropdown.value) return;
    // Check if click is on the avatar button (inside userDropdownRef)
    if (userDropdownRef.value && userDropdownRef.value.contains(event.target)) return;
    // Check if click is on the teleported dropdown menu
    if (event.target.closest('.user-dropdown-menu')) return;
    showUserDropdown.value = false;
};

onMounted(() => {
    window.addEventListener('mousedown', handleOutsideClick);
});

onUnmounted(() => {
    window.removeEventListener('mousedown', handleOutsideClick);
});

const logout = () => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/logout';
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
    
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
};
</script>

<style scoped>
.user-profile-dropdown {
    position: relative;
    margin-left: 8px;
    flex-shrink: 0;
}

.user-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #e74c3c;
    color: white;
    font-weight: bold;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 2px solid rgba(255,255,255,0.3);
    user-select: none;
    padding: 0;
    line-height: 1;
    font-family: inherit;
    -webkit-appearance: none;
    appearance: none;
}

.user-dropdown-menu {
    position: absolute;
    right: 0;
    top: 44px;
    background: #2d2d2d;
    border: 1px solid #444;
    border-radius: 8px;
    padding: 12px;
    min-width: 200px;
    z-index: 9999;
    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
    animation: fadeIn 0.2s ease;
}

.user-info {
    padding-bottom: 5px;
}

.user-name {
    color: white;
    font-weight: bold;
    font-size: 14px;
    display: block;
}

.user-email {
    color: #aaa;
    font-size: 12px;
    margin-top: 2px;
    display: block;
    word-break: break-all;
}

.dropdown-divider {
    border-top: 1px solid #444;
    margin: 10px 0;
}

.logout-btn {
    width: 100%;
    background: #e74c3c;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s;
}

.logout-btn:hover {
    background: #c0392b;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
