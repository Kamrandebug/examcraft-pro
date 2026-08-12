<template>
  <div class="auto-preview">
    <div class="ap-action-bar">
      <button class="btn btn-sm ap-action-btn" @click="uiStore.setView('auto')">
        <i class="fa fa-arrow-left"></i> Edit Selection
      </button>
      <div class="ap-action-right">
        <button class="btn btn-sm ap-action-btn" @click="printPaper">
          <i class="fa fa-print"></i> Print Paper
        </button>
        <button class="btn btn-sm ap-action-btn" @click="goHome">
          <i class="fa fa-home"></i> Home
        </button>
      </div>
    </div>

    <div class="ap-scroll">
      <div class="ap-paper-sheet">
        <div class="ap-header">
          <h2 class="ap-school-name">{{ autoPaperStore.schoolName }}</h2>
          <h4 class="ap-exam-label">Examination Paper</h4>
          <div class="ap-meta-line">
            <span>Grade: {{ autoPaperStore.grade }}</span>
            <span class="ap-meta-sep">|</span>
            <span>Subject: {{ autoPaperStore.subject }}</span>
          </div>
          <div class="ap-meta-line">
            <span>Date: {{ autoPaperStore.paperDate }}</span>
            <span class="ap-meta-sep">|</span>
            <span>Total Marks: {{ autoPaperStore.totalMarks }}</span>
          </div>
          <hr class="ap-hr" />
          <h3 class="ap-paper-title">{{ autoPaperStore.paperTitle }}</h3>
          <hr class="ap-hr" />
        </div>

        <div class="ap-section-header">
          Section A &mdash; Multiple Choice
        </div>
        <div class="ap-section-subtitle">
          (Each question carries 1 mark)
        </div>

        <div class="ap-questions">
          <div class="ap-q-item" v-for="(mcq, idx) in autoPaperStore.selectedMcqs" :key="mcq.id">
            <div class="ap-q-stem">
              <b>{{ idx + 1 }}.</b> {{ mcq.question }}
            </div>
            <div class="ap-q-options">
              <div class="ap-q-opt" v-for="opt in mcq.options" :key="opt.label">
                <b>{{ opt.label }}.</b> {{ opt.option_text }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useUiStore } from '../stores/uiStore';
import { useAutoPaperStore } from '../stores/autoPaperStore';

const uiStore = useUiStore();
const autoPaperStore = useAutoPaperStore();

function printPaper() {
  window.print();
}

function goHome() {
  autoPaperStore.reset();
  uiStore.setView('home');
}
</script>

<style scoped>
.auto-preview {
  height: 100%;
  display: flex;
  flex-direction: column;
  background: var(--bg-primary);
}

.ap-action-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 20px;
  background: var(--bg-card);
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}

.ap-action-right {
  display: flex;
  gap: 8px;
}

.ap-action-btn {
  font-family: var(--font-body);
  font-size: 12px;
  font-weight: 600;
  color: var(--text-secondary);
  background: var(--bg-secondary);
  border: 1px solid var(--border-light);
  border-radius: var(--radius-sm);
  padding: 6px 14px;
  transition: var(--transition);
  cursor: pointer;
}

.ap-action-btn:hover {
  border-color: var(--accent-2);
  color: var(--accent-2);
}

.ap-scroll {
  flex: 1;
  overflow-y: auto;
  padding: 24px;
  display: flex;
  justify-content: center;
}

.ap-paper-sheet {
  width: 210mm;
  min-height: 297mm;
  background: #fff;
  box-shadow: var(--shadow-lg);
  border-radius: 2px;
  padding: 40px 48px;
  font-family: var(--paper-font-family, 'Arial', sans-serif);
  color: #111;
  box-sizing: border-box;
}

.ap-school-name {
  text-align: center;
  font-size: 16pt;
  font-weight: 700;
  margin-bottom: 2px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.ap-exam-label {
  text-align: center;
  font-size: 12pt;
  font-weight: 400;
  color: #444;
  margin-bottom: 14px;
}

.ap-meta-line {
  text-align: center;
  font-size: 10pt;
  color: #333;
  margin-bottom: 2px;
}

.ap-meta-sep {
  margin: 0 8px;
  color: #999;
}

.ap-hr {
  border: none;
  border-top: 1px solid #333;
  margin: 14px 0;
}

.ap-paper-title {
  text-align: center;
  font-size: 14pt;
  font-weight: 700;
  margin: 0;
}

.ap-section-header {
  font-size: 12pt;
  font-weight: 700;
  margin-top: 20px;
  margin-bottom: 2px;
}

.ap-section-subtitle {
  font-size: 10pt;
  font-style: italic;
  color: #555;
  margin-bottom: 16px;
}

.ap-questions {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.ap-q-item {
  page-break-inside: avoid;
}

.ap-q-stem {
  font-size: var(--paper-q-font-size, 11pt);
  font-weight: var(--paper-q-font-weight, 400);
  margin-bottom: 4px;
  line-height: var(--paper-line-height, 1.5);
}

.ap-q-opt {
  font-size: var(--paper-opt-font-size, 10pt);
  margin-left: 20px;
  margin-bottom: 1px;
  line-height: 1.6;
}

.ap-q-opt b {
  margin-right: 6px;
}
</style>

<style>
@media print {
  .ap-action-bar {
    display: none !important;
  }
  body {
    background: white !important;
  }
  .auto-preview {
    background: white !important;
  }
  .ap-paper-sheet {
    box-shadow: none !important;
    border: none !important;
    margin: 0 !important;
    padding: 20px 24px !important;
  }
}
</style>
