<?php
/* 
 * Basic analysis to find letter frequency, best words, etc.
 */

// Autoload
include_once dirname(__DIR__,1) . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__,1));
$dotenv->load();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <title>5 Wetter Word Analysis</title>
    </head>
    <body>
        
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="/">5 Wetter Word Analysis</a>
            </div>
        </nav>
<?php
try {
    $dbh = new PDO('mysql:host=localhost;dbname=' . $_ENV['DB_DBSE'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

    $word_count = 0;
    $letter_count = array('a' => 0,'b' => 0,'c' => 0,'d' => 0,'e' => 0,'f' => 0,'g' => 0,'h' => 0,'i' => 0,'j' => 0,'k' => 0,'l' => 0,'m' => 0,'n' => 0,'o' => 0,'p' => 0,'q' => 0,'r' => 0,'s' => 0,'t' => 0,'u' => 0,'v' => 0,'w' => 0,'x' => 0,'y' => 0,'z' => 0,);
    
    // Select all 5 letter words from combined dictionary
    $stmtstr = "SELECT word from " . $_ENV['TBL_COMB'] . " WHERE LENGTH(word) = 5";
    $stmt = $dbh->prepare($stmtstr);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        // The dictionary should only contain the letters a-z, but better to be safe...
         $clean_word = preg_replace('/[^a-z]/','',$row[0] ?? '');
         if (($clean_word != '') && (strlen($clean_word) == 5)) {
             $word_count++;
             foreach (str_split(strtolower($clean_word)) as $key => $val) {
                 $letter_count[$val] = $letter_count[$val] + 1;
             }
         }
    }
?>
        <div class="container">
            <h2>Frequency Analysis</h2>
            <p>Words: <?= $word_count ?></p>
<?php
    $letter_count_sorted = $letter_count;
    arsort($letter_count_sorted);
?>
            <h3>Letters by Frequency:</h3>
            <div class="row">
<?php
    foreach ($letter_count_sorted as $key => $val) {
        echo('                <div class="col-md-2">');
        echo('                    ' . strtoupper($key) . ': ' . $val);
        echo('                </div>');
    }
?>
            </div>
<?php
    // Assign value to each letter based on letter counts
    $letter_value = $letter_count_sorted;

    // Now re-read all the words and give them a value
    $word_value_list = array();
    // Select all 5 letter words from combined dictionary
    $stmtstr = "SELECT word from " . $_ENV['TBL_COMB'] . " WHERE LENGTH(word) = 5";
    $stmt = $dbh->prepare($stmtstr);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $word_value = 0;
        $word_prevletters = "";
        // The dictionary should only contain the letters a-z, but better to be safe...
        $clean_word = preg_replace('/[^a-z]/','',$row[0] ?? '');
        if (($clean_word != '') && (strlen($clean_word) == 5)) {
            foreach (str_split(strtolower($clean_word)) as $key => $val) {
                // We need to exclude repeated letters from the value as that won't help in finding the answer
                if (strpbrk($word_prevletters, $val) === false) {
                    $word_value = $word_value + $letter_value[$val];
                }
                $word_prevletters .= $val;
            }
        }
        $word_value_list[$clean_word] = $word_value;
    }
    
    // Find most valuable words
    $word_value_list_reverse = $word_value_list;
    arsort($word_value_list_reverse);
?>
            <h3>Highest Value Finding Words:</h3>
            <div class="row">
<?php
    $i = 0;
    foreach ($word_value_list_reverse as $key => $val) {
        echo('                <div class="col-md-2">');
        echo('                    ' . strtoupper($key) . ': ' . $val);
        echo('                </div>');
        $i++;
        if ($i > 23) { break; }
    }
?>
            </div>
            <p>Let's say we take AROSE as the first finding word. What is the best second word?</p>
<?php
    // Let's do this again, but exclude AROSE...
    $word_value_list = array();
    // Select all 5 letter words from combined dictionary
    $stmtstr = "SELECT word from " . $_ENV['TBL_COMB'] . " WHERE LENGTH(word) = 5";
    $stmt = $dbh->prepare($stmtstr);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $word_value = 0;
        $word_prevletters = "arose";
        // The dictionary should only contain the letters a-z, but better to be safe...
        $clean_word = preg_replace('/[^a-z]/','',$row[0] ?? '');
        if (($clean_word != '') && (strlen($clean_word) == 5)) {
            foreach (str_split(strtolower($clean_word)) as $key => $val) {
                // We need to exclude repeated letters from the value as that won't help in finding the answer
                if (strpbrk($word_prevletters, $val) === false) {
                    $word_value = $word_value + $letter_value[$val];
                }
                $word_prevletters .= $val;
            }
        }
        $word_value_list[$clean_word] = $word_value;
    }
    
    // Find most valuable words
    $word_value_list_reverse = $word_value_list;
    arsort($word_value_list_reverse);
?>
            <h3>Highest Value Finding Words after AROSE:</h3>
            <div class="row">
<?php
    $i = 0;
    foreach ($word_value_list_reverse as $key => $val) {
        echo('                <div class="col-md-2">');
        echo('                    ' . strtoupper($key) . ': ' . $val);
        echo('                </div>');
        $i++;
        if ($i > 23) { break; }
    }
?>
            </div>
            <p>What if we need a 3rd word?</p>
<?php
    // Let's do this again, but exclude AROSE...
    $word_value_list = array();
    // Select all 5 letter words from combined dictionary
    $stmtstr = "SELECT word from " . $_ENV['TBL_COMB'] . " WHERE LENGTH(word) = 5";
    $stmt = $dbh->prepare($stmtstr);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $word_value = 0;
        $word_prevletters = "aroseuntil";
        // The dictionary should only contain the letters a-z, but better to be safe...
        $clean_word = preg_replace('/[^a-z]/','',$row[0] ?? '');
        if (($clean_word != '') && (strlen($clean_word) == 5)) {
            foreach (str_split(strtolower($clean_word)) as $key => $val) {
                // We need to exclude repeated letters from the value as that won't help in finding the answer
                if (strpbrk($word_prevletters, $val) === false) {
                    $word_value = $word_value + $letter_value[$val];
                }
                $word_prevletters .= $val;
            }
        }
        $word_value_list[$clean_word] = $word_value;
    }
    
    // Find most valuable words
    $word_value_list_reverse = $word_value_list;
    arsort($word_value_list_reverse);
?>
            <h3>Highest Value Finding Words after AROSE and UNTIL/UNLIT:</h3>
            <div class="row">
<?php
    $i = 0;
    foreach ($word_value_list_reverse as $key => $val) {
        echo('                <div class="col-md-2">');
        echo('                    ' . strtoupper($key) . ': ' . $val);
        echo('                </div>');
        $i++;
        if ($i > 23) { break; }
    }
?>
            </div>
            <p>Obviously, this all depends on the live dictionary and what &quot;bad&quot; words the new owners have removed...</p>
        </div>
<?php
} catch (PDOException $e) {
?>
        <div class="container">
            <p>Error: Cannot connect to database.</p>
        </div>
<?php
}
?>
        <div class="container">
            <p><a href="/">Word Finder</a></p>
            <p>Stuart Millington 2022</p>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </body>
</html>
