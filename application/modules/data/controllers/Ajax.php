<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ajax extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function get_department(){
    	$id_center = isset($_GET['id_center']) ? $_GET['id_center'] : false;

    	$department = array();
    	if(isset($id_center) AND $id_center){
    		$this->db->select('id,name');
    		$this->db->from('tbl_departments');
    		$this->db->where('status', 'on');
    		$this->db->where('id_center', $id_center);

    		$department = $this->db->get()->result_array();
    	}
    	echo json_encode($department);
    }

    function get_group_agent(){
    	$id_department = isset($_GET['id_department']) ? $_GET['id_department'] : false;

    	$group = array();
    	$agent = array();
    	if(isset($id_department) AND !empty($id_department)){
    		$this->db->select('id,name');
    		$this->db->from('tbl_groups');
    		$this->db->where('status', 'on');
    		$this->db->where('id_department in('.$id_department.')');
    		$group = $this->db->get()->result_array();

    		$this->db->select('id,full_name,ext');
    		$this->db->from('tbl_accounts');
    		$this->db->where('status', 'on');
    		$this->db->where('id_department in('.$id_department.')');
    		$agent = $this->db->get()->result_array();
    	}
    	$dataResult = array(
    		'group' => $group,
    		'agent' => $agent
		);
    	echo json_encode($dataResult);
    }

    function get_agent(){
    	$id_group = isset($_GET['id_group']) ? $_GET['id_group'] : false;

    	$agent = array();
    	if(isset($id_group) AND !empty($id_group)){
    		$this->db->select('id,full_name,ext');
    		$this->db->from('tbl_accounts');
    		$this->db->where('status', 'on');
    		$this->db->where('id_group in('.$id_group.')');
    		$agent = $this->db->get()->result_array();
    	}
    	echo json_encode($agent);
    }

    function get_status_c1(){
    	$id_call_status = isset($_GET['id_call_status']) ? $_GET['id_call_status'] : false;

    	$status_c1_data = array();
    	if( isset($id_call_status) AND $id_call_status ){
    		$this->db->select('id,name');
    		$this->db->from('tbl_call_status_child_c1');
    		$this->db->where('undata', 1);
    		$this->db->where('id_call_status in('.$id_call_status.')');
    		
    		$status_c1_data = $this->db->get()->result_array();
    	}

    	echo json_encode($status_c1_data);
    }

    function get_status_c2(){
    	$id_call_status_c1 = isset($_GET['id_call_status_c1']) ? $_GET['id_call_status_c1'] : false;

    	$status_c2_data = array();
    	if( isset($id_call_status_c1) AND $id_call_status_c1 ){
    		$this->db->select('id,name');
    		$this->db->from('tbl_call_status_child_c2');
    		$this->db->where('undata', 1);
    		$this->db->where('id_call_status_c1 in('.$id_call_status_c1.')');
    		
    		$status_c2_data = $this->db->get()->result_array();
    	}

    	echo json_encode($status_c2_data);
    }

    function getsourcebytype($type){
        $source = array();

        if(isset($type) AND !empty($type)){
            $this->db->select('id,name');
            $this->db->from('tbl_source');
            $this->db->where('status', 'on');
            
            switch (strtoupper($type)) {
                case 'PG':
                    $this->db->where('pg', 1);
                    break;

                case 'LEAD':
                    $this->db->where('mkt', 1);
                    break;

                case 'BIGDATA':
                    $this->db->where('big', 1);
                    break;
                
                default:
                    $this->db->where('big !=', 1);
                    $this->db->where('mkt !=', 1);
                    $this->db->where('pg !=', 1);
                    break;
            }

            $source = $this->db->get()->result_array();
        }
        
        echo json_encode($source);
    }
}