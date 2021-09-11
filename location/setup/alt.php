<?php
require_once dirname(__FILE__, 3) . "/includes/class.API.php";

$count = 0;
try {

    $db = API::db();


    $distircts = fopen("libs/cities.txt", "r");
    if ($distircts) {
        $mainArr = [];
        while (($line = fgets($distircts)) !== false) {
            $arr = explode("---", $line);
            $district_id = $arr['0'];
            $name = $arr['1'];
            $state_id = $arr['2'];

            $dis = $db->query("SELECT * FROM in_districts WHERE district_id = $district_id");
            

            $row = $dis->fetch_array();
            if (!$row) {
                echo "error at $district_id";
                continue;
            }

            if ($row['lat'] == 0) {
                $ind = $db->query("SELECT * FROM india WHERE asciiname = '$name'");
                if ($res = $ind->fetch_array()) {
                    $lat = $res['latitude'];
                    $lng = $res['longitude'];
                    $db->query("UPDATE in_districts SET lat = $lat, lng = $lng WHERE district_id = $district_id");
                }
            }
        }
        fclose($distircts);
    }


    API::printSuccess([
        "terms" => "ok",
        "count" => $count
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
