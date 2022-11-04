<?php
//die(hash("sha256", "admin"));
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
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css"/>
    <link rel="icon" type="image/png" href="logo.png">
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<?php
if (! isset($_SESSION['username'])) {
?>
<div class="login">
    <div class="col-md-6 mx-auto mt-5 p-5 bg-white">
        <div class="text-center">
            <img src="logo.png" alt="Network Sniffer" style="width:100px;" class="rounded-pill">
        </div>
        <form action="app/login.php" method="post">
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
        <a class="navbar-brand" href="#">
            <img src="logo.png" alt="Network Sniffer" style="width:40px;" class="rounded-pill">
        </a>
        <span class="navbar-text">SNEAKY</span>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="#">HOME</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./app/alarm.php">Alarm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./app/login.php?out">LOGOUT</a>
            </li>
            <li class="nav-item bg-white p-3 text-dark" onclick="window.location='app/profile.php'">
                <img src="user.png" alt="Network Sniffer" style="width:20px;" class="rounded-pill">
                <?=isset($_SESSION['username']) ? $_SESSION['username'] : "USER"?>
            </li>
        </ul>
    </div>
</nav>
<div class="container mb-5">
    <div class="row">
        <div class="col-md-12 text-center pt-5">
            <img src="logo.png" alt="Network Sniffer" style="width:50px;" class="rounded-pill">
            <h5 class="text-primary p-4 text-center">
                Sneaky Network Monitor
            </h5>
        </div>
        <div class="col-md-12">
            <div class="row">
                <h3 class="col-md-12">FILTER THROUGH</h3>
                <div class="p-2 col-md-2">
                    <h6>PROTOCOL</h6>
                    <input type="checkbox" name="icmp" <?=isset($_SESSION['icmp']) ? "checked" : ''?>/> ICMP <input type="checkbox" name="http" <?=isset($_SESSION['http']) ? "checked" : ''?>/> HTTP <input type="checkbox" name="tcp" <?=isset($_SESSION['tcp']) ? "checked" : ''?>/> TCP
                </div>
                <div class="p-2 col-md-2">
                    <H6>PORT</H6>
                    <input type="checkbox" name="port80" <?=isset($_SESSION['port80']) ? "checked" : ''?>/> Port 80 <input type="checkbox" name="port443" <?=isset($_SESSION['port443']) ? "checked" : ''?>/> Port 443
                </div>
            </div>
            <canvas CLASS="shadow p-4" id="chart" style="width:100%" height="200; margin-bottom:200px"></canvas>
        </div>
        <div class="col-md-12 table-responsive">
            <h4 class="text-center p-4">Connected Devices</h4>
            <table class="table table-striped w-100 shadow table-dark">
                <thead>
                    <tr class="">
                        <th>Sno</th>
                        <th>Device</th>
                        <th>Internet Protocol ADDRESS</th>
                    </tr>
                </thead>
                <tbody id="table"></tbody>
            </table>
        </div>
    </div>
</div>
<div class="bottom p-3 bg-dark text-white text-center">
    <h6 id="machineInfo" class="col-md-12"></h6>
</div>
<div class="bottom-right mt-4 border-0 border-bottom border-danger bg-transparent">

            <div class="card-header bg-dark text-white p-3 text-center">LIVE Traffic</div>
            <div class="card-body">
                <table class="table table-striped table-borderless shadow table-danger">
                    <thead>
                    <tr>
                        <th class="text-center">IN <span class='text-success'>&#8595</span></th>
                        <th class="text-center">OUT <span class='text-primary'>&#8593</span></th>
                        <th class="text-center"><small>Devices</small></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><small id="tfIn" class="text-center rounded"></small></td><td><small id="tfOut" class="text-center"></small></td>
                        <td><small id="devices"></small></td>
                    </tr>
                    </tbody>
                </table>
            </div>

</div>
<script src="./bootstrap/js/bootstrap.min.js"></script>
<script src="./jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
</script>
</body>
</html>
<script>
    $.get("traffic.php?os", function success(data){
        let d = JSON.parse(data)
        $("#machineInfo").html(d.os_version)
    })
    getInterfaces()
    function getStats() {
        $.get("app/get_stats.php?stats", function success(data){
            let d = JSON.parse(data)
            $("#tfIn").html(d.live_in + "Mbs")
            $("#tfOut").html(d.live_out + "Mbs")
            $("#devices").html(d.devices)
        })
    }

    function getInterfaces() {
        $.get("app/get_interfaces.php", function success(data) {
            console.log(data)
            let d = JSON.parse(data)
            $("#table").html(d)
        })
    }

    setInterval(function(){
        getStats()
    }, 4000)
</script>
<script src="app/script.js" type="text/javascript"></script>
<script>
    //Refreshes after a
    setInterval(function(){
        getGraphStats()
        getInterfaces()
    }, 90000)
    $("[name=icmp]").on("change", function () {
        let checked = 0;
        let check = $(this) . is(":checked")
        if (check)
            checked = 1
        $.post("app/filter.php?protocol=icmp&l=" + checked, function s(data) {
            console.log(data)
        })
    })
    $("[name=http]").on("change", function () {
        //console.log("http")
        let checked = 0;
        let check = $(this) . is(":checked")
        if (check)
            checked = 1
        $.post("app/filter.php?protocol=http&l=" + checked, function s(data) {
            console.log(data)
        })
    })

    $("[name=port80]").on("change", function () {
        //console.log("http")
        let checked = 0;
        let check = $(this) . is(":checked")
        if (check)
            checked = 1
        $.post("app/filter.php?protocol=port80&l=" + checked, function s(data) {
            console.log(data)
        })
    })

    $("[name=port443]").on("change", function () {
        //console.log("http")
        let checked = 0;
        let check = $(this) . is(":checked")
        if (check)
            checked = 1
        $.post("app/filter.php?protocol=port443&l=" + checked, function s(data) {
            console.log(data)
        })
    })
    $("[name=tcp]").on("change", function () {
        //console.log("http")
        let checked = 0;
        let check = $(this) . is(":checked")
        if (check)
            checked = 1
        $.post("app/filter.php?protocol=tcp&l=" + checked, function s(data) {
            console.log(data)
        })
    })
</script>