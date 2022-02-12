<?php
// Elastic http://localhost:9200/

$ela_hosts = ['localhost:9200'];
require './vendor/autoload.php';

// Get USA data and set upper timestamp to "1 second to 2038"
function getUsaData ($startts = 0, $endts = 2147483646) {
    global $ela_hosts;
    $client = Elasticsearch\ClientBuilder::create()->setHosts($ela_hosts)->build();
    
    if (is_int($startts) && ($startts > 0) && ($startts < 2147483647) && ($startts < $endts)) {
        $startts_safe = $startts;
    } else {
        $startts_safe = 0;
    }
    
    if (is_int($endts) && ($endts > 0) && ($endts < 2147483647) && ($startts < $endts)) {
        $endts_safe = $endts;
    } else {
        $endts_safe = 2147483646;
    }
    
    $params = [
        'index' => 'usa_covid_history',
        'from' => 0,
        'size' => 9999,
        'sort' => '_id',
        'body'  => [
            'query' => [
                'range' => [
                    'dtetms' => [
                        'gte' => $startts_safe,
                        'lte' => $endts_safe
                    ]
                ]
            ]
        ]
    ];
    $return_data = array();
    try {
        $result = $client->search($params);
        if (is_array($result) && ($result['hits']['total']['value'] > 0)) {
            $avgcaseshist = array();
            $avgdeathhist = array();
            foreach($result['hits']['hits'] as $key => $val) {
                // Calculate 7 day averages
                if (count($avgcaseshist) > 6) { // All arrays should be the same length
                    array_shift($avgcaseshist);
                    array_shift($avgdeathhist);
                }
                $avgcaseshist[] = $val['_source']['daycases'];
                $avgdeathhist[] = $val['_source']['daydeaths'];
                if(count($avgcaseshist)) {
                    $averagecases = round(array_sum($avgcaseshist)/count($avgcaseshist),0);
                }
                if(count($avgdeathhist)) {
                    $averagedeaths = round(array_sum($avgdeathhist)/count($avgdeathhist),0);
                }

                $return_data[$val['_source']['dtetms']] = array(
                    'dtetms' => $val['_source']['dtetms'],
                    'dtestr' => $val['_source']['dtestr'],
                    'cumcases' => $val['_source']['cumcases'],
                    'daycases' => $val['_source']['daycases'],
                    'cumdeaths' => $val['_source']['cumdeaths'],
                    'daydeaths' => $val['_source']['daydeaths'],
                    'avgcases' => $averagecases,
                    'avgdeaths' => $averagedeaths
                );
            }
            // Remove the first week for a stable average
            for ($x = 0; $x < 7; $x++) {
                array_shift($return_data);
            }
        } else {
            $return_data[0] = array(
                'dtetms' => 0,
                'dtestr' => 'Error: No Data',
                'cumcases' => 0,
                'daycases' => 0,
                'cumdeaths' => 0,
                'daydeaths' => 0,
                'avgcases' => 0,
                'avgdeaths' => 0
            );
        }
    } catch (Exception $e) {
            $return_data[0] = array(
                'dtetms' => 0,
                'dtestr' => 'Error: Elasticsearch Not Running',
                'cumcases' => 0,
                'daycases' => 0,
                'cumdeaths' => 0,
                'daydeaths' => 0,
                'avgcases' => 0,
                'avgdeaths' => 0
            );
    }
    return $return_data;
}

// Get England data and set upper timestamp to "1 second to 2038"
function getEnglandData ($startts = 0, $endts = 2147483646) {
    global $ela_hosts;
    $client = Elasticsearch\ClientBuilder::create()->setHosts($ela_hosts)->build();
    
    if (is_int($startts) && ($startts > 0) && ($startts < 2147483647) && ($startts < $endts)) {
        $startts_safe = $startts;
    } else {
        $startts_safe = 0;
    }
    
    if (is_int($endts) && ($endts > 0) && ($endts < 2147483647) && ($startts < $endts)) {
        $endts_safe = $endts;
    } else {
        $endts_safe = 2147483646;
    }
    
    $params = [
        'index' => 'england_covid_history',
        'from' => 0,
        'size' => 9999,
        'sort' => '_id',
        'body'  => [
            'query' => [
                'range' => [
                    'dtetms' => [
                        'gte' => $startts_safe,
                        'lte' => $endts_safe
                    ]
                ]
            ]
        ]
    ];
    $return_data = array();
    try {
        $result = $client->search($params);
        if (is_array($result) && ($result['hits']['total']['value'] > 0)) {
            $avgcaseshist = array();
            $avgdeathhist = array();
            $avgadmithist = array();
            foreach($result['hits']['hits'] as $key => $val) {
                // Calculate 7 day averages
                if (count($avgcaseshist) > 6) { // All arrays should be the same length
                    array_shift($avgcaseshist);
                    array_shift($avgdeathhist);
                    array_shift($avgadmithist);
                }
                $avgcaseshist[] = $val['_source']['daycases'];
                $avgdeathhist[] = $val['_source']['daydeaths'];
                $avgadmithist[] = $val['_source']['dayadmits'];
                if(count($avgcaseshist)) {
                    $averagecases = round(array_sum($avgcaseshist) / count($avgcaseshist),0);
                }
                if(count($avgdeathhist)) {
                    $averagedeaths = round(array_sum($avgdeathhist) / count($avgdeathhist),0);
                }
                if(count($avgadmithist)) {
                    $averageadmits = round(array_sum($avgadmithist) / count($avgadmithist),0);
                }

               $return_data[$val['_source']['dtetms']] = array(
                    'dtetms' => $val['_source']['dtetms'],
                    'dtestr' => $val['_source']['dtestr'],
                    'daycases' => $val['_source']['daycases'],
                    'daydeaths' => $val['_source']['daydeaths'],
                    'dayadmits' => $val['_source']['dayadmits'],
                    'avgcases' => $averagecases,
                    'avgdeaths' => $averagedeaths,
                    'avgadmits' => $averageadmits
                );
            }
            // Remove the first week for a stable average
            for ($x = 0; $x < 7; $x++) {
                array_shift($return_data);
            }
        } else {
            $return_data[0] = array(
                'dtetms' => 0,
                'dtestr' => 'Error: No Data',
                'daycases' => 0,
                'daydeaths' => 0,
                'dayadmits' => 0,
                'avgcases' => 0,
                'avgdeaths' => 0,
                'avgadmits' => 0
            );
        }
    } catch (Exception $e) {
            $return_data[0] = array(
                'dtetms' => 0,
                'dtestr' => 'Error: Elasticsearch Not Running',
                'cumcases' => 0,
                'daycases' => 0,
                'cumdeaths' => 0,
                'daydeaths' => 0,
                'avgcases' => 0,
                'avgdeaths' => 0
            );
    }
    return $return_data;
}
