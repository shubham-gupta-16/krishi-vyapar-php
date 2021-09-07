<?php
require_once dirname(__FILE__, 2) . "/includes/class.API.php";
require_once dirname(__FILE__, 2) . "/libs/PHPFastAuth.php";

try {
    $mobile = API::receivePOST('mobile', true);

    $auth = new PHPFastAuth(API::db());
    $user = $auth->getUserWithMobile($mobile);
    API::printSuccess([
        'uid' => $user['uid'],
        'name' => $user['name']
    ]);
} catch (Exception $e) {
    API::printError($e);
}
