<?php
require_once __DIR__ . '/config.php';


function get_remote_data($ds = "")  // Fetch remote data with Curl
{
    if ($ds == "diamond") {
        $api_url = "https://data.bus-data.dft.gov.uk/api/v1/datafeed/763/?api_key=ADD_YOUR_API_KEY";
    } elseif ($ds == "nxbus") {
        $api_url = "https://data.bus-data.dft.gov.uk/api/v1/datafeed/6583/?api_key=ADD_YOUR_API_KEY";
    } else {
        return false;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

    // Fetch and check for curl errors
    $result = curl_exec($ch);
    $return_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errors = curl_error($ch);
    curl_close($ch);
    if (($result !== false) && ($return_code == 200)) {
        $data = simplexml_load_string($result);  // Parse remote data
        if (is_object($data)) {  // Was the data parsed
            $data_arr = json_decode(json_encode($data),1);
            return $data_arr;
        } else {
            return false;
        }
    }
}
