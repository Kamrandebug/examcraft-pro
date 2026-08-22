import { defineStore } from 'pinia';
import { ref, reactive } from 'vue';

export const useExamStore = defineStore('exam', () => {
    const paperMeta = ref({
        title: 'Physics 5054 — Paper 1',
        organization: 'Cambridge Assessment International Education',
        subtitle: 'Cambridge Ordinary Level',
        subject: 'PHYSICS',
        code: '5054/11',
        duration: '1 hour',
        paperType: 'Paper 1 Multiple Choice',
        date: 'May/June 2025',
        instructions: 'Write in soft pencil.<br>Do not use staples, paper clips, glue or correction fluid.<br>There are forty questions on this paper. Answer all questions.<br>Each correct answer will score one mark. No negative marking.',
        instrHeading: 'READ THESE INSTRUCTIONS FIRST',
        materials: 'Multiple Choice Answer Sheet',
        materialsLabel: 'Additional Materials:',
        logo: null
    });

    const pages = ref([
        {
            id: 'p1',
            blocks: []
        }
    ]);

    const styleState = reactive({
        pageSize: 'A4',
        orientation: 'portrait',
        numberingStyle: 'standard'
    });

    // Global overrides for style/layout from user prompt
    const qNumberStart = ref('1');
    const globalOptsLayout = ref('2col');
    const showAnswerBoxes = ref(true);
    const showMarks = ref(true);
    const twoColumn = ref(false);

    const coverFooter = reactive({
        left: 'IB19 06_5054_11/3RP<br>© UCLES 2025',
        center: '',
        right: ''
    });

    const pageFooter = reactive({
        left: '© UCLES 2025',
        center: '',
        right: '[Turn over]',
        pagePos: 'right'
    });

    const selectedBlockId = ref(null);
    const activePageIdx = ref(0);

    const editPaperId = ref(null);
    const isEditMode = ref(false);

    function loadFromSnapshot(pd) {
        if (!pd) return;
        if (pd.pages) pages.value = pd.pages;
        if (pd.paperMeta) paperMeta.value = { ...paperMeta.value, ...pd.paperMeta };
        if (pd.styleState) Object.assign(styleState, pd.styleState);
        if (pd.coverFooter) Object.assign(coverFooter, pd.coverFooter);
        if (pd.pageFooter) Object.assign(pageFooter, pd.pageFooter);

        if (pd.globalOpts) {
            const g = pd.globalOpts;
            if (g.qNumberStart !== undefined) qNumberStart.value = g.qNumberStart;
            if (g.globalOptsLayout !== undefined) globalOptsLayout.value = g.globalOptsLayout;
            if (g.showAnswerBoxes !== undefined) showAnswerBoxes.value = g.showAnswerBoxes;
            if (g.showMarks !== undefined) showMarks.value = g.showMarks;
            if (g.twoColumn !== undefined) twoColumn.value = g.twoColumn;
        }
    }

    function getSnapshot() {
        return {
            pages: pages.value,
            paperMeta: paperMeta.value,
            styleState: styleState,
            coverFooter: coverFooter,
            pageFooter: pageFooter,
            globalOpts: {
                qNumberStart: qNumberStart.value,
                globalOptsLayout: globalOptsLayout.value,
                showAnswerBoxes: showAnswerBoxes.value,
                showMarks: showMarks.value,
                twoColumn: twoColumn.value
            }
        };
    }

    return {
        paperMeta,
        pages,
        styleState,
        qNumberStart,
        globalOptsLayout,
        showAnswerBoxes,
        showMarks,
        twoColumn,
        coverFooter,
        pageFooter,
        selectedBlockId,
        activePageIdx,
        editPaperId,
        isEditMode,
        loadFromSnapshot,
        getSnapshot
    };
});
