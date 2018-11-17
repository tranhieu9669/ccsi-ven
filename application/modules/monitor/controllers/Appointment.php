<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointment extends MY_Controller {
	public $_center = array();
	public $_frametime = array();

	public function __construct()
    {
        parent::__construct();
        # trung tam
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'spa');
        $this->_center = $this->db->get('tbl_centers')->result_array();
    }

    function index(){
    	$where_now = array();
    	$framenow = array();
    	$this->db->select('id, name, start, end');
        $this->db->where('status', 'on');
        $this->db->where('starttime >=', '2000-01-01 '.date('H:i:s'));
        $framenow = $this->db->get('tbl_frametime')->result_array();
        foreach ($framenow as $key => $value) {
        	array_push($where_now, $value['id']);
        }

        $where_next = array();
    	$framenext = array();
    	$this->db->select('id, name, start, end');
        $this->db->where('status', 'on');
        $this->db->where('starttime <', '2000-01-01 '.date('H:i:s'));
        $framenext = $this->db->get('tbl_frametime')->result_array();
        foreach ($framenext as $key => $value) {
        	array_push($where_next, $value['id']);
        }

        $datenow = date('Y-m-d');
        $datenext = date('Y-m-d', strtotime($datenow . ' + 1 days'));

    	$appointmentnow = array();
    	$this->db->select('id_center,id_frametime,sum(case app_status when "cancel" then 1 else 0 end) as cancel, count(1) as total');
		$this->db->from('tbl_appointments');
		$this->db->where('last_app', 'on');
		$this->db->where_in('id_frametime', $where_now);
		$this->db->where('app_date', $datenow);
		$this->db->group_by('id_center,id_frametime');
		$appointmentnow = $this->db->get()->result_array();

		$_appointment = array();
		if(isset($appointmentnow) AND !empty($appointmentnow)){
			foreach ($appointmentnow as $key => $value) {
				$id_center = $value['id_center'];
				$id_frametime = $value['id_frametime'];
				$total = $value['total'];
				$cancel = $value['cancel'];

				$_appointment[$id_center.'-'.$id_frametime]['total'] = $total;
				$_appointment[$id_center.'-'.$id_frametime]['cancel'] = $cancel;
			}
		}

		$appointmentnext = array();
    	$this->db->select('id_center,id_frametime,sum(case app_status when "cancel" then 1 else 0 end) as cancel, count(1) as total');
		$this->db->from('tbl_appointments');
		$this->db->where('last_app', 'on');
		$this->db->where_in('id_frametime', $where_next);
		$this->db->where('app_date', $datenext);
		$this->db->group_by('id_center,id_frametime');
		$appointmentnext = $this->db->get()->result_array();

		if(isset($appointmentnext) AND !empty($appointmentnext)){
			foreach ($appointmentnext as $key => $value) {
				$id_center = $value['id_center'];
				$id_frametime = $value['id_frametime'];
				$total = $value['total'];
				$cancel = $value['cancel'];

				$_appointment[$id_center.'-'.$id_frametime]['total'] = $total;
				$_appointment[$id_center.'-'.$id_frametime]['cancel'] = $cancel;
			}
		}

		$dataReturn = array();
		foreach ($this->_center as $key => $value) {
			$id_center = $value['id'];
			$name_center = $value['name'];
			$dataReturn[$id_center]['name'] = $name_center;
			foreach ($framenow as $_key => $_value) {
				$id_frametime = $_value['id'];
				$name_frametime = $_value['name'];
				$start_frametime = $_value['start'];
				$end_frametime = $_value['end'];

				$dataReturn[$id_center]['data'][] = array(
					'frametime' => $name_frametime,
					'total' => (isset($_appointment[$id_center.'-'.$id_frametime]['total'])? $_appointment[$id_center.'-'.$id_frametime]['total'] : 0),
					'cancel' => (isset($_appointment[$id_center.'-'.$id_frametime]['cancel']) ? $_appointment[$id_center.'-'.$id_frametime]['cancel'] : 0)
				);
			}

			foreach ($framenext as $_key => $_value) {
				$id_frametime = $_value['id'];
				$name_frametime = $_value['name'];
				$start_frametime = $_value['start'];
				$end_frametime = $_value['end'];

				$dataReturn[$id_center]['data'][] = array(
					'frametime' => $name_frametime,
					'total' => (isset($_appointment[$id_center.'-'.$id_frametime]['total'])? $_appointment[$id_center.'-'.$id_frametime]['total'] : 0),
					'cancel' => (isset($_appointment[$id_center.'-'.$id_frametime]['cancel']) ? $_appointment[$id_center.'-'.$id_frametime]['cancel'] : 0)
				);
			}
		}

		$data['dataReturn'] = $dataReturn;
    	# app
    	$data['content']   = 'appointment/index';
    	$this->setlayout($data, 'v2/monitor');
    }

    function load(){
    	$where_now = array();
    	$framenow = array();
    	$this->db->select('id, name, start, end');
        $this->db->where('status', 'on');
        $this->db->where('starttime >=', '2000-01-01 '.date('H:i:s'));
        $framenow = $this->db->get('tbl_frametime')->result_array();
        foreach ($framenow as $key => $value) {
        	array_push($where_now, $value['id']);
        }

        $where_next = array();
    	$framenext = array();
    	$this->db->select('id, name, start, end');
        $this->db->where('status', 'on');
        $this->db->where('starttime <', '2000-01-01 '.date('H:i:s'));
        $framenext = $this->db->get('tbl_frametime')->result_array();
        foreach ($framenext as $key => $value) {
        	array_push($where_next, $value['id']);
        }

        $datenow = date('Y-m-d');
        $datenext = date('Y-m-d', strtotime($datenow . ' + 1 days'));

    	$appointmentnow = array();
    	$this->db->select('id_center,id_frametime,sum(case app_status when "cancel" then 1 else 0 end) as cancel, count(1) as total');
		$this->db->from('tbl_appointments');
		$this->db->where('last_app', 'on');
		$this->db->where_in('id_frametime', $where_now);
		$this->db->where('app_date', $datenow);
		$this->db->group_by('id_center,id_frametime');
		$appointmentnow = $this->db->get()->result_array();

		$_appointment = array();
		if(isset($appointmentnow) AND !empty($appointmentnow)){
			foreach ($appointmentnow as $key => $value) {
				$id_center = $value['id_center'];
				$id_frametime = $value['id_frametime'];
				$total = $value['total'];
				$cancel = $value['cancel'];

				$_appointment[$id_center.'-'.$id_frametime]['total'] = $total;
				$_appointment[$id_center.'-'.$id_frametime]['cancel'] = $cancel;
			}
		}

		$appointmentnext = array();
    	$this->db->select('id_center,id_frametime,sum(case app_status when "cancel" then 1 else 0 end) as cancel, count(1) as total');
		$this->db->from('tbl_appointments');
		$this->db->where('last_app', 'on');
		$this->db->where_in('id_frametime', $where_next);
		$this->db->where('app_date', $datenext);
		$this->db->group_by('id_center,id_frametime');
		$appointmentnext = $this->db->get()->result_array();

		if(isset($appointmentnext) AND !empty($appointmentnext)){
			foreach ($appointmentnext as $key => $value) {
				$id_center = $value['id_center'];
				$id_frametime = $value['id_frametime'];
				$total = $value['total'];
				$cancel = $value['cancel'];

				$_appointment[$id_center.'-'.$id_frametime]['total'] = $total;
				$_appointment[$id_center.'-'.$id_frametime]['cancel'] = $cancel;
			}
		}

		$dataReturn = array();
		foreach ($this->_center as $key => $value) {
			$id_center = $value['id'];
			$name_center = $value['name'];
			$dataReturn[$id_center]['name'] = $name_center;
			foreach ($framenow as $_key => $_value) {
				$id_frametime = $_value['id'];
				$name_frametime = $_value['name'];
				$start_frametime = $_value['start'];
				$end_frametime = $_value['end'];

				$dataReturn[$id_center]['data'][] = array(
					'frametime' => $name_frametime,
					'total' => (isset($_appointment[$id_center.'-'.$id_frametime]['total'])? $_appointment[$id_center.'-'.$id_frametime]['total'] : 0),
					'cancel' => (isset($_appointment[$id_center.'-'.$id_frametime]['cancel']) ? $_appointment[$id_center.'-'.$id_frametime]['cancel'] : 0)
				);
			}

			foreach ($framenext as $_key => $_value) {
				$id_frametime = $_value['id'];
				$name_frametime = $_value['name'];
				$start_frametime = $_value['start'];
				$end_frametime = $_value['end'];

				$dataReturn[$id_center]['data'][] = array(
					'frametime' => $name_frametime,
					'total' => (isset($_appointment[$id_center.'-'.$id_frametime]['total'])? $_appointment[$id_center.'-'.$id_frametime]['total'] : 0),
					'cancel' => (isset($_appointment[$id_center.'-'.$id_frametime]['cancel']) ? $_appointment[$id_center.'-'.$id_frametime]['cancel'] : 0)
				);
			}
		}

		$data['dataReturn'] = $dataReturn;
    	# app
    	$data['content']   = 'appointment/load';
    	$this->setlayout($data, null);
    }
}