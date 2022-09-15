<?php
include_once "db.config.php";

function _get_stats() {
    global $db;
    $query = mysqli_query($db, "select count(id) as connections, (select count(id) from interfaces where date_added = '" . date("Y-m-d") . "' and ipv4 != '') as _today from interfaces where ipv4 != ''") or die(mysqli_error($db));
    $d = ["connections" => 0, "today" => 0];
    while ($row = mysqli_fetch_array($query)) {
        $d['connections'] = number_format($row['connections']);
        $d['today'] = number_format($row['_today']);
    }
    return $d;
}

if(isset($_GET['stats']))
    echo json_encode(_get_stats());
