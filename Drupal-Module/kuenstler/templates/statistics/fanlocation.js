//dummy data
var fanLocations = {};

//<editor-fold>
fanLocations['Leipzig'] = {
    amount: 24
};
fanLocations['Berlin'] = {
    amount: 50
};
fanLocations['Dresden'] = {
    amount: 11
};
fanLocations['Groothusen'] = {
    amount: 1
};
fanLocations['Nimmerland'] = {
    amount: 123123123
};
fanLocations['Regensburg'] = {
    amount: 64
};
fanLocations['Ulm'] = {
    amount: 22
};
fanLocations['Neumark'] = {
    amount: 460
};
fanLocations['Darmstadt'] = {
    amount: 23
};
fanLocations['Schnackenburg'] = {
    amount: 38
};
fanLocations['Solingen'] = {
    amount: 2
};
fanLocations['Braunschweig'] = {
    amount: 78
};
fanLocations['Halle'] = {
    amount: 15
};
fanLocations['Hannover'] = {
    amount: 27
};
fanLocations['Bremen'] = {
    amount: 7
};
fanLocations['Hamburg'] = {
    amount: 44
};
fanLocations['Löningen'] = {
    amount: 8
};
fanLocations['Kempten'] = {
    amount: 5
};
fanLocations['Lübeck'] = {
    amount: 99
};
fanLocations['Emden'] = {
    amount: 3
};
fanLocations['Magdeburg'] = {
    amount: 33
};
fanLocations['Saarbrücken'] = {
    amount: 22
};
fanLocations['Bad Brückenau'] = {
    amount: 2
};
fanLocations['Brühl'] = {
    amount: 11
};
fanLocations['Geldern'] = {
    amount: 4
};
//</editor-fold>

var centerDeutschland = new google.maps.LatLng(51.165691, 10.451526000000058);
var map;
var styles;
var fanCircle;
var fanCircles = [];
var circles = [];
var markers = [];
var needGeocode = false;
var dbcounter = 0;
var storedCounter = 0;
var remainingCities = [];
//show circles or marker. false -> circles, true -> marker
var currentMarkerState = true;

var mapOptions = {
    center: centerDeutschland,
    zoom: 6,
    mapTypeId: google.maps.MapTypeId.ROADMAP
};
var styledMap;
var circleOptions = {
    clickable: true,
    strokeColor: "#FF0000",
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: "#FF0000",
    fillOpacity: 0.35,
    map: map,
    center: 0,
    radius: 0
};

function initializeMap() {
    console.log("initialize");
    setupCustomMapLayout();
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    //Associate the styled map with the MapTypeId and set it to display.
    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');

    //first try setting all circles.
    //because of ajax, the step for handling QUERY_OVER_LIMIT Errors
    //should be a bit delayed
    for (var city in fanLocations) {
        getCoordinatesAndDrawCircle(city, fanLocations[city].amount);
    }

    //start the function for setting and retrieving the remaining
    //cities in about 2 secs
    setTimeout(function() {
        setRemainingCities();
    }, 3000);
  
    // Create the DIV to hold the control and
    // call the HomeControl() constructor passing
    // in this DIV.
    var homeControlDiv = document.createElement('div');
    var homeControl = new HomeControl(homeControlDiv, map);

    homeControlDiv.index = 1;
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);


}


function setupCustomMapLayout() {
    // Create an array of styles.
    styles = [{
            stylers: [{
                    hue: "#0099ff"
                }, {
                    saturation: 20
                }
            ]
        }, {
            featureType: "road",
            elementType: "geometry",
            stylers: [{
                    lightness: 100
                }, {
                    visibility: "off"
                }
            ]
        }, {
            featureType: "road",
            elementType: "labels",
            stylers: [{
                    visibility: "off"
                }
            ]
        }, {
            featureType: "landscape",
            elementType: "all",
            stylers: [{
                    lightness: -20,
                    hue: "#68421a"
                }, {
                    visibility: "on"
                }

            ]
        }
    ];

    styledMap = new google.maps.StyledMapType(styles,
            {
                name: "Styled Map"
            }
    );

    mapOptions.mapTypeControlOptions = {
        mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
    };
}

/**
 * Function for toggle-button
 * @param controlDiv
 * @param map
 */
function HomeControl(controlDiv, map) {
    // Set CSS styles for the DIV containing the control
    // Setting padding to 5 px will offset the control
    // from the edge of the map
    controlDiv.style.padding = '5px';

    // Set CSS for the control border
    var controlUI = document.createElement('div');
    controlUI.style.backgroundColor = 'white';
    controlUI.style.borderStyle = 'solid';
    controlUI.style.borderWidth = '1px';
    controlUI.style.cursor = 'pointer';
    controlUI.style.textAlign = 'center';
    controlUI.title = 'Click to toggle between circles or markers.';
    controlDiv.appendChild(controlUI);

    // Set CSS for the control interior
    var controlText = document.createElement('div');
    controlText.style.fontFamily = 'Arial,sans-serif';
    controlText.style.fontSize = '12px';
    controlText.style.paddingLeft = '4px';
    controlText.style.paddingRight = '4px';
    controlText.innerHTML = 'Toggle';
    controlUI.appendChild(controlText);

    // Setup the click event listeners
    google.maps.event.addDomListener(controlUI, 'click', function () {
        if (currentMarkerState)
            toggleCirclesMarker("circle", map);
        else
            toggleCirclesMarker("marker", map);
        currentMarkerState = !currentMarkerState;
    });
}

/**
 * Toggle between showing markers or circles
 * @param state
 * @param map
 */
function toggleCirclesMarker(state, map) {
    if (state === "circle") {
        //hide all circles
        for (var circle in circles) {
            circles[circle].setMap(null);
        }
        //show all markers
        for (var marker in markers) {
            markers[marker].setMap(this.map);
        }
    } else {
        //show all circles
        for (var circle in circles) {
            circles[circle].setMap(this.map);
        }
        //hide all markers
        for (var marker in markers) {
            markers[marker].setMap(null);
        }
    }

}

/**
 * Function looks up coordinates for a certain name. Also it calls the function for drawing a circle
 * @param cityName
 * @param amount
 */
function getCoordinatesAndDrawCircle(cityName, amount) {
    
    //try to receive coordinates from stored values in database.
    $.post('sites/all/modules/kuenstler/templates/statistics/db/db_get_city_locations.php', {name: cityName}, function (jsonData) {

        var jsonObject = jsonData;
        if (jsonObject.success === 1) {
            console.log("DB request successfull for " + jsonObject.name);
            dbcounter++;
            needGeocode = false;
        } else {
            needGeocode = true;
        }
    }, "json")
            .done(function (data) {
                var jsonObject = data;
                if (jsonObject.success === 1) {
                    console.log("DBCounter: " + dbcounter);
                    placeFanCircle(new google.maps.LatLng(jsonObject.lng, jsonObject.lat), amount, jsonObject.name);
                    //placeFanCircle(new google.maps.LatLng(51.339695,12.373075), 100, "Sample");
                    console.log("Placed data from server: "+jsonObject.lat+", "+jsonObject.lng+", "+amount);
                } else {
                    googleGeocode(cityName, amount, function (data) {
                        storeCoordinates(data.coords, amount, cityName);
                    });
                }
            });


}

/**
 * Try geocoding via google api. Not processed cities, because of Query_Over_Limit
 * Errors will be stored for later processing.
 * @param {string} cityName
 * @param {int} amount
 * @param {function} callback
 */
function googleGeocode(cityName, amount, callback) {
    //if there are no fitting values in db, ask google geocoder.

    var geocoder = new google.maps.Geocoder();
    geocoder.geocode(
            {
                'address': cityName
            }, function (results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            console.log("Geocoded " + cityName);
            var coords = results[0].geometry.location;
            callback({coords: coords, name: cityName, amount: amount});
        } else {
            if (status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
                console.log("QUERY LIMIT ASSHOLE! on: " + cityName);
                //store a not processed city in array
                remainingCities.push(cityName);
            } else {
                console.log("GeocoderStatus not ok. " + status);
            }
        }
    });

}

/**
 * Store geo data in db
 * @param {google.maps.LatLng} coords
 * @param {int} amount fans for later processing. 
 * @param {string} name
 */
function storeCoordinates(coords, amount, name) {

    var data = {
        name: name,
        lat: coords.lat(),
        lng: coords.lng()
    }

    //send post-request
    $.post('sites/all/modules/kuenstler/templates/statistics/db/db_set_city_location.php', data, function (jsonData) {
        var jsonObject = jsonData;
        if (jsonObject.success === 1) {
            console.log("Stored data in DB for " + name);
            storedCounter++;
        } else {
            console.error("Failed to store data in DB");
            console.error(jsonObject.error);
        }
    }, "json")
            .done(function () {
                placeFanCircle(coords, amount, name);
                console.log("Store COunter: " + storedCounter);
            });
}

/**
 * Takes array remainingCities, which is may be filled with some city names without coords.
 * Calls googleGeocode for further processing. If after this call, still are any cities in 
 * remainingCities, we'll do it again.. and again.. and hopefully terminate it.
 * @returns {undefined}
 */
function setRemainingCities() {
    //copy array. googleGeocode() will manipulate it.
    var remainingCitiesTemp = remainingCities;
    //clear array
    remainingCities = [];
    //if there're any not processed cities
    if(remainingCitiesTemp.length>0) {
        console.log("setRemainingCities for "+remainingCitiesTemp.length+" cities");
        //go through all city names in remainingCities
        for(var i=0; i<remainingCitiesTemp.length; i++) {
            console.log("var name: "+remainingCitiesTemp[i]);
            
            //12query/s is google limit
            if(i<11) {
                //add remaining unprocess cities
                remainingCities.push(remainingCitiesTemp[i]);
            } else {
            
                //call googleGeocode function with name, amount, and the corresponding callback funciton
                googleGeocode(name, fanLocations[remainingCitiesTemp[i]].amount, function(data) {
                    //data coords, name and amount. we'll store computed data in database
                    storeCoordinates(data.coords, data.amount, data.name);
                });
            }
        }
    }
    
    //if there're still any query_over_limit Errors, do the same thing again.
    //in 2 secs
    if(remainingCities.length > 0) {
        remainingCitiesTemp = [];
        setTimeout(function() {
            setRemainingCities();
        }, 2000);
    }
}

/**
 * This function places a circle with with certain coordinates, radius and name on the map.
 * @param coordinates google.maps.LatLng
 * @param amount
 * @param name
 */
function placeFanCircle(coordinates, amount, name) {
    //circle coordinates
    circleOptions.center = coordinates;
    //
    if (amount <= 10) {
        circleOptions.radius = 6000;
    } else {
        if (amount >= 80)
            circleOptions.radius = 79 * 500;
        else
            circleOptions.radius = amount * 500;
    }
    circleOptions.map = map;

    //create a new circle object
    var circle = new google.maps.Circle(circleOptions);
    console.log("placed circle to "+coordinates+", amount: "+amount);
    circles.push(circle);

    //create a corresponding mar    ker
    var markerOptions = {
        map: null,
        position: coordinates,
        title: name + ": " + amount
    };
    var marker = new google.maps.Marker(markerOptions);
    markers.push(marker);

    //info window
    var infoWindow = new google.maps.InfoWindow({
        content: "<div>" + name + ": </br>" + amount + "</div>"
    });
    //add info window to circle
    google.maps.event.addListener(circle, 'click', function (ev) {
        infoWindow.setPosition(circle.getCenter());
        infoWindow.open(map);
    });
    //add info window to marker
    google.maps.event.addListener(marker, 'click', function (ev) {
        infoWindow.setPosition(marker.getPosition());
        infoWindow.open(map);
    });
}

