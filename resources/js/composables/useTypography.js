import { useTypoStore } from '../stores/typoStore';

export function useTypography() {
    const typoStore = useTypoStore();

    function applyTypoToPaper() {
        const s = typoStore.typoState;
        const root = document.documentElement;
        
        root.style.setProperty('--paper-font-family',    s.bodyFont);
        root.style.setProperty('--paper-header-font',    s.headerFont);
        root.style.setProperty('--paper-q-font-size',    s.qFontSize + 'pt');
        root.style.setProperty('--paper-opt-font-size',  s.optFontSize + 'pt');
        root.style.setProperty('--paper-section-size',   s.sectionSize + 'pt');
        root.style.setProperty('--paper-header-size',    s.headerSize + 'pt');
        root.style.setProperty('--paper-footer-size',    (s.footerSize || 10) + 'pt');
        root.style.setProperty('--paper-line-height',    s.lineHeight);
        root.style.setProperty('--paper-word-spacing',   s.wordSpacing + 'px');
        root.style.setProperty('--paper-letter-spacing', s.letterSpacing + 'px');
        root.style.setProperty('--paper-para-spacing',   s.paraSpacing + 'px');
        root.style.setProperty('--paper-q-font-weight',  s.qWeight);
        root.style.setProperty('--paper-section-weight', s.sectionWeight);

        // Also set the generic font size which might be used as a fallback
        root.style.setProperty('--paper-font-size', s.qFontSize + 'pt');
    }

    function applyTypoPreset(id) {
        const preset = typoStore.TYPO_PRESETS.find(p => p.id === id);
        if (!preset) return;
        
        typoStore.typoState = { ...typoStore.typoState, ...JSON.parse(JSON.stringify(preset.settings)) };
        applyTypoToPaper();
    }

    function applyTypoFont(type, val) {
        if (type === 'body') typoStore.typoState.bodyFont = val;
        else if (type === 'header') typoStore.typoState.headerFont = val;
        applyTypoToPaper();
    }

    function applyTypoSize(type, val) {
        const num = parseFloat(val);
        if (type === 'q') typoStore.typoState.qFontSize = num;
        else if (type === 'opt') typoStore.typoState.optFontSize = num;
        else if (type === 'section') typoStore.typoState.sectionSize = num;
        else if (type === 'header') typoStore.typoState.headerSize = num;
        else if (type === 'footer') typoStore.typoState.footerSize = num;
        applyTypoToPaper();
    }

    function applyTypoSpacing(type, val) {
        const num = parseFloat(val);
        if (type === 'lineHeight') typoStore.typoState.lineHeight = num;
        else if (type === 'wordSpacing') typoStore.typoState.wordSpacing = num;
        else if (type === 'letterSpacing') typoStore.typoState.letterSpacing = num;
        else if (type === 'paraSpacing') typoStore.typoState.paraSpacing = num;
        applyTypoToPaper();
    }

    function applyTypoWeight(type, val) {
        const num = parseInt(val);
        if (type === 'q') typoStore.typoState.qWeight = num;
        else if (type === 'section') typoStore.typoState.sectionWeight = num;
        applyTypoToPaper();
    }

    function resetTypography() {
        applyTypoPreset('cambridge-o');
    }

    return { 
        applyTypoToPaper, 
        applyTypoPreset, 
        applyTypoFont, 
        applyTypoSize, 
        applyTypoSpacing, 
        applyTypoWeight, 
        resetTypography 
    };
}
