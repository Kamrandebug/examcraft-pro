<template>
  <div id="print-overlay" class="modal-overlay" v-show="uiStore.showPreview" @click.self="closePreview">
    <div class="preview-toolbar">
      <div class="d-flex gap-2">
        <button class="topbar-btn" @click="closePreview">
          <i class="fa-solid fa-xmark"></i> Close
        </button>
        <button class="topbar-btn accent" @click="printPaper">
          <i class="fa-solid fa-print"></i> Print
        </button>
        <button class="topbar-btn blue" @click="exportPDF">
          <i class="fa-solid fa-file-pdf"></i> PDF
        </button>
      </div>
    </div>
    <div id="preview-content">
      <!-- Content populated by usePrint.showPreview() -->
    </div>
  </div>
</template>

<script setup>
import { useUiStore } from '../../stores/uiStore';
import { usePrint } from '../../composables/usePrint';

const uiStore = useUiStore();
const { closePreview, printPaper, exportPDF } = usePrint();
</script>
