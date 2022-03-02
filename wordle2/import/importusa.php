<?php
// Autoload
include_once dirname(__DIR__, 1) . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

// Import US English words
$uswords = file(__DIR__ . "/" . $_ENV['US_DICT']);

if (count($uswords) > 0) {
    echo "<p>Found " . count($uswords) . " words in US Dictionary</p>";
    echo "<p>";
    $i = 0;
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=' . $_ENV['DB_DBSE'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        foreach ($uswords as $key => $val) {
            // Clean characters
            $val = strtolower($val);
            $val = preg_replace('/[^a-z]/','',$val);

            try {
                $stmt = $dbh->prepare("INSERT INTO wordlist_usa (word) VALUES (:word) ON DUPLICATE KEY UPDATE word=:word;");
                $stmt->bindParam(':word', $val);
                $stmt->execute();
                $stmt = $dbh->prepare("INSERT INTO wordlist_comb (word) VALUES (:word) ON DUPLICATE KEY UPDATE word=:word;");
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
    echo "<p>Could not load US Dictionary</p>";
}
