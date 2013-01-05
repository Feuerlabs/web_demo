Device ID: <?php echo $dev_id?><br/>

<table border="1">
     <tr>
     <th>CAN Frame</th>
     <th>Sample Interval</th>
     <th>Buffer Size</th>
     </tr>
     <?php foreach ($logging_list as $log_entry): ?>
     <tr>
     <td>
     <?php echo $log_entry['frame_id'] ?>
     </td>
     <td>
       <?php echo $log_entry['sample_interval'] ?>
     </td>
     <td>
     <?php echo $log_entry['buffer_size'] ?>
     </td>
     <td>
     <a href=<?php echo "/index.php/logging/set/",$dev_id,"/",$log_entry['frame_id'] ?>>edit</a>
     </td>
     <td>
     <a href=<?php echo "/index.php/logging/delete/",$dev_id,"/",$log_entry['frame_id'] ?>>delete</a>
     </td>
     </tr>
     <?php endforeach ?>

     </table>
    <br/>
    <a <?php echo 'href="/index.php/logging/set/'.$dev_id.'"' ?> target="_self">Add Log Specification</a>
    <a <?php echo 'href="/index.php/logging/push/'.$dev_id.'"' ?> target="_self">Push Log Specification</a>

<br>
