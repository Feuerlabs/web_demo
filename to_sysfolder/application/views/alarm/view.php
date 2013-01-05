Device ID: <?php echo $dev_id?><br/>

<table border="1">
     <tr>
     <th>CAN Frame</th>
     <th>Trigger Threshold</th>
     <th>Reset Threshold</th>
     </tr>
     <?php foreach ($alarm_list as $alarm_entry): ?>
     <tr>
     <td>
     <?php echo $alarm_entry['frame_id'] ?>
     </td>
     <td>
       <?php echo $alarm_entry['trigger_threshold'] ?>
     </td>
     <td>
     <?php echo $alarm_entry['reset_threshold'] ?>
     </td>
     <td>
     <a href=<?php echo "/index.php/alarm/set/",$dev_id,"/",$alarm_entry['frame_id'] ?>>edit</a>
     </td>
     <td>
     <a href=<?php echo "/index.php/alarm/delete/",$dev_id,"/",$alarm_entry['frame_id'] ?>>delete</a>
     </td>
     </tr>
     <?php endforeach ?>

     </table>
    <br/>
    <a <?php echo 'href="/index.php/alarm/set/'.$dev_id.'"' ?> target="_self">Add Alarm Specification</a>
    <a <?php echo 'href="/index.php/alarm/push/'.$dev_id.'"' ?> target="_self">Push Alarm Specification</a>

<br>
