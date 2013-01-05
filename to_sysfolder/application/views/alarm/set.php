<?php echo validation_errors(); ?>

<?php echo form_open('alarm/set') ?>

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

       <label for="trigger_threshold">Trigger Treshold</label>
	<input type="number" name="trigger_threshold"
	     <?php if (isset($trigger_threshold)) { echo 'value="', $trigger_threshold, '"'; } ?>
	/><br />


        <label for="reset_threshold">Reset Threshold</label>
	<input type="number" name="reset_threshold"
	     <?php if (isset($reset_threshold)) { echo 'value="', $reset_threshold, '"'; } ?>
	/><br />

	<input type="submit" name="submit"
	     <?php if ($can_frame_id)
	echo 'value="Update"'; else echo 'value="Create"';?> />
</form>
