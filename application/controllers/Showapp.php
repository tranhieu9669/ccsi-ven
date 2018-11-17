<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Showapp extends MX_Controller {
	public function __construct()
    {
        parent::__construct();
    }

	function index(){
		# get center
		$center = array();
		$this->db->select('id,name');
		$this->db->where('type', 'call');
		$this->db->where('status', 'on');
		$this->db->order_by('position');
		$center = $this->db->get('tbl_centers')->result_array();

		$department = array();
		$this->db->select('id,name');
		$this->db->where('status', 'on');
		$this->db->where('showapp >', 0);
		$this->db->order_by('showapp,name');
		$department = $this->db->get('tbl_departments')->result_array();

		# get group
		$group = array();
		$this->db->select('id,name,id_department');
		$this->db->where('status', 'on');
		$this->db->where('showapp >', 0);
		$this->db->order_by('showapp,name');
		$group=$this->db->get('tbl_groups')->result_array();

		# get app
		$datenow = date('Y-m-d');
		$datenext = date('Y-m-d', strtotime($datenow . ' + 1 days'));
		$data['datenow'] = $datenow;
		$data['datenext'] = $datenext;

		$datestart = date('Y-m-d', strtotime($datenow . ' - 1 days'));
		$dateend = date('Y-m-d', strtotime($datenow . ' + 2 days'));

		$dataapp = array();
		$this->db->select('app_date,id_center_call as id_center,id_department,id_group,count(1) as app');
		$this->db->from('tbl_appointments');
		$this->db->where('app_date >', $datestart);
		$this->db->where('app_date <', $dateend);
		$this->db->where('last_app', 'on');
		$this->db->where('app_status !=', 'cancel');
		$this->db->group_by('app_date,id_city,id_department,id_group');
		$this->db->order_by('id_city,id_department,id_group');
		$app_dt = $this->db->get()->result_array();

		if(isset($app_dt) AND !empty($app_dt)){
			foreach ($app_dt as $key => $value) {
				$app_date = $value['app_date'];
				$id_center = $value['id_center'];
				$id_department = $value['id_department'];
				$id_group = $value['id_group'];
				$app = $value['app'];

				if( ! isset($dataapp[$id_department.'-'.$id_group.'-'.$app_date]) ){
					$dataapp[$id_department.'-'.$id_group.'-'.$app_date] = $app;
				}
			}
		}

		$data['department'] = $department;
	    $data['group'] = $group;
		$data['dataapp'] = $dataapp;
	    $this->load->view('showapp/index', $data);
	}

	function loadcontent(){
		# get center
		$center = array();
		$this->db->select('id,name');
		$this->db->where('type', 'call');
		$this->db->where('status', 'on');
		$this->db->order_by('position');
		$center = $this->db->get('tbl_centers')->result_array();

		$department = array();
		$this->db->select('id,name');
		$this->db->where('status', 'on');
		$this->db->where('showapp >', 0);
		$this->db->order_by('showapp,name');
		$department = $this->db->get('tbl_departments')->result_array();

		# get group
		$group = array();
		$this->db->select('id,name,id_department');
		$this->db->where('status', 'on');
		$this->db->where('showapp >', 0);
		$this->db->order_by('showapp,name');
		$group=$this->db->get('tbl_groups')->result_array();

		# get app
		$datenow = date('Y-m-d');
		$datenext = date('Y-m-d', strtotime($datenow . ' + 1 days'));
		$data['datenow'] = $datenow;
		$data['datenext'] = $datenext;

		$datestart = date('Y-m-d', strtotime($datenow . ' - 1 days'));
		$dateend = date('Y-m-d', strtotime($datenow . ' + 2 days'));

		$dataapp = array();
		$this->db->select('app_date,id_center_call as id_center,id_department,id_group,count(1) as app');
		$this->db->from('tbl_appointments');
		$this->db->where('app_date >', $datestart);
		$this->db->where('app_date <', $dateend);
		$this->db->where('last_app', 'on');
		$this->db->where('app_status !=', 'cancel');
		$this->db->group_by('app_date,id_city,id_department,id_group');
		$this->db->order_by('id_city,id_department,id_group');
		$app_dt = $this->db->get()->result_array();

		if(isset($app_dt) AND !empty($app_dt)){
			foreach ($app_dt as $key => $value) {
				$app_date = $value['app_date'];
				$id_center = $value['id_center'];
				$id_department = $value['id_department'];
				$id_group = $value['id_group'];
				$app = $value['app'];

				if( ! isset($dataapp[$id_department.'-'.$id_group.'-'.$app_date]) ){
					$dataapp[$id_department.'-'.$id_group.'-'.$app_date] = $app;
				}
			}
		}

		$data['department'] = $department;
	    $data['group'] = $group;
		$data['dataapp'] = $dataapp;
	    $this->load->view('showapp/content', $data);
	}
}
?>