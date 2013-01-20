<?php
class Device_model extends CI_Model {

    public function __construct()
    {
	log_message('debug', 'DeviceModel::construct()');
	$this->load->model('logging_model');
	$this->load->helper('exosense'); // For the exosense_client
	$this->load->library('jsonrpc');
	$this->load->database();
    }

    public function create_device($device_id,
				  $device_type,
				  $device_key,
				  $server_key,
				  $description,
				  $waypoint_interval,
				  $can_bus_speed,
				  $can_frame_type,
				  $retry_count,
				  $retry_interval)
    {
	log_message('debug', 'DeviceModel::create()');
	$client = exosense_client($this,
				  'exodm:provision-device',
				  array('dev-id' => $device_id,
					'device-type' => $device_type,
					'server-key' => $server_key ,
					'device-key' => $device_key));
	$res = $client->send_request();
	if (!$res) {
	    log_message('debug', 'DeviceModel::create(): send_request(provision): Failed: '.$client->get_response()->error_code);
	    return FALSE;
	}

	// Find result element
	$res = $client->get_response_object();
	if (!array_key_exists('result', $res) ||
	    !array_key_exists('result', $res['result']) ||
	    ($res_code = $res['result']['result']) != '0') {
	    log_message('debug', 'DeviceModel::create(): send_request(provision): Failed: '.$client->get_response()->error_message);
	    return FALSE;
	}


	//
	// Setup the config set.
	//
	$client->method('exodm:create-config-set');
	$client->request(array('name' => $device_id,
			       'yang' => $this->config->item('exosense_yang_file'),
			       'notification-url' => $this->config->item('exosense_notification_url')));

	$res = $client->send_request();
	if (!$res) {
	    log_message('debug', 'DeviceModel::create(): send_request(cfg-set): Failed: '.$client->get_response()->error_message);
	    return FALSE;
	}

	// Find result element
	$res = $client->get_response_object();
	$rescode = 'unknown';
	if (!array_key_exists('result', $res) ||
	    !array_key_exists('result', $res['result']) ||
	    ($res_code = $res['result']['result']) != '0') {
	    log_message('debug', 'DeviceModel::create(): send_request(cfg-set): Failed: '.$client->get_response()->error_message.' Result code": '.$rescode);

	    return FALSE;
	}


	//
	// Add config set members.
	//
	$client->method('exodm:add-config-set-members');
	$client->request(array('name' => array($device_id),
			       'dev-id' => array($device_id)));

	$res = $client->send_request();
	if (!$res) {
	    log_message('debug', 'DeviceModel::create(): send_request(cfg-add): Failed');
	    return FALSE;
	}

	// Find result element
	$res = $client->get_response_object();
	$rescode = 'unknown';
	if (!array_key_exists('result', $res) ||
	    !array_key_exists('result', $res['result']) ||
	    ($res_code = $res['result']['result']) != '0') {
	    log_message('debug', 'DeviceModel::create(): send_request(cfg-add): Failed: '.$client->get_response()->error_message.' Result code": '.$rescode);
	    return FALSE;
	}

	//
	// Invoke the update-config-entry-request
	//
	$res = exosense_request_reuse($client,
				      'demo:update-config-entry-request',
				      array('device-id' => $device_id,
					    'config-entries' =>
					    array(array('name' => 'waypoint_interval',
							'val' => $waypoint_interval),
						  array('name' => 'can_bus_speed',
							'val' => $can_bus_speed),
						  array('name' => 'can_frame_type',
							'val' => $can_frame_type),
						  array('name' => 'retry_count',
							'val' => $retry_count),
						  array('name' => 'retry_interval',
							'val' => $retry_interval),
						  array('name' => 'waypoint_interval',
							'val' => $waypoint_interval)
						)) , FALSE);

	if (!$res) {
	    log_message('debug', 'DeviceModel::create(): send_request(upd-cfg): Failed');
	    return FALSE;
	}

	// Find result element
	$res = $client->get_response_object();
	$rescode = 'unknown';
	if (!array_key_exists('result', $res) ||
	    !array_key_exists('result', $res['result']) ||
	    ($res_code = $res['result']['result']) != '0') {
	    log_message('debug', 'DeviceModel::create(): send_request(upd-cfg): Failed: '.$client->get_response()->error_message.' Result code": '.$rescode);
//	    return FALSE; // While waiting for this to be implemented
	}

	// Insert into database
	$this->db->insert('device',
			  array('device_id' => $device_id,
				'device_key' => $device_key,
				'server_key' => $server_key,
				'description' => $description,
				'can_speed' => $can_bus_speed,
				'can_frame_type' => $can_frame_type,
				'connect_retry_count' => $retry_count,
				'connect_retry_interval' => $retry_interval,
				'waypoint_interval' => $waypoint_interval));

    }

    public function update_device($device_id,
				  $device_key,
				  $server_key,
				  $description,
				  $waypoint_interval,
				  $can_bus_speed,
				  $can_frame_type,
				  $retry_count,
				  $retry_interval)
    {
	log_message('debug', 'DeviceModel::update()');
	// Update device and server key.
	$client = exosense_client($this,
				  'exodm:update-device',
				  array('dev-id' => $device_id,
					'server-key' => $device_key,
					'device-key' => $server_key));
	$client->send_request();
	$res = $client->get_response_object();
	$rescode = 'unknown';
	if (!array_key_exists('result', $res) ||
	    !array_key_exists('result', $res['result']) ||
	    ($res_code = $res['result']['result']) != '0') {
	    log_message('debug', 'DeviceModel::update(): send_request(upd-device): Failed: '.$client->get_response()->error_message.' Result code": '.$rescode);
	    return FALSE;
	}

	// Send config request
	$res = exosense_request_reuse($client,
				      'demo:update-config-entry-request',
				      array('device-id' => $device_id,
					    'config-entries' =>
					    array(
						  array('name' => 'waypoint_interval',
							'val' => $waypoint_interval),
						  array('name' => 'can_bus_speed',
							'val' => $can_bus_speed),
						  array('name' => 'can_frame_type',
							'val' => $can_frame_type),
						  array('name' => 'retry_count',
							'val' => $retry_count),
						  array('name' => 'retry_interval',
							'val' => $retry_interval),
						  array('name' => 'waypoint_interval',
							'val' => $waypoint_interval)
						)), FALSE);
	$client->send_request();
	$res = $client->get_response_object();
	$rescode = 'unknown';
	if (!array_key_exists('result', $res) ||
	    !array_key_exists('result', $res['result']) ||
	    ($res_code = $res['result']['result']) != '0') {
	    log_message('debug', 'DeviceModel::update(): send_request(upd-cfg-req): Failed: '.$client->get_response()->error_message.' Result code": '.$rescode);
//	    return FALSE; // While waiting for this to be implemented
	}

	// Update database
	$this->db->where('device_id', $device_id);
	$this->db->update('device',
			  array('description' => $description,
				'device_key' => $device_key,
				'server_key' => $server_key,
				'can_speed' => $can_bus_speed,
				'can_frame_type' => $can_frame_type,
				'connect_retry_count' => $retry_count,
				'connect_retry_interval' => $retry_interval,
				'waypoint_interval' => $waypoint_interval));

	log_message('debug', 'DeviceModel::update(). device_id['.$device_id.']');
    }


    public function list_devices()
    {
	$client = exosense_client($this,
				  'exodm:list-devices',
				  array('n' => 100,
					'previous' => '0'));
	$res = $client->send_request();
	if (!$res) {
	    log_message('debug', 'DeviceModel::list(7): Failed');
	    return FALSE;
	}

	// Find result element
	$res = $client->get_response_object();
	$devarr = $res['result']['devices'];

	// Traverse the list
	foreach($devarr as $key => $device) {
	    // Load db description for the current device.
	    $this->db->where('device_id', $device['dev-id']);
	    $res = $this->db->get('device');

	    if ($res->num_rows() > 0) {
		$dbdev = $res->result_array();
		$dbdev = $dbdev[0];
		log_message('debug', 'DeviceModel::list(): Got '.print_r($devarr[$key], TRUE));
		log_message('debug', 'DeviceModel::list(): Got '.print_r($dbdev, TRUE));
		$devarr[$key]['description'] = $dbdev['description'];
		$devarr[$key]['waypoint_interval'] = $dbdev['waypoint_interval'];
		$devarr[$key]['can_bus_speed'] = $dbdev['can_speed'];
		$devarr[$key]['can_frame_type'] = $dbdev['can_frame_type'];
		$devarr[$key]['retry_count'] = $dbdev['connect_retry_count'];
		$devarr[$key]['retry_interval'] = $dbdev['connect_retry_interval'];
		$devarr[$key]['server_key'] = $dbdev['server_key'];
		$devarr[$key]['device_key'] = $dbdev['device_key'];
	    } else {
		$devarr[$key]['description'] = 'n/a';
		$devarr[$key]['waypoint_interval'] = 'n/a';
		$devarr[$key]['can_bus_speed'] = 'n/a';
		$devarr[$key]['can_frame_type'] = 'n/a';
		$devarr[$key]['retry_count'] = 'n/a';
		$devarr[$key]['retry_interval'] = 'n/a';
		$devarr[$key]['server_key'] = 'n/a';
		$devarr[$key]['device_key'] = 'n/a';
	    }
	}

	return $devarr;
    }

    public function lookup_device($dev_id)
    {
	$client = exosense_client($this,
				  'exodm:lookup-device',
				  array('dev-id' => $dev_id));

	$res = $client->send_request();
	if (!$res) {
	    log_message('debug', 'DeviceModel::list(7): Failed');
	    return FALSE;
	}
	$res = $client->get_response_object();

	$device = $res['result']['devices'][0];
	$device['devid'] = $device['dev-id'];
	$device['device_type'] = $device['device-type'];

	// Load db description for the current device.
	$this->db->where('device_id', $device['dev-id']);
	$query = $this->db->get('device');
	if ($query->num_rows()  == 0) {
	    $device['description'] = '??';
	    $device['waypoint_interval']  = '??';
	    $device['can_bus_speed']  = '??';
	    $device['can_frame_type'] = '??';
	    $device['retry_count']  = '??';
	    $device['retry_interval']  = '??';
	    $device['server_key']  = '??';
	    $device['device_key']  = '??';
	} else {
	    $dbdev = $query->result_array();
	    $dbdev = $dbdev[0];
	    log_message('debug', 'DeviceModel::lookup_device(): Got '.print_r($dbdev, TRUE));
	    $device['description'] = $dbdev['description'];
	    $device['waypoint_interval'] = $dbdev['waypoint_interval'];
	    $device['can_bus_speed'] = $dbdev['can_speed'];
	    $device['can_frame_type'] = $dbdev['can_frame_type'];
	    $device['retry_count'] = $dbdev['connect_retry_count'];
	    $device['retry_interval'] = $dbdev['connect_retry_interval'];
	    $device['server_key'] = $dbdev['server_key'];
	    $device['device_key'] = $dbdev['device_key'];
	}
	return $device;
    }

    public function list_device_types()
    {
	$client = exosense_client($this,
				  'exodm:list-device-types',
				  array('n' => 100,
					'previous' => '""'), FALSE);
	$res = $client->send_request();
	if (!$res) {
	    log_message('debug', 'DeviceModel::list(7): Failed');
	    return FALSE;
	}

	// Find result element
	$res = $client->get_response_object();
	$devarr = $res['result']['device-types'];

	return $devarr;
    }

    public function delete_device($device_id)
    {
	log_message('debug', 'DeviceModel::delete(). device_id['.$device_id.']');
	$client = exosense_client($this,
				  'exodm:delete-config-set',
				  array('name' => $device_id));

	$client->send_request();

	// Send config request
	$res = exosense_request_reuse($client,
				      'exodm:deprovision-devices',
				      array('dev-id' => array($device_id)), FALSE);
	$client->send_request();
	$res = $client->get_response_object();

	// Delete logging specification model from db.
	$this->logging_model->delete($device_id, FALSE, TRUE);

	// Update database
	$this->db->where('device_id', $device_id);

	$this->db->delete('device');

    }
}
