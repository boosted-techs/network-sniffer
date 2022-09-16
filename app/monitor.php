<?php
    include_once "p.php";
    $i = $_GET['l'];
    $query = mysqli_query($db, "select * from interfaces where interface = '$i' order by id desc limit 1") or die(mysqli_error($db));
    get_packets($i);
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
    <style>
        @font-face {
            font-family : 'Open Sans';
            src : url("../css/fonts/OpenSans-VariableFont_wdth,wght.ttf");
        }
        body {
            font-family: "Open Sans";
        }
        .bottom {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 100%;
            height: 50px;
            z-index: 99;
        }

        .bottom-right{
            position: fixed;
            bottom: 55px;
            right: 0;
            width: 200px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../logo.png" alt="Network Sniffer" style="width:40px;" class="rounded-pill">
        </a>
        <span class="navbar-text">SNEAKY</span>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="#">HOME</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./alarm.html">Alarm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./workflow.html">Work flow</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="col-md-12 text-center pt-5">
            <img src="../logo.png" alt="Network Sniffer" style="width:50px;" class="rounded-pill">
            <h5 class="text-primary p-4 text-center">
                <?php
                $a = shell_exec("../sneaky/src/main/main -sneaky=0 -rp=$i");
                var_dump($a);
                ?>
                <table class="table table-striped table-borderless table-dark shadow">
                    <tr>
                        <th>interface</th>
                        <th>ipv4</th>
                        <th>ipv6</th>
                        <th>subnet</th>
                        <th>default mask</th>
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
                        <td><?=$row['ipv6']?></td>
                        <td><?=$row['subnet']?></td>
                        <td><?=$row['defaultMask']?></td>
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
        </div>

    </div>
</div>
<div class="bottom p-3 bg-dark text-white text-center">
    <h6 id="machineInfo" class="col-md-12"></h6>
</div>
<div class="bottom-right mt-4 border-0 border-bottom border-danger bg-transparent">

    <div class="card-header bg-transparent">Connections History</div>
    <div class="card-body">
        <table class="table table-striped table-borderless shadow table-danger">
            <thead>
            <tr>
                <th class="text-center">TODAY</th>
                <th class="text-center">ALL</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><h1 id="today" class="text-center rounded border border-danger"></h1></td><td><h1 id="all" class="text-center"></h1></td>
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
            console.log(data)
            let d = JSON.parse(data)
            $("#today").html(d.today)
            $("#all").html(d.connections)
        })
    }


    setInterval(function(){
        getStats()
    }, 4000)
</script>