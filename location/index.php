<?php
require_once dirname(__FILE__, 2) . "/includes/class.API.php";
require_once dirname(__FILE__, 2) . "/includes/class.QueryManager.php";

$count = 0;
try {
    API::initResponseTime();
    $db = API::db();

    $q = API::receivePOST('q', true);
    $q = str_replace(',', '', $q);
    $q = $db->real_escape_string($q);

    $queryManager = new QueryManager($q);

    $ord = $queryManager->getGroupOrder(['i4.asciiname', 'i3.asciiname', 'i_towns.asciiname'], '', '%');
    $ord .= $queryManager->getGroupOrder(['i4.asciiname', 'i3.asciiname', 'i_towns.asciiname'], '%', '%');
    $ord .= $queryManager->getInidividualOrder(['i4.asciiname', 'i3.asciiname', 'i_towns.asciiname']);
    $orderElseCount = $queryManager->orderElseCount();

    $logic = $queryManager->getBaseLogic(['i_towns.asciiname', 'i3.asciiname'], '', '%');
    $logic .= $queryManager->getIndividualLogic(['i_towns.asciiname', 'i3.asciiname', 'i4.asciiname'], '', '%');
    
    // die($logic);

    $result = $db->query("SELECT CONCAT(i_towns.asciiname, ', ', i3.asciiname, ', ', i4.asciiname) as locText,
        i_towns.latitude as lat, i_towns.longitude as lng
        FROM i_towns, i3, i4 
        WHERE i_towns.geonameid != i3.geoname_id
        AND i_towns.admin2_code = i3.cid
        AND i_towns.admin1_code = i4.sid
        AND i3.state = i4.sid
        -- AND i_towns.feature_class = 'P'
        AND ($logic)
        order by case 
            $ord
            else $orderElseCount
        end, 
        i_towns.asciiname, i3.asciiname

    LIMIT 10");
    if (!$result)
        throw new Exception("STMT Failed", 1);
    $arr = [];
    while ($row = $result->fetch_assoc()) {
        $arr[] = $row;
    }

    API::printSuccess([
        "locations" => $arr,
        "count" => $count
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
