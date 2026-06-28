<template>
  <div class="block-text text-block" :style="{
    fontSize: (block.fontSize || 12) + 'pt',
    textAlign: block.align || 'left',
    fontWeight: block.bold ? '700' : '400',
    fontStyle: block.italic ? 'italic' : 'normal'
  }">
    <div class="text-content" v-html="block.content || 'Enter text here...'"></div>
  </div>
</template>

<script setup>
const props = defineProps({
  block: Object
});
</script>
