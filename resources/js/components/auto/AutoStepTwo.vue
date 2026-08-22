<template>
  <div class="step-card">
    <div class="step-card-header">
      <span class="step-card-icon">⚙️</span>
      <h5 class="step-card-title">Settings</h5>
    </div>
    <div class="step-card-body">
      <div class="ag-section-title">Additional Materials</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="ag-label">Additional Materials (one per line)</label>
          <textarea class="form-control ag-input" rows="3" v-model="additionalMaterials"></textarea>
        </div>
      </div>

      <div class="ag-section-title">Instructions</div>
      <div class="row g-3">
        <div class="col-12">
          <div class="ag-instructions-preview">{{ instructions }}</div>
          <button type="button" class="btn btn-sm ag-outline-btn mt-2" @click="editingInstructions = !editingInstructions">
            {{ editingInstructions ? '✅ Done Editing' : '✏️ Edit Instructions' }}
          </button>
          <textarea v-show="editingInstructions" class="form-control ag-input mt-2" rows="10" v-model="instructions"></textarea>
        </div>
      </div>

      <div class="ag-section-title">Logo (Optional)</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="ag-label">Institution Logo (Optional)</label>
          <div class="ag-logo-row">
            <img v-if="logoDataUrl" class="ag-logo-preview" :src="logoDataUrl" alt="Logo preview" />
            <button type="button" class="btn btn-sm ag-outline-btn" @click="pickLogo">Choose Logo</button>
            <button v-if="logoDataUrl" type="button" class="btn btn-sm ag-outline-btn ag-remove-btn" @click="removeLogo">Remove Logo</button>
          </div>
          <input ref="logoInput" type="file" accept="image/*" class="d-none" @change="onLogoChange" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useAutoPaperStore } from '../../stores/autoPaperStore';

const autoPaperStore = useAutoPaperStore();

// Local refs bound to store
const additionalMaterials = ref(autoPaperStore.additionalMaterials);
const instructions = ref(autoPaperStore.instructions);
const logoDataUrl = ref(autoPaperStore.logoDataUrl);
const editingInstructions = ref(false);
const logoInput = ref(null);

// Sync local refs → store
watch([additionalMaterials, instructions], () => {
  autoPaperStore.setPaperMeta({
    additionalMaterials: additionalMaterials.value,
    instructions: instructions.value,
  });
});

function pickLogo() {
  if (logoInput.value) logoInput.value.click();
}

function onLogoChange(event) {
  const file = event.target.files && event.target.files[0];
  if (!file) return;
  resizeLogo(file).then((dataUrl) => {
    logoDataUrl.value = dataUrl;
    autoPaperStore.setLogo({ file, dataUrl });
  });
  event.target.value = '';
}

function resizeLogo(file) {
  return new Promise((resolve) => {
    const reader = new FileReader();
    reader.onload = () => {
      const img = new Image();
      img.onload = () => {
        const MAX = 300;
        let { width, height } = img;
        const scale = Math.min(1, MAX / Math.max(width, height));
        width = Math.round(width * scale);
        height = Math.round(height * scale);
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, width, height);
        let dataUrl;
        try { dataUrl = canvas.toDataURL('image/jpeg', 0.85); }
        catch { dataUrl = canvas.toDataURL('image/png'); }
        resolve(dataUrl);
      };
      img.onerror = () => resolve(reader.result);
      img.src = reader.result;
    };
    reader.onerror = () => resolve(null);
    reader.readAsDataURL(file);
  });
}

function removeLogo() {
  logoDataUrl.value = null;
  autoPaperStore.setLogo({ file: null, dataUrl: null });
}
</script>

<style scoped>
.step-card {
  background: var(--bg-card);
  border: 1px solid var(--border-light);
  border-top: 3px solid #C9A84C;
  border-radius: 8px;
}

.step-card-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-light);
  background: #1B2A4A;
  border-radius: 8px 8px 0 0;
}

.step-card-icon {
  font-size: 18px;
}

.step-card-title {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  font-weight: 700;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin: 0;
}

.step-card-body {
  padding: 20px;
}

.ag-label {
  font-family: var(--font-body);
  font-size: 10px;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin-bottom: 4px;
  display: block;
}

.ag-input {
  font-family: var(--font-body);
  font-size: 12px;
  background: var(--bg-secondary);
  border: 1px solid var(--border-light);
  color: var(--text-primary);
  border-radius: var(--radius-sm);
  padding: 8px 10px;
  transition: var(--transition);
}

.ag-input:focus {
  border-color: #C9A84C;
  box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.14);
}

.ag-section-title {
  font-family: var(--font-body);
  font-size: 11px;
  font-weight: 700;
  color: var(--accent-2);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin: 16px 0 10px;
  padding-bottom: 6px;
  border-bottom: 1px solid var(--border-light);
}

.ag-section-title:first-child {
  margin-top: 0;
}

.ag-instructions-preview {
  font-family: var(--font-body);
  font-size: 12px;
  line-height: 1.5;
  white-space: pre-wrap;
  color: var(--text-secondary);
  background: var(--bg-secondary);
  border: 1px solid var(--border-light);
  border-radius: var(--radius-sm);
  padding: 10px 12px;
  max-height: 200px;
  overflow-y: auto;
}

.ag-logo-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.ag-logo-preview {
  max-height: 60px;
  max-width: 140px;
  border: 1px solid var(--border-light);
  border-radius: var(--radius-sm);
  background: #fff;
  padding: 2px;
  object-fit: contain;
}

.ag-outline-btn {
  font-family: var(--font-body);
  font-size: 10px;
  font-weight: 600;
  color: var(--text-secondary);
  background: var(--bg-card);
  border: 1px solid var(--border-light);
  border-radius: var(--radius-sm);
  padding: 4px 10px;
  transition: var(--transition);
}

.ag-outline-btn:hover {
  border-color: var(--accent-2);
  color: var(--accent-2);
}

.ag-remove-btn {
  color: #d9534f;
  border-color: #d9534f;
}

.ag-remove-btn:hover {
  background: #d9534f;
  color: #fff;
  border-color: #d9534f;
}
</style>