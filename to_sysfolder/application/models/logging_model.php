<?php
class Logging_model extends CI_Model {

    public function __construct()
    {
	log_message('debug', 'Logging_model::construct()');
	$this->load->library('jsonrpc');
	$this->config->load('exosense');
	$this->load->helper('exosense'); // For the exosense_client
	$this->load->database();
    }

    public function create($device_id,
			   $can_frame_id,
			   $sample_interval,
			   $buffer_size)
    {
	log_message('debug', 'LoggingModel::create():'.$device_id.' '.$can_frame_id);
	$this->db->insert('log_specification',
			  array('device_id' => $device_id,
				'frame_id' => $can_frame_id,
				'sample_interval' => $sample_interval,
				'buffer_size' => $buffer_size));
    }


    public function view($device_id, $can_frame_id = FALSE)
    {
	log_message('debug', 'LoggingModel::view('.$device_id.', '.$can_frame_id.')');
	$this->db->where('device_id', $device_id);
	if ($can_frame_id) {
	    $this->db->where('frame_id', $can_frame_id);
	    $res = $this->db->get('log_specification')->result_array();
	    return $res[0];
	}

	return $this->db->get('log_specification')->result_array();
   }


    public function delete($device_id, $can_frame_id = FALSE, $inhibit_exosene_call = FALSE)
    {
	$this->db->where('device_id', $device_id);
	if ($can_frame_id)
	    $this->db->where('frame_id', $can_frame_id);

	$this->db->delete('log_specification');
    }

    public function push($device_id) {
	$log_spec_arr = $this->view($device_id);
	foreach($log_spec_arr as $log_spec) {
	    $client = exosense_client($this,
				      'demo:update-config-entry-request',
				      array('device-id' => $device_id,
					    'config-entries' =>
					    array(array('name' => 'logging',
							'val' => array(array('can_frame_id' => $log_spec['frame_id'],
								       'sample_interval' => $log_spec['sample_interval'],
									     'buffer_size' => $log_spec['buffer_size']))))));
	    $client->send_request();
	}
    }
}
