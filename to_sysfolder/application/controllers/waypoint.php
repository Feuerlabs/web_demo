<?php

class Waypoint extends CI_Controller {

    public function __construct()
    {
	parent::__construct();
	log_message('debug', 'Waypoint::__construct()');
	$this->load->model('waypoint_model');
	$this->load->library('googlemaps');
	$this->load->helper('url');
	$this->load->helper('download');
    }

    public function view($dev_id)
    {
	log_message('debug', 'Waypoint::view(1): '.$dev_id);
	$data['dev_id'] = $dev_id;
	$waypoint_list = &$this->waypoint_model->view($dev_id);
	$config = array();
	if(count($waypoint_list) > 0)
	    $config['center'] = $waypoint_list[0]['lat'].', '.$waypoint_list[0]['lon'];


	$this->googlemaps->initialize($config);

	log_message('debug', 'Waypoint::view(2): '.$waypoint_list);

	$polyline = array();
	$polyline['points'] = array();
	for($i = 0; $i < count($waypoint_list); ++$i)
	    $polyline['points'][$i] = $waypoint_list[$i]['lat'].', '.$waypoint_list[$i]['lon'];

	$this->googlemaps->add_polyline($polyline);

	$data['map'] = $this->googlemaps->create_map();

	$data['title'] = "View Waypoints";
	$this->load->view('waypoint/view', $data);
    }

    public function download($dev_id)
    {
	$waypoint_list = &$this->waypoint_model->view($dev_id);
	$waypoint_str = '';
	log_message('debug', 'Waypoint::download(1): '.$dev_id.' '.count($waypoint_list));

	for($i=0; $i < count($waypoint_list); ++$i)
	    $waypoint_str .= $waypoint_list[$i]['ts'].','.$waypoint_list[$i]['lat'].','.$waypoint_list[$i]['lon']."\n";

	force_download('device_'.$dev_id.'.csv', $waypoint_str);
	redirect('waypoint/view/'.$dev_id);
    }

    public function delete($dev_id)
    {
	log_message('debug', 'Waypoint::view(1)');
	$this->waypoint_model->delete($dev_id);
	redirect('waypoint/view/'.$dev_id);
    }
}
