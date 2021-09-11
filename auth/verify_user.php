<?php

require_once dirname(__FILE__, 2) . "/includes/class.API.php";
require_once dirname(__FILE__, 2) . "/libs/PHPFastAuth.php";

try {
    $auth = new PHPFastAuth(API::db());
    $token = API::getAuthToken();
    $uid = $auth->verifyToken($token);
    $user = $auth->getUser($uid);

    API::printSuccess([
        'name' => $user['name']
    ]);
} catch (Exception $e) {
    API::printError($e);
}
