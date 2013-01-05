<?php

class Logging extends CI_Controller {

    public function __construct()
    {
	parent::__construct();
	log_message('debug', 'Logging::__construct()');
	$this->load->model('logging_model');
	$this->load->model('can_model');
	$this->load->helper('url');
    }

    public function view($dev_id)
    {
	log_message('debug', 'Logging::view(1): '.$dev_id);
	$data['dev_id'] = $dev_id;
	$data['logging_list'] = $this->logging_model->view($dev_id);
	log_message('debug', 'Logging::view(2): '.$data['logging_list']);

	$data['title'] = "View Log Specification";
	$data['home_url'] = $this->config->item('base_url');
	$this->load->view('templates/header', $data);
	$this->load->view('logging/view', $data);
	$this->load->view('templates/footer');
    }

    public function set($dev_id = FALSE, $can_frame_id = FALSE)
    {
	$this->load->helper('form');
	$this->load->library('form_validation');
	log_message('debug', 'Logging::set(1):'.$dev_id.', '.$can_frame_id);

	if ($dev_id)
	    $data = $this->logging_model->view($dev_id, $can_frame_id);

	$data['dev_id'] = $dev_id;
	$data['can_frame_id'] = $can_frame_id;
	$data['can_frames'] = $this->can_model->view();
	$data['home_url'] = $this->config->item('base_url');

	if ($can_frame_id) {
	    $data['title'] = 'Update existing log specification';
	    log_message('debug', 'Logging::set(3)');
	}
	else
	    $data['title'] = 'Create a new log specification';
	    log_message('debug', 'Logging::set(4)');

	$this->form_validation->set_rules('can_frame_id', 'CAN Frame ID', 'required');
	$this->form_validation->set_rules('sample_interval', 'Sample Interval (msec)', 'required');
	$this->form_validation->set_rules('buffer_size', 'CAN Frames to store', 'required');
	log_message('debug', 'Logging::set(5)');

	if ($this->form_validation->run() === FALSE)
	{
	    $this->load->view('templates/header', $data);
	    $this->load->view('logging/set', $data);
	    $this->load->view('templates/footer');
	}
	else
	{
	    log_message('debug', 'Logging::set(5):'.$this->input->post('submit') );

	    if ($can_frame_id || $this->input->post('submit')== 'Update')
		$this->logging_model->update($this->input->post('dev_id'),
					     $this->input->post('can_frame_id'),
					     $this->input->post('sample_interval'),
					     $this->input->post('buffer_size'));


	    else
		$this->logging_model->create($this->input->post('dev_id'),
					     $this->input->post('can_frame_id'),
					     $this->input->post('sample_interval'),
					     $this->input->post('buffer_size'));

	    redirect('logging/view/'.$this->input->post('dev_id'));

	}

	log_message('debug', 'Logging::set(6)');
    }

    public function delete($dev_id, $can_frame_id)
    {
	log_message('debug', 'Logging::view(1)');
	$this->logging_model->delete($dev_id, $can_frame_id);
	redirect('logging/view/'.$dev_id);
    }

    public function push($dev_id)
    {
	log_message('debug', 'Logging::push(1)');
	$this->logging_model->push($dev_id);
	redirect('logging/view/'.$dev_id);
    }
}
