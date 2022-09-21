<?php
include_once "db.config.php";

if (! isset($_SESSION['username']))
    die("Access denied");
$pwd = hash("sha256", $_POST['oldPwd']);
$newPwd = $_POST['newPwd'];
$newPwd1 = $_POST['newPwd1'];
if (strcmp($newPwd1, $newPwd) != 0)
    die("Passwords donot match");
$data = mysqli_query($db, "select username from login where password = '$pwd'") or die(mysqli_error($db));
if (mysqli_num_rows($data) < 1)
    die("Wrong password provided.");
$password = hash("sha256", $newPwd);
mysqli_query($db, "update login set password = '$password' where username = '" . $_SESSION['username'] . "'") or die(mysqli_error($db));

header("location:login.php?out=1");
exit;
