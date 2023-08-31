$(document).ready( function(){
    small_widgets();
    line_chart();
})
function line_chart()
{
    ajax_request(base_url + '/admin/dashboard/line-chart')
        .then( function(res){
            let line_chart_html = '';
            if( res.length > 0 ){
                for(let i=0; i < res.length; i++){
                    line_chart_html = `
                            <div class="${res[i].div_column_class}">
                                <div class="card bg-chart ">
                                    <div class="card-header text-white anime">
                                        ${res[i].title} Chart
                                        <p class="text-white">${res[i].description == null ? '' : res[i].description}</p>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <canvas id="myChart${i}" height="100"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    `;
                    $('#line_chart_container').append(line_chart_html);
                    //init chart
                    var ctx = document.getElementById('myChart' + i).getContext("2d");
                    let draw = Chart.controllers.line.prototype.draw;
                    Chart.controllers.line = Chart.controllers.line.extend({
                        draw: function() {
                            draw.apply(this, arguments);
                            let ctx = this.chart.chart.ctx;
                            let _stroke = ctx.stroke;
                            ctx.stroke = function() {
                                ctx.save();
                                ctx.shadowColor = 'rgba(0,0,0,0.3)';
                                ctx.shadowBlur = 10;
                                ctx.shadowOffsetX = 0;
                                ctx.shadowOffsetY = 4;
                                _stroke.apply(this, arguments)
                                ctx.restore();
                            }
                        }
                    });

                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: res[i].data.label,
                            datasets:
                            [
                                {
                                    label: res[i].title,
                                    borderColor: res[i].color,
                                    pointBorderColor: res[i].color,
                                    pointBackgroundColor: res[i].color,
                                    pointHoverBackgroundColor: res[i].color,
                                    pointHoverBorderColor: res[i].color,
                                    pointBorderWidth: 3,
                                    pointHoverRadius: 6,
                                    pointHoverBorderWidth: 3,
                                    pointRadius: 3,
                                    fill: false,
                                    borderWidth: 3,
                                        data: res[i].data.value
                                }
                            ]
                        },
                        options: {
                            tooltips: {
                               callbacks: {
                                labelColor: function(tooltipItem) {
                                    return {
                                        borderColor: 'rgba(255, 255, 255, 0.5)',
                                        backgroundColor: 'rgba(255, 255, 255, 0.5)'
                                    }
                                }
                               },
                              backgroundColor: '#FFF',
                              titleFontSize: 16,
                              titleFontColor: '#455a64',
                              bodyFontColor: '#909fa7',
                              bodyFontSize: 14,
                              footerAlign: "center",
                              bodyFontFamily: "Montserrat",
                              borderColor:"#ccc",
                              borderWidth:1,
                              xPadding:20,
                              yPadding:20,
                              caretPadding:20,
                              mode: 'index',
                              intersect: false,
                              displayColors: false
                            },
                            legend: {
                                display: false
                            },
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        fontColor: "rgba(255,255,255,0.5)",
                                        beginAtZero: true,
                                        maxTicksLimit: 5,
                                        padding: 10,
                                        fontFamily:"Montserrat"
                                    },
                                    gridLines: {
                                        drawTicks: false,
                                        display: false
                                    }

                                }],
                                xAxes: [{
                                    gridLines: {
                                        zeroLineColor: "transparent"
                                    },
                                    ticks: {
                                        padding: 20,
                                        fontColor: "rgba(255,255,255,0.5)",
                                        fontFamily:"Montserrat"
                                    }
                                }]
                            }
                        }
                    });
                }
            }
        })
}

function small_widgets()
{
    ajax_request(base_url + '/admin/dashboard/small-widget')
        .then( (res) => {
            let small_widget_html = '';
            if( res.length > 0 ){
                for( let i=0; i < res.length; i++ ){
                    small_widget_html += `<div class="${res[i].div_column_class}">
                                            <div class="widget bg-light padding-0">
                                                <div class="row row-table">
                                                    <div class="col-xs-4 text-center padding-15 bg-primary" style="background:${res[i].color} !important;" >
                                                        <em class="${res[i].icon} fa-3x"></em>
                                                    </div>
                                                    <div class="col-xs-8 padding-15 text-right">
                                                        <h2 class="mv-0">${res[i].value}</h2>
                                                        <div class="margin-b-0 text-muted">${res[i].title}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;
                }
            }
            $('#small_widget').append(small_widget_html);
        })
}
