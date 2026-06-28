<template>
  <div class="block-image">
    <img v-if="block.src" :src="block.src" :style="{ width: block.width || '100%' }" />
    <div v-else class="image-placeholder">
      <i class="fa-solid fa-image"></i>
      <p>No image selected</p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  block: Object
});
</script>
