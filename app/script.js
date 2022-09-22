let canvas = document.getElementById('chart');
let chart = new Chart(canvas, {
    type: 'line',
    data: {
        labels: ['0:00'],
        datasets: [{
            label: 'Traffic IN',
            yAxisID: 'A',
            data: [0],
            borderColor: "#009900",
            fill : false,
        }, {
            label: 'Traffic OUT',
            yAxisID: 'B',
            data: [0],
            borderColor: "#0099ff",
        }],
    },
    options: {
        scales: {
            yAxes: [{
                id: 'A',
                type: 'linear',
                position: 'left',
                ticks: {
                    suggestedMin: 0,
                    suggestedMax: 0.2
                },
                gridLines: {
                    color: "rgba(0, 0, 0, 0)",
                }
            }, {
                id: 'B',
                type: 'linear',
                position: 'right',
                ticks: {
                    suggestedMin: 0,
                    suggestedMax: 0.2,
                    display: false
                },
                gridLines: {
                    color: "rgba(0, 0, 0, 0)",
                }
            }]
        },
        elements: {
            line: {
                fill: false
            },
        }
    }
});

function getGraphStats() {
    $.get("app/get_stats.php?graph", function xx(data) {
        //console.log(data)
        let rx = JSON.parse(data)
        //console.log(rx)
        updateGraph(rx.time, rx, 0)
    })
}

getGraphStats();

function updateGraph(label, data, index) {
    // label.forEach((row) => {
    //     chart.data.labels.push(row);
    // })
    chart.data.labels = label
    chart.data.datasets.forEach((dataset,index) => {
        // data[index].forEach((row) => {
        //     dataset.data.push(row)
        // })
        dataset.data = data[index]
       // dataset.data.push(data[index]);
       console.log(dataset)
    });
    //console.log(chart.data.labels)
    chart.update();
}