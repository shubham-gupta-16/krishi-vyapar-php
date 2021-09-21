<?php
require_once dirname(__FILE__, 3) . "/includes/class.API.php";

$count = 0;
try {

    $db = API::db();
    $size = API::receivePOST("size", true);
    $q = API::receivePOST("q", true);

    $arr = [];
    $select = $db->query("SELECT * FROM i_towns LIMIT $q, $size"); #40
    while ($row = $select->fetch_assoc()) {
        $arr[] = $row;
    }
    

    echo json_encode($arr);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
