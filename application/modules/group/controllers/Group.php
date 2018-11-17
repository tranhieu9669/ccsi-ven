<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Group extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách tài khoản', ''),
        );

        # Status call
        $status = array();
        $statuscall = array();
        $this->db->select('id, name, color');
        $this->db->where('status', 'on');
        $statuscall = $this->db->get('tbl_call_status')->result_array();

        # Chart data
        $datenow = date('Y-m-d');
        $title = array(
            'text' => 'Biểu đồ trạng thái cuộc gọi'
        );
        $legend = array(
            'position' => 'bottom'
        );
        $series = array();
        $tooltip = array(
            'visible' => true,
            'format' => '{0}',
            'template' => '#= value # Call' ##= category #/03: 
        );
        $categories = array();
        $dataReport = array();
        $maxval = 1;

        $db_group = $this->setdbconnect($this->_id_group, 'group');
        for($day=6;$day>=0;$day--){
            $datecheck = date('Y-m-d', strtotime($datenow . ' - '.$day.' days'));
            array_push($categories, date('d-m', strtotime($datecheck)));
            foreach ($statuscall as $key => $value) {
                $dataReport[$datecheck][$value['id']] = 0;
            }

            $dataResult = array();
            $db_group->select('id_status, countcall');
            $db_group->where('datecall', $datecheck);
            #$db_group->where('agent_id', $this->_id_agent);
            $dataResult = $db_group->get('tbl_call_report')->result_array();

            if( isset($dataResult) AND !empty($dataResult) ){
                foreach ($dataResult as $key => $value) {
                    $id_status = $value['id_status'];
                    $countcall = $value['countcall'];

                    if($countcall > $maxval){
                        $maxval = $countcall;
                    }

                    $dataReport[$datecheck][$id_status] = $countcall;
                }
            }
        }

        foreach ($statuscall as $key => $value) {
            $_data = array();
            foreach ($dataReport as $_key => $_value) {
                array_push($_data, $_value[$value['id']]);
            }

            $_series = array(
                'type' => 'column', # line
                'data' => $_data,
                'name' => $value['name'],
                'color' => $value['color'],
                'axis' => 'default'
            );

            array_push($series, $_series);
        }

        $majorUnit = 1;
        if($maxval >= 10 AND $maxval < 20){
            $majorUnit = 2;
        }elseif($maxval >= 20 AND $maxval < 40){
            $majorUnit = 3;
        }elseif($maxval >= 40 AND $maxval < 100){
            $majorUnit = 5;
        }else{
            $majorUnit = 10;
        }

        $valueAxes = array(
            array(
                'name' => 'default',
                'color' => '#007eff',
                'min' => 0,
                'max' => $maxval + 1,
                'majorUnit' => $majorUnit
            )
        );

        $categoryAxis = array(
            'categories' => $categories,
            #'axisCrossingValues' => array(32, 32, 0),
            'justified' => true
        );

        $chart = array(
            'title' => $title,
            'legend' => $legend,
            'series' => $series,
            'valueAxes' => $valueAxes,
            'categoryAxis' => $categoryAxis,
            'tooltip' => $tooltip
        );

        $data['chart'] = json_encode($chart);

        $data['content'] = 'index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function account(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách tài khoản', ''),
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

            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;

            $this->db->start_cache();

            $this->db->select('full_name,mobile,ext,roles,dbname');
            $this->db->from('tbl_accounts');
            $this->db->where('status','on');
            $this->db->where('roles !=','group');
            $this->db->where('id_group',$this->_id_group);

            if($inputsearch){
                $this->db->where('(full_name like "%'.$inputsearch.'%" OR ext like "%'.$inputsearch.'%")');
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
            '_wth_order'    => 50,
            '_wth_mobile'   => 150,
            '_wth_ext'      => 100,
            '_wth_dbname'   => 250,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'account';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function checkapp($id_center_spa=false, $id_center_call=false, $id_department=false, $appdate=false, $id_frametime=false){
        $rtn = false;

        if($id_center_spa AND $id_center_call AND $id_department AND $appdate AND $id_frametime){
            # kiem tra gioi han trung tam : tbl_limit_center
            $this->db->select('id, limited');
            $this->db->where('id_center', $id_center_spa);
            $this->db->where('date', $appdate);
            $this->db->where('id_frametime', $id_frametime);
            $this->db->where('limited > appointment');
            $this->db->limit(1);
            $check_center = $this->db->get('tbl_limit_center')->row_array();

            if( isset($check_center['id']) AND !empty($check_center['id']) ){
                $c_limit = $check_center['limited'];

                $check_app = array();
                $this->db->select('count(1) as appointment');
                $this->db->where('id_center', $id_center_spa);
                $this->db->where('id_frametime', $id_frametime);
                $this->db->where('app_date', $appdate);
                $this->db->where('last_app', 'on');
                $this->db->where('app_status !=', 'cancel');
                $check_app = $this->db->get('tbl_appointments')->row_array();

                if( !isset($check_app['appointment']) OR empty($check_app['appointment']) OR (!empty($check_app['appointment']) AND $check_app['appointment'] < $c_limit) ){
                    # kiem tra gioi han phong : tbl_limit_department
                    $this->db->select('id, limited');
                    $this->db->where('id_center_spa', $id_center_spa);
                    $this->db->where('id_center_call', $id_center_call);
                    $this->db->where('id_department', $id_department);
                    $this->db->where('date', $appdate);
                    $this->db->where('id_frametime', $id_frametime);
                    $this->db->where('limited > appointment');
                    $this->db->limit(1);
                    $check_department = $this->db->get('tbl_limit_department')->row_array();

                    if( isset($check_department['id']) AND !empty($check_department['id']) ){
                        $d_limit = $check_department['limited'];

                        $check_app = array();
                        $this->db->select('count(1) as appointment');
                        $this->db->where('id_center', $id_center_spa);
                        $this->db->where('id_center_call', $id_center_call);
                        $this->db->where('id_department', $id_department);
                        $this->db->where('id_frametime', $id_frametime);
                        $this->db->where('app_date', $appdate);
                        $this->db->where('last_app', 'on');
                        $this->db->where('app_status !=', 'cancel');
                        $check_app = $this->db->get('tbl_appointments')->row_array();

                        if( !isset($check_app['appointment']) OR empty($check_app['appointment']) OR (!empty($check_app['appointment']) AND $check_app['appointment'] < $d_limit) ){
                            $rtn = true;
                        }
                    }
                }
            }
        }

        return $rtn;
    }

    function callappointment($id=false){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Gọi lại lịch hẹn', ''),
        );

        $error = '';

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

            $this->db->select('app_code,id_cus,cus_mobile,cus_first_name,cus_last_name,cus_source,id_call,id_city,id_center,id_frametime,app_datetime,app_date,app_time,app_content,id_center_call,id_department,id_group,id_agent,agent_ext,agent_mobile,agent_first_name,agent_last_name,sms_time_send,sms_status,sms_content,app_created_at,app_status,crm_status');
            $this->db->where('id', $id);
            $data_update = $this->db->get('tbl_appointments')->row_array();

            $cus_mobile = $data_update['cus_mobile'];
            switch ($app_status) {
                case 'change':
                    $_app = true;
                    if($hd_call_status_type == 'appointment'){
                        $_app = $this->checkapp($id_center, $this->_id_center, $this->_id_department, $appointment, $id_frametime);
                    }

                    if($_app){
                        //$this->db->where('id', $id);
                        $this->db->where('cus_mobile', $cus_mobile);
                        $this->db->update('tbl_appointments', array('last_app' => 'off'));

                        $old_status = $data_update['app_status'];
                        $old_id_center = $data_update['id_center'];
                        $old_id_frametime = $data_update['id_frametime'];
                        $old_app_date = $data_update['app_date'];

                        $data_update['id_city'] = $id_center_city;
                        $data_update['id_center'] = $id_center;
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
							$this->db->where('mobile', $cus_mobile);
                            $this->db->update('tbl_customer', array('app' => 1));
                            # tang lich moi
                            $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`+1) WHERE `id_center`='.$id_center.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');

                            $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`+1) WHERE `id_center_spa`='.$id_center.' AND `id_center_call`='.$this->_id_center.' AND `id_department`='.$this->_id_department.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');
                        }else{
                            # tru lich cu
                            $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`-1) WHERE `id_center`='.$old_id_center.' AND `id_frametime`='.$old_id_frametime.' AND `date`="'.$old_app_date.'"');

                            $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`-1) WHERE `id_center_spa`='.$old_id_center.' AND `id_center_call`='.$this->_id_center.' AND `id_department`='.$this->_id_department.' AND `id_frametime`='.$old_id_frametime.' AND `date`="'.$old_app_date.'"');

                            # tang lich moi
                            $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`+1) WHERE `id_center`='.$id_center.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');

                            $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`+1) WHERE `id_center_spa`='.$id_center.' AND `id_center_call`='.$this->_id_center.' AND `id_department`='.$this->_id_department.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');
                        }
                    }else{
                        $error = 'Giới hạn lịch hẹn đã hết';
                        $data['error'] = $error;
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

                    $data_update['app_content'] = $content_app;
                    $data_update['app_status'] = 'cancel';
                    $data_update['crm_status'] = 'local';
                    $data_update['app_created_at'] = date('Y-m-d H:i:s');
                    $data_update['last_by_updated'] = $this->_uid;

                    $this->db->insert('tbl_appointments', $data_update);

                    $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`-1) WHERE `id_center`='.$old_id_center.' AND `id_frametime`='.$old_id_frametime.' AND `date`="'.$old_app_date.'"');

                    $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`-1) WHERE `id_center_spa`='.$old_id_center.' AND `id_center_call`='.$this->_id_center.' AND `id_department`='.$this->_id_department.' AND `id_frametime`='.$old_id_frametime.' AND `date`="'.$old_app_date.'"');
                    break;
                
                default:
                    # code...
                    break;
            }

            if( !isset($error) OR empty($error) ){
                redirect(base_url().'group/call/appointment');
            }
        }

        if( !isset($appointmentdt) OR empty($appointmentdt) ){
            $this->db->select('id,CONCAT(cus_first_name, " ", cus_last_name) as fullname,cus_mobile,id_center,id_frametime,app_date,app_time,app_content,CONCAT(agent_first_name, " ", agent_last_name) as agentname,agent_ext,id_department,id_group');
            $this->db->where('id', $id);
            $appointmentdt = $this->db->get('tbl_appointments')->row_array();

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
        $data['content'] = 'callappointment';
        $this->setlayout($data, 'v2/'.$this->_role);
    }
}