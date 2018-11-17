<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ajax extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function statuschild1(){
    	$id_call_status = isset($_GET['id_call_status']) ? $_GET['id_call_status'] : FALSE;
        $status_child1 = array();
        $this->db->select('id, name, type');
        $this->db->where('id_call_status', $id_call_status);
        $this->db->where('status', 'on');
        $status_child1 = $this->db->get('tbl_call_status_child_c1')->result_array();
        echo json_encode($status_child1);
    }

    function statuschild2(){
        $id_call_status     = isset($_GET['id_call_status']) ? $_GET['id_call_status'] : FALSE;
        $id_call_status_c1  = isset($_GET['id_call_status_c1']) ? $_GET['id_call_status_c1'] : FALSE;

        $status_child2 = array();
        $this->db->select('id, name, type');
        $this->db->where('id_call_status', $id_call_status);
        $this->db->where('id_call_status_c1', $id_call_status_c1);
        $this->db->where('status', 'on');
        $status_child2 = $this->db->get('tbl_call_status_child_c2')->result_array();
        echo json_encode($status_child2);
    }

    function districtbycity(){
    	$id_city = isset($_GET['id_city']) ? $_GET['id_city'] : FALSE;

    	$district = array();
    	$this->db->select('id,name');
    	$this->db->where('id_city', $id_city);
    	$this->db->where('status', 'on');
    	$district = $this->db->get('tbl_district')->result_array();
    	echo json_encode($district);
    }

    function centerspabycity(){
    	$id_city = isset($_GET['id_city']) ? $_GET['id_city'] : FALSE;
			
		$id_department_call = $this->_id_department;
		
    	$center = array();
    	$this->db->select('id,code,name');
    	$this->db->where('id_city', $id_city);
    	$this->db->where('type', 'spa');
    	$this->db->where('status', 'on');
		if($id_department_call == 2){ # Hoa Sao HCM
            //$this->db->where('id', 4); # Le Thanh Ton
			$this->db->where_in('id', array(4,9,11)); # Le Thanh Ton , NTH, NTHO
        }elseif ($id_department_call == 8) { # Hoa Sao HN
            #$this->db->where('id', 2); # Thu Khuye
			# 2 - Thuy Khue
			# 5 - Yen Lang
			# 8 - Tran Thai Tong
			$this->db->where_in('id', array(2,5,8));
        }
    	$center = $this->db->get('tbl_centers')->result_array();
    	echo json_encode($center);
    }

    function frametimeappointment(){
    	$id_center = isset($_GET['id_center']) ? $_GET['id_center'] : FALSE;
		$dateapp = isset($_GET['dateapp']) ? $_GET['dateapp'] : FALSE;

		$where_frame_center = array();
		$frame_center = array();
		$this->db->select('id_frametime');
		$this->db->where('date', $dateapp);
		$this->db->where('id_center', $id_center);
		$this->db->where('limited > appointment');
		$frame_center = $this->db->get('tbl_limit_center')->result_array();
		foreach ($frame_center as $key => $value) {
			array_push($where_frame_center, $value['id_frametime']);
		}

		$where_frame = array();
		$frame_department = array();
		$this->db->select('id_frametime');
		$this->db->where('date', $dateapp);
		$this->db->where_in('id_frametime', $where_frame_center);
		$this->db->where('id_center_spa', $id_center);
		$this->db->where('id_center_call', $this->_id_center);
		$this->db->where('id_department', $this->_id_department);
		$this->db->where('limited > appointment');
		$frame_department = $this->db->get('tbl_limit_department')->result_array();
		foreach ($frame_department as $key => $value) {
			array_push($where_frame, $value['id_frametime']);
		}

		$list_frame = array();
		$this->db->select('id,name,start,end');
		$this->db->where_in('id', $where_frame);
		$list_frame = $this->db->get('tbl_frametime')->result_array();

		echo json_encode($list_frame);
    }
}