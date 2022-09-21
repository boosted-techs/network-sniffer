<?php
include_once "db.config.php";
function login() {
    $password = hash("sha256", $_POST['password']);
    $username = $_POST['username'];
    global $db;
    $query = mysqli_query($db, "select username from login where username = '$username' and password = '$password'") or die(mysqli_error($db));
    if (mysqli_num_rows($query) > 0)
        $_SESSION['username'] = $username;
    header("location:" . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_POST['login']))
    login();
if (isset($_GET['out'])) {
    session_destroy();
    header("location:" . $_SERVER['HTTP_REFERER']);
    exit;
}
