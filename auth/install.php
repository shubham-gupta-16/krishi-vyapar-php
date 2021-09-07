<?php
require_once dirname(__FILE__, 2) . "/includes/class.API.php";
require_once dirname(__FILE__, 2) . "/libs/PHPFastAuth.php";

try {
    $auth = new PHPFastAuth(API::db());
    $auth->install();
    API::printSuccess([
        "status"=>"ok"
    ]);
} catch (Exception $e) {
    API::printError($e);
}
