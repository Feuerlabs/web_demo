<table border="1">
     <tr>
     <th>ID</th>
     <th>Type</th>
     <th>Description</th>
     </tr>
     <?php foreach ($device_list as $device): ?>
     <tr>
     <td>
     <?php echo $device['dev-id'] ?>
     </td>
     <td>
       <?php echo $device['device-type'] ?>
     </td>
     <td>
       <?php echo $device['description'] ?>
     </td>
     <td>
          <a href=<?php echo "/index.php/waypoint/view/",$device['dev-id'] ?>>view waypoints</a>
     </td>
     <td>
          <a href=<?php echo "/index.php/logdata/summary/",$device['dev-id'] ?>>view logs</a>
     </td>
     <td>
          <a href=<?php echo "/index.php/logging/view/",$device['dev-id'] ?>>setup logging</a>
     </td>
     <td>
         <a href=<?php echo "/index.php/alarm/view/",$device['dev-id'] ?>>setup alarm</a>
     </td>
     <td>
          <a href=<?php echo "/index.php/device/set/",$device['dev-id'] ?>>edit</a>
     </td>
     <td>
          <a href=<?php echo "/index.php/device/delete/",$device['dev-id'] ?>>delete</a>
     </td>
     </tr>
     <?php endforeach ?>

     </table>
<div id="device_add">
     <a href="/index.php/device/set">Add Device</a>
</div>
