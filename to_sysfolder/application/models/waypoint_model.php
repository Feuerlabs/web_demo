<?php
class Waypoint_model extends CI_Model {

    public function __construct()
    {
	log_message('debug', 'Waypoint_model::construct()');
	$this->load->library('jsonrpc');
	$this->load->database();
    }

    public function store($device_id, $waypoints)
    {
	log_message('debug', 'WaypointModel::store(1): device-id('.$device_id.')');
	for($i = 0; $i < count($waypoints); ++$i) {
	    $ts = $waypoints[$i]['ts'];
	    $lat = $waypoints[$i]['lat'];
	    $lon = $waypoints[$i]['lon'];
	    log_message('debug', 'WaypointModel::store(2): ts('.$ts.') lat('.$lat.') lon('.$lon.')');
	    if (!$this->db->insert('waypoint_entry',
				   array('device_id' => $device_id,
					 'ts' => date('Y-m-d H:i:s',$ts),
					 'lat' => $lat,
					 'lon' => $lon)))
		return FALSE;
	}
	return TRUE;
    }


    public function view($device_id)
    {
	log_message('debug', 'WaypointModel::view():'.print_r($device_id, TRUE));
	$this->db->where('device_id', $device_id);
	$this->db->order_by('ts', 'asc');

	return $this->db->get('waypoint_entry')->result_array();
    }

    public function delete($device_id, $waypoint_id = FALSE)
    {
	if ($waypoint_id == FALSE)
	    $this->db->where('device_id', $device_id);
	else
	    $this->db->where('id', $waypoint_id);

	$this->db->delete('waypoint_entry');
    }
}
