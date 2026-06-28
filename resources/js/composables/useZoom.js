import { useUiStore } from '../stores/uiStore';

export function useZoom() {
    const uiStore = useUiStore();

    function setZoom(val) {
        uiStore.currentZoom = Math.min(1.4, Math.max(0.4, val));
    }

    function zoomIn()  { setZoom(uiStore.currentZoom + 0.1); }
    function zoomOut() { setZoom(uiStore.currentZoom - 0.1); }
    function zoomFit() { setZoom(0.85); }

    return { setZoom, zoomIn, zoomOut, zoomFit };
}
