<?php

class Can extends CI_Controller {

    public function __construct()
    {
	parent::__construct();
	log_message('debug', 'Can::__construct()');
	$this->load->model('can_model');
	$this->load->helper('url');
    }

    public function view()
    {
	log_message('debug', 'Can::view(1)');
	$data['title'] = "View CAN Frames";
	$data['can_list'] = $this->can_model->view();
	$this->load->view('templates/header', $data);
	$this->load->view('can/view', $data);
	$this->load->view('templates/footer');
    }

    public function set($can_id = FALSE)
    {
	log_message('debug', 'Can::create()');

	$this->load->helper('form');
	$this->load->library('form_validation');

	if ($can_id) {
	    $data = $this->can_model->view($can_id);
	    $data['title'] = 'Update existing CAN frame descriptor';
	}
	else
	    $data['title'] = 'Create a new CAN frame descriptor';

	$this->form_validation->set_rules('frame_id', 'Can Frame ID', 'required');
	$this->form_validation->set_rules('label', 'Label', 'required');
	$this->form_validation->set_rules('description', 'Description', 'required');
	$this->form_validation->set_rules('unit_of_measurement', 'Unit of measurement', 'required');
	$this->form_validation->set_rules('min_value', 'Minimum value', 'required');
	$this->form_validation->set_rules('max_value', 'Maximum value', 'required');

	if ($this->form_validation->run() === FALSE)
	{
		$this->load->view('templates/header', $data);
		$this->load->view('can/set', $data);
		$this->load->view('templates/footer');
	}
	else
	{
	    log_message('debug', 'Can::create(): Invoking model');
	    if ($can_id || $this->input->post('submit') == 'Update')
		$this->can_model->update($this->input->post('frame_id'),
					 $this->input->post('label'),
					 $this->input->post('description'),
					 $this->input->post('unit_of_measurement'),
					 $this->input->post('min_value'),
					 $this->input->post('max_value'));

	    else
		$this->can_model->create($this->input->post('frame_id'),
					 $this->input->post('label'),
					 $this->input->post('description'),
					 $this->input->post('unit_of_measurement'),
					 $this->input->post('min_value'),
					 $this->input->post('max_value'));

	    redirect("can/view");
	}
    }

    public function delete($can_frame_id = FALSE)
    {
	if ($can_frame_id)
	    $this->can_model->delete($can_frame_id);

	redirect("can/view");
    }
}
