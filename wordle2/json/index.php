<?php
// Autoload
include_once dirname(__DIR__,1) . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__,1));
$dotenv->load();

// Blank return
$result = array();
$words = "";

// Record of data from NYT for dev ref.
    //$_POST['boardState']
    //{"words":"stare,,,,,"}
    //$_POST['evaluations']
    //{"words":"correct,absent,absent,absent,absent,,,,,"}

// Clean input vars
$safe_boardState = preg_replace('/[^a-z,]/','',$_POST['boardState'] ?? '');
$safe_evaluations = preg_replace('/[^a-z,]/','',$_POST['evaluations'] ?? '');

$used_words = explode(",", $safe_boardState);
$word_evaluations = explode(",", $safe_evaluations);

// Check if any words have been guessed yet
if ((is_array($used_words)) && (count($used_words) > 0) && (is_array($word_evaluations)) && (count($word_evaluations) > 0)) {
    
    $wordcount = 0;
    $chartest_correct = array();    // Due to the sequence of building the query, first build array of required tests
    $chartest_absent = array();
    $chartest_present = array();
    // Loop through words
    foreach ($used_words as $wordkey_key => $wordkey) {
        
        if (($wordkey != "") && (strlen($wordkey) == 5)) {
            $wordchars = str_split($wordkey,1);

            // Loop through word chars
            $charcount = 0;
            foreach ($wordchars as $charkey_key => $charkey) {
                
                // Check for an evaluation result
                $evalpos = $charcount + ($wordcount * 5);
                if ((isset($word_evaluations[$evalpos])) &&($word_evaluations[$evalpos] != "")) {
                    switch ($word_evaluations[$evalpos]) {
                        case "correct":
                            $chartest_correct[] = array($charkey,($charcount + 1));
                            break;
                        case "absent":
                            $chartest_absent[] = array($charkey);
                            break;
                        case "present":
                            $chartest_present[] = array($charkey,($charcount + 1));
                            break;
                        default:
                            break;
                    }
                }
                $charcount++;
            }
            $wordcount++;
        }
    }
    
    // Now that we have arrays of tests, build the SQL
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=' . $_ENV['DB_DBSE'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

        try {
        
            $bindvals = array(); // Bind values
            $bindcount = 1;
            $stmtstr = "SELECT word from " . $_ENV['TBL_SRCGSS'] . " WHERE LENGTH(word) = 5";
            // Corect letters select
            if ((is_array($chartest_correct)) && (count($chartest_correct) > 0)) {
                //$chartest_correct[] = array($charkey,($charcount + 1));
                foreach ($chartest_correct as $key => $val) {
                    $stmtstr .= " AND SUBSTRING(word, " . $val[1] . ",1) = :bindval" . $bindcount;
                    $bindvals[] = array($bindcount,$val[0]);
                    $bindcount++;
                }
            }
            // Absent letters select
            if ((is_array($chartest_absent)) && (count($chartest_absent) > 0)) {
                //$chartest_absent[] = array($charkey);
                foreach ($chartest_absent as $key => $val) {
                    $stmtstr .= " AND NOT(word LIKE :bindval" . $bindcount . ")";
                    $bindvals[] = array($bindcount, "%" . $val[0] . "%");
                    $bindcount++;
                }
            }
            // Present letters select
            if ((is_array($chartest_present)) && (count($chartest_present) > 0)) {
                //$chartest_present[] = array($charkey,($charcount + 1));
                foreach ($chartest_present as $key => $val) {
                    $stmtstr .= " AND (word LIKE :bindval" . $bindcount . ")";
                    $bindvals[] = array($bindcount, "%" . $val[0] . "%");
                    $bindcount++;
                    $stmtstr .= " AND NOT(SUBSTRING(word, " . $val[1] . ",1) = :bindval" . $bindcount . ")";
                    $bindvals[] = array($bindcount,$val[0]);
                    $bindcount++;
                }
            }

            // Prepare statement then the binds
            $stmt = $dbh->prepare($stmtstr);

            // Binds
            if ((is_array($bindvals)) && (count($bindvals) > 0)) {
                foreach ($bindvals as $key => $val) {
                    $stmt->bindParam(':bindval' . $val[0], $bindvals[$key][1]);
                }
            }

            $stmt->execute();
            $safe_wordlist = "";
            
            while ($row = $stmt->fetch()) {
                $safe_wordlist .= $row[0] . "\t";
            }

        } catch (PDOException $e) {

        }
        
    } catch (PDOException $e) {

    }

    // Dont't send silly long lists
    if (strlen($safe_wordlist) > 100) {
        $safe_wordlist = substr($safe_wordlist,0,100) . "...";
    }
    $words = "Suggestions: " . $safe_wordlist;
} else {
    $words = "Starting word: STARE";
}

$result['words'] = $words;
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json;');
echo json_encode($result);
exit;
