<?php

class Exosense extends CI_Controller {

    public function __construct()
    {
	parent::__construct();
	$this->load->model('alarm_model');
	$this->load->library('jsonrpc');

        $this->server = &$this->jsonrpc->get_server();
        $methods = array(
	    'demo:process-waypoints' => array('function' => 'Exosense._process_waypoints',
					 'parameters' => array(array('name' => 'device-id'),
							       array('name' => 'waypoints')),
					 'summary' => 'Processes waypoints from a device.'),
	    'demo:process-logdata' => array('function' => 'Exosense._process_logdata',
				    'parameters' => array(array('name' => 'device-id'),
							  array('name' => 'logdata')),
				    'summary' => 'Processes logging entries from a device.'),

	    'demo:process-alarms' => array('function' => 'Exosense._process_alarms',
				    'parameters' => array(array('name' => 'device-id'),
							  array('name' => 'alarms')),
				     'summary' => 'Processes logging entries from a device.'));

	$this->server->define_methods($methods);
	$this->server->set_object($this);
        $this->server->serve();

	log_message('debug', 'Exosense::__construct()');
    }

    public function index()
    {
    }

    public function process_waypoints()
    {
	// Invoked by code igniter. nil function
    }

    public function process_alarms()
    {
	// Invoked by code igniter. nil function
    }

    public function process_logdata()
    {
	// Invoked by code igniter. nil function
    }

    public function _process_waypoints($arg)
    {
	$this->load->model('waypoint_model');
	log_message('debug', 'Exosense::process_waypoints(): '.print_r($arg, TRUE));

	$res = $this->waypoint_model->store($arg['device-id'], $arg['waypoints']);

	if ($res)
	    return $this->server->send_response(array('result' => '0'));
	else
	    return $this->server->send_response(array('result' => '1'));
    }

    public function _process_logdata($arg)
    {
	$this->load->model('logdata_model');
	log_message('debug', 'Exosense::process_logdata(): '.print_r($arg, TRUE));

	$res = $this->logdata_model->store($arg['device-id'], $arg['logdata']);

	if ($res)
	    return $this->server->send_response(array('result' => '0'));
	else
	    return $this->server->send_response(array('result' => '1'));
    }

    public function _process_alarms($arg)
    {
	$this->load->model('alarmdata_model');
	log_message('debug', 'Exosense::process_alarmdata(): '.print_r($arg, TRUE));

	$res = $this->alarmdata_model->store($arg['device-id'], $arg['alarms']);

	if ($res)
	    return $this->server->send_response(array('result' => '0'));
	else
	    return $this->server->send_response(array('result' => '1'));
    }
}
