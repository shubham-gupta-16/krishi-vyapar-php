<?php
require_once dirname(__FILE__, 3) . "/includes/class.API.php";

$url = "http://49a5-2409-4063-419f-abf2-e9f3-7c84-bc24-e840.ngrok.io/krishi-vyapar-app-api/krishi-vyapar-php/location/setup/alt.php";

try {
    $db = API::db();
    $size = API::receivePOST("size", true);
    $q = API::receivePOST("q", true);
    $vare = (array) loadData($url, $q, $size);
    print_r($vare);

    foreach ($vare as $arr) {
        $arr = (array) $arr;
        $geonameid = $arr['geonameid'];
        $asciiname = $arr['asciiname'];
        $alternatenames = $arr['alternatenames'];
        $latitude = $arr['latitude'];
        $longitude = $arr['longitude'];
        $feature_class = $arr['feature_class'];
        $admin1_code = $arr['admin1_code'];
        $admin2_code = $arr['admin2_code'];

        $res = $db->query("INSERT INTO i_towns (geonameid, asciiname, alternatenames, latitude, longitude, feature_class, 
    admin1_code, admin2_code) VALUES (
        '$geonameid',
        '$asciiname',
        '$alternatenames',
        '$latitude',
        '$longitude',
        '$feature_class',
        '$admin1_code',
        '$admin2_code')");

        if (!$res) {
            echo "error";
        }
    }
    
    
} catch (\Throwable $th) {
    echo $th->getMessage();
}


function loadData($url, $key, $size): array
{

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "q=$key&size=$size",
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return [];
    } else {
        return json_decode($response);
    }
}
