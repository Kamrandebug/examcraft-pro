export function useRuler() {
    function drawRuler(canvas, scrollTop, scrollHeight) {
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const h = canvas.height;
        const w = canvas.width;
        ctx.clearRect(0, 0, w, h);
        
        // draw tick marks every 10px in scroll space
        // major ticks every 50px with mm labels
        const ratio = h / scrollHeight;
        
        const rootStyle = getComputedStyle(document.documentElement);
        const tickColor = rootStyle.getPropertyValue('--ruler-tick').trim() || '#666';
        const labelColor = rootStyle.getPropertyValue('--ruler-label').trim() || '#999';

        for (let mm = 0; mm <= scrollHeight; mm += 10) {
            const y = (mm - scrollTop) * ratio;
            if (y < 0 || y > h) continue;
            
            const isMajor = mm % 50 === 0;
            ctx.strokeStyle = tickColor;
            ctx.lineWidth = isMajor ? 1.5 : 0.75;
            
            ctx.beginPath();
            ctx.moveTo(isMajor ? 0 : w * 0.4, y);
            ctx.lineTo(w, y);
            ctx.stroke();
            
            if (isMajor) {
                ctx.fillStyle = labelColor;
                ctx.font = '8px sans-serif';
                ctx.fillText(Math.round(mm) + '', 1, y - 1);
            }
        }
    }

    function updateIndicator(indicatorEl, scrollTop, scrollHeight, clientH, rulerH) {
        if (!indicatorEl) return;
        const ratio = rulerH / scrollHeight;
        const top = scrollTop * ratio;
        const height = clientH * ratio;
        indicatorEl.style.top = top + 'px';
        indicatorEl.style.height = height + 'px';
    }

    return { drawRuler, updateIndicator };
}
