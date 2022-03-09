<?php
// Autoload
include_once __DIR__ . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Include search helpers and filter post param
include_once __DIR__ . '/searchutils.php';
$sid = preg_replace('/[^0-9]/','',$_POST['srcfil'] ?? '');

// Due to Gooogle maps timeouts on KMZ files, we need to pregenerate
// the KMZ file, rather than encode it on the fly. This needs to be
// safe from race conditions...
include_once __DIR__ . '/kmlkmz.php';
$temp_id = pregen_kmz($sid);

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
                    minZoom: 3,
                    center: bhx,
                });

                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://tools.z-add.co.uk/test/kmldown.php?kmz=<?= $temp_id ?>',
                    preserveViewport: false,
                    clickable: true,
                    map: map
                });
            }
        </script>
    </head>
    <body>
        <div class="header">
            <h2>Live Flights with Google Maps</h2>
            <div class="filters">
                <form action="/test/" method="POST">
                    <select name="srcfil" id="srcfil">
                        <?= search_sel($sid) ?>
                    </select>
                    <input type="submit" name="submit" value="Update" />
                </form>
            </div>
        </div>
        <div id="map"></div>
        <div class="footer">
            <p class="credit">Flight Data from: The OpenSky Network, https://opensky-network.org</p>
        </div>
        <script src="https://maps.googleapis.com/maps/api/js?key=<?= $_ENV['GOOGLE_API_KEY'] ?>&callback=initMap&libraries=&v=weekly" async></script>
    </body>
</html>
