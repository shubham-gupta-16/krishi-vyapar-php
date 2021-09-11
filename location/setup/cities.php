<?php
require_once dirname(__FILE__, 3) . "/includes/class.API.php";

$count = 0;
try {

    $db = API::db();

    $st = "CREATE TABLE IF NOT EXISTS `in_states` (
        `state_id` INT(11) NOT NULL,
        `name` VARCHAR(255) NOT NULL ,
        PRIMARY KEY (`state_id`)
    );";

    $di = "CREATE TABLE IF NOT EXISTS in_districts (
        `district_id` INT(11) NOT NULL,
        `name` varchar(200),
        `state_id` varchar(200),
        `lat` decimal(11,8),
        `lng` decimal(11,8),
        PRIMARY KEY (`district_id`)
    )";

    if (!API::db()->query($st)) {
        throw new Exception("Error while creating posts_requests table");
    }
    $count++;
    if (!API::db()->query($di)) {
        throw new Exception("Error while creating india table");
    }
    $count++;

    $distircts = fopen("libs/cities.txt", "r");
    if ($distircts) {
        while (($line = fgets($distircts)) !== false) {
            $arr = explode("---", $line);
            $district_id = $arr['0'];
            $name = $arr['1'];
            $state_id = $arr['2'];

            $lat = 0;
            $lng = 0;

            $insert = "INSERT INTO in_districts VALUES(
                '$district_id',
                '$name',
                '$state_id',
                '$lat',
                '$lng')";
            API::db()->query($insert);
        }
        fclose($distircts);
    }

    $states = fopen("libs/states.txt", "r");
    if ($states) {
        while (($line = fgets($states)) !== false) {
            $arr = explode("---", $line);
            $state_id = $arr['0'];
            $name = $arr['1'];

            $insert = "INSERT INTO in_states VALUES(
                '$state_id',
                '$name')";
            API::db()->query($insert);
        }
        fclose($states);
    }

    API::printSuccess([
        "terms" => "ok",
        "count" => $count
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
