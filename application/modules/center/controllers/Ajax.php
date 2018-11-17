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
        $this->db->where('id_city in('.$id_city.')');
        $this->db->where('type', 'call');
        $this->db->where('status', 'on');
        $center = $this->db->get('tbl_centers')->result_array();
        echo json_encode($center);
    }

    function department_by_center(){
    	$id_center = isset($_GET['id_center']) ? $_GET['id_center'] : FALSE;
    	$department = array();
        $this->db->select('id, name');
        $this->db->where('id_center in('.$id_center.')');
        $this->db->where('status', 'on');
        $department = $this->db->get('tbl_departments')->result_array();
        echo json_encode($department);
    }

    function group_by_center(){
        $id_center = isset($_GET['id_center']) ? $_GET['id_center'] : FALSE;
        $group = array();
        $this->db->select('id, name');
        $this->db->where('id_center in('.$id_center.')');
        $this->db->where('status', 'on');
        $group = $this->db->get('tbl_groups')->result_array();
        echo json_encode($group);
    }

    function agent_by_center(){
        $id_center = isset($_GET['id_center']) ? $_GET['id_center'] : FALSE;
        $agent = array();
        $this->db->select('id, full_name, ext');
        $this->db->where('id_center in('.$id_center.')');
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $agent = $this->db->get('tbl_accounts')->result_array();
        echo json_encode($agent);
    }

    function group_by_department(){
        $id_department = isset($_GET['id_department']) ? $_GET['id_department'] : FALSE;
        $group = array();
        $this->db->select('id, name');
        $this->db->where('id_department in('.$id_department.')');
        $this->db->where('status', 'on');
        $group = $this->db->get('tbl_groups')->result_array();
        echo json_encode($group);
    }

    function agent_by_department(){
        $id_department = isset($_GET['id_department']) ? $_GET['id_department'] : FALSE;
        $agent = array();
        $this->db->select('id, full_name, ext');
        $this->db->where('id_department in('.$id_department.')');
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $agent = $this->db->get('tbl_accounts')->result_array();
        echo json_encode($agent);
    }

    function agent_by_group(){
        $id_group = isset($_GET['id_group']) ? $_GET['id_group'] : FALSE;
        $agent = array();
        $this->db->select('id, full_name, ext');
        $this->db->where('id_group in('.$id_group.')');
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $agent = $this->db->get('tbl_accounts')->result_array();
        echo json_encode($agent);
    }

    function fileup_by_source(){
        $id_source = isset($_GET['id_source']) ? $_GET['id_source'] : FALSE;
        $fileup = array();
        $this->db->select('id, title');
        $this->db->where('id_source in('.$id_source.')');
        $fileup = $this->db->get('tbl_file_upload')->result_array();
        echo json_encode($fileup);
    }
}