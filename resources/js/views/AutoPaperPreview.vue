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
          <div class="ap-materials-row">
            <span class="ap-materials-label">Additional Materials:</span>
            <ul class="ap-materials-list">
              <li v-for="(m, i) in materialsLines" :key="i">{{ m }}</li>
            </ul>
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
                <span class="ap-q-number">{{ idx + 1 }}</span><span v-html="cleanText(mcq.stem_text)"></span>
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
                  <span class="ap-q-opt-text" v-html="cleanText(opt.text)"></span>
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

function cleanText(html) {
  if (!html) return '';
  const div = document.createElement('div');
  div.innerHTML = html;
  div.querySelectorAll('a').forEach((a) => {
    a.replaceWith(document.createTextNode(a.textContent));
  });
  div.querySelectorAll('[style]').forEach((el) => {
    el.removeAttribute('style');
  });
  return div.innerHTML;
}

const instructionsHtml = computed(() => {
  const text = cleanText(autoPaperStore.instructions || '');
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

    // Determine API endpoint based on admin context
    const baseUrl = window.adminTargetUserId
      ? `/api/admin/users/${window.adminTargetUserId}/papers`
      : '/api/user/papers';

    if (savedPaperId.value) {
      await window.axios.put(`${baseUrl}/${savedPaperId.value}`, payload);
    } else {
      const { data } = await window.axios.post(baseUrl, payload);
      savedPaperId.value = data.paper.id;
    }

    showToast('Paper saved successfully!', 'success');

    // Redirect to admin papers section if in admin context
    if (window.adminTargetUserId) {
      setTimeout(() => {
        window.location.href = `/admin/users/${window.adminTargetUserId}/papers`;
      }, 800);
    }
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
  font-size: 10pt;
  color: #000;
  box-sizing: border-box;
  line-height: 1.5;
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
.ap-materials-row {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin: 8px 0;
  font-size: 10pt;
}

.ap-materials-label {
  font-weight: bold;
  white-space: nowrap;
  flex-shrink: 0;
}

.ap-materials-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.ap-materials-list li {
  line-height: 1.6;
  color: #000;
}

/* ---- Row 6/7: instructions ---- */
.ap-instructions-heading {
  text-align: center;
  font-weight: bold;
  text-decoration: none;
  margin: 12px 0 8px;
  font-size: 10.5pt;
  color: #000;
}

.ap-instructions-body {
  white-space: normal;
  font-size: 10.5pt;
}

.ap-instructions-heading,
.ap-instructions-body,
.ap-instructions-body * {
  color: #000 !important;
  text-decoration: none !important;
}

.ap-instructions-heading strong,
.ap-instructions-heading b,
.ap-instructions-body strong,
.ap-instructions-body b {
  color: #000 !important;
  font-weight: bold !important;
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
  /* flex gap on .ap-questions already spaces items; kill the redundant margin */
  margin-bottom: 0 !important;
  font-size: 10pt;
  line-height: 1.5;
  color: #000;
  page-break-inside: avoid;
}

.ap-q-stem {
  font-size: var(--paper-q-font-size, 10pt);
  font-weight: var(--paper-q-font-weight, 400);
  margin-bottom: 4px;
  line-height: var(--paper-line-height, 1.5);
}

.ap-q-number {
  font-weight: bold;
  margin-right: 4px;
}

.ap-q-stem-img {
  max-width: 200px;
  max-height: 150px;
  display: block;
  margin: 4px 0;
  border: 1px solid #ddd;
  filter: none;
}

.ap-q-options {
  margin-top: 8px !important;
  padding: 0 !important;
}

/* Option row — [label] [text] [image] on one flex row. Row height is driven
   by content only (the image height sets it), no artificial inflation. */
.ap-q-opt,
.ap-q-opt--with-img {
  display: flex !important;
  align-items: center !important;   /* vertically center label+text with image */
  gap: 10px !important;
  padding: 3px 0 !important;        /* minimal top/bottom padding only */
  margin: 0 0 6px 0 !important;     /* 6px gap between option rows */
  min-height: unset !important;
  height: auto !important;
}

.ap-q-opt-label {
  font-weight: bold !important;
  min-width: 18px !important;
  flex-shrink: 0 !important;
  align-self: center !important;
  padding: 0 !important;
  margin: 0 !important;
}

.ap-q-opt-text {
  flex: 1 !important;
  line-height: 1.5 !important;
  align-self: center !important;
  padding: 0 !important;
  margin: 0 !important;
}

.ap-q-opt-img {
  max-height: 75px !important;    /* readable size — was wrongly reduced to 32px */
  max-width: 110px !important;
  width: auto !important;
  height: auto !important;
  object-fit: contain !important;
  flex-shrink: 0 !important;
  border: none !important;
  box-shadow: none !important;
  outline: none !important;
  background: transparent !important;
  padding: 0 !important;
  margin: 0 !important;
  display: block !important;
}

/* Force plain black text on all question and option content. */
.ap-q-stem,
.ap-q-stem * {
  color: #000 !important;
  text-decoration: none !important;
  font-style: normal !important;
}

.ap-q-opt,
.ap-q-opt * {
  color: #000 !important;
  text-decoration: none !important;
}
</style>
