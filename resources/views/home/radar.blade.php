<script>
    $(document).ready(function () {

        'use strict';

        var labelstring = @json($labels);
        var datasets_array = @json($datasets);
        var r = document.querySelector(':root');
        var rs = getComputedStyle(r);
        var blue = rs.getPropertyValue('--blue');

        for (var [i, dataset] of Object.entries(datasets_array)) {
            var RADARCHART = $('#radarChart-' + (parseInt(i) + 1));

            Chart.defaults.color = 'grey';

            new Chart(RADARCHART, {
                    type: 'radar',
                    data: {
                        labels: labelstring,
                        datasets: dataset
                    },
                    options: {
                        scales: {
                            r: {
                                min: -2,
                                max: 2,
                                ticks: {
                                    maxTicksLimit: 5,
                                    color: 'darkgrey',
                                    backdropColor: 'transparent',
                                    z: 5,
                                },
                                pointLabels: {
                                    // callback: function (pointLabel, index, labels) {
                                    // return window.innerWidth > 1600 ? pointLabel : ' ';
                                    // },
                                    font: {
                                        size: window.innerWidth > 1600 ? 16 : 12,
                                        weight: function (pointLabel, index, labels) {
                                            return pointLabel.label[0][0] === '*' ? 'bold' : 'normal';
                                        },
                                    },
                                    color: function (pointLabel, index, labels) {
                                        return pointLabel.label[0][0] === '*' ? blue : 'grey';
                                    },
                                },
                                angleLines: {
                                    color: 'grey' // lines radiating from the center
                                }
                                ,
                                grid: {
                                    color: 'grey'
                                }

                            },
                        },
                        tooltips: {
                            enabled: true,
                            callbacks:
                                {
                                    label: function (tooltipItem, data) {
                                        return data.datasets[tooltipItem.datasetIndex].label + ' : ' + data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                    }
                                }
                        },
                    },
                    plugins: [{
                        beforeInit: function (chart) {
                            chart.data.labels.forEach(function (e, i, a) {
                                if (/\n/.test(e)) {
                                    a[i] = e.split(/\n/)
                                }
                            })
                        },
                    }],
                    responsive: true
                }
            );
        }
    })
    ;
</script>
