<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Staff extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function __destruct() {
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url() . $this->_role),
            array('Danh sách cuộc gọi', ''),
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

        $db_staff = $this->setdbconnect($this->_id_agent);
        for($day=6;$day>=0;$day--){
            $datecheck = date('Y-m-d', strtotime($datenow . ' - '.$day.' days'));
            array_push($categories, date('d-m', strtotime($datecheck)));
            foreach ($statuscall as $key => $value) {
                $dataReport[$datecheck][$value['id']] = 0;
            }

            $dataResult = array();
            $db_staff->select('id_status, countcall');
            $db_staff->where('datecall', $datecheck);
            $db_staff->where('agent_id', $this->_id_agent);
            $dataResult = $db_staff->get('tbl_call_report')->result_array();

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
        }else{
            $majorUnit = 5;
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

        $data['ishome'] = true;

        $data['content'] = 'index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    # Danh sach chua goi
    function listcall(){
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
            $startdate = isset($request['startdate']) ? $request['startdate'] : false;

            $db_staff = $this->setdbconnect($this->_id_agent);
            $db_staff->start_cache();

            $db_staff->select('id,fullname,mobile,gender,start_date');

            $db_staff->from('tbl_customer');
            $db_staff->where_in('status', array('assign','new'));
            $db_staff->where('end_ext', $this->_ext);

            if($inputsearch){
                $db_staff->where('(mobile like "%'.$inputsearch.'%" OR fullname like "%'.$inputsearch.'%")');
            }
            if($startdate){
                $_SESSION['startdate'] = $startdate;
                $db_staff->where('created_at >', $startdate.' 00:00:01');
                $db_staff->where('created_at <', $startdate.' 23:59:59');
            }else{
                $_SESSION['startdate'] = '';
            }
			$db_staff->order_by('created_at', 'desc');

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
            '_wth_gender'   => 85,
            '_wth_mobile'   => 100,
            '_wth_date'     => 120,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'listcall';
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

    # new call
    function startcase($id=false){
        $data['breadcrumb'] = array(
            array('Home', base_url() . $this->_role),
            array('Thông tin cuộc gọi', ''),
        );

        $error = '';

        $db_staff = $this->setdbconnect($this->_id_agent);

        $customerdt = array();
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $customerdt = $_POST;

            $id_call = $this->input->post('id_call');
            $id_fileup = $this->input->post('id_fileup');
            $id_link = $this->input->post('id_link');
            $fullname = $this->input->post('fullname');
            $expname = explode(' ', $fullname);
            $last_name = $expname[count($expname) - 1];
            $first_name = trim(str_replace($last_name, '', $fullname));

            $gender = $this->input->post('gender');
            $mobile = $this->input->post('mobile');
            $address = $this->input->post('address');
            $birthday = $this->input->post('birthday');
            $age = $this->input->post('age');
            $id_city = $this->input->post('id_city');
            $id_district = $this->input->post('id_district');
            $hd_call_status_type = $this->input->post('hd_call_status_type');
            $id_call_status = $this->input->post('id_call_status');
            $id_call_status_c1 = $this->input->post('id_call_status_c1');
            $id_call_status_c2 = $this->input->post('id_call_status_c2');            
            $content_call = $this->input->post('content_call');
            # lich hen
            $id_center_city = $this->input->post('id_center_city');
            $id_center = $this->input->post('id_center');
            $id_frametime = $this->input->post('id_frametime');
            $appointment = $this->input->post('appointment');
            $timeapp = $this->input->post('hd_frametime_start');
            $content_app = $this->input->post('content_app');
            # callback
            $dateforcus = $this->input->post('dateforcus');
            $timeforcus = $this->input->post('timeforcus');
            # call detail
            $priority_level = $this->input->post('priority_level');

            ##########################################
            $_app = true;
            if($hd_call_status_type == 'appointment'){
                $_app = $this->checkapp($id_center, $this->_id_center, $this->_id_department, $appointment, $id_frametime);
            }

            if($_app){
                $customer_update = array(
                    'fullname' => $fullname,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'age' => $age,
                    'gender' => $gender,
                    'address' => $address,
                    'id_city' => $id_city,
                    'id_district' => $id_district,
                    'callval' => 1,
                    'end_time' => date('Y-m-d H:i:s'),
                    'id_call_status' => $id_call_status,
                    'id_call_status_c1' => $id_call_status_c1,
                    'id_call_status_c2' => $id_call_status_c2,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'last_updated_by' => $this->_uid,
                    'close_date' => date('Y-m-d'),
                );

                if($hd_call_status_type == 'appointment'){
                    $customer_update['appointment'] = $appointment.' '.$timeapp.':00';
                }elseif($hd_call_status_type == 'callback'){
                    $customer_update['callback'] = $dateforcus . ' ' . $timeforcus . ':00';
                }

                #update customer
                if( isset($customer_update) AND !empty($customer_update) ){
                    //$db_staff->where('id_link', $id_link);
                    $db_staff->where('mobile', $mobile);
                    $db_staff->update('tbl_customer', $customer_update);

                    $db_center = $this->setdbconnect($this->_id_center, 'center');
                    //$db_center->where('id', $id_link);
                    $db_center->where('mobile', $mobile);
                    $db_center->update('tbl_customer', $customer_update);

                    $this->db->where('mobile', $mobile);
                    $this->db->update('tbl_customer', array(
                        'id_call_status' => $id_call_status,
                        'id_call_status_c1' => $id_call_status_c1,
                        'id_call_status_c2' => $id_call_status_c2,
                    ));
                    #update call detail
                    $call_detail_update = array(
                        'id_call_status' => $id_call_status,
                        'id_call_status_c1' => $id_call_status_c1,
                        'id_call_status_c2' => $id_call_status_c2,
                        'content_call' => $content_call,
                        'priority_level' => $priority_level,
                        'updated_at' => date('Y-m-d H:i:s'),
                    );

                    if($hd_call_status_type == 'appointment'){
                        $call_detail_update['appointment'] = $appointment.' '.$timeapp.':00';
                    }elseif($hd_call_status_type == 'callback'){
                        $call_detail_update['callback'] = $dateforcus . ' ' . $timeforcus . ':00';
                    }elseif($hd_call_status_type == 'close'){

                    }

                    if(isset($call_detail_update) AND !empty($call_detail_update)){
                        $db_staff->where('id', $id_call);
                        $db_staff->update('tbl_call_detail', $call_detail_update);

                        $db_staff->where('id_cus', $id_link);
                        $db_staff->update('tbl_call_detail_last', $call_detail_update);

                        # update appointment
                        if($hd_call_status_type == 'appointment'){#tbl_appointments
                            $app_insert = array(
                                'app_code' => 'LH-'.$this->_ext.'-'.date('YmdHis'),
                                'id_cus' => $id_link,
                                'cus_mobile' => $mobile,
                                'cus_first_name' => $first_name,
                                'cus_last_name' => $last_name,
                                'id_call' => $id_call,
                                'id_city' => $id_center_city,
                                'id_center' => $id_center,
                                'id_frametime' => $id_frametime,
                                'app_datetime' => $appointment.' '.$timeapp.':00',
                                'app_date' => $appointment,
                                'app_time' => $timeapp,
                                'app_content' => $content_app,
                                'id_center_call' => $this->_id_center,
                                'id_department' => $this->_id_department,
                                'id_group' => $this->_id_group,
                                'id_agent' => $this->_id_agent,
                                'agent_ext' => $this->_ext,
                                'agent_mobile' => $this->_mobile,
                                'agent_first_name' => $this->_agent_fname,
                                'agent_last_name' => $this->_agent_lname,
                                'sms_status' => 'new',
                                'app_created_at' => date('Y-m-d H:i:s'),
                                'app_status' => 'new',
                            );

                            if($id_fileup == 99999){
                                $app_insert['demo6t'] = 1;
                            }

                            $app_created_check = date('Y-m-d H:i:s', strtotime($StartTime . ' -10 minutes'));
                            $this->db->select('id');
                            $this->db->where('cus_mobile', $mobile);
                            $this->db->where('last_app', 'on');
							$this->db->where('app_status !=', 'cancel');
                            //$this->db->where('app_created_at >', $app_created_check);
                            $check_app = $this->db->get('tbl_appointments')->row_array();

                            if( isset($check_app) AND !empty($check_app) ){
                                if($id){
                                    redirect(base_url().'staff/list');
                                }else{
                                    redirect(base_url().'staff/startcase');
                                }
                            }else{
                                $this->db->where('cus_mobile', $mobile);
                                $this->db->update('tbl_appointments', array('last_app' => 'off'));
                                $this->db->insert('tbl_appointments', $app_insert);
								
								$this->db->where('mobile', $mobile);
                                $this->db->update('tbl_customer', array('app' => 1));

                                $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`+1) WHERE `id_center`='.$id_center.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');

                                $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`+1) WHERE `id_center_spa`='.$id_center.' AND `id_center_call`='.$this->_id_center.' AND `id_department`='.$this->_id_department.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');
                            }
                            # CALL CRM
                        }
                        $check_call_report = array();
                        $db_staff->select('id');
                        $db_staff->where('datecall', date('Y-m-d'));
                        $db_staff->where('agent_id', $this->_id_agent);
                        $db_staff->where('id_status', $id_call_status);
                        $check_call_report = $db_staff->get('tbl_call_report')->row_array();

                        if( isset($check_call_report['id']) AND !empty($check_call_report['id']) ){
                            $db_staff->query("UPDATE `tbl_call_report` SET `countcall`=(`countcall` + 1),`updated_at`='".date('Y-m-d H:i:s')."' WHERE `datecall`='".date('Y-m-d')."' AND `agent_id`=".$this->_id_agent." AND `id_status`=".$id_call_status);
                        }else{
                            $db_call_report = array(
                                'datecall' => date('Y-m-d'),
                                'agent_id' => $this->_id_agent,
                                'agent_ext' => $this->_ext,
                                'id_status' => $id_call_status,
                                'countcall' => 1,
                                'updated_at' => date('Y-m-d H:i:s'),
                            );

                            $db_staff->insert('tbl_call_report', $db_call_report);
                        }
                    }
                }
                # redirect
                #redirect(base_url().'staff');
                if($this->_autodial){
                    redirect(base_url().'staff');
                }else{
                    if($id){
                        redirect(base_url().'staff/list');
                    }else{
                        redirect(base_url().'staff/startcase');
                    }
                }
            }else{
                $error = 'Giới hạn lịch hẹn đã hết';
                $data['error'] = $error;
            }
            #############################
        }

        $id_call = 0;
        # Thông tin khách hàng
        if( !isset($customerdt) OR empty($customerdt) ){
            /*if(!$id AND $this->_autodial){
                // kiem tra cuoc goi lai
                $check_calback = array();
                $db_staff->select('id');
                $db_staff->where('id_call_status', 6);
                $db_staff->where('id_agent', $this->_id_agent);
                //$db_staff->where('end_ext', $this->_ext);
                $db_staff->where('callback >', date('Y-m-d H:i:s'));
                $db_staff->order_by('callback', 'asc');
                $db_staff->limit(1);
                $check_calback = $db_staff->get('tbl_customer')->row_array();

                if(isset($check_calback) AND !empty($check_calback)){
                    $id = $check_calback['id'];
                }
            }*/
            // get thong tin khach hang
            $db_staff->select('id,id_link,id_fileup,fullname,mobile,email,birthday,age,gender,address,id_city,id_district,source,start_ext,end_ext,start_date,close_date');
            $db_staff->where_in('status', array('assign', 'new'));
            $db_staff->where('end_ext="'.$this->_ext.'"');
            if($id){
                $db_staff->where('id', $id);
            }else{
                $db_staff->order_by('rand()');
                $db_staff->limit(1);
            }
            $customerdt = $db_staff->get('tbl_customer')->row_array();

            if(isset($customerdt) AND !empty($customerdt)){
                # kiem tra so co lich hen chua
                $_mobile = $customerdt['mobile'];
                $id = $customerdt['id'];
                $id_link = $customerdt['id_link'];
                # update called -- call
                $db_staff->where('id', $id);
                $db_staff->where('id_link', $id_link);
                $db_staff->update('tbl_customer', array(
                    'status' => 'call',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'last_updated_by' => $this->_uid
                ));

                $insert_detail = array(
                    'uniqid' => '',
                    'id_cus' => $id_link,
                    'mobile' => $_mobile,
                    'fullname' => $customerdt['fullname'],
                    'agent_ext' => $this->_ext,
                    'id_center' => $this->_id_center,
                    'id_department' => $this->_id_department,
                    'id_group' => $this->_id_group,
                    'id_agent' => $this->_id_agent,
                    'call_type' => 'new',
                    'created_at' => date('Y-m-d H:i:s'),
                    'start_date' => $customerdt['start_date'],
                    'close_date' => $customerdt['close_date'],
                );
                # insert call detail
                $db_staff->insert('tbl_call_detail', $insert_detail);
                $id_call = $db_staff->insert_id();
                $customerdt['id_call'] = $id_call;
                # insert call detail last
                $db_staff->insert('tbl_call_detail_last', $insert_detail);
                # check lich hen
                $this->db->select('app_status');
                $this->db->where('cus_mobile', $_mobile);
                $this->db->where('last_app', 'on');
                $this->db->order_by('id', 'desc');
                $app_check = $this->db->get('tbl_appointments')->row_array();
                if( isset($app_check) AND !empty($app_check) ){
                    $_app_status = $app_check['app_status'];
                    if($_app_status != 'cancel'){
                        $data['msg_app_status'] = 'Khách hàng đang có lịch và tạm thời bị khóa, liên hệ kiểm tra';

                        # them moi vao bang check
                        $this->db->insert('tbl_app_check', array(
                            'id_center' => $this->_id_center,
                            'id_department' => $this->_id_department,
                            'id_group' => $this->_id_group,
                            'id_agent' => $this->_id_agent,
                            'ext' => $this->_ext,
                            'mobile' => $_mobile,
                            'created_at' => date('Y-m-d H:i:s')
                        ));
                        # khoa ban ghi khach hang
                        $db_staff->where('id', $id);
                        $db_staff->update('tbl_customer', array('status' => 'close'));
                    }
                }
            }
        }
        $data['customerdt'] = $customerdt;

        # Thông tin Tỉnh/Thành phố
        $city = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $city = $this->db->get('tbl_city')->result_array();
        $data['city'] = $city;

        $district = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $district = $this->db->get('tbl_district')->result_array();
        $data['district'] = $district;

        $call_status = array();
        $this->db->select('id,name,type');
        $this->db->where('status', 'on');
        $call_status = $this->db->get('tbl_call_status')->result_array();
        $data['call_status'] = $call_status;

        ##################
        $data["limit"] = $this->grid_limit;
        $data['column_introduced'] = json_encode(array(
            '_wth_order'        => 60,
            '_wth_mobile'       => 150,
            '_wth_relationship' => 150,
            '_wth_status'       => 180,
        ));

        if($this->_autodial){
            $sipname = $this->_sipname;
            if($sipname == 'deaura'){
                $sipserver = $this->_sipserver;
                $urlob = 'http://'.$sipserver.'/ob.php';
                if( $customerdt['mobile'] ){
                    if(1){
                        $url_call = $urlob . '?txtextension=' . $this->_ext . '&txtphonenumber=' . $customerdt['mobile'];
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL , $url_call);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
                        $result = curl_exec($ch);
                        curl_close($ch);
                    }
                    //$rtn = 'SUCCESS';
                    echo "<script> console.log('call success'); </script>";
                }else{
                    //$rtn = 'FAIL-Kết nối tổng đài lỗi.';
                    echo "<script> console.log('call error'); </script>";
                }
            }
        }

        $data['content'] = 'startcase';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function introduced($id=false){
        if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 0;

            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;

            //$inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;

            $db_staff = $this->setdbconnect($this->_id_agent);
            $db_staff->start_cache();

            $db_staff->select('full_name,mobile,id_relationship,call_status');

            $db_staff->from('tbl_customer_introduc');
            $db_staff->where('id_customer', $id);

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
    }

    function check_dup(){
        $mobile = $this->input->post('mobile');
        if(isset($mobile) AND !empty($mobile)){
            $mobile = !mb_detect_encoding($mobile, 'UTF-8', TRUE) ? utf8_encode($mobile) : $mobile;
            $mobile = preg_replace("/[^0-9]/", "", $mobile);
            if( substr($mobile, 0, 1) != '0' ){
                $mobile = '0'.$mobile;
            }

            if( (strlen($mobile) > 9 AND strlen($mobile) < 12) OR ( (substr($mobile, 0, 2) != '01') AND strlen($mobile) > 10 ) ){
                $this->db->select('id');
                $this->db->where('mobile', $mobile);
                $detail = $this->db->get('tbl_customer')->row_array();
                if( isset($detail['id']) AND !empty($detail['id']) ){
                    $this->form_validation->set_message('check_dup', '<b>%s</b> đã có thông tin trong hệ thống.');
                    return FALSE;
                }else{
                    return TRUE;
                }
            }else{
                $this->form_validation->set_message('check_dup', '<b>%s</b> không đúng.');
                    return FALSE;
            }
        }else{
            $this->form_validation->set_message('check_dup', '<b>%s</b> không để trống.');
                return FALSE;
        }
    }

    function introducedetail($id=false){
        $detail = array();
        $msg_success = '';
        $msg_error = '';

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $validation = array(
                array(
                    'field' => 'full_name',
                    'label' => 'Password Call',
                    'rules' => 'required|max_length[75]',
                ),
                array(
                    'field' => 'mobile',
                    'label' => 'Mobile',
                    'rules' => 'required|max_length[25]',
                )
            );

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( ($this->form_validation->run($this) !== FALSE) AND intval($id) > 0 ){
                $first_name         = '';
                $last_name          = '';
                $full_name          = $this->input->post('full_name');
                $explode_name       = explode(' ', $full_name);
                $last_name          = $explode_name[count($explode_name) - 1];
                $first_name         = str_replace($last_name, '', $full_name);
                $mobile             = $this->input->post('mobile');
                $mobile = !mb_detect_encoding($mobile, 'UTF-8', TRUE) ? utf8_encode($mobile) : $mobile;
                $mobile = preg_replace("/[^0-9]/", "", $mobile);

                $mobile = convert_mobile($mobile);

                if( check_mobile($mobile) ){
                    $age                = $this->input->post('age');
                    $gender             = $this->input->post('gender');
                    $mkt                = $this->input->post('mkt');
                    if( ! isset($mkt) OR empty($mkt) ){
                        $mkt=0;
                    }
                    $mkt=0;
                    $address            = $this->input->post('address');
                    $id_relationship    = $this->input->post('id_relationship');

                    # check dup tong
                    $checkglobal = array();
                    $this->db->select('id,id_fileup,first_name,last_name,fullname,mobile,id_source,source,id_center,id_department,id_group,id_agent,focus,demo,sale,app,block');
                    $this->db->from('tbl_customer');
                    $this->db->where('mobile', $mobile);
                    $checkglobal = $this->db->get()->row_array();
					
					//var_dump($checkglobal);

                    if( !isset($checkglobal['id']) OR empty($checkglobal['id']) ){
                        $checkappointment = array();
                        $this->db->select('id');
                        $this->db->where('cus_mobile', $mobile);
                        $this->db->where('last_app', 'on');
                        $this->db->where('app_status !=', 'cancel');
                        $checkappointment = $this->db->get('tbl_appointments')->row_array();
                        if( !isset($checkappointment) or empty($checkappointment)){
                            $new_customer = array(
                                'first_name'=> $first_name,
                                'last_name' => $last_name,
                                'fullname'  => $full_name,
                                'mobile'    => $mobile,
                                'gender'    => $gender,
                                'id_department' => $this->_id_department,
                                'id_group' => $this->_id_group,
                                'id_agent' => $this->_id_agent,
                                'start_ext' => $this->_ext,
                                'start_time' => date('Y-m-d H:i:s'),
                                'end_ext' => $this->_ext,
                                'end_time' => date('Y-m-d H:i:s'),
                                'status' => 'assign',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => $this->_uid,
                                'start_date' => date('Y-m-d'),
                                'close_date' => date('Y-m-d'),
                                'focus' => null,
                                'last_time_focus' => date('Y-m-d H:i:s'),
                            );

                            if( isset($address) AND !empty($address) ){
                                $new_customer['address'] = $address;
                            }

                            # insert dbcenter
                            $source_data = array(
                                'first_name'=> $first_name,
                                'last_name' => $last_name,
                                'fullname'  => $full_name,
                                'mobile'    => $mobile,
                                'id_center' => $this->_id_center,
                                'id_department' => $this->_id_department,
                                'id_group' => $this->_id_group,
                                'id_agent' => $this->_id_agent,
                                'status' => 'assign',
                                'created_at'=> date('Y-m-d H:i:s'),
                                'focus' => null,
                                'last_time_focus' => date('Y-m-d H:i:s'),
                            );

                            $this->db->insert('tbl_customer', $source_data);
                            $id_link = $this->db->insert_id();
                            
                            $new_customer['id_link'] = $id_link;
                            $db_center = $this->setdbconnect($this->_id_center, 'center');
                            $db_center->insert('tbl_customer', $new_customer);

                            $new_introduc = array(
                                'id_customer'           => $id,
                                'id_customer_introduc'  => $id_link,
                                'full_name'             => $full_name,
                                'mobile'                => $mobile,
                                'id_relationship'       => $id_relationship,
                            );
                            $db_center->insert('tbl_customer_introduc', $new_introduc);

                            # insert dbstaff
                            $db_staff = $this->setdbconnect($this->_id_agent);
                            
                            unset($new_customer["id_department"]);
                            unset($new_customer["id_group"]);
                            unset($new_customer["focus"]);
                            unset($new_customer["last_time_focus"]);

                            $new_customer["id_link"] = $id_link;
                            # them moi khach hang
                            $db_staff->insert('tbl_customer', $new_customer);
                            $db_staff->insert('tbl_customer_introduc', $new_introduc);

                            $msg_success    = 'Cập nhật thông tin thành công';
                        }else{
                            $msg_error = 'Khách hàng đã có hẹn';
                        }
                    }else{
                        if(isset($checkglobal['sale']) AND !empty($checkglobal['sale'])){
                            $msg_error = 'Khách hàng đã mua sản phẩm';
                        }elseif(isset($checkglobal['demo']) AND !empty($checkglobal['demo'])){
                            $msg_error = 'Khách hàng đã đến demo';
                        }elseif(isset($checkglobal['app']) AND !empty($checkglobal['app'])){
                            $msg_error = 'Khách hàng đã có hẹn';
                        }elseif( isset($checkglobal['block']) AND $checkglobal['block'] > 0 ){
                            $msg_error = 'Khách hàng bị block';
                        }elseif( isset($checkglobal['id_group']) AND !empty($checkglobal['id_group']) AND $checkglobal['id_group']==54 AND $this->_id_group != 54 ){
                            $msg_error = 'Khách hàng Commandos';
                        }elseif( $this->_id_group != 54 AND in_array($checkglobal['focus'], array('mkt', 'pg')) ){
                            $msg_error = 'Khách hàng Commandos';
                        }else{
                            $id = $checkglobal['id'];
                            $mobile = $checkglobal['mobile'];
                            $id_source = $checkglobal['id_source'];
                            $source = $checkglobal['source'];
                            $id_center = $checkglobal['id_center'];
                            $id_department = $checkglobal['id_department'];
                            $id_group = $checkglobal['id_group'];
                            $id_agent = $checkglobal['id_agent'];

                            if(isset($id_center) AND !empty($id_center)){
                                $_dbcenter = $this->setdbconnect($id_center, 'center');
                                if($_dbcenter){
                                    $_dbcenter->where('mobile', $mobile);
                                    $_dbcenter->delete('tbl_customer');
                                }
                            }

                            if(isset($id_department) AND !empty($id_department) AND intval($id_department) > 90){
                                $dbAuto = $this->autodbconnect();
                                if($dbAuto){
                                    $dbAuto->where('mobile', $mobile);
                                    $dbAuto->delete('tbl_customer');
                                }
                            }else{
                                if(isset($id_department) AND !empty($id_department)){
                                    $_dbdepartment = $this->setdbconnect($id_department, 'department');
                                    if($_dbdepartment){
                                        $_dbdepartment->where('mobile', $mobile);
                                        $_dbdepartment->delete('tbl_customer');
                                    }
                                }

                                if(isset($id_group) AND !empty($id_group)){
                                    $_dbgroup = $this->setdbconnect($id_group, 'group');
                                    if($_dbgroup){
                                        $_dbgroup->where('mobile', $mobile);
                                        $_dbgroup->delete('tbl_customer');
                                    }
                                }
                            }

                            $source_data = array(
                                'first_name'=> $first_name,
                                'last_name' => $last_name,
                                'fullname'  => $full_name,
                                #'source'    => $this->_uid,
                                'id_center' => $this->_id_center,
                                'id_department' => $this->_id_department,
                                'id_group' => $this->_id_group,
                                'id_agent' => $this->_id_agent,
                                'status' => 'assign',
                            );

                            if($this->_id_group != 54){
                                $source_data['focus'] = null;
                                $source_data['last_time_focus'] = date('Y-m-d H:i:s');
                            }
                            $this->db->where('id', $id);
                            $this->db->where('mobile', $mobile);
                            $this->db->update('tbl_customer', $source_data);

                            $new_customer = array(
                                'id_fileup' => $checkglobal['id_fileup'],
                                'first_name'=> $checkglobal['first_name'],
                                'last_name' => $checkglobal['last_name'],
                                'fullname'  => $checkglobal['fullname'],
                                'mobile'    => $checkglobal['mobile'],
                                'id_link' => $id,
                                'id_source' => $id_source,
                                'source' => $source,
                                'id_department' => $this->_id_department,
                                'id_group' => $this->_id_group,
                                'id_agent' => $this->_id_agent,
                                'start_ext' => $this->_ext,
                                'start_time' => date('Y-m-d H:i:s'),
                                'end_ext' => $this->_ext,
                                'end_time' => date('Y-m-d H:i:s'),
                                'status' => 'assign',
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => $this->_uid,
                                'start_date' => date('Y-m-d'),
                                'close_date' => date('Y-m-d'),
                            );
                            if($this->_id_group != 54){
                                $new_customer['focus'] = null;
                                $new_customer['last_time_focus'] = date('Y-m-d H:i:s');
                            }

                            $_db_center = $this->setdbconnect($this->_id_center, 'center');
                            $_db_center->insert('tbl_customer', $new_customer);
                            $_id_link = $_db_center->insert_id();

                            $new_introduc = array(
                                'id_customer'           => $id,
                                'id_customer_introduc'  => $_id_link,
                                'full_name'             => $full_name,
                                'mobile'                => $mobile,
                                'id_relationship'       => $id_relationship,
                            );
                            $_db_center->insert('tbl_customer_introduc', $new_introduc);

                            # insert dbstaff
                            $_db_staff = $this->setdbconnect($this->_id_agent);
                            
                            unset($new_customer["id_department"]);
                            unset($new_customer["id_group"]);
                            unset($new_customer["focus"]);
                            unset($new_customer["last_time_focus"]);
                            # them moi khach hang
                            $_db_staff->insert('tbl_customer', $new_customer);
                            $_db_staff->insert('tbl_customer_introduc', $new_introduc);

                            $msg_success    = 'Cập nhật thông tin thành công';
                        }
                    }
                }else{
                    $msg_error = 'Số điện thoại không đúng';
                }
            }
            $detail = $_POST;
        }
        $data['success'] = $msg_success;
        $data['error']   = $msg_error;
        $data['detail']  = $detail;
        $data['content'] = 'introduced';
        $this->setlayout($data, 'v2/dialog');
    }

    # callback
    function callback($id=false){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Gọi lại khách hàng', ''),
        );

        $error = '';

        $db_staff = $this->setdbconnect($this->_id_agent);

        $customerdt = array();
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $customerdt = $_POST;

            $id_call = $this->input->post('id_call');
            $id_fileup = $this->input->post('id_fileup');
            $id_link = $this->input->post('id_link');
            $fullname = $this->input->post('fullname');
            $expname = explode(' ', $fullname);
            $last_name = $expname[count($expname) - 1];
            $first_name = trim(str_replace($last_name, '', $fullname));

            $gender = $this->input->post('gender');
            $mobile = $this->input->post('mobile');
            $address = $this->input->post('address');
            $birthday = $this->input->post('birthday');
            $age = $this->input->post('age');
            $id_city = $this->input->post('id_city');
            $id_district = $this->input->post('id_district');
            $hd_call_status_type = $this->input->post('hd_call_status_type');
            $id_call_status = $this->input->post('id_call_status');
            $id_call_status_c1 = $this->input->post('id_call_status_c1');
            $id_call_status_c2 = $this->input->post('id_call_status_c2');            
            $content_call = $this->input->post('content_call');
            # lich hen
            $id_center_city = $this->input->post('id_center_city');
            $id_center = $this->input->post('id_center');
            $id_frametime = $this->input->post('id_frametime');
            $appointment = $this->input->post('appointment');
            $timeapp = $this->input->post('hd_frametime_start');
            $content_app = $this->input->post('content_app');
            # callback
            $dateforcus = $this->input->post('dateforcus');
            $timeforcus = $this->input->post('timeforcus');
            # call detail
            $priority_level = $this->input->post('priority_level');

            ########################################
            $_app = true;
            if($hd_call_status_type == 'appointment'){
                $_app = $this->checkapp($id_center, $this->_id_center, $this->_id_department, $appointment, $id_frametime);
            }

            if($_app){
                $customer_update = array(
                    'fullname' => $fullname,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'age' => $age,
                    'gender' => $gender,
                    'address' => $address,
                    'id_city' => $id_city,
                    'id_district' => $id_district,
                    #'callval' => 1,
                    'end_time' => date('Y-m-d H:i:s'),
                    'id_call_status' => $id_call_status,
                    'id_call_status_c1' => $id_call_status_c1,
                    'id_call_status_c2' => $id_call_status_c2,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'last_updated_by' => $this->_uid,
                    'close_date' => date('Y-m-d'),
                );

                if($hd_call_status_type == 'appointment'){
                    $customer_update['appointment'] = $appointment.' '.$timeapp.':00';
                }elseif($hd_call_status_type == 'callback'){
                    $customer_update['callback'] = $dateforcus . ' ' . $timeforcus . ':00';
                }

                #update customer
                if( isset($customer_update) AND !empty($customer_update) ){
                    //$db_staff->where('id_link', $id_link);
                    $db_staff->where('mobile', $mobile);
                    $db_staff->update('tbl_customer', $customer_update);

                    $db_center = $this->setdbconnect($this->_id_center, 'center');
                    //$db_center->where('id', $id_link);
                    $db_center->where('mobile', $mobile);
                    $db_center->update('tbl_customer', $customer_update);

                    $this->db->where('mobile', $mobile);
                    $this->db->update('tbl_customer', array(
                        'id_call_status' => $id_call_status,
                        'id_call_status_c1' => $id_call_status_c1,
                        'id_call_status_c2' => $id_call_status_c2,
                    ));
                    #update call detail
                    $call_detail_update = array(
                        'id_call_status' => $id_call_status,
                        'id_call_status_c1' => $id_call_status_c1,
                        'id_call_status_c2' => $id_call_status_c2,
                        'content_call' => $content_call,
                        'priority_level' => $priority_level,
                        'updated_at' => date('Y-m-d H:i:s'),
                    );

                    if($hd_call_status_type == 'appointment'){
                        $call_detail_update['appointment'] = $appointment.' '.$timeapp.':00';
                    }elseif($hd_call_status_type == 'callback'){
                        $call_detail_update['callback'] = $dateforcus . ' ' . $timeforcus . ':00';
                    }

                    if(isset($call_detail_update) AND !empty($call_detail_update)){
                        $db_staff->where('id', $id_call);
                        $db_staff->update('tbl_call_detail', $call_detail_update);

                        $db_staff->where('id_cus', $id_link);
                        $db_staff->update('tbl_call_detail_last', $call_detail_update);

                        # update appointment
                        if($hd_call_status_type == 'appointment'){#tbl_appointments
                            $app_insert = array(
                                'app_code' => 'LH-'.$this->_ext.'-'.date('YmdHis'),
                                'id_cus' => $id_link,
                                'cus_mobile' => $mobile,
                                'cus_first_name' => $first_name,
                                'cus_last_name' => $last_name,
                                'id_call' => $id_call,
                                'id_city' => $id_center_city,
                                'id_center' => $id_center,
                                'id_frametime' => $id_frametime,
                                'app_datetime' => $appointment.' '.$timeapp.':00',
                                'app_date' => $appointment,
                                'app_time' => $timeapp,
                                'app_content' => $content_app,
                                'id_center_call' => $this->_id_center,
                                'id_department' => $this->_id_department,
                                'id_group' => $this->_id_group,
                                'id_agent' => $this->_id_agent,
                                'agent_ext' => $this->_ext,
                                'agent_mobile' => $this->_mobile,
                                'agent_first_name' => $this->_agent_fname,
                                'agent_last_name' => $this->_agent_lname,
                                'sms_status' => 'new',
                                'app_created_at' => date('Y-m-d H:i:s'),
                                'app_status' => 'new',
                            );

                            if($id_fileup == 99999){
                                $app_insert['demo6t'] = 1;
                            }

                            $app_created_check = date('Y-m-d H:i:s', strtotime($StartTime . ' -10 minutes'));
                            $this->db->select('id');
                            $this->db->where('cus_mobile', $mobile);
                            $this->db->where('last_app', 'on');
							$this->db->where('app_status !=', 'cancel');
                            //$this->db->where('app_created_at >', $app_created_check);
                            $check_app = $this->db->get('tbl_appointments')->row_array();

                            if( isset($check_app) AND !empty($check_app) ){
                                redirect(base_url().'staff/call/callback');
                            }else{
                                $this->db->where('cus_mobile', $mobile);
                                $this->db->update('tbl_appointments', array('last_app' => 'off'));
                                $this->db->insert('tbl_appointments', $app_insert);
								
								$this->db->where('mobile', $mobile);
                                $this->db->update('tbl_customer', array('app' => 1));

                                $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`+1) WHERE `id_center`='.$id_center.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');

                                $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`+1) WHERE `id_center_spa`='.$id_center.' AND `id_center_call`='.$this->_id_center.' AND `id_department`='.$this->_id_department.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');
                            }
                        }
                        $check_call_report = array();
                        $db_staff->select('id');
                        $db_staff->where('datecall', date('Y-m-d'));
                        $db_staff->where('agent_id', $this->_id_agent);
                        $db_staff->where('id_status', $id_call_status);
                        $check_call_report = $db_staff->get('tbl_call_report')->row_array();

                        if( isset($check_call_report['id']) AND !empty($check_call_report['id']) ){
                            $db_staff->query("UPDATE `tbl_call_report` SET `countcall`=(`countcall` + 1),`updated_at`='".date('Y-m-d H:i:s')."' WHERE `datecall`='".date('Y-m-d')."' AND `agent_id`=".$this->_id_agent." AND `id_status`=".$id_call_status);
                        }else{
                            $db_call_report = array(
                                'datecall' => date('Y-m-d'),
                                'agent_id' => $this->_id_agent,
                                'agent_ext' => $this->_ext,
                                'id_status' => $id_call_status,
                                'countcall' => 1,
                                'updated_at' => date('Y-m-d H:i:s'),
                            );

                            $db_staff->insert('tbl_call_report', $db_call_report);
                        }
                    }
                }
                # redirect
                redirect(base_url().'staff/call/callback');
                #redirect(base_url().'staff');
            }else{
                $error = 'Giới hạn lịch hẹn đã hết';
                $data['error'] = $error;
            }
            #########################################
        }

        $id_call = 0;
        # Thông tin khách hàng
        if( !isset($customerdt) OR empty($customerdt) ){
            $db_staff->select('id,id_link,id_fileup,fullname,mobile,email,birthday,age,gender,address,id_city,id_district,source,start_ext,end_ext,start_date,close_date');
            $db_staff->where('end_ext="'.$this->_ext.'"');
            $db_staff->where('id', $id);
            $customerdt = $db_staff->get('tbl_customer')->row_array();

            if(isset($customerdt) AND !empty($customerdt)){
                $id_link = $customerdt['id_link'];
                $db_staff->where('id_cus', $id_link);
                $db_staff->where('is_callback', 0);
                $db_staff->update('tbl_call_detail', array('is_callback'=>1));

                $db_staff->select('content_call');
                $db_staff->where('id_cus', $id_link);
                $content_last = $db_staff->get('tbl_call_detail_last')->row_array();
                $customerdt['content_call'] = isset($content_last['content_call']) ? $content_last['content_call'] : '';

                $db_staff->where('id_cus', $id_link);
                $db_staff->delete('tbl_call_detail_last');
                # update called -- call
                $db_staff->where('id', $id);
                $db_staff->where('id_link', $id_link);
                $db_staff->update('tbl_customer', array(
                    'status' => 'call',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'last_updated_by' => $this->_uid
                ));

                $insert_detail = array(
                    'uniqid' => '',
                    'id_cus' => $id_link,
                    'mobile' => $customerdt['mobile'],
                    'fullname' => $customerdt['fullname'],
                    'agent_ext' => $this->_ext,
                    'id_center' => $this->_id_center,
                    'id_department' => $this->_id_department,
                    'id_group' => $this->_id_group,
                    'id_agent' => $this->_id_agent,
                    'call_type' => 'callback',
                    'created_at' => date('Y-m-d H:i:s'),
                    'start_date' => $customerdt['start_date'],
                    'close_date' => $customerdt['close_date'],
                );
                # insert call detail
                $db_staff->insert('tbl_call_detail', $insert_detail);
                $id_call = $db_staff->insert_id();
                $customerdt['id_call'] = $id_call;
                # insert call detail last
                $db_staff->insert('tbl_call_detail_last', $insert_detail);
                # check lich hen
                $this->db->select('app_status');
                $this->db->where('cus_mobile', $customerdt['mobile']);
                $this->db->where('last_app', 'on');
                $this->db->order_by('id', 'desc');
                $app_check = $this->db->get('tbl_appointments')->row_array();
                if( isset($app_check) AND !empty($app_check) ){
                    $_app_status = $app_check['app_status'];
                    if($_app_status != 'cancel'){
                        $data['msg_app_status'] = 'Khách hàng đang có lịch và tạm thời bị khóa, liên hệ kiểm tra';

                        # them moi vao bang check
                        $this->db->insert('tbl_app_check', array(
                            'id_center' => $this->_id_center,
                            'id_department' => $this->_id_department,
                            'id_group' => $this->_id_group,
                            'id_agent' => $this->_id_agent,
                            'ext' => $this->_ext,
                            'mobile' => $customerdt['mobile'],
                            'created_at' => date('Y-m-d H:i:s')
                        ));
                        # khoa ban ghi khach hang
                        $db_staff->where('id', $customerdt['id']);
                        $db_staff->update('tbl_customer', array('status' => 'close'));
                    }
                }
            }
        }
        $data['customerdt'] = $customerdt;

        # Thông tin Tỉnh/Thành phố
        $city = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $city = $this->db->get('tbl_city')->result_array();
        $data['city'] = $city;

        $district = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $district = $this->db->get('tbl_district')->result_array();
        $data['district'] = $district;

        $call_status = array();
        $this->db->select('id,name,type');
        $this->db->where('status', 'on');
        $call_status = $this->db->get('tbl_call_status')->result_array();
        $data['call_status'] = $call_status;

        ##################
        $data["limit"] = $this->grid_limit;
        $data['column_introduced'] = json_encode(array(
            '_wth_order'        => 60,
            '_wth_mobile'       => 150,
            '_wth_relationship' => 150,
            '_wth_status'       => 180,
        ));

        $data['content'] = 'callback';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function edit($id=false){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Gọi lại khách hàng', ''),
        );

        $error = '';

        $db_staff = $this->setdbconnect($this->_id_agent);

        $customerdt = array();
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $customerdt = $_POST;

            $id_call = $this->input->post('id_call');
            $id_fileup = $this->input->post('id_fileup');
            $id_link = $this->input->post('id_link');
            $fullname = $this->input->post('fullname');
            $expname = explode(' ', $fullname);
            $last_name = $expname[count($expname) - 1];
            $first_name = trim(str_replace($last_name, '', $fullname));

            $gender = $this->input->post('gender');
            $mobile = $this->input->post('mobile');
            $address = $this->input->post('address');
            $birthday = $this->input->post('birthday');
            $age = $this->input->post('age');
            $id_city = $this->input->post('id_city');
            $id_district = $this->input->post('id_district');
            $hd_call_status_type = $this->input->post('hd_call_status_type');
            $id_call_status = $this->input->post('id_call_status');
            $id_call_status_c1 = $this->input->post('id_call_status_c1');
            $id_call_status_c2 = $this->input->post('id_call_status_c2');            
            $content_call = $this->input->post('content_call');
            # lich hen
            $id_center_city = $this->input->post('id_center_city');
            $id_center = $this->input->post('id_center');
            $id_frametime = $this->input->post('id_frametime');
            $appointment = $this->input->post('appointment');
            $timeapp = $this->input->post('hd_frametime_start');
            $content_app = $this->input->post('content_app');
            # callback
            $dateforcus = $this->input->post('dateforcus');
            $timeforcus = $this->input->post('timeforcus');
            # call detail
            $priority_level = $this->input->post('priority_level');


            ########################################
            $_app = true;
            if($hd_call_status_type == 'appointment'){
                $_app = $this->checkapp($id_center, $this->_id_center, $this->_id_department, $appointment, $id_frametime);
            }
            if($_app){
                $customer_update = array(
                    'fullname' => $fullname,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'age' => $age,
                    'gender' => $gender,
                    'address' => $address,
                    'id_city' => $id_city,
                    'id_district' => $id_district,
                    #'callval' => 1,
                    'end_time' => date('Y-m-d H:i:s'),
                    'id_call_status' => $id_call_status,
                    'id_call_status_c1' => $id_call_status_c1,
                    'id_call_status_c2' => $id_call_status_c2,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'last_updated_by' => $this->_uid,
                    'close_date' => date('Y-m-d'),
                );

                if($hd_call_status_type == 'appointment'){
                    $customer_update['appointment'] = $appointment.' '.$timeapp.':00';
                }elseif($hd_call_status_type == 'callback'){
                    $customer_update['callback'] = $dateforcus . ' ' . $timeforcus . ':00';
                }

                #update customer
                if( isset($customer_update) AND !empty($customer_update) ){
                    //$db_staff->where('id_link', $id_link);
                    $db_staff->where('mobile', $mobile);
                    $db_staff->update('tbl_customer', $customer_update);

                    $db_center = $this->setdbconnect($this->_id_center, 'center');
                    //$db_center->where('id', $id_link);
                    $db_center->where('mobile', $mobile);
                    $db_center->update('tbl_customer', $customer_update);

                    $this->db->where('mobile', $mobile);
                    $this->db->update('tbl_customer', array(
                        'id_call_status' => $id_call_status,
                        'id_call_status_c1' => $id_call_status_c1,
                        'id_call_status_c2' => $id_call_status_c2,
                    ));

                    #update call detail
                    $call_detail_update = array(
                        'id_call_status' => $id_call_status,
                        'id_call_status_c1' => $id_call_status_c1,
                        'id_call_status_c2' => $id_call_status_c2,
                        'content_call' => $content_call,
                        'priority_level' => $priority_level,
                        'updated_at' => date('Y-m-d H:i:s'),
                    );

                    if($hd_call_status_type == 'appointment'){
                        $call_detail_update['appointment'] = $appointment.' '.$timeapp.':00';
                    }elseif($hd_call_status_type == 'callback'){
                        $call_detail_update['callback'] = $dateforcus . ' ' . $timeforcus . ':00';
                    }

                    if(isset($call_detail_update) AND !empty($call_detail_update)){
                        $db_staff->where('id', $id_call);
                        $db_staff->update('tbl_call_detail', $call_detail_update);

                        $db_staff->where('id_cus', $id_link);
                        $db_staff->update('tbl_call_detail_last', $call_detail_update);

                        # update appointment
                        if($hd_call_status_type == 'appointment'){#tbl_appointments
                            $app_insert = array(
                                'app_code' => 'LH-'.$this->_ext.'-'.date('YmdHis'),
                                'id_cus' => $id_link,
                                'cus_mobile' => $mobile,
                                'cus_first_name' => $first_name,
                                'cus_last_name' => $last_name,
                                'id_call' => $id_call,
                                'id_city' => $id_center_city,
                                'id_center' => $id_center,
                                'id_frametime' => $id_frametime,
                                'app_datetime' => $appointment.' '.$timeapp.':00',
                                'app_date' => $appointment,
                                'app_time' => $timeapp,
                                'app_content' => $content_app,
                                'id_center_call' => $this->_id_center,
                                'id_department' => $this->_id_department,
                                'id_group' => $this->_id_group,
                                'id_agent' => $this->_id_agent,
                                'agent_ext' => $this->_ext,
                                'agent_mobile' => $this->_mobile,
                                'agent_first_name' => $this->_agent_fname,
                                'agent_last_name' => $this->_agent_lname,
                                'sms_status' => 'new',
                                'app_created_at' => date('Y-m-d H:i:s'),
                                'app_status' => 'new',
                            );

                            if($id_fileup == 99999){
                                $app_insert['demo6t'] = 1;
                            }

                            $app_created_check = date('Y-m-d H:i:s', strtotime($StartTime . ' -10 minutes'));
                            $this->db->select('id');
                            $this->db->where('cus_mobile', $mobile);
                            $this->db->where('last_app', 'on');
							$this->db->where('app_status !=', 'cancel');
                            //$this->db->where('app_created_at >', $app_created_check);
                            $check_app = $this->db->get('tbl_appointments')->row_array();

                            if( isset($check_app) AND !empty($check_app) ){
                                redirect(base_url().'staff');
                            }else{
                                $this->db->where('cus_mobile', $mobile);
                                $this->db->update('tbl_appointments', array('last_app' => 'off'));
                                $this->db->insert('tbl_appointments', $app_insert);
								
								$this->db->where('mobile', $mobile);
                                $this->db->update('tbl_customer', array('app' => 1));

                                $this->db->query('UPDATE `tbl_limit_center` SET `appointment`=(`appointment`+1) WHERE `id_center`='.$id_center.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');

                                $this->db->query('UPDATE `tbl_limit_department` SET `appointment`=(`appointment`+1) WHERE `id_center_spa`='.$id_center.' AND `id_center_call`='.$this->_id_center.' AND `id_department`='.$this->_id_department.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"');
                            }
                        }
                        $check_call_report = array();
                        $db_staff->select('id');
                        $db_staff->where('datecall', date('Y-m-d'));
                        $db_staff->where('agent_id', $this->_id_agent);
                        $db_staff->where('id_status', $id_call_status);
                        $check_call_report = $db_staff->get('tbl_call_report')->row_array();

                        if( isset($check_call_report['id']) AND !empty($check_call_report['id']) ){
                            $db_staff->query("UPDATE `tbl_call_report` SET `countcall`=(`countcall` + 1),`updated_at`='".date('Y-m-d H:i:s')."' WHERE `datecall`='".date('Y-m-d')."' AND `agent_id`=".$this->_id_agent." AND `id_status`=".$id_call_status);
                        }else{
                            $db_call_report = array(
                                'datecall' => date('Y-m-d'),
                                'agent_id' => $this->_id_agent,
                                'agent_ext' => $this->_ext,
                                'id_status' => $id_call_status,
                                'countcall' => 1,
                                'updated_at' => date('Y-m-d H:i:s'),
                            );

                            $db_staff->insert('tbl_call_report', $db_call_report);
                        }
                    }
                }
                # redirect
                redirect(base_url().'staff');
            }else{
                $error = 'Giới hạn lịch hẹn đã hết';
                $data['error'] = $error;
            }
            ############################################################
        }

        # Thông tin khách hàng
        if( !isset($customerdt) OR empty($customerdt) ){
            $db_staff->select('id,id_link,id_fileup,fullname,mobile,email,birthday,age,gender,address,id_city,id_district,source,start_ext,end_ext,start_date,close_date');
            $db_staff->where('end_ext="'.$this->_ext.'"');
            $db_staff->where('id', $id);
            $customerdt = $db_staff->get('tbl_customer')->row_array();

            if(isset($customerdt) AND !empty($customerdt)){
                $id_call = false;
                $db_staff->select('id');
                $db_staff->where('id_cus', $customerdt['id_link']);
                $db_staff->where('id_call_status is null');
                $db_staff->limit(1);
                $calldt = $db_staff->get('tbl_call_detail')->row_array();

                $id_call = isset($calldt['id']) ? $calldt['id'] : false;
                $customerdt['id_call'] = $id_call;
                # check lich hen
                $this->db->select('app_status');
                $this->db->where('cus_mobile', $customerdt['mobile']);
                $this->db->where('last_app', 'on');
                $this->db->order_by('id', 'desc');
                $app_check = $this->db->get('tbl_appointments')->row_array();
                if( isset($app_check) AND !empty($app_check) ){
                    $_app_status = $app_check['app_status'];
                    if($_app_status != 'cancel'){
                        $data['msg_app_status'] = 'Khách hàng đang có lịch và tạm thời bị khóa, liên hệ kiểm tra';

                        # them moi vao bang check
                        $this->db->insert('tbl_app_check', array(
                            'id_center' => $this->_id_center,
                            'id_department' => $this->_id_department,
                            'id_group' => $this->_id_group,
                            'id_agent' => $this->_id_agent,
                            'ext' => $this->_ext,
                            'mobile' => $customerdt['mobile'],
                            'created_at' => date('Y-m-d H:i:s')
                        ));
                        # khoa ban ghi khach hang
                        $db_staff->where('id', $customerdt['id']);
                        $db_staff->update('tbl_customer', array('status' => 'close'));
                    }
                }
            }
        }
        $data['customerdt'] = $customerdt;
        # Thông tin Tỉnh/Thành phố
        $city = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $city = $this->db->get('tbl_city')->result_array();
        $data['city'] = $city;

        $district = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $district = $this->db->get('tbl_district')->result_array();
        $data['district'] = $district;

        $call_status = array();
        $this->db->select('id,name,type');
        $this->db->where('status', 'on');
        $call_status = $this->db->get('tbl_call_status')->result_array();
        $data['call_status'] = $call_status;

        ##################
        $data["limit"] = $this->grid_limit;
        $data['column_introduced'] = json_encode(array(
            '_wth_order'        => 60,
            '_wth_mobile'       => 150,
            '_wth_relationship' => 150,
            '_wth_status'       => 180,
        ));

        $data['content'] = 'edit';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function getcallback(){
        $db_staff = $this->setdbconnect($this->_id_agent);
        $timenow = date('Y-m-d H:i:s');
        $timecheck = date('Y-m-d H:i:s', strtotime($timenow . ' + 30 minutes'));
        $dtcallback = array();
        $db_staff->select('cus.id,call.callback,cus.fullname,cus.mobile');

        $db_staff->from('tbl_call_detail_last as call');
        $db_staff->join('tbl_customer as cus', 'cus.id_link=call.id_cus');
        $db_staff->where('call.callback is not null');
        $db_staff->where('cus.status !=', 'unassign');
        $db_staff->where('call.is_callback', 0);
        $db_staff->where('call.id_agent', $this->_id_agent);
        $db_staff->where('call.agent_ext', $this->_ext);

        $db_staff->where('call.callback <', $timecheck);
        $db_staff->where('call.callback >', $timenow);
        $db_staff->limit(1);
        $dtcallback = $db_staff->get()->row_array();

        echo json_encode($dtcallback);
    }

    function getmisscall(){
        $dtmiscall = array();
        $this->db->select('mobile, calldate');
        $this->db->from('tbl_misscall');
        $this->db->where('ext', $this->_ext);
        $this->db->order_by('calldate', 'desc');
        $this->db->limit(1);
        $dtcallback = $this->db->get()->row_array();

        echo json_encode($dtcallback);
    }
}
