<template>
  <div 
    class="question-block" 
    :class="{ selected: examStore.selectedBlockId === block.id }"
    :data-id="block.id"
    @click="examStore.selectedBlockId = block.id"
  >
    <!-- Block Controls -->
    <div class="block-controls">
      <button class="block-ctrl-btn" title="Move Up" @click.stop="moveBlockUp(block.id)">
        <i class="fa-solid fa-chevron-up"></i>
      </button>
      <button class="block-ctrl-btn" title="Move Down" @click.stop="moveBlockDown(block.id)">
        <i class="fa-solid fa-chevron-down"></i>
      </button>
      <button class="block-ctrl-btn" title="Duplicate" @click.stop="duplicateBlock(block.id)">
        <i class="fa-solid fa-copy"></i>
      </button>
      <button class="block-ctrl-btn danger" title="Delete" @click.stop="deleteBlock(block.id)">
        <i class="fa-solid fa-trash-can"></i>
      </button>
    </div>

    <McqBlock     v-if="block.type === 'mcq'"      :block="block" />
    <SectionBlock v-else-if="block.type === 'section'" :block="block" />
    <TextBlock    v-else-if="block.type === 'text'"    :block="block" />
    <ImageBlock   v-else-if="block.type === 'image'"   :block="block" />
    <TableBlock   v-else-if="block.type === 'table'"   :block="block" />
    <DividerBlock v-else-if="block.type === 'divider'" :block="block" />
  </div>
</template>

<script setup>
import { useExamStore } from '../../stores/examStore';
import { useBlockOperations } from '../../composables/useBlockOperations';
import McqBlock from './McqBlock.vue';
import SectionBlock from './SectionBlock.vue';
import TextBlock from './TextBlock.vue';
import ImageBlock from './ImageBlock.vue';
import TableBlock from './TableBlock.vue';
import DividerBlock from './DividerBlock.vue';

const props = defineProps({
  block: Object
});

const examStore = useExamStore();
const { deleteBlock, duplicateBlock, moveBlockUp, moveBlockDown } = useBlockOperations();
</script>
