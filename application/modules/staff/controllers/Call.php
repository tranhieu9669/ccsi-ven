<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Call extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách gọi', ''),
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
            $id_status = isset($request['id_status']) ? $request['id_status'] : false;
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            $enddate = $startdate;
            #$enddate = date('Y-m-d', strtotime($startdate . ' + 5 days'));

            $db_staff = $this->setdbconnect($this->_id_agent);
            $db_staff->start_cache();

            $db_staff->select('cus.id,cus.id_link,CONCAT(cus.fullname," - ",cus.mobile) as customer,call.id_call_status,call.id_call_status_c1,call.id_call_status_c2,call.content_call,call.callback,call.is_callback,call.appointment,call.created_at');

            $db_staff->from('tbl_call_detail as call');
            $db_staff->join('tbl_customer as cus', 'cus.id_link=call.id_cus');
            $db_staff->where('cus.status !=', 'unassign');
            $db_staff->where('call.id_call_status > 0');
            $db_staff->where('call.agent_ext', $this->_ext);
            $db_staff->where('cus.end_ext', $this->_ext);

            if($inputsearch){
                $db_staff->where('(cus.mobile like "%'.$inputsearch.'%" OR cus.fullname like "%'.$inputsearch.'%")');
            }else{
                if($id_status){
                    $exid = explode('-', $id_status);

                    switch (count($exid)) {
                        case 1:
                            $db_staff->where('call.id_call_status', intval($exid[0]));
                            break;

                        case 2:
                            $db_staff->where('call.id_call_status', intval($exid[0]));
                            $db_staff->where('call.id_call_status_c1', intval($exid[1]));
                            break;

                        case 3:
                            $db_staff->where('call.id_call_status', intval($exid[0]));
                            $db_staff->where('call.id_call_status_c1', intval($exid[1]));
                            $db_staff->where('call.id_call_status_c2', intval($exid[2]));
                            break;
                         
                        default:
                            # code...
                            break;
                     }
                }

                if($startdate){
                    $db_staff->where('call.created_at > ', $startdate . ' 07:00:00');
                    $db_staff->where('call.created_at < ', $enddate . ' 22:00:00');
                }
            }

            $db_staff->stop_cache();
            $total = $db_staff->count_all_results();

            $db_staff->limit($limit, $offset);
            $dataResult = $db_staff->get()->result_array();

            $db_staff->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'query' => $db_staff->last_query(),
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 50,
            '_wth_customer' => 300,
            '_wth_status'   => 165,
            '_wth_time'     => 160,
            '_wth_action'   => 60,
        ));

        #status call
        $tbl_call_status = array();
        $this->db->select('id,name,type');
        $this->db->where('status', 'on');
        $tbl_call_status = $this->db->get('tbl_call_status')->result_array();

        $tbl_call_status_child_c1 = array();
        $this->db->select('id,name,type,id_call_status');
        $this->db->where('status', 'on');
        $tbl_call_status_child_c1 = $this->db->get('tbl_call_status_child_c1')->result_array();

        $tbl_call_status_child_c2 = array();
        $this->db->select('id,name,type,id_call_status,id_call_status_c1');
        $this->db->where('status', 'on');
        $tbl_call_status_child_c2 = $this->db->get('tbl_call_status_child_c2')->result_array();

        $status_call = array();

        foreach ($tbl_call_status as $key => $value) {
            $id = $value['id'];
            $name = $value['name'];
            $type = $value['type'];

            if(isset($type) AND !empty($type)){
                $status_call[] = array(
                    'id' => $id,
                    'name' => $name,
                    'type' => $type
                );
            }else{
                $status_call[] = array(
                    'id' => $id,
                    'name' => $name
                );
            }

            foreach ($tbl_call_status_child_c1 as $key1 => $value1) {
                $id1 = $value1['id'];
                $name = $value1['name'];
                $type1 = $value1['type'];
                $id_call_status = $value1['id_call_status'];

                if($id_call_status == $id){
                    if( (!isset($type) OR empty($type)) AND (isset($type1) AND !empty($type1)) ){
                        $status_call[] = array(
                            'id' => $id.'-'.$id1,
                            'name' => ' --- '.$name,
                            'type' => $type1
                        );
                    }else{
                        $status_call[] = array(
                            'id' => $id.'-'.$id1,
                            'name' => ' --- '.$name
                        );
                    }

                    unset($tbl_call_status_child_c1[$key1]);

                    foreach ($tbl_call_status_child_c2 as $key2 => $value2) {
                        $id2 = $value2['id'];
                        $name = $value2['name'];
                        $type2 = $value2['type'];
                        $id_call_status = $value2['id_call_status'];
                        $id_call_status_c1 = $value2['id_call_status_c1'];

                        if($id_call_status == $id AND $id_call_status_c1 == $id1){
                            if( (!isset($type) OR empty($type)) AND (!isset($type1) OR empty($type1)) AND (isset($type2) AND !empty($type2)) ){
                                $status_call[] = array(
                                    'id' => $id.'-'.$id1.'-'.$id2,
                                    'name' => ' --- --- '.$name,
                                    'type' => $type2
                                );
                            }else{
                                $status_call[] = array(
                                    'id' => $id.'-'.$id1.'-'.$id2,
                                    'name' => ' --- --- '.$name
                                );
                            }
                        }
                    }
                }
            }
        }

        $data['status_call'] = $status_call;

        $status_grid = array();
        foreach ($status_call as $key => $value) {
            $status_grid[$value['id']] = $value['name'];
        }
        $data['status_grid'] = json_encode($status_grid);
        #var_dump($status_grid);
        $data['content'] = 'call/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function callback(){
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

            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            $enddate = $startdate;
            #$enddate = date('Y-m-d', strtotime($startdate . ' + 5 days'));

            $db_staff = $this->setdbconnect($this->_id_agent);
            $db_staff->start_cache();

            $db_staff->select('cus.id,cus.id_link,CONCAT(cus.fullname," - ",cus.mobile) as customer,call.id_call_status,call.id_call_status_c1,call.id_call_status_c2,call.content_call,call.created_at,call.callback');

            $db_staff->from('tbl_call_detail_last as call');
            $db_staff->join('tbl_customer as cus', 'cus.id_link=call.id_cus');
            $db_staff->where('call.callback is not null');
            $db_staff->where('cus.status !=', 'unassign');
            $db_staff->where('call.is_callback', 0);
            $db_staff->where('call.id_agent', $this->_id_agent);
            $db_staff->where('call.agent_ext', $this->_ext);
            $db_staff->where('cus.end_ext', $this->_ext);

            if($inputsearch){
                $db_staff->where('(cus.mobile like "%'.$inputsearch.'%" OR cus.fullname like "%'.$inputsearch.'%")');
            }else{
                if($startdate){
                    $db_staff->where('call.callback > ', $startdate . ' 07:00:00');
                    $db_staff->where('call.callback < ', $enddate . ' 22:00:00');
                }
            }

            $db_staff->stop_cache();
            $total = $db_staff->count_all_results();

            $db_staff->limit($limit, $offset);
            $dataResult = $db_staff->get()->result_array();

            $db_staff->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'query' => $db_staff->last_query(),
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 50,
            '_wth_customer' => 300,
            '_wth_status'   => 165,
            '_wth_time'     => 160,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'call/callback';
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

            $this->db->select('app.id, app.cus_mobile, app.cus_first_name, app.cus_last_name, cen.name, app.id_center, app.app_datetime, app.agent_ext, app.sms_status, app.app_status,app.crm_status, app.crm_app_status, app.crm_app_content');
            $this->db->from('tbl_appointments as app');
            $this->db->join('tbl_centers as cen', 'cen.id=app.id_center and cen.type="spa"');
            $this->db->where('app.id_agent', $this->_id_agent);
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

        $data['content'] = 'call/appointment';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function nostatus(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách gọi không lưu log', ''),
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
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');

            $db_staff = $this->setdbconnect($this->_id_agent);
            $db_staff->start_cache();

            $db_staff->select('cus.id,cus.id_link,CONCAT(cus.fullname," - ",cus.mobile) as customer,call.id_call_status,call.id_call_status_c1,call.id_call_status_c2,call.content_call,call.created_at');

            //call.call_type,call.callback,call.appointment
            $db_staff->from('tbl_call_detail_last as call');
            $db_staff->join('tbl_customer as cus', 'cus.id_link=call.id_cus');
            $db_staff->where('call.id_call_status is null');
            $db_staff->where('call.agent_ext', $this->_ext);
            $db_staff->where('cus.end_ext', $this->_ext);
            $db_staff->where('cus.status', 'call');
            //$db_staff->where('cus.status !=', 'unassign');

            if($inputsearch){
                $db_staff->where('(cus.mobile like "%'.$inputsearch.'%" OR cus.fullname like "%'.$inputsearch.'%")');
            }else{
                if($startdate){
                    $db_staff->where('call.created_at > ', $startdate . ' 07:00:00');
                    $db_staff->where('call.created_at < ', $startdate . ' 22:00:00');
                }
            }

            $db_staff->stop_cache();
            $total = $db_staff->count_all_results();

            $db_staff->limit($limit, $offset);
            $dataResult = $db_staff->get()->result_array();

            $db_staff->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'query' => $db_staff->last_query(),
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 50,
            '_wth_customer' => 300,
            '_wth_status'   => 165,
            '_wth_time'     => 160,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'call/nostatus';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function introduce(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách giới thiệu', ''),
        );

        $data['content'] = 'call/introduce';
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

            $this->db->select('app_code,id_cus,demo6t,cus_mobile,cus_first_name,cus_last_name,cus_source,id_call,id_city,id_center,id_frametime,app_datetime,app_date,app_time,app_content,id_center_call,id_department,id_group,id_agent,agent_ext,agent_mobile,agent_first_name,agent_last_name,sms_time_send,sms_status,sms_content,app_created_at,app_status,crm_status');
            $this->db->where('id', $id);
            $data_update = $this->db->get('tbl_appointments')->row_array();

            $cus_mobile = $data_update['cus_mobile'];
            switch ($app_status) {
                case 'change':
                    #############################################
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
                        $demo6t = $data_update['demo6t'];

                        $data_update['id_city'] = $id_center_city;
                        $data_update['demo6t'] = $demo6t;
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

                    # tru lich
                    $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`-1) WHERE `id_center`='.$old_id_center.' AND `id_frametime`='.$old_id_frametime.' AND `date`="'.$old_app_date.'"');

                    $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`-1) WHERE `id_center_spa`='.$old_id_center.' AND `id_center_call`='.$this->_id_center.' AND `id_department`='.$this->_id_department.' AND `id_frametime`='.$old_id_frametime.' AND `date`="'.$old_app_date.'"');
                    break;
                
                default:
                    # code...
                    break;
            }

            if( !isset($error) OR empty($error) ){
                redirect(base_url().'staff/call/appointment');
            }
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
        $data['content'] = 'callappointment';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function misscall(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách gọi nhỡ', ''),
        );

        if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 0;

            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;

            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            $enddate = isset($request['enddate']) ? $request['enddate'] : date('Y-m-d');

            $this->db->start_cache();
            $this->db->select('calldate,mobile');
            $this->db->from('tbl_misscall');
            $this->db->where('ext', $this->_ext);

            if($inputsearch){
                $this->db->where('mobile like "%'.$inputsearch.'%"');
            }else{
                if($startdate){
                    $this->db->where('calldate > ', $startdate . ' 00:00:00');
                    $this->db->where('calldate < ', $enddate . ' 23:59:59');
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
            '_wth_order'    => 50,
            '_wth_mobile' => 300,
        ));

        $data['content'] = 'call/misscall';
        $this->setlayout($data, 'v2/'.$this->_role);
    }
}
