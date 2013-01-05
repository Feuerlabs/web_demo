<html>
<head>
  <title>Google Maps JavaScript API v3 Example: Map Simple</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta charset="utf-8">
  <style>
    html, body, #map_canvas {
    margin: 0;
    padding: 0;
    height: 100%;
    }
  </style>
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
  <script>
    var map;
    function initialize() {
      var bounds = new google.maps.LatLngBounds();
      var mapOptions = {
        zoom: 8,
        center: new google.maps.LatLng(<?php echo $ctr_lat, $ctr_lon ?>),
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

      <?php foreach ($waypoints as $waypoing) echo 'bounds.extend(myLat

    }

   google.maps.event.addDomListener(window, 'load', initialize);
  </script>
</head>
<body>
<h1>Exosense</h1>
<h2><?php echo $title?></h2>
<a href="/index.php/device/view" target="_self">Devices</a>|
<a href="/index.php/can/view" target="_self">CAN Frames</a>
<p>
