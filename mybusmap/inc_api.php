<?php

/**
 * Gets live bus tracking data from the DFT API.
 *
 * @param string $ds The bus company key.
 */
function get_remote_data(string $ds)  // Fetch remote data with Curl
{
    global $apikey_dft;
    global $dftid_diamond;
    global $dftid_nxbus;
    
    if ($ds == "diamond") {
        $api_url = "https://data.bus-data.dft.gov.uk/api/v1/datafeed/" . $dftid_diamond . "/?api_key=" . $apikey_dft;
    } elseif ($ds == "nxbus") {
        $api_url = "https://data.bus-data.dft.gov.uk/api/v1/datafeed/" . $dftid_nxbus . "/?api_key=" . $apikey_dft;
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
