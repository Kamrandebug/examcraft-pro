<template>
  <div id="canvas-area" @dragover.prevent @drop="handleDrop">
    <div id="canvas-inner">
      <!-- Zoom bar -->
      <div id="zoom-bar">
        <button class="zoom-btn" @click="setZoom(uiStore.currentZoom - 0.1)">
          <i class="fa fa-minus"></i>
        </button>
        <label>Zoom</label>
        <input 
          type="range" 
          id="zoom-range" 
          min="0.4" 
          max="1.4" 
          step="0.05" 
          :value="uiStore.currentZoom"  
          @input="setZoom(parseFloat($event.target.value))"
        />
        <span id="zoom-val">{{ Math.round(uiStore.currentZoom * 100) }}%</span>
        <button class="zoom-btn" @click="setZoom(uiStore.currentZoom + 0.1)">
          <i class="fa fa-plus"></i>
        </button>
        <button class="zoom-btn" @click="setZoom(1)" title="Reset">
          <i class="fa fa-expand-arrows-alt"></i>
        </button>
      </div>

      <!-- Pages container -->
      <div id="pages-container">
        <PageCanvas 
          v-for="(page, idx) in examStore.pages" 
          :key="page.id" 
          :pageIndex="idx" 
          :pageData="page" 
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { nextTick, watch, onMounted, onUnmounted } from 'vue';
import { useExamStore } from '../stores/examStore';
import { useUiStore } from '../stores/uiStore';
import { useBlockOperations } from '../composables/useBlockOperations';
import PageCanvas from './canvas/PageCanvas.vue';

const examStore = useExamStore();
const uiStore = useUiStore();
const { addBlock } = useBlockOperations();

let resizeObserver = null;

function setZoom(val) {
  const clamped = Math.max(0.4, Math.min(1.4, val));
  uiStore.currentZoom = clamped;
  updatePageScaling();
}

function updatePageScaling() {
  const clamped = uiStore.currentZoom;
  // Use requestAnimationFrame to ensure we measure after browser layout
  requestAnimationFrame(() => {
    document.querySelectorAll('.page-canvas').forEach(pc => {
      // Temporarily reset transform/margins to get "natural" height
      pc.style.transform = 'none';
      pc.style.marginBottom = '0';
      
      const naturalH = pc.offsetHeight || 1123;
      const scaledH = naturalH * clamped;
      
      pc.style.transform = 'scale(' + clamped + ')';
      pc.style.transformOrigin = 'top center';
      
      const deadSpace = naturalH - scaledH;
      // Use floor to ensure we don't accidentally overlap due to sub-pixel rounding
      pc.style.marginBottom = '-' + Math.floor(deadSpace) + 'px';
    });
  });
}

// Watch for zoom changes
watch(() => uiStore.currentZoom, updatePageScaling);

// Re-observe pages if new ones are added
watch(() => examStore.pages.length, () => {
  nextTick(() => {
    if (resizeObserver) {
      document.querySelectorAll('.page-canvas').forEach(pc => {
        resizeObserver.observe(pc);
      });
    }
  });
});

onMounted(() => {
  // Use ResizeObserver to detect when page content changes (text, images, new blocks)
  // This ensures marginBottom is always correct even if content loads asynchronously
  resizeObserver = new ResizeObserver(() => {
    updatePageScaling();
  });

  nextTick(() => {
    document.querySelectorAll('.page-canvas').forEach(pc => {
      resizeObserver.observe(pc);
    });
  });
});

onUnmounted(() => {
  if (resizeObserver) {
    resizeObserver.disconnect();
  }
});

function handleDrop(event) {
  const type = event.dataTransfer.getData('text/plain');
  if (type) {
    addBlock(type);
  }
}
</script>
