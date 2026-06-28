<template>
  <div class="block-mcq">
    <div class="q-block-header">
      <div class="q-block-number">{{ block.qNum || 1 }}</div>
      <div class="q-stem q-block-body" v-html="block.stem || 'Question stem goes here...'"></div>
    </div>

    <div class="q-options mcq-options" :class="block.layout || 'mcq-options-2col'" :style="{ marginTop: typoStore.typoState.paraSpacing + 'px' }">
      <div v-for="(opt, idx) in block.options" :key="idx" class="mcq-option-row">
        <div class="mcq-option-letter">{{ String.fromCharCode(65 + idx) }}</div>
        <div class="mcq-option-text" v-html="typeof opt === 'string' ? opt : opt.text"></div>
      </div>
    </div>

    <div class="answer-box" v-if="examStore.styleState.showAnswerBoxes">
      <!-- Answer box UI -->
    </div>

    <span class="marks-badge" v-if="block.showMarks">[{{ block.marks || 1 }}]</span>
  </div>
</template>

<script setup>
import { useExamStore } from '../../stores/examStore';
import { useTypoStore } from '../../stores/typoStore';

const props = defineProps({
  block: Object
});

const examStore = useExamStore();
const typoStore = useTypoStore();
</script>
