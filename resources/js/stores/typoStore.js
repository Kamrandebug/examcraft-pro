import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useTypoStore = defineStore('typo', () => {
    const typoState = ref({
        bodyFont: 'Arial, sans-serif',
        headerFont: 'Arial, sans-serif',
        qFontSize: 11,
        optFontSize: 11,
        sectionSize: 12,
        headerSize: 13,
        footerSize: 10,
        lineHeight: 1.5,
        wordSpacing: 0,
        letterSpacing: 0,
        paraSpacing: 4,
        qWeight: 400,
        sectionWeight: 700
    });

    const TYPO_PRESETS = [
        {
            id: 'cambridge-o',
            name: 'Cambridge O Level',
            desc: 'Standard MCQ paper\nArial 11pt · 1.4 line',
            sample: 'Aa Bb Cc 123',
            font: 'Arial, sans-serif',
            settings: {
                bodyFont: 'Arial, sans-serif',
                headerFont: 'Arial, sans-serif',
                qFontSize: 11,
                optFontSize: 11,
                sectionSize: 12,
                headerSize: 13,
                footerSize: 10,
                lineHeight: 1.4,
                wordSpacing: 0,
                letterSpacing: 0,
                paraSpacing: 3,
                qWeight: 400,
                sectionWeight: 700
            }
        },
        {
            id: 'cambridge-a',
            name: 'Cambridge A Level',
            desc: 'Structured questions\nArial 11pt · 1.5 line',
            sample: 'Aa Bb Cc 123',
            font: 'Arial, sans-serif',
            settings: {
                bodyFont: 'Arial, sans-serif',
                headerFont: 'Arial, sans-serif',
                qFontSize: 11,
                optFontSize: 11,
                sectionSize: 12,
                headerSize: 13,
                footerSize: 10,
                lineHeight: 1.5,
                wordSpacing: 0.5,
                letterSpacing: 0,
                paraSpacing: 4,
                qWeight: 400,
                sectionWeight: 700
            }
        },
        {
            id: 'cambridge-pre-u',
            name: 'Cambridge Pre-U',
            desc: 'Essay style\nTimes 11pt · 1.5 line',
            sample: 'Aa Bb Cc 123',
            font: "'Times New Roman', serif",
            settings: {
                bodyFont: "'Times New Roman', serif",
                headerFont: 'Arial, sans-serif',
                qFontSize: 11.5,
                optFontSize: 11,
                sectionSize: 12,
                headerSize: 13,
                footerSize: 10,
                lineHeight: 1.5,
                wordSpacing: 0,
                letterSpacing: 0.1,
                paraSpacing: 5,
                qWeight: 400,
                sectionWeight: 700
            }
        },
        {
            id: 'cambridge-ig',
            name: 'Cambridge IGCSE',
            desc: 'International\nArial 10.5pt · 1.4 line',
            sample: 'Aa Bb Cc 123',
            font: 'Arial, sans-serif',
            settings: {
                bodyFont: 'Arial, sans-serif',
                headerFont: 'Arial, sans-serif',
                qFontSize: 10.5,
                optFontSize: 10.5,
                sectionSize: 11,
                headerSize: 13,
                footerSize: 9.5,
                lineHeight: 1.4,
                wordSpacing: 0,
                letterSpacing: 0,
                paraSpacing: 3,
                qWeight: 400,
                sectionWeight: 700
            }
        },
        {
            id: 'garamond-classic',
            name: 'Garamond Classic',
            desc: 'Old-style serif\nEB Garamond 12pt · 1.6',
            sample: 'Aa Bb Cc 123',
            font: "'EB Garamond', Garamond, serif",
            settings: {
                bodyFont: "'EB Garamond', Garamond, serif",
                headerFont: "'EB Garamond', Garamond, serif",
                qFontSize: 12,
                optFontSize: 11.5,
                sectionSize: 13,
                headerSize: 14,
                footerSize: 10,
                lineHeight: 1.6,
                wordSpacing: 0,
                letterSpacing: 0.2,
                paraSpacing: 5,
                qWeight: 400,
                sectionWeight: 600
            }
        },
        {
            id: 'baskerville-book',
            name: 'Baskerville Book',
            desc: 'Academic textbook\nLibre Baskerville 11pt',
            sample: 'Aa Bb Cc 123',
            font: "'Libre Baskerville', Baskerville, serif",
            settings: {
                bodyFont: "'Libre Baskerville', Baskerville, serif",
                headerFont: 'Arial, sans-serif',
                qFontSize: 11,
                optFontSize: 10.5,
                sectionSize: 12,
                headerSize: 13,
                footerSize: 9.5,
                lineHeight: 1.55,
                wordSpacing: 0.5,
                letterSpacing: 0.1,
                paraSpacing: 4,
                qWeight: 400,
                sectionWeight: 700
            }
        }
    ];

    return {
        typoState,
        TYPO_PRESETS
    };
});
