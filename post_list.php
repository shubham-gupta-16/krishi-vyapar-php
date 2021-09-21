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

    $db = API::db();
    $page = API::receivePOST("page", true);
    $lat = API::receivePOST("lat", true);
    $lng = API::receivePOST("lng", true);
    $type = (int) API::receivePOST("type", true);
    $q = API::receivePOST("q");


    $andLogic = '';
    $orderCase = '';
    if ($q != null) {
        $q = $db->real_escape_string($q);
        $queryManager = new QueryManager($q);
        $orderCase = ', CASE ';
        $ord = $queryManager->getGroupOrder(['part.title', 'part.tags', 'part.description'], '', '%');
        $ord .= $queryManager->getGroupOrder(['part.title', 'part.tags', 'part.description'], '%', '%');
        $ord .= $queryManager->getInidividualOrder(['part.title', 'part.tags', 'part.description']);
        $orderElseCount = $queryManager->orderElseCount();
        $orderCase .= "$ord else $orderElseCount end";

        $andLogic = "AND (" . $queryManager->getBaseLogic(['part.title', 'part.tags', 'part.description'], '%', '%');
        $andLogic .= $queryManager->getIndividualLogic(['part.title', 'part.tags', 'part.description'], '%', '%') . ")";
    }

    // die($andLogic);

    $q_run = "SELECT id, title, price, quantity, unit, createdAt, locText, ST_DISTANCE(POINT($lat, $lng), part.loc) AS dist FROM part
        WHERE part.type = $type
        $andLogic
        AND status = 0
        ORDER BY dist $orderCase
        LIMIT 10
    ;";

    // die($q_run);

    $result = $db->query($q_run);

    if (!$result) {
        throw new Exception("DB ERROR", -1);
        
    }

    $imageHandler = new PHPImageHandler(API::db(), dirname(__FILE__) . '/images');

    $arr = [];
    while ($row = $result->fetch_assoc()) {
        $images = $imageHandler->getImagesFor('part', $row['id'], 1);
        $image = sizeof($images) > 0 ? $images[0] : null;
        $arr[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'price' => $row['price'],
            'quantity' => $row['quantity'],
            'unit' => $row['unit'],
            'createdAt' => $row['createdAt'],
            'locText' => $row['locText'],
            'image' => $image,
        ];
    }

    API::printSuccess([
        "posts" => $arr,
        "page" => $page,
        "maxPage" => 1,
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
