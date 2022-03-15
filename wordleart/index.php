<?php
/* 
 * Generate wordle style grids withing wordle style grids.
 */

// Autoload classes from the classes folder
spl_autoload_register(function ($class) {
    include __DIR__ . '/classes/' . $class . '.class.php';
});

function draw_grid ($letter = "", $word = "") {
    $grid = new LetterMatrixGrid();
    $grid_data = $grid->getGrid($letter,5,5,0,0);
    
    $wordle = new WordleWords();
    
    for ($y = 0; $y < 5; $y++) {
        // Get a fitting word for each row
        $wordle_word = $wordle->getWord($word, $grid_data[0][$y], $grid_data[1][$y], $grid_data[2][$y], $grid_data[3][$y], $grid_data[4][$y]);
        $grid_word = array();
        if (($wordle_word != "") && ($y < 5)) {
            $grid_word[0][$y] = strtoupper(substr($wordle_word,0,1));
            $grid_word[1][$y] = strtoupper(substr($wordle_word,1,1));
            $grid_word[2][$y] = strtoupper(substr($wordle_word,2,1));
            $grid_word[3][$y] = strtoupper(substr($wordle_word,3,1));
            $grid_word[4][$y] = strtoupper(substr($wordle_word,4,1));
        } else {
            $grid_word[0][$y] = "&nbsp;";
            $grid_word[1][$y] = "&nbsp;";
            $grid_word[2][$y] = "&nbsp;";
            $grid_word[3][$y] = "&nbsp;";
            $grid_word[4][$y] = "&nbsp;";
        }
        echo '            <div class="row">';
        echo '                <div class="col-12 tcen">';
        for ($x = 0; $x < 5; $x++) {
            if ($grid_data[$x][$y] == 1) {
                echo '<span class="wordbox_green">' . $grid_word[$x][$y] . '</span>';
            } else {
                echo '<span class="wordbox_grey">' . $grid_word[$x][$y] . '</span>';
            }
        }
        echo '                </div>';
        echo '            </div>';
    }
    echo '<p>&nbsp;</p>';
}

// Words and letters
$word_row = array();
$word_row[] = array(
    'word' => "kitty",
    'letter1' => "K",
    'letter2' => "I",
    'letter3' => "T",
    'letter4' => "T",
    'letter5' => "Y",
);
$word_row[] = array(
    'word' => "plays",
    'letter1' => "P",
    'letter2' => "L",
    'letter3' => "A",
    'letter4' => "Y",
    'letter5' => "S",
);
$word_row[] = array(
    'word' => "games",
    'letter1' => "G",
    'letter2' => "A",
    'letter3' => "M",
    'letter4' => "E",
    'letter5' => "S",
);
$word_row[] = array(
    'word' => "while",
    'letter1' => "W",
    'letter2' => "H",
    'letter3' => "I",
    'letter4' => "L",
    'letter5' => "E",
);
$word_row[] = array(
    'word' => "human",
    'letter1' => "P",
    'letter2' => "U",
    'letter3' => "P",
    'letter4' => "P",
    'letter5' => "Y",
);
$word_row[] = array(
    'word' => "rests",
    'letter1' => "R",
    'letter2' => "E",
    'letter3' => "S",
    'letter4' => "T",
    'letter5' => "S",
);


?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Wordle Art</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <style type="text/css">
            .wordbox_green {
                width: 22px;
                height: 22px;
                background-color: #6aaa64;
                border: solid thin #787c7e;
                display: inline-block;
                margin-right: 3px;
                text-align: center;
            }
            .wordbox_grey {
                width: 22px;
                height: 22px;
                background-color: #d8dcde;
                border: solid thin #787c7e;
                display: inline-block;
                margin-right: 3px;
                text-align: center;
            }
            body {
                background-color: #d8dcde;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12 tcen">
                    <p>&nbsp;</p>
                </div>
            </div>
<?php
    foreach ($word_row as $key => $val) {
?>
            <div class="row">
                <div class="col-2 tcen">
                    <?php draw_grid($val['letter1'], $val['word']); ?>
                </div>
                <div class="col-2 tcen">
                    <?php draw_grid($val['letter2'], $val['word']); ?>
                </div>
                <div class="col-2 tcen">
                    <?php draw_grid($val['letter3'], $val['word']); ?>
                </div>
                <div class="col-2 tcen">
                    <?php draw_grid($val['letter4'], $val['word']); ?>
                </div>
                <div class="col-2 tcen">
                    <?php draw_grid($val['letter5'], $val['word']); ?>
                </div>
                <div class="col-2 tcen">
                    &nbsp;
                </div>
            </div>
<?php
    }
?>
        </div>
    </body>
</html>
