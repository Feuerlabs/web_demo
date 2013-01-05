<?php

class Home extends CI_Controller {

    public function __construct()
    {
	parent::__construct();
	log_message('debug', 'Home::__construct()');
	$this->load->model('device_model');
	$this->load->model('can_model');
    }

    public function index()
    {
	$device_data['device_list'] = $this->device_model->list_devices();

	$can_data['can_list'] = $this->can_model->view();
	$home_data['home_url'] = $this->config->item('base_url');

	$home_data['device_list_view'] = $this->load->view('device/view', $device_data, TRUE);
	$home_data['can_list_view'] = $this->load->view('can/view', $can_data, TRUE);

	$this->load->view('templates/header', $home_data);
	$this->load->view('home/home', $home_data);
	$this->load->view('templates/footer');
    }
}
