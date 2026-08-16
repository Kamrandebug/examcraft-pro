<template>
  <div class="auto-preview">
    <div class="ap-action-bar">
      <button class="btn btn-sm ap-action-btn" @click="uiStore.setView('auto')">
        <i class="fa fa-arrow-left"></i> Edit Selection
      </button>
      <div class="ap-action-right">
        <button class="btn btn-sm ap-action-btn" @click="savePaper" :disabled="isSaving">
          <i class="fa" :class="isSaving ? 'fa-spinner fa-spin' : (savedPaperId ? 'fa-check' : 'fa-save')"></i>
          {{ isSaving ? 'Saving...' : (savedPaperId ? 'Saved ✓' : 'Save to My Papers') }}
        </button>
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
          <!-- Row 1: logo + org/title -->
          <div class="ap-head-row">
            <div v-if="autoPaperStore.logoDataUrl" class="ap-head-logo">
              <img :src="autoPaperStore.logoDataUrl" class="ap-logo-img" alt="Logo" />
            </div>
            <div class="ap-head-org">
              <div class="ap-org-name">{{ autoPaperStore.schoolName }}</div>
              <div class="ap-org-sub">Cambridge Ordinary Level</div>
            </div>
          </div>

          <!-- Row 2: rule -->
          <hr class="ap-hr" />

          <!-- Row 3: subject/code/session -->
          <div class="ap-subject-row">
            <div class="ap-subject-left">
              <div class="ap-subject-name">{{ autoPaperStore.subject }}</div>
              <div class="ap-subject-paper">Paper 1 Multiple Choice</div>
            </div>
            <div class="ap-subject-right">
              <div class="ap-code">{{ autoPaperStore.paperCode }}</div>
              <div class="ap-code-line">{{ autoPaperStore.session }}</div>
              <div class="ap-code-line">{{ autoPaperStore.duration }}</div>
            </div>
          </div>

          <!-- Row 4: additional materials -->
          <div class="ap-materials">
            <div class="ap-materials-label">Additional Materials:</div>
            <div class="ap-materials-list">
              <div v-for="(line, i) in materialsLines" :key="i" class="ap-materials-item">{{ line }}</div>
            </div>
          </div>

          <!-- Row 5: rule -->
          <hr class="ap-hr" />

          <!-- Row 6: instructions heading -->
          <div class="ap-instructions-heading">READ THESE INSTRUCTIONS FIRST</div>

          <!-- Row 7: instructions body -->
          <div class="ap-instructions-body" v-html="instructionsHtml"></div>

          <!-- Row 8: rule -->
          <hr class="ap-hr" />

          <!-- Row 9: footer note -->
          <div class="ap-footer-note">
            <div class="ap-footer-pages">Answer all {{ autoPaperStore.selectedMcqs.length }} questions.</div>
            <div class="ap-footer-code">{{ autoPaperStore.paperCode }}&nbsp;&nbsp;{{ autoPaperStore.session }}</div>
          </div>
        </div>

        <div class="ap-section">
          <div class="ap-section-header">
            Section A
          </div>
          <div class="ap-section-intro">
            Answer all {{ autoPaperStore.selectedMcqs.length }} questions.
          </div>

          <div class="ap-questions">
            <div class="ap-q-item" v-for="(mcq, idx) in autoPaperStore.selectedMcqs" :key="mcq.id">
              <div class="ap-q-stem">
                <b>{{ idx + 1 }}</b>&nbsp;{{ mcq.stem_text }}
                <img
                  v-if="mcq.stem_image"
                  :src="mcq.stem_image"
                  class="ap-q-stem-img"
                  alt="Question image"
                />
              </div>
              <div class="ap-q-options">
                <div
                  class="ap-q-opt"
                  :class="{ 'ap-q-opt--with-img': opt.image }"
                  v-for="opt in mcq.options"
                  :key="opt.label"
                >
                  <b class="ap-q-opt-label">{{ opt.label }}</b>
                  <span class="ap-q-opt-text">{{ opt.text }}</span>
                  <img
                    v-if="opt.image"
                    :src="opt.image"
                    class="ap-q-opt-img"
                    alt="Option image"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useUiStore } from '../stores/uiStore';
import { useAutoPaperStore } from '../stores/autoPaperStore';
import { useToast } from '../composables/useToast';

const uiStore = useUiStore();
const autoPaperStore = useAutoPaperStore();
const { showToast } = useToast();

const isSaving = ref(false);
const savedPaperId = ref(window.initialPaperId || null);

const materialsLines = computed(() =>
  (autoPaperStore.additionalMaterials || '')
    .split('\n')
    .map((l) => l.trim())
    .filter((l) => l.length > 0)
);

const instructionsHtml = computed(() => {
  const text = autoPaperStore.instructions || '';
  const escaped = text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

  const keywords = [
    'all',
    'A',
    'B',
    'C',
    'D',
    'one',
    'soft pencil',
    'Answer Sheet very carefully',
    'correct',
    'wrong',
    'rough working',
    'booklet',
    'Electronic calculators',
  ];

  // Replace the hardcoded "forty" with the actual number of selected MCQs.
  const count = autoPaperStore.selectedMcqs.length;
  let html = escaped.replace(/\bforty\b/gi, String(count));
  keywords.forEach((kw) => {
    const re = new RegExp('\\b' + escapeRegExp(kw) + '\\b', 'g');
    html = html.replace(re, (match) => `<strong>${match}</strong>`);
  });

  return html.replace(/\n/g, '<br/>');
});

function escapeRegExp(str) {
  return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

async function savePaper() {
  isSaving.value = true;
  try {
    const payload = {
      title: autoPaperStore.paperTitle || 'Untitled Paper',
      type: 'auto',
      grade: autoPaperStore.grade || null,
      subject: autoPaperStore.subject || null,
      school_name: autoPaperStore.schoolName || null,
      exam_date: autoPaperStore.paperDate || null,
      status: 'draft',
      paper_data: {
        paperTitle: autoPaperStore.paperTitle,
        schoolName: autoPaperStore.schoolName,
        paperDate: autoPaperStore.paperDate,
        grade: autoPaperStore.grade,
        subject: autoPaperStore.subject,
        paperCode: autoPaperStore.paperCode,
        session: autoPaperStore.session,
        duration: autoPaperStore.duration,
        additionalMaterials: autoPaperStore.additionalMaterials,
        instructions: autoPaperStore.instructions,
        logoDataUrl: await shrinkLogoDataUrl(autoPaperStore.logoDataUrl),
        selectedMcqs: autoPaperStore.selectedMcqs,
      },
    };

    if (savedPaperId.value) {
      await window.axios.put(`/api/user/papers/${savedPaperId.value}`, payload);
    } else {
      const { data } = await window.axios.post('/api/user/papers', payload);
      savedPaperId.value = data.paper.id;
    }

    showToast('Paper saved to your dashboard!', 'success');
  } catch (err) {
    console.error('Save failed:', err);
    showToast('Failed to save paper. Please try again.', 'error');
  } finally {
    isSaving.value = false;
  }
}

/**
 * Re-encode an already-persisted logo data URL (which may be a large raw
 * base64 PNG from before the downscale fix) into a small JPEG. Returns the
 * URL unchanged if it is not an image or cannot be resized.
 */
function shrinkLogoDataUrl(dataUrl) {
  if (!dataUrl || !dataUrl.startsWith('data:image')) {
    return Promise.resolve(dataUrl);
  }
  return new Promise((resolve) => {
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

      try {
        resolve(canvas.toDataURL('image/jpeg', 0.85));
      } catch {
        resolve(canvas.toDataURL('image/png'));
      }
    };
    img.onerror = () => resolve(dataUrl);
    img.src = dataUrl;
  });
}

function printPaper() {
  window.print();
}

function goHome() {
  if (window.authUser?.role === 'admin') {
    autoPaperStore.reset();
    uiStore.setView('home');
  } else {
    window.location.href = '/user/dashboard';
  }
}
</script>

<style scoped>
.auto-preview {
  height: 100%;
  min-height: 0;
  grid-row: 1 / -1;
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
  min-height: 0;
  overflow-y: auto;
  padding: 24px;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}

.ap-paper-sheet {
  width: 210mm;
  min-height: 297mm;
  height: auto;
  overflow: visible;
  background: #fff;
  box-shadow: var(--shadow-lg);
  border-radius: 2px;
  padding: 20mm;
  font-family: 'Times New Roman', 'Times', 'Liberation Serif', serif;
  font-size: 11pt;
  color: #111;
  box-sizing: border-box;
  line-height: 1.45;
}

/* ---- Header: Row 1 — logo + org ---- */
.ap-head-row {
  display: flex;
  align-items: center;
  gap: 16px;
}

.ap-head-logo {
  flex-shrink: 0;
}

.ap-logo-img {
  max-height: 60px;
  max-width: 140px;
  object-fit: contain;
}

.ap-head-org {
  flex: 1;
}

.ap-org-name {
  font-size: 12pt;
  font-weight: 700;
  line-height: 1.3;
}

.ap-org-sub {
  font-size: 10.5pt;
  color: #222;
}

/* ---- Row 2 / 5 / 8 rules ---- */
.ap-hr {
  border: none;
  border-top: 1.5px solid #000;
  margin: 12px 0;
}

/* ---- Row 3: subject / code / session ---- */
.ap-subject-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.ap-subject-name {
  font-size: 13pt;
  font-weight: 700;
  text-transform: uppercase;
}

.ap-subject-paper {
  font-size: 11pt;
}

.ap-subject-right {
  text-align: right;
}

.ap-code {
  font-size: 13pt;
  font-weight: 700;
}

.ap-code-line {
  font-size: 11pt;
}

/* ---- Row 4: additional materials ---- */
.ap-materials {
  margin: 10px 0 0 20px;
}

.ap-materials-label {
  font-weight: 700;
}

.ap-materials-item {
  margin-left: 12px;
}

/* ---- Row 6/7: instructions ---- */
.ap-instructions-heading {
  font-size: 11pt;
  font-weight: 700;
  text-decoration: underline;
  text-align: center;
  margin-bottom: 8px;
}

.ap-instructions-body {
  white-space: normal;
  font-size: 11pt;
}

/* ---- Row 9: footer ---- */
.ap-footer-note {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 9pt;
  color: #333;
}

.ap-footer-code {
  white-space: pre;
}

/* ---- Questions ---- */
.ap-section {
  margin-top: 24px;
}

.ap-section-header {
  font-size: 12pt;
  font-weight: 700;
  border-top: 1px solid #000;
  padding-top: 8px;
  margin-bottom: 2px;
}

.ap-section-intro {
  font-size: 11pt;
  margin-bottom: 14px;
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

.ap-q-stem-img {
  max-width: 200px;
  max-height: 150px;
  display: block;
  margin: 8px 0 12px 0;
  border: 1px solid #ddd;
  filter: none;
}

.ap-q-options {
  margin-left: 20px;
}

/* Text-only option: keep the original inline layout. */
.ap-q-opt {
  font-size: var(--paper-opt-font-size, 10pt);
  margin-bottom: 1px;
  line-height: 1.6;
}

/* Option with an image: [label] [text] [thumbnail] on one flex row,
   constrained to the full width of the options container so the image
   never escapes the white paper boundary. */
.ap-q-opt--with-img {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
}

.ap-q-opt-label {
  white-space: nowrap;
}

.ap-q-opt-text {
  display: inline;
}

.ap-q-opt--with-img .ap-q-opt-text {
  flex: 1 1 auto;
  min-width: 0;
  overflow-wrap: anywhere;
}

.ap-q-opt-img {
  flex-shrink: 0;
  max-height: 60px;
  max-width: 120px;
  width: auto;
  height: auto;
  object-fit: contain;
  border: 1px solid #ddd;
  filter: none;
}
</style>
