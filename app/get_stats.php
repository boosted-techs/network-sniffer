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



function get_interfaces() {
    global $db;
    $query = mysqli_query($db, "select ipv4 from interfaces group by ipv4") or die(mysqli_error($db));
    $d = [];
    while ($row = mysqli_fetch_array($query)) {
        $d[] = $row['ipv4'];
    }
    return $d;
}

function read_interface_packets_packets() {
    global $db;
    $q = mysqli_query($db,"select * from live_packets  order by id desc");
    $interface = get_interfaces();
    $traffic_in = 0;
    $traffic_out = 0;
    $traffic_monitor = [];
    $i = 0;
    $live_tf_in = 0;
    $live_tf_out= 0;
    while ($s = mysqli_fetch_array($q)) {
        $ob = [];
        //print_r($s['packet_info']);
        $d = explode(" ", $s['packet_info']);
        $dst_ip = explode("=", $d[43]);
        $packet_size = isset($d[1]) ? $d[1] : 0; //Bytes
        $destination_ip = isset($dst_ip[1]) ? $dst_ip[1] : 0;
        if (in_array($destination_ip, $interface)) {
            $traffic_in += ($packet_size/1024);
            if ($s['_read'] == 0)
                $live_tf_in += ($packet_size/1024);
        } else {
            $traffic_out += ($packet_size/1024);
            if ($s['_read'] == 0)
                $live_tf_out += ($packet_size/1024);
        }

        if ($s['_read'] == 0)
            mysqli_query($db, "update live_packets set _read = 1 where id = '" . $s['id'] . "'");
    }
    $traffic_monitor['in'] = round(($traffic_in/1024), 4);
    $traffic_monitor['out'] = round(($traffic_out/1024), 4);
    $traffic_monitor["live_in"] = round(($live_tf_in/1024), 3);
    $traffic_monitor["live_out"] = round(($live_tf_out/1024), 3);
    return $traffic_monitor;
}

if(isset($_GET['stats']))
    echo json_encode(read_interface_packets_packets());
