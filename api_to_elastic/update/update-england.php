<?php
// Elastic http://localhost:9200/
// Kibana  http://localhost:5601/

$ela_hosts = ['localhost:9200'];
require '../vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->setHosts($ela_hosts)->build();

//** ENGLAND History */
try {
    // Disease.sh data source for USA history
    $url = 'https://api.coronavirus.data.gov.uk/v1/data?filters=areaType=nation' . ";" . 'areaName=england&structure={%22date%22:%22date%22,%22newCases%22:%22newCasesByPublishDate%22,%22newDeaths%22:%22newDeaths28DaysByDeathDate%22,%22newAdmissions%22:%22newAdmissions%22,%22covidOccupiedMVBeds%22:%22covidOccupiedMVBeds%22,%22hospitalCases%22:%22hospitalCases%22}';

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
    
    // Combined array to hold all data
    $combarr = array();

    // "data":[{"date":"2020-09-17","newCases":2788,"newDeaths":null}
    
    foreach ($jshistorydec['data'] as $histkey => $histval) {
        $pieces = explode('-',$histval['date']);
        $tlktme = mktime(0,0,0,$pieces[2],$pieces[1],$pieces[0]);
        $tlkdte = date("Y/m/d",$tlktme);
        $combarr[$tlktme]['dtestr'] = $tlkdte;
        
        // Firstly cases
        if ($histval['newCases'] > 0) {
            // There can be multiple records for a single day from some of these APIs, so need to add them up
            if (isset($combarr[$tlktme]['daycases'])) {
                $combarr[$tlktme]['daycases'] = $combarr[$tlktme]['daycases'] + $histval['newCases'];
            } else {
                $combarr[$tlktme]['daycases'] = $histval['newCases'];
            }
        } else {
            if (!(isset($combarr[$tlktme]['daycases']))) {
                $combarr[$tlktme]['daycases'] = 0;
            }
        }
        
        // Then deaths
        if ($histval['newDeaths'] > 0) {
            // There can be multiple records for a single day from some of these APIs, so need to add them up
            if (isset($combarr[$tlktme]['daydeaths'])) {
                $combarr[$tlktme]['daydeaths'] = $combarr[$tlktme]['daydeaths'] + $histval['newDeaths'];
            } else {
                $combarr[$tlktme]['daydeaths'] = $histval['newDeaths'];
            }
        } else {
            if (!(isset($combarr[$tlktme]['daydeaths']))) {
                $combarr[$tlktme]['daydeaths'] = 0;
            }
        }
        
        // Then hospital admissions
        if ($histval['newAdmissions'] > 0) {
            // There can be multiple records for a single day from some of these APIs, so need to add them up
            if (isset($combarr[$tlktme]['dayadmits'])) {
                $combarr[$tlktme]['dayadmits'] = $combarr[$tlktme]['dayadmits'] + $histval['newAdmissions'];
            } else {
                $combarr[$tlktme]['dayadmits'] = $histval['newAdmissions'];
            }
        } else {
            if (!(isset($combarr[$tlktme]['dayadmits']))) {
                $combarr[$tlktme]['dayadmits'] = 0;
            }
        }
    }

    
    // Sort the array on the record's timestamp
    asort($combarr);
    
    // Now add/update the data in elasticsearch
    $updrec = 0;
    foreach ($combarr as $key => $value) {
        $params = [
            'index' => 'england_covid_history',
            'id'    => $key,
            'body'  => [
                'doc' => [
                    'dtetms' => $key,
                    'dtestr' => $value['dtestr'],
                    'daycases' => $value['daycases'],
                    'daydeaths' => $value['daydeaths'],
                    'dayadmits' => $value['dayadmits'],
                ],
                'doc_as_upsert' => true,
            ]
        ];
        $response = $client->update($params);
        $updrec++;
    }

} catch (Exception $e) {
  
}
echo("England History: " . $updrec . " records updated.\r\n");
?>