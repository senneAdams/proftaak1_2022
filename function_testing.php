<?php
require_once 'backend/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
</head>
<body>

<canvas id="canvas" style="display: block; width: 1417px; height: 708px;" width="1417" height="708"></canvas>

<script>
    var barChartData = {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
            label: 'Dataset 1',
            borderWidth: 1,
            data: [
                -10,
                -40,
                80
            ]
        }, {
            label: 'Dataset 2',
            borderWidth: 1,
            data: [
                10,
                20,
                30,
            ]
        }]

    };

    var ctx = document.getElementById('canvas').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Chart.js Bar Chart'
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var value = tooltipItem.yLabel;
                        return value < 0 ? -value : value;
                    }
                }
            },
            scales: {
                xAxes: [{
                    stacked: false
                }],
                yAxes: [{
                    stacked: false,
                    ticks: {
                        callback: function(value, index, values) {
                            return value < 0 ? -value : value;
                        }
                    }
                }]
            }
        }
    });
</script>
</body>
</html>


