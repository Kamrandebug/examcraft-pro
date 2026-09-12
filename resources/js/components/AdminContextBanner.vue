<template>
  <div v-if="isAdminContext" class="admin-context-banner">
    <div class="banner-content">
      <i class="fas fa-shield-alt"></i>
      <span class="banner-text">
        <strong>Admin Context:</strong> Editing paper for <strong>{{ adminTargetUserName }}</strong>
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const isAdminContext = computed(() => !!window.adminTargetUserId);
const adminTargetUserName = computed(() => window.adminTargetUserName || 'Unknown User');
</script>

<style scoped>
.admin-context-banner {
  background: linear-gradient(90deg, #dc2626 0%, #b91c1c 100%);
  color: #fff;
  padding: 10px 16px;
  border-bottom: 2px solid #991b1b;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.banner-content {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 13px;
  font-weight: 500;
  max-width: 100%;
  word-break: break-word;
}

.banner-content i {
  flex-shrink: 0;
  font-size: 16px;
}

.banner-text strong {
  font-weight: 600;
}
</style>
