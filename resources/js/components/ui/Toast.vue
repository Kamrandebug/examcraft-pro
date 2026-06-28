<template>
  <div class="toast-item" :class="type" v-if="visible">
    <span>{{ message }}</span>
    <button class="toast-close" @click="() => {/* TODO closeToast */}">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
  message: String,
  type: String,
  visible: Boolean
});
</script>
