<?php
// Autoload
include_once dirname(__DIR__, 1) . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

// Import Wordle's Allowed Guesses List
$ukwords = file(__DIR__ . "/" . $_ENV['WRDGSS_DICT']);

if (count($ukwords) > 0) {
    echo "<p>Found " . count($ukwords) . " words in Wordle Allowed Guesses List</p>";
    echo "<p>";
    $i = 0;
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=' . $_ENV['DB_DBSE'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        foreach ($ukwords as $key => $val) {
            // Clean characters
            $val = strtolower($val);
            $val = preg_replace('/[^a-z]/','',$val);

            try {
                $stmt = $dbh->prepare("INSERT INTO wordlist_srcgss (word) VALUES (:word) ON DUPLICATE KEY UPDATE word=:word;");
                $stmt->bindParam(':word', $val);
                $stmt->execute();
                $i++;
            } catch (PDOException $e) {

            }
        }
        echo "</p>";
        echo "<p>" . $i . " words processed</p>";
    } catch (PDOException $e) {
        echo "<p>Could not connect to database.</p>";
    }
} else {
    echo "<p>Could not load Wordle Allowed Guesses</p>";
}
