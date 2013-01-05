<?php echo validation_errors(); ?>

<?php echo form_open('can/set') ?>

        <br />
	     <label for="frame_id">Can Frame ID</label>
	     <?php
	     echo '<input type="text" name="frame_id" ';
	     if (isset($frame_id))
		 echo 'value="',$frame_id,'" readonly';

             echo '/><br/>';
        ?>

        <label for="label">Label</label>
	<input type="text" name="label"
	     <?php if (isset($label)) { echo 'value="', $label, '"'; } ?>
	/><br />

	<label for="unit_of_measurement">Unit of Measurement</label>
	<input type="text" name="unit_of_measurement"
	     <?php if (isset($unit_of_measurment)) { echo 'value="', $unit_of_measurment, '"'; } ?>
	/><br />


	<label for="description">Description</label>
        <textarea name="description"><?php if (isset($description)) { echo $description; } ?></textarea><br />


        <label for="min_value">Minimum Value</label>
	<input type="number" name="min_value"
	     <?php if (isset($min_value)) { echo 'value="', $min_value, '"'; } ?>
	/><br />

        <label for="max_value">Maximum Value</label>
	<input type="number" name="max_value"
	     <?php if (isset($max_value)) { echo 'value="', $max_value, '"'; } ?>
	/><br />

	<input type="submit" name="submit"
	     <?php if (isset($frame_id))
	echo 'value="Update"'; else echo 'value="Create"';?> />
</form>
