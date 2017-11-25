function initMap() {
    var directionsService;
    var polylines = [];
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 14,
        center: {lat: 44.444156, lng: 26.0539071},
        //disableDefaultUI: false,
        styles: mapstyles
    });
    

    $('#searchBtn').on('click', function (event) {
        calcRoute(
            $('#currentLocation').val(),
            $('#destination').val()
        );
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
        var request = {
            origin: start,
            destination: end,
            provideRouteAlternatives: true,
            unitSystem: google.maps.UnitSystem.METRIC,
            travelMode: google.maps.TravelMode['DRIVING']
        };
        directionsService.route(request, function(response, status) {
            // clear former polylines
            console.log(response);
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
                    });
                }
                map.fitBounds(bounds);
            }
        });
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
}

