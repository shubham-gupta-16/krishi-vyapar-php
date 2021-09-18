<?php
require_once dirname(__FILE__, 1) . "/includes/class.API.php";
require_once dirname(__FILE__, 1) . "/libs/PHPFastAuth.php";

const CATEGORY_VERSION = 7;
const SUB_CATEGORY_VERSION = 7;

$__count = 0;
try {
    API::initResponseTime();
    $auth = new PHPFastAuth(API::db());
    $__count++;
    $token = API::getAuthToken();
    $uid = $auth->verifyToken($token);
    $user = $auth->getUser($uid);

    $categoryVersion = API::receivePOST("categoryVersion", true);
    $subCategoryVersion = API::receivePOST("subCategoryVersion", true);

    $categoryArr = [];
    $subCategoryArr = [];
    if ($categoryVersion < CATEGORY_VERSION) {
        $res = API::db()->query("SELECT * FROM z_category");
        while ($row = $res->fetch_assoc()) {
            $row['json'] = json_decode($row['json']);
            $categoryArr[] = $row;
        }
    }
    if ($subCategoryVersion < SUB_CATEGORY_VERSION) {
        $res = API::db()->query("SELECT * FROM z_sub_category");
        while ($row = $res->fetch_assoc()) {
            $row['json'] = json_decode($row['json']);
            $subCategoryArr[] = $row;
        }
    }
    API::printSuccess([
        'name' => $user['name'],
        'categoryVersion' => CATEGORY_VERSION,
        'subCategoryVersion' => SUB_CATEGORY_VERSION,
        'category' => $categoryArr,
        'subCategory' => $subCategoryArr,
    ]);

} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$__count>");
}
