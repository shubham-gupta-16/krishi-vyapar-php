<?php

require_once dirname(__FILE__, 2) . "/includes/class.API.php";
require_once dirname(__FILE__, 2) . "/libs/PHPFastAuth.php";

try {
    $name = API::receivePOST('name', true);

    $auth = new PHPFastAuth(API::db());
    $uid = $auth->verifyToken(API::getAuthToken());
    
    $auth->updateName($uid, $name);

    API::printSuccess();
} catch (Exception $e) {
    API::printError($e);
}
