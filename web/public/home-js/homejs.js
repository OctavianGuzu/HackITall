function initMap() {
    var historyEntries;
    var directionsService;
    var polylines = [];
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 14,
        center: {lat: 44.444156, lng: 26.0539071},
        //disableDefaultUI: false,
        styles: mapstyles
    });

    $(document).ready(function() {
        $('#searchBtn').on('click', function (event) {
            calcRoute(
                $('#currentLocation').val(),
                $('#destination').val()
            );
            var postData = {
                name : $('#destination').val()
            };
            $.ajax({
                url: "/saveSearch-ajax",
                type: "POST",
                data: postData,
                dataType: "json",
                success: function (response) {
                    console.log(response.result);
                    /*if (response.result) {
                        alert("Search saved");
                    } else {
                        alert("Failed to save search");
                    }*/
                }
            });
        });

        $('.clickable').on('click', function (event) {
            var clickedEntry = $(this).attr('id');
            var index = parseInt(clickedEntry.substr(3, 1));
            $('#historyModal').modal('hide');
            calcRoute($('#currentLocation').val(), historyEntries[index].name);
        });

        $('#goHome').on('click', function (e) {
            $.ajax({
                url: "/getHome-ajax",
                type: "POST",
                data: {},
                dataType: "json",
                success: function (response) {
                    calcRoute($('#currentLocation').val(), response.home);
                }
            });
        });

        $('#goToWork').on('click', function (e) {
            $.ajax({
                url: "/getWork-ajax",
                type: "POST",
                data: {},
                dataType: "json",
                success: function (response) {
                    calcRoute($('#currentLocation').val(), response.work);
                }
            });
        });

        $('#getHistory').on('click', function (e) {
            $.ajax({
                url: "/getHistory-ajax",
                type: "POST",
                data: {},
                dataType: "json",
                success: function (response) {
                    var name = response.username;
                    var saves = response.history;
                    historyEntries = saves;
                    var formattedDate;
                    for(var j=0; j < saves.length; j++) {
                        formattedDate = (saves[j].date_created.date).slice(0, -7);
                        $('#row' + j).html("<td>" + saves[j].name + "</td>" + "<td></td>" + "<td class=\"text-right\"> " + formattedDate + "</td>")
                        $('#tbody').append('<tr class="clickable" id="row' + (j + 1) + '"></tr>');
                    }
                    $('#showUser').html(name + "\'s search history");
                    $('#historyModal').modal('show');
                }
            });
        });

        $('#NavFeedback').on('click', function (event) {
            $('#feedbackModal').modal('show');
        });

        $('#buttonSendFeedback').on('click', function (event) {
            $('#feedbackText').val("");
            $('#feedbackModal').modal('hide');
            $('#thankYouModal').modal('show');
            setTimeout(function(){ $('#thankYouModal').modal('hide'); }, 2000);
        });

        $('#NavCallCenter').on('click', function (event) {
            $('#callModal').modal('show');
        });

        $('#btnCloseFeed').on('click', function (event) {
            $('#feedbackText').val("");
        });

    });

    directionsService = new google.maps.DirectionsService();

    google.maps.Polyline.prototype.getBounds = function(startBounds) {
        if(startBounds) {
            var bounds = startBounds;
        }
        else {
            var bounds = new google.maps.LatLngBounds();
        }

        this.getPath().forEach(function(item, index) {
            bounds.extend(new google.maps.LatLng(item.lat(), item.lng()));
        });
        return bounds;
    };

    function calcRoute(start, end) {
        if (start == "") {
            start = {lat: 44.444156, lng: 26.0539071};
        }
        var mode = $('#sel_mode').val();

        var request = {
            origin: start,
            destination: end,
            provideRouteAlternatives: true,
            unitSystem: google.maps.UnitSystem.METRIC,
            travelMode: google.maps.TravelMode[mode]
        };
        directionsService.route(request, function(response, status) {
            // clear former polylines
            for(var j in  polylines ) {
                polylines[j].setMap(null);
            }
            polylines = [];
            if (status == google.maps.DirectionsStatus.OK) {
                var bounds = new google.maps.LatLngBounds();
                // draw the lines in reverse orde, so the first one is on top (z-index)
                for(var i=response.routes.length - 1; i>=0; i-- ) {
                    // let's make the first suggestion highlighted;
                    if(i==0) {
                        var color = '#0000ff';

                    }
                    else {
                        var color = '#999999';
                    }
                    var line = drawPolyline(response.routes[i].overview_path, color);
                    polylines.push(line);
                    bounds = line.getBounds(bounds);
                    google.maps.event.addListener(line, 'click', function() {
                        // detect which route was clicked on
                        var index = polylines.indexOf(this);
                        highlightRoute(index);
                        showRoutInfo(response, index, response.routes.length);

                    });
                }
                map.fitBounds(bounds);
            }
        });
    }

    function showRoutInfo(response, index, len) {
        index = len - index - 1;
        var leg = response.routes[index].legs[0];
        var steps = leg.steps;
        var firstStep = steps[0];
        var key = '2e3ac1f2ece244628e1ed135851f4595';
        var total_pollution = 0;
        var count = 0;
        var distance = response.routes[index].legs[0].distance.text;
        var time = response.routes[index].legs[0].duration.text;
        var first = true;
        var air_qual = "";
        var first_weather = true;
        var general_weather = "";
        var minTemp, maxTemp;
        var recomandations = "";

        //https://api.breezometer.com/baqi/?lat=40.7324296&lon=-73.9977264&key=YOUR_API_KEY
        for(var i = 0; i < steps.length; i += 5) {
            $.get(
                "https://api.breezometer.com/baqi/",
                {lat : steps[i].start_location.lat(), lon : steps[i].start_location.lng(), 'key': key},
                function(data) {
                    if (data.breezometer_aqi) {
                        total_pollution += parseInt(data.breezometer_aqi);
                        count++;
                    }

                    if (i + 5 >= steps.length && first) {
                        air_qual = data.breezometer_description;
                        first = false;
                        recomandations = data.random_recommendations.health;
                    }
                }
            );

            $.get("https://api.openweathermap.org/data/2.5/weather",
                {lat : parseInt(steps[i].start_location.lat()), lon : parseInt(steps[i].start_location.lng()),
                    appid: '78f28e6aa225bc4c0edb9bfcb5a6d4f6'},
                function(data) {
                    if (first_weather) {
                        first_weather = false;
                        minTemp = parseInt(data.main.temp);
                        maxTemp = parseInt(data.main.temp);
                    } else {
                        if (parseInt(data.main.temp) > maxTemp)
                            maxTemp = parseInt(data.main.temp);
                        if (parseInt(data.main.temp) < minTemp)
                            minTemp = parseInt(data.main.temp);
                    }

                    if (i + 5 >= steps.length) {
                        general_weather = data.weather[0].description;
                    }

                });
        }
        setTimeout(function() {

            var avg_pollution = total_pollution / count;
           // console.log(distance);
            $('#showDistance').text(distance);

           // console.log(time);
            $('#showETA').text(time);

           // console.log(total_pollution / count);
            $('#showAveragePollution').text(parseInt(total_pollution / count) + "/100");

            //console.log(air_qual);
            $('#showAirQuality').text(air_qual);

           // console.log(general_weather);
            $('#showGeneralWeather').text(general_weather);

           // console.log(parseInt(minTemp - 272.15));
            $('#showMinTemp').text(parseInt(minTemp - 272.15));

           // console.log(parseInt(maxTemp - 272.15));
            $('#showMaxTemp').text(parseInt(maxTemp - 272.15));

          //  console.log(recomandations);
            $('#showAdvice').text("Advice: " + recomandations +".");


            $('#routeModal').modal('show');

        }, 2 * 1000);
    }

    function highlightRoute(index) {
        for(var j in  polylines ) {
            if(j==index) {
                var color = '#0000ff';
            }
            else {
                var color = '#999999';
            }
            polylines[j].setOptions({strokeColor: color});
        }
    }

    function drawPolyline(path, color) {
        var line = new google.maps.Polyline({
            path: path,
            strokeColor: color,
            strokeOpacity: 0.7,
            strokeWeight: 3
        });
        line.setMap(map);
        return line;
    }

    google.maps.event.addDomListener(window, "resize", function() {
        var center = map.getCenter();
        google.maps.event.trigger(map, "resize");
        map.setCenter(center);
    });


//////////////////////////Implementare ANDI///////////////////////////

    var options = {};
    var home = new HomePage(options);
    home.init();
}


var HomePage = function (options) {
    "use strict";

    this.onLogout = function() {
        $('#LogoutBtn').on('click', function (e) {
            window.location.replace('/logout');
        });
    };

    this.onNewAcc = function() {
        $('#registerAcc').on('click', function (e) {
            console.log("new acc");
            window.location.replace('/register');
        });
    };

    this.init = function () {
        var puppetObj = this;
        puppetObj.onLogout();
        puppetObj.onNewAcc();
    };
    return this;
};

$('#guzu').on('click', function () {
    var username = $('#username').val();
    var pass = $('#password').val();
    var pass2 = $('#re-password').val();
    var home = $('#home').val();
    var work = $('#work').val();

    if  (pass != pass2) {
        alert("Passwords don't match");
        return;
    }

    var postData = {
        username : username,
        password : pass,
        home : home,
        work : work
    };
    $.ajax({
        url: "/register-ajax",
        type: "POST",
        data: postData,
        dataType: "json",
        success: function (response) {

            if (response.result) {
                alert("Successful Registration, redirecting to login page");
                setTimeout(function(){
                    window.location.replace('/login');
                }, 1000);
            } else {
                alert("Registration failed");
            }
        }
    });
});