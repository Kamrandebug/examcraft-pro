import { useUiStore } from '../stores/uiStore';

export const THEMES = {
    day:       { label: 'Day',        icon: '🌤',  canvasBg: '#dde1e9', desc: 'GitHub Light' },
    afternoon: { label: 'Afternoon',  icon: '�', canvasBg: '#16132a', desc: 'Dracula Pro' },
    night:     { label: 'Night',      icon: '🌙',  canvasBg: '#2a2d38', desc: 'One Dark Pro' },
    latenight: { label: 'Late Night', icon: '�',  canvasBg: '#16161e', desc: 'Tokyo Night' }
};

export function useTheme() {
    const uiStore = useUiStore();

    function setTheme(name) {
        if (!THEMES[name]) return;
        
        document.documentElement.setAttribute('data-theme', name);
        uiStore.theme = name;
        localStorage.setItem('examcraft-theme', name);
        
        // update canvas background color via CSS variable
        document.documentElement.style.setProperty('--canvas-bg', THEMES[name].canvasBg);
    }

    function initTheme() {
        const saved = localStorage.getItem('examcraft-theme');
        if (saved && THEMES[saved]) {
            setTheme(saved);
            return;
        }
        
        // Default to latenight as per user instructions
        setTheme('latenight');
    }

    return { THEMES, setTheme, initTheme };
}
