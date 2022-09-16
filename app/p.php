<?php
include_once "db.config.php";

//$a = shell_exec("sudo -u welcome whoami");
//var_dump($a);

function get_packets($eth) {
    $a = shell_exec("sudo ../sneaky/src/main/main -sneaky=0");
    var_dump($a);
}