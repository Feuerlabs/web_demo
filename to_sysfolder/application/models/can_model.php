<?php
class Can_model extends CI_Model {

    public function __construct()
    {
	log_message('debug', 'Can_model::construct()');
	$this->load->library('jsonrpc');
	$this->load->database();
    }

    public function create($can_frame_id,
			   $label,
			   $description,
			   $unit_of_measurment,
			   $min_value,
			   $max_value)
    {
	log_message('debug', 'CanModel::create()');
	$this->db->insert('can_frame',
			  array('frame_id' => $can_frame_id,
				'label' => $label,
				'unit_of_measurement' => $unit_of_measurment,
				'description' => $description,
				'min_value' => $min_value,
				'max_value' => $max_value));

    }


    public function update($can_frame_id,
			   $label,
			   $description,
			   $unit_of_measurment,
			   $min_value,
			   $max_value)
    {
	log_message('debug', 'CanModel::update()');
	$this->db->where('frame_id', $can_frame_id);

	$this->db->update('can_frame',
			  array('label' => $label,
				'description' => $description,
				'unit_of_measurement' => $unit_of_measurment,
				'min_value' => $min_value,
				'max_value' => $max_value));
    }


    public function view($can_frame_id = FALSE)
    {
	log_message('debug', 'CanModel::view():'.print_r($can_frame_id, TRUE));
	if ($can_frame_id) {
	    $this->db->where('frame_id', $can_frame_id);
	    $res = $this->db->get('can_frame')->result_array();
	    return $res[0];
	}

	return $this->db->get('can_frame')->result_array();
    }


    public function delete($can_frame_id)
    {
	$this->db->where('frame_id', $can_frame_id);

	$this->db->delete('can_frame');
    }
}
