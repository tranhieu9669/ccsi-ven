<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Confirm extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Thống kê Confirm khách hàng', ''),
        );

        $data['content'] = 'index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function appointment(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách lịch hẹn', ''),
        );

        if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 0;

            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;

            $id_center = isset($request['id_center']) ? $request['id_center'] : false;
            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            $enddate = $startdate;
            #$enddate = date('Y-m-d', strtotime($startdate . ' + 5 days'));

            $this->db->start_cache();

            $this->db->select('app.id,app.cus_mobile,app.cus_first_name,app.cus_last_name,cen.name,app.id_center,app.app_datetime,app.agent_ext,app.sms_status,app.app_status,app.app_created_at');
            $this->db->from('tbl_appointments as app');
            $this->db->join('tbl_centers as cen', 'cen.id=app.id_center and cen.type="spa"');

            //$this->db->where('app.id_agent', $this->_id_agent);
            $this->db->where('app.last_app', 'on');
            $this->db->where('app.app_status !=', 'cancel');

            if($inputsearch){
                $this->db->where('app.cus_mobile like "%'.$inputsearch.'%"');
            }else{
                if($id_center){
                    $this->db->where('app.id_center', $id_center);
                }

                if($startdate){
                    $this->db->where('app.app_datetime > ', $startdate . ' 07:00:00');
                    $this->db->where('app.app_datetime < ', $enddate . ' 22:00:00');
                }
            }

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->limit($limit, $offset);
            $dataResult = $this->db->get()->result_array();

            $this->db->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'query' => $this->db->last_query(),
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order' => 50,
            '_wth_first_name' => 160,
            '_wth_last_name' => 85,
            '_wth_mobile' => 100,
            '_wth_time' => 160,
            '_wth_status' => 80,
            '_wth_action' => 60,
        ));

        $centerspa = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'spa');
        $centerspa = $this->db->get('tbl_centers')->result_array();
        $data['centerspa'] = $centerspa;

        $data['content'] = 'appointment';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function callapp($id=false){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách lịch hẹn', base_url().'confirm/appointment'),
            array('Confirm khách hàng', ''),
        );

    	$appointmentdt = array();
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $appointmentdt = $_POST;

            $app_status = $this->input->post('app_status');
            $id_center_city = $this->input->post('id_center_city');
            $appointment = $this->input->post('appointment');
            $id_center = $this->input->post('id_center');
            $hd_frametime_start = $this->input->post('hd_frametime_start');
            $id_frametime = $this->input->post('id_frametime');
            $content_app = $this->input->post('content_app');
            $resendsms = $this->input->post('resendSMS');

            $this->db->select('app_code,id_cus,cus_mobile,demo6t,cus_first_name,cus_last_name,cus_source,id_call,id_city,id_center,id_frametime,app_datetime,app_date,app_time,app_content,id_center_call,id_department,id_group,id_agent,agent_ext,agent_mobile,agent_first_name,agent_last_name,sms_time_send,sms_status,sms_content,app_created_at,app_status,crm_status');
            $this->db->where('id', $id);
            $data_update = $this->db->get('tbl_appointments')->row_array();
			
			$cus_mobile = $data_update['cus_mobile'];
            switch ($app_status) {
                case 'change':
                    //$this->db->where('id', $id);
					$this->db->where('cus_mobile', $cus_mobile);
                    $this->db->update('tbl_appointments', array('last_app' => 'off'));

                    $old_status = $data_update['app_status'];
                    $old_id_center = $data_update['id_center'];
                    $old_id_frametime = $data_update['id_frametime'];
                    $old_app_date = $data_update['app_date'];
                    $old_id_center_call = $data_update['id_center_call'];
                    $old_id_department = $data_update['id_department'];
                    $demo6t = $data_update['demo6t'];

                    $data_update['id_city'] = $id_center_city;
                    $data_update['id_center'] = $id_center;
                    $data_update['demo6t'] = $demo6t;
                    $data_update['id_frametime'] = $id_frametime;
                    $data_update['app_datetime'] = $appointment . ' ' . $hd_frametime_start . ':00';
                    $data_update['app_date'] = $appointment;
                    $data_update['app_time'] = $hd_frametime_start;
                    $data_update['app_content'] = $content_app;
                    $data_update['sms_status'] = 'new';
                    $data_update['app_status'] = 'change';
                    $data_update['crm_status'] = 'local';
                    $data_update['app_created_at'] = date('Y-m-d H:i:s');
                    $data_update['last_by_updated'] = $this->_uid;

                    $this->db->insert('tbl_appointments', $data_update);

                    if($old_status == 'cencal'){
                        # tang lich moi
                        $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`+1) WHERE `id_center`='.$id_center.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');

                        $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`+1) WHERE `id_center_spa`='.$id_center.' AND `id_center_call`='.$old_id_center_call.' AND `id_department`='.$old_id_department.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');
                    }else{
                        # tru lich cu
                        $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`-1) WHERE `id_center`='.$old_id_center.' AND `id_frametime`='.$old_id_frametime.' AND `date`="'.$old_app_date.'"');

                        $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`-1) WHERE `id_center_spa`='.$old_id_center.' AND `id_center_call`='.$old_id_center_call.' AND `id_department`='.$old_id_department.' AND `id_frametime`='.$old_id_frametime.' AND `date`="'.$old_app_date.'"');

                        # tang lich moi
                        $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`+1) WHERE `id_center`='.$id_center.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');

                        $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`+1) WHERE `id_center_spa`='.$id_center.' AND `id_center_call`='.$old_id_center_call.' AND `id_department`='.$old_id_department.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');
                    }
                    break;

                case 'comfirm':
                    //$this->db->where('id', $id);
					$this->db->where('cus_mobile', $cus_mobile);
                    $this->db->update('tbl_appointments', array('last_app' => 'off'));

                    $data_update['app_content'] = $content_app;
                    $data_update['app_status'] = 'comfirm';
                    $data_update['crm_status'] = 'local';
                    $data_update['app_created_at'] = date('Y-m-d H:i:s');
                    $data_update['last_by_updated'] = $this->_uid;

                    if( isset($resendsms) AND !empty($resendsms) AND $resendsms){
                        $data_update['sms_resend_status'] = 'new';
                        $data_update['sms_resend_updated_by'] = $this->_uid;
                    }

                    $this->db->insert('tbl_appointments', $data_update);
                    break;

                case 'cancel':
					$this->db->where('mobile', $cus_mobile);
                    $this->db->update('tbl_customer', array('app' => 0));
                    //$this->db->where('id', $id);
					$this->db->where('cus_mobile', $cus_mobile);
                    $this->db->update('tbl_appointments', array('last_app' => 'off'));

                    $old_status = $data_update['app_status'];
                    $old_id_center = $data_update['id_center'];
                    $old_id_frametime = $data_update['id_frametime'];
                    $old_app_date = $data_update['app_date'];
                    $old_id_center_call = $data_update['id_center_call'];
                    $old_id_department = $data_update['id_department'];

                    $data_update['app_content'] = $content_app;
                    $data_update['app_status'] = 'cancel';
                    $data_update['crm_status'] = 'local';
                    $data_update['app_created_at'] = date('Y-m-d H:i:s');
                    $data_update['last_by_updated'] = $this->_uid;

                    $this->db->insert('tbl_appointments', $data_update);

                    $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`-1) WHERE `id_center`='.$old_id_center.' AND `id_frametime`='.$old_id_frametime.' AND `date`="'.$old_app_date.'"');

                    $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`-1) WHERE `id_center_spa`='.$old_id_center.' AND `id_center_call`='.$old_id_center_call.' AND `id_department`='.$old_id_department.' AND `id_frametime`='.$old_id_frametime.' AND `date`="'.$old_app_date.'"');
                    break;
                
                default:
                    # code...
                    break;
            }

            redirect(base_url().'confirm/appointment');
        }

        if( !isset($appointmentdt) OR empty($appointmentdt) ){
            $this->db->select('id,CONCAT(cus_first_name, " ", cus_last_name) as fullname,cus_mobile,id_center,id_frametime,app_date,app_time,app_content,CONCAT(agent_first_name, " ", agent_last_name) as agentname,agent_ext,id_department,id_group,app_created_at');
            $this->db->where('id', $id);
            $appointmentdt = $this->db->get('tbl_appointments')->row_array();

            # xoa lich trung
            $this->db->where('cus_mobile', $appointmentdt['cus_mobile']);
            $this->db->where('app_created_at', $appointmentdt['app_created_at']);
            $this->db->where('last_app', 'off');
            $this->db->delete('tbl_appointments');
            # insert call app

            $id_center = $appointmentdt['id_center'];
            $this->db->select('name');
            $this->db->where('id', $id_center);
            $this->db->where('type', 'spa');
            $_center = $this->db->get('tbl_centers')->row_array();
            $appointmentdt['cenname'] = $_center['name'];

            $id_frametime = $appointmentdt['id_frametime'];
            $this->db->select('name, start, end');
            $this->db->where('id', $id_frametime);
            $_frametime = $this->db->get('tbl_frametime')->row_array();
            $appointmentdt['franame'] = $_frametime['name'].'('.$_frametime['start'].' - '.$_frametime['end'].')';

            $id_department = $appointmentdt['id_department'];
            $this->db->select('name');
            $this->db->where('id', $id_department);
            $_department = $this->db->get('tbl_departments')->row_array();
            $appointmentdt['depname'] = $_department['name'];

            $id_group = $appointmentdt['id_group'];
            $this->db->select('name');
            $this->db->where('id', $id_group);
            $_group = $this->db->get('tbl_groups')->row_array();
            $appointmentdt['groname'] = $_group['name'];
        }

        # Thông tin Tỉnh/Thành phố
        $city = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $city = $this->db->get('tbl_city')->result_array();
        $data['city'] = $city;

        $data['appointmentdt'] = $appointmentdt;
        $data['content'] = 'detail';
        $this->setlayout($data, 'v2/'.$this->_role);
    }
}