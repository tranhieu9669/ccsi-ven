<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment extends MX_Controller {
	public $_center = array();
	public $_frametime = array();

	public function __construct()
    {
        parent::__construct();
        # trung tam
        $this->db->select('id, alert as name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'spa');
        $this->db->order_by('position');
        $this->_center = $this->db->get('tbl_centers')->result_array();
    }

    public function index()
    {
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
    	if(isset($where_now) AND !empty($where_now)){
    		$this->db->select('id_center,id_frametime,sum(case app_status when "cancel" then 1 else 0 end) as cancel, count(1) as total');
			$this->db->from('tbl_appointments');
			$this->db->where('last_app', 'on');
			$this->db->where_in('id_frametime', $where_now);
			$this->db->where('app_date', $datenow);
			$this->db->group_by('id_center,id_frametime');
			$appointmentnow = $this->db->get()->result_array();
    	}

    	$limitnow = array();
    	if(isset($where_now) AND !empty($where_now)){
    		$this->db->select('date,id_center,id_frametime,limited');
    		$this->db->from('tbl_limit_center');
    		$this->db->where_in('id_frametime', $where_now);
    		$this->db->where('date', $datenow);
    		$limitnow = $this->db->get()->result_array();
    	}
    	$_limitnow = array();
    	if(isset($limitnow) AND !empty($limitnow)){
    		foreach ($limitnow as $key => $value) {
    			$id_center = $value['id_center'];
    			$id_frametime = $value['id_frametime'];
    			$limited = $value['limited'];
    			$_limitnow[$id_center.'-'.$id_frametime] = $limited;
    		}
    	}

		$_appointment = array();
		if(isset($appointmentnow) AND !empty($appointmentnow)){
			foreach ($appointmentnow as $key => $value) {
				$id_center = $value['id_center'];
				$id_frametime = $value['id_frametime'];
				$total = $value['total'];
				$cancel = $value['cancel'];
				$limited = isset($_limitnow[$id_center.'-'.$id_frametime]) ? $_limitnow[$id_center.'-'.$id_frametime] : 0;

				$round = 0;
				if($limited > 0){
					$round = round( (($total - $cancel)*100)/$limited, 0, PHP_ROUND_HALF_DOWN);
				}

				$_appointment[$id_center.'-'.$id_frametime]['total'] = $total;
				$_appointment[$id_center.'-'.$id_frametime]['cancel'] = $cancel;
				$_appointment[$id_center.'-'.$id_frametime]['limit'] = $limited;
				$_appointment[$id_center.'-'.$id_frametime]['round'] = $round;
			}
		}

		$appointmentnext = array();
		if(isset($where_next) AND !empty($where_next)){
			$this->db->select('id_center,id_frametime,sum(case app_status when "cancel" then 1 else 0 end) as cancel, count(1) as total');
			$this->db->from('tbl_appointments');
			$this->db->where('last_app', 'on');
			$this->db->where_in('id_frametime', $where_next);
			$this->db->where('app_date', $datenext);
			$this->db->group_by('id_center,id_frametime');
			$appointmentnext = $this->db->get()->result_array();
		}

		$limitnext = array();
    	if(isset($where_next) AND !empty($where_next)){
    		$this->db->select('date,id_center,id_frametime,limited');
    		$this->db->from('tbl_limit_center');
    		$this->db->where_in('id_frametime', $where_next);
    		$this->db->where('date', $datenext);
    		$limitnext = $this->db->get()->result_array();
    	}
    	$_limitnext = array();
    	if(isset($limitnext) AND !empty($limitnext)){
    		foreach ($limitnext as $key => $value) {
    			$id_center = $value['id_center'];
    			$id_frametime = $value['id_frametime'];
    			$limited = $value['limited'];
    			$_limitnext[$id_center.'-'.$id_frametime] = $limited;
    		}
    	}

		if(isset($appointmentnext) AND !empty($appointmentnext)){
			foreach ($appointmentnext as $key => $value) {
				$id_center = $value['id_center'];
				$id_frametime = $value['id_frametime'];
				$total = $value['total'];
				$cancel = $value['cancel'];
				$limited = isset($_limitnext[$id_center.'-'.$id_frametime]) ? $_limitnext[$id_center.'-'.$id_frametime] : 0;
				$round = 0;
				if($limited > 0){
					$round = round( (($total - $cancel)*100)/$limited, 0, PHP_ROUND_HALF_DOWN);
				}

				$_appointment[$id_center.'-'.$id_frametime]['total'] = $total;
				$_appointment[$id_center.'-'.$id_frametime]['cancel'] = $cancel;
				$_appointment[$id_center.'-'.$id_frametime]['limit'] = $limited;
				$_appointment[$id_center.'-'.$id_frametime]['round'] = $round;
			}
		}
		$dataResult = $_appointment;

		$data['dataResult'] = $dataResult;
		$data['center'] = $this->_center;

		$data['framenow'] = $framenow;
		$data['framenext'] = $framenext;
    	$this->load->view('appointment', $data);
    }

    function loadcontent(){
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
    	if(isset($where_now) AND !empty($where_now)){
    		$this->db->select('id_center,id_frametime,sum(case app_status when "cancel" then 1 else 0 end) as cancel, count(1) as total');
			$this->db->from('tbl_appointments');
			$this->db->where('last_app', 'on');
			$this->db->where_in('id_frametime', $where_now);
			$this->db->where('app_date', $datenow);
			$this->db->group_by('id_center,id_frametime');
			$appointmentnow = $this->db->get()->result_array();
    	}

    	$limitnow = array();
    	if(isset($where_now) AND !empty($where_now)){
    		$this->db->select('date,id_center,id_frametime,limited');
    		$this->db->from('tbl_limit_center');
    		$this->db->where_in('id_frametime', $where_now);
    		$this->db->where('date', $datenow);
    		$limitnow = $this->db->get()->result_array();
    	}
    	$_limitnow = array();
    	if(isset($limitnow) AND !empty($limitnow)){
    		foreach ($limitnow as $key => $value) {
    			$id_center = $value['id_center'];
    			$id_frametime = $value['id_frametime'];
    			$limited = $value['limited'];
    			$_limitnow[$id_center.'-'.$id_frametime] = $limited;
    		}
    	}

		$_appointment = array();
		if(isset($appointmentnow) AND !empty($appointmentnow)){
			foreach ($appointmentnow as $key => $value) {
				$id_center = $value['id_center'];
				$id_frametime = $value['id_frametime'];
				$total = $value['total'];
				$cancel = $value['cancel'];
				$limited = isset($_limitnow[$id_center.'-'.$id_frametime]) ? $_limitnow[$id_center.'-'.$id_frametime] : 0;

				$round = 0;
				if($limited > 0){
					$round = round( (($total - $cancel)*100)/$limited, 0, PHP_ROUND_HALF_DOWN);
				}

				$_appointment[$id_center.'-'.$id_frametime]['total'] = $total;
				$_appointment[$id_center.'-'.$id_frametime]['cancel'] = $cancel;
				$_appointment[$id_center.'-'.$id_frametime]['limit'] = $limited;
				$_appointment[$id_center.'-'.$id_frametime]['round'] = $round;
			}
		}

		$appointmentnext = array();
		if(isset($where_next) AND !empty($where_next)){
			$this->db->select('id_center,id_frametime,sum(case app_status when "cancel" then 1 else 0 end) as cancel, count(1) as total');
			$this->db->from('tbl_appointments');
			$this->db->where('last_app', 'on');
			$this->db->where_in('id_frametime', $where_next);
			$this->db->where('app_date', $datenext);
			$this->db->group_by('id_center,id_frametime');
			$appointmentnext = $this->db->get()->result_array();
		}

		$limitnext = array();
    	if(isset($where_next) AND !empty($where_next)){
    		$this->db->select('date,id_center,id_frametime,limited');
    		$this->db->from('tbl_limit_center');
    		$this->db->where_in('id_frametime', $where_next);
    		$this->db->where('date', $datenext);
    		$limitnext = $this->db->get()->result_array();
    	}
    	$_limitnext = array();
    	if(isset($limitnext) AND !empty($limitnext)){
    		foreach ($limitnext as $key => $value) {
    			$id_center = $value['id_center'];
    			$id_frametime = $value['id_frametime'];
    			$limited = $value['limited'];
    			$_limitnext[$id_center.'-'.$id_frametime] = $limited;
    		}
    	}

		if(isset($appointmentnext) AND !empty($appointmentnext)){
			foreach ($appointmentnext as $key => $value) {
				$id_center = $value['id_center'];
				$id_frametime = $value['id_frametime'];
				$total = $value['total'];
				$cancel = $value['cancel'];
				$limited = isset($_limitnext[$id_center.'-'.$id_frametime]) ? $_limitnext[$id_center.'-'.$id_frametime] : 0;
				$round = 0;
				if($limited > 0){
					$round = round( (($total - $cancel)*100)/$limited, 0, PHP_ROUND_HALF_DOWN);
				}

				$_appointment[$id_center.'-'.$id_frametime]['total'] = $total;
				$_appointment[$id_center.'-'.$id_frametime]['cancel'] = $cancel;
				$_appointment[$id_center.'-'.$id_frametime]['limit'] = $limited;
				$_appointment[$id_center.'-'.$id_frametime]['round'] = $round;
			}
		}
		$dataResult = $_appointment;

		$data['dataResult'] = $dataResult;
		$data['center'] = $this->_center;

		$data['framenow'] = $framenow;
		$data['framenext'] = $framenext;
    	$this->load->view('appcontent', $data);
    }
}