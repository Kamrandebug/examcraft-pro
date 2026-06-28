<template>
  <div class="mini-editor-wrap">
    <div class="me-toolbar mini-editor-toolbar">
      <button class="tb-icon" @click="() => {/* TODO execCommand('bold') */}">
        <i class="fa-solid fa-bold"></i>
      </button>
      <button class="tb-icon" @click="() => {/* TODO execCommand('italic') */}">
        <i class="fa-solid fa-italic"></i>
      </button>
      <button class="tb-icon" @click="() => {/* TODO execCommand('superscript') */}">
        <i class="fa-solid fa-superscript"></i>
      </button>
      <button class="tb-icon" @click="() => {/* TODO execCommand('subscript') */}">
        <i class="fa-solid fa-subscript"></i>
      </button>
      <button class="tb-icon" @click="() => {/* TODO execCommand('removeFormat') */}">
        <i class="fa-solid fa-eraser"></i>
      </button>
    </div>
    <div 
      class="me-body" 
      contenteditable="true" 
      @input="updateValue"
      v-html="modelValue"
      :placeholder="placeholder"
    ></div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: String,
  placeholder: String
});

const emit = defineEmits(['update:modelValue']);

const updateValue = (event) => {
  emit('update:modelValue', event.target.innerHTML);
};
</script>
