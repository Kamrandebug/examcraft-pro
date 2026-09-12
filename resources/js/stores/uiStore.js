import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useUiStore = defineStore('ui', () => {
    const currentZoom = ref(0.85);
    const leftPanelWidth = ref(255);
    const rightPanelWidth = ref(320);
    const leftCollapsed = ref(false);
    const rightCollapsed = ref(false);
    const activeRTab = ref('properties');
    const theme = ref('latenight');
    const showProjectModal = ref(false);
    const showPreview = ref(false);
    const showCtVariantModal = ref(false);
    const showThemeDropdown = ref(false);
    const showUserDropdown = ref(false);
    const showSavedBadge = ref(false);
    const footerShowPageNum = ref(true);
    const toasts = ref([]);
    const currentView = ref(window.initialMode || 'home');

    function setView(view) {
        currentView.value = view;
    }

    return {
        currentZoom,
        leftPanelWidth,
        rightPanelWidth,
        leftCollapsed,
        rightCollapsed,
        activeRTab,
        theme,
        showProjectModal,
        showPreview,
        showCtVariantModal,
        showThemeDropdown,
        showUserDropdown,
        showSavedBadge,
        toasts,
        currentView,
        setView,
    };
});
