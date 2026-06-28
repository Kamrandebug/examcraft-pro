<template>
  <div ref="handle" class="resize-handle" :class="'resize-handle-' + side">
    <div class="rh-label">
      <span>⠿</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { usePanelResize } from '../../composables/usePanelResize';

const props = defineProps({
  side: {
    type: String,
    required: true,
    validator: (value) => ['left', 'right'].includes(value)
  }
});

const handle = ref(null);
const { initResize } = usePanelResize();
let cleanup = null;

onMounted(() => {
  if (handle.value) {
    cleanup = initResize(handle.value, props.side);
  }
});

onUnmounted(() => {
  if (cleanup) cleanup();
});
</script>
