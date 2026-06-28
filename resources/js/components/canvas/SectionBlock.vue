<template>
  <div class="block-section section-header-block" :style="{ textAlign: block.align || 'center' }">
    <div class="section-title">{{ block.title || 'Section Title' }}</div>
    <div class="section-subtitle" v-if="block.subtitle" style="font-size: 0.8em; font-weight: 400; margin-top: 2px;">{{ block.subtitle }}</div>
    <hr class="section-divider" v-if="block.showDivider" style="border: none; border-top: 2px solid #333; margin: 4px 0 0 0;" />
  </div>
</template>

<script setup>
const props = defineProps({
  block: Object
});
</script>
