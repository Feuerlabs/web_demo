<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
     <title>Exosense</title>
     <link rel="stylesheet" type="text/css" href="http://www.feuermade.com/exosense/demo.css"/>
     <?php echo $map['js']; ?></head>

<body>

<div id="wrapper">
<div id="header">
<a href="/index.php"><img src="http://feuermade.com/exosense/Feuerlabs_inc.jpg" alt="Feuerlabs, Inc"/></a>
</div>
<?php echo $map['html']; ?>
<br/>
<div id="container">
<a href=<?php echo '"/index.php/waypoint/delete/',$dev_id.'"'?>>Delete logs</a>
     <a href=<?php echo '"/index.php/waypoint/download/',$dev_id,'"'?>>Download as CSV file</a>
</div>
<div id="footer">
&copy; 2012 Feuerlabs, Inc
</div>
</div>
</body>
</html>
