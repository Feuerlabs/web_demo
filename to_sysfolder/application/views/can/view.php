<table border="1">
     <tr>
     <th>CAN Frame ID</th>
     <th>Label</th>
     <th>Measurement Unit</th>
     <th>Description</th>
     <th>Min Value</th>
     <th>Max Value</th>
     </tr>
     <?php foreach ($can_list as $can): ?>
     <tr>
     <td>
     <?php echo $can['frame_id'] ?>
     </td>
     <td>
     <?php echo $can['label'] ?>
     </td>
     <td>
     <?php echo $can['unit_of_measurement'] ?>
     </td>
     <td>
     <?php echo $can['description'] ?>
     </td>
     <td>
     <?php echo $can['min_value'] ?>
     </td>
     <td>
     <?php echo $can['max_value'] ?>
     </td>
     <td>
     <a <?php echo 'href="/index.php/can/set/',$can['frame_id'].'"' ?>>edit</a>
    </td>
     <td>
     <a <?php echo 'href="/index.php/can/delete/',$can['frame_id'].'"' ?>>delete</a>
     </td>
     </tr>
     <?php endforeach ?>
</table>
<a href="/index.php/can/set">Add CAN Frame</a>



