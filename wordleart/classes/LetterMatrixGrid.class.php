<?php

/**
 * A class to generate dot matrix style character grids.
 */
class LetterMatrixGrid {

    protected $gridFive = array();
    protected int $min_width = 5;
    protected int $min_height = 5;
    protected int $max_width = 10;
    protected int $max_height = 10;
    public string $status = "";
    
    /**
     * Constructor with a 5x5 matrix.
     */
    function __construct() {
        $this->gridFive['a'] = array(0x7c,0x44,0x44,0x7c,0x44);
        $this->gridFive['b'] = array(0x7c,0x44,0x78,0x44,0x7c);
        $this->gridFive['c'] = array(0x7c,0x40,0x40,0x40,0x7c);
        $this->gridFive['d'] = array(0x78,0x44,0x44,0x44,0x78);
        $this->gridFive['e'] = array(0x7c,0x40,0x78,0x40,0x7c);
        $this->gridFive['f'] = array(0x7c,0x40,0x70,0x40,0x40);
        $this->gridFive['g'] = array(0x7c,0x40,0x4c,0x44,0x7c);
        $this->gridFive['h'] = array(0x44,0x44,0x7c,0x44,0x44);
        $this->gridFive['i'] = array(0x7c,0x10,0x10,0x10,0x7c);
        $this->gridFive['j'] = array(0x0c,0x04,0x04,0x44,0x7c);
        $this->gridFive['k'] = array(0x44,0x48,0x70,0x48,0x44);
        $this->gridFive['l'] = array(0x40,0x40,0x40,0x40,0x7c);
        $this->gridFive['m'] = array(0x44,0x6c,0x54,0x44,0x44);
        $this->gridFive['n'] = array(0x44,0x64,0x54,0x4c,0x44);
        $this->gridFive['o'] = array(0x38,0x44,0x44,0x44,0x38);
        $this->gridFive['p'] = array(0x78,0x44,0x78,0x40,0x40);
        $this->gridFive['q'] = array(0x7c,0x44,0x44,0x7c,0x10);
        $this->gridFive['r'] = array(0x78,0x44,0x78,0x44,0x44);
        $this->gridFive['s'] = array(0x7c,0x40,0x7c,0x04,0x7c);
        $this->gridFive['t'] = array(0x7c,0x10,0x10,0x10,0x10);
        $this->gridFive['u'] = array(0x44,0x44,0x44,0x44,0x7c);
        $this->gridFive['v'] = array(0x44,0x44,0x28,0x28,0x10);
        $this->gridFive['w'] = array(0x44,0x44,0x54,0x54,0x28);
        $this->gridFive['x'] = array(0x44,0x28,0x10,0x28,0x44);
        $this->gridFive['y'] = array(0x44,0x44,0x28,0x10,0x10);
        $this->gridFive['z'] = array(0x7c,0x08,0x10,0x20,0x7c);
        $this->gridFive['0'] = array(0x7c,0x4c,0x54,0x64,0x7c);
        $this->gridFive['1'] = array(0x10,0x30,0x10,0x10,0x38);
        $this->gridFive['2'] = array(0x78,0x04,0x38,0x40,0x7c);
        $this->gridFive['3'] = array(0x7c,0x04,0x38,0x04,0x7c);
        $this->gridFive['4'] = array(0x40,0x40,0x50,0x7c,0x10);
        $this->gridFive['5'] = array(0x7c,0x40,0x78,0x04,0x78);
        $this->gridFive['6'] = array(0x7c,0x40,0x7c,0x44,0x7c);
        $this->gridFive['7'] = array(0x7c,0x04,0x08,0x10,0x10);
        $this->gridFive['8'] = array(0x7c,0x44,0x7c,0x44,0x7c);
        $this->gridFive['9'] = array(0x7c,0x44,0x7c,0x04,0x7c);
        $this->gridFive['space'] = array(0x00,0x00,0x00,0x00,0x00);
        $this->gridFive['err'] = array(0x7c,0x7c,0x7c,0x7c,0x7c);
    }
    
    /**
     * Checks that the specified grid size is valid.
     *
     * @param int $width The matrix width in blocks.
     * @param int $height The matrix height in blocks.
     */
    private function isValidGrid(int $width, int $height) : bool {
        if (($width >= $this->min_width) && ($width <= $this->max_width)) {
            if (($height >= $this->min_height) && ($height <= $this->max_height)) {
                return true;
            } else {
                $this->status = "Error: Invalid height.";
                return false;
            }
        } else {
            $this->status = "Error: Invalid width.";
            return false;
        }
    }
    
    /**
     * Generate a blank grid.
     *
     * @param int $width The matrix width in blocks.
     * @param int $height The matrix height in blocks.
     */
    private function getBlankGrid(int $grid_x, int $grid_y) {
        if (!($this->isValidGrid($grid_x, $grid_y))) {
            $grid_x = 5;
            $grid_y = 6;
        }
        $output_grid = array();
        // X axis starting from zero
        for ($x = 0; $x < $grid_x; $x++) {

            // Y axis starting from zero
            for ($y = 0; $y < $grid_y; $y++) {
                $output_grid[$x][$y] = 0;
            }
        }
        return $output_grid;
    }
    
    /**
     * Returns a matrix for the specified character. Matrix size limited to a
     * size ranging from 5x5 to 10x10
     *
     * @param string $alnum The character which the matrix is generated for.
     * @param int $matrix_x The matrix width in blocks.
     * @param int $matrix_y The matrix height in blocks.
     * @param int $offset_x The matrix offset in blocks.
     * @param int $offset_y The matrix offset in blocks.
     */
    public function getGrid(string $alnum = "", int $matrix_x = 5, int $matrix_y = 5, int $offset_x = 0, int $offset_y = 0) : array {
        
        $output_grid = array();
        
        if (!($this->isValidGrid($matrix_x, $matrix_y))) {
            $matrix_x = 5;
            $matrix_y = 6;
        }
        $safe_alnum = strtolower(preg_replace('/[^a-zA-Z0-9]/','',$alnum ?? ''));

        // Set key to "space" for invalid characters and space
        if ($safe_alnum == "") {
            $safe_alnum = "space";
        }
        // Get grid from hex array
        $grid_rows = $this->gridFive[$safe_alnum];

        //Determine starting row/col (the source grid is 5x5)
        if ((5 + $offset_x) <= $this->max_width) {
            $start_x = $offset_x;
        } else {
            $start_x = 0;
        }
        if ((5 + $offset_y) <= $this->max_height) {
            $start_y = $offset_y;
        } else {
            $start_y = 0;
        }
        
        // Blank the grid
        $output_grid = $this->getBlankGrid($matrix_x, $matrix_y);
        
        // Add the character blocks
        $row_y = $start_y;
        foreach ($grid_rows as $key => $val) {
            $output_grid[($start_x)][$row_y] = ((0b01000000 & $val) ? 1 : 0);
            $output_grid[($start_x + 1)][$row_y] = ((0b00100000 & $val) ? 1 : 0);
            $output_grid[($start_x + 2)][$row_y] = ((0b00010000 & $val) ? 1 : 0);
            $output_grid[($start_x + 3)][$row_y] = ((0b00001000 & $val) ? 1 : 0);
            $output_grid[($start_x + 4)][$row_y] = ((0b00000100 & $val) ? 1 : 0);
            $row_y++;
        }

        return $output_grid;
    }

    
}
