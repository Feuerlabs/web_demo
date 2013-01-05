<?php
class Logdata_model extends CI_Model {

    public function __construct()
    {
	log_message('debug', 'Logdata_model::construct()');
	$this->load->database();
    }

    public function store($device_id, $logdata)
    {
	log_message('debug', 'Logdata::store(1): device-id('.$device_id.') '.count($logdata).' entries.');
	for($i = 0; $i < count($logdata); ++$i) {
	    $ts = explode('.', $logdata[$i]['ts']);
	    // Add decimals on second, if present.
	    if (count($ts) == 2)
		$dbts = date('Y-m-d H:i:s', $ts[0]).'.'.substr($ts[1],0,3);
	    else
		$dbts = date('Y-m-d H:i:s', $logdata[$i]['ts']);

	    log_message('debug', 'Logdata::store(1): ts('.$dbts.')');
	    if (!$this->db->insert('log_entry',
				   array('device_id' => $device_id,
					 'frame_id' => $logdata[$i]['can-frame-id'],
					 'ts' => $dbts,
					 'can_value' => $logdata[$i]['can-value'])))
		return FALSE;
	}
	return TRUE;
    }


    public function summary($device_id, $can_frame_id)
    {
	log_message('debug', 'LogdataModel::summary(1): device_id('.$device_id.') frame_id('.$can_frame_id.')');
	$this->db->where('device_id', $device_id);
	$this->db->where('frame_id', $can_frame_id);
	$this->db->select_min('can_value', 'min_val');
	$this->db->select_max('can_value', 'max_val');
	$this->db->select_min('ts', 'min_ts');
	$this->db->select_max('ts', 'max_ts');
	$summary = $this->db->get('log_entry')->result_array();
	$summary = $summary[0];

	// No records retrieved
	if (!$summary['min_val']) {
	    $summary['min_val'] = 'n/a';
	    $summary['max_val'] = 'n/a';
	    $summary['min_ts'] = 'n/a';
	    $summary['max_ts'] = 'n/a';
	    $summary['count'] = 0;
	} else {
	    // Get number of records
	    $this->db->select('count(device_id)', FALSE);
	    $this->db->where('device_id', $device_id);
	    $this->db->where('frame_id', $can_frame_id);
	    $count = $this->db->get('log_entry')->result_array();
	    $summary['count'] = $count[0]['count'];
	}

	// Retrieve alarms.
	$this->db->where('device_id', $device_id);
	$this->db->where('frame_id', $can_frame_id);
	$this->db->where('reset_ts', '1900-01-01 00:00:00');
	$alarm = $this->db->get('alarm_entry')->result_array();
	log_message('debug', 'LogdataModel::summary(2): '.print_r($alarm, TRUE).count($alarm));

	if (count($alarm) == 0) {
	    $summary['alarm_id'] = -1;
	    $summary['alarm_can_value'] = 0;
	    $summary['alarm_set_ts'] = 0;
	} else {
	    $alarm = $alarm[0];
	    $summary['alarm_id'] = $alarm['id'];
	    $summary['alarm_can_value'] = $alarm['can_value'];
	    $summary['alarm_set_ts'] = $alarm['set_ts'];
	}

	return $summary;
    }


    public function view($device_id, $can_frame_id = FALSE)
    {
	log_message('debug', 'LogdataModel::view('.$device_id.', '.$can_frame_id.')');
	$this->db->where('device_id', $device_id);
	if ($can_frame_id)
	    $this->db->where('frame_id', $can_frame_id);
	$this->db->order_by('ts', 'asc');
	$res = $this->db->get('log_entry')->result_array();
	return $res;
    }

    public function delete($device_id, $can_frame_id = FALSE)
    {

	$this->db->where('device_id', $device_id);
	if ($can_frame_id)
	    $this->db->where('frame_id', $can_frame_id);

	$this->db->delete('log_entry');
    }

}
