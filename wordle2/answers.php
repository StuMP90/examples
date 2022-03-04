<?php
// Autoload
include_once __DIR__ . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <title>Wordle Word Finder</title>
    </head>
    <body>
        
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="/">Wordle Word Finder 2.0</a>
            </div>
        </nav>
        
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <h2>Recent and Upcoming Answers</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
<?php
    // Answer ID 259 is for Friday 4th March 2022
    // This is a timestamp of 1646395200 for midday GMT
    // This is a timestamp of 1646352000 for start of day
    // 86400 seconds in a day
    // So, ID 259 corresponds to 19055 days on timestamp
    // A delta of 18796
    //
    // So, get timestamp for current day, subtract the offset and you have today's ID
    $cur_dy = date("j");
    $cur_mn = date("n");
    $cur_yr = date("Y");
    $cur_ts = mktime(0, 0, 0, $cur_mn, $cur_dy, $cur_yr);
    $cur_dy_num = (int) ($cur_ts / 86400);
    $cur_dy_wordle = $cur_dy_num - 18796;
    echo "<p>Current Server Date: " . date("Y/m/d",$cur_ts) . "</p>";
    echo "<p>Current Wordle Day Number: " . htmlspecialchars($cur_dy_wordle) . "</p>";
    echo "<hr />";
    
    // List the previous 5 answers , today's and the next 5
    $wordle_min = $cur_dy_wordle - 6;
    $wordle_max = $cur_dy_wordle + 6;
    
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=' . $_ENV['DB_DBSE'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $stmtstr = "SELECT id,word from " . $_ENV['TBL_SRCANS'] . " WHERE id > :wmnd AND  id < :wxnd ORDER BY id ASC";
        $stmt = $dbh->prepare($stmtstr);
        $stmt->bindParam(':wmnd', $wordle_min);
        $stmt->bindParam(':wxnd', $wordle_max);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $human_ts = ($row[0] + 18796) * 86400;
            
            echo "<p>" . (($cur_dy_wordle == $row[0]) ? "<strong>" : "") . date("l jS F Y",$human_ts) . " : " . $row[1] . (($cur_dy_wordle == $row[0]) ? "</strong>" : "") . "</p>";
        }
    } catch (PDOException $e) {

    }
?>
                    <p>Note: The answers above are based on the source code as of 2nd March 2022. The NYT could change them at any time.</p>
                    <hr />
                </div>
            </div>
        </div>
        
        <div class="container">
            <p><a href="/analysis">Finder words by letter frequency and position analysis</a></p>
            <p><a href="/answers.php">Answers for cheats...</a></p>
            <p><a href="/">Word Finder</a></p>
            <p>Stuart Millington 2022</p>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </body>
</html>