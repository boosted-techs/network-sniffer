<?php
include_once "db.config.php";
if (! isset($_SESSION['username']))
    die("Access denied");
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
    $traffic_monitor['devices'] = isset($_SESSION['connected']) ? $_SESSION['connected'] : 0;
    record_alarms(($traffic_monitor["live_in"] + $traffic_monitor["live_out"]), ($traffic_monitor['in'] + $traffic_monitor['out']));
    return $traffic_monitor;
}

if(isset($_GET['stats']))
    echo json_encode(read_interface_packets_packets());

//if(isset($_GET['graph']))
//   echo json_encode(getGraphStats());
//
//function getGraphStats() {
//    global $db;
//    $q = mysqli_query($db,"select * from live_packets  order by id desc limit 100");
//    $interface = get_interfaces();
//    $traffic_monitor[0] = [];
//    $traffic_monitor[1] = [];
//    $traffic_monitor['time'] = [];
//    while ($s = mysqli_fetch_array($q)) {
//        //print_r($s['packet_info']);
//        $d = explode(" ", $s['packet_info']);
//        $dst_ip = explode("=", $d[43]);
//        $packet_size = isset($d[1]) ? $d[1] : 0; //Bytes
//        $destination_ip = isset($dst_ip[1]) ? $dst_ip[1] : 0;
//        if (in_array($destination_ip, $interface))
//            $traffic_monitor[0][] += round(($packet_size/1024), 3);
//        else
//            $traffic_monitor[1][] += round(($packet_size/1024), 3);
//        $traffic_monitor['time'][] = $s['_time'];
//    }
//    return $traffic_monitor;
//}

if(isset($_GET['graph']))
    echo json_encode(getGraphStats());

function getGraphStats() {
    global $db;
    $q = mysqli_query($db,"select * from live_packets  where date_added = '" . date("Y-m-d") . "' order by id desc limit 5000 ");
    $interface = get_interfaces();
    $traffic_monitor[0] = [];
    $traffic_monitor[1] = [];
    $traffic_monitor['time'] = [];
    while ($s = mysqli_fetch_array($q)) {
        //print_r($s['packet_info']);
        $d = explode(" ", $s['packet_info']);
        $dst_ip = explode("=", $d[43]);
        $packet_size = isset($d[1]) ? $d[1] : 0; //Bytes
        $destination_ip = isset($dst_ip[1]) ? $dst_ip[1] : 0;
        if (in_array($s['_time'], $traffic_monitor['time'])) {
            $key = array_search($s['_time'], $traffic_monitor['time']);
            if (in_array($destination_ip, $interface))
                $traffic_monitor[0][$key] = round(($packet_size / 1024), 3);

            else
                $traffic_monitor[1][$key] = round(($packet_size / 1024), 3);
        } else {
            if (in_array($destination_ip, $interface)) {
                $traffic_monitor[0][] = round(($packet_size / 1024), 3);
                $traffic_monitor[1][] = 0;
            } else {
                $traffic_monitor[1][] = round(($packet_size / 1024), 3);
                $traffic_monitor[0][] = 0;
            }
            $traffic_monitor['time'][] = $s['_time'];
        }
    }
    return $traffic_monitor;
}

function record_alarms($live_traffic, $general_traffic) {
    global $db;
    $query = mysqli_query($db, "select * from alarm_limits order by id desc limit 1");
    if (mysqli_num_rows($query) < 1)
        return;

    while($row = mysqli_fetch_array($query)) {
        $live_tf_limit = $row['live_tf_limit'];
        $cum_tf_limit = $row['live_cum_limit'];
        $date = date("Y-m-d");
        //Live traffic reports
        if ($live_tf_limit > 0 && $live_tf_limit <= $live_traffic) {
            //If live limit is greater than 0, then report else it means reports where turned off
            $live = mysqli_query($db, "select id from alarm where date_added = '$date' and live_tf_in = '$live_traffic'") or die(mysqli_error($db));
            if (mysqli_num_rows($live) < 1)
                mysqli_query($db, "insert into alarm (date_added, live_tf_in, cum_tf_in, cum_tf, live_tf) 
                        values('$date', '$live_traffic', '$general_traffic', '$cum_tf_limit', '$live_tf_limit')") or die(mysqli_error($db));
        }
        if ($cum_tf_limit > 0 && $cum_tf_limit <= $general_traffic) {
            //If live limit is greater than 0, then report else it means reports where turned off
            $live = mysqli_query($db, "select id from alarm where date_added = '$date' and cum_tf_in = '$general_traffic'") or die(mysqli_error($db));
            if (mysqli_num_rows($live) < 1)
                mysqli_query($db, "insert into alarm (date_added, live_tf_in, cum_tf_in, cum_tf, live_tf, _type) 
                values('$date', '$live_traffic', '$general_traffic', '$cum_tf_limit', '$live_tf_limit', '2')") or die(mysqli_error($db));
        }
    }
}

if (isset($_GET['alarm']))
    echo json_encode(get_alarms());

function get_alarms() {
    global $db;
    $query = mysqli_query($db, "select * from alarm order by _timestamp desc limit 200");
    $table = "";
    $i = 1;
    $traffic_time[2] = [];
    $traffic_time[1] = [];
    $traffic_time[0] = [];

    while($row = mysqli_fetch_array($query)) {
        $traffic_time[2][] =  $row['_timestamp'];
        $traffic_time[1][] = $row['live_tf_in'];
        $traffic_time[0][] = $row['cum_tf_in'];
        $table .= "<tr>";
        $table .= "<td>" . $i . "</td>";
        $table .= "<td>" . ($row['date_added']) . "</td>";
        $table .= "<td>" . $row['live_tf_in'] . "Mbs</td>";
        $table .= "<td>" . $row['cum_tf_in'] . "Mbs</td>";
        $table .= "<td>" . $row['_timestamp'] . "</td>";
        $table .= "<td>" . ($row['_type'] == 1 ? "Live traffic has reached limits" : "General traffic limits reached") . "</td>";
        $table .= "</tr>";
        $i++;
    }
    $traffic_time['content'] = $table;
    return $traffic_time;
}
