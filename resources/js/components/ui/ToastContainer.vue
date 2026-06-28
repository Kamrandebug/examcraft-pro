<template>
  <div id="toast-container">
    <Toast v-for="toast in uiStore.toasts" :key="toast.id" v-bind="toast" />
  </div>
</template>

<script setup>
import { useUiStore } from '../../stores/uiStore';
import Toast from './Toast.vue';

const uiStore = useUiStore();
</script>
