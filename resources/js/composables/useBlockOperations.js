import { useExamStore } from '../stores/examStore';

const BLOCK_DEFAULTS = {
    mcq: {
        type: 'mcq',
        qNum: 0, 
        stem: 'Question text here',
        options: ['Option A', 'Option B', 'Option C', 'Option D'],
        correct: 'A', 
        marks: 1,
        layout: '1col',        // '1col' | '2col' | 'inline'
        tableVariant: null,    // null | 1 | 2
        hasImage: false, 
        imageData: null, 
        imagePos: 'above',
        hasAnswerBox: false,
        showMarks: true
    },
    section: {
        type: 'section',
        title: 'Section A', 
        subtitle: '',
        showDivider: true, 
        align: 'left'
    },
    text: {
        type: 'text',
        content: 'Enter text here...',
        fontSize: 11, 
        align: 'left', 
        bold: false, 
        italic: false
    },
    image: {
        type: 'image',
        src: null, 
        caption: '',
        width: 100, 
        align: 'center'
    },
    table: {
        type: 'table',
        rows: 3, 
        cols: 3,
        headers: ['Column 1', 'Column 2', 'Column 3'],
        data: [['','',''],['','','']],
        variant: 1
    },
    divider: {
        type: 'divider',
        style: 'solid', 
        thickness: 1
    }
};

export function useBlockOperations() {
    const examStore = useExamStore();

    function generateId() {
        return 'block-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    }

    function addBlock(type) {
        if (!BLOCK_DEFAULTS[type]) return;
        
        const defaults = JSON.parse(JSON.stringify(BLOCK_DEFAULTS[type]));
        defaults.id = generateId();
        
        // Auto-assign question number for mcq blocks
        if (type === 'mcq') {
            defaults.qNum = getNextQNum();
        }
        
        const page = examStore.pages[examStore.activePageIdx];
        if (!page) return;
        
        page.blocks.push(defaults);
        examStore.selectedBlockId = defaults.id;
        
        // scroll canvas to bottom so new block is visible
        setTimeout(() => {
            const container = document.getElementById('pages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 50);
    }

    function deleteBlock(id) {
        examStore.pages.forEach(page => {
            const idx = page.blocks.findIndex(b => b.id === id);
            if (idx !== -1) page.blocks.splice(idx, 1);
        });
        if (examStore.selectedBlockId === id) examStore.selectedBlockId = null;
        renumberBlocks();
    }

    function duplicateBlock(id) {
        examStore.pages.forEach(page => {
            const idx = page.blocks.findIndex(b => b.id === id);
            if (idx !== -1) {
                const clone = JSON.parse(JSON.stringify(page.blocks[idx]));
                clone.id = generateId();
                page.blocks.splice(idx + 1, 0, clone);
                examStore.selectedBlockId = clone.id;
            }
        });
        renumberBlocks();
    }

    function moveBlockUp(id) {
        examStore.pages.forEach(page => {
            const idx = page.blocks.findIndex(b => b.id === id);
            if (idx > 0) {
                const tmp = page.blocks[idx];
                page.blocks[idx] = page.blocks[idx - 1];
                page.blocks[idx - 1] = tmp;
            }
        });
    }

    function moveBlockDown(id) {
        examStore.pages.forEach(page => {
            const idx = page.blocks.findIndex(b => b.id === id);
            if (idx !== -1 && idx < page.blocks.length - 1) {
                const tmp = page.blocks[idx];
                page.blocks[idx] = page.blocks[idx + 1];
                page.blocks[idx + 1] = tmp;
            }
        });
    }

    function renumberBlocks() {
        let qNum = examStore.qNumberStart || 1;
        examStore.pages.forEach(page => {
            page.blocks.forEach(block => {
                if (block.type === 'mcq') {
                    block.qNum = qNum++;
                }
            });
        });
    }

    function getNextQNum() {
        let max = (examStore.qNumberStart || 1) - 1;
        examStore.pages.forEach(page => {
            page.blocks.forEach(b => {
                if (b.type === 'mcq' && b.qNum > max) max = b.qNum;
            });
        });
        return max + 1;
    }

    function addPage() {
        examStore.pages.push({
            id: 'page-' + Date.now(),
            blocks: []
        });
        examStore.activePageIdx = examStore.pages.length - 1;
    }

    function clearAllBlocks() {
        if (confirm('Clear all blocks from all pages?')) {
            examStore.pages.forEach(p => p.blocks = []);
            examStore.selectedBlockId = null;
        }
    }

    return {
        addBlock, deleteBlock, duplicateBlock,
        moveBlockUp, moveBlockDown, renumberBlocks,
        addPage, clearAllBlocks
    };
}
