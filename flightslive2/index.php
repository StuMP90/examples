<?php
// Autoload
include_once __DIR__ . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Due to Google's incredibly low timeout on loading KML files
// and the ram limit on the development server it is necessary
// to pre-generate the KML rather than to translate the
// openskies api data into KML on the fly.
// 
// In a production version this would all be from a local cache, either
// MySQL or ElasticSearch.

// Replace example.com with your own FQDN
// Trigger the API-to-KML translation
$api_url = "https://example.com/kml.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
$result = curl_exec($ch);
// And free up the memory
unset($result);
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Live Flights with Google Maps</title>
        <link rel="stylesheet" type="text/css" href="./style.css" />
        <script src="./index.js"></script>
    </head>
    <body>
        <div class="header">
            <h2>Live Flights with Google Maps</h2>
        </div>
        <div id="map"></div>
        <p>Flight Data from: The OpenSky Network, https://opensky-network.org</p>
        <script src="https://maps.googleapis.com/maps/api/js?key=<?= $_ENV['GOOGLE_API_KEY'] ?>&callback=initMap&libraries=&v=weekly" async></script>
    </body>
</html>
