<?php
require_once dirname(__FILE__, 2) . "/includes/class.API.php";

$count = 0;
try {
    $db = API::db();
    $time_init = microtime(true) * 1000;

    $q = API::receivePOST('q', true);
    $qArr = explode(" ", $q, 5);
    $q = $db->real_escape_string($q);
    $_q_ = "%$q%";



    $oc = 1;
    $logic = "i2.asciiname LIKE '%$q%' OR i3.asciiname LIKE '%$q%'";
    // $logic .= " OR '$q' LIKE CONCAT('%', i2.asciiname, '%') OR '$q' LIKE CONCAT('%', i3.asciiname, '%')";
    $order = 
    " WHEN i2.asciiname LIKE '$q' THEN ". $oc++ .
    " WHEN i3.asciiname LIKE '$q' THEN " . $oc++ . 
    // " WHEN i2.asciiname LIKE '$q%' THEN " . $oc++ . 
    // " WHEN i3.asciiname LIKE '$q%' THEN " . $oc++ . 
    // " WHEN i2.asciiname LIKE '%$q%' THEN " . $oc++ .
    // " WHEN i3.asciiname LIKE '%$q%' THEN " . $oc++ .
    "";
    

    foreach ($qArr as $val) {
        $logic .= " OR i2.asciiname LIKE '%$val%' OR i3.asciiname LIKE '%$val%' OR i4.asciiname LIKE '%$val%'";
        $order .=
        " WHEN i2.asciiname LIKE '$val' THEN " . $oc++ .
        " WHEN i3.asciiname LIKE '$val' THEN " . $oc++ .
        " WHEN i4.asciiname LIKE '$val' THEN " . $oc++ .
        "";
    }

    foreach ($qArr as $val) {
        $order .=
            " WHEN i2.asciiname LIKE '$val%' THEN " . $oc++ .
            " WHEN i3.asciiname LIKE '$val%' THEN " . $oc++ .
            " WHEN i4.asciiname LIKE '$val%' THEN " . $oc++ .
            "";
    }

    foreach ($qArr as $val) {
        $order .=
            " WHEN i2.asciiname LIKE '%$val%' THEN " . $oc++ .
            " WHEN i3.asciiname LIKE '%$val%' THEN " . $oc++ .
            " WHEN i4.asciiname LIKE '%$val%' THEN " . $oc++ .
            "";
    }

    // echo $order;

    $result = $db->query("SELECT i2.geonameid as id, i2.asciiname as locality, i3.asciiname as city, i4.asciiname as state,
        i2.latitude as lat, i2.longitude as lng, i2.feature_class
        FROM i2, i3, i4 
        WHERE i2.geonameid NOT IN (i3.geoname_id)
        AND i2.admin2_code = i3.cid
        AND i2.admin1_code = i4.sid
        AND i3.state = i4.sid
        AND ($logic)
        order by case 
            $order
            else $oc
        end

    LIMIT 10");
    if (!$result) 
        throw new Exception("STMT Failed", 1);
    $arr = [];
    while ($row = $result->fetch_assoc()) {
        $arr[] = $row;
    }

    $time_finish = microtime(true) * 1000;

    API::printSuccess([
        "time" => $time_finish - $time_init,
        "locations" => $arr,
        "count" => $count
    ]);
} catch (Exception $e) {
    API::printError($e, $e->getMessage() . " <$count>");
}
