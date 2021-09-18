<?php
require_once dirname(__FILE__, 1) . "/includes/class.API.php";
require_once dirname(__FILE__, 1) . "/libs/PHPFastAuth.php";

$count = 0;
try {
    $auth = new PHPFastAuth(API::db());
    $auth->install();
    $count++;

    $db = API::db();

    $post_request = "CREATE TABLE IF NOT EXISTS `post_request` (
        `id` INT(11) NOT NULL AUTO_INCREMENT ,
        `uid` VARCHAR(255) NOT NULL ,
        `title` VARCHAR(255) NOT NULL ,
        `description` TEXT NULL ,
        `price` FLOAT NOT NULL default 0 ,
        `quantity` FLOAT NOT NULL default 0 ,
        `unit` VARCHAR(50) NOT NULL,
        `categoryId` INT(11) NOT NULL ,
        `subCategoryId` INT(11) NOT NULL ,
        `tags` VARCHAR(255) NOT NULL,
        `locText` VARCHAR(255) NOT NULL,
        `loc` POINT NOT NULL ,
        `viewsCount` TINYINT(1) NOT NULL default 0 ,
        `favCount` TINYINT(1) NOT NULL default 0 ,
        `createdAt` DATETIME NOT NULL ,
        `type` TINYINT(1) NOT NULL default 0,
        `status` INT(11) NOT NULL default 0,
        `extras` JSON NULL ,
        PRIMARY KEY (`id`)
    );";


    if (!$db->query($post_request)) {
        throw new Exception("Error while creating post_request table");
    }
    $count++;

    API::printSuccess([
        "terms" => "ok",
        "count" => $count
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
