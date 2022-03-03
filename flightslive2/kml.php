<?php
// Due to Google Maps timeouts, this has to pre-generate the KML file...
ob_start();

// Echo xml opener due to usual tag conflict
echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
?>
<kml xmlns="http://www.opengis.net/kml/2.2">
  <Document>
    <Style id="arrowIcon0">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-0.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon1">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-1.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon2">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-2.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon3">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-3.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon4">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-4.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon5">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-5.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon6">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-6.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon7">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-7.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon8">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-8.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon9">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-9.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon10">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-10.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon11">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-11.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon12">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-12.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon13">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-13.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon14">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-14.png</href>
           </Icon>
        </IconStyle>
    </Style>
    <Style id="arrowIcon15">
        <IconStyle>
           <scale>1.0</scale>
           <Icon>
              <href>https://earth.google.com/images/kml-icons/track-directional/track-15.png</href>
           </Icon>
        </IconStyle>
    </Style>
<?php
// Fetch remote data with Curl
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
    // Convert json to php array
    $data_arr_a = json_decode($data_arr,1);
    // Write a placemark for each plane
    foreach ($data_arr_a['states'] as $key => $val) {
        //icao24 = $val[0];
        //flight = $val[1];
        //long = $val[5];
        //lat = $val[6];
        //country = $val[2];
        //alt = $val[13]; //meters: 7 = baro, 13 = geo
        //speed = $val[9]; //meters per second ground speed
        //track = $val[10]; //direction
?>
    <Placemark>
      <name><?= $val[1] ?> (<?= $val[0] ?>)</name>
      <styleUrl>#arrowIcon<?= number_format(floor($val[10]/22.5),0) ?></styleUrl>
      <description>
        <![CDATA[
          <p><strong>Callsign: <?= $val[1] ?></strong><br />
          ICAO24: <?= $val[0] ?><br />
          ICAO Origin: <?= htmlspecialchars($val[2],ENT_QUOTES) ?><br />
          Lat,Long: <?= $val[6] ?>,<?= $val[5] ?><br />
          Altitude: <?= number_format($val[13],0) ?> metres<br />
          Speed: <?= number_format(($val[9] * 2.236936),0) ?> mph<br />
          Heading: <?= number_format($val[10],0) ?> degrees<br />
          Links: <a href="https://www.planespotters.net/hex/<?= strtoupper($val[0]) ?>/" target="_blank">PlaneSpotters</a> &middot; <a href="https://globe.adsbexchange.com/?icao=<?= $val[0] ?>" target="_blank">ADSBExchange</a>
          </p>
        ]]>
      </description>
      <Point>
        <coordinates><?= $val[5] ?>,<?= $val[6] ?></coordinates>
      </Point>
    </Placemark>
<?php
    }
}
?>
  </Document>
</kml>
<?php
$contents = ob_get_flush();
// Generate .kml file
file_put_contents("kml.kml",$contents);
// And a .kmz/zip file due to size limits
$zip = new ZipArchive;
$res = $zip->open('kml.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
if ($res === TRUE) {
    $zip->addFile('kml.kml', 'doc.kml');
    $zip->close();
}
ob_clean();
