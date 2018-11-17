<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ajax extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function center_by_city(){
        $id_city = isset($_GET['id_city']) ? $_GET['id_city'] : FALSE;
        $center = array();
        $this->db->select('id, name');
        $this->db->where('id_city', $id_city);
        $this->db->where('type', 'call');
        $this->db->where('status', 'on');
        $center = $this->db->get('tbl_centers')->result_array();
        echo json_encode($center);
    }

    function department_by_center(){
    	$id_center = isset($_GET['id_center']) ? $_GET['id_center'] : FALSE;
    	$department = array();
        $this->db->select('id, name');
        $this->db->where('id_center', $id_center);
        $this->db->where('status', 'on');
        $department = $this->db->get('tbl_departments')->result_array();
        echo json_encode($department);
    }

    function group_by_department(){
        $id_department = isset($_GET['id_department']) ? $_GET['id_department'] : FALSE;
        $group = array();
        $this->db->select('id, name');
        $this->db->where('id_department', $id_department);
        $this->db->where('status', 'on');
        $group = $this->db->get('tbl_groups')->result_array();
        echo json_encode($group);
    }
}