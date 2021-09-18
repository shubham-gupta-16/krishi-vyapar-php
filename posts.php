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

        $andLogic = "AND (" . $queryManager->getBaseLogic(['posts.title', 'posts.category', 'posts.subCategory'], '%', '%');
        $andLogic .= $queryManager->getIndividualLogic(['posts.title', 'posts.category', 'posts.subCategory'], '%', '%') . ")";
    }

    // die($andLogic);

    $q_run = "SELECT *, ST_X(posts.loc) as lat, ST_Y(posts.loc) as lng, ST_DISTANCE(POINT($lat, $lng), posts.loc) AS dist FROM posts, fast_auth_users
        WHERE posts.type = $type
        $andLogic
        AND posts.uid = fast_auth_users.uid
        ORDER BY dist $orderCase
        LIMIT 10
    ;";

    // die($q_run);

    $result = $db->query($q_run);

    $arr = [];
    while ($row = $result->fetch_assoc()) {
        $arr[] = [
            'id' => $row['post_id'],
            'title' => $row['title'],
            'des' => $row['description'],
            'price' => $row['price'],
            'category' => $row['category'],
            'subCategory' => $row['subCategory'],
            'views' => $row['viewsCount'],
            'fav' => $row['favCount'],
            'createdAt' => $row['createdAt'],
            'status' => $row['status'],
            'extras' => $row['extras'],
            'user' => [
                'uid'=> $row['uid'],
                'name'=> $row['name'],
            ],
            'location' => [
                'locText' => $row['address'],
                'lat' => $row['lat'],
                'lng' => $row['lng'],
            ],
        ];
    }

    API::printSuccess([
        "posts" => $arr,
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
