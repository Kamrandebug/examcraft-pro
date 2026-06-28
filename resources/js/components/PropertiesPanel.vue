<template>
  <div v-if="selectedBlock" class="properties-panel">
    <div class="prop-type-badge">{{ selectedBlock.type.toUpperCase() }} BLOCK</div>
    
    <!-- MCQ Block Properties -->
    <template v-if="selectedBlock.type === 'mcq'">
      <div class="prop-group">
        <label class="prop-label">Question Stem</label>
        <textarea class="prop-textarea" v-model="selectedBlock.stem"></textarea>
      </div>
      <div class="option-editor">
        <div class="option-editor-row" v-for="(opt, idx) in selectedBlock.options" :key="idx">
          <div class="option-letter-badge">{{ String.fromCharCode(65 + idx) }}</div>
          <textarea class="option-text-input" v-model="selectedBlock.options[idx]"></textarea>
          <div class="option-correct-radio" @click="selectedBlock.correct = String.fromCharCode(65 + idx)">
            <div class="correct-indicator" :class="{ marked: selectedBlock.correct === String.fromCharCode(65 + idx) }"></div>
          </div>
        </div>
      </div>
      <div class="prop-row">
        <div class="prop-group">
          <label class="prop-label">Marks</label>
          <input type="number" class="prop-input" v-model="selectedBlock.marks" />
        </div>
        <div class="prop-group">
          <label class="prop-label">Layout</label>
          <select class="prop-select" v-model="selectedBlock.layout">
            <option value="1col">1 Column</option>
            <option value="2col">2 Columns</option>
            <option value="inline">Inline</option>
          </select>
        </div>
      </div>
      <div class="prop-group">
        <ToggleSwitch v-model="selectedBlock.showMarks" label="Show Marks" />
        <ToggleSwitch v-model="selectedBlock.hasAnswerBox" label="Answer Box" />
        <ToggleSwitch v-model="selectedBlock.hasImage" label="Has Image" />
      </div>
    </template>

    <!-- Section Block Properties -->
    <template v-else-if="selectedBlock.type === 'section'">
      <div class="prop-group">
        <label class="prop-label">Section Title</label>
        <input type="text" class="prop-input" v-model="selectedBlock.title" />
      </div>
      <div class="prop-group">
        <label class="prop-label">Subtitle</label>
        <input type="text" class="prop-input" v-model="selectedBlock.subtitle" />
      </div>
      <div class="prop-group">
        <label class="prop-label">Alignment</label>
        <select class="prop-select" v-model="selectedBlock.align">
          <option value="left">Left</option>
          <option value="center">Center</option>
          <option value="right">Right</option>
        </select>
      </div>
      <div class="prop-group">
        <ToggleSwitch v-model="selectedBlock.showDivider" label="Show Divider" />
      </div>
    </template>

    <!-- Text Block Properties -->
    <template v-else-if="selectedBlock.type === 'text'">
      <div class="prop-group">
        <label class="prop-label">Content</label>
        <textarea class="prop-textarea" v-model="selectedBlock.content"></textarea>
      </div>
      <div class="prop-row">
        <div class="prop-group">
          <label class="prop-label">Font Size</label>
          <input type="number" class="prop-input" v-model="selectedBlock.fontSize" />
        </div>
        <div class="prop-group">
          <label class="prop-label">Alignment</label>
          <select class="prop-select" v-model="selectedBlock.align">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
            <option value="justify">Justify</option>
          </select>
        </div>
      </div>
      <div style="display:flex;gap:4px;">
        <button class="btn-sm-dark" :class="{ active: selectedBlock.bold }" @click="selectedBlock.bold = !selectedBlock.bold">Bold</button>
        <button class="btn-sm-dark" :class="{ active: selectedBlock.italic }" @click="selectedBlock.italic = !selectedBlock.italic">Italic</button>
      </div>
    </template>

    <!-- Image Block Properties -->
    <template v-else-if="selectedBlock.type === 'image'">
      <div class="prop-group">
        <label class="prop-label">Image</label>
        <div class="img-upload-zone" @click="$refs.imgInput.click()">
          <i class="fa fa-cloud-upload-alt"></i>
          <span>Click to upload image</span>
        </div>
        <input type="file" ref="imgInput" style="display:none" @change="handleImageUpload" accept="image/*"/>
      </div>
      <div class="prop-group">
        <label class="prop-label">Caption</label>
        <input type="text" class="prop-input" v-model="selectedBlock.caption" />
      </div>
      <div class="prop-row">
        <div class="prop-group">
          <label class="prop-label">Width (%)</label>
          <input type="number" class="prop-input" v-model="selectedBlock.width" />
        </div>
        <div class="prop-group">
          <label class="prop-label">Alignment</label>
          <select class="prop-select" v-model="selectedBlock.align">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
          </select>
        </div>
      </div>
    </template>

    <!-- Table Block Properties -->
    <template v-else-if="selectedBlock.type === 'table'">
      <div class="prop-row">
        <div class="prop-group">
          <label class="prop-label">Rows</label>
          <input type="number" class="prop-input" v-model="selectedBlock.rows" @change="updateTableDimensions" />
        </div>
        <div class="prop-group">
          <label class="prop-label">Cols</label>
          <input type="number" class="prop-input" v-model="selectedBlock.cols" @change="updateTableDimensions" />
        </div>
      </div>
      <div class="prop-group" v-for="(h, idx) in selectedBlock.headers" :key="idx">
        <label class="prop-label">Header {{ idx + 1 }}</label>
        <input type="text" class="prop-input" v-model="selectedBlock.headers[idx]" />
      </div>
    </template>

    <!-- Divider Block Properties -->
    <template v-else-if="selectedBlock.type === 'divider'">
      <div class="prop-row">
        <div class="prop-group">
          <label class="prop-label">Style</label>
          <select class="prop-select" v-model="selectedBlock.style">
            <option value="solid">Solid</option>
            <option value="dashed">Dashed</option>
            <option value="dotted">Dotted</option>
          </select>
        </div>
        <div class="prop-group">
          <label class="prop-label">Thickness</label>
          <input type="number" class="prop-input" v-model="selectedBlock.thickness" />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useExamStore } from '../stores/examStore';
import ToggleSwitch from './ui/ToggleSwitch.vue';

const examStore = useExamStore();
const imgInput = ref(null);

const selectedBlock = computed(() => {
  if (!examStore.selectedBlockId) return null;
  for (const page of examStore.pages) {
    const block = page.blocks.find(b => b.id === examStore.selectedBlockId);
    if (block) return block;
  }
  return null;
});

function handleImageUpload(e) {
  const file = e.target.files[0];
  if (file && selectedBlock.value) {
    const reader = new FileReader();
    reader.onload = (event) => {
      selectedBlock.value.src = event.target.result;
    };
    reader.readAsDataURL(file);
  }
}

function updateTableDimensions() {
  if (!selectedBlock.value) return;
  const b = selectedBlock.value;
  if (!b.headers) b.headers = [];
  if (b.headers.length < b.cols) {
    while (b.headers.length < b.cols) b.headers.push(`Col ${b.headers.length + 1}`);
  } else {
    b.headers = b.headers.slice(0, b.cols);
  }
}
</script>
