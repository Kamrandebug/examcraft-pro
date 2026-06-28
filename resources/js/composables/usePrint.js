import { nextTick } from 'vue';
import { useUiStore } from '../stores/uiStore';
import { useExamStore } from '../stores/examStore';

export function usePrint() {
    const uiStore = useUiStore();
    const examStore = useExamStore();

    function showPreview() {
        uiStore.showPreview = true;
        nextTick(() => {
            const src = document.getElementById('pages-container');
            const dest = document.getElementById('preview-content');
            if (src && dest) {
                // Clear and clone to avoid reference issues and scripts execution
                dest.innerHTML = '';
                const clone = src.cloneNode(true);
                // Remove scaling from clone for preview
                clone.style.transform = 'none';
                clone.style.width = '100%';
                dest.appendChild(clone);
            }
        });
    }

    function closePreview() {
        uiStore.showPreview = false;
    }

    function printPaper() {
        const content = document.getElementById('pages-container').innerHTML;
        const styles = Array.from(document.querySelectorAll('style, link[rel="stylesheet"]'))
            .map(s => s.outerHTML)
            .join('\n');
            
        const win = window.open('', '_blank');
        win.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Print Exam Paper</title>
                ${styles}
                <style>
                    @page { size: A4; margin: 0; }
                    body { margin: 0; padding: 0; background: white !important; }
                    #pages-container { transform: none !important; width: 100% !important; display: block !important; }
                    .page-canvas { page-break-after: always; box-shadow: none !important; margin: 0 auto !important; }
                    /* Hide UI elements if any leaked */
                    .block-controls, .zoom-bar, .v-ruler-wrap { display: none !important; }
                </style>
            </head>
            <body>
                <div id="pages-container">
                    ${content}
                </div>
            </body>
            </html>
        `);
        win.document.close();
        win.focus();
        
        // Wait for fonts/images to load
        setTimeout(() => {
            win.print();
            win.close();
        }, 800);
    }

    async function exportPDF() {
        const { jsPDF } = window.jspdf;
        const pages = document.querySelectorAll('.page-canvas');
        const pdf = new jsPDF('p', 'mm', 'a4');
        
        for (let i = 0; i < pages.length; i++) {
            // Hide controls temporarily
            const controls = pages[i].querySelectorAll('.block-controls');
            controls.forEach(c => c.style.display = 'none');
            
            const canvas = await html2canvas(pages[i], { 
                scale: 2,
                useCORS: true,
                logging: false
            });
            
            // Restore controls
            controls.forEach(c => c.style.display = '');
            
            const imgData = canvas.toDataURL('image/png');
            if (i > 0) pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, 0, 210, 297);
        }
        
        pdf.save((examStore.paperMeta?.title || 'examcraft') + '.pdf');
    }

    function exportAnswerKey() {
        let html = `
            <html>
            <head>
                <title>Answer Key - ${examStore.paperMeta?.title || 'Untitled'}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 40px; }
                    h2 { border-bottom: 2px solid #333; padding-bottom: 10px; }
                    ol { column-count: 2; }
                    li { margin-bottom: 8px; font-size: 14px; }
                    .ak-num { font-weight: bold; margin-right: 10px; }
                </style>
            </head>
            <body>
                <h2>Answer Key: ${examStore.paperMeta?.title || 'Untitled'}</h2>
                <p>Subject: ${examStore.paperMeta?.subject || 'N/A'} | Date: ${examStore.paperMeta?.date || 'N/A'}</p>
                <ol>
        `;
        
        examStore.pages.forEach(page => {
            page.blocks.forEach(block => {
                if (block.type === 'mcq') {
                    html += `<li><span class="ak-num">Q${block.qNum}:</span> ${block.correct}</li>`;
                }
            });
        });
        
        html += `
                </ol>
            </body>
            </html>
        `;
        
        const win = window.open('', '_blank');
        win.document.write(html);
        win.document.close();
        win.print();
    }

    return { showPreview, closePreview, printPaper, exportPDF, exportAnswerKey };
}
