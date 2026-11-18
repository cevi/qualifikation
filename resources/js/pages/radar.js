import Chart from 'chart.js/auto';

/**
 * Radar-Diagramme der Kompetenzen (Quali, Vergleich, Profil, Dashboard, Druckansicht). Daten aus
 * <script type="application/json" id="radar-config"> (home/radar.blade.php): { labels, datasets }, datasets[i] gehört
 * zur Leinwand #radarChart-(i+1). Auf der Quali führt das erste Diagramm angetippte Antworten sofort nach.
 */

/** Längste Zeile einer Beschriftung im Radar; längere Namen brechen an Leerzeichen um. */
export const LABEL_LINE_LENGTH = 24;

/**
 * Lange Beschriftung auf mehrere Zeilen verteilen. Chart.js verkleinert sonst den Kreis, bis die Namen auf einer Zeile
 * links und rechts Platz haben (auf breiten Bildschirmen blieb das Radar klein mit viel Leerraum).
 */
export function wrapLabel(label, max = LABEL_LINE_LENGTH) {
    const lines = [];
    String(label).split(' ').forEach((word) => {
        const last = lines.length - 1;
        if (last >= 0 && `${lines[last]} ${word}`.length <= max) {
            lines[last] = `${lines[last]} ${word}`;
        } else {
            lines.push(word);
        }
    });
    return lines.length > 1 ? lines : String(label);
}

/** Beschriftungen mit Zeilenumbruch werden mehrzeilig (Array), lange ohne Umbruch werden umgebrochen. */
export function splitLabels(labels) {
    return labels.map((label) => (/\n/.test(label) ? label.split(/\n/) : wrapLabel(label)));
}

/**
 * Beschriftung ohne den Stern der Kernkompetenzen (die sind fett und blau). Schmal (Handy, Dashboard-Kachel): nur die
 * Nummer ("1.1"), der ganze Name steht im Tooltip.
 */
export function pointLabel(label, compact) {
    const lines = (Array.isArray(label) ? [...label] : [String(label)]);
    lines[0] = lines[0].replace(/^\*/, '');
    if (compact) {
        return lines[0].split(' ')[0];
    }
    return Array.isArray(label) ? lines : lines[0];
}

/** Skala wie die Antworten ("-", "0", "+", "++" haben die Werte -1 bis 2); die Mitte (-2) bleibt leer. */
export const SCALE_LABELS = { '-1': '-', 0: '0', 1: '+', 2: '++' };

export function scaleLabel(value) {
    return SCALE_LABELS[value] ?? '';
}

/** Dunkle Variante der Farben aus Helper::GetDataset (heller, damit sie auf Grau lesbar bleiben). */
export const DARK_SERIES = {
    first: { color: '#9ca3af', fill: 'rgba(156,163,175,0)' },
    second: { color: '#76a9fa', fill: 'rgba(118,169,250,0.16)' },
    leader: { color: '#ff8a4c', fill: 'rgba(255,138,76,0.12)' },
};

/** Im Dark Mode die Farben der Runden ersetzen (Datensätze mit "series" aus Helper::GetDataset). */
export function applyDarkColors(datasets) {
    datasets.flat().forEach((dataset) => {
        const dark = DARK_SERIES[dataset.series];
        if (dark) {
            Object.assign(dataset, {
                borderColor: dark.color,
                backgroundColor: dark.fill,
                pointBackgroundColor: dark.color,
                pointBorderColor: '#1f2937',
                pointHoverBorderColor: dark.color,
            });
        }
    });
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

function chartOptions(compact, colors, labels) {
    // context.label ist schon ohne Stern (pointLabel), darum über den Index in den Original-Beschriftungen nachsehen
    const isCore = (context) => isCoreCompetence(labels[context.index] ?? '');
    return {
        aspectRatio: compact ? 1 : 1.4,
        scales: {
            r: {
                startAngle: 15,
                min: -2,
                max: 2,
                // Die Skala liegt auf der ersten Linie: Hintergrund in Kartenfarbe, damit sie lesbar bleibt. Schmal ist
                // dafür kein Platz; die Legende der Seite erklärt die Werte.
                ticks: { display: !compact, stepSize: 1, callback: scaleLabel, color: colors.text, backdropColor: colors.card, backdropPadding: 2, font: { size: 12, weight: 'bold' }, z: 5 },
                pointLabels: {
                    callback: (label) => pointLabel(label, compact),
                    font: (context) => ({
                        size: window.innerWidth > 1600 ? 16 : 12,
                        weight: isCore(context) ? 'bold' : 'normal',
                    }),
                    color: (context) => (isCore(context) ? colors.core : colors.text),
                },
                angleLines: { color: colors.grid },
                grid: { color: colors.grid },
            },
        },
    };
}

export function initRadarCharts(config, root = document) {
    const style = getComputedStyle(root.documentElement);
    const dark = root.documentElement.classList.contains('dark');
    const colors = {
        // Wie das Etikett «Kernkompetenz» auf Quali und Vergleich
        core: dark ? '#a4cafe' : '#1e429f',
        // Hintergrund der Karte (bg-white bzw. dark:bg-gray-800)
        card: dark ? '#1f2937' : '#ffffff',
        text: style.getPropertyValue('--color-text-primary').trim(),
        grid: style.getPropertyValue('--color-chart-grid').trim(),
    };
    Chart.defaults.color = colors.text;
    if (dark) {
        applyDarkColors(config.datasets);
    }
    const labels = splitLabels(config.labels);

    const charts = config.datasets.map((datasets, index) => {
        const canvas = root.getElementById(`radarChart-${index + 1}`);
        if (!canvas) {
            return null;
        }
        const compact = canvas.parentElement.clientWidth < 560;
        return new Chart(canvas, {
            type: 'radar',
            data: { labels, datasets },
            options: chartOptions(compact, colors, labels),
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
