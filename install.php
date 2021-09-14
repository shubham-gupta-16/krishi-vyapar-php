<?php
require_once dirname(__FILE__, 1) . "/includes/class.API.php";
require_once dirname(__FILE__, 1) . "/libs/PHPFastAuth.php";

$count = 0;
try {
    $auth = new PHPFastAuth(API::db());
    $auth->install();
    $count++;

    $db = API::db();

    $post_request = "CREATE TABLE IF NOT EXISTS `posts` (
        `post_id` INT(11) NOT NULL AUTO_INCREMENT ,
        `uid` VARCHAR(255) NOT NULL ,
        `title` VARCHAR(255) NOT NULL ,
        `description` TEXT NULL ,
        `price` FLOAT NOT NULL default 0 ,
        `category` VARCHAR(255) NOT NULL ,
        `subCategory` VARCHAR(255) NOT NULL ,
        `loc` POINT NOT NULL ,
        `address` VARCHAR(255) NOT NULL,
        `viewsCount` TINYINT(1) NOT NULL default 0 ,
        `favCount` TINYINT(1) NOT NULL default 0 ,
        `createdAt` DATETIME NOT NULL ,
        `type` TINYINT(1) NOT NULL default 0,
        `status` INT(11) NOT NULL default 0,
        `extras` JSON NULL ,
        PRIMARY KEY (`post_id`)
    );";


    if (!$db->query($post_request)) {
        throw new Exception("Error while creating posts_requests table");
    }
    $count++;

    API::printSuccess([
        "terms" => "ok",
        "count" => $count
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
