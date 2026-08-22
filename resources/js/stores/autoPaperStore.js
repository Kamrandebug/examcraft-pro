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

    // ── Original state fields (unchanged) ────────────────────
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

    // ── New wizard state ─────────────────────────────────────
    const currentStep = ref(1);
    const mcqList = ref([]);
    const mcqLoading = ref(false);
    const mcqError = ref(null);
    const selectedMcqIds = ref([]);

    // ── New edit mode state ──────────────────────────────────
    const editPaperId = ref(null);
    const isEditMode = ref(false);
    const editLoading = ref(false);
    const editError = ref(null);

    // ── Computed ─────────────────────────────────────────────
    const totalMarks = computed(() => selectedMcqs.value.length);

    const isStep1Valid = computed(() =>
        !!(grade.value?.trim() && subject.value?.trim())
    );

    const selectedMcqCount = computed(() => selectedMcqIds.value.length);

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
                
                // Exclude runtime-only flags like isEditMode, editLoading, etc.
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

    // ── Original actions (unchanged) ─────────────────────────
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

    // ── New wizard actions ───────────────────────────────────
    function nextStep() {
        if (currentStep.value < 3) currentStep.value++;
    }

    function prevStep() {
        if (currentStep.value > 1) currentStep.value--;
    }

    function goToStep(n) {
        if (n >= 1 && n <= 3) currentStep.value = n;
    }

    async function loadMcqs() {
        this.mcqLoading = true;
        this.mcqError = null;
        this.mcqList = [];
        try {
            console.log('loadMcqs called with:', this.grade, this.subject);
            const res = await window.axios.get(
                '/api/question-bank/filter',
                { params: { grade: this.grade, subject: this.subject } }
            );

            // API returns { success: true, data: [...] }
            if (res.data && res.data.success) {
                this.mcqList = res.data.data;
            } else {
                this.mcqError = 'No questions found for this grade/subject.';
            }
        } catch (err) {
            this.mcqError = err.response?.data?.message
                || 'Failed to load MCQs. Check your connection.';
            console.error('loadMcqs error:', err);
        } finally {
            this.mcqLoading = false;
        }
    }

    function toggleMcq(id) {
        const idx = selectedMcqIds.value.indexOf(id);
        if (idx === -1) {
            selectedMcqIds.value.push(id);
        } else {
            selectedMcqIds.value.splice(idx, 1);
        }
    }

    function selectAllMcqs() {
        selectedMcqIds.value = mcqList.value.map(q => q.id);
    }

    function clearMcqSelection() {
        selectedMcqIds.value = [];
    }

    async function loadPaperForEdit(id) {
        // 1. Clear any stale state immediately
        this.paperTitle = '';
        this.paperCode = '';
        this.session = '';
        this.duration = '';
        this.schoolName = '';
        this.paperDate = '';
        this.grade = '';
        this.subject = '';
        this.additionalMaterials = '';
        this.instructions = '';
        this.logoDataUrl = null;
        this.selectedMcqs = [];
        this.selectedMcqIds = [];
        this.mcqList = [];
        this.currentStep = 1;
        this.isEditMode = false;
        this.editPaperId = null;

        // 2. Also clear localStorage so watcher doesn't restore old values
        if (typeof localStorage !== 'undefined') {
            localStorage.removeItem(STORAGE_KEY);
        }

        this.editLoading = true;
        this.editError = null;
        try {
            const res = await window.axios.get(`/api/user/papers/${id}`);
            
            const paper = res.data.paper ?? res.data;

            // Hydrate paper meta fields
            this.paperTitle = paper.title ?? '';
            this.subject = paper.subject ?? '';
            this.grade = paper.grade ?? '';
            this.schoolName = paper.school_name ?? '';
            this.paperDate = paper.exam_date ?? '';

            // Hydrate from paper_data JSON column
            const pd = (typeof paper.paper_data === 'string')
                ? JSON.parse(paper.paper_data)
                : (paper.paper_data ?? {});

            this.paperCode = pd.paperCode ?? '';
            this.session = pd.session ?? '';
            this.duration = pd.duration ?? '';
            this.additionalMaterials = pd.additionalMaterials ?? DEFAULT_ADDITIONAL_MATERIALS;
            this.instructions = pd.instructions ?? DEFAULT_INSTRUCTIONS;
            this.logoDataUrl = pd.logoDataUrl ?? null;

            // Re-hydrate selected MCQs list
            this.selectedMcqs = pd.selectedMcqs ?? [];
            this.selectedMcqIds = (pd.selectedMcqs ?? []).map(q => q.id);
            this.mcqList = pd.selectedMcqs ?? [];

            // Set edit mode
            this.editPaperId = id;
            this.isEditMode = true;

            // Start at step 1 as requested by user
            this.currentStep = 1;

        } catch (err) {
            this.editError = err.response?.data?.message
                ?? 'Failed to load paper for editing.';
            console.error('[EditMode] FAILED:', err.response ?? err);
        } finally {
            this.editLoading = false;
        }
    }

    async function updatePaper() {
        if (!this.editPaperId) return false;
        try {
            const payload = {
                title: this.paperTitle,
                subject: this.subject,
                grade: this.grade,
                school_name: this.schoolName,
                exam_date: this.paperDate,
                paper_data: {
                    paperCode: this.paperCode,
                    session: this.session,
                    duration: this.duration,
                    additionalMaterials: this.additionalMaterials,
                    instructions: this.instructions,
                    logoDataUrl: this.logoDataUrl,
                    selectedMcqs: this.selectedMcqs,
                    // Keep grade/subject in paper_data too for consistency
                    grade: this.grade,
                    subject: this.subject
                }
            };
            await window.axios.put(`/api/user/papers/${this.editPaperId}`, payload);
            return true;
        } catch (err) {
            console.error('updatePaper error:', err);
            return false;
        }
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
        currentStep.value = 1;
        mcqList.value = [];
        mcqLoading.value = false;
        mcqError.value = null;
        selectedMcqIds.value = [];
        editPaperId.value = null;
        isEditMode.value = false;
        editLoading.value = false;
        editError.value = null;
    }

    return {
        // Original state
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
        // New wizard state
        currentStep,
        mcqList,
        mcqLoading,
        mcqError,
        selectedMcqIds,
        editPaperId,
        isEditMode,
        editLoading,
        editError,
        // Computed
        isStep1Valid,
        selectedMcqCount,
        // Original actions
        setPaperMeta,
        setLogo,
        setSelectedMcqs,
        // New wizard actions
        nextStep,
        prevStep,
        goToStep,
        loadMcqs,
        toggleMcq,
        selectAllMcqs,
        clearMcqSelection,
        loadPaperForEdit,
        updatePaper,
        reset,
    };
});