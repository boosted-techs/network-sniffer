<?php
function get_os() {
    $os_version = php_uname();
    echo json_encode(array("os_version" => $os_version));
}

if(isset($_GET['os']))
    get_os();
if(isset($_GET['os2']))
    get_traffic();

function get_traffic() {
    /*
     * Get underlying traffic for the network adaptor
     */
    $no = rand(0, 4000);
    $no1 = rand (0, 5000);
    echo json_encode(array("in" => $no, "out" => $no1));
}
