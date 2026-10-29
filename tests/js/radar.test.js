import { beforeEach, describe, expect, it, vi } from 'vitest';

// Chart.js zeichnet in happy-dom nicht; die Attrappe merkt sich Leinwand und Optionen
const created = vi.hoisted(() => []);
vi.mock('chart.js/auto', () => {
    class Chart {
        static defaults = {};

        constructor(canvas, config) {
            this.canvas = canvas;
            this.config = config;
            this.update = vi.fn();
            created.push(this);
        }
    }
    return { default: Chart };
});

const { applyAnswer, initRadarCharts, isCoreCompetence, pointLabel, splitLabels } = await import(
    '../../resources/js/pages/radar.js'
);

// Wie Helper::GetSurveysDataset: pro Diagramm die Runden, je mit Fragen-ids und Werten
function datasets() {
    return [
        [
            { label: '1. Selbsteinschätzung', dataset_ids: [11, 12, 13], data: [0, 0, 0] },
            { label: 'Einschätzung Leitende', dataset_ids: [11, 12, 13], data: [0, 0, 0] },
        ],
        [{ label: '1. Selbsteinschätzung', dataset_ids: [11, 12, 13], data: [1, 1, 1] }],
    ];
}

describe('Beschriftungen', () => {
    it('macht aus Zeilenumbrüchen mehrzeilige Beschriftungen', () => {
        expect(splitLabels(['1.1 Cevitechnik', '2.1 Vielseitigkeit und\nKreativität'])).toEqual([
            '1.1 Cevitechnik',
            ['2.1 Vielseitigkeit und', 'Kreativität'],
        ]);
    });

    it('zeigt schmal nur die Nummer', () => {
        expect(pointLabel('*2.2 Führen, Leiten', true)).toBe('*2.2');
        expect(pointLabel('*2.2 Führen, Leiten', false)).toBe('*2.2 Führen, Leiten');
    });

    it('erkennt Kernkompetenzen am Stern, auch mehrzeilig', () => {
        expect(isCoreCompetence('*1.1 Cevitechnik')).toBe(true);
        expect(isCoreCompetence(['*2.3 Planungs- und', 'Organisationsfähigkeit'])).toBe(true);
        expect(isCoreCompetence('1.2 J+S-Sportfachtechnik')).toBe(false);
    });
});

describe('Antwort antippen', () => {
    it('trägt die 1. Selbsteinschätzung in den ersten Datensatz ein (Antwort-id 4 = ++ = 2)', () => {
        const data = datasets();

        expect(applyAnswer(data, '12.1.1.24')).toBe(true);
        expect(data[0][0].data).toEqual([0, 2, 0]);
        expect(data[0][1].data).toEqual([0, 0, 0]);
    });

    it('trägt die Leitenden-Runde in den letzten Datensatz ein, wenn es keine 2. Runde gibt', () => {
        const data = datasets();

        expect(applyAnswer(data, '13.3.1.31')).toBe(true);
        expect(data[0][1].data).toEqual([0, 0, -1]);
    });

    it('ändert nichts bei unbekannter Frage', () => {
        const data = datasets();

        expect(applyAnswer(data, '99.1.9.92')).toBe(false);
        expect(data[0][0].data).toEqual([0, 0, 0]);
    });
});

describe('Diagramme', () => {
    beforeEach(() => {
        created.length = 0;
        document.body.innerHTML = `
            <div id="schmal"><canvas id="radarChart-1"></canvas></div>
            <div id="breit"><canvas id="radarChart-2"></canvas></div>
            <input type="radio" id="12.1.1.23">`;
        Object.defineProperty(document.getElementById('schmal'), 'clientWidth', { value: 300 });
        Object.defineProperty(document.getElementById('breit'), 'clientWidth', { value: 900 });
    });

    it('legt pro Leinwand ein Diagramm an, schmal mit Nummern, breit mit Namen', () => {
        initRadarCharts({ labels: ['*1.1 Cevitechnik'], datasets: datasets() });

        expect(created.map((chart) => chart.canvas.id)).toEqual(['radarChart-1', 'radarChart-2']);
        const [narrow, wide] = created.map((chart) => chart.config.options);
        expect(narrow.aspectRatio).toBe(1);
        expect(narrow.scales.r.pointLabels.callback('*1.1 Cevitechnik')).toBe('*1.1');
        expect(wide.aspectRatio).toBe(1.4);
        expect(wide.scales.r.pointLabels.callback('*1.1 Cevitechnik')).toBe('*1.1 Cevitechnik');
    });

    it('zeichnet nach einer angetippten Antwort das erste Diagramm neu', () => {
        const config = { labels: ['a', 'b', 'c'], datasets: datasets() };
        initRadarCharts(config);

        document.getElementById('12.1.1.23').dispatchEvent(new Event('change', { bubbles: true }));

        expect(config.datasets[0][0].data).toEqual([0, 1, 0]);
        expect(created[0].update).toHaveBeenCalledTimes(1);
        expect(created[1].update).not.toHaveBeenCalled();
    });
});
