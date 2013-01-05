<?php echo validation_errors(); ?>

<?php echo form_open('device/set') ?>

        <br />
	     <label for="device_id">DeviceID</label>
	     <input type="text" name="device_id" <?php if (isset($devid)) echo 'value="',$devid,'" readonly' ?> /><br/>


	<label for="device_type">Device Type</label>
	     <select name = "device_type" <?php if (isset($devid)) echo ' readonly' ?> >

        <?php foreach ($device_types as $device_type_iter): ?>
        <option value="<?php echo $device_type_iter['name']?>"<?php if (isset($device_type) && $device_type == $device_type_iter['name']) echo ' selected'?>><?php echo $device_type_iter['name']?></option>
         <?php endforeach ?>
        </select>
        <br/>

	<label for="description">Description</label>
        <textarea name="description"><?php if (isset($description)) { echo $description; } ?></textarea><br />


        <label for="server_key">Server key</label>
	<input type="number" name="server_key"
	     <?php if (isset($server_key)) { echo 'value="', $server_key, '"'; } ?>
	/><br />

        <label for="device_key">Device key</label>
	<input type="number" name="device_key"
	     <?php if (isset($device_key)) { echo 'value="', $device_key, '"'; } ?>
	/><br />


        <label for="waypoint_interval">Waypoint interval</label>
	<input type="number" name="waypoint_interval"
	     <?php if (isset($waypoint_interval)) { echo 'value="', $waypoint_interval, '"'; } ?>
	/><br />


	<label for="can_bus_speed">CAN bus speed</label>
        <select name = "can_bus_speed">
	     <option value="250"
	     <?php if (isset($can_bus_speed) && $can_bus_speed == "250") { echo 'selected'; } ?>
	     >250 kbit</option>
          <option value="500"
	     <?php if (isset($can_bus_speed) && $can_bus_speed == "500") { echo 'selected'; } ?>
	     >500 kbit</option>
        </select>
        <br />

	<label for="can_frame_id_type">CAN frame ID size</label>
        <select name = "can_frame_id_type">
          <option value="11"
	     <?php if (isset($can_frame_id_type) && $can_frame_id_type == "11") { echo 'selected'; } ?>
	     >11 bit</option>
          <option value="29"
	     <?php if (isset($can_frame_id_type) && $can_frame_id_type == "29") { echo 'selected'; } ?>
	     >29 bit</option>
        </select>
        <br />

	<label for="retry_count">Retry count</label>
	<input type="number" name="retry_count"
	     <?php if (isset($retry_count)) { echo 'value="', $retry_count, '"'; } ?>
	/><br />

	<label for="retry_interval">Retry interval</label>
	<input type="number" name="retry_interval"
	     <?php if (isset($retry_interval)) { echo 'value="', $retry_interval, '"'; } ?>
	/><br />

	<input type="submit" name="submit"
	     <?php if (isset($devid))
	echo 'value="Update"'; else echo 'value="Create"';?> />
</form>
