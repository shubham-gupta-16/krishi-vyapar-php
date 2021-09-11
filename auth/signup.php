<?php

require_once dirname(__FILE__, 2) . "/includes/class.API.php";
require_once dirname(__FILE__, 2) . "/libs/PHPFastAuth.php";

try {
    $mobile = API::receivePOST('mobile', true);
    $uid = API::receivePOST('uid', true);

    $auth = new PHPFastAuth(API::db());
    if (!$auth->isUserExistWithMobile($mobile)) {
        $signUp = new PHPFastAuth\SignUpWithMobile($mobile);

        // todo uncomment this in production
        // $signUp->setUid($uid);

        $auth->forceSignUp($signUp);
    }
    $user = $auth->getUserWithMobile($mobile);
    /* todo uncomment in production
     if ($user['uid'] !== $uid) {
        throw PHPFastAuth\Errors::ERROR_USER_NOT_EXIST();
    } */

    $signIn = new PHPFastAuth\SignInWithUID($user['uid']);

    $token = $auth->signInWithoutPassword($signIn);
    API::printSuccess([
        'token' => $token,
        'uid' => $signIn->uid,
        'name' => $user['name'],
    ]);
} catch (Exception $e) {
    API::printError($e);
}
