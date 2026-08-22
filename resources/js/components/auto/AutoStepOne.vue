<template>
  <div class="step-card">
    <div class="step-card-header">
      <span class="step-card-icon">📋</span>
      <h5 class="step-card-title">Paper Info</h5>
    </div>
    <div class="step-card-body">
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
          <label class="ag-label">Grade <span class="text-danger">*</span></label>
          <select
            class="form-select ag-input"
            :class="{ 'is-invalid': showErrors && !grade.trim() }"
            v-model="grade"
            @change="onGradeChange"
          >
            <option value="" disabled>Select grade</option>
            <option v-for="g in gradeList" :key="g" :value="g">{{ g }}</option>
          </select>
          <div v-if="showErrors && !grade.trim()" class="ag-error-text">This field is required</div>
        </div>
        <div class="col-md-6">
          <label class="ag-label">Subject <span class="text-danger">*</span></label>
          <select
            class="form-select ag-input"
            :class="{ 'is-invalid': showErrors && !subject.trim() }"
            v-model="subject"
            :disabled="!grade"
          >
            <option value="" disabled>Select subject</option>
            <option v-for="s in availableSubjects" :key="s" :value="s">{{ s }}</option>
          </select>
          <div v-if="showErrors && !subject.trim()" class="ag-error-text">This field is required</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useAutoPaperStore } from '../../stores/autoPaperStore';

const autoPaperStore = useAutoPaperStore();

const gradeSubjects = {
  'O Level': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'English Language', 'Urdu', 'Islamiyat', 'Pakistan Studies', 'Economics', 'Commerce', 'Accounting'],
  'A Level': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Further Mathematics', 'Computer Science', 'Economics', 'Psychology'],
  '8th Grade': ['General Science', 'Mathematics', 'Urdu', 'English', 'Social Studies', 'Islamiyat', 'Pakistan Studies'],
  '9th Grade': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'Urdu', 'English', 'Islamiyat', 'Pakistan Studies'],
  '10th Grade': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'Urdu', 'English', 'Islamiyat', 'Pakistan Studies'],
};

const gradeList = Object.keys(gradeSubjects);

// Local refs bound to store
const paperTitle = ref(autoPaperStore.paperTitle);
const paperCode = ref(autoPaperStore.paperCode);
const session = ref(autoPaperStore.session);
const duration = ref(autoPaperStore.duration);
const schoolName = ref(autoPaperStore.schoolName);
const paperDate = ref(autoPaperStore.paperDate);
const grade = ref(autoPaperStore.grade);
const subject = ref(autoPaperStore.subject);

const showErrors = ref(false);

const availableSubjects = computed(() => {
  return grade.value ? (gradeSubjects[grade.value] || []) : [];
});

// Sync local refs → store
watch([paperTitle, schoolName, paperDate, grade, subject, paperCode, session, duration], () => {
  autoPaperStore.setPaperMeta({
    title: paperTitle.value,
    school: schoolName.value,
    date: paperDate.value,
    grade: grade.value,
    subject: subject.value,
    paperCode: paperCode.value,
    session: session.value,
    duration: duration.value,
  });
});

function onGradeChange() {
  subject.value = '';
}

// Expose validate for parent
defineExpose({ validate: () => {
  showErrors.value = true;
  return autoPaperStore.isStep1Valid;
}});
</script>

<style scoped>
.step-card {
  background: var(--bg-card);
  border: 1px solid var(--border-light);
  border-top: 3px solid #C9A84C;
  border-radius: 8px;
}

.step-card-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-light);
  background: #1B2A4A;
  border-radius: 8px 8px 0 0;
}

.step-card-icon {
  font-size: 18px;
}

.step-card-title {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  font-weight: 700;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin: 0;
}

.step-card-body {
  padding: 20px;
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
  border-color: #C9A84C;
  box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.14);
}

.ag-input.is-invalid {
  border-color: #dc2626 !important;
}

.ag-error-text {
  font-family: var(--font-body);
  font-size: 11px;
  color: #dc2626;
  margin-top: 4px;
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

.text-danger {
  color: #dc2626 !important;
}
</style>