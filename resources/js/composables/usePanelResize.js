import { useUiStore } from '../stores/uiStore';

export function usePanelResize() {
    const uiStore = useUiStore();

    function initResize(handleEl, side) {
        let dragging = false;
        let startX = 0;
        let startWidth = 0;

        const onMouseDown = (e) => {
            dragging = true;
            startX = e.clientX;
            startWidth = side === 'left' 
                ? uiStore.leftPanelWidth 
                : uiStore.rightPanelWidth;
            
            document.body.style.cursor = 'col-resize';
            document.body.style.userSelect = 'none';
            handleEl.classList.add('dragging');
        };

        const onMouseMove = (e) => {
            if (!dragging) return;
            
            const diff = side === 'left' 
                ? e.clientX - startX 
                : startX - e.clientX;
            
            const newWidth = Math.min(480, Math.max(180, startWidth + diff));
            
            if (side === 'left') uiStore.leftPanelWidth = newWidth;
            else uiStore.rightPanelWidth = newWidth;
        };

        const onMouseUp = () => {
            if (!dragging) return;
            dragging = false;
            document.body.style.cursor = '';
            document.body.style.userSelect = '';
            handleEl.classList.remove('dragging');
        };

        handleEl.addEventListener('mousedown', onMouseDown);
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);

        // Return cleanup function
        return () => {
            handleEl.removeEventListener('mousedown', onMouseDown);
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        };
    }

    return { initResize };
}
