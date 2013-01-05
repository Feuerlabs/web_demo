<h2>Device ID: <?php echo $dev_id?></h2><br/>
<head><?php echo $map['js']; ?></head>
<body><?php echo $map['html']; ?>
<br/>
<a href=<?php echo "/index.php/waypoint/delete/",$dev_id?>>Delete logs</a>
<a href=<?php echo "/index.php/waypoint/download/",$dev_id?>>Download as CSV file</a>
</body>

</html>
