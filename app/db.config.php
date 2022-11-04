<?php
session_start();
ini_set('memory_limit', '256M');
//ini_set("memory_limit", '512M');
$db = mysqli_connect("127.0.0.1", "root", "root", "sneaky");