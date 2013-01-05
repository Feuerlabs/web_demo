<?php

class Alarm extends CI_Controller {

    public function __construct()
    {
	parent::__construct();
	log_message('debug', 'Alarm::__construct()');
	$this->load->model('alarm_model');
	$this->load->model('can_model');
	$this->load->helper('url');
    }

    public function view($dev_id)
    {
	log_message('debug', 'Alarm::view(1): '.$dev_id);
	$data['dev_id'] = $dev_id;
	$data['alarm_list'] = $this->alarm_model->view($dev_id);
	log_message('debug', 'Alarm::view(2): '.print_r($data['alarm_list'], TRUE));

	$data['title'] = "View Alarm Specification";
	$data['home_url'] = $this->config->item('base_url');
	$this->load->view('templates/header', $data);
	$this->load->view('alarm/view', $data);
	$this->load->view('templates/footer');
    }

    public function set($dev_id = FALSE, $can_frame_id = FALSE)
    {
	$this->load->helper('form');
	$this->load->library('form_validation');
	log_message('debug', 'Alarm::set(1):'.$dev_id.', '.$can_frame_id);

	if ($dev_id)
	    $data = $this->alarm_model->view($dev_id, $can_frame_id);

	$data['dev_id'] = $dev_id;
	$data['can_frame_id'] = $can_frame_id;
	$data['can_frames'] = $this->can_model->view();
	$data['home_url'] = $this->config->item('base_url');
	if ($can_frame_id) {
	    $data['title'] = 'Update existing alarm specification';
	    log_message('debug', 'Alarm::set(3)');
	}
	else
	    $data['title'] = 'Create a new alarm specification';
	    log_message('debug', 'Alarm::set(4)');

	$this->form_validation->set_rules('can_frame_id', 'CAN Frame ID', 'required');
	$this->form_validation->set_rules('trigger_threshold', 'Trigger Threshold', 'required');
	$this->form_validation->set_rules('reset_threshold', 'Reset Threshold', 'required');
	log_message('debug', 'Alarm::set(5)');

	if ($this->form_validation->run() === FALSE)
	{
	    $this->load->view('templates/header', $data);
	    $this->load->view('alarm/set', $data);
	    $this->load->view('templates/footer');
	}
	else
	{
	    log_message('debug', 'Alarm::set(5):'.$this->input->post('submit') );

	    if ($can_frame_id || $this->input->post('submit')== 'Update')
		$this->alarm_model->update($this->input->post('dev_id'),
					     $this->input->post('can_frame_id'),
					     $this->input->post('trigger_threshold'),
					     $this->input->post('reset_threshold'));
	    else
		$this->alarm_model->create($this->input->post('dev_id'),
					     $this->input->post('can_frame_id'),
					     $this->input->post('trigger_threshold'),
					     $this->input->post('reset_threshold'));

	    redirect('alarm/view/'.$this->input->post('dev_id'));

	}

	log_message('debug', 'Alarm::set(6)');
    }

    public function delete($dev_id, $can_frame_id)
    {
	log_message('debug', 'Alarm::view(1)');
	$this->alarm_model->delete($dev_id, $can_frame_id);
	redirect('alarm/view/'.$dev_id);
    }

    public function push($dev_id)
    {
	log_message('debug', 'Alarm::push(1)');
	$this->alarm_model->push($dev_id);
	redirect('alarm/view/'.$dev_id);
    }
}
