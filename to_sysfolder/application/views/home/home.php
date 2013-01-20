<div class="tabs">
   <div class="tab">
       <input type="radio" id="devices" name="top-tab" checked="checked"/>
       <label for="devices">Devices</label>
       <div class="content">
       <?php echo $device_list_view ?>
       </div>

   <div class="tab">
       <input type="radio" id="can_frames" name="top-tab"/>
       <label for="can_frames">CAN Frames</label>
       <div class="content">
       <?php echo $can_list_view ?>
       </div>
   </div>
</div>