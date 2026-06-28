<template>
  <div class="block-divider">
    <hr :class="block.style || 'solid'" />
  </div>
</template>

<script setup>
const props = defineProps({
  block: Object
});
</script>
