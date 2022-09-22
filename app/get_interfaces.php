<?php
include_once "db.config.php";
if (! isset($_SESSION['username']))
    die("Access denied");
function _get_interfaces_today($date) {
    global $db;

    $d = shell_exec("sudo -u root ../sneaky/src/main/main -sneaky=0 -nic=en0");

    $query = mysqli_query($db, "select * from interfaces where date_added = '$date' and _read = 0 order by id desc") or die(mysqli_error($db));
    $results = "";
    mysqli_query($db, "update interfaces set _read = 1");
    $i = 1;
    while ($row = mysqli_fetch_array($query)) {
        $r = explode(".", $row['ipv4']);
        if (count($r) != 4)
            continue;
        $string = "<tr>";
        $string .= "<td>" . $i . "</td>";
        $string .= "<td><a href='./app/monitor.php?l=". $row['interface'] ."'>" . $row['interface'] . "</a></td>";
        $string .= "<td>" . $row['ipv4'] . "</td>";
        $results .= $string;
        $_SESSION['connected'] = $i;
        $i++;
    }
    return $results;
}

$r = _get_interfaces_today(date("Y-m-d"));
echo json_encode($r);