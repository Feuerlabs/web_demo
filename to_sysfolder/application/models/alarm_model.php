<?php
class Alarm_model extends CI_Model {

    public function __construct()
    {
	log_message('debug', 'Alarm_model::construct()');
	$this->load->database();
    }

    public function create($device_id,
			   $can_frame_id,
			   $trigger_threshold,
			   $reset_threshold)
    {
	log_message('debug', 'AlarmModel::store():'.print_r($alarms, TRUE));

	$this->db->insert('alarm_specification',
			  array('device_id' => $device_id,
				'frame_id' => $can_frame_id,
				'trigger_threshold' => $trigger_threshold,
				'reset_threshold' => $reset_threshold));
    }

    public function update($device_id,
			   $can_frame_id,
			   $trigger_threshold,
			   $reset_threshold)
    {
	log_message('debug', 'AlarmModel::update()');

	// Update database
	$this->db->where('device_id', $device_id);
	$this->db->where('frame_id', $can_frame_id);
	$this->db->update('alarm_specification',
			  array('trigger_threshold' => $trigger_threshold,
				'reset_threshold' => $reset_threshold));

	log_message('debug', 'DeviceModel::update(). device_id['.$device_id.']');
    }


    public function view($device_id, $can_frame_id = FALSE)
    {
	log_message('debug', 'AlarmModel::view():'.print_r($device_id, TRUE));
	$this->db->where('device_id', $device_id);
	if ($can_frame_id) {
	    $this->db->where('frame_id', $can_frame_id);
	    $res = $this->db->get('alarm_specification')->result_array();
	    return $res[0];
	}
	return $this->db->get('alarm_specification')->result_array();
    }


    public function delete($device_id, $can_frame_id = FALSE)
    {
	$this->db->where('device_id', $device_id);

	if ($can_frame_id)
	    $this->db->where('frame_id', $can_frame_id);

	$this->db->delete('alarm_specification');
    }


    public function push($device_id) {
	$this->config->load('exosense');
	$this->load->library('jsonrpc');
	$this->load->helper('exosense'); // For the exosense_client
	$alarm_spec_arr = $this->view($device_id);
	foreach($alarm_spec_arr as $alarm_spec) {
	    $client = exosense_client($this,
				      'thinkdemo:update-config-entry-request',
				      array('device-id' => $device_id,
					    'config-entries' =>
					    array(array('name' => 'alarm', 'val' =>
							array(array('can_frame_id' => $alarm_spec['frame_id'],
							      'trigger_threshold' => $alarm_spec['trigger_threshold'],
								    'reset_threshold' => $alarm_spec['reset_threshold']))))));
	    $client->send_request();
	}


    }
}

