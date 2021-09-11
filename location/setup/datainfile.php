<?php
require_once dirname(__FILE__, 3) . "/includes/class.API.php";

$count = 0;
try {

    // $handle = fopen("libs/i2_cities.txt", "r");
    // $i = [];
    // if ($handle) {
    //     while (($line = fgets($handle)) !== false) {
    //         $arr = explode("	", $line);
    //         $key = $arr['0'];
    //         if (isset($i[$key])) {
    //             echo $key . "\n";
    //         }
    //         $i[$key] = 1;

    //     }

    //     fclose($handle);
    // }

    // die();


    $db = API::db();

    $in = "CREATE TABLE IF NOT EXISTS i4 (
        `sid` FLOAT NOT NULL,
        `name` varchar(200),
        `asciiname` varchar(200),
        `geoname_id` INT(11) NOT NULL,
        PRIMARY KEY (`sid`)
    )";

    $count++;
    if (!$db->query($in)) {
        throw new Exception("Error while creating india table");
    }


    $res = $db->query("LOAD DATA INFILE 'libs/i2_states.txt'
    INTO TABLE i4
    FIELDS TERMINATED by '	'
    LINES TERMINATED BY '\n'");

    API::printSuccess([
        "terms" => "ok",
        "res" => $res,
        "count" => $count
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
