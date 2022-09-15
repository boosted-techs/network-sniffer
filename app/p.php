<?php
include_once "db.config.php";

$a = shell_exec("../sneaky/src/main/main -sneaky=0 -rp=en0");
var_dump($a);