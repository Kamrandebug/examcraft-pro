<template>
  <div class="auto-stepper">
    <div class="stepper-track">
      <div
        v-for="step in 3"
        :key="step"
        class="stepper-step"
        :class="stepClass(step)"
        @click="onStepClick(step)"
      >
        <div class="stepper-circle">
          <i v-if="step < currentStep" class="fa fa-check"></i>
          <span v-else>{{ step }}</span>
        </div>
        <div class="stepper-label">{{ stepLabels[step - 1] }}</div>
      </div>

      <!-- Connector lines between circles -->
      <div
        v-for="step in 2"
        :key="'line-' + step"
        class="stepper-connector"
        :class="{ completed: step < currentStep }"
        :style="{ left: connectorPositions[step - 1] + '%', width: connectorWidth + '%' }"
      />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  currentStep: { type: Number, required: true },
});

const emit = defineEmits(['go-to-step']);

const stepLabels = ['Paper Info', 'Settings', 'Load MCQs'];

// Connector positions: between circle 1-2 and 2-3
// Each circle is at 16.66%, 50%, 83.33% (evenly spaced in 3 columns)
const connectorPositions = [33.33, 66.66];
const connectorWidth = 16.66;

function stepClass(step) {
  if (step < props.currentStep) return 'completed';
  if (step === props.currentStep) return 'active';
  return 'upcoming';
}

function onStepClick(step) {
  if (step < props.currentStep) {
    emit('go-to-step', step);
  }
}
</script>

<style scoped>
.auto-stepper {
  padding: 0 0 8px;
  margin-bottom: 24px;
}

.stepper-track {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  position: relative;
  padding: 0 20px;
}

.stepper-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  flex: 1;
  z-index: 1;
}

.stepper-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  font-weight: 700;
  transition: all 0.3s ease;
  cursor: default;
}

.stepper-step.completed .stepper-circle {
  background: #C9A84C;
  color: #1B2A4A;
  cursor: pointer;
}

.stepper-step.active .stepper-circle {
  background: #1B2A4A;
  color: #fff;
  border: 2px solid #C9A84C;
  box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.2);
}

.stepper-step.upcoming .stepper-circle {
  background: #fff;
  color: #5A6070;
  border: 2px solid #DDD8CC;
}

.stepper-label {
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  font-weight: 600;
  text-align: center;
  max-width: 100px;
  transition: color 0.3s ease;
}

.stepper-step.completed .stepper-label {
  color: #C9A84C;
}

.stepper-step.active .stepper-label {
  color: #1B2A4A;
  font-weight: 700;
}

.stepper-step.upcoming .stepper-label {
  color: #5A6070;
}

.stepper-connector {
  position: absolute;
  top: 20px;
  height: 2px;
  background: #DDD8CC;
  z-index: 0;
  transition: background 0.3s ease;
}

.stepper-connector.completed {
  background: #C9A84C;
}

@media (max-width: 576px) {
  .stepper-track {
    flex-direction: column;
    align-items: flex-start;
    padding: 0;
    gap: 0;
  }

  .stepper-step {
    flex-direction: row;
    gap: 12px;
    margin-bottom: 16px;
  }

  .stepper-label {
    text-align: left;
    max-width: none;
  }

  .stepper-connector {
    display: none;
  }
}
</style>