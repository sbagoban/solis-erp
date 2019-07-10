function index_menuhandler(menuid)
{
    if (menuid == "newsroom")
    {
        window.location = "index.php";
    } else if (menuid == "areas")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "usergroups")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "users")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "accessgranting")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "countries")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "areas")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "coasts")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "grphotels")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "hoteltype")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "mealplans")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "optservices")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "airports")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "childrenages")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "bankdetails")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "exgrates")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "companies")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "to")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "dateperiods")
    {
        window.location = "index.php?m=" + menuid;
    } else if (menuid == "categovehicles")
    {
        window.location = "index.php?m=" + menuid;
    }
    else if (menuid == "ratings")
    {
        window.location = "index.php?m=" + menuid;
    }
    else if (menuid == "inventory")
    {
        window.location = "index.php?m=" + menuid;
    }
    else if (menuid == "ratescalc")
    {
        window.location = "index.php?m=" + menuid;
    }
}


function getWeatherForecast(weather_apiid, weather_lon, weather_lat)
{
    $.ajax({
        url: "http://api.openweathermap.org/data/2.5/forecast?lat=" + weather_lat + "&lon=" + weather_lon + "&appid=" + weather_apiid + "&units=metric&cnt=10",
        data: "",
        type: 'POST',
        success: function (resp) {
            loadWeatherChart(resp);
            console.log(resp);
        },
        error: function (e) {
            alert('Error: ' + e);
        }
    });
}



function loadWeatherChart(resp)
{
    Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#292b2c';


    var labels = [];
    var data = [];

    for (var i = 0; i < resp.list.length; i++)
    {
        var item = resp.list[i];
        var dt = item.dt;
        var temp = item.main.temp;
        var weather = item.weather[0].main;
        var weather_description = item.weather[0].description;
        var wind = item.wind.speed;

        var date = new Date(parseInt(dt, 10) * 1000);

        var ampm = " am";
        if (date.getHours() > 12)
        {
            var ampm = " pm";
        }

        labels.push(global_weekday_abrv[date.getDay()] + " " + date.getHours() + ampm);
        data.push(temp);
    }

    var ctx = document.getElementById("myWeather");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                    label: "Temp: ",
                    lineTension: 0.3,
                    backgroundColor: "rgba(2,117,216,0.2)",
                    borderColor: "rgba(2,117,216,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(2,117,216,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(2,117,216,1)",
                    pointHitRadius: 20,
                    pointBorderWidth: 2,
                    data: data
                }]
        },
        options: {
            scales: {
                xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: true
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                yAxes: [{
                        ticks: {
                            min: 10,
                            max: 40,
                            maxTicksLimit: 5
                        },
                        gridLines: {
                            color: "rgba(0, 0, 0, .125)",
                        }
                    }]
            },
            legend: {
                display: false
            }
        }
    });

}


