function initMap() {
    var directionsDisplay1 = new google.maps.DirectionsRenderer();
    var directionsDisplay2 = new google.maps.DirectionsRenderer();
    var directionsDisplay3 = new google.maps.DirectionsRenderer();
    var directionsService = new google.maps.DirectionsService();
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 7,
        center: {lat: 41.85, lng: -87.65},
        //disableDefaultUI: false,
        styles: mapstyles
    });
    directionsDisplay1.set(map);

    var onChangeHandler = function() {
        calculateAndDisplayRoute(directionsService);
    };

    $('#searchBtn').on('click', function (event) {
        onChangeHandler();
    });


    function calculateAndDisplayRoute(directionsService) {
        directionsService.route({
            origin: "chicago",
            destination: "seattle",
            provideRouteAlternatives: true,
            travelMode: 'DRIVING'
        }, function(response, status) {
            if (status === 'OK') {
                console.log(response);
                for (var i = 0; i < 3; i++) {
                    if (i < response.routes.length) {
                        if (i == 0) {
                            directionsDisplay1.setDirections(response);
                            directionsDisplay1.setMap(map);
                        } else if (i == 1) {
                            response.routes[0] = response.routes[1];
                            directionsDisplay2.setDirections(response);
                            directionsDisplay2.setMap(map);
                        } else if (i == 2) {
                            response.routes[0] = response.routes[2];
                            directionsDisplay2.setDirections(response);
                            directionsDisplay2.setMap(map);
                        }
                    }
                }

            } else {
                window.alert('Directions request failed due to ' + status);
            }
        });
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