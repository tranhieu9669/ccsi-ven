<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function department_by_center(){
        $id_center = isset($_GET['id_center']) ? $_GET['id_center'] : FALSE;

        $center = array();
        $this->db->select('id, name');
        $this->db->where('id_center in(' . $id_center . ')');
        //$this->db->where('id_center', $id_center);
        $this->db->where('status', 'on');
        $center = $this->db->get('tbl_departments')->result_array();

        echo json_encode($center);
    }

    function group_by_department(){
        $id_department = isset($_GET['id_department']) ? $_GET['id_department'] : FALSE;

        $group = array();
        $this->db->select('id, name');
        $this->db->where('id_department in(' . $id_department . ')');
        //$this->db->where('id_department', $id_department);
        $this->db->where('status', 'on');
        $group = $this->db->get('tbl_groups')->result_array();

        echo json_encode($group);
    }

    function agent_by_group(){
        $id_group = isset($_GET['id_group']) ? $_GET['id_group'] : FALSE;

        $agent = array();
        $this->db->select('id, full_name, username');
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $this->db->where('id_group in(' . $id_group . ')');
        //$this->db->where('id_group', $id_group);
        $agent = $this->db->get('tbl_accounts')->result_array();

        echo json_encode($agent);
    }

    function frametime_by_center(){
        $id_center = isset($_GET['id_center']) ? $_GET['id_center'] : FALSE;

        $frametime = array();
        $this->db->select('id,name,start,end');
        $this->db->where('id_center in(' . $id_center . ')');
        //$this->db->where('id_center', $id_center);
        $this->db->where('status', 'on');
        $frametime = $this->db->get('tbl_frametime')->result_array();

        echo json_encode($frametime);
    }

    function frametime_appointment_check(){
        $id_center_spa = isset($_GET['id_center']) ? $_GET['id_center'] : FALSE;
        $id_center_call = $this->_id_center;
        $id_department = $this->_id_department;
        $dateapp = isset($_GET['dateapp']) ? $_GET['dateapp'] : FALSE;

        $result = array();
        if( $id_center_spa AND $id_center_call AND $dateapp ){
            $limit_center = array();
            $this->db->select('id_frametime');
            $this->db->where('limited > 0');
            $this->db->where('limited > appointment');
            $this->db->where('id_center', $id_center_spa);
            $this->db->where('date', $dateapp);
            $limit_center = $this->db->get('tbl_limit_center')->result_array();

            $_limit_center = array();
            foreach ($limit_center as $key => $value) {
                array_push($_limit_center, intval($value['id_frametime']));
            }

            if( isset($_limit_center) AND !empty($_limit_center) ){
                # check giới hạn phòng
                $limit_department = array();
                $this->db->select('id_frametime');
                $this->db->where('limited > 0');
                $this->db->where('limited > appointment');
                $this->db->where('id_center_call', $id_center_call);
                $this->db->where('id_center_spa', $id_center_spa);
                $this->db->where('id_department', $id_department);
                $this->db->where('date', $dateapp);
                $this->db->where_in('id_frametime', $_limit_center);
                $limit_department = $this->db->get('tbl_limit_department')->result_array();

                $_limit_department = array();
                if( isset($limit_department) AND !empty($limit_department) ){
                    foreach ($limit_department as $key => $value) {
                        array_push($_limit_department, intval($value['id_frametime']));
                    }
                }
				
                if( isset($_limit_department) AND !empty($_limit_department) ){
                    $this->db->select('id,name,start,end');
                    $this->db->where('status', 'on');
                    $this->db->where_in('id', $_limit_department);
                    $result = $this->db->get('tbl_frametime')->result_array();
                }
            }
        }
        echo json_encode($result);
    }

    function district_by_city(){
        $id_city = isset($_GET['id_city']) ? $_GET['id_city'] : FALSE;

        $district = array();
        $this->db->select('id, name');
        $this->db->where('id_city in(' . $id_city . ')');
        //$this->db->where('id_city', $id_city);
        $this->db->where('status', 'on');
        $district = $this->db->get('tbl_district')->result_array();

        echo json_encode($district);
    }

    function status_call_child1(){
        $id_call_status = isset($_GET['id_call_status']) ? $_GET['id_call_status'] : FALSE;

        $status_child1 = array();
        $this->db->select('id, name');
        $this->db->where('id_call_status', $id_call_status);
        $this->db->where('status', 'on');
        $status_child1 = $this->db->get('tbl_call_status_child_c1')->result_array();
        echo json_encode($status_child1);
    }

    function status_call_child2(){
        $id_call_status     = isset($_GET['id_call_status']) ? $_GET['id_call_status'] : FALSE;
        $id_call_status_c1  = isset($_GET['id_call_status_c1']) ? $_GET['id_call_status_c1'] : FALSE;

        $status_child2 = array();
        $this->db->select('id, name');
        $this->db->where('id_call_status', $id_call_status);
        $this->db->where('id_call_status_c1', $id_call_status_c1);
        $this->db->where('status', 'on');
        $status_child2 = $this->db->get('tbl_call_status_child_c2')->result_array();
        echo json_encode($status_child2);
    }

    function center_by_city(){
        $id_city = isset($_GET['id_city']) ? $_GET['id_city'] : FALSE;
        $type    = isset($_GET['type']) ? $_GET['type'] : "call";

        $district = array();
        $this->db->select('id, code, name');
        $this->db->where('id_city in(' . $id_city . ')');
        $this->db->where('type', $type);
        $this->db->where('status', 'on');
        $district = $this->db->get('tbl_centers')->result_array();

        echo json_encode($district);
    }

    function cus_autocomplete(){
        if (isset($_GET['term'])){
            $return_arr = array();

            $this->db->select('id,fullname');
            $this->db->where('fullname like "%'.$_GET['term'].'%" OR mobile like "%'.$_GET['term'].'%"');
            $this->db->where('blacklist', 0);
            $result = $this->db->get('tbl_customer')->result_array();

            if( isset($result) AND !empty($result) ){
                foreach ($result as $key => $value) {
                    //array_push($return_arr, array('id'=>$value['id'],'fullname'=>$value['fullname']));
                    $return_arr['id']       = $value['id'];
                    $return_arr['fullname'] = $value['fullname'];
                }
            }

            echo json_encode($return_arr);
        }
    }

    function fileup_by_source(){
        $id_source = isset($_GET['id_source']) ? $_GET['id_source'] : FALSE;

        $result = array();
        $this->db->select('id,title');
        $this->db->where('id_source in('.$id_source.')');
        $result = $this->db->get('tbl_file_upload')->result_array();

        echo json_encode($result);
    }
}