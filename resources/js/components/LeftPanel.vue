<template>
  <div id="left-panel" :style="{ width: uiStore.leftCollapsed ? '0px' : uiStore.leftPanelWidth + 'px' }">
    <div class="panel-header" style="display:flex;align-items:center;justify-content:space-between;">
      <span>Paper Designer</span>
      <button class="panel-collapse-btn" @click="togglePanel('left')" title="Collapse panel">
        <i class="fa" :class="uiStore.leftCollapsed ? 'fa-chevron-right' : 'fa-chevron-left'" id="left-collapse-icon"></i>
      </button>
    </div>

    <div id="left-panel-scroll">
      <!-- Paper Settings -->
      <div class="panel-section">
        <div class="panel-section-title">Paper Settings</div>
        <div class="meta-row">
          <div class="meta-label">Organization</div>
          <input class="meta-input" id="meta-org" v-model="examStore.paperMeta.organization" @input="renderHeader()"/>
        </div>
        <div class="meta-row">
          <div class="meta-label">Sub Title</div>
          <input class="meta-input" id="meta-org-sub" v-model="examStore.paperMeta.subtitle" @input="renderHeader()"/>
        </div>
        <div class="meta-row">
          <div class="meta-label">Subject</div>
          <input class="meta-input" id="meta-subject" v-model="examStore.paperMeta.subject" @input="renderHeader()"/>
        </div>
        <div class="meta-row" style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
          <div>
            <div class="meta-label">Paper Code</div>
            <input class="meta-input" id="meta-code" v-model="examStore.paperMeta.code" @input="renderHeader()"/>
          </div>
          <div>
            <div class="meta-label">Duration</div>
            <input class="meta-input" id="meta-duration" v-model="examStore.paperMeta.duration" @input="renderHeader()"/>
          </div>
        </div>
        <div class="meta-row" style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
          <div>
            <div class="meta-label">Paper Type</div>
            <select class="meta-select" id="meta-paper-type" v-model="examStore.paperMeta.paperType" @change="renderHeader()">
              <option value="Paper 1 Multiple Choice">Paper 1 Multiple Choice</option>
              <option value="Paper 2 Core">Paper 2 Core</option>
              <option value="Paper 3 Extended">Paper 3 Extended</option>
              <option value="Paper 4 Alternative to Practical">Paper 4 Alt. to Practical</option>
              <option value="Paper 5 Practical Test">Paper 5 Practical Test</option>
              <option value="Paper 6 Alternative to Coursework">Paper 6 Alt. to Coursework</option>
            </select>
          </div>
          <div>
            <div class="meta-label">Date</div>
            <input class="meta-input" id="meta-date" v-model="examStore.paperMeta.date" @input="renderHeader()"/>
          </div>
        </div>
        <div class="meta-row">
          <div class="meta-label">Instructions</div>
          <MiniRichEditor v-model="examStore.paperMeta.instructions" placeholder="Enter instructions..." @input="renderHeader()" />
        </div>
        <div class="meta-row">
          <div class="meta-label">Additional Materials</div>
          <MiniRichEditor v-model="examStore.paperMeta.materials" placeholder="e.g. Scientific Calculator" @input="renderHeader()" />
        </div>
        <div class="meta-row">
          <div class="meta-label">Materials Label <span style="font-size:9px;color:var(--text-muted);font-weight:400;">(left column text)</span></div>
          <input class="meta-input" id="meta-materials-label" v-model="examStore.paperMeta.materialsLabel" placeholder="e.g. Additional Materials:" @input="renderHeader()"/>
        </div>
        <div class="meta-row">
          <div class="meta-label">Instructions Heading</div>
          <input class="meta-input" id="meta-instr-heading" v-model="examStore.paperMeta.instrHeading" placeholder="e.g. READ THESE INSTRUCTIONS FIRST" @input="renderHeader()"/>
        </div>
      </div>

      <!-- Add Blocks -->
      <div class="panel-section">
        <div class="panel-section-title">Add Blocks</div>
        <div class="block-palette">
          <div class="palette-item" draggable="true" @click="addBlock('mcq')" @dragstart="paletteDragStart($event, 'mcq')">
            <i class="fa fa-list-ol"></i>
            <span>MCQ</span>
          </div>
          <div class="palette-item" draggable="true" @click="addBlock('section')" @dragstart="paletteDragStart($event, 'section')">
            <i class="fa fa-heading"></i>
            <span>Section</span>
          </div>
          <div class="palette-item" draggable="true" @click="addBlock('text')" @dragstart="paletteDragStart($event, 'text')">
            <i class="fa fa-align-left"></i>
            <span>Text</span>
          </div>
          <div class="palette-item" draggable="true" @click="addBlock('image')" @dragstart="paletteDragStart($event, 'image')">
            <i class="fa fa-image"></i>
            <span>Image</span>
          </div>
          <div class="palette-item" draggable="true" @click="addBlock('table')" @dragstart="paletteDragStart($event, 'table')">
            <i class="fa fa-table"></i>
            <span>Table</span>
          </div>
          <div class="palette-item" draggable="true" @click="addBlock('divider')" @dragstart="paletteDragStart($event, 'divider')">
            <i class="fa fa-minus"></i>
            <span>Divider</span>
          </div>
        </div>
      </div>

      <!-- Questions List -->
      <div class="panel-header" style="display:flex;align-items:center;justify-content:space-between;">
        <span>Questions</span>
        <span id="q-count-badge">{{ qCount }}</span>
      </div>
      <div id="question-list" style="padding:6px;">
        <div 
          v-for="q in questionBlocks" 
          :key="q.id" 
          class="q-list-item" 
          :class="{ active: examStore.selectedBlockId === q.id }"
          @click="selectBlock(q.id)"
        >
          <div class="q-num">{{ q.qNum }}</div>
          <div class="q-label">{{ truncateText(q.stem, 30) }}</div>
          <div class="q-type-badge">MCQ</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useExamStore } from '../stores/examStore';
import { useUiStore } from '../stores/uiStore';
import { useBlockOperations } from '../composables/useBlockOperations';
import MiniRichEditor from './ui/MiniRichEditor.vue';

const examStore = useExamStore();
const uiStore = useUiStore();
const { addBlock } = useBlockOperations();

const qCount = computed(() => {
  return questionBlocks.value.length;
});

const questionBlocks = computed(() => {
  const blocks = [];
  examStore.pages.forEach(page => {
    page.blocks.forEach(block => {
      if (block.type === 'mcq') blocks.push(block);
    });
  });
  return blocks;
});

function selectBlock(id) {
  examStore.selectedBlockId = id;
  // Scroll to block
  const el = document.querySelector(`.question-block[data-id="${id}"]`);
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function truncateText(text, len) {
  if (!text) return '...';
  const plain = text.replace(/<[^>]*>/g, '');
  return plain.length > len ? plain.substr(0, len) + '...' : plain;
}

function togglePanel(side) {
  if (side === 'left') uiStore.leftCollapsed = !uiStore.leftCollapsed;
}

function paletteDragStart(event, type) {
  event.dataTransfer.setData('text/plain', type);
}

function renderHeader() {
  // Logic to trigger header refresh if needed, though Vue handles reactivity
}
</script>
