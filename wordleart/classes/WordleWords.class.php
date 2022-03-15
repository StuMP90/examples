<?php
require_once dirname(__DIR__, 1) . '/config.inc.php';

/**
 * A class to generate dot matrix style character grids.
 */
class WordleWords {

    /**
     * Constructor.
     */
    function __construct() {

    }
    
    /**
     * Checks that the specified grid size is valid.
     *
     * @param int $width The matrix width in blocks.
     * @param int $height The matrix height in blocks.
     */
    public function getWord(string $target_word, int $p1, int $p2, int $p3, int $p4, int $p5) : string {
        
        $ret_word = "";
        
        // Check that the target word is valid and replace with "words" if not
        $safe_target = strtolower(preg_replace('/[^a-zA-Z]/','',$target_word ?? ''));
        if (!((is_string($safe_target)) && (strlen($safe_target) == 5))) {
            $safe_target = "words";
        }

        try {
            $dbh = new PDO('mysql:host=localhost;dbname=' . MY_DBDBSE, MY_DBUSER, MY_DBPASS);

            try {
                $stmtstr = "SELECT word from " . MY_DBTBLE_GUESS . " WHERE LENGTH(word) = 5";

                // Select letters
                if ($p1 == 1) {
                    $stmtstr .= " AND SUBSTRING(word,1,1) = :letter1";
                } else {
                    $stmtstr .= " AND NOT(SUBSTRING(word,1,1) = :letter1)";
                }
                if ($p2 == 1) {
                    $stmtstr .= " AND SUBSTRING(word,2,1) = :letter2";
                } else {
                    $stmtstr .= " AND NOT(SUBSTRING(word,2,1) = :letter2)";
                }
                if ($p3 == 1) {
                    $stmtstr .= " AND SUBSTRING(word,3,1) = :letter3";
                } else {
                    $stmtstr .= " AND NOT(SUBSTRING(word,3,1) = :letter3)";
                }
                if ($p4 == 1) {
                    $stmtstr .= " AND SUBSTRING(word,4,1) = :letter4";
                } else {
                    $stmtstr .= " AND NOT(SUBSTRING(word,4,1) = :letter4)";
                }
                if ($p5 == 1) {
                    $stmtstr .= " AND SUBSTRING(word,5,1) = :letter5";
                } else {
                    $stmtstr .= " AND NOT(SUBSTRING(word,5,1) = :letter5)";
                }
                $stmtstr .= " ORDER BY RAND()";

                // Prepare statement then the binds
                $stmt = $dbh->prepare($stmtstr);
                
                $safe_letter1 = substr($safe_target,0,1);
                $safe_letter2 = substr($safe_target,1,1);
                $safe_letter3 = substr($safe_target,2,1);
                $safe_letter4 = substr($safe_target,3,1);
                $safe_letter5 = substr($safe_target,4,1);

                $stmt->bindParam(':letter1', $safe_letter1);
                $stmt->bindParam(':letter2', $safe_letter2);
                $stmt->bindParam(':letter3', $safe_letter3);
                $stmt->bindParam(':letter4', $safe_letter4);
                $stmt->bindParam(':letter5', $safe_letter5);

                $stmt->execute();
                while ($row = $stmt->fetch()) {
                    $ret_word = $row[0];
                    break;
                }
            } catch (PDOException $e) {

            }            
        } catch (PDOException $e) {

        }
        return $ret_word;
    }
    
}
