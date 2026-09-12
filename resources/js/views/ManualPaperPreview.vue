<template>
  <div class="mp-preview-shell">

    <!-- Action bar (hidden on print) - fixed at top -->
    <div class="mp-action-bar no-print">
      <div class="d-flex gap-2 align-items-center">
        <button class="btn btn-sm btn-outline-secondary text-white border-secondary"
                @click="goBack">
          ← Back to My Papers
        </button>
        <span class="badge bg-primary ms-2">Manual Paper Preview</span>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-warning"
                @click="editPaper">
          ✏ Edit in Designer
        </button>
        <button class="btn btn-sm btn-warning fw-bold"
                @click="printPaper">
          🖨 Print / Export PDF
        </button>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="mp-loading">
      <div class="spinner-border text-warning"></div>
      <p class="mt-3 text-muted">Loading paper content…</p>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="mp-error">
      {{ error }}
    </div>

    <!-- A4 Paper Sheet - scrollable area -->
    <div v-else class="mp-paper-wrapper">
      <div class="mp-paper-sheet" id="manual-paper-print">

      <!-- PAPER HEADER (Cambridge style) -->
      <div class="mp-header">
        <div class="mp-header-top">
          <img v-if="meta.logoDataUrl || meta.logo" 
               :src="meta.logoDataUrl || meta.logo" 
               class="mp-logo" alt="Logo" />
          <div class="mp-school-name">
            {{ meta.organization || meta.schoolName || '' }}
          </div>
        </div>
        <hr class="mp-rule" />
        <div class="mp-header-meta">
          <div class="mp-subject-line">
            {{ meta.subject || meta.paperTitle }}
            <span v-if="meta.code || meta.paperCode"> {{ meta.code || meta.paperCode }}</span>
          </div>
          <div class="mp-meta-row">
            <span v-if="meta.session || meta.date">{{ meta.session || meta.date }}</span>
            <span v-if="meta.duration"> · {{ meta.duration }}</span>
          </div>
        </div>
        <hr class="mp-rule" />
      </div>

      <!-- PAGES → BLOCKS -->
      <div v-for="(page, pi) in pages" :key="page.id || pi" 
           class="mp-page">

        <!-- Page break between pages (except first) -->
        <div v-if="pi > 0" class="mp-page-break"></div>

        <!-- Render each block -->
        <div v-for="(block, bi) in page.blocks" 
             :key="block.id || bi" 
             class="mp-block">

          <!-- SECTION block -->
          <div v-if="block.type === 'section'" class="mp-section">
            <div class="mp-section-title">
              {{ block.title || '' }}
            </div>
            <div v-if="block.subtitle"
                 class="mp-section-sub">
              {{ block.subtitle }}
            </div>
            <hr v-if="block.showDivider !== false"
                class="mp-section-rule" />
          </div>

          <!-- TEXT block -->
          <div v-else-if="block.type === 'text'" class="mp-text"
               v-html="cleanHtml(block.content || '')">
          </div>

          <!-- DIVIDER block -->
          <hr v-else-if="block.type === 'divider'"
              class="mp-divider"
              :style="{ borderStyle: block.style || 'solid' }" />

          <!-- IMAGE block -->
          <div v-else-if="block.type === 'image'" class="mp-image-block">
            <img :src="block.src || ''"
                 :style="{ width: (block.width || 60) + '%' }"
                 class="mp-img" alt="" />
            <p v-if="block.caption"
               class="mp-img-caption">
              {{ block.caption }}
            </p>
          </div>

          <!-- TABLE block -->
          <div v-else-if="block.type === 'table'" class="mp-table-wrap">
            <table class="mp-table">
              <thead v-if="(block.headers || []).length">
                <tr>
                  <th v-for="(h, hi) in block.headers" :key="hi">{{ h }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, ri) in (block.data || [])" :key="ri">
                  <td v-for="(cell, ci) in row" :key="ci">{{ cell }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- MCQ block -->
          <div v-else-if="block.type === 'mcq'"
               class="mp-mcq"
               :class="`mp-mcq--${block.layout || '1col'}`">

            <!-- Question number + stem -->
            <div class="mp-mcq-stem">
              <span class="mp-mcq-num">{{ block.qNum || getMcqNumber(pi, bi) }}.</span>
              <!-- Stem image above -->
              <img v-if="block.hasImage && block.imageData && block.imagePos !== 'below'"
                   :src="block.imageData"
                   class="mp-mcq-stem-img" alt="" />
              <!-- Stem text -->
              <span v-html="cleanHtml(block.stem || '')"></span>
              <!-- Stem image below -->
              <img v-if="block.hasImage && block.imageData && block.imagePos === 'below'"
                   :src="block.imageData"
                   class="mp-mcq-stem-img" alt="" />
            </div>

            <!-- Marks -->
            <div v-if="block.showMarks !== false && block.marks" class="mp-mcq-marks">
              [{{ block.marks }}]
            </div>

            <!-- Options -->
            <div class="mp-mcq-opts"
                 :class="block.layout === '2col'
                   ? 'mp-mcq-opts--2col' : (block.layout === 'inline' ? 'mp-mcq-opts--inline' : '')">
              <div v-for="(opt, oi) in getOptions(block)"
                   :key="oi" class="mp-mcq-opt">
                <span class="mp-mcq-opt-label">
                  {{ ['A','B','C','D','E','F'][oi] }}.
                </span>
                <img v-if="opt.image"
                     :src="opt.image"
                     class="mp-mcq-opt-img" alt="" />
                <span v-html="cleanHtml(opt.text || '')"></span>
              </div>
            </div>

          </div>

        </div><!-- end block -->
      </div><!-- end page -->

    </div><!-- end paper sheet -->
      </div><!-- end paper wrapper -->
    </div><!-- end preview shell -->
</template>

<script setup>
import { ref, onMounted } from 'vue'

const loading = ref(false)
const error   = ref(null)
const paperId = ref(null)

// Paper data refs
const meta  = ref({})
const pages = ref([])

// Track MCQ numbering across all pages
function getMcqNumber(pageIdx, blockIdx) {
  let count = 0
  for (let pi = 0; pi <= pageIdx; pi++) {
    const blocks = pages.value[pi]?.blocks || []
    const limit  = pi === pageIdx ? blockIdx : blocks.length - 1
    for (let bi = 0; bi <= limit; bi++) {
      if (blocks[bi]?.type === 'mcq') count++
    }
  }
  return count
}

// Normalize options from different possible structures
function getOptions(block) {
  // Structure A (examStore): options as array of strings ['A text', 'B text', ...]
  if (Array.isArray(block.options) && block.options.length > 0) {
    // Detect if it's an array of strings or objects
    if (typeof block.options[0] === 'string') {
      const labels = ['A','B','C','D','E','F']
      return block.options.slice(0, 6).map((text, i) => ({
        text:  text || '',
        image: ''
      }))
    }
    // Structure A2: options as array of objects {text, image}
    if (typeof block.options[0] === 'object') {
      return block.options.map(o => ({
        text:  o.text  || o.option_text || '',
        image: o.image || o.option_image || ''
      }))
    }
  }
  // Fallback: return empty options so question numbers still show
  return []
}

// Strip dangerous HTML but keep formatting tags
function cleanHtml(html) {
  if (!html) return ''
  return html
    .replace(/<a\b[^>]*>(.*?)<\/a>/gi, '$1')
    .replace(/style="[^"]*"/gi, '')
    .replace(/<script\b[^>]*>[\s\S]*?<\/script>/gi, '')
}

onMounted(async () => {
  // Read paper_id from URL
  const params = new URLSearchParams(window.location.search)
  const pid    = params.get('paper_id')
  if (!pid) {
    error.value = 'No paper_id in URL.'
    return
  }
  paperId.value = Number(pid)

  loading.value = true
  try {
    const res = await window.axios.get(`/api/user/papers/${pid}`)
    const paper = res.data.paper ?? res.data

    // Parse paper_data
    let pd = paper.paper_data
    if (typeof pd === 'string') pd = JSON.parse(pd)
    pd = pd || {}

    // Hydrate meta from top-level paper fields + paper_data.paperMeta
    const pm = pd.paperMeta || pd.meta || {}
    meta.value = {
      paperTitle:   pm.paperTitle   || paper.title      || '',
      paperCode:    pm.paperCode    || pm.code          || '',
      code:         pm.code         || '',
      subject:      pm.subject      || paper.subject     || '',
      organization: pm.organization || paper.school_name || '',
      session:      pm.session      || pm.date          || '',
      date:         pm.date         || '',
      duration:     pm.duration     || '',
      paperNumber:  pm.paperNumber  || '',
      logoDataUrl:  pd.logoDataUrl  || pm.logo          || null,
      logo:         pm.logo         || null,
    }

    // Hydrate pages
    pages.value = pd.pages || []

    // Clean URL without reload
    const cleanUrl = window.location.pathname + window.location.search.replace(/&?mode=preview/, '')
    window.history.replaceState({}, '', cleanUrl)
  } catch (err) {
    error.value = err.response?.data?.message
      || 'Failed to load paper.'
    console.error('ManualPaperPreview load error:', err)
  } finally {
    loading.value = false;
  }
})

function goBack() {
  window.location.href = '/user/papers'
}

function editPaper() {
  window.location.href = `/user/manual?paper_id=${paperId.value}`
}

function printPaper() {
  window.print();
}
</script>

<style scoped>
/* ── Shell ── */
.mp-preview-shell {
  min-height: 100vh;
  background: #1a1a2e;
  padding: 0;
}

/* ── Action bar ── */
.mp-action-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 24px;
  background: #0f1629;
  border-bottom: 1px solid rgba(201,168,76,0.2);
  position: sticky;
  top: 0;
  z-index: 100;
}

/* ── Paper Wrapper (scrollable container) ── */
.mp-paper-wrapper {
  height: calc(100vh - 61px);
  overflow-y: auto;
  overflow-x: hidden;
  padding: 40px 20px;
  background: #1a1a2e;
}

/* ── A4 Paper Sheet ── */
.mp-paper-sheet {
  width: 210mm;
  min-height: 297mm;
  margin: 0 auto;
  background: #fff;
  color: #000 !important;
  padding: 20mm 18mm 18mm;
  font-family: 'Times New Roman', Times, serif;
  font-size: 10pt;
  line-height: 1.4;
  box-shadow: 0 4px 24px rgba(0,0,0,0.4);
}

/* ── Header ── */
.mp-header-top {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 6px;
}
.mp-logo {
  max-height: 48px;
  max-width: 120px;
  object-fit: contain;
}
.mp-school-name {
  font-size: 11pt;
  font-weight: bold;
}
.mp-rule {
  border: none;
  border-top: 1.5px solid #000;
  margin: 6px 0;
}
.mp-header-meta {
    text-align: right;
}
.mp-subject-line {
  font-size: 11pt;
  font-weight: bold;
}
.mp-meta-row {
  font-size: 10pt;
  color: #333;
}

/* ── Blocks ── */
.mp-block {
  margin-bottom: 15px;
}

/* Section */
.mp-section-title {
  font-size: 11pt;
  font-weight: bold;
  margin-bottom: 2px;
}
.mp-section-sub {
  font-size: 10pt;
  margin-bottom: 4px;
}
.mp-section-rule {
  border: none;
  border-top: 1px solid #000;
  margin-top: 4px;
}

/* Text */
.mp-text {
  font-size: 10pt;
  line-height: 1.5;
  margin-bottom: 8px;
}

/* Divider */
.mp-divider {
  border: none;
  border-top: 1px solid #000;
  margin: 8px 0;
}

/* Image */
.mp-image-block {
  text-align: center;
  margin: 8px 0;
}
.mp-img {
  max-width: 100%;
  object-fit: contain;
}
.mp-img-caption {
  font-size: 9pt;
  color: #555;
  margin-top: 4px;
}

/* Table */
.mp-table-wrap {
  overflow-x: auto;
  margin: 8px 0;
}
.mp-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 10pt;
}
.mp-table th,
.mp-table td {
  border: 1px solid #000;
  padding: 3px 6px;
}
.mp-table th {
  background: #f0f0f0;
  font-weight: bold;
  text-align: center;
}

/* MCQ */
.mp-mcq {
  margin-bottom: 15px;
  break-inside: avoid;
  page-break-inside: avoid;
}
.mp-mcq-stem {
  font-size: 10pt;
  line-height: 1.5;
  margin-bottom: 6px;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  align-items: flex-start;
}
.mp-mcq-num {
  font-weight: bold;
  flex-shrink: 0;
}
.mp-mcq-stem-img {
  max-width: 120px;
  max-height: 100px;
  object-fit: contain;
  display: block;
  margin: 4px 0;
}
.mp-mcq-marks {
  font-size: 9pt;
  color: #666;
  margin-bottom: 4px;
  font-weight: 500;
}
.mp-mcq-opts {
  padding-left: 25px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.mp-mcq-opts--2col {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px 20px;
}
.mp-mcq-opts--inline {
  flex-direction: row;
  flex-wrap: wrap;
  gap: 12px;
}
.mp-mcq-opts--inline .mp-mcq-opt {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  min-width: 120px;
}
.mp-mcq-opt {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 10pt;
}
.mp-mcq-opt-label {
  font-weight: bold;
  flex-shrink: 0;
  min-width: 20px;
}
.mp-mcq-opt-img {
  max-width: 75px;
  max-height: 60px;
  object-fit: contain;
}

/* Page break between pages */
.mp-page-break {
  page-break-before: always;
  break-before: page;
  height: 0;
}

@media print {
  .no-print {
    display: none !important;
  }
}
</style>
