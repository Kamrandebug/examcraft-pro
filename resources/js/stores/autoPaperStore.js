import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';

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

const STORAGE_KEY = 'examcraft.autoPaper';

/**
 * Read previously persisted paper settings (survives page refreshes).
 */
function loadPersisted() {
    try {
        if (typeof localStorage === 'undefined') return null;
        const raw = localStorage.getItem(STORAGE_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch {
        return null;
    }
}

export const useAutoPaperStore = defineStore('autoPaper', () => {
    const saved = loadPersisted();

    const paperTitle = ref(saved?.paperTitle ?? '');
    const schoolName = ref(saved?.schoolName ?? '');
    const paperDate = ref(saved?.paperDate ?? '');
    const grade = ref(saved?.grade ?? '');
    const subject = ref(saved?.subject ?? '');
    const paperCode = ref(saved?.paperCode ?? '');
    const session = ref(saved?.session ?? '');
    const duration = ref(saved?.duration ?? '');
    const additionalMaterials = ref(saved?.additionalMaterials ?? DEFAULT_ADDITIONAL_MATERIALS);
    const instructions = ref(saved?.instructions ?? DEFAULT_INSTRUCTIONS);
    const logoFile = ref(null);
    const logoDataUrl = ref(saved?.logoDataUrl ?? null);
    const selectedMcqs = ref(saved?.selectedMcqs ?? []);

    const totalMarks = computed(() => selectedMcqs.value.length);

    // Persist every change so a refresh keeps the wizard state intact.
    watch(
        [
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
            logoDataUrl,
            selectedMcqs,
        ],
        () => {
            try {
                if (typeof localStorage === 'undefined') return;
                localStorage.setItem(STORAGE_KEY, JSON.stringify({
                    paperTitle: paperTitle.value,
                    schoolName: schoolName.value,
                    paperDate: paperDate.value,
                    grade: grade.value,
                    subject: subject.value,
                    paperCode: paperCode.value,
                    session: session.value,
                    duration: duration.value,
                    additionalMaterials: additionalMaterials.value,
                    instructions: instructions.value,
                    logoDataUrl: logoDataUrl.value,
                    selectedMcqs: selectedMcqs.value,
                }));
            } catch {
                // Ignore write failures (private mode, quota, etc.).
            }
        },
        { deep: true }
    );

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
