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
                <a class="nav-link active" href="../">HOME</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="alarm.php">Alarm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="login.php?out">LOGOUT</a>
            </li>
            <li class="nav-item bg-white p-3 text-dark"  onclick="window.location='profile.php'">
                <img src="../user.png" alt="Network Sniffer" style="width:20px;" class="rounded-pill">
                <?=isset($_SESSION['username']) ? $_SESSION['username'] : 'USER'?>
            </li>
        </ul>
    </div>
</nav>
<div class="container" style="margin-bottom: 100px">
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
                Set Alarm when Live traffic is <b class="text-danger"><?=$limits['live_tf_limit']?>Mbs</b> or Cumulative when Live traffic is <b class="text-danger"><?=$limits['live_cum_limit']?>Mbs</b>
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
                <table class="table table-striped table-borderless shadow table-success">
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
                <div class="col-md-12 p-3">
                    <canvas id="chart" class="shadow p-3" style="width:100%" height="200"></canvas>
                </div>
                <div class="alert alert-info">
                    When the threshold traffic for live and cumulative traffic is reached, alarms are set and notifications shown here.
                    It should be noted, when traffic threshold is set to 0, no alarms shall be set and no notifications shall be logged.
                </div>
                <h4 class="text-center">Alarms</h4>
                <table class="table table-striped table-borderless shadow table-danger">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>Live Traffic</th>
                        <th>Cumulative Traffic</th>
                        <th>Time</th>
                        <th>Message</th>
                    </tr>
                    </thead>
                    <tbody id="alarmBody">

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
                    NB: Traffic limits are in MegaBytes
                </div>
               <form action="set-limits.php" method="post">
                   <label>Maximum Network limit on live traffic in Mbs</label>
                   <input type="text" class="form-control rounded-0 border-0 border-bottom border-danger" placeholder="eg 2" name="liveTf"/>
                   <label>Maximum Network Limit on Cumulative traffic in Mbs</label>
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
    let xValues = [""];
    let yValues = [];
    let barColors = ["#dc3545"];

    let chart = new Chart("chart", {
        type: "bar",
        data: {
            labels: xValues,
            datasets: [{
                label : "Cumulative Traffic",
                backgroundColor: ['#dc3545'],
                data: yValues,
                id : "A"
            }, {
                label: "Live traffic",
                backgroundColor: ['#0066ff'],
                data: yValues,
                id : "B"
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    id: 'A',
                    type: 'linear',
                    position: 'left',
                    ticks: {
                        suggestedMin: 0,
                        suggestedMax: 20
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, 0)"
                    }
                }, {
                    id: 'B',
                    type: 'linear',
                    position: 'right',
                    ticks: {
                        suggestedMin: 0,
                        suggestedMax: 20,
                        display: false
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, 0)"
                    }
                }]
            }
        }
    });

    $.get("../traffic.php?os", function success(data){
        let d = JSON.parse(data)
        $("#machineInfo").html(d.os_version)
    })

    function getStats() {
        $.get("get_stats.php?stats", function success(data){
            //console.log(data)
            let d = JSON.parse(data)
            $("#tfIn").html(d.in + "Mbs")
            $("#tfOut").html(d.out + "Mbs")
            $("#tfInLive").html(d.live_in + "Mbs")
            $("#tfOutLive").html(d.live_out + "Mbs")
        })
    }
    getAlarms();
    function getAlarms() {
        $.get("get_stats.php?alarm", function success(data) {
            console.log(data)
            let r = JSON.parse(data)
            $("#alarmBody").html(r.content)
            updateChart(r)
        })
    }

    function updateChart(data) {
        chart.data.labels = data[2]
        chart.data.datasets.forEach((dataset,index) => {
            // data[index].forEach((row) => {
            //     dataset.data.push(row)
            // })
            dataset.data = data[index]
            console.log(dataset)
        });
        chart.update()
    }

    getStats()

    setInterval(function(){
        getStats()
        //monitorDevice()
    }, 4000)
</script>