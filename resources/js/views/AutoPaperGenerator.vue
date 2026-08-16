<template>
  <div class="auto-generator">
    <div class="auto-generator-inner">
      <div class="auto-generator-header">
        <button class="btn btn-sm back-btn" @click="goHome">
          <i class="fa fa-arrow-left"></i> Back to Home
        </button>
        <h3 class="auto-generator-title">Auto Paper Generator</h3>
      </div>

      <div class="card settings-card">
        <div class="card-header">
          <h5 class="card-title mb-0">Paper Settings</h5>
        </div>
        <div class="card-body">
          <div class="ag-section-title">Paper Identity</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="ag-label">Paper Title</label>
              <input type="text" class="form-control ag-input" v-model="paperTitle" placeholder="e.g. Mid-Term Examination 2025" />
            </div>
            <div class="col-md-6">
              <label class="ag-label">Paper Code</label>
              <input type="text" class="form-control ag-input" v-model="paperCode" placeholder="e.g. 5054/11" />
            </div>
            <div class="col-md-6">
              <label class="ag-label">Session</label>
              <input type="text" class="form-control ag-input" v-model="session" placeholder="e.g. May/June 2025" />
            </div>
            <div class="col-md-6">
              <label class="ag-label">Duration</label>
              <input type="text" class="form-control ag-input" v-model="duration" placeholder="e.g. 1 hour" />
            </div>
          </div>

          <div class="ag-section-title">Institution</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="ag-label">School Name</label>
              <input type="text" class="form-control ag-input" v-model="schoolName" placeholder="e.g. The City School" />
            </div>
            <div class="col-md-6">
              <label class="ag-label">Date</label>
              <input type="date" class="form-control ag-input" v-model="paperDate" />
            </div>
          </div>

          <div class="ag-section-title">Paper Setup</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="ag-label">Grade</label>
              <select class="form-select ag-input" v-model="selectedGrade" @change="onGradeChange">
                <option value="" disabled>Select grade</option>
                <option v-for="g in gradeList" :key="g" :value="g">{{ g }}</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="ag-label">Subject</label>
              <select class="form-select ag-input" v-model="selectedSubject" :disabled="!selectedGrade" @change="onSubjectChange">
                <option value="" disabled>Select subject</option>
                <option v-for="s in availableSubjects" :key="s" :value="s">{{ s }}</option>
              </select>
            </div>
          </div>

          <div class="ag-section-title">Additional Materials</div>
          <div class="row g-3">
            <div class="col-12">
              <label class="ag-label">Additional Materials (one per line)</label>
              <textarea class="form-control ag-input" rows="3" v-model="additionalMaterials"></textarea>
            </div>
          </div>

          <div class="ag-section-title">Instructions</div>
          <div class="row g-3">
            <div class="col-12">
              <div class="ag-instructions-preview">{{ instructions }}</div>
              <button type="button" class="btn btn-sm ag-outline-btn mt-2" @click="editingInstructions = !editingInstructions">
                {{ editingInstructions ? '✅ Done Editing' : '✏️ Edit Instructions' }}
              </button>
              <textarea v-show="editingInstructions" class="form-control ag-input mt-2" rows="10" v-model="instructions"></textarea>
            </div>
          </div>

          <div class="ag-section-title">Logo (Optional)</div>
          <div class="row g-3">
            <div class="col-12">
              <label class="ag-label">Institution Logo (Optional)</label>
              <div class="ag-logo-row">
                <img v-if="logoDataUrl" class="ag-logo-preview" :src="logoDataUrl" alt="Logo preview" />
                <button type="button" class="btn btn-sm ag-outline-btn" @click="pickLogo">Choose Logo</button>
                <button v-if="logoDataUrl" type="button" class="btn btn-sm ag-outline-btn ag-remove-btn" @click="removeLogo">Remove Logo</button>
              </div>
              <input ref="logoInput" type="file" accept="image/*" class="d-none" @change="onLogoChange" />
            </div>
          </div>
        </div>
      </div>

      <div class="load-bar">
        <button class="btn ag-load-btn" :disabled="!canLoad || isLoading" @click="loadMcqs">
          <i class="fa fa-download" v-if="!isLoading"></i>
          <span v-if="isLoading" class="spinner-border spinner-border-sm me-1" role="status"></span>
          {{ isLoading ? 'Loading...' : 'Load MCQs' }}
        </button>
      </div>

      <div class="card questions-card" v-if="hasLoaded">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <span class="badge-count">Found {{ questions.length }} questions</span>
            <span class="badge-selected ms-2">{{ selectedCount }} selected &mdash; Total Marks: {{ selectedCount }}</span>
          </div>
          <div>
            <button class="btn btn-sm ag-outline-btn me-1" @click="selectAll">Select All</button>
            <button class="btn btn-sm ag-outline-btn" @click="deselectAll">Deselect All</button>
          </div>
        </div>
        <div class="card-body ag-questions-body">
          <div class="ag-mcq-row" v-for="q in questions" :key="q.id" :class="{ selected: selectedIds.has(q.id) }" @click="toggleQuestion(q.id)">
            <div class="ag-checkbox">
              <div class="ag-checkbox-box" :class="{ checked: selectedIds.has(q.id) }">
                <i class="fa fa-check" v-if="selectedIds.has(q.id)"></i>
              </div>
            </div>
            <div class="ag-mcq-content">
              <div class="ag-mcq-stem-row">
                <div class="ag-mcq-stem" v-html="q.stem_text"></div>
                <img
                  v-if="q.stem_image"
                  :src="q.stem_image"
                  style="max-height: 40px; margin-left: 10px; vertical-align: middle;"
                  alt="Question image"
                />
              </div>
              <div style="font-size: 0.8em; color: #aaa; margin-left: 24px;">
                <span v-for="opt in q.options" :key="opt.label" style="margin-right: 16px;">
                  {{ opt.label }}. {{ opt.text }}
                </span>
              </div>
            </div>
          </div>
          <div v-if="questions.length === 0 && !isLoading && !loadError" class="text-center text-muted py-4">
            No questions found for this grade and subject.
          </div>
          <div v-if="loadError" class="text-center text-danger py-4">
            {{ loadError }}
          </div>
        </div>
      </div>

      <div class="generate-bar" v-if="hasLoaded">
        <button class="btn ag-generate-btn" :disabled="!canGenerate" @click="generatePaper">
          <i class="fa fa-magic"></i> Generate Paper
        </button>
        <span class="generate-hint" v-if="!canGenerate">
          {{ selectedIds.size === 0 ? 'Select at least one question' : 'Fill all paper settings above' }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { useUiStore } from '../stores/uiStore';
import { useAutoPaperStore } from '../stores/autoPaperStore';
import { useToast } from '../composables/useToast';

const uiStore = useUiStore();
const autoPaperStore = useAutoPaperStore();
const { showToast } = useToast();

const gradeSubjects = {
  'O Level': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'English Language', 'Urdu', 'Islamiyat', 'Pakistan Studies', 'Economics', 'Commerce', 'Accounting'],
  'A Level': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Further Mathematics', 'Computer Science', 'Economics', 'Psychology'],
  '8th Grade': ['General Science', 'Mathematics', 'Urdu', 'English', 'Social Studies', 'Islamiyat', 'Pakistan Studies'],
  '9th Grade': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'Urdu', 'English', 'Islamiyat', 'Pakistan Studies'],
  '10th Grade': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'Urdu', 'English', 'Islamiyat', 'Pakistan Studies'],
};

const gradeList = Object.keys(gradeSubjects);

const paperTitle = ref(autoPaperStore.paperTitle);
const schoolName = ref(autoPaperStore.schoolName);
const paperDate = ref(autoPaperStore.paperDate);
const selectedGrade = ref(autoPaperStore.grade);
const selectedSubject = ref(autoPaperStore.subject);
const paperCode = ref(autoPaperStore.paperCode);
const session = ref(autoPaperStore.session);
const duration = ref(autoPaperStore.duration);
const additionalMaterials = ref(autoPaperStore.additionalMaterials);
const instructions = ref(autoPaperStore.instructions);
const logoDataUrl = ref(autoPaperStore.logoDataUrl);
const editingInstructions = ref(false);
const logoInput = ref(null);

const availableSubjects = computed(() => {
  return selectedGrade.value ? (gradeSubjects[selectedGrade.value] || []) : [];
});

const questions = ref([]);
const selectedIds = ref(new Set());
const isLoading = ref(false);
const loadError = ref('');
const hasLoaded = ref(false);

const selectedCount = computed(() => selectedIds.value.size);

const canLoad = computed(() => selectedGrade.value !== '' && selectedSubject.value !== '');

const canGenerate = computed(() =>
  selectedIds.value.size > 0 &&
  paperTitle.value.trim() !== '' &&
  schoolName.value.trim() !== '' &&
  paperDate.value !== ''
);

watch(selectedGrade, () => {
  selectedSubject.value = '';
  questions.value = [];
  selectedIds.value = new Set();
  hasLoaded.value = false;
  loadError.value = '';
});

watch(selectedSubject, () => {
  questions.value = [];
  selectedIds.value = new Set();
  hasLoaded.value = false;
  loadError.value = '';
});

watch(
  [
    paperTitle,
    schoolName,
    paperDate,
    selectedGrade,
    selectedSubject,
    paperCode,
    session,
    duration,
    additionalMaterials,
    instructions,
  ],
  () => {
    autoPaperStore.setPaperMeta({
      title: paperTitle.value,
      school: schoolName.value,
      date: paperDate.value,
      grade: selectedGrade.value,
      subject: selectedSubject.value,
      paperCode: paperCode.value,
      session: session.value,
      duration: duration.value,
      additionalMaterials: additionalMaterials.value,
      instructions: instructions.value,
    });
  }
);

function onGradeChange() {}
function onSubjectChange() {}

function pickLogo() {
  if (logoInput.value) logoInput.value.click();
}

function onLogoChange(event) {
  const file = event.target.files && event.target.files[0];
  if (!file) return;
  resizeLogo(file).then((dataUrl) => {
    logoDataUrl.value = dataUrl;
    autoPaperStore.setLogo({ file, dataUrl });
  });
  event.target.value = '';
}

/**
 * Downscale the logo to a bounded size (max 300px on the longest edge) and
 * re-encode as a JPEG data URL. Storing the raw full-resolution PNG as a
 * base64 string bloats `paper_data` beyond MySQL's `max_allowed_packet`,
 * which makes saving fail with a 500. JPEG is far smaller for photographic
 * logos and is fine for a paper header thumbnail.
 */
function resizeLogo(file) {
  return new Promise((resolve) => {
    const reader = new FileReader();
    reader.onload = () => {
      const img = new Image();
      img.onload = () => {
        const MAX = 300;
        let { width, height } = img;
        const scale = Math.min(1, MAX / Math.max(width, height));
        width = Math.round(width * scale);
        height = Math.round(height * scale);

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, width, height);

        // Prefer JPEG; fall back to PNG if the source has transparency.
        let dataUrl;
        try {
          dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        } catch {
          dataUrl = canvas.toDataURL('image/png');
        }
        resolve(dataUrl);
      };
      img.onerror = () => resolve(reader.result);
      img.src = reader.result;
    };
    reader.onerror = () => resolve(null);
    reader.readAsDataURL(file);
  });
}

function removeLogo() {
  logoDataUrl.value = null;
  autoPaperStore.setLogo({ file: null, dataUrl: null });
}

function goHome() {
  if (window.authUser?.role === 'admin') {
    uiStore.setView('home');
  } else {
    window.location.href = '/user/dashboard';
  }
}

async function loadMcqs(preSelectedIds) {
  isLoading.value = true;
  loadError.value = '';
  questions.value = [];
  selectedIds.value = new Set();
  hasLoaded.value = false;

  try {
    const response = await window.axios.get('/api/question-bank/filter', {
      params: {
        grade: selectedGrade.value,
        subject: selectedSubject.value,
      },
    });

    if (response.data.success) {
      questions.value = response.data.data;
      hasLoaded.value = true;

      if (preSelectedIds && preSelectedIds.length) {
        const idSet = new Set(preSelectedIds);
        selectedIds.value = new Set(
          questions.value
            .filter((q) => idSet.has(q.id))
            .map((q) => q.id)
        );
      }

      if (questions.value.length === 0) {
        loadError.value = 'No MCQs found for this grade and subject.';
      }
    } else {
      loadError.value = 'Failed to load questions. Please try again.';
    }
  } catch {
    loadError.value = 'Network error. Please check your connection.';
  } finally {
    isLoading.value = false;
  }
}

async function loadPaperForEdit(paperId) {
  try {
    const { data } = await window.axios.get(`/api/user/papers/${paperId}`);
    const pd = data.paper_data || {};

    // Keep the paper id so the preview's "Save" updates instead of duplicating.
    window.initialPaperId = Number(paperId);

    autoPaperStore.setPaperMeta({
      title: pd.paperTitle ?? '',
      school: pd.schoolName ?? '',
      date: pd.paperDate ?? '',
      grade: pd.grade ?? '',
      subject: pd.subject ?? '',
      paperCode: pd.paperCode ?? '',
      session: pd.session ?? '',
      duration: pd.duration ?? '',
      additionalMaterials: pd.additionalMaterials ?? undefined,
      instructions: pd.instructions ?? undefined,
    });
    autoPaperStore.setLogo({ file: null, dataUrl: pd.logoDataUrl ?? null });
    autoPaperStore.setSelectedMcqs(pd.selectedMcqs ?? []);

    // Mirror store state into the local form refs.
    paperTitle.value = autoPaperStore.paperTitle;
    schoolName.value = autoPaperStore.schoolName;
    paperDate.value = autoPaperStore.paperDate;
    paperCode.value = autoPaperStore.paperCode;
    session.value = autoPaperStore.session;
    duration.value = autoPaperStore.duration;
    additionalMaterials.value = autoPaperStore.additionalMaterials;
    instructions.value = autoPaperStore.instructions;
    logoDataUrl.value = autoPaperStore.logoDataUrl;

    const gradeToSet = autoPaperStore.grade;
    const subjectToSet = autoPaperStore.subject;

    // Set grade first, then subject — but the grade watcher resets subject,
    // so restore the subject after watchers flush.
    selectedGrade.value = gradeToSet;
    await nextTick();
    selectedSubject.value = subjectToSet;

    const preSelectedIds = (pd.selectedMcqs ?? []).map((q) => q.id);

    if (selectedGrade.value && selectedSubject.value) {
      await loadMcqs(preSelectedIds);
    }

    showToast('Paper loaded for editing.', 'success');
  } catch (err) {
    console.error('Failed to load paper for editing:', err);
    showToast('Failed to load paper for editing.', 'error');
  }
}

onMounted(() => {
  const params = new URLSearchParams(window.location.search);
  const paperId = params.get('paper_id');
  if (paperId) {
    loadPaperForEdit(paperId);
  } else {
    // Restore the persisted selection after a refresh: re-fetch the matching
    // questions and re-check the previously selected ones.
    const persisted = autoPaperStore.selectedMcqs || [];
    if (selectedGrade.value && selectedSubject.value && persisted.length) {
      loadMcqs(persisted.map((q) => q.id));
    } else if (selectedGrade.value && selectedSubject.value) {
      loadMcqs();
    }
  }
});

function toggleQuestion(id) {
  const newSet = new Set(selectedIds.value);
  if (newSet.has(id)) {
    newSet.delete(id);
  } else {
    newSet.add(id);
  }
  selectedIds.value = newSet;
}

function selectAll() {
  selectedIds.value = new Set(questions.value.map((q) => q.id));
}

function deselectAll() {
  selectedIds.value = new Set();
}

function generatePaper() {
  autoPaperStore.setPaperMeta({
    title: paperTitle.value.trim(),
    school: schoolName.value.trim(),
    date: paperDate.value,
    grade: selectedGrade.value,
    subject: selectedSubject.value,
    paperCode: paperCode.value.trim(),
    session: session.value.trim(),
    duration: duration.value.trim(),
    additionalMaterials: additionalMaterials.value,
    instructions: instructions.value,
  });
  autoPaperStore.setSelectedMcqs(
    questions.value.filter((q) => selectedIds.value.has(q.id))
  );
  uiStore.setView('auto-preview');
}
</script>

<style scoped>
.auto-generator {
  height: 100%;
  min-height: 0;
  grid-row: 1 / -1;
  overflow-y: auto;
  background: var(--bg-primary);
}

.auto-generator-inner {
  max-width: 900px;
  margin: 0 auto;
  padding: 24px 20px;
}

.auto-generator-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
}

.auto-generator-title {
  font-family: var(--font-body);
  font-size: 20px;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
}

.back-btn {
  font-family: var(--font-body);
  font-size: 12px;
  font-weight: 600;
  color: var(--text-secondary);
  background: var(--bg-card);
  border: 1px solid var(--border-light);
  border-radius: var(--radius-sm);
  padding: 6px 14px;
  transition: var(--transition);
  cursor: pointer;
}

.back-btn:hover {
  border-color: var(--accent-2);
  color: var(--accent-2);
}

.settings-card {
  background: var(--bg-card);
  border: 1px solid var(--border-light);
  border-radius: var(--radius);
  margin-bottom: 16px;
}

.settings-card .card-header {
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border-light);
  font-family: var(--font-body);
  font-size: 12px;
  font-weight: 700;
  color: var(--accent-2);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.ag-label {
  font-family: var(--font-body);
  font-size: 10px;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin-bottom: 4px;
  display: block;
}

.ag-input {
  font-family: var(--font-body);
  font-size: 12px;
  background: var(--bg-secondary);
  border: 1px solid var(--border-light);
  color: var(--text-primary);
  border-radius: var(--radius-sm);
  padding: 8px 10px;
  transition: var(--transition);
}

.ag-input:focus {
  border-color: var(--accent-2);
  box-shadow: none;
}

.ag-section-title {
  font-family: var(--font-body);
  font-size: 11px;
  font-weight: 700;
  color: var(--accent-2);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin: 16px 0 10px;
  padding-bottom: 6px;
  border-bottom: 1px solid var(--border-light);
}

.ag-section-title:first-child {
  margin-top: 0;
}

.ag-instructions-preview {
  font-family: var(--font-body);
  font-size: 12px;
  line-height: 1.5;
  white-space: pre-wrap;
  color: var(--text-secondary);
  background: var(--bg-secondary);
  border: 1px solid var(--border-light);
  border-radius: var(--radius-sm);
  padding: 10px 12px;
  max-height: 200px;
  overflow-y: auto;
}

.ag-logo-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.ag-logo-preview {
  max-height: 60px;
  max-width: 140px;
  border: 1px solid var(--border-light);
  border-radius: var(--radius-sm);
  background: #fff;
  padding: 2px;
  object-fit: contain;
}

.ag-remove-btn {
  color: #d9534f;
  border-color: #d9534f;
}

.ag-remove-btn:hover {
  background: #d9534f;
  color: #fff;
  border-color: #d9534f;
}

.load-bar {
  text-align: center;
  margin-bottom: 16px;
}

.ag-load-btn {
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 600;
  background: var(--accent-2);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  padding: 8px 20px;
  transition: var(--transition);
}

.ag-load-btn:hover:not(:disabled) {
  filter: brightness(1.15);
}

.ag-load-btn:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}

.questions-card {
  background: var(--bg-card);
  border: 1px solid var(--border-light);
  border-radius: var(--radius);
  margin-bottom: 16px;
}

.questions-card .card-header {
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border-light);
}

.badge-count {
  font-family: var(--font-body);
  font-size: 11px;
  font-weight: 700;
  color: var(--text-primary);
}

.badge-selected {
  font-family: var(--font-body);
  font-size: 11px;
  font-weight: 600;
  color: var(--accent-2);
}

.ag-outline-btn {
  font-family: var(--font-body);
  font-size: 10px;
  font-weight: 600;
  color: var(--text-secondary);
  background: var(--bg-card);
  border: 1px solid var(--border-light);
  border-radius: var(--radius-sm);
  padding: 4px 10px;
  transition: var(--transition);
}

.ag-outline-btn:hover {
  border-color: var(--accent-2);
  color: var(--accent-2);
}

.ag-questions-body {
  padding: 8px;
  max-height: 480px;
  overflow-y: auto;
}

.ag-mcq-row {
  display: flex;
  gap: 10px;
  padding: 10px 8px;
  border: 1px solid transparent;
  border-radius: 6px;
  cursor: pointer;
  transition: var(--transition);
  margin-bottom: 4px;
}

.ag-mcq-row:hover {
  background: var(--bg-hover);
}

.ag-mcq-row.selected {
  border-color: var(--accent-2);
  background: rgba(125, 207, 255, 0.06);
}

.ag-checkbox {
  flex-shrink: 0;
  padding-top: 2px;
}

.ag-checkbox-box {
  width: 20px;
  height: 20px;
  border: 2px solid var(--border);
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  color: #fff;
  transition: var(--transition);
}

.ag-checkbox-box.checked {
  background: var(--accent-2);
  border-color: var(--accent-2);
}

.ag-mcq-content {
  flex: 1;
  min-width: 0;
}

.ag-mcq-stem {
  font-family: var(--paper-font-family, 'Arial', sans-serif);
  font-size: 12pt;
  color: var(--text-primary);
  margin-bottom: 4px;
  line-height: 1.5;
}

.ag-mcq-stem-row {
  display: flex;
  align-items: flex-start;
  gap: 8px;
}

.ag-mcq-stem-img {
  max-height: 40px;
  max-width: 120px;
  object-fit: contain;
  border: 1px solid var(--border-light);
  border-radius: 4px;
  flex-shrink: 0;
}

.ag-mcq-options-preview {
  display: flex;
  flex-wrap: wrap;
  gap: 6px 14px;
  margin-top: 2px;
}

.ag-mcq-opt-chip {
  font-family: var(--font-body);
  font-size: 10px;
  color: var(--text-secondary);
}


.generate-bar {
  text-align: center;
  padding: 8px 0;
}

.ag-generate-btn {
  font-family: var(--font-body);
  font-size: 14px;
  font-weight: 700;
  background: var(--accent-3);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  padding: 10px 28px;
  transition: var(--transition);
}

.ag-generate-btn:hover:not(:disabled) {
  filter: brightness(1.15);
}

.ag-generate-btn:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}

.generate-hint {
  display: block;
  font-family: var(--font-body);
  font-size: 11px;
  color: var(--text-muted);
  margin-top: 6px;
}
</style>
