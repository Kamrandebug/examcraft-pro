<template>
  <div class="auto-generator">
    <div class="auto-generator-inner">
      <div class="auto-generator-header">
        <button class="btn btn-sm back-btn" @click="uiStore.setView('home')">
          <i class="fa fa-arrow-left"></i> Back to Home
        </button>
        <h3 class="auto-generator-title">Auto Paper Generator</h3>
      </div>

      <div class="card settings-card">
        <div class="card-header">
          <h5 class="card-title mb-0">Paper Settings</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="ag-label">Paper Title</label>
              <input type="text" class="form-control ag-input" v-model="paperTitle" placeholder="e.g. Mid-Term Examination 2025" />
            </div>
            <div class="col-md-6">
              <label class="ag-label">School Name</label>
              <input type="text" class="form-control ag-input" v-model="schoolName" placeholder="e.g. The City School" />
            </div>
            <div class="col-md-4">
              <label class="ag-label">Date</label>
              <input type="date" class="form-control ag-input" v-model="paperDate" />
            </div>
            <div class="col-md-4">
              <label class="ag-label">Grade</label>
              <select class="form-select ag-input" v-model="selectedGrade" @change="onGradeChange">
                <option value="" disabled>Select grade</option>
                <option v-for="g in gradeList" :key="g" :value="g">{{ g }}</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="ag-label">Subject</label>
              <select class="form-select ag-input" v-model="selectedSubject" :disabled="!selectedGrade" @change="onSubjectChange">
                <option value="" disabled>Select subject</option>
                <option v-for="s in availableSubjects" :key="s" :value="s">{{ s }}</option>
              </select>
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
              <div class="ag-mcq-stem" v-html="q.question"></div>
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
import { ref, computed, watch } from 'vue';
import { useUiStore } from '../stores/uiStore';
import { useAutoPaperStore } from '../stores/autoPaperStore';

const uiStore = useUiStore();

const gradeSubjects = {
  'O Level': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'English Language', 'Urdu', 'Islamiyat', 'Pakistan Studies', 'Economics', 'Commerce', 'Accounting'],
  'A Level': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Further Mathematics', 'Computer Science', 'Economics', 'Psychology'],
  '8th Grade': ['General Science', 'Mathematics', 'Urdu', 'English', 'Social Studies', 'Islamiyat', 'Pakistan Studies'],
  '9th Grade': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'Urdu', 'English', 'Islamiyat', 'Pakistan Studies'],
  '10th Grade': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'Urdu', 'English', 'Islamiyat', 'Pakistan Studies'],
};

const gradeList = Object.keys(gradeSubjects);

const paperTitle = ref('');
const schoolName = ref('');
const paperDate = ref('');
const selectedGrade = ref('');
const selectedSubject = ref('');

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

function onGradeChange() {}
function onSubjectChange() {}

async function loadMcqs() {
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
  const autoPaperStore = useAutoPaperStore();
  autoPaperStore.setPaperMeta({
    title: paperTitle.value.trim(),
    school: schoolName.value.trim(),
    date: paperDate.value,
    grade: selectedGrade.value,
    subject: selectedSubject.value,
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
