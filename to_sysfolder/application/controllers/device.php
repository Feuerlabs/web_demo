<?php

class Device extends CI_Controller {

    public function __construct()
    {
	parent::__construct();
	$this->load->model('device_model');
	$this->config->load('exosense');
	$this->load->helper('exosense'); // For the exosense_client
	$this->load->helper('url');
	log_message('debug', 'Device::__construct()');
    }



    public function set($dev_id = FALSE)
    {
	log_message('debug', 'Device::create()');

	$this->load->helper('form');
	$this->load->library('form_validation');

	if ($dev_id) {
	    $data = $this->device_model->lookup_device($dev_id);
	    $data['title'] = 'Update existing device';
	}
	else
	    $data['title'] = 'Create a new device';

	$data['device_types'] = $this->device_model->list_device_types();
	$this->form_validation->set_rules('device_id', 'DeviceID', 'required');
	$this->form_validation->set_rules('device_type', 'Device Type', 'required');
	$this->form_validation->set_rules('description', 'description', 'required');
	$this->form_validation->set_rules('waypoint_interval', 'Waypoint Interval', 'required');
	$this->form_validation->set_rules('can_bus_speed', 'CAN bus speed', 'required');
	$this->form_validation->set_rules('can_frame_id_type', 'CAN frame ID length', 'required');
	$this->form_validation->set_rules('retry_count', 'Retry Count', 'required');
	$this->form_validation->set_rules('retry_interval', 'Retry Interval', 'required');
	$this->form_validation->set_rules('server_key', 'Server Key', 'required');
	$this->form_validation->set_rules('device_key', 'Device Key', 'required');

	if ($this->form_validation->run() === FALSE)
	{
		$this->load->view('templates/header', $data);
		log_message('debug', 'Device::set(data): '. print_r($data, TRUE));
		$this->load->view('device/set', $data);
		$this->load->view('templates/footer');
	}
	else
	{
	    log_message('debug', 'Device::create(): Invoking model');
	    if ($this->input->post('submit') == 'Update')
		$this->device_model->update_device($this->input->post('device_id'),
						   $this->input->post('device_key'),
						   $this->input->post('server_key'),
						   $this->input->post('description'),
						   $this->input->post('waypoint_interval'),
						   $this->input->post('can_bus_speed'),
						   $this->input->post('can_frame_id_type'),
						   $this->input->post('retry_count'),
						   $this->input->post('retry_interval'));
	    else
		$this->device_model->create_device($this->input->post('device_id'),
						   $this->input->post('device_type'),
						   $this->input->post('device_key'),
						   $this->input->post('server_key'),
						   $this->input->post('description'),
						   $this->input->post('waypoint_interval'),
						   $this->input->post('can_bus_speed'),
						   $this->input->post('can_frame_id_type'),
						   $this->input->post('retry_count'),
						   $this->input->post('retry_interval'));

	    redirect("home");
	}
    }

    public function delete($dev_id = FALSE)
    {
	if ($dev_id)
	    $this->device_model->delete_device($dev_id);

	redirect("home");
    }
}
