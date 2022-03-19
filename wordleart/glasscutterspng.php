<?php

/* 
 * Create "wordle" style grids from actual images.
 * 
 * The images need to be divisible into 5x6 grids and the last line will be
 * used for a message or titles.
 */

// Autoload classes from the classes folder
spl_autoload_register(function ($class) {
    include __DIR__ . '/classes/' . $class . '.class.php';
});

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Wordle Art Test</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <style type="text/css">
            div.imgrow img {
                max-width: 95vw;
                min-height: 90vh;
                max-height: 100vh;
            }

            body {
                background-color: #d8dcde;
                padding: 0;
                margin: 0;
            }
        </style>
    </head>
    <body>
<?php
    $imggrid = new ImageToGrid();
    $grid_grid = $imggrid->getWordGrid("Glasscutters.png");
    $match_words = array("lager","stout","casks","amber","darts","sport","music","bowls");
    $search_words = array("lager","bowls","casks","music");
    $filename = "glasscutters";
    $title_message = "The Glasscutters Arms, Barnett Street, Wordsley";
    $grid_render_block = $imggrid->renderGrid($grid_grid['grid'],$match_words,$search_words,$title_message);
    $imggrid->paintGridPng($grid_render_block['grid'], $grid_render_block['grid_words'],$filename,1,"005bbb");
?>
        <div class="imgrow">
            <img src="/images/out/<?= $filename ?>.png" />
        </div>
    </body>
</html>