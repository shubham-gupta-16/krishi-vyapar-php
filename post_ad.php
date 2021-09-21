<?php
require_once dirname(__FILE__, 1) . "/includes/class.API.php";
require_once dirname(__FILE__, 1) . "/libs/PHPFastAuth.php";
require_once dirname(__FILE__, 1) . "/libs/PHPImageHandler.php";


$__count = 0;
try {
    API::initResponseTime();
    $auth = new PHPFastAuth(API::db());
    $__count++;
    $token = API::getAuthToken();
    $uid = $auth->verifyToken($token);
    $user = $auth->getUser($uid);

    $title = API::receivePOST('title', true);
    $desc = API::receivePOST('desc', true);
    $price = API::receivePOST('price', true);
    $quantity = API::receivePOST('quantity', true);
    $unit = API::receivePOST('unit', true);
    $extras = API::receivePOST('extras', true);
    $locText = API::receivePOST('locText', true);
    $lat = API::receivePOST('lat', true);
    $lng = API::receivePOST('lng', true);
    $categoryId = API::receivePOST('categoryId', true);
    $subCategoryId = API::receivePOST('subCategoryId', true);

    $createdAt = API::getCurrentTimeForMySQL();

    $res = API::db()->query("INSERT INTO part (uid, title, description, price, quantity, unit, categoryId,
     subCategoryId, tags, locText, loc, createdAt, type, extras) VALUES (
        '$uid', '$title', '$desc', '$price', '$quantity', '$unit', '$categoryId',
     '$subCategoryId', '', '$locText', POINT($lat, $lng), '$createdAt', 0, '$extras')");

    if (!$res) {
        throw new Exception(API::UNKNOWN_ERROR_MSG, API::UNKNOWN_ERROR_CODE);
    }

    $imageHandler = new PHPImageHandler(API::db(), dirname(__FILE__,2) . '/images');
    $postID = getLastPostID(API::db());
    $index = 1;
    while (isset($_FILES['image_' . $index])) {
        $newImage = PHPImageHandler\NewImage::for('part', $postID)->setFile($_FILES['image_' . $index]['tmp_name']);
        $newImage->setMoreResolutions(360, 144);
        $imageHandler->addImage($newImage);
        $index++;
    }

    API::printSuccess([
        'files' => $_FILES
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$__count>");
}

function getLastPostID(mysqli $db) : int
{
    $qForID = "SELECT max(id) from part";
    $idRes = $db->query($qForID);
    if (!$idRes) {
        return 0;
    }
    $nextID = $idRes->fetch_array()[0];
    return $nextID;
}
