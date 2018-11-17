<?php # http://192.168.1.6:8084/api/showup
defined('BASEPATH') OR exit('No direct script access allowed');

class Showup extends MX_Controller {
	public $_center = array();
	public $url_api = array();

	public function __construct()
    {
        parent::__construct();
        $this->db->select('id, code, alert as name, showup');
        $this->db->where('status', 'on');
        $this->db->where('type', 'spa');
        $this->db->order_by('position');
        $this->_center = $this->db->get('tbl_centers')->result_array();
    }

    function index(){
    	# get data app
    	$appointment = array();
    	$sql = "select id_center,";
    	$sql .= " (select id from tbl_frametime where 1=1";
    	$sql .= " and CONCAT( DATE_FORMAT(NOW(), '%Y-%m-%d'), ' ', `startexecute`) < crm_app_income";
    	$sql .= " and CONCAT( DATE_FORMAT(NOW(), '%Y-%m-%d'), ' ', `endexecute`) >= crm_app_income";
    	$sql .= " limit 1) as id_frametime_in, count(1) as count";
    	$sql .= " from tbl_appointments";
		$sql .= " where app_date='".date('Y-m-d')."'";
		$sql .= " and last_app='on'";
		$sql .= " and crm_app_status is not null";
		$sql .= " group by id_center,id_frametime_in";

		$appointment = $this->db->query($sql)->result_array();

		if(isset($appointment) AND !empty($appointment)){
			foreach ($appointment as $key => $value) {
				$id_center = $value['id_center'];
				$id_frametime = $value['id_frametime_in'];
				$count = $value['count'];

				if( !isset($dataResult[$id_center.'-'.$id_frametime]) ){
					$dataResult[$id_center.'_'.$id_frametime] = $count;
				}
			}
		}

    	$framenow = array();
    	$this->db->select('id, name, start, end');
        $this->db->where('status', 'on');
        $this->db->order_by('starttime');
        $framenow = $this->db->get('tbl_frametime')->result_array();
        $data['framenow'] = $framenow;

	    $data['dataResult'] = $dataResult;
		$data['center'] = $this->_center;

	    $this->load->view('showup/index', $data);
    }

    function loadcontent(){
    	# get data app
    	$appointment = array();
    	$appointment = array();
    	$sql = "select id_center,";
    	$sql .= " (select id from tbl_frametime where 1=1";
    	$sql .= " and CONCAT( DATE_FORMAT(NOW(), '%Y-%m-%d'), ' ', `startexecute`) < crm_app_income";
    	$sql .= " and CONCAT( DATE_FORMAT(NOW(), '%Y-%m-%d'), ' ', `endexecute`) >= crm_app_income";
    	$sql .= " limit 1) as id_frametime_in, count(1) as count";
    	$sql .= " from tbl_appointments";
		$sql .= " where app_date='".date('Y-m-d')."'";
		$sql .= " and last_app='on'";
		$sql .= " and crm_app_status is not null";
		$sql .= " group by id_center,id_frametime_in";
		$appointment = $this->db->query($sql)->result_array();

		if(isset($appointment) AND !empty($appointment)){
			foreach ($appointment as $key => $value) {
				$id_center = $value['id_center'];
				$id_frametime = $value['id_frametime_in'];
				$count = $value['count'];

				if( !isset($dataResult[$id_center.'-'.$id_frametime]) ){
					$dataResult[$id_center.'_'.$id_frametime] = $count;
				}
			}
		}

    	$framenow = array();
    	$this->db->select('id, name, start, end');
        $this->db->where('status', 'on');
        $this->db->order_by('starttime');
        $framenow = $this->db->get('tbl_frametime')->result_array();
        $data['framenow'] = $framenow;

	    $data['dataResult'] = $dataResult;
		$data['center'] = $this->_center;

	    $this->load->view('showup/content', $data);
    }
}
?>