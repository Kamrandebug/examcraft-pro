<template>
  <div class="block-table">
    <table class="table table-bordered">
      <thead v-if="block.showHead">
        <tr>
          <th v-for="(col, idx) in block.cols" :key="idx">{{ col.label }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, rIdx) in block.rows" :key="rIdx">
          <td v-for="(cell, cIdx) in row" :key="cIdx" v-html="cell"></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
const props = defineProps({
  block: Object
});
</script>
