import { useUiStore } from '../stores/uiStore';

export function useToast() {
    const uiStore = useUiStore();

    function showToast(message, type = 'info', duration = 3000) {
        const toast = {
            id: Date.now() + Math.random(),
            message,
            type, // 'success' | 'error' | 'info' | 'warning'
            visible: true
        };
        
        uiStore.toasts.push(toast);

        setTimeout(() => {
            const idx = uiStore.toasts.findIndex(t => t.id === toast.id);
            if (idx !== -1) {
                uiStore.toasts.splice(idx, 1);
            }
        }, duration);
    }

    return { showToast };
}
