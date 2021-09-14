<?php
require_once dirname(__FILE__, 3) . "/includes/class.API.php";

$count = 0;
try {
    $db = API::db();

    $in = "CREATE TABLE IF NOT EXISTS i_towns (
        `geonameid` INT(11),
        `name` varchar(200),
        `asciiname` varchar(200),
        `alternatenames` varchar(10000),
        `latitude` decimal(11,8),
        `longitude` decimal(11,8),
        `feature_class` varchar(1),
        `feature_code` varchar(10),
        `country_code` varchar(2),
        `cc2` varchar(200),
        `admin1_code` varchar(20),
        `admin2_code` varchar(80) ,
        `admin3_code` varchar(20),
        `admin4_code` varchar(20),
        `population` bigint(13) ,
        `elevation` int(11),
        `dem` int(11),
        `timezone` varchar(40),
        `modification` varchar(200),
        PRIMARY KEY (`geonameid`)
    )";

    $count++;
    if (!$db->query($in)) {
        throw new Exception("Error while creating india table");
    }


    $res = $db->query("LOAD DATA INFILE 'libs/i2.txt'
    INTO TABLE i_towns
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
