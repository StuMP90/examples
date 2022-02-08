<?php
// Elastic http://localhost:9200/
// Kibana  http://localhost:5601/

$ela_hosts = ['localhost:9200'];
require '../vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->setHosts($ela_hosts)->build();

//** USA History */
try {
    // Disease.sh data source for USA history
    $url = "https://disease.sh/v3/covid-19/historical/usa?lastdays=all";

    // Use CURL to fetch data from API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    $json = curl_exec($ch);
    curl_close($ch);

    // Decode raw data from API
    $jshistorydec = json_decode($json,true);
    
    // As the data no longer includes recovery data, just use the cases and deaths data.
    $casesarr = array();
    $deatharr = array();

    // Data is cumulative and uses USA date formats, so we need to convert the date to something more reliable...
    
    // Firstly cases...
    foreach ($jshistorydec['timeline']['cases'] as $tlk => $tlv) {
        $pieces = explode('/',$tlk);
        $tlktme = mktime(0,0,0,$pieces[0],$pieces[1],$pieces[2]);
        $tlkdte = date("Y/m/d",$tlktme);
        // There can be multiple records for a single day from some of these APIs, so need to add them up
        if (isset($casesarr[$tlktme])) {
            $casesarr[$tlktme] = $casesarr[$tlktme] + $tlv;
        } else {
            $casesarr[$tlktme] = $tlv;
        }
    }

    // Then deaths...
    foreach ($jshistorydec['timeline']['deaths'] as $tlk => $tlv) {
        $pieces = explode('/',$tlk);
        $tlktme = mktime(0,0,0,$pieces[0],$pieces[1],$pieces[2]);
        $tlkdte = date("Y/m/d",$tlktme);
        // There can be multiple records for a single day from some of these APIs, so need to add them up
        if (isset($deatharr[$tlktme])) {
            $deatharr[$tlktme] = $deatharr[$tlktme] + $tlv;
        } else {
            $deatharr[$tlktme] = $tlv;
        }
    }
    
    // Sort the arrays on the record's timestamp
    asort($casesarr);
    asort($deatharr);
    
    // Combined array to hold all data
    $combarr = array();

    // Add daily and cumulative cases to combined array
    $prevtot = 0;
    foreach ($casesarr as $tlk => $tlv) {
        $combarr[$tlk]['dtestr'] = date("Y/m/d",$tlk);
        $combarr[$tlk]['cumcases'] = $tlv;
        $combarr[$tlk]['daycases'] = $tlv - $prevtot;
        $prevtot = $tlv;
    }

    // Add daily and cumulative deaths to combined array
    $prevtot = 0;
    foreach ($deatharr as $tlk => $tlv) {
        $combarr[$tlk]['dtestr'] = date("Y/m/d",$tlk);
        $combarr[$tlk]['cumdeaths'] = $tlv;
        $combarr[$tlk]['daydeaths'] = $tlv - $prevtot;
        $prevtot = $tlv;
    }

    // Now add/update the data in elasticsearch
    $updrec = 0;
    foreach ($combarr as $key => $value) {
        $params = [
            'index' => 'usa_covid_history',
            'id'    => $key,
            'body'  => [
                'doc' => [
                    'dtetms' => $key,
                    'dtestr' => $value['dtestr'],
                    'cumcases' => $value['cumcases'],
                    'daycases' => $value['daycases'],
                    'cumdeaths' => $value['cumdeaths'],
                    'daydeaths' => $value['daydeaths'],
                ],
                'doc_as_upsert' => true,
            ]
        ];
        $response = $client->update($params);
        $updrec++;
    }

} catch (Exception $e) {
  
}
echo("USA History: " . $updrec . " records updated.\r\n");
?>