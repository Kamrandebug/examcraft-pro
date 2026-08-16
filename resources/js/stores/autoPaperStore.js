import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

const DEFAULT_INSTRUCTIONS = `Write in soft pencil.
Do not use staples, paper clips, glue or correction fluid.
Write your name, centre number and candidate number on the Answer Sheet in the spaces provided unless this has been done for you.
DO NOT WRITE IN ANY BARCODES.

There are forty questions on this paper. Answer all questions. For each question there are four possible answers A, B, C and D.
Choose the one you consider correct and record your choice in soft pencil on the separate Answer Sheet.

Read the instructions on the Answer Sheet very carefully.

Each correct answer will score one mark. A mark will not be deducted for a wrong answer.
Any rough working should be done in this booklet.
Electronic calculators may be used.`;

const DEFAULT_ADDITIONAL_MATERIALS = `Multiple Choice Answer Sheet
Soft clean eraser
Soft pencil (type B or HB is recommended)`;

export const useAutoPaperStore = defineStore('autoPaper', () => {
    const paperTitle = ref('');
    const schoolName = ref('');
    const paperDate = ref('');
    const grade = ref('');
    const subject = ref('');
    const paperCode = ref('');
    const session = ref('');
    const duration = ref('');
    const additionalMaterials = ref(DEFAULT_ADDITIONAL_MATERIALS);
    const instructions = ref(DEFAULT_INSTRUCTIONS);
    const logoFile = ref(null);
    const logoDataUrl = ref(null);
    const selectedMcqs = ref([]);

    const totalMarks = computed(() => selectedMcqs.value.length);

    function setPaperMeta({
        title,
        school,
        date,
        grade: g,
        subject: s,
        paperCode: code,
        session: ses,
        duration: dur,
        additionalMaterials: materials,
        instructions: instr,
    }) {
        if (title !== undefined) paperTitle.value = title;
        if (school !== undefined) schoolName.value = school;
        if (date !== undefined) paperDate.value = date;
        if (g !== undefined) grade.value = g;
        if (s !== undefined) subject.value = s;
        if (code !== undefined) paperCode.value = code;
        if (ses !== undefined) session.value = ses;
        if (dur !== undefined) duration.value = dur;
        if (materials !== undefined) additionalMaterials.value = materials;
        if (instr !== undefined) instructions.value = instr;
    }

    function setLogo({ file, dataUrl }) {
        if (file !== undefined) logoFile.value = file;
        if (dataUrl !== undefined) logoDataUrl.value = dataUrl;
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
        paperCode.value = '';
        session.value = '';
        duration.value = '';
        additionalMaterials.value = DEFAULT_ADDITIONAL_MATERIALS;
        instructions.value = DEFAULT_INSTRUCTIONS;
        logoFile.value = null;
        logoDataUrl.value = null;
        selectedMcqs.value = [];
    }

    return {
        paperTitle,
        schoolName,
        paperDate,
        grade,
        subject,
        paperCode,
        session,
        duration,
        additionalMaterials,
        instructions,
        logoFile,
        logoDataUrl,
        selectedMcqs,
        totalMarks,
        setPaperMeta,
        setLogo,
        setSelectedMcqs,
        reset,
    };
});
