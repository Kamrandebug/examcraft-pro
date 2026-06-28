<template>
  <div 
    class="toggle-switch" 
    :class="{ active: modelValue }" 
    @click="$emit('update:modelValue', !modelValue)"
  >
    <div class="toggle-track"></div>
    <div class="toggle-thumb"></div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: Boolean
});

const emit = defineEmits(['update:modelValue']);
</script>
