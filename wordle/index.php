<?php
// Autoload
include_once __DIR__ . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Clean inputs and prepare selected flag
if ((isset($_POST['langsel'])) && (($_POST['langsel'] == 'comb') || ($_POST['langsel'] == 'uk') || ($_POST['langsel'] == 'usa'))) {
    $safe_langsel = $_POST['langsel'];
} else {
    $safe_langsel = "comb";
}
switch ($safe_langsel) {
    case 'uk':
        $selcomb = "";
        $seluk = "selected";
        $selusa = "";
        break;
    case 'usa':
        $selcomb = "";
        $seluk = "";
        $selusa = "selected";
        break;
    default:
        $selcomb = "selected";
        $seluk = "";
        $selusa = "";
}
$safe_incletters = preg_replace('/[^a-z]/','',$_POST['incletters'] ?? '');
$safe_excletters = preg_replace('/[^a-z]/','',$_POST['excletters'] ?? '');
$safe_letter1 = preg_replace('/[^a-z]/','',$_POST['letter1'] ?? '');
$safe_letter2 = preg_replace('/[^a-z]/','',$_POST['letter2'] ?? '');
$safe_letter3 = preg_replace('/[^a-z]/','',$_POST['letter3'] ?? '');
$safe_letter4 = preg_replace('/[^a-z]/','',$_POST['letter4'] ?? '');
$safe_letter5 = preg_replace('/[^a-z]/','',$_POST['letter5'] ?? '');

$safe_bpletter1 = preg_replace('/[^a-z]/','',$_POST['bpletter1'] ?? '');
$safe_bpletter2 = preg_replace('/[^a-z]/','',$_POST['bpletter2'] ?? '');
$safe_bpletter3 = preg_replace('/[^a-z]/','',$_POST['bpletter3'] ?? '');
$safe_bpletter4 = preg_replace('/[^a-z]/','',$_POST['bpletter4'] ?? '');
$safe_bpletter5 = preg_replace('/[^a-z]/','',$_POST['bpletter5'] ?? '');
$safe_wordlist = "";

// Run search if any inputs are set
if (($safe_incletters != "") || ($safe_excletters != "") || ($safe_letter1 != "") || ($safe_letter2 != "") || ($safe_letter3 != "") || ($safe_letter4 != "") || ($safe_letter5 != "")) {
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=' . $_ENV['DB_DBSE'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

        try {
            // Table selection
            switch ($safe_langsel) {
                case 'uk':
                    $stmtstr = "SELECT word from " . $_ENV['TBL_UK'] . " WHERE LENGTH(word) = 5";
                    break;
                case 'usa':
                    $stmtstr = "SELECT word from " . $_ENV['TBL_USA'] . " WHERE LENGTH(word) = 5";
                    break;
                default:
                    $stmtstr = "SELECT word from " . $_ENV['TBL_COMB'] . " WHERE LENGTH(word) = 5";
            }

            // Fixed / found letters
            if ($safe_letter1 != "") {
                $stmtstr .= " AND SUBSTRING(word,1,1) = :letter1";
            }
            if ($safe_letter2 != "") {
                $stmtstr .= " AND SUBSTRING(word,2,1) = :letter2";
            }
            if ($safe_letter3 != "") {
                $stmtstr .= " AND SUBSTRING(word,3,1) = :letter3";
            }
            if ($safe_letter4 != "") {
                $stmtstr .= " AND SUBSTRING(word,4,1) = :letter4";
            }
            if ($safe_letter5 != "") {
                $stmtstr .= " AND SUBSTRING(word,5,1) = :letter5";
            }

            // Found letter bad position
            if ($safe_bpletter1 != "") {
                $stmtstr .= " AND NOT(SUBSTRING(word,1,1) = :bpletter1)";
            }
            if ($safe_bpletter2 != "") {
                $stmtstr .= " AND NOT(SUBSTRING(word,2,1) = :bpletter2)";
            }
            if ($safe_bpletter3 != "") {
                $stmtstr .= " AND NOT(SUBSTRING(word,3,1) = :bpletter3)";
            }
            if ($safe_bpletter4 != "") {
                $stmtstr .= " AND NOT(SUBSTRING(word,4,1) = :bpletter4)";
            }
            if ($safe_bpletter5 != "") {
                $stmtstr .= " AND NOT(SUBSTRING(word,5,1) = :bpletter5)";
            }
            
            // Included letters
            if ($safe_incletters != "") {
                $safe_letters_arr = str_split($safe_incletters);
                $i = 1;
                foreach ($safe_letters_arr as $key => $val) {
                    $stmtstr .= " AND word LIKE :incletter" . $i;
                    $i++;
                }
            }
            
            // Excluded letters
            if ($safe_excletters != "") {
                $safe_exletters_arr = str_split($safe_excletters);
                $i = 1;
                foreach ($safe_exletters_arr as $key => $val) {
                    $stmtstr .= " AND NOT(word LIKE :excletter" . $i . ")";
                    $i++;
                }
            }

            // Prepare statement then the binds
            $stmt = $dbh->prepare($stmtstr);

            // Fixed / found letters
            if ($safe_letter1 != "") {
                $stmt->bindParam(':letter1', $safe_letter1);
            }
            if ($safe_letter2 != "") {
                $stmt->bindParam(':letter2', $safe_letter2);
            }
            if ($safe_letter3 != "") {
                $stmt->bindParam(':letter3', $safe_letter3);
            }
            if ($safe_letter4 != "") {
                $stmt->bindParam(':letter4', $safe_letter4);
            }
            if ($safe_letter5 != "") {
                $stmt->bindParam(':letter5', $safe_letter5);
            }
            
            // Found letter bad position
            if ($safe_bpletter1 != "") {
                $stmt->bindParam(':bpletter1', $safe_bpletter1);
            }
            if ($safe_bpletter2 != "") {
                $stmt->bindParam(':bpletter2', $safe_bpletter2);
            }
            if ($safe_bpletter3 != "") {
                $stmt->bindParam(':bpletter3', $safe_bpletter3);
            }
            if ($safe_bpletter4 != "") {
                $stmt->bindParam(':bpletter4', $safe_bpletter4);
            }
            if ($safe_bpletter5 != "") {
                $stmt->bindParam(':bpletter5', $safe_bpletter5);
            }

            // Included letters
            if ($safe_incletters != "") {
                $safe_letters_arr = str_split($safe_incletters);
                $i = 1;
                $bindvalin = array();
                foreach ($safe_letters_arr as $key => $val) {
                    $bindvalin[$i] = '%' . $val . '%';
                    $stmt->bindParam(':incletter' . $i, $bindvalin[$i]);
                    $i++;
                }
            }
            
            // Excluded letters
            if ($safe_excletters != "") {
                $safe_exletters_arr = str_split($safe_excletters);
                $i = 1;
                $bindvalex = array();
                foreach ($safe_exletters_arr as $key => $val) {
                    $bindvalex[$i] = '%' . $val . '%';
                    $stmt->bindParam(':excletter' . $i, $bindvalex[$i]);
                    $i++;
                }
            }

            $stmt->execute();
            while ($row = $stmt->fetch()) {
                $safe_wordlist .= $row[0] . "\t";
            }

        } catch (PDOException $e) {

        }
    } catch (PDOException $e) {

    }
}
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
                <a class="navbar-brand" href="/">Wordle Word Finder</a>
            </div>
        </nav>

        <form action="/" method="post">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <h2>Letters</h2>
                        <div class="row">
                            <label for="langsel">Language:</label>
                            <select class="form-select" aria-label="Language" name="langsel" id="langsel">
                              <option value="comb" <?= $selcomb ?>>Combined (UK and USA)</option>
                              <option value="uk" <?= $seluk ?>>UK</option>
                              <option value="usa" <?= $selusa ?>>USA</option>
                            </select>
                        </div>
                        <div class="row">
                            <label for="incletters">Include Letters:</label>
                            <input type="text" name="incletters" id="incletters" value="<?= $safe_incletters ?>" />
                        </div>
                       <div class="row">
                            <label for="confletters">Confirmed Positions:</label>
                            <input type="text" name="letter1" id="letter1" class="col-md-1" maxlength="1" value="<?= $safe_letter1 ?>" />
                            <input type="text" name="letter2" id="letter2" class="col-md-1" maxlength="1" value="<?= $safe_letter2 ?>" />
                            <input type="text" name="letter3" id="letter3" class="col-md-1" maxlength="1" value="<?= $safe_letter3 ?>" />
                            <input type="text" name="letter4" id="letter4" class="col-md-1" maxlength="1" value="<?= $safe_letter4 ?>" />
                            <input type="text" name="letter5" id="letter5" class="col-md-1" maxlength="1" value="<?= $safe_letter5 ?>" />
                        </div>
                       <div class="row">
                            <label for="badposletters">Bad Positions:</label>
                            <input type="text" name="bpletter1" id="bpletter1" class="col-md-1" maxlength="1" value="<?= $safe_bpletter1 ?>" />
                            <input type="text" name="bpletter2" id="bpletter2" class="col-md-1" maxlength="1" value="<?= $safe_bpletter2 ?>" />
                            <input type="text" name="bpletter3" id="bpletter3" class="col-md-1" maxlength="1" value="<?= $safe_bpletter3 ?>" />
                            <input type="text" name="bpletter4" id="bpletter4" class="col-md-1" maxlength="1" value="<?= $safe_bpletter4 ?>" />
                            <input type="text" name="bpletter5" id="bpletter5" class="col-md-1" maxlength="1" value="<?= $safe_bpletter5 ?>" />
                        </div>
                        <div class="row">
                            <label for="excletters">Exclude Letters:</label>
                            <input type="text" name="excletters" id="excletters" value="<?= $safe_excletters ?>" />
                        </div>
                         <div class="row">&nbsp;</div>
                        <div class="row">
                            <input class="btn btn-primary col-md-4" name="submit" type="submit" value="Submit">
                            &nbsp; <a href="/" class="btn btn-primary col-md-4">Clear</a>
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <h2>Words</h2>
                        <textarea name="wordlist" id="wordlist" class="col-md-12" rows="15"><?= $safe_wordlist ?></textarea>

                    </div>
                </div>
            </div>
        </form>

        <div class="container">
            <p>Stuart Millington 2022</p>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </body>
</html>
