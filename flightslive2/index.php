<?php
// Autoload
include_once __DIR__ . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Due to Gooogle maps timeouts on KMZ files, we need to pregenerate
// the KMZ file, rather than encode it on the fly. This needs to be
// safe from race conditions...
include_once __DIR__ . '/kmlkmz.php';
$temp_id = pregen_kmz();

// The kmldown.php script will pass the generated file to Google
// and delete it once it has been downloaded.

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Live Flights with Google Maps</title>
        <link rel="stylesheet" type="text/css" href="./style.css" />
        <script>
            // Initialize and add the map
            function initMap() {
                // BHX location
                const bhx = { lat: 52.452381, lng: -1.743507 };

                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 3,
                    center: bhx,
                });

                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://tools.z-add.co.uk/test/kmldown.php?kmz=<?= $temp_id ?>',
                    preserveViewport: true,
                    clickable: true,
                    map: map
                });
            }
        </script>
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
