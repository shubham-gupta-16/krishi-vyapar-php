<?php
require_once dirname(__FILE__, 3) . "/includes/class.API.php";

$count = 0;
try {

    $db = API::db();

    $inJson = jToArr("libs/in.json");

    $in = "CREATE TABLE IF NOT EXISTS test_india (
        `id` INT(11),
        `city` varchar(200),
        `lat` decimal(11,8),
        `lng` decimal(11,8),
        `population` bigint(13) default 0,
        `proper` bigint(13) default 0,
        `state` varchar(200),
        PRIMARY KEY (`id`)
    )";

    if (!API::db()->query($in)) {
        throw new Exception("Error while creating posts_requests table");
    }


    $i = 0;
    foreach ($inJson as $d) {
        $data = (array) $d;
        $i++;
        $city = $data['city'];
        $lat = $data['lat'];
        $lng = $data['lng'];
        $an = $data['admin_name'];
        $population = $data['population'];
        $population_proper = $data['population_proper'];
        $res = $db->query("UPDATE in_districts SET lat = $lat, lng = $lng WHERE name = '$city'");
        if (!$res) {
            echo "-----------------ERROR at $city------------------\n";
        }
    }

    API::printSuccess([
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}

function jToArr(string $file):array
{
    $str = file_get_contents($file);
    return json_decode($str);
}
