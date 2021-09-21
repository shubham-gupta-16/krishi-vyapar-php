<?php

require_once './final_cat_list.php';

require_once dirname(__FILE__, 1) . '/includes/class.API.php';

API::db()->query("DROP TABLE IF EXISTS `z_category`");
API::db()->query("DROP TABLE IF EXISTS `z_sub_category`");

API::db()->query("CREATE TABLE IF NOT EXISTS `z_sub_category` (
        `sub_category_id` INT(11) NOT NULL AUTO_INCREMENT ,
        `category_id` INT(11) NOT NULL ,
        `json` JSON NOT NULL ,
        `status` INT(11) NOT NULL default 1,
        `position` INT(11) NOT NULL default 0,
        PRIMARY KEY (`sub_category_id`)
    )");

API::db()->query("CREATE TABLE IF NOT EXISTS `z_category` (
        `category_id` INT(11) NOT NULL AUTO_INCREMENT ,
        `json` JSON NOT NULL ,
        `status` INT(11) NOT NULL default 1,
        `position` INT(11) NOT NULL default 0,
        PRIMARY KEY (`category_id`)
    )");

foreach ($category as $each) {
    $category_id = $each['category_id'];
    $json = json_encode($each['json'], JSON_UNESCAPED_UNICODE);
    API::db()->query("INSERT INTO `z_category` (`category_id`, `json`) VALUES ($category_id, '$json')");
}

foreach ($sub_category as $each) {
    $sub_category_id = $each['sub_category_id'];
    $category_id = $each['category_id'];
    $json = json_encode($each['json'], JSON_UNESCAPED_UNICODE);
    API::db()->query("INSERT INTO `z_sub_category` (`sub_category_id`, `category_id`, `json`) VALUES ($sub_category_id, $category_id, '$json')");
}
