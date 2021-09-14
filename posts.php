<?php
require_once dirname(__FILE__, 1) . "/includes/class.API.php";
require_once dirname(__FILE__, 1) . "/includes/class.QueryManager.php";
require_once dirname(__FILE__, 1) . "/libs/PHPFastAuth.php";

$count = 0;
try {
    API::initResponseTime();
    $auth = new PHPFastAuth(API::db());
    $auth->install();
    $count++;

    $db = API::db();
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
        $ord = $queryManager->getGroupOrder(['posts.title', 'posts.category', 'posts.subCategory'], '', '%');
        $ord .= $queryManager->getGroupOrder(['posts.title', 'posts.category', 'posts.subCategory'], '%', '%');
        $ord .= $queryManager->getInidividualOrder(['posts.title', 'posts.category', 'posts.subCategory']);
        $orderElseCount = $queryManager->orderElseCount();
        $orderCase .= "$ord else $orderElseCount end";
        
        $andLogic = "AND (" . $queryManager->getBaseLogic(['posts.title', 'posts.category', 'posts.subCategory'], '', '%');
        $andLogic .= $queryManager->getIndividualLogic(['posts.title', 'posts.category', 'posts.subCategory'], '', '%') . ")";
    }

    // die($andLogic);

    $result = $db->query("SELECT *, ST_DISTANCE(POINT($lat, $lng), posts.loc) AS dist FROM posts
        WHERE type = $type
        $andLogic
        ORDER BY dist $orderCase
        LIMIT 10
    ;");

    $arr = [];
    while ($row = $result->fetch_assoc()) {
        $arr[] = [
            'dist' => $row['dist'],
            'title' => $row['title'],
        ];
    }

    API::printSuccess([
        "count" => $arr,
        "responseTime" => API::getResponseTime()
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
