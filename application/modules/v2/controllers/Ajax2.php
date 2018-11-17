<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax2 extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function group_by_center(){
        $id_center = isset($_GET['id_center']) ? $_GET['id_center'] : FALSE;

        $group = array();
        $this->db->select('id, name');
        $this->db->where('id_center in(' . $id_center . ')');
        $this->db->where('status', 'on');
        $group = $this->db->get('tbl_groups')->result_array();

        echo json_encode($group);
    }

    function group_by_department(){
        $id_department = isset($_GET['id_department']) ? $_GET['id_department'] : FALSE;

        $group = array();
        $this->db->select('id, name');
        $this->db->where('id_department in(' . $id_department . ')');
        $this->db->where('status', 'on');
        $group = $this->db->get('tbl_groups')->result_array();

        echo json_encode($group);
    }

    function agent_by_center(){
        $id_center = isset($_GET['id_center']) ? $_GET['id_center'] : FALSE;

        $agent = array();
        $this->db->select('id, full_name, ext');
        $this->db->where('id_center in(' . $id_center . ')');
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $agent = $this->db->get('view_account')->result_array();

        echo json_encode($agent);
    }

    function agent_by_department(){
        $id_department = isset($_GET['id_department']) ? $_GET['id_department'] : FALSE;

        $agent = array();
        $this->db->select('id, full_name, ext');
        $this->db->where('id_department in(' . $id_department . ')');
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $agent = $this->db->get('view_account')->result_array();

        echo json_encode($agent);
    }

    function agent_by_group(){
        $id_group = isset($_GET['id_group']) ? $_GET['id_group'] : FALSE;

        $agent = array();
        $this->db->select('id, full_name, ext');
        $this->db->where('id_group in(' . $id_group . ')');
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $agent = $this->db->get('view_account')->result_array();

        echo json_encode($agent);
    }

    function limit_assign_department(){
        $id_center_call = isset($_GET['id_center_call']) ? $_GET['id_center_call'] : FALSE;
        $id_center_spa = isset($_GET['id_center_spa']) ? $_GET['id_center_spa'] : FALSE;
        $date = isset($_GET['date']) ? $_GET['date'] : FALSE;

        $result = array();

        $limit_call = array();
        $this->db->select('id_frametime,limit,schedule');
        $this->db->where('status', 'on');
        $this->db->where('id_center', $id_center_call);
        $limit_call = $this->db->get('tbl_frametime_center_limit')->result_array();

        $limit_spa = array();
        $this->db->select('id_frametime,limit,schedule');
        $this->db->where('status', 'on');
        $this->db->where('date', $date);
        $this->db->where('id_center', $id_center_spa);
        $limit_spa = $this->db->get('tbl_frametime_center_limit')->result_array();

        $result['call'] = $limit_call;
        $result['spa'] = $limit_spa;

        echo json_encode($result);
    }

    function limited_assign_department(){
        $id_center_call = isset($_GET['id_center_call']) ? $_GET['id_center_call'] : FALSE;
        $id_center_spa = isset($_GET['id_center_spa']) ? $_GET['id_center_spa'] : FALSE;
        $date = isset($_GET['date']) ? $_GET['date'] : FALSE;

        $result = array();
        $this->db->select('id_frametime,id_department,limit,schedule');
        $this->db->where('status', 'on');
        $this->db->where('date', $date);
        $this->db->where('id_center', $id_center_call);
        $this->db->where('id_center_spa', $id_center_spa);
        $result = $this->db->get('tbl_frametime_department_limit')->result_array();
        echo json_encode($result);
    }
}