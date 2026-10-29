import Chart from 'chart.js/auto';

/**
 * Radar-Diagramme der Kompetenzen (Quali, Vergleich, Profil, Dashboard, Druckansicht). Daten aus
 * <script type="application/json" id="radar-config"> (home/radar.blade.php): { labels, datasets }, datasets[i] gehört
 * zur Leinwand #radarChart-(i+1). Auf der Quali führt das erste Diagramm angetippte Antworten sofort nach.
 */

/** Beschriftungen mit Zeilenumbruch werden für Chart.js zu mehrzeiligen Beschriftungen (Array). */
export function splitLabels(labels) {
    return labels.map((label) => (/\n/.test(label) ? label.split(/\n/) : label));
}

/** Schmal (Handy, Dashboard-Kachel): nur die Nummer ("*1.1"), der ganze Name steht im Tooltip. */
export function pointLabel(label, compact) {
    return compact ? String(label).split(' ')[0] : label;
}

/** Kernkompetenzen beginnen mit "*" (Helper::GetSurveyLabels), auch bei mehrzeiligen Beschriftungen. */
export function isCoreCompetence(label) {
    return String(Array.isArray(label) ? label[0] : label).startsWith('*');
}

/**
 * Wert einer angetippten Antwort im ersten Diagramm eintragen. Radio-id aus survey.blade.php:
 * "<Frage-id>.<Runde>.<Fragenummer><Antwort-id>", Runde 1 = 1. Selbsteinschätzung, 2 = zweite, 3 = Leitende.
 * Die Leitenden-Runde ist der letzte Datensatz, wenn es weniger Datensätze als Runden gibt. Der Wert ist die letzte
 * Ziffer der id minus 2 (Antworten "-", "0", "+", "++" haben die ids 1 bis 4).
 * Gibt true zurück, wenn ein Wert geändert wurde.
 */
export function applyAnswer(datasets, radioId) {
    const questionId = Number(radioId.substring(0, radioId.indexOf('.')));
    const rest = radioId.substring(radioId.indexOf('.') + 1);
    const round = Number(rest.substring(0, rest.indexOf('.')));
    const rounds = datasets[0];
    const index = round > rounds.length ? 1 : round - 1;
    const position = rounds[index]?.dataset_ids.indexOf(questionId) ?? -1;
    if (position < 0) {
        return false;
    }
    rounds[index].data[position] = Number(radioId.slice(-1)) - 2;
    return true;
}

function chartOptions(compact, colors) {
    return {
        aspectRatio: compact ? 1 : 1.4,
        scales: {
            r: {
                startAngle: 15,
                min: -2,
                max: 2,
                ticks: { maxTicksLimit: 5, color: colors.text, backdropColor: 'transparent', z: 5 },
                pointLabels: {
                    callback: (label) => pointLabel(label, compact),
                    font: {
                        size: window.innerWidth > 1600 ? 16 : 12,
                        weight: (context) => (isCoreCompetence(context.label) ? 'bold' : 'normal'),
                    },
                    color: (context) => (isCoreCompetence(context.label) ? colors.blue : colors.text),
                },
                angleLines: { color: colors.grid },
                grid: { color: colors.grid },
            },
        },
    };
}

export function initRadarCharts(config, root = document) {
    const style = getComputedStyle(root.documentElement);
    const colors = {
        blue: style.getPropertyValue('--blue').trim(),
        text: style.getPropertyValue('--color-text-primary').trim(),
        grid: style.getPropertyValue('--color-chart-grid').trim(),
    };
    Chart.defaults.color = colors.text;

    const charts = config.datasets.map((datasets, index) => {
        const canvas = root.getElementById(`radarChart-${index + 1}`);
        if (!canvas) {
            return null;
        }
        const compact = canvas.parentElement.clientWidth < 560;
        return new Chart(canvas, {
            type: 'radar',
            data: { labels: splitLabels(config.labels), datasets },
            options: chartOptions(compact, colors),
        });
    });

    root.addEventListener('change', (event) => {
        if (event.target.matches?.('input[type=radio]') && applyAnswer(config.datasets, event.target.id)) {
            charts[0]?.update();
        }
    });

    return charts;
}

const configElement = document.getElementById('radar-config');
if (configElement) {
    initRadarCharts(JSON.parse(configElement.textContent));
}
