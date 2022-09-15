<?php
include_once "db.config.php";

function _get_interfaces_today($date) {
    global $db;
    $query = mysqli_query($db, "select * from interfaces where date_added = '$date' order by id desc") or die(mysqli_error($db));
    $results = "";
    $i = 1;
    while ($row = mysqli_fetch_array($query)) {
        $string = "<tr>";
        $string .= "<td>" . $i . "</td>";
        $string .= "<td>" . $row['interface'] . "</td>";
        $string .= "<td>" . $row['ipv4'] . "</td>";
        $string .= "<td>" . $row['ipv6'] . "</td>";
        $string .= "<td>" . $row['subnet'] . "</td>";
        $string .= "<td>" . $row['defaultMask'] . "</td>";
        $string .= "<td>" . $row['description'] . "</td>";
        $i++;
        $results .= $string;
    }
    return $results;
}

$r = _get_interfaces_today(date("Y-m-d"));
echo json_encode($r);