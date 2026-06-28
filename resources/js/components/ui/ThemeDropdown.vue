<template>
  <div 
    v-show="uiStore.showThemeDropdown"
    id="theme-dropdown"
    style="display:block;position:absolute;top:36px;right:0;min-width:180px;background:var(--bg-card);border:1px solid var(--border-light);border-radius:var(--radius-sm);box-shadow:var(--shadow-lg);z-index:99999;overflow:hidden;padding:4px;"
  >
    <div 
      v-for="(info, id) in THEMES" 
      :key="id" 
      :id="'tdrop-' + id" 
      class="tdrop-item" 
      :class="{ active: uiStore.theme === id }"
      @click="handleSetTheme(id)"
    >
      <span>{{ info.icon }}</span>
      <div>
        <b>{{ info.label }}</b>
        <span>{{ info.desc }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useUiStore } from '../../stores/uiStore';
import { useTheme } from '../../composables/useTheme';

const uiStore = useUiStore();
const { THEMES, setTheme } = useTheme();

function handleSetTheme(id) {
  setTheme(id);
  uiStore.showThemeDropdown = false;
}
</script>
