<?php
session_start();
if (isset($_GET['protocol']) and $_GET['protocol'] == "http") {
    if ($_GET['l'] == 1)
        $_SESSION['http'] = 1;
    else
        unset($_SESSION['http']);
}

if (isset($_GET['protocol']) and $_GET['protocol'] == "icmp") {
    if ($_GET['l'] == 1)
        $_SESSION['icmp'] = 1;
    else
        unset($_SESSION['icmp']);
}

if (isset($_GET['protocol']) and $_GET['protocol'] == "port80") {
    if ($_GET['l'] == 1)
        $_SESSION['port80'] = 1;
    else
        unset($_SESSION['port80']);
}

if (isset($_GET['protocol']) and $_GET['protocol'] == "port443") {
    if ($_GET['l'] == 1)
        $_SESSION['port443'] = 1;
    else
        unset($_SESSION['port443']);
}

if (isset($_GET['protocol']) and $_GET['protocol'] == "tcp") {
    if ($_GET['l'] == 1)
        $_SESSION['tcp'] = 1;
    else
        unset($_SESSION['tcp']);
}