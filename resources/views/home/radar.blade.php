<script type="module">
    $(document).ready(function () {

        'use strict';

        var labelstring = @json($labels);
        var datasets_array = @json($datasets);
        var r = document.querySelector(':root');
        var rs = getComputedStyle(r);
        var blue = rs.getPropertyValue('--blue');

        $('input[type=radio]').change(function () {
            let id = this.id;
            let question_id = id.substring(0, id.indexOf('.'));
            let count = id.slice(-1);
            let right_id = id.substring(id.indexOf('.') + 1);
            let survey_number = right_id.substring(0, right_id.indexOf('.'));
            let survey_number_abs = survey_number > datasets_array[0].length ? 1 : survey_number - 1;
            datasets_array[0][survey_number_abs].data[question_id] = count - 2;
            chart.update();
        });


        for (var [i, dataset] of Object.entries(datasets_array)) {
            var RADARCHART = $('#radarChart-' + (parseInt(i) + 1));

            Chart.defaults.color = 'grey';

            var chart = new Chart(RADARCHART, {
                    type: 'radar',
                    data: {
                        labels: labelstring,
                        datasets: dataset
                    },
                    options: {
                        scales: {
                            r: {
                                startAngle: 15,
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
