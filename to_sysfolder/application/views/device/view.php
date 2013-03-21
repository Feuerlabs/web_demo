<table border="1">
     <tr>
     <th>ID</th>
     <th>Type</th>
     <th>Description</th>
     </tr>
     <?php foreach ($device_list as $device): ?>
     <tr>
     <td>
     <?php echo $device['device-id'] ?>
     </td>
     <td>
       <?php echo $device['device-type'] ?>
     </td>
     <td>
       <?php echo $device['description'] ?>
     </td>
     <td>
     <a href=<?php echo '"/index.php/waypoint/view/',$device['device-id'],'"' ?>>view waypoints</a>
     </td>
     <td>
     <a href=<?php echo '"/index.php/logdata/summary/',$device['device-id'],'"' ?>>view logs</a>
     </td>
     <td>
     <a href=<?php echo '"/index.php/logging/view/',$device['device-id'],'"' ?>>setup logging</a>
     </td>
     <td>
     <a href=<?php echo '"/index.php/alarm/view/',$device['device-id'],'"' ?>>setup alarm</a>
     </td>
     <td>
     <a href=<?php echo '"/index.php/device/set/',$device['device-id'],'"' ?>>edit</a>
     </td>
     <td>
     <a href=<?php echo '"/index.php/device/delete/',$device['device-id'],'"' ?>>delete</a>
     </td>
     </tr>
     <?php endforeach ?>

     </table>
<div id="device_add">
     <a href="/index.php/device/set">Add Device</a>
</div>
