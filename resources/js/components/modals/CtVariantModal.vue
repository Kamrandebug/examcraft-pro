<template>
  <div id="ct-variant-modal" class="modal-overlay" v-show="uiStore.showCtVariantModal" @click.self="uiStore.showCtVariantModal = false">
    <div class="modal-box">
      <div class="modal-header">
        <h5 class="m-0">Choose Table Style</h5>
        <button class="panel-collapse-btn" @click="uiStore.showCtVariantModal = false">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="modal-body d-flex flex-column gap-3">
        <div class="ct-variant-card" @click="$emit('selected', 1)">
          <div class="ct-variant-preview">
            <!-- Variant 1 Thumbnail -->
            <div style="border: 1px solid #ddd; padding: 5px; font-size: 8px;">
              Variant 1 Style
            </div>
          </div>
          <div class="ct-variant-info">
            <div class="ct-variant-label">Variant 1</div>
            <div class="ct-variant-desc">Classic border-only style.</div>
          </div>
        </div>

        <div class="ct-variant-card" @click="$emit('selected', 2)">
          <div class="ct-variant-preview">
            <!-- Variant 2 Thumbnail -->
            <div style="border: 1px solid #ddd; padding: 5px; font-size: 8px; background: #f9f9f9;">
              Variant 2 Style
            </div>
          </div>
          <div class="ct-variant-info">
            <div class="ct-variant-label">Variant 2</div>
            <div class="ct-variant-desc">Modern alternating row style.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useUiStore } from '../../stores/uiStore';
const uiStore = useUiStore();

const emit = defineEmits(['selected']);
</script>
