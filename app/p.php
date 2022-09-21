<?php
include_once "db.config.php";
if (! isset($_SESSION['username']))
    die("Access denied");

//$a = shell_exec("sudo -u welcome whoami");
//var_dump($a);

//function get_packets($eth) {
//    $a = shell_exec("sudo ../sneaky/src/main/main -sneaky=0 -lp=en0");
//    var_dump($a);
//}

function get_interface($interface) {
    global $db;
    $query = mysqli_query($db, "select ipv4 from interfaces where interface = '$interface' order by id desc") or die(mysqli_error($db));
    $d = [];
    while ($row = mysqli_fetch_array($query)) {
        if (in_array($row['ipv4'], $d))
            continue;
        $d[] = $row['ipv4'];
    }
    return $d;
}

function read_packets($interface) {
    global $db;
    $q = mysqli_query($db,"select * from live_packets where device = '$interface' order by id desc");
    $interface = get_interface($interface);
    $traffic_in = 0;
    $traffic_out = 0;
    $traffic_monitor = [];
    $live_tf_in = 0;
    $live_tf_out= 0;
    $i = 0;
    while ($s = mysqli_fetch_array($q)) {
        $ob = [];
        //print_r($s['packet_info']);
        $d = explode(" ", $s['packet_info']);
        $packet_size = isset($d[1]) ? $d[1] : 0; //Bytes
        $time_stamp = $d[10] . " " . $d[11];  //Bytes
        $layer_1 = $d[16]; //Bytes
        $layer_type = $d[23]; //Layer type eg Ethernet
        $src_mac = $d[21]; //Source Mac address
        $destination_mac = $d[22]; //Destination MacAddress
        $src_port = isset($d[53]) ? trim($d[53]) : NULL;
        $destination_port = isset($d[54]) ? trim($d[54]) : NULL;
        $src_port = explode("=", $src_port);
        $src_port = isset($src_port[1]) ? explode("(", $src_port[1])[0] : NULL;

        $destination_port = explode("=", $destination_port);
        $destination_port = isset($destination_port[1]) ? explode("(", $destination_port[1])[0] : NULL;
        //$destination_port = $destination_port != null ? explode("=", $destination_port)[1] : NULL;
        $src_ip = explode("=", $d[42]);
        $dst_ip = explode("=", $d[43]);
        $source_ip = isset($src_ip[1]) ? $src_ip[1] : 0;
        $destination_ip = isset($dst_ip[1]) ? $dst_ip[1] : 0;

        if (in_array($destination_ip, $interface)) {
            $traffic_in += ($packet_size/1024);
            $ob['traffic'] = 'IN';
            if ($s['_read'] == 0)
                $live_tf_in += ($packet_size/1024);

        } else {
            $traffic_out += ($packet_size/1024);
            $ob['traffic'] = "OUT";
            if ($s['_read'] == 0)
                $live_tf_out += ($packet_size/1024);
        }
        if ($s['_read'] == 0)
            mysqli_query($db, "update live_packets set _read = 1 where id = '" . $s['id'] . "'");
        if ($i > 50)
            continue;
        $i++;
        /*
         * Create an array of results
         */
        $ob['packet_size'] = round(($packet_size/1024), 3);
        $ob['timestamp'] = $time_stamp;
        $ob['layer_one'] = $layer_1;
        $ob['layer_type'] = $layer_type;
        $ob['src_mac'] = $src_mac;
        $ob['dst_mac'] = $destination_mac;
        $ob['src_port'] = $src_port;
        $ob['dst_port'] = $destination_port;
        $ob['src_ip'] = $source_ip;
        $ob['dst_ip'] = $destination_ip;
        $traffic_monitor['data'][] = $ob;
    }
    $traffic_monitor['traffic_in'] = round(($traffic_in/1024), 3);
    $traffic_monitor['traffic_out'] = round(($traffic_out/1024), 3);
    $traffic_monitor["live_in"] = round(($live_tf_in/1024), 3);
    $traffic_monitor["live_out"] = round(($live_tf_out/1024), 3);
    return $traffic_monitor;
}

if (isset($_GET['d']))
    echo json_encode(read_packets($_GET['d']));