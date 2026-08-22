<template>
  <div class="auto-paper-wizard">
    <!-- Header -->
    <div class="wizard-header text-center py-3">
      <h2 style="color:#1B2A4A; font-family:'EB Garamond',serif;">
        Auto Paper Generator
        <span v-if="store.isEditMode" class="badge bg-warning text-dark ms-2" style="font-size: 0.5em; vertical-align: middle;">
          ✏ Editing Paper
        </span>
      </h2>
      <p class="text-muted">
        Fill in the details and load MCQs to generate your paper
      </p>
    </div>

    <!-- Loading state (show while paper is being fetched) -->
    <div v-if="store.editLoading" class="text-center py-5">
      <div class="spinner-border text-warning" role="status">
        <span class="visually-hidden">Loading…</span>
      </div>
      <p class="mt-3 text-muted">Loading paper data…</p>
    </div>

    <div v-else-if="store.editError" class="alert alert-danger m-4">
      ⚠ {{ store.editError }}
    </div>

    <template v-else>
      <!-- Stepper -->
      <AutoStepper :currentStep="store.currentStep" @go-to-step="store.goToStep($event)" />

      <!-- Step Content with fade transition -->
      <Transition name="step-fade" mode="out-in">
        <AutoStepOne v-if="store.currentStep === 1" :key="1" ref="stepOneRef" />
        <AutoStepTwo v-else-if="store.currentStep === 2" :key="2" />
        <AutoStepThree v-else-if="store.currentStep === 3" :key="3" />
      </Transition>

      <!-- Navigation -->
      <div class="wizard-nav d-flex align-items-center mt-3 pt-3" style="border-top: 1px solid #DDD8CC;">
        <!-- Back button: hidden on step 1 -->
        <button
          v-if="store.currentStep > 1"
          class="btn btn-outline-secondary"
          @click="store.prevStep()"
        >
          ← Back
        </button>

        <div class="flex-grow-1"></div>

        <!-- Next button: only on step 1 and 2 -->
        <button
          v-if="store.currentStep < 3"
          class="btn btn-primary"
          @click="handleNext()"
        >
          Next →
        </button>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAutoPaperStore } from '../stores/autoPaperStore';
import AutoStepper from './auto/AutoStepper.vue';
import AutoStepOne from './auto/AutoStepOne.vue';
import AutoStepTwo from './auto/AutoStepTwo.vue';
import AutoStepThree from './auto/AutoStepThree.vue';

const store = useAutoPaperStore();
const stepOneRef = ref(null);

function handleNext() {
  if (store.currentStep === 1) {
    if (store.isStep1Valid) {
      store.nextStep();
    } else {
      // Trigger validation display in AutoStepOne
      if (stepOneRef.value) {
        stepOneRef.value.validate();
      }
    }
  } else if (store.currentStep === 2) {
    store.nextStep();
  }
}
</script>

<style scoped>
.auto-paper-wizard {
  max-width: 900px;
  margin: 0 auto;
  padding: 1.5rem 1rem 2rem;
  min-height: 100vh;
  overflow: visible;
}

.wizard-header h2 {
  margin-bottom: 4px;
}

.wizard-nav {
  position: static;
  background: transparent;
  z-index: 10;
}

.wizard-nav .btn-outline-secondary {
  font-size: 14px;
  font-weight: 600;
}

.wizard-nav .btn-primary {
  background: #1B2A4A;
  border-color: #1B2A4A;
  font-size: 14px;
  font-weight: 600;
  padding: 8px 24px;
}

.wizard-nav .btn-primary:hover {
  background: #2a3d6a;
  border-color: #2a3d6a;
}

/* Step fade transition */
.step-fade-enter-active,
.step-fade-leave-active {
  transition: all 0.25s ease;
}

.step-fade-enter-from {
  opacity: 0;
  transform: translateX(20px);
}

.step-fade-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}
</style>