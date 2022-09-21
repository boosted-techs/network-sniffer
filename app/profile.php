<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <title>
        Sneaky
    </title>
    <meta name="description" content="Network">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css"/>
    <link rel="icon" type="image/png" href="../logo.png">
    <link rel="stylesheet" type="text/css" href="../style.css"/>
</head>
<body>
<?php
if (! isset($_SESSION['username'])) {
    ?>
    <div class="login">
        <div class="col-md-6 mx-auto mt-5 p-5 bg-white">
            <div class="text-center">
                <img src="../logo.png" alt="Network Sniffer" style="width:100px;" class="rounded-pill">
            </div>
            <form action="login.php" method="post">
                <h6>Username</h6>
                <input type="text" class="form-control rounded-0" name="username"/>
                <h6>Password</h6>
                <input type="password" class="form-control rounded-0" name="password"/>
                <input type="hidden" name="login"/>
                <button class="btn btn-primary rounded-0 mt-3 form-control" type="submit">LOGIN</button>
            </form>
        </div>
    </div>
    <?php
}
?>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="../">
            <img src="../logo.png" alt="Network Sniffer" style="width:40px;" class="rounded-pill">
        </a>
        <span class="navbar-text">SNEAKY</span>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="#">HOME</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../app/alarm.php">Alarm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../app/login.php?out">LOGOUT</a>
            </li>
            <li class="nav-item bg-white p-3 text-dark" onclick="window.location='profile.php'">
                <img src="../user.png" alt="Network Sniffer" style="width:20px;" class="rounded-pill">
                <?=isset($_SESSION['username']) ? $_SESSION['username'] : 'USER'?>
            </li>
        </ul>
    </div>
</nav>
<div class="col-md-6 mx-auto mt-5">
    <div class="card shadow border-0">
        <div class="card-header">
            <h3 class="card-title">Update password</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                You will be logged out after a successful password update.
            </div>
            <form action="changePassword.php" method="post">
                <label>Old password</label>
                <input type="text" name="oldPwd" class="form-control" placeholder="Default password is admin"/>
                <label>new password</label>
                <input type="password" name="newPwd" class="form-control mt-3" placeholder="Enter new password"/>
                <label>Repeat password</label>
                <input type="password" name="newPwd1" class="form-control mt-3" placeholder="Repeat password"/>
                <button class="btn btn-primary form-control rounded-0 mt-3" type="submit">Update password</button>
            </form>
        </div>
    </div>
</div>
</body>