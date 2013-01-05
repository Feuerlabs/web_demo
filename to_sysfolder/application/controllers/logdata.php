<?php

class Logdata extends CI_Controller {

    public function __construct()
    {
	parent::__construct();
	log_message('debug', 'Logdata::__construct()');
	$this->load->model('logging_model');
	$this->load->model('can_model');
	$this->load->model('logdata_model');
	$this->load->helper('url');
	$this->load->helper('download');
    }

    public function summary($dev_id, $graph_filename = FALSE)
    {
	log_message('debug', 'Logdata::summary(1): '.$dev_id);

	$logspec = &$this->logging_model->view($dev_id);

	$summary = array();
	for($i=0; $i < count($logspec); ++$i) {
	    $can_desc = $this->can_model->view($logspec[$i]['frame_id']);

	    $summary[$i] = &$this->logdata_model->summary($dev_id, $logspec[$i]['frame_id']);
	    $summary[$i]['label'] = $can_desc['label'];
	    $summary[$i]['frame_id'] =  $logspec[$i]['frame_id'];
	}

	$data['title'] = "View Log data";
	$data['dev_id'] = $dev_id;
	$data['summary_list'] = &$summary;
	$data['home_url'] = $this->config->item('base_url');
	if ($graph_filename)
	    $data['graph_filename'] = $graph_filename;

	$this->load->view('templates/header', $data);
	$this->load->view('logdata/summary', $data);
	$this->load->view('templates/footer');
    }


    public function details($dev_id, $frame_id)
    {
	log_message('debug', 'Logdata::details(0): '.getcwd());
	$can_spec = &$this->can_model->view($frame_id);
	include $this->config->item("libchart_root").'/classes/libchart.php';
	$logdata_list = &$this->logdata_model->view($dev_id, $frame_id);
	log_message('debug', 'Logdata::details(1): '.$dev_id.' '.count($logdata_list));


	if (count($logdata_list) < 1024)
	    $width = 1024;
	else
	    $width = count($logdata_list);
	$chart = new LineChart($width, 480);

	$dataSet = new XYDataSet();
	foreach($logdata_list as $logdata) {
	    $dataSet->addPoint(new Point($logdata['ts'], $logdata['can_value']));
	}
	$chart->getConfig()->setShowPointCaption(false);
	$chart->setDataSet($dataSet);

	$chart->setTitle("Device: ".$dev_id." CAN Frame: ".$can_spec['label']."[".$frame_id."]");
	$graph_filename = $dev_id."_".$frame_id.".png";
	$chart->render('generated/'.$graph_filename);
	redirect('logdata/summary/'.$dev_id.'/'.$graph_filename);
    }

    public function download($dev_id)
    {
	$logdata_list = &$this->logdata_model->view($dev_id);
	$logdata_str = '';
	log_message('debug', 'Logdata::download(1): '.$dev_id.' '.count($logdata_list));

	for($i=0; $i < count($logdata_list); ++$i)
	    $logdata_str .= $logdata_list[$i]['ts'].','.$logdata_list[$i]['frame_id'].','.$logdata_list[$i]['can_value']."\n";

	force_download('device_'.$dev_id.'.csv', $logdata_str);
	redirect('logdata/summary/'.$dev_id);
    }

    public function delete($dev_id, $frame_id)
    {
	log_message('debug', 'Logdata::delete(): dev_id('.$dev_id.') frame_id('.$frame_id.')');
	$this->logdata_model->delete($dev_id, $frame_id);
	redirect('logdata/summary/'.$dev_id);
    }

    public function reset_alarm($dev_id, $alarm_id)
    {
	$this->load->model('alarmdata_model');
	log_message('debug', 'Logdata::reset_alarm(): dev_id('.$dev_id.') alarm_id('.$alarm_id.')');
	$this->alarmdata_model->reset($dev_id, $alarm_id);
	redirect('logdata/summary/'.$dev_id);
    }
}
