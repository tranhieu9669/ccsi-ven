<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Marketing extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách khách hàng Marketing', ''),
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
            
            $this->db->start_cache();

            $this->db->select('app.id,app.cus_mobile,app.cus_first_name,app.cus_last_name,cen.name,cen.alert,app.id_center,app.app_datetime,app.agent_ext,app.sms_status,app.app_status');
            $this->db->from('tbl_appointments as app');
            $this->db->join('tbl_centers as cen', 'cen.id=app.id_center and cen.type="spa"');
            $this->db->where('app.last_app', 'on');
            $this->db->where('app.agent_ext', $this->_ext);

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
                'query' => $this->db->last_query()
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

        $data['content'] = 'index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function detail($id=false){
        $success= '';
        $error  = '';
        $detail = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $detail = $_POST;

            $validation = array(
                array(
                    'field' => 'fullname',
                    'label' => 'Tên khách hàng',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'mobile',
                    'label' => 'Số điện thoại',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'id_city',
                    'label' => 'Thành phố',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'id_dialog_center',
                    'label' => 'Chi nhánh',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'appointment',
                    'label' => 'Ngày hẹn',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'id_frametime',
                    'label' => 'Ca hẹn',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'app_content',
                    'label' => 'Ghi chú',
                    'rules' => 'required',
                ),
            );

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run() !== FALSE ){
                $fullname = $this->input->post('fullname');
                $mobile = $this->input->post('mobile');
                $age = $this->input->post('age');
                $gender = $this->input->post('gender');
                $address = $this->input->post('address');
                $id_city = $this->input->post('id_city');
                $id_dialog_center = $this->input->post('id_dialog_center');
                $appointment = $this->input->post('appointment');
                $id_frametime = $this->input->post('id_frametime');
                $timeapp = $this->input->post('hd_frametime_start');
                $app_content =  $this->input->post('app_content');

                # Check tôn tai của KH
                $mobile = !mb_detect_encoding($mobile, 'UTF-8', TRUE) ? utf8_encode($mobile) : $mobile;
                $mobile = preg_replace("/[^0-9]/", "", $mobile);

                $last_name      = '';
                $first_name     = '';
                $explode_name   = explode(' ', $fullname);
                $last_name      = $explode_name[count($explode_name) - 1];
                $first_name     = str_replace($last_name, '', $fullname);

                if( (substr($mobile, 0, 2) == '08' AND strlen($mobile) == 10 ) OR (substr($mobile, 0, 2) == '09' AND strlen($mobile) == 10 ) OR (substr($mobile, 0, 2) == '01' AND strlen($mobile) == 11) ){
                    $checkglobal = array();
                    $this->db->select('id,id_fileup,first_name,last_name,fullname,mobile,id_center,id_department,id_group,id_agent,demo,sale,app,block');
                    $this->db->from('tbl_customer');
                    $this->db->where('mobile', $mobile);
                    $checkglobal = $this->db->get()->row_array();

                    if( !isset($checkglobal['id']) OR empty($checkglobal['id']) ){
                        # them moi
                        $new_customer = array(
                            'first_name'=> $first_name,
                            'last_name' => $last_name,
                            'fullname'  => $fullname,
                            'mobile'    => $mobile,
                            'source'    => $this->_uid,
                            'id_center' => 0,
                            'id_department' => 0,
                            'id_group' => 0,
                            'id_agent' => $this->_id_agent,
                            'status' => 'assign',
                            'created_at'=> date('Y-m-d H:i:s'),
                            'app' => 1
                        );

                        $this->db->insert('tbl_customer', $new_customer);
                        $id_link = $this->db->insert_id();
                        // App
                        $app_insert = array(
                            'app_code' => 'LH-'.$this->_ext.'-'.date('YmdHis'),
                            'id_cus' => $id_link,
                            'cus_mobile' => $mobile,
                            'cus_first_name' => $first_name,
                            'cus_last_name' => $last_name,
                            'id_call' => 0,
                            'id_city' => $id_city,
                            'id_center' => $id_dialog_center,
                            'id_frametime' => $id_frametime,
                            'app_datetime' => $appointment.' '.$timeapp.':00',
                            'app_date' => $appointment,
                            'app_time' => $timeapp,
                            'app_content' => $app_content,
                            'id_center_call' => 0,
                            'id_department' => 0,
                            'id_group' => 0,
                            'id_agent' => $this->_id_agent,
                            'agent_ext' => $this->_ext,
                            'agent_mobile' => $this->_mobile,
                            'agent_first_name' => $this->_agent_fname,
                            'agent_last_name' => $this->_agent_lname,
                            'sms_status' => 'new',
                            'app_created_at' => date('Y-m-d H:i:s'),
                            'app_status' => 'new',
                        );

                        $this->db->insert('tbl_appointments', $app_insert);

                        $success = "Thêm lịch hẹn thành công";
                    }else{
                        if(isset($checkglobal['sale']) AND !empty($checkglobal['sale'])){
                            $error = 'Khách hàng đã mua sản phẩm';
                        }elseif(isset($checkglobal['demo']) AND !empty($checkglobal['demo'])){
                            $error = 'Khách hàng đã đến demo';
                        }elseif( isset($checkglobal['block']) AND $checkglobal['block'] > 0 ){
                            $error = 'Khách hàng bị block';
                        }elseif(isset($checkglobal['app']) AND !empty($checkglobal['app'])){
                            $error = 'Khách hàng đã đặt lịch';
                        }else{
                            $id_center = $checkglobal['id_center'];
                            $id_department = $checkglobal['id_department'];
                            $id_group = $checkglobal['id_group'];

                            if(isset($id_center) AND !empty($id_center)){
                                $_dbcenter = $this->setdbconnect($id_center, 'center');
                                $_dbcenter->where('mobile', $mobile);
                                $_dbcenter->delete('tbl_customer');
                            }

                            if(isset($id_department) AND !empty($id_department)){
                                $_dbdepartment = $this->setdbconnect($id_department, 'department');
                                $_dbdepartment->where('mobile', $mobile);
                                $_dbdepartment->delete('tbl_customer');
                            }

                            if(isset($id_group) AND !empty($id_group)){
                                $_dbgroup = $this->setdbconnect($id_group, 'group');
                                $_dbgroup->where('mobile', $mobile);
                                $_dbgroup->delete('tbl_customer');
                            }

                            $customer_data = array(
                                'first_name'=> $first_name,
                                'last_name' => $last_name,
                                'fullname'  => $fullname,
                                'source'    => $this->_uid,
                                'id_center' => 0,
                                'id_department' => 0,
                                'id_group' => 0,
                                'id_agent' => $this->_id_agent,
                                'status' => 'assign',
                                'created_at'=> date('Y-m-d H:i:s'),
                                'app' => 1
                            );
                            $this->db->where('id', $id);
                            $this->db->where('mobile', $mobile);
                            $this->db->update('tbl_customer', $customer_data);

                            $app_insert = array(
                                'app_code' => 'LH-'.$this->_ext.'-'.date('YmdHis'),
                                'id_cus' => $id_link,
                                'cus_mobile' => $mobile,
                                'cus_first_name' => $first_name,
                                'cus_last_name' => $last_name,
                                'id_call' => 0,
                                'id_city' => $id_city,
                                'id_center' => $id_dialog_center,
                                'id_frametime' => $id_frametime,
                                'app_datetime' => $appointment.' '.$timeapp.':00',
                                'app_date' => $appointment,
                                'app_time' => $timeapp,
                                'app_content' => $app_content,
                                'id_center_call' => 0,
                                'id_department' => 0,
                                'id_group' => 0,
                                'id_agent' => $this->_id_agent,
                                'agent_ext' => $this->_ext,
                                'agent_mobile' => $this->_mobile,
                                'agent_first_name' => $this->_agent_fname,
                                'agent_last_name' => $this->_agent_lname,
                                'sms_status' => 'new',
                                'app_created_at' => date('Y-m-d H:i:s'),
                                'app_status' => 'new',
                            );

                            $this->db->insert('tbl_appointments', $app_insert);

                            $success = "Thêm lịch hẹn thành công";
                        }
                    }
                }else{
                    $error = 'Số điện thoại không đúng';
                }
            }
        }

        $city = array();
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->order_by('position');
        $city = $this->db->get('tbl_city')->result_array();
        $data['city'] = $city;

        if(isset($detail['id_city']) AND !empty($detail['id_city'])){
            $center = array();
            $this->db->select('id,code,name');
            $this->db->where('status', 'on');
            $this->db->where('type', 'spa');
            $this->db->where('id_city', $detail['id_city']);
            $center = $this->db->get('tbl_centers')->result_array();
            $data['center'] = $center;
        }

        if( isset($detail['id_dialog_center']) AND !empty($detail['id_dialog_center']) AND isset($detail['appointment']) AND !empty($detail['appointment']) ){
            $where_frame_center = array();
            $frame_center = array();
            $this->db->select('id_frametime');
            $this->db->where('date', $detail['appointment']);
            $this->db->where('id_center', $detail['id_dialog_center']);
            $this->db->where('limited > appointment');
            $frame_center = $this->db->get('tbl_limit_center')->result_array();
            foreach ($frame_center as $key => $value) {
                array_push($where_frame_center, $value['id_frametime']);
            }

            if(isset($where_frame_center) AND !empty($where_frame_center)){
                $list_frame = array();
                $this->db->select('id,name,start,end');
                $this->db->where_in('id', $where_frame_center);
                $list_frame = $this->db->get('tbl_frametime')->result_array();
                $data['list_frame'] = $list_frame;
            }
        }

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;

        $data['content'] = 'detail';
        $this->setlayout($data, NULL);
    }

    function frameapp(){
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

        /*$where_frame = array();
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
        }*/

        $list_frame = array();

        if(isset($where_frame_center) AND !empty($where_frame_center)){
            $this->db->select('id,name,start,end');
            $this->db->where_in('id', $where_frame_center);
            $list_frame = $this->db->get('tbl_frametime')->result_array();
        }

        echo json_encode($list_frame);
    }
}