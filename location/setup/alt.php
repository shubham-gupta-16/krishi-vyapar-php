<?php
require_once dirname(__FILE__, 3) . "/includes/class.API.php";

$count = 0;
try {

    $db = API::db();

    $select = $db->query("SELECT cid, asciiname FROM i3"); #40
    while ($row = $select->fetch_assoc()) {
        $name = $row['asciiname'];
        $cid = $row['cid'];
        echo "$cid --- $name\n";
        // $hi = translate($name);
        // if ($hi != null) {
        //     $trans = $hi->data->translations[0]->translatedText;
        //     $update = $db->query("UPDATE i3 SET name = '$trans' WHERE asciiname = '$name'");

        //     if ($update) {
        //         echo "$name -> $trans\n";
        //     } else {
        //         echo "$name ```error```\n";
        //     }
        // }

    }
    die();
    

    API::printSuccess([
        "terms" => "ok",
        "count" => $count
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}

function translate($text)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://google-translate1.p.rapidapi.com/language/translate/v2",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "q=$text&target=hi&source=en",
        CURLOPT_HTTPHEADER => [
            "accept-encoding: application/gzip",
            "content-type: application/x-www-form-urlencoded",
            "x-rapidapi-host: google-translate1.p.rapidapi.com",
            "x-rapidapi-key: a1deaa8ecdmshf17d7f6b05a63f5p1ce0e5jsna86c2d4d81d4"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return null;
    } else {
        return json_decode($response);
    }
}
