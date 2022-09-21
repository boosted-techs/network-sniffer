<?php
    include_once "p.php";
    $i = $_GET['l'];
    $query = mysqli_query($db, "select * from interfaces where interface = '$i' order by id desc limit 1") or die(mysqli_error($db));
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
                <a class="nav-link" href="./alarm.php">Alarm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./login.php?out">LOGOUT</a>
            </li>
            <li class="nav-item bg-white p-3 text-dark"  onclick="window.location='profile.php'">
                <img src="../user.png" alt="Network Sniffer" style="width:20px;" class="rounded-pill">
                <?=isset($_SESSION['username']) ? $_SESSION['username'] : 'USER'?>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="col-md-12 text-center pt-5">
            <img src="../logo.png" alt="Network Sniffer" style="width:50px;" class="rounded-pill">
            <h5 class="text-primary p-4 text-center">
                <table class="table table-striped table-borderless table-dark shadow">
                    <tr>
                        <th>interface</th>
                        <th>Internal IP address</th>
                        <th>Traffic In</th>
                        <th>Traffic Out</th>
                        <th>Description</th>
                        <th>Date added</th>
                    </tr>
                    <tbody>
                    <?php
                        while ($row = mysqli_fetch_array($query)) {
                            ?>
                    <tr>
                        <td><?=$row['interface']?></td>
                        <td><?=$row['ipv4']?></td>
                        <td id="in"></td>
                        <td id="out"></td>
                        <td><?=$row['description']?></td>
                        <td><?=$row['date_added']?></td>
                    </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                </table>
            </h5>
            <h5 class="text-center p-3">Packets</h5>
            <table class="table table-danger shadow">
                <thead>
                <tr>
                    <th></th>
                    <th>Size</th>
                    <th>From</th>
                    <th>To</th>
                    <th></th>
                    <th>Time</th>
                </tr>
                </thead>
                <tbody id="tbody">

                </tbody>
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
        <table class="table table-striped table-borderless shadow table-warning">
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
<script src="../bootstrap/js/bootstrap.min.js"></script>
<script src="../jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
</script>
</body>
</html>
<script>
    $.get("../traffic.php?os", function success(data){
        let d = JSON.parse(data)
        $("#machineInfo").html(d.os_version)
    })

    function getStats() {
        $.get("get_stats.php?stats", function success(data){
            let d = JSON.parse(data)
            $("#tfIn").html(d.in + "Mbs")
            $("#tfOut").html(d.out + "Mbs")
        })
    }

    function monitorDevice() {
        $.get("p.php?d=<?=$_GET['l']?>", function success(data){
            let r = JSON.parse(data)
            //let d = JSON.parse(data)
            $("#in").html(r.traffic_in + " MBs")
            $("#out").html(r.traffic_out + " MBs")

            let h = r.data
            let html;
            h.forEach((row, index) => {
                html += "<tr><td>" + (index + 1) + "</td>";
                html += "<td>" + row.packet_size+ " KBs </td>";
                html += "<td>" + row.src_ip + ":" + row.src_port + "</td>";
                html += "<td>" + row.dst_ip + ":" + row.dst_port + "</td>";
                html += "<td><b>" + row.traffic + "</b></td>";
                html += "<td>" + row.timestamp + "</td></tr>"
            })

            $("#tbody").html(html)
        })
    }

    monitorDevice()
    setInterval(function(){
        getStats()
        //monitorDevice()
    }, 4000)

    setInterval(function(){
        monitorDevice()
    }, 19000)
</script>