<?php
function get_remote_data() {  // Fetch remote data with Curl
    $api_url = "https://opensky-network.org/api/states/all";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

    // Fetch and check for curl errors
    $result = curl_exec($ch);
    $return_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errors = curl_error($ch);
    curl_close($ch);
    if (($result !== false) && ($return_code == 200)) {
        $data_arr = json_decode(json_encode($result),1);
        return $data_arr;
    } else {
        return false;
    }
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.12.0/css/ol.css" type="text/css">
        <style>
            .header {
                width:100%;
                height: 3em;
            }
            .map {
                height: calc(100vh - 5em);
                width: 100%;
            }
            .popover {
                width: 250px;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.12.0/build/ol.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
        <title>Live Flights</title>
    </head>
    <body>
        <div class="header">
            <h2>Flights Live</h2>
        </div>
        <div id="map" class="map"><div id="popup"></div></div>
        <p>Flight Data from: The OpenSky Network, https://opensky-network.org</p>
        <script type="text/javascript">
            var map = new ol.Map({
                target: 'map',
                layers: [
                    new ol.layer.Tile({
                    source: new ol.source.OSM()
                    })
                ],
                view: new ol.View({
                    center: ol.proj.fromLonLat([-1.743507,52.452381]),
                    zoom: 1
                })
            });
            
<?php
    $flight_data = get_remote_data();
    if ($flight_data != false) {
        $flight_data_arr = json_decode($flight_data,1);
        $flight_arr = array();
        $i = 0;
        // Whilst it is not needed to preprocess the data into a secondary array,
        // it may be cleaner and more convenient for later versions
        foreach ($flight_data_arr['states'] as $key => $val) {
            $flight_arr[$i]['icao24'] = $val[0];
            $flight_arr[$i]['flight'] = $val[1];
            $flight_arr[$i]['long'] = $val[5];
            $flight_arr[$i]['lat'] = $val[6];
            $flight_arr[$i]['country'] = $val[2];
            $flight_arr[$i]['alt'] = $val[13]; //meters: 7 = baro, 13 = geo
            $flight_arr[$i]['speed'] = $val[9]; //meters per second ground speed
            $flight_arr[$i]['track'] = $val[10]; //direction
            $i++;
        }
        
        $marker_list_arr = array();
        $j = 1;
        // Add marker js
        foreach ($flight_arr as $key => $val) {
            echo("            var marker" . $j . " = new ol.Feature({");
            switch ($val['country']) {
                case "Russian Federation":
                    echo("                type: 'geoMarkerPlaneRussia',");
                    break;
                case "Belarus":
                    echo("                type: 'geoMarkerPlaneAmber',");
                    break;
                case "China":
                    echo("                type: 'geoMarkerPlaneYellow',");
                    break;
                default:
                    echo("                type: 'geoMarkerPlane',");
            }
            echo("                geometry: new ol.geom.Point(ol.proj.transform([" . $val['long'] . "," . $val['lat'] . "], 'EPSG:4326', 'EPSG:3857')),");
            echo("                name: 'Callsign: " . $val['flight'] . " (" . $val['icao24'] . ")<br/>ICAO: " . htmlspecialchars($val['country'],ENT_QUOTES) . " <br>Altitude: " .  number_format($val['alt'],0)  . " m <br>Velocity: " .  number_format(($val['speed'] * 2.236936),0)  . " mph <br>Heading: " .  number_format($val['track'],0)  . " deg',\n");
            echo("                operator: '" . strtoupper($val['icao24']) . "',");
            echo("            });");
            $marker_list_arr[] = "marker" . $j;
            $j++;
        }
            echo("            var markers = new ol.source.Vector({");
            echo("                features: [");
            foreach ($marker_list_arr as $key => $val) {
                echo($val . ",");
            }
            echo("]");
            echo("            });");
   }
?>
            
            var CircleStyle = ol.style.Circle;        
            var {Fill, Icon, Stroke, Style} = ol.style;
            
            var styles = {
                'geoMarkerPlane': new Style({
                    image: new CircleStyle({
                        radius: 5,
                        fill: new Fill({color: 'green'}),
                        stroke: new Stroke({
                            color: 'green',
                            width: 3,
                        }),
                    }),
                }),
                'geoMarkerPlaneRussia': new Style({
                    image: new CircleStyle({
                        radius: 5,
                        fill: new Fill({color: 'red'}),
                        stroke: new Stroke({
                            color: 'red',
                            width: 3,
                        }),
                    }),
                }),
                'geoMarkerPlaneYellow': new Style({
                    image: new CircleStyle({
                        radius: 5,
                        fill: new Fill({color: 'yellow'}),
                        stroke: new Stroke({
                            color: 'yellow',
                            width: 3,
                        }),
                    }),
                }),
                'geoMarkerPlaneAmber': new Style({
                    image: new CircleStyle({
                        radius: 5,
                        fill: new Fill({color: '#ffbf00'}),
                        stroke: new Stroke({
                            color: '#ffbf00',
                            width: 3,
                        }),
                    }),
                }),
            };
<?php
    if ($flight_data != false) {
?>        
            var markerVectorLayer = new ol.layer.Vector({
                source: markers,
                style: function (feature) {
                    return styles[feature.get('type')];
                },
            });
            map.addLayer(markerVectorLayer);

            const element = document.getElementById('popup');

            const popup = new ol.Overlay({
                element: element,
                positioning: 'bottom-center',
                stopEvent: false,
                offset: [0, -10],
            });
            map.addOverlay(popup);

            map.on('click', function (event) {
                $(element).popover('dispose');

                const feature = map.getFeaturesAtPixel(event.pixel)[0];
                if (feature) {
                    const coordinate = feature.getGeometry().getCoordinates();
                    popup.setPosition([
                        coordinate[0],
                        coordinate[1],
                    ]);
                    $(element).popover({
                        container: element.parentElement,
                        html: true,
                        sanitize: false,
                        content: feature.get('name') + "<br/><a href='https://www.planespotters.net/hex/" + feature.get('operator') + "'>PlaneSpotters.net Lookup</a><br/><a href='https://globe.adsbexchange.com/?icao=" + feature.get('operator') + "'>adsbexchange.com Lookup</a>",
                        placement: 'top',
                    });
                    $(element).popover('show');
                }
            });
<?php
    }
?>
        </script>
    </body>
</html>
