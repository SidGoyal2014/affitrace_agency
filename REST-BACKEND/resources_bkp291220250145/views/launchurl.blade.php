<script>

    /*
    function get_coords(){

        // Use Geolocation API to get user's location
        if(navigator.geolocation){

            navigator.geolocation.getCurrentPosition(function(position){

                // Send the latitude and longitude to the server (e.g., using AJAX)
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;


const protocol = window.location.protocol; // "http:" or "https:"
const hostname = window.location.hostname; // "example.com"
const pathname = window.location.pathname; // "/path/to/page"



                const queryString = window.location.search;

                // Remove the leading "?" character
                const queryStringWithoutQuestionMark = queryString.slice(1);

                location.href='/offer/gourl/1/18?'+queryStringWithoutQuestionMark+'&lat=' + latitude + '&lng=' + longitude;



            }, function(error) {
                console.error('Error getting geolocation:', error.message);
            });

        } else {
            console.error('Geolocation is not supported by this browser.');

        }

    };


    get_coords();
    */


</script>

<img src="{{ asset('assets/img/processing.gif') }}" />



<!DOCTYPE html>
<html lang="en">
<head>
    <base target="_top">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Quick Start - Leaflet</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        .leaflet-container {
            height: 400px;
            width: 600px;
            max-width: 100%;
            max-height: 100%;
        }
    </style>

    
</head>
<body>



<div id="map" style="width: 600px; height: 400px;"></div>
<script>

    const map = L.map('map').setView([51.505, -0.09], 13);

    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

</script>



</body>
</html>
