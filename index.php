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
        </ul>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="col-md-12 text-center pt-5">
            <img src="logo.png" alt="Network Sniffer" style="width:50px;" class="rounded-pill">
            <h5 class="text-primary p-4 text-center">
                Welcome to Sneaky Packet Capture Tool
            </h5>
        </div>
        <div class="col-md-12 table-responsive">
            <table class="table table-striped w-100 shadow table-secondary">
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
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><small id="tfIn" class="text-center rounded"></small></td><td><small id="tfOut" class="text-center"></small></td>
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

    function getStats() {
        $.get("app/get_stats.php?stats", function success(data){
            let d = JSON.parse(data)
            $("#tfIn").html(d.live_in + "Mbs")
            $("#tfOut").html(d.live_out + "Mbs")
        })
    }

    $.get("app/get_interfaces.php", function success(data){
        let d = JSON.parse(data)
        $("#table").html(d)
    })

    setInterval(function(){
        getStats()
    }, 4000)
</script>