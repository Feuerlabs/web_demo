<?php echo validation_errors(); ?>

<?php echo form_open('logging/set') ?>

     <br />
     <label for="device_id">DeviceID</label>
     <?php echo $dev_id ?><br/>
     <input type="hidden" name="dev_id" value="<?php echo $dev_id?>"/>

     <label for="can_frame_id">CAN Frame</label>
     <?php
	  if (!$can_frame_id)  {
	      echo '<select name = "can_frame_id">';
	      foreach ($can_frames as $can_frame) {
		  echo '<option value="',$can_frame['frame_id'],'">',
		  $can_frame['label'],' [',$can_frame['frame_id'].']</option>';
	      }
	      echo '</select>';
	  }
	  else
	      echo '<input type="number" readonly name="can_frame_id" value="', $can_frame_id,'"/>';
    ?>
    <br/>

       <label for="sample_interval">Sample Interval (msec)</label>
	<input type="number" name="sample_interval"
	     <?php if (isset($sample_interval)) { echo 'value="', $sample_interval, '"'; } ?>
	/><br />


        <label for="buffer_size">Number of CAN Frames to store</label>
	<input type="number" name="buffer_size"
	     <?php if (isset($buffer_size)) { echo 'value="', $buffer_size, '"'; } ?>
	/><br />

	<input type="submit" name="submit"
	     <?php if ($can_frame_id)
	echo 'value="Update"'; else echo 'value="Create"';?> />
</form>
