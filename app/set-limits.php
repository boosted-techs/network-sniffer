<?php
include_once "db.config.php";

function add_limits() {
    global $db;
    $lv_tf = (int)$_POST['liveTf'];
    $cumulative = (int)$_POST['liveCf'];
    $date = date("Y-m-d");
    mysqli_query($db, "insert into alarm_limits (date_added, live_tf_limit, live_cum_limit) values('$date', '$lv_tf', '$cumulative')");
    header("location:alarm.php?s=2");
}

add_limits();