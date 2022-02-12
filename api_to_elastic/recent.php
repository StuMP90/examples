<?php
require_once './getmydata.php';
// Get data for last 3 months (allow 7 days for averaging)
$usa_start_timestamp = time() - (97 * 24 * 60 * 60);
$usa_data = getUsaData($usa_start_timestamp);

// Finish England data 5 days ago due to data completion delays
$england_start_timestamp = time() - (102 * 24 * 60 * 60);
$england_end_timestamp = time() - (5 * 24 * 60 * 60);
$england_data = getEnglandData($england_start_timestamp,$england_end_timestamp);
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <title>Covid History Examples</title>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            // Load Charts and the corechart package.
            google.charts.load('current', {'packages':['corechart']});

            // Draw the first chart when Charts is loaded.
            google.charts.setOnLoadCallback(drawUSAChart);

            // Draw the second chart when Charts is loaded.
            google.charts.setOnLoadCallback(drawEnglandChart);

            // Callback that draws the first chart.
            function drawUSAChart() {

                // Create the data table.
                var data = google.visualization.arrayToDataTable([
                  ['Date', 'Avg Cases', 'Avg Deaths'],
<?php
                foreach($usa_data as $key => $val) {
                    echo("['" . $val['dtestr']  . "'," . $val['avgcases']  . "," . $val['avgdeaths']  . "],");
                }
?>
                ]);

                // Set options.
                var options = {title:'USA Daily Average (7D) for 3 months',
                    curveType: 'function',
                    height:500,
                    vAxis: { scaleType: 'log' },
                    legend: { position: 'top' },
                    curveType: 'function',
                    chartArea:{left: 100, right: 25, top: 50, bottom: 100, width:'auto',height:'auto'},
                };

                // Instantiate and draw the chart.
                var chart = new google.visualization.LineChart(document.getElementById('USA_chart_div'));
                chart.draw(data, options);
            }

            // Callback that draws the second chart.
            function drawEnglandChart() {

                // Create the data table.
                var data = google.visualization.arrayToDataTable([
                  ['Date', 'Avg Cases', 'Avg Deaths'],
<?php
                foreach($england_data as $key => $val) {
                    echo("['" . $val['dtestr']  . "'," . $val['avgcases']  . "," . $val['avgdeaths']  . "],");
                }
?>
                ]);

                // Set options.
                var options = {title:'England Daily Average (7D) for 3 months',
                    curveType: 'function',
                    height:500,
                    vAxis: { scaleType: 'log' },
                    legend: { position: 'top' },
                    curveType: 'function',
                    chartArea:{left: 100, right: 25, top: 50, bottom: 100, width:'auto',height:'auto'},
                };

                // Instantiate and draw the chart.
                var chart = new google.visualization.LineChart(document.getElementById('England_chart_div'));
                chart.draw(data, options);
            }
        </script>
    </head>
    <body>
        
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="/">Covid History</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="/">Full History</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/recent.php">Last 3 Months</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/month.php">Last Month</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <div class="container">
            <h1>Covid History Examples</h1>
            <p class="lead">Using Data stored in Elasticsearch</p>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div id="USA_chart_div" style="border: 1px solid #ccc"></div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div id="England_chart_div" style="border: 1px solid #ccc"></div>
                </div>
            </div>
            <p>Dashboard by Stuart Millington 2022. Data provided by <a href="https://corona.lmao.ninja/">disease.sh - Open Disease Data</a> and <a href="https://coronavirus.data.gov.uk/">coronavirus.data.gov.uk</a>.</p>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </body>
</html>