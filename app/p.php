<?php
include_once "db.config.php";

//$a = shell_exec("sudo -u welcome whoami");
//var_dump($a);

//function get_packets($eth) {
//    $a = shell_exec("sudo ../sneaky/src/main/main -sneaky=0 -lp=en0");
//    var_dump($a);
//}

function get_interface($interface) {
    global $db;
    $query = mysqli_query($db, "select * from interfaces where interface = '$interface' order by id desc limit 1") or die(mysqli_error($db));
    return mysqli_fetch_array($query);
}

function read_packets($interface) {
    global $db;
    $q = mysqli_query($db,"select * from live_packets where device = '$interface' order by id desc");
    $interface = get_interface($interface);
    $traffic_in = 0;
    $traffic_out = 0;
    $traffic_monitor = [];
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
        $src_port = isset($d[53]) ? $d[53] : NULL;
        $destination_port = isset($d[54]) ? $d[54] : NULL;
        $src_ip = explode("=", $d[42]);
        $dst_ip = explode("=", $d[43]);
        $source_ip = isset($src_ip[1]) ? $src_ip[1] : 0;
        $destination_ip = isset($dst_ip[1]) ? $dst_ip[1] : 0;

        if ($destination_ip == $interface['ipv4']) {
            $traffic_in += ($packet_size/1000);
            $ob['traffic'] = 'IN';
        } else {
            $traffic_out += ($packet_size/1000);
            $ob['traffic'] = "OUT";
        }

        /*
         * Create an array of results
         */
        $ob['packet_size'] = $packet_size;
        $ob['timestamp'] = $time_stamp;
        $ob['layer_one'] = $layer_1;
        $ob['layer_type'] = $layer_type;
        $ob['src_mac'] = $src_mac;
        $ob['dst_mac'] = $destination_mac;
        $ob['src_port'] = $src_port;
        $ob['dst_port'] = $destination_port;
        $ob['src_ip'] = $source_ip;
        $ob['dst_ip'] = $destination_ip;
        $traffic_monitor[] = $ob;
    }
    $traffic_monitor['traffic_in'] = $traffic_in;
    $traffic_monitor['traffic_out'] = $traffic_out;
    return $traffic_monitor;
}

if (isset($_GET['d']))
    echo json_encode(read_packets($_GET['d']));