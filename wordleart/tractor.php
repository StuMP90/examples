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
                max-width: 90%;
                max-height: 300px;
                height: 300px;
            }
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
            .wordbox_sapphire {
                width: 22px;
                height: 22px;
                background-color: #005bbb;
                border: solid thin #787c7e;
                display: inline-block;
                margin-right: 3px;
                text-align: center;
            }
            .wordbox_yellow {
                width: 22px;
                height: 22px;
                background-color: #ffd500;
                border: solid thin #787c7e;
                display: inline-block;
                margin-right: 3px;
                text-align: center;
            }
            body {
                background-color: #d8dcde;
                width: 6253px !important;
                padding-top: 3px;
            }
            .row {
                margin-left: 3px;
                margin-right: 0;
            }
        </style>
    </head>
    <body>
<?php
    $imggrid = new ImageToGrid();
    $grid_grid = $imggrid->getWordGrid("tractor.png");
    $match_words = array("PUTIN","SATAN","CROOK","WRONG","SHADY","ROGUE","THIEF");
    $search_words = array("putin","crook");
    $title_message = "When you attack us, you will see our faces. Not our backs, but our faces. Ukraine is an independent, sovereign, nation. Russia is committing war crimes and pursuing an illegal war.";
    $grid_render_block = $imggrid->renderGrid($grid_grid['grid'],$match_words,$search_words,$title_message);
    $imggrid->paintGrid($grid_render_block['grid'], $grid_render_block['grid_words'],2,"wordbox_sapphire","wordbox_yellow");
?>
    </body>
</html>