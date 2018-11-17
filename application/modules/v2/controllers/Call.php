<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Call extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index($id=FALSE, $istype='call', $id_call=FALSE){
    	$data['breadcrumb'] = array(
			array('Home', base_url()),
			array('Thông tin cuộc gọi', ''),
		);

        $call_type = 'new';
        $flag      = false;
        if( $id AND intval($id) > 0 ){
            $flag      = true;
        }

        $customerdt = array();
        $uniqid = FALSE;
        $id_cus_call = FALSE;
        $data_post = array();

        # lưu dữ liệu
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $uniqid = $this->input->post('uniqid');
            $id_cus_call = $this->input->post('id_cus_call');
            if( isset($_POST['submit_save_data']) AND !empty($_POST['submit_save_data']) ){
                /*var_dump($_POST);
                die;*/
                $fullname = $this->input->post('fullname');
                $mobile = $this->input->post('mobile');
                $gender = $this->input->post('gender');
                $address = $this->input->post('address');
                $birthday = $this->input->post('birthday');
                if($birthday){
                    $birthday = $birthday . '-01-01';
                }

                $age = $this->input->post('age');

                $id_city = $this->input->post('id_city');
                $id_district = $this->input->post('id_district');

                $hd_id_call_status = $this->input->post('hd_id_call_status');
                $id_call_status = $this->input->post('id_call_status');
                $hd_id_call_status_c1 = $this->input->post('hd_id_call_status_c1');
                $id_call_status_c1 = $this->input->post('id_call_status_c1');
                $hd_id_call_status_c2 = $this->input->post('hd_id_call_status_c2');
                $id_call_status_c2 = $this->input->post('id_call_status_c2');

                $dateforcus = $this->input->post('dateforcus');
                if($dateforcus){
                    $exp_dateforcus = explode('/', $dateforcus);
                    $dateforcus = $exp_dateforcus[2].'-'.$exp_dateforcus[1].'-'.$exp_dateforcus[0];
                }
                $timeforcus = $this->input->post('timeforcus');

                $priority_level = $this->input->post('priority_level');
                $id_group = $this->input->post('id_group');
                $id_agent = $this->input->post('id_agent');
                $content = $this->input->post('content');
                $appointment = $this->input->post('appointment');
                if($appointment){
                    $exp_appointment = explode('/', $appointment);
                    $appointment = $exp_appointment[2].'-'.$exp_appointment[1].'-'.$exp_appointment[0];
                }
                $id_center = $this->input->post('id_center');
                $id_center_city = $this->input->post('id_center_city');
                $id_frametime = $this->input->post('id_frametime');

                $content_call = $this->input->post('content_call');
                $content_app = $this->input->post('content_app');

                $frametimedt = array();
                $this->db->select('id, start');
                $this->db->where('id', $id_frametime);
                $frametimedt = $this->db->get('tbl_frametime')->row_array();

                $centerdt = array();
                $this->db->where('id', $id_center);
                $centerdt = $this->db->get('tbl_centers')->row_array();

                # dieu kien dữ liệu
                $validation = array();

                $validation = array(
                    array(
                        'field' => 'fullname',
                        'label' => 'Tên khách hàng',
                        'rules' => 'trim|required|max_length[75]',
                    ),
                    array(
                        'field' => 'gender',
                        'label' => 'Giới tính',
                        'rules' => 'required',
                    ),
                    array(
                        'field' => 'age',
                        'label' => 'Tuổi',
                        'rules' => 'required|integer',
                    ),
                    array(
                        'field' => 'id_city',
                        'label' => 'Tỉnh/Thành phố',
                        'rules' => 'required',
                    ),
                    array(
                        'field' => 'id_call_status',
                        'label' => 'Trạng thái chính',
                        'rules' => 'required',
                    ),
                    array(
                        'field' => 'content_call',
                        'label' => 'Ghi chú',
                        'rules' => 'required',
                    ),
                );
                
                if($hd_id_call_status_c1){
                    $validation[] = array(
                        'field' => 'id_call_status_c1',
                        'label' => 'Trạng thái 1',
                        'rules' => 'required',
                    );

                    if($hd_id_call_status_c2){
                        $validation[] = array(
                            'field' => 'id_call_status_c2',
                            'label' => 'Trạng thái 2',
                            'rules' => 'required',
                        );                    
                    }
                }

                if( isset($hd_id_call_status) AND !empty($hd_id_call_status) ){
                    if( $hd_id_call_status == 'callback' ){
                        $validation[] = array(
                            'field' => 'dateforcus',
                            'label' => 'Ngày theo',
                            'rules' => 'required',
                        );
                        $validation[] = array(
                            'field' => 'timeforcus',
                            'label' => 'Giờ theo',
                            'rules' => 'required',
                        );
                    }else{
                        $validation[] = array(
                            'field' => 'id_center_city',
                            'label' => 'Tỉnh/Thành phố',
                            'rules' => 'required',
                        );
                        $validation[] = array(
                            'field' => 'id_center',
                            'label' => 'Trung tâm',
                            'rules' => 'required',
                        );
                        $validation[] = array(
                            'field' => 'appointment',
                            'label' => 'Ngày hẹn',
                            'rules' => 'required',
                        );
                        $validation[] = array(
                            'field' => 'id_frametime',
                            'label' => 'Ca hẹn',
                            'rules' => 'required',
                        );
                        $validation[] = array(
                            'field' => 'content_app',
                            'label' => 'Ghi chú',
                            'rules' => 'required',
                        );
                    }
                }

                $this->load->library('form_validation');
                $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
                $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
                $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
                $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

                $this->form_validation->set_rules($validation);
                if ( $this->form_validation->run() !== FALSE ){
                    $first_name = '';
                    $last_name  = '';
                    $fullname = convert_codau_sang_khongdau($fullname);
                    $explodename= explode(' ', $fullname);
                    $last_name  = $explodename[count($explodename) - 1];
                    $first_name = trim(str_replace($last_name, '', $fullname));
                    $cusUpdate = array(
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'fullname' => $fullname,
                        'birthday' => $birthday,
                        'gender' => $gender,
                        'address' => $address,
                        'id_city' => $id_city,
                        'id_district' => $id_district,
                        'id_call_status' => $id_call_status,
                        'id_call_status_c1' => $id_call_status_c1,
                        'id_call_status_c2' => $id_call_status_c2,
                        'status' => 'called',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'last_updated_by' => $this->_uid,
                    );

                    if( isset($age) AND !empty($age) ){
                        $cusUpdate['age'] = $age;
                    }
                    if( isset($hd_id_call_status) AND !empty($hd_id_call_status) ){
                        if( $hd_id_call_status == 'callback' ){
                            $cusUpdate['callback'] = $dateforcus . ' ' . $timeforcus;
                        }else{
                            $cusUpdate['appointment'] = $appointment . ' ' . $frametimedt['start'] . ':00';
                        }
                    }

                    $this->db->where('id', $id_cus_call);
                    $this->db->update('tbl_customer', $cusUpdate);

                    # cap nhat thong tin chi tiet cuoc goi
                    $callUpdate = array(
                        'id_call_status' => $id_call_status,
                        'id_call_status_c1' => $id_call_status_c1,
                        'id_call_status_c2' => $id_call_status_c2,
                        'content_call' => $content_call,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'priority_level' => $priority_level,
                        'id_city' => $id_center_city,
                        'id_center' => $id_center,
                        'id_frametime' => $id_frametime,
                    );

                    if( isset($hd_id_call_status) AND !empty($hd_id_call_status) ){
                        if( $hd_id_call_status == 'callback' ){
                            $callUpdate['callback'] = $dateforcus . ' ' . $timeforcus . ':00';
                        }else{
                            $callUpdate['appointment'] = $appointment . ' ' . $frametimedt['start'] . ':00';
                        }
                    }

                    $this->db->where('uniqid', $uniqid);
                    $this->db->update('tbl_call_detail', $callUpdate);

                    # them lich hen 
                    if( isset($hd_id_call_status) AND !empty($hd_id_call_status) ){
                        if( $hd_id_call_status == 'appointment' ){
                            $appData = array(
                                'app_code' => 'APP-CCSI-'.date('YmdHis').'-'.$uniqid,
                                'id_cus' => $id_cus_call,
                                'cus_mobile' => $mobile,
                                'cus_first_name' => $first_name,
                                'cus_last_name' => $last_name,
                                #'id_call' => '',
                                'uniqid_call' => $uniqid,
                                'id_city' => $id_center_city,
                                'id_center' => $id_center,
                                'center_address' => isset($centerdt['addresssms']) ? $centerdt['addresssms'] : '',
                                'id_frametime' => $id_frametime,
                                'app_datetime' => $appointment . ' ' . $frametimedt['start'] . ':00',
                                'app_date' => $appointment,
                                'app_time' => $frametimedt['start'],
                                'id_center_call' => $this->_id_center,
                                'id_department' => $this->_id_department,
                                'id_group' => $this->_id_group,
                                'id_agent' => $this->_id_agent,
                                'agent_ext' => $this->_ext,
                                'agent_mobile' => $this->_mobile,
                                'agent_first_name' => $this->_agent_fname,
                                'agent_last_name' => $this->_agent_lname,
                                'app_content' => $content_app,
                                'app_created_at' => date('Y-m-d H:i:s'),
                                'app_status' => 'new',
                                'status' => 'on',
                            );

                            $this->db->insert('tbl_appointments', $appData);
                            // cap nhat lich hen trung tam
                            $sql_query = 'UPDATE `tbl_limit_center` set `appointment`=(`appointment` + 1) where `id_center` in('.$id_center.','.$this->_id_center.') AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"';
                            $this->db->query($sql_query);
                            // cap nhat lich hen phong
                            $sql_query = 'UPDATE `tbl_limit_department` set `appointment`=(`appointment` + 1) where `id_center_call`='.$this->_id_center.' AND `id_center_spa`='.$id_center.' AND `id_department`='.$this->_id_department.' AND `id_frametime`='.$id_frametime.' AND `date`="'.$appointment.'"';
                            $this->db->query($sql_query);
                        }
                    }

                    # chuyen sang trang cuoc goi
                    redirect(base_url());
                }
                $data_post = $_POST;
            }else{
                $this->db->where('id', $id_cus_call);
                $this->db->update('tbl_customer', array('status' => 'close'));

                $this->db->where('uniqid', $uniqid);
                $this->db->where('id_cus', $id_cus_call);
                $this->db->update('tbl_call_detail', array('call_type' => 'cancel'));
                redirect(base_url());
            }
        }

        # kiem tra khach hang chưa gọi

        # sinh Uniqid
        $recusdt = array();
        if( ! isset($uniqid) OR empty($uniqid) ){
            $this->db->select('uniqid, id_cus');
            $this->db->where('agent_ext', $this->_ext);
            $this->db->where_in('call_type', array('new', 'callback'));
            $this->db->where('id_call_status is null');
            if( isset($id) AND $id ){
                $this->db->where('id_cus', $id);
            }
            $this->db->order_by('id', 'desc');
            $recusdt = $this->db->get('tbl_call_detail')->row_array();

            if( isset($recusdt) AND !empty($recusdt) ){
                $uniqid = $recusdt['uniqid'];
                $id     = $recusdt['id_cus'];
                $istype = 'recall';
            }
        }

        if( ! isset($uniqid) OR empty($uniqid) ){
            $uniqid = uniqid('', FALSE);
        }

        $dateforcus = date('Y-m-d');
        $timeforcus = date('H:i:s');

        $data['uniqid'] = $uniqid;
        $data['dateforcus'] = $dateforcus;
        $data['timeforcus'] = $timeforcus;

        # thong tin khach hang
        #if( isset($recalldt['id_cus']) AND !empty($recalldt['id_cus']) ){
        if( isset($id) AND !empty($id) AND $id ){ #$istype='call' OR 'callback'
            $this->db->select('*');
            $this->db->where('id', $id);
            $customerdt = $this->db->get('tbl_customer')->row_array();

            if( isset($customerdt) AND !empty($customerdt) AND in_array($istype, array('callback', 'appointment')) ){
                $call_type = $istype;

                $call_detail = array(
                    'uniqid'    => $uniqid,
                    'id_cus'    => $customerdt['id'],
                    'id_center_call' => $this->_id_center,
                    'id_department' => $this->_id_department,
                    'id_group'  => $this->_id_group,
                    'id_agent'  => $this->_id_agent,
                    'agent_ext' => $customerdt['end_ext'],
                    'call_type' => $call_type,
                    'created_at'=> $dateforcus . ' ' . $timeforcus,
                    'updated_at'=> $dateforcus . ' ' . $timeforcus,
                    'start_date'=> $customerdt['start_date'],
                    'close_date'=> date('Y-m-d')
                );

                $this->db->insert('tbl_call_detail', $call_detail);

                $this->db->where('id', $customerdt['id']);
                $this->db->update('tbl_customer', array('close_date'=> date('Y-m-d')));
            }
            # Thông tin cập nhật cuối cùng
        }else{
            $this->db->select('*');
            $this->db->where('status', 'assign');
            $this->db->where('end_ext', $this->_ext);
            $this->db->order_by('rand()');
            $this->db->limit(1);
            $customerdt = $this->db->get('tbl_customer')->row_array();

            if( isset($customerdt) AND !empty($customerdt) AND $istype == 'call' ){
                # thong tin call detail
                $call_detail = array(
                    'uniqid'    => $uniqid,
                    'id_cus'    => $customerdt['id'],
                    'id_center_call' => $this->_id_center,
                    'id_department' => $this->_id_department,
                    'id_group'  => $this->_id_group,
                    'id_agent'  => $this->_id_agent,
                    'agent_ext' => $customerdt['end_ext'],
                    'call_type' => $call_type,
                    'created_at'=> $dateforcus . ' ' . $timeforcus,
                    'updated_at'=> $dateforcus . ' ' . $timeforcus,
                    'start_date'=> $customerdt['start_date'],
                    'close_date'=> date('Y-m-d')
                );

                $this->db->insert('tbl_call_detail', $call_detail);

                $this->db->where('id', $customerdt['id']);
                $this->db->update('tbl_customer', array('status' => 'call', 'close_date'=> date('Y-m-d')));
            }
        }

        if( isset($data_post) AND !empty($data_post) ){
            // set default
        }

        $data['customerdt'] = $customerdt;

        if( ! isset($customerdt) OR empty($customerdt) ){
            $data['content'] = 'call/empty';
            $this->setlayout($data, 'v2/tmpl');
        }else{
            # get tinh/thanh pho
            $city = array();
            $this->db->select('id, name');
            $this->db->where('status', 'on');
            $this->db->order_by('position', 'ASC');
            $city = $this->db->get('tbl_city')->result_array();
            $data['city'] = $city;
            # get quan/huyen
            $district = array();
            if( isset($customerdt['id_city']) AND !empty($customerdt['id_city']) ){
                $this->db->select('id, name');
                $this->db->where('status', 'on');
                $district = $this->db->get('tbl_district')->result_array();
            }
            $data['district'] = $district;

            # trang thai
            $call_status    = array();
            $this->db->select('id, name, type');
            $this->db->where('status', 'on');
            $call_status    = $this->db->get('tbl_call_status')->result_array();
            $data['call_status'] = $call_status;

            if( isset($detail['id_call_status']) AND !empty($detail['id_call_status']) ){
                $call_status_c1 = array();
                $this->db->select('id, name');
                $this->db->where('id_call_status', $detail['id_call_status']);
                $this->db->where('status', 'on');
                $call_status_c1    = $this->db->get('tbl_call_status_child_c1')->result_array();
                $data['call_status_c1'] = $call_status_c1;

                if( isset($detail['id_call_status_c1']) AND !empty($detail['id_call_status_c1']) ){
                    $call_status_c2 = array();
                    $this->db->select('id, name');
                    $this->db->where('id_call_status', $detail['id_call_status']);
                    $this->db->where('id_call_status_c1', $detail['id_call_status_c1']);
                    $this->db->where('status', 'on');
                    $call_status_c2    = $this->db->get('tbl_call_status_child_c2')->result_array();
                    $data['call_status_c2'] = $call_status_c2;
                }
            }

            # khung gio
            $frametime = array();
            $this->db->select('id, name, start, end');
            $this->db->where('status', 'on');
            $this->db->where('id_center', $this->_id_center);
            $this->db->order_by('id', 'asc');
            $frametime = $this->db->get('tbl_frametime')->result_array();
            $data['frametime'] = $frametime;

            # muc do uu tien

            # chuyen team
            $group = array();
            $this->db->select('id, name');
            $this->db->where('status', 'on');
            $this->db->where('id_center', $this->_id_center);
            $this->db->where('id_department', $this->_id_department);
            $group = $this->db->get('tbl_groups')->result_array();
            $data['group'] = $group;

            # chuyen nhan vien
            $agent = array();
            $this->db->select('id, full_name, username');
            $this->db->where('status', 'on');
            $this->db->where('roles', 'staff');
            $this->db->where('id_center', $this->_id_center);
            $this->db->where('id_department', $this->_id_department);
            $this->db->where('id_group', $this->_id_group);
            $agent = $this->db->get('tbl_accounts')->result_array();
            $data['agent'] = $agent;

            # lich su cuoc goi & thong tin gioi thieu
            $data['column_introduced'] = json_encode(array(
                '_wth_order'        => 60,
                '_wth_mobile'       => 150,
                '_wth_relationship' => 150,
                '_wth_status'       => 180,
            ));

            $data['column_history'] = json_encode(array(
                '_wth_order' => 60,
                '_wth_time' => 180,
                '_wth_ext' => 80,
                '_wth_source' => 150,
                '_wth_status' => 150,
            ));

            $data['istype'] = $istype;

            $data['content'] = 'call/index';
            $this->setlayout($data, 'v2/tmpl');
        }
    }
    
    function introduced($id=FALSE){
        if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 0;

            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $inputsearch= isset($request['inputsearch']) ? $request['inputsearch'] : '';
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;
            
            $this->db->start_cache();
            $this->db->select('full_name, mobile, id_relationship, call_status');
            $this->db->from('tbl_customer_introduc');
            $this->db->where('id_customer', $id);
            $this->db->order_by('id', 'DESC');

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->limit($limit, $offset);
            $dataResult = $this->db->get()->result_array();

            $this->db->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'sql'   => $this->db->last_query()
            );

            echo json_encode($return);
            return;
        }
    }

    function addintroduc($id=FALSE){ # id khach hang
        $detail = array();
        $msg_success    = '';
        $msg_error      = '';

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $validation = array(
                array(
                    'field' => 'full_name',
                    'label' => 'Password Call',
                    'rules' => 'required|max_length[75]',
                ),
                array(
                    'field' => 'mobile',
                    'label' => 'Full Name',
                    'rules' => 'required|max_length[25]|is_unique[tbl_customer.mobile]',
                )
            );

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( ($this->form_validation->run() !== FALSE) AND intval($id) > 0 ){
                $first_name         = '';
                $last_name          = '';
                $full_name          = $this->input->post('full_name');
                $explode_name       = explode(' ', $full_name);
                $last_name          = $explode_name[count($explode_name) - 1];
                $first_name         = str_replace($last_name, '', $full_name);

                $mobile             = $this->input->post('mobile');
                $age                = $this->input->post('age');
                $gender             = $this->input->post('gender');
                $address            = $this->input->post('address');
                $id_relationship    = $this->input->post('id_relationship');

                $new_customer = array(
                    'first_name'=> $first_name,
                    'last_name' => $last_name,
                    'fullname'  => $full_name,
                    'mobile'    => $mobile,
                    'gender'    => $gender,
                );
                if( isset($age) AND !empty($age) ){
                    $new_customer['age'] = $age;
                }
                if( isset($address) AND !empty($address) ){
                    $new_customer['address'] = $address;
                }

                $this->db->trans_begin();

                # them moi khach hang
                $this->db->insert('tbl_customer', $new_customer);
                $id_newcus = $this->db->insert_id();
                # them moi quan he #tbl_customer_introduc
                $new_introduc = array(
                    'id_customer'           => $id,
                    'id_customer_introduc'  => $id_newcus,
                    'full_name'             => $full_name,
                    'mobile'                => $mobile,
                    'id_relationship'       => $id_relationship,
                );
                $this->db->insert('tbl_customer_introduc', $new_introduc);

                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $msg_error      = 'Cập nhật thông tin không thành công';
                }else{
                    $this->db->trans_commit();
                    $msg_success    = 'Cập nhật thông tin thành công';
                }
            }
            $detail = $_POST;
        }
        $data['success'] = $msg_success;
        $data['error']   = $msg_error;
        $data['detail']  = $detail;
        $data['content'] = 'call/introduc';
        $this->setlayout($data, 'v2/dialog');
    }

    function history($id=FALSE){    # id khach hang
        if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 0;

            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $inputsearch= isset($request['inputsearch']) ? $request['inputsearch'] : '';
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;
            
            $this->db->start_cache();
            $this->db->select('*');
            $this->db->from('view_call_detail_status');
            $this->db->where('id_cus', $id);
            $this->db->order_by('id', 'DESC');

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->limit($limit, $offset);
            $dataResult = $this->db->get()->result_array();

            $this->db->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'sql'   => $this->db->last_query()
            );

            echo json_encode($return);
            return;
        }
    }

    function detail($id=false){
        exit();
    }
}