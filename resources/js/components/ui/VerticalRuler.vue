<template>
  <div class="v-ruler-wrap" :class="side">
    <button class="ruler-up-btn" @click="scrollUp">
      <i class="fa-solid fa-chevron-up"></i>
    </button>
    <canvas ref="rulerCanvas" class="ruler-canvas"></canvas>
    <div ref="indicator" class="ruler-indicator"></div>
    <button class="ruler-down-btn" @click="scrollDown">
      <i class="fa-solid fa-chevron-down"></i>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useRuler } from '../../composables/useRuler';
import { useUiStore } from '../../stores/uiStore';

const props = defineProps({
  side: {
    type: String,
    required: true,
    validator: (value) => ['left', 'right'].includes(value)
  }
});

const uiStore = useUiStore();
const rulerCanvas = ref(null);
const indicator = ref(null);
const { drawRuler, updateIndicator } = useRuler();

function updateCanvasSize() {
  if (!rulerCanvas.value) return;
  const wrapper = rulerCanvas.value.parentElement;
  if (!wrapper) return;
  
  // Update internal canvas dimensions to match display size
  rulerCanvas.value.height = wrapper.clientHeight - 40; // minus buttons
  rulerCanvas.value.width = wrapper.clientWidth;
  
  handleScroll();
}

function handleScroll() {
  const container = document.getElementById('canvas-area');
  if (!container || !rulerCanvas.value) return;

  const scrollTop = container.scrollTop;
  const scrollHeight = container.scrollHeight;
  const clientHeight = container.clientHeight;
  const rulerHeight = rulerCanvas.value.height;

  drawRuler(rulerCanvas.value, scrollTop, scrollHeight);
  updateIndicator(indicator.value, scrollTop, scrollHeight, clientHeight, rulerHeight);
}

function scrollUp() {
  const container = document.getElementById('canvas-area');
  if (container) container.scrollTop -= 100;
}

function scrollDown() {
  const container = document.getElementById('canvas-area');
  if (container) container.scrollTop += 100;
}

// Watch for zoom changes which affect scrollHeight
watch(() => uiStore.currentZoom, () => {
  // Wait for layout shift to finish
  setTimeout(handleScroll, 50);
});

onMounted(() => {
  const container = document.getElementById('canvas-area');
  if (container) {
    container.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', updateCanvasSize);
    
    updateCanvasSize();
  }
});

onUnmounted(() => {
  const container = document.getElementById('canvas-area');
  if (container) {
    container.removeEventListener('scroll', handleScroll);
  }
  window.removeEventListener('resize', updateCanvasSize);
});
</script>
