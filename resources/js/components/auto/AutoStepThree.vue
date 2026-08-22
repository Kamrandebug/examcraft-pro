<template>
  <div>
    <!-- Summary info box -->
    <div class="step-summary-box">
      <strong>Grade:</strong> {{ store.grade || '—' }} &nbsp;&nbsp;|&nbsp;&nbsp;
      <strong>Subject:</strong> {{ store.subject || '—' }}
    </div>

    <!-- Load MCQs button -->
    <button
      class="btn load-mcqs-btn w-100"
      :disabled="store.mcqLoading"
      @click="store.loadMcqs()"
    >
      <span v-if="store.mcqLoading" class="spinner-border spinner-border-sm me-2" role="status"></span>
      <span v-else>📚</span>
      {{ store.mcqLoading ? 'Loading MCQs...' : 'Load MCQs' }}
    </button>

    <!-- Error alert -->
    <div v-if="store.mcqError" class="alert alert-danger mt-3">
      {{ store.mcqError }}
    </div>

    <!-- Loading state -->
    <div v-if="store.mcqLoading" class="text-center py-4">
      <div class="spinner-border text-warning" role="status"></div>
      <p class="mt-2 text-muted">Loading MCQs…</p>
    </div>

    <!-- MCQ List (only visible after loading and has items) -->
    <template v-else-if="store.mcqList.length > 0">
      <div class="mcq-result-header">
        <span class="mcq-result-count">
          {{ store.mcqList.length }} MCQs found for {{ store.grade }} — {{ store.subject }}
        </span>
        <div class="mcq-select-actions">
          <button class="btn btn-sm btn-outline-select" @click="store.selectAllMcqs()">
            Select All ({{ store.mcqList.length }})
          </button>
          <button class="btn btn-sm btn-outline-clear" @click="store.clearMcqSelection()">
            Clear
          </button>
        </div>
      </div>

      <div class="mcq-list">
        <div
          v-for="(question, index) in store.mcqList"
          :key="question.id"
          class="mcq-item"
          :class="{ selected: store.selectedMcqIds.includes(question.id) }"
          @click="store.toggleMcq(question.id)"
        >
          <!-- Checkbox -->
          <input
            type="checkbox"
            class="mcq-checkbox"
            :checked="store.selectedMcqIds.includes(question.id)"
            @click.stop="store.toggleMcq(question.id)"
          />

          <!-- Body -->
          <div class="mcq-body">
            <!-- Stem image thumbnail (if exists) -->
            <img
              v-if="question.stem_image"
              :src="question.stem_image"
              class="mcq-stem-img"
              alt=""
            />

            <!-- Q number + stem text -->
            <div class="mcq-stem">
              <span style="color:#C9A84C;font-weight:700;margin-right:4px;">
                Q{{ index + 1 }}.
              </span>
              {{ question.stem_text || '(No stem text)' }}
            </div>

            <!-- Options grid -->
            <div class="mcq-options">
              <div
                v-for="(opt, optIdx) in (question.options || []).slice(0, 4)"
                :key="optIdx"
                class="mcq-opt"
              >
                <span class="mcq-opt-label">
                  {{ ['A','B','C','D'][optIdx] }}.
                </span>
                <img
                  v-if="opt.image"
                  :src="opt.image"
                  class="mcq-opt-img"
                  alt=""
                />
                <span v-if="opt.text">{{ opt.text }}</span>
              </div>
            </div>
          </div>

          <!-- Correct answer badge -->
          <div
            v-if="question.correct_answer"
            class="mcq-correct-badge"
          >
            ✓ {{ question.correct_answer }}
          </div>
        </div>
      </div>
    </template>

    <!-- Empty state after load -->
    <div
      v-else-if="!store.mcqLoading && store.mcqList.length === 0 && store.mcqError === null && hasAttemptedLoad"
      class="alert alert-warning mt-3"
    >
      No MCQs found for {{ store.grade }} – {{ store.subject }}.
      Ask your admin to add questions to the question bank.
    </div>

    <!-- Sticky bottom action bar -->
    <div v-if="store.selectedMcqIds.length > 0" class="mcq-action-bar">
      <span class="mcq-action-count">{{ store.selectedMcqIds.length }} question{{ store.selectedMcqIds.length !== 1 ? 's' : '' }} selected</span>

      <button
        v-if="!store.isEditMode"
        class="btn mcq-generate-btn"
        @click="generatePaper"
      >
        🎓 Generate Paper
      </button>

      <button
        v-else
        class="btn mcq-update-btn"
        @click="handleUpdate"
      >
        💾 Update Paper
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAutoPaperStore } from '../../stores/autoPaperStore';
import { useUiStore } from '../../stores/uiStore';

const store = useAutoPaperStore();
const uiStore = useUiStore();

const hasAttemptedLoad = ref(false);

// Watch for mcqList changes to track if a load has been attempted
// We set hasAttemptedLoad to true once mcqLoading goes from true to false
const unwatch = computed(() => {
  // This is just for template access; we use a different approach
  return store.mcqList;
});

function optionLetter(idx) {
  return ['A', 'B', 'C', 'D'][idx] || '';
}

function generatePaper() {
  // Map selected IDs to actual MCQ objects from mcqList
  const selectedMcqs = store.mcqList.filter(q => store.selectedMcqIds.includes(q.id));
  store.setSelectedMcqs(selectedMcqs);
  uiStore.setView('auto-preview');
}

async function handleUpdate() {
  // Sync selectedMcqs objects from selectedMcqIds
  store.setSelectedMcqs(
    store.mcqList.filter(q => store.selectedMcqIds.includes(q.id))
  );
  const success = await store.updatePaper();
  if (success) {
    window.location.href = '/user/dashboard';
  } else {
    alert('Update failed. Please try again.');
  }
}

// Track whether a load has been attempted
import { watch } from 'vue';
watch(() => store.mcqLoading, (loading, wasLoading) => {
  if (wasLoading && !loading) {
    hasAttemptedLoad.value = true;
  }
});
</script>

<style scoped>
.step-summary-box {
  background: rgba(201, 168, 76, 0.08);
  border: 1px solid rgba(201, 168, 76, 0.3);
  border-radius: 8px;
  padding: 12px 16px;
  color: #1B2A4A;
  margin-bottom: 16px;
  font-size: 14px;
}

.load-mcqs-btn {
  background: #C9A84C;
  color: #1B2A4A;
  font-weight: 700;
  border-radius: 8px;
  padding: 12px;
  border: none;
  font-size: 15px;
  margin-bottom: 16px;
  transition: filter 0.2s ease;
}

.load-mcqs-btn:hover:not(:disabled) {
  filter: brightness(1.1);
}

.load-mcqs-btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.mcq-error-alert {
  background: rgba(139, 26, 26, 0.08);
  border-left: 4px solid #8B1A1A;
  color: #8B1A1A;
  padding: 12px 16px;
  border-radius: 4px;
  margin-bottom: 16px;
  font-size: 14px;
}

.mcq-result-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.mcq-result-count {
  font-size: 13px;
  color: #5A6070;
}

.mcq-select-actions {
  display: flex;
  gap: 8px;
}

.btn-outline-select {
  font-size: 12px;
  font-weight: 600;
  color: #1B2A4A;
  border: 1px solid #C9A84C;
  border-radius: 6px;
  padding: 4px 12px;
  background: transparent;
  transition: all 0.2s ease;
}

.btn-outline-select:hover {
  background: #C9A84C;
  color: #1B2A4A;
}

.btn-outline-clear {
  font-size: 12px;
  font-weight: 600;
  color: #5A6070;
  border: 1px solid #DDD8CC;
  border-radius: 6px;
  padding: 4px 12px;
  background: transparent;
  transition: all 0.2s ease;
}

.btn-outline-clear:hover {
  background: #DDD8CC;
  color: #1B2A4A;
}

/* MCQ list container */
.mcq-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-height: 55vh;
  overflow-y: auto;
  padding-right: 4px;
}

/* Individual MCQ card */
.mcq-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 10px 12px;
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  background: rgba(255,255,255,0.04);
  cursor: pointer;
  transition: border-color 0.15s;
  min-height: unset;    /* remove any min-height */
  height: auto;         /* let content determine height */
}

.mcq-item:hover {
  border-color: rgba(201, 168, 76, 0.5);
}

.mcq-item.selected {
  border-color: #C9A84C;
  background: rgba(201, 168, 76, 0.08);
}

/* Checkbox column */
.mcq-checkbox {
  flex-shrink: 0;
  margin-top: 2px;
  width: 16px;
  height: 16px;
  accent-color: #C9A84C;
}

/* Content column */
.mcq-body {
  flex: 1;
  min-width: 0;         /* allow text truncation */
}

/* Question number + stem */
.mcq-stem {
  font-size: 0.84rem;
  font-weight: 500;
  color: #e2e8f0;
  line-height: 1.4;
  /* show max 2 lines then ellipsis */
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin-bottom: 6px;
}

/* Stem image thumbnail */
.mcq-stem-img {
  width: 56px !important;
  height: 56px !important;
  max-width: 56px !important;
  max-height: 56px !important;
  object-fit: contain;
  float: right;
  margin-left: 8px;
  border-radius: 4px;
}

/* Options grid */
.mcq-options {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2px 12px;
}

.mcq-opt {
  font-size: 0.75rem;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 4px;
  /* single line truncation */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mcq-opt-label {
  font-weight: 600;
  color: #C9A84C;
  flex-shrink: 0;
}

/* Option image thumbnail */
.mcq-opt-img {
  width: 36px !important;
  height: 36px !important;
  max-width: 36px !important;
  max-height: 36px !important;
  object-fit: contain;
  flex-shrink: 0;
}

/* Correct answer badge */
.mcq-correct-badge {
  flex-shrink: 0;
  align-self: flex-start;
  font-size: 0.7rem;
  padding: 2px 6px;
  border-radius: 10px;
  background: rgba(34, 197, 94, 0.15);
  color: #4ade80;
  border: 1px solid rgba(34, 197, 94, 0.3);
  white-space: nowrap;
}

.mcq-action-bar {
  position: sticky;
  bottom: 0;
  background: #1B2A4A;
  padding: 12px 16px;
  border-radius: 8px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 16px;
  z-index: 10;
}

.mcq-action-count {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.9rem;
}

.mcq-generate-btn {
  background: #C9A84C;
  color: #1B2A4A;
  font-weight: 700;
  border: none;
  border-radius: 6px;
  padding: 8px 20px;
  font-size: 14px;
  transition: filter 0.2s ease;
}

.mcq-generate-btn:hover {
  filter: brightness(1.1);
}

.mcq-update-btn {
  background: #28a745;
  color: #fff;
  font-weight: 700;
  border: none;
  border-radius: 6px;
  padding: 8px 20px;
  font-size: 14px;
  transition: filter 0.2s ease;
}

.mcq-update-btn:hover {
  filter: brightness(1.1);
}

.mcq-empty-state {
  text-align: center;
  color: #5A6070;
  padding: 40px;
  font-size: 14px;
}
</style>