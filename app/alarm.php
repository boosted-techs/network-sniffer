<?php
include_once "p.php";
function get_limits() {
    global $db;
    $query = mysqli_query($db, "select * from alarm_limits order by id desc limit 1") or die(mysqli_error($db));
    return mysqli_fetch_array($query);
}
$limits = get_limits();
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
        <a class="navbar-brand" href="../">
            <img src="../logo.png" alt="Network Sniffer" style="width:40px;" class="rounded-pill">
        </a>
        <span class="navbar-text">SNEAKY</span>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="../">HOME</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./alarm.php">Alarm</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <button type="button" class="btn btn-primary mt-3 rounded-0" data-bs-toggle="modal" data-bs-target="#myModal">
        Add Traffic Limits
    </button>
    <div class="row">
        <div class="col-md-12 text-center p-4">
            <img src="../logo.png" alt="Network Sniffer" style="width:50px;" class="rounded-pill">
        </div>
        <?php
        if (isset($_GET['s'])) {
            ?>
            <div class="col-md-12 alert alert-success text-center mt-3 text-center rounded-0">
                Limits have been successfully set.
            </div>
            <?php
        }
        ?>
        <div class="col-md-12 shadow p-3 bg-dark">
            <h6 class="text-center p-2 text-white">
                Set Alarm when Live traffic is <b class="text-danger"><?=$limits['live_tf_limit']?>GB</b> or Cumulative when Live traffic is <b class="text-danger"><?=$limits['live_cum_limit']?>GB</b>
            </h6>
        </div>
        <div class="col-md-5 text-center pt-5">
            <div class="card-body">
                <h4 class="text-center">LIVE Traffic</h4>
                <table class="table table-striped table-borderless shadow table-primary">
                    <thead>
                    <tr>
                        <th class="text-center">IN <span class='text-success'>&#8595</span></th>
                        <th class="text-center">OUT <span class='text-primary'>&#8593</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><h5 id="tfInLive" class="text-center rounded"></h5></td><td><h5 id="tfOutLive" class="text-center"></h5></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-7 text-center pt-5">
            <div class="card-body">
                <h4 class="text-center">Cumulative Traffic</h4>
                <table class="table table-striped table-borderless shadow table-danger">
                    <thead>
                    <tr>
                        <th class="text-center">IN <span class='text-success'>&#8595</span></th>
                        <th class="text-center">OUT <span class='text-primary'>&#8593</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><h5 id="tfIn" class="text-center rounded"></h5></td><td><h5 id="tfOut" class="text-center"></h5></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12 p-3">
            <div class="card-body">
                <h4 class="text-center">Alarms</h4>
                <table class="table table-striped table-borderless shadow table-secondary">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>Live Traffic In</th>
                        <th>Live Traffic out</th>
                        <th>Cumulative Traffic In</th>
                        <th>Cumulative Traffic Out</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<div class="bottom p-3 bg-dark text-white text-center">
    <h6 id="machineInfo" class="col-md-12"></h6>
</div>
<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Set Traffic Limits</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="alert alert-info">
                    At such set limits, the system shall warn and add log at when the alarm has been recorded.
                    NB: Traffic limiits are in Gigabytes
                </div>
               <form action="set-limits.php" method="post">
                   <label>Maximum Network limit on live traffic in Gbs</label>
                   <input type="text" class="form-control rounded-0 border-0 border-bottom border-danger" placeholder="eg 2" name="liveTf"/>
                   <label>Maximum Network Limit on Cumulative traffic in Gbs</label>
                   <input type="text" class="form-control rounded-0 border-0 border-bottom border-danger" placeholder="eg 2" name="liveCf"/>
                   <button class="btn btn-primary rounded-0 mt-3 form-control">Add</button>
               </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
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
            $("#tfIn").html(d.in + "Mbs")
            $("#tfOut").html(d.out + "Mbs")
            $("#tfInLive").html(d.live_in + "Mbs")
            $("#tfOutLive").html(d.live_out + "Mbs")
        })
    }

    getStats()

    setInterval(function(){
        getStats()
        //monitorDevice()
    }, 4000)
</script>