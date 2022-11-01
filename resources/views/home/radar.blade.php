<script>
    $(document).ready(function () {

        'use strict';

        var brandleader = 'rgba(51, 179, 90, 0.2)';
        var branduser_first = 'rgba(179,181,198,0.2)';
        var branduser_second = 'rgba(50,181,198,0.2)';

        var surveys = @json($surveys);
        var user = @json(Auth::user());

        for (var [i, survey] of Object.entries(surveys)){
            var RADARCHART  = $('#radarChart-'+(parseInt(i)+1));

            var labelstring = [];
            var datasets = [];
            var datapoints_first = [];
            var datapoints_second = [];
            var datapoints_leader = [];


            survey.chapters.forEach(chapter => {
                var questions = chapter.questions;
                questions.forEach(question => {
                    labelstring.push(question.question.competence);
                    datapoints_first.push(question.answer_first.count);
                    datapoints_second.push(question.answer_second.count);
                    datapoints_leader.push(question.answer_leader.count);
                });
            });

            datasets.push({
                label: '1. Selbsteinschätzung',
                backgroundColor: branduser_first,
                borderWidth: 2,
                borderColor: branduser_first,
                pointBackgroundColor: branduser_first,
                pointBorderColor: "#fff",
                pointHoverBackgroundColor: "#fff",
                pointHoverBorderColor: branduser_first,
                data: datapoints_first
            });
            if(survey.survey_status_id >= @json(config('status.survey_2offen'))){
                datasets.push({
                    label: '2. Selbsteinschätzung',
                    backgroundColor: branduser_second,
                    borderWidth: 2,
                    borderColor: branduser_second,
                    pointBackgroundColor: branduser_second,
                    pointBorderColor: "#fff",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: branduser_second,
                    data: datapoints_second
                });
            }
            if(user.role_id != @json(config('status.role_Teilnehmer'))){
                datasets.push({
                            label: 'Leiter Qualifikation',
                            backgroundColor: brandleader,
                            borderWidth: 2,
                            borderColor: brandleader,
                            pointBackgroundColor: brandleader,
                            pointBorderColor: "#fff",
                            pointHoverBackgroundColor: "#fff",
                            pointHoverBorderColor: brandleader,
                            data: datapoints_leader
                        });
            }

            var radarChart = new Chart(RADARCHART, {
                type: 'radar',
                data: {
                    labels:  labelstring,
                    datasets: datasets
                },
                options: {
                    scales: {
                        r: {
                            ticks: {
                                min: -2,
                                max: 2,
                                maxTicksLimit: 5,
                            },
                            pointLabels: {
                                // callback: function (pointLabel, index, labels) {
                                    // return window.innerWidth > 1600 ? pointLabel : ' ';
                                // },
                                font: {
                                    size: window.innerWidth > 1600 ? 16 : 12,
                                    // weight: 800,
                                },
                            },
                        }
                    },
                    tooltips: {
                        enabled: true,
                        callbacks: {
                            label: function (tooltipItem, data) {
                                return data.datasets[tooltipItem.datasetIndex].label + ' : ' + data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            }
                        }
                    },
                },
                plugins: [{
                    beforeInit: function (chart) {
                        chart.data.labels.forEach(function (e, i, a) {
                            if(e[0] === 'V'){
                               // e = '<strong>' + e + '</strong>';
                            }
                            if (/\n/.test(e)) {
                                a[i] = e.split(/\n/)
                            }
                        })
                    },
                }],
                responsive: true
            });
        }
    });
</script>
