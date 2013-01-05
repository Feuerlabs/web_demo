
<div id="logdata-summary-table">
<table border="1">
     <tr>
     <th>CAN Frame ID</th>
     <th>CAN Label</th>
     <th>Count</th>
     <th>Start</th>
     <th>Stop</th>
     <th>Min Value</th>
     <th>Max Value</th>
     <th>Alarm</th>
     <th>Alarm Value</th>
     </tr>
     <?php foreach ($summary_list as $summary): ?>
     <tr>
     <td>
     <?php echo $summary['frame_id'] ?>
     </td>
     <td>
       <?php echo $summary['label'] ?>
     </td>
     <td>
       <?php echo $summary['count'] ?>
     </td>
     <td>
     <?php echo $summary['min_ts'] ?>
     </td>
     <td>
     <?php echo $summary['max_ts'] ?>
     </td>
     <td>
     <?php echo $summary['min_val'] ?>
     </td>
     <td>
     <?php echo $summary['max_val'] ?>
     </td>
     <td>
     <?php echo $summary['alarm_set_ts'] ?>
     </td>
     <td>
     <?php echo $summary['alarm_can_value'] ?>
     </td>
     <td>
     <?php if ($summary['alarm_id'] != -1)
     echo '<a href="/index.php/logdata/reset_alarm/',$dev_id,'/',$summary['alarm_id'],'">reset alarm</a>';
     else
         echo 'n/a';
	     ?>
    </td>
     <td>
     <a href=<?php echo "/index.php/logdata/details/",$dev_id,"/",$summary['frame_id'] ?>>details</a>
     </td>
     <td>
     <a href=<?php echo "/index.php/logdata/download/",$dev_id,"/",$summary['frame_id'] ?>>download</a>
     </td>
     <td>
     <a href=<?php echo "/index.php/logdata/delete/",$dev_id,"/",$summary['frame_id'] ?>>delete</a>
     </td>
     </tr>
     <?php endforeach ?>

     </table>

     <?php if (isset($graph_filename)) echo '<div id="logdata-graph"><img src="/generated/'.$graph_filename.'"></div>'?>
     <div id="logdata-clear-all">

     <a href="/index.php/logdata/delete/<?php echo $dev_id?>">Delete all logs for device</a>
     </div>
</div>
