<template>
  <div class="page-canvas" :id="'page-canvas-' + pageIndex" ref="pageEl">
    <!-- Cover Page Header (index 0) -->
    <div v-if="pageIndex === 0" style="padding:20px 24px 0 24px;">
      <!-- TOP: Logo | Org Info -->
      <div style="display:flex;align-items:flex-start;gap:0;margin-bottom:14px;">
        <div style="flex-shrink:0;margin-right:14px;">
          <div style="background:#000;color:#fff;width:70px;height:70px;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:8px;font-weight:700;text-align:center;padding:4px;box-sizing:border-box;">
            <div style="font-size:7px;font-weight:400;margin-bottom:2px;">Cambridge</div>
            <div style="font-size:9px;font-weight:700;border:1px solid #fff;padding:1px 4px;margin-bottom:2px;">O Level</div>
          </div>
        </div>
        <div style="flex:1;">
          <div style="font-family:var(--paper-header-font);font-size:var(--paper-header-size);font-weight:700;color:#111;margin-bottom:2px;">
            {{ examStore.paperMeta.organization }}
          </div>
          <div style="font-family:var(--paper-font-family);font-size:var(--paper-opt-font-size);color:#333;">
            {{ examStore.paperMeta.subtitle }}
          </div>
        </div>
      </div>

      <hr style="border:none;border-top:1px solid #333;margin:0 0 8px 0;"/>

      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0;">
        <div>
          <div style="font-family:var(--paper-font-family);font-size:var(--paper-header-size);font-weight:700;color:#111;letter-spacing:var(--paper-letter-spacing);">
            {{ examStore.paperMeta.subject }}
          </div>
          <div style="font-family:var(--paper-font-family);font-size:var(--paper-opt-font-size);color:#222;margin-top:2px;">
            {{ examStore.paperMeta.paperType }}
          </div>
        </div>
        <div style="text-align:right;">
          <div style="font-family:var(--paper-font-family);font-size:12pt;font-weight:700;color:#111;">
            {{ examStore.paperMeta.code }}
          </div>
          <div style="font-family:var(--paper-font-family);font-size:var(--paper-opt-font-size);color:#222;margin-top:2px;">
            {{ examStore.paperMeta.date }}
          </div>
          <div style="font-family:var(--paper-font-family);font-size:var(--paper-opt-font-size);font-weight:700;color:#111;margin-top:2px;">
            {{ examStore.paperMeta.duration }}
          </div>
        </div>
      </div>

      <div v-if="examStore.paperMeta.materials" style="margin-top:10px;font-family:var(--paper-font-family);font-size:var(--paper-opt-font-size);">
        <table style="border-collapse:collapse;width:100%;">
          <tbody>
            <tr>
              <td style="padding:1px 0;vertical-align:top;width:140px;color:#222;">
                {{ examStore.paperMeta.materialsLabel || 'Additional Materials:' }}
              </td>
              <td style="padding:1px 0 1px 6px;color:#222;" v-html="examStore.paperMeta.materials"></td>
            </tr>
          </tbody>
        </table>
      </div>

      <hr style="border:none;border-top:1px solid #666;margin:12px 0 0 0;"/>

      <div v-if="examStore.paperMeta.instructions || examStore.paperMeta.instrHeading" style="margin-top:10px;padding:0;">
        <div v-if="examStore.paperMeta.instrHeading" style="font-family:var(--paper-font-family);font-size:var(--paper-opt-font-size);font-weight:700;color:#111;margin-bottom:6px;">
          {{ examStore.paperMeta.instrHeading }}
        </div>
        <div style="font-family:var(--paper-font-family);font-size:var(--paper-opt-font-size);color:#222;line-height:var(--paper-line-height);" v-html="examStore.paperMeta.instructions"></div>
      </div>
    </div>

    <!-- Drop Zone -->
    <div class="questions-drop-zone" :id="'questions-drop-zone-' + pageIndex">
      <BlockRenderer 
        v-for="block in pageData.blocks" 
        :key="block.id" 
        :block="block" 
      />
    </div>

    <!-- Footer -->
    <div class="paper-info-bar">
      <div :id="'footer-left-' + pageIndex" style="flex:1;">
        {{ pageIndex === 0 ? examStore.coverFooter.left : examStore.pageFooter.left }}
      </div>
      <div :id="'footer-center-' + pageIndex" style="flex:1;text-align:center;display:flex;align-items:center;justify-content:center;gap:6px;">
        {{ pageIndex === 0 ? examStore.coverFooter.center : examStore.pageFooter.center }}
      </div>
      <div :id="'footer-right-' + pageIndex" style="flex:1;text-align:right;font-weight:500;">
        {{ pageIndex === 0 ? examStore.coverFooter.right : examStore.pageFooter.right }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Sortable from 'sortablejs';
import { useExamStore } from '../../stores/examStore';
import { useUiStore } from '../../stores/uiStore';
import BlockRenderer from './BlockRenderer.vue';

const props = defineProps({
  pageIndex: Number,
  pageData: Object
});

const examStore = useExamStore();
const uiStore = useUiStore();
const pageEl = ref(null);

onMounted(() => {
  const dropZone = pageEl.value.querySelector('.questions-drop-zone');
  Sortable.create(dropZone, {
    group: 'blocks',
    animation: 150,
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    onEnd(evt) {
      const page = examStore.pages[props.pageIndex];
      const moved = page.blocks.splice(evt.oldIndex, 1)[0];
      page.blocks.splice(evt.newIndex, 0, moved);
    }
  });
});
</script>
