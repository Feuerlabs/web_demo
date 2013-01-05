<?php
class Alarmdata_model extends CI_Model {

    public function __construct()
    {
	log_message('debug', 'Alarm_model::construct()');
	$this->load->library('jsonrpc');
	$this->load->database();
    }

    public function store($device_id, $alarmdata)
    {
	log_message('debug', 'Alarmdata::store(1): device-id('.$device_id.') '.count($alarmdata).' entries.');
	for($i = 0; $i < count($alarmdata); ++$i) {
	    $ts = explode('.', $alarmdata[$i]['ts']);
	    // Add decimals on second, if present.
	    if (count($ts) == 2)
		$dbts = date('Y-m-d H:i:s', $ts[0]).'.'.substr($ts[1],0,3);
	    else
		$dbts = date('Y-m-d H:i:s', $alarmdata[$i]['ts']);

	    log_message('debug', 'Alarmdata::store(1): ts('.$dbts.')');
	    if (!$this->db->insert('alarm_entry',
				   array('device_id' => $device_id,
					 'frame_id' => $alarmdata[$i]['can-frame-id'],
					 'set_ts' => $dbts,
					 'can_value' => $alarmdata[$i]['can-value'])))
		return FALSE;
	}
	return TRUE;
    }


    public function view($device_id)
    {
	log_message('debug', 'AlarmModel::view():'.print_r($device_id, TRUE));
	$this->db->where('device_id', $device_id);
	$this->db->order_by('set_ts', 'asc');
	return $this->db->get('alarm_entry')->result_array();
    }

    public function delete($device_id, $alarm_id = FALSE)
    {
	if ($alarm_id == FALSE)
	    $this->db->where('device_id', $device_id);
	else
	    $this->db->where('id', $alarm_id);

	$this->db->delete('alarm_entry');
    }

    public function reset($device_id, $alarm_id)
    {
	$this->db->where('id', $alarm_id);

	$ts = time();
	$this->db->update('alarm_entry',
			  array('reset_ts' => date('Y-m-d H:i:s', time())));
    }
}
