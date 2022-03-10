<?php
/* 
 * Utilities, configuration and lists for search filters
 */

function search_arr() {
    $arr = array();
    $arr[] = array("sfield" => "", "sstr" => "", "sdesc" => "No Filters");
    
    $arr[] = array("sfield" => "icao24", "sstr" => "a835af", "sdesc" => "Elon Musk");
    
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "SPEEDBIRD", "sdesc" => "British Airways");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "UNITED", "sdesc" => "United Airlines Inc");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "RYANAIR", "sdesc" => "Ryanair");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "AMERICAN", "sdesc" => "American Airlines Inc");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "DELTA", "sdesc" => "Delta Air Lines");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "AEROFLOT", "sdesc" => "Aeroflot");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "POBEDA", "sdesc" => "Pobeda");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "WORLD EXPRESS", "sdesc" => "DHL");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "FEDEX", "sdesc" => "FEDEX");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "VORTEX", "sdesc" => "Royal Air Force");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "NAVY", "sdesc" => "Royal Navy");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "AIR CHIEF", "sdesc" => "United States Air Force");
    $arr[] = array("sfield" => "operatorcallsign", "sstr" => "REACH", "sdesc" => "USAF Air Mobility Command");

    $arr[] = array("sfield" => "manufacturername", "sstr" => "Airbus", "sdesc" => "Manufacturer: Airbus");
    $arr[] = array("sfield" => "manufacturername", "sstr" => "Boeing", "sdesc" => "Manufacturer: Boeing");
    $arr[] = array("sfield" => "manufacturername", "sstr" => "Bombardier", "sdesc" => "Manufacturer: Bombardier");
    $arr[] = array("sfield" => "manufacturername", "sstr" => "Cessna", "sdesc" => "Manufacturer: Cessna");
    $arr[] = array("sfield" => "manufacturername", "sstr" => "Bell", "sdesc" => "Manufacturer: Bell");
    $arr[] = array("sfield" => "manufacturername", "sstr" => "Piper", "sdesc" => "Manufacturer: Piper");
    $arr[] = array("sfield" => "manufacturername", "sstr" => "Robinson", "sdesc" => "Manufacturer: Robinson");

//    $arr[] = array("sfield" => "", "sstr" => "", "sdesc" => "");
    return $arr;
}

function search_sel($sid = 0) {
    $srch_list = search_arr();
    foreach ($srch_list as $key => $val) {
        if ($sid == $key) {
            echo '<option value="' . $key . '" selected="selected">' . htmlspecialchars($val['sdesc']) . '</option>';
        } else {
            echo '<option value="' . $key . '">' . htmlspecialchars($val['sdesc']) . '</option>';
        }
    }
}
