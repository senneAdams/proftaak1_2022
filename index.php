
<?php

require 'backend/bezoeker_stelling.php';
require 'backend/stelling.php';
require_once 'backend/Chart.php';
error_reporting(1);

session_start();
if ( ! isset($_SESSION['userID'])) {
    header("Location:login.php");
}

$bezoeker_stelling = new Bezoeker_stelling($_SESSION['admin']);

$stelling = new Stelling();
?>
    <!DOCTYPE html>
    <html>
    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script
                src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
        </script>
    </head>
    <style>
        .box {
            height: 200px;
            padding: 30px;
            width: 500px;
            display: inline-block;
        }

        .box {
            height: 200px;
            width: 350px;
        }

        .yesBox {
            width: 50px;
            height: 50px;
            display: inline-block;
        }

        .noBox {
            width: 50px;
            height: 50px;
            display: inline-block;
        }

        .btn-group button {
            background-color: #04AA6D; /* Green background */
            border: 1px solid green; /* Green border */
            color: white; /* White text */
            padding: 10px 24px; /* Some padding */
            cursor: pointer; /* Pointer/hand icon */
            float: left; /* Float the buttons side by side */
        }

        .btn-group button:not(:last-child) {
            border-right: none; /* Prevent double borders */
        }

        /* Clear floats (clearfix hack) */
        .btn-group:after {
            content: "";
            clear: both;
            display: table;
        }

        #textarea {
            width: 350px;
            height: 200px;
        }

        #delete {
            margin-top: 10px;
            width: 31.7%;
        }

        #chart {
            width: 100% !important;
            height: 100%     !important;
        }

        #chartYesWrapper{
            margin-top: 100px;
        }


        .questionWrapper {
            width: 100vw;
            height: 50%;
        }


    </style>
    <body>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Stellingen <span class="sr-only"></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Users/Stellingen toevoegen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Grafiek</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <form method="post">
        <button name="logout">logout</button>
    </form>
    <?php
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === 1) { // stelling toevoegen
        echo '         <form method="post"><div class="question">
                       <textarea id="textarea" name="question"></textarea>
                       </div>
                       <div class="btn-group">
                       <button type="submit" name="questionsubmit">stelling toevoegen</button>
                        </div></form>';
    }
    ?>
    <div class="questionWrapper">
        <form method="post">
            <?php
            try {
                $bezoeker_stelling->showQuestion();  // stellingen laten zien
            } catch (Exception $e){
                echo $e->getMessage();
            }
            ?>
        </form>
    </div>
    <div id="chartYesWrapper"><canvas id="chart"></canvas></div>
    </body>
    <script>
        <?php
        $chart = new Chart();
        ?>
        let values = <?php echo json_encode($chart->getQuestionAnswers());; ?>;
        let idArr = [];
        let voteYes = [];
        let voteNo = [];
        for (let i = 0; i < values.length; i++) {
            idArr.push(values[i].QuestionDescr);
            voteYes.push(values[i].yesCount);
            voteNo.push('-' + values[i].noCount);
        }
        console.log(idArr)



        let xValues = idArr;
        let yYesValues = voteYes;
        let yNoValues = voteNo;

        let barChartData = {
            labels: xValues,
            datasets: [{
                label: 'Stemmen voor',
                borderWidth: 1,
                data: yYesValues,
                fill: true,
                            lineTension: 0,
                            backgroundColor: "rgb(0,128,0)",
            }, {
                label: 'Stemmen tegen',
                borderWidth: 1,
                data: yNoValues
                ,
                lineTension: 0,
                backgroundColor: "rgb(242, 38, 19)",
            }]

        };


        new Chart('chart', {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                legend: {
                    position: 'top',
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
                        stacked: true,
                        position: 'top',
                    }],
                    yAxes: [{ticks: {min: -5, max: 5}}],
                }
            }
        });

    </script>
    </html>
<?php

if (isset($_POST) && ! empty($_POST)) {
    foreach ($_POST as $key => $value) {
        if (is_integer($key)) {
            $questionID    = $key;
            $questionYesNo = $value;
        } else {
            $deleteID = preg_replace('~\D~', '', $key);
        }
    }
    if (isset($questionID) && isset($questionYesNo)) { // voegt count toe aan stelling
        $bezoeker_stelling->addToCount($_SESSION['userID'], $questionID, $questionYesNo);
    }
    if (isset($deleteID)) { // verwijdert stelling
        try {
            $stelling->deleteQuestion($deleteID);
        } catch (Exception $e){
            echo $e->getMessage();
        }
    }
}
if (isset($_POST['questionsubmit'])) { // voegt stelling toe
    try {
        $stelling->insertQuestion($_POST['question']);
    } catch (Exception $e){
        echo $e->getMessage();
    }
}
if (isset($_POST['logout'])){
    require_once 'backend/users.php';
    $logout = new Users('nvt','nvt');

    try {
        $logout->Logout($_POST['logout']);
    } catch (Exception $e){
        echo $e->getMessage();
    }
}
