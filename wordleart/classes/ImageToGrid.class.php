<?php
/* 
 * Convert images to "wordle" grids.
 * 
 * As I am supplying the images to myself, some safety and sanity checks will
 * be skipped for now as this is a local test/development and I kind of trust
 * myself - a bit anyway.
 */

// Autoload classes from the classes folder
spl_autoload_register(function ($class) {
    include dirname(__DIR__, 1) . '/classes/' . $class . '.class.php';
});

class ImageToGrid {
    
    /**
     * Constructor.
     */
    function __construct() {
        
    }
    
    /**
     * Checks that the image is valid and returns info about the grid that it
     * would be converted into.
     *
     * @param string $file The image filename.
     */
    public function getGridInfo(string $file) : array {
        $grid_info = array();
        
        $safe_srcfile = dirname(__DIR__,1) . "/images/in/" . $file;
        
        if (file_exists($safe_srcfile)) {
            list($width, $height) = getimagesize($safe_srcfile);
            if (($width > 0) && ($height > 0)) {
                $grid_info['width'] = $width;
                $grid_info['height'] = $height;
                $grid_info['blockwidth'] = $width / 5;
                $grid_info['blockheight'] = $height / 6;                
                $grid_info['status'] = "Valid image";
            } else {
                $grid_info['status'] = "Inalid image";
            }
        } else {
            $grid_info['status'] = "File not found";
        }
        return $grid_info;
    }
    
    /**
     * Returns a word grid from the image.
     *
     * @param string $file The image filename.
     */
    public function getWordGrid(string $file) : array {
        $grid_info = array();
        
        $safe_srcfile = dirname(__DIR__,1) . "/images/in/" . $file;
        
        if (file_exists($safe_srcfile)) {
            list($width, $height) = getimagesize($safe_srcfile);
            if (($width > 0) && ($height > 0)) {
                $grid_info['width'] = $width;
                $grid_info['height'] = $height;
                $grid_info['blockwidth'] = $width / 5;
                $grid_info['blockheight'] = $height / 6;                

                $img = imagecreatefrompng($safe_srcfile);
                for($y = 0; $y < $height; $y++) {
                    for($x = 0; $x < $width; $x++) {
                        $rgb = imagecolorat($img, $x, $y);
                        $r = ($rgb >> 16) & 0xFF;
                        $g = ($rgb >> 8) & 0xFF;
                        $b = $rgb & 0xFF;
                        if (($r < 150) && ($g < 150) && ($b < 150)) {
                            $grid_info['grid'][$x][$y] = 1;
                        } else {
                            $grid_info['grid'][$x][$y] = 0;
                        }
                    }
                }
                $grid_info['status'] = "Valid image";
            } else {
                $grid_info['status'] = "Invalid image";
            }
        } else {
            $grid_info['status'] = "File not found";
        }
        return $grid_info;
    }

    /**
     * Get the maximum value of array key.
     *
     * @param array $arr The array.
     */
    private function getIntKeyMax(array $arr) : array {
        $ret_val = array();
        $ret_val['x'] = 0;
        $ret_val['y'] = 0;
        
        if ((is_array($arr)) && (count($arr) > 0)) {
            // The first array is X
            foreach ($arr as $key => $val) {
                if ((is_int($key)) && ($key > $ret_val['x'])) {
                    $ret_val['x'] = $key;

                    // Inner array is Y
                    foreach ($val as $key2 => $val2) {
                        if ((is_int($key2)) && ($key2 > $ret_val['y'])) {
                            $ret_val['y'] = $key2;
                        }
                    }
                    
                }
            }
        }
        return $ret_val;
    }
    
    /**
     * Render the grid from the previously generated array.
     *
     * @param array $grid The grid as an array.
     * @param array $match_words The grid as an array.
     * @param array $search_words The grid as an array.
     * @param string $title_msg (optional) A "title" message for the bottom line.
     */
    public function renderGrid(array $grid, array $match_words, array $search_words, string $title_msg = "") : array {
        
        $max_a = $this->getIntKeyMax($grid);
        $max_x = $max_a['x'];
        $max_y = $max_a['y'];
        $width = $max_a['x'] + 1;
        $height = $max_a['y'] + 1;
        
        // Get words for the grid
        // Move across the x axis in blocks of 5
        $grid_word = array();
        $wordle = new WordleWords();
        
        for ($x = 0; $x < $width; $x = $x + 5) {
            for ($y = 0; $y < $height; $y++) {

                // Loop through the $search_words
                foreach ($search_words as $search_key) {
                    // match_words allows bypassing the database when all bits are
                    // on. This saves database load (and time outs on the mac this
                    // is running on). Bypass the database if all 5 positions are on.
                    if (($grid[($x)][$y] + $grid[($x + 1)][$y] + $grid[($x + 2)][$y] + $grid[($x + 3)][$y] + $grid[($x + 4)][$y]) == 5) {
                        // Get variations for putin for variety
                        $wordle_word = strtoupper($match_words[array_rand($match_words, 1)]);
                    } else {
                        $wordle_word = $wordle->getWordCustom($search_key, $grid[($x)][$y], $grid[($x + 1)][$y], $grid[($x + 2)][$y], $grid[($x + 3)][$y], $grid[($x + 4)][$y]);
                    }
                    if ($wordle_word != "") {
                        $grid_word[($x)][$y] = strtoupper(substr($wordle_word,0,1));
                        $grid_word[($x + 1)][$y] = strtoupper(substr($wordle_word,1,1));
                        $grid_word[($x + 2)][$y] = strtoupper(substr($wordle_word,2,1));
                        $grid_word[($x + 3)][$y] = strtoupper(substr($wordle_word,3,1));
                        $grid_word[($x + 4)][$y] = strtoupper(substr($wordle_word,4,1));
                        break;  // Break out of $search_words loop once word found
                    } else {
                        $grid_word[($x)][$y] = " ";
                        $grid_word[($x + 1)][$y] = " ";
                        $grid_word[($x + 2)][$y] = " ";
                        $grid_word[($x + 3)][$y] = " ";
                        $grid_word[($x + 4)][$y] = " ";
                    }
                }
            }
        }

        // If a custom title is set for the bottom row
        if ((is_string($title_msg)) && (strlen($title_msg) > 0)) {
            $title_message = strtoupper($title_msg);
            $message_arr = str_split($title_message, 1);
            for ($x = 0; $x < $width; $x++) {
                $grid[$x][$max_y] = 1;
                if ((isset($message_arr[$x])) && ($message_arr[$x] != " ")) {    // Have to check for blank as spaces don't align properly on the grid...
                    $grid_word[($x)][$max_y] = $message_arr[$x];
                } else {
                    $grid_word[($x)][$max_y] = "_";
                }
            }
        }

        return array(
            'grid' => $grid,
            'grid_words' => $grid_word
            );
    }

    /**
     * Paint the grid from the previously generated array.
     *
     * @param array $grid The grid as an array.
     * @param array $grid_word The words for the grid as an array.
     * @param int $bgcol The number of background colours.
     * @param string $bgcls1 The first background class.
     * @param string $bgcls2 (optional) The second background class.
     * @param string $bgcls3 (optional) The third background class.
     */
    public function paintGrid(array $grid, array $grid_word, int $bgcol = 1, string $bgcls1 = "", string $bgcls2 = "", string $bgcls3 = "") : void {
        
        $max_a = $this->getIntKeyMax($grid);
        $max_x = $max_a['x'];
        $max_y = $max_a['y'];
        $width = $max_a['x'] + 1;
        $height = $max_a['y'] + 1;
        
        // Output the HTML Grid
        for ($y = 0; $y < $height; $y++) {
            echo '            <div class="row">';
            echo '                <div class="tcen">';
            for ($x = 0; $x < $width; $x++) {
                if ($grid[$x][$y] == 1) {
                    echo '<span class="wordbox_green">' . $grid_word[$x][$y] . '</span>';
                } else {
                    switch ($bgcol) {
                        case 3:
                            if ($y < ($height * 0.3333)) {
                                echo '<span class="' . $bgcls1 . '">' . $grid_word[$x][$y] . '</span>';
                            } elseif ($y < ($height * 0.6666)) {
                                echo '<span class="' . $bgcls2 . '">' . $grid_word[$x][$y] . '</span>';
                            } else {
                                echo '<span class="' . $bgcls3 . '">' . $grid_word[$x][$y] . '</span>';
                            }
                            break;
                        case 2:
                            if ($y < ($height * 0.5)) {
                                echo '<span class="' . $bgcls1 . '">' . $grid_word[$x][$y] . '</span>';
                            } else {
                                echo '<span class="' . $bgcls2 . '">' . $grid_word[$x][$y] . '</span>';
                            }
                            break;
                        default:
                            echo '<span class="' . $bgcls1 . '">' . $grid_word[$x][$y] . '</span>';
                            break;
                    }
                }
            }
            echo '                </div>';
            echo '            </div>';
        }
    }
    
    /**
     * Create a PNG image from the previously generated array.
     *
     * @param array $grid The grid as an array.
     * @param array $grid_word The words for the grid as an array.
     * @param string $filename The output filename.
     * @param int $bgcol The number of background colours.
     * @param string $bgcls1 The first background colour.
     * @param string $bgcls2 (optional) The second background colour.
     * @param string $bgcls3 (optional) The third background colour.
     */
    public function paintGridPng(array $grid, array $grid_word, string $filename, int $bgcol = 1, string $bgcls1 = "d8dcde", string $bgcls2 = "d8dcde", string $bgcls3 = "d8dcde") : void {
        
        $max_a = $this->getIntKeyMax($grid);
        $max_x = $max_a['x'];
        $max_y = $max_a['y'];
        $width = $max_a['x'] + 1;
        $height = $max_a['y'] + 1;
        
        $pixel_width = 3 + ($width * 25);
        $pixel_height = 3 + ($height * 25);
        
        $im_dstfile = dirname(__DIR__, 1)  . "/images/out/" . $filename . ".png";
        
        // Attempt to create a blank image
        $im = @imagecreatetruecolor($pixel_width, $pixel_height) or die('Cannot Initialize new GD image stream');
        // Set to 300dpi
        imageresolution($im, 300);

        // Set coloured backgrounds
        switch ($bgcol) {
            case 3:
                $split1 = floor($pixel_height * 0.3333);
                $split2 = floor($pixel_height * 0.6666);
                $bg1 = imagecolorallocate($im, hexdec(substr($bgcls1,0,2)), hexdec(substr($bgcls1,2,2)), hexdec(substr($bgcls1,4,2)));
                $bg2 = imagecolorallocate($im, hexdec(substr($bgcls2,0,2)), hexdec(substr($bgcls2,2,2)), hexdec(substr($bgcls2,4,2)));
                $bg3 = imagecolorallocate($im, hexdec(substr($bgcls3,0,2)), hexdec(substr($bgcls3,2,2)), hexdec(substr($bgcls3,4,2)));
                imagefilledrectangle($im, 3, 3, ($pixel_width - 3), $split1, $bg1);
                imagefilledrectangle($im, 3, ($split1 + 1), ($pixel_width - 3), $split2, $bg2);
                imagefilledrectangle($im, 3, ($split2 + 1), ($pixel_width - 3), ($pixel_height - 3), $bg3);
                break;
            case 2:
                $split1 = floor($pixel_height * 0.5);
                $bg1 = imagecolorallocate($im, hexdec(substr($bgcls1,0,2)), hexdec(substr($bgcls1,2,2)), hexdec(substr($bgcls1,4,2)));
                $bg2 = imagecolorallocate($im, hexdec(substr($bgcls2,0,2)), hexdec(substr($bgcls2,2,2)), hexdec(substr($bgcls2,4,2)));
                imagefilledrectangle($im, 3, 3, ($pixel_width - 3), $split1, $bg1);
                imagefilledrectangle($im, 3, ($split1 + 1), ($pixel_width - 3), ($pixel_height - 3), $bg2);
                break;
            default:
                $bg1 = imagecolorallocate($im, hexdec(substr($bgcls1,0,2)), hexdec(substr($bgcls1,2,2)), hexdec(substr($bgcls1,4,2)));
                imagefilledrectangle($im, 3, 3, ($pixel_width - 3), ($pixel_height - 3), $bg1);
                break;
        }
        
        // Draw the individual active grid blocks
        // The inactive blocks do not need to be drawn as the background was
        // set in the loop above.
        $back_colour = "6aaa64";
        $block_on = imagecolorallocate($im, hexdec(substr($back_colour,0,2)), hexdec(substr($back_colour,2,2)), hexdec(substr($back_colour,4,2)));
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $start_x = 3 + ($x * 25) + 1;
                $start_y = 3 + ($y * 25) + 1;
                if ($grid[$x][$y] == 1) {
                    imagefilledrectangle($im, $start_x, $start_y, ($start_x  + 20), ($start_y + 20), $block_on);
                }
            }
        }
        
        // Draw letters onto the image
        $black = imagecolorallocate($im, 0, 0, 0);
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $start_x = 3 + ($x * 25) + 9;
                $start_y = 3 + ($y * 25) + 5;
                if ($grid_word[$x][$y] != "_") {    // No longer need to surpress spaces due to HTML
                    imagechar($im, 3, $start_x, $start_y, $grid_word[$x][$y], $black);
                }
            }
        }

        // Save the image
        imagepng($im, $im_dstfile);
        imagedestroy($im);
    }
}
