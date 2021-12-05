<?php

$root_server = "https://bmlt.narcotiquesanonymes.org";
$API_KEY="";

$meetings = json_decode(get("$root_server/client_interface/json/?switcher=GetSearchResults"), true);
$serverInfo = json_decode(get("$root_server/client_interface/json/?switcher=GetServerInfo"), true);
$regionBias = $serverInfo[0]['regionBias'];

#echo "bmlt_id,city,zip_code,county,state,country\n";
foreach ($meetings as $meeting) {
    $id = $meeting['id_bigint'];
    $city = $meeting['location_municipality'];
    $zip_code = $meeting['location_postal_code_1'];
    $county = $meeting['location_sub_province'];
    $state = $meeting['location_province'];
    if (trim($meeting['location_nation']) != "") {
        $country = $meeting['location_nation'];
    } else {
        $country = $regionBias;
    }

    if ($city && $state) {
        $location = "$city, $state";
        // If the country is here we will use it to help with geocoding, but
        // the update will be by city and state
        if ($country) {
            $location .= ", $country";
        }
    } else {
        $location = "";
        // If city is here we will use it to help with geocoding, but
        // the update will be by country plus county/state/zip
        if ($city) {
            $location = $city;
        }

        if ($county) {
            $location .= ($location ? ", $county" : $county);
        }

        if ($state) {
            $location .= ($location ? ", $state" : $state);
        }

        if ($zip_code) {
            $location .= ($location ? ", $zip_code" : $zip_code);
        }

        if ($country) {
            $location .= ($location ? ", $country" : $country);
        }
    }

    $addressString = urlencode($location);
    $gecoded = json_decode(get("https://maps.googleapis.com/maps/api/geocode/json?key=$API_KEY&address=$addressString"), true);
    $components = $gecoded['results'][0]['address_components'];
    $formattedAddress = $gecoded['results'][0]['formatted_address'];
    $componentTypes = array();
    foreach ($components as $component) {
        if (isset($component['types'])) {
            $componentTypes[] .= $component['types'][0];
        }
    }
    $ComponentTypesString = implode(",", $componentTypes);
    echo "\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
    echo "LocationString: " . $location . "\n";
    echo "ComponentTypes: " . $ComponentTypesString . "\n";
    echo "FormattedAddress: " . $formattedAddress . "\n";
}


function get($url)
{
    #error_log($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0) +bmlttz');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    $errorno = curl_errno($ch);
    curl_close($ch);
    if ($errorno > 0) {
        throw new Exception(curl_strerror($errorno));
    }

    return $data;
}
