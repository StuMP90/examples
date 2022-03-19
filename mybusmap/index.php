<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/inc_api.php';
ini_set('display_errors', 0);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>MyBus</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://labs.os.uk/public/os-api-branding/v0.3.0/os-api-branding.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="/site.css" />
</head>
<body>
<div class="bussel container">
    <div class="row">
<?php
    if ((isset($_GET['bid'])) && (array_key_exists($_GET['bid'],$bus_arr))) {
        $xbid = $_GET['bid'];
    } else {
        $xbid = array_key_first($bus_arr);
    }
    $bus_live_datax = get_remote_data($bus_arr[$xbid]);
    $bus_live_data = $bus_live_datax['ServiceDelivery']['VehicleMonitoringDelivery']['VehicleActivity'];
    $col_wid = (int) floor(12/(count($bus_arr) + 1));
    foreach ($bus_arr as $key => $value) {
        ?>
        <div class="col-<?php echo($col_wid); ?> tcen">
            <a class="btn <?php echo(((string)$xbid == (string)$key ? 'btn-primary': 'btn-outline-primary')); ?>" href="/?bid=<?php echo(urlencode($key)); ?>"><?php echo(htmlspecialchars($key)); ?></a>
        </div>
        <?php
    }
?>
        <div class="col-<?php echo($col_wid); ?> tcen">
            <a class="btn btn-outline-primary" href="#" onClick="window.location.reload();"><i class="fas fa-sync"></i></a>
        </div>
    </div>
</div>
<div id="map"></div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://labs.os.uk/public/os-api-branding/v0.3.0/os-api-branding.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>

    var apiKey = '<?= $apikey_osmaps ?>';
    var serviceUrl = 'https://api.os.uk/maps/raster/v1/zxy';
    
    var geolat = 52.49750000;
    var geolong = -2.16861111;
    var initzoom = 12;
    var options = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
    };
    function showPosition(position) {
        geolat = position.coords.latitude;
        geolong = position.coords.longitude;
        newzoom = 15;
        var latLon = L.latLng(geolat, geolong);
        map.setView(latLon,newzoom);
    }
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    }

    // Set bounds
    var bounds = [
        [ 52.43083333, -2.31000000 ], // Southwest coordinates.
        [ 52.61444444, -1.96972222 ] // Northeast coordinates.
    ];

    // Initialize the map.
    var mapOptions = {
        minZoom: 12,
        maxZoom: 16,
        center: [ geolat, geolong ],
        zoom: initzoom,
        maxBounds: bounds,
        attributionControl: false
    };

    var map = L.map('map', mapOptions);

    // Load and display ZXY tile layer on the map.
    var basemap = L.tileLayer(serviceUrl + '/Road_3857/{z}/{x}/{y}.png?key=' + apiKey, {
        maxZoom: 20
    }).addTo(map);

    // Add pinned locations to map.
    var greenIcon = new L.Icon({
        iconUrl: '/marker-icon-green.png',
        shadowUrl: '/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    var blueIcon = new L.Icon({
        iconUrl: '/marker-icon-blue.png',
        shadowUrl: '/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    var goldIcon = new L.Icon({
        iconUrl: '/marker-icon-gold.png',
        shadowUrl: '/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    var arr0Icon = new L.Icon({
        iconUrl: '/arr0.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr1Icon = new L.Icon({
        iconUrl: '/arr1.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr2Icon = new L.Icon({
        iconUrl: '/arr2.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr3Icon = new L.Icon({
        iconUrl: '/arr3.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr4Icon = new L.Icon({
        iconUrl: '/arr4.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr5Icon = new L.Icon({
        iconUrl: '/arr5.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr6Icon = new L.Icon({
        iconUrl: '/arr6.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr7Icon = new L.Icon({
        iconUrl: '/arr7.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr8Icon = new L.Icon({
        iconUrl: '/arr8.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr9Icon = new L.Icon({
        iconUrl: '/arr9.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr10Icon = new L.Icon({
        iconUrl: '/arr10.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr11Icon = new L.Icon({
        iconUrl: '/arr11.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
    var arr12Icon = new L.Icon({
        iconUrl: '/arr12.png',
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [22, 22]
    });
<?php
    foreach ($pinloc_arr as $pinloc) {
        foreach ($pinloc as $key => $value) {
?>
    var marker = L.marker([<?php echo($value); ?>],{
        title: '<?php echo(htmlspecialchars($key)); ?>',
        icon: greenIcon,
    }).addTo(map);
<?php
        }
    }
    foreach ($bus_live_data as $bus_stat) {
        if ($bus_stat['MonitoredVehicleJourney']['LineRef'] == $bus_mtch_arr[$xbid]) {
?>
    var marker = L.marker([<?php echo $bus_stat['MonitoredVehicleJourney']['VehicleLocation']['Latitude'] . "," . $bus_stat['MonitoredVehicleJourney']['VehicleLocation']['Longitude']; ?>],{
        title: '<?php echo(htmlspecialchars($xbid)); ?>',
<?php
    if (isset($bus_stat['MonitoredVehicleJourney']['Bearing'])) {
        $iconsel = floor($bus_stat['MonitoredVehicleJourney']['Bearing']/30);
        echo("        icon: arr" . $iconsel . "Icon,");
    } else {
        echo("        icon: goldIcon,");
    }
?>

    }).addTo(map);
<?php
        }
    }
?>
    var marker = L.marker([51.5, -0.09]).addTo(map);

</script>
</body>
</html>