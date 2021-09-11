<?php
require_once dirname(__FILE__, 1) . "/includes/class.API.php";
require_once dirname(__FILE__, 1) . "/libs/PHPFastAuth.php";

$count = 0;
try {
    $auth = new PHPFastAuth(API::db());
    $auth->install();
    $count++;

    $db = API::db();

    $post_request = "CREATE TABLE IF NOT EXISTS `posts_requests` (
        `id` INT(11) NOT NULL AUTO_INCREMENT ,
        `uid` VARCHAR(255) NOT NULL ,
        `title` VARCHAR(255) NOT NULL ,
        `description` TEXT NULL ,
        `price` FLOAT NOT NULL default 0 ,
        `category` VARCHAR(255) NOT NULL ,
        `subCategory` VARCHAR(255) NOT NULL ,
        `loc` POINT NOT NULL ,
        `viewsCount` TINYINT(1) NOT NULL default 0 ,
        `favCount` TINYINT(1) NOT NULL default 0 ,
        `createdAt` DATETIME NOT NULL ,
        `type` TINYINT(1) NOT NULL default 0,
        `status` INT(11) NOT NULL default 0,
        `extras` JSON NULL ,
        PRIMARY KEY (`id`)
    );";

    $in = "CREATE TABLE IF NOT EXISTS india (
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

    if (!$db->query($post_request)) {
        throw new Exception("Error while creating posts_requests table");
    }
    $count++;
    if (!$db->query($in)) {
        throw new Exception("Error while creating india table");
    }
    $count++;

    API::printSuccess([
        "terms" => "ok",
        "count" => $count
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
