import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useAutoPaperStore = defineStore('autoPaper', () => {
    const paperTitle = ref('');
    const schoolName = ref('');
    const paperDate = ref('');
    const grade = ref('');
    const subject = ref('');
    const selectedMcqs = ref([]);

    const totalMarks = computed(() => selectedMcqs.value.length);

    function setPaperMeta({ title, school, date, grade: g, subject: s }) {
        paperTitle.value = title;
        schoolName.value = school;
        paperDate.value = date;
        grade.value = g;
        subject.value = s;
    }

    function setSelectedMcqs(mcqs) {
        selectedMcqs.value = mcqs;
    }

    function reset() {
        paperTitle.value = '';
        schoolName.value = '';
        paperDate.value = '';
        grade.value = '';
        subject.value = '';
        selectedMcqs.value = [];
    }

    return {
        paperTitle,
        schoolName,
        paperDate,
        grade,
        subject,
        selectedMcqs,
        totalMarks,
        setPaperMeta,
        setSelectedMcqs,
        reset,
    };
});
