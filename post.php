<?php
require_once dirname(__FILE__, 1) . "/includes/class.API.php";
require_once dirname(__FILE__, 1) . "/includes/class.QueryManager.php";
require_once dirname(__FILE__, 1) . "/libs/PHPFastAuth.php";
require_once dirname(__FILE__, 1) . "/libs/PHPImageHandler.php";

$count = 0;
try {
    API::initResponseTime();
    $auth = new PHPFastAuth(API::db());
    $count++;

    $id = (int) API::receivePOST("id", true);

    $q_run = "SELECT *, ST_X(part.loc) as lat, ST_Y(part.loc) as lng FROM part, fast_auth_users
        WHERE part.id = $id
        AND part.uid = fast_auth_users.uid
        LIMIT 1
    ;";

    // die($q_run);

    $result = API::db()->query($q_run);

    $imageHandler = new PHPImageHandler(API::db(), dirname(__FILE__) . '/images');
    
    $arr = [];
    if ($row = $result->fetch_assoc()) {
        $images = $imageHandler->getImagesFor('part', $row['id']);
        $arr = [
            'id' => $row['id'],
            'title' => $row['title'],
            'des' => $row['description'],
            'price' => $row['price'],
            'quantity' => $row['quantity'],
            'unit' => $row['unit'],
            'categoryId' => $row['categoryId'],
            'subCategoryId' => $row['subCategoryId'],
            'views' => $row['viewsCount'],
            'fav' => $row['favCount'],
            'createdAt' => $row['createdAt'],
            'status' => $row['status'],
            'extras' => $row['extras'],
            'images' => $images,
            'user' => [
                'uid'=> $row['uid'],
                'name'=> $row['name'],
                'mobile'=> '+91 ' . $row['mobile'],
            ],
            'location' => [
                'locText' => $row['locText'],
                'lat' => $row['lat'],
                'lng' => $row['lng'],
            ],
        ];
    }

    API::printSuccess([
        "post" => $arr,
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
