<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
CREATE 
    ALGORITHM = MERGE 
VIEW `view_account` 
    AS SELECT cen.name AS cenname,dep.name AS depname,gro.name AS groname,acc.id,acc.full_name,acc.mobile,acc.username,age.ext,age.password,acc.roles,acc.id_city,acc.id_center,acc.id_department,acc.id_group,acc.status 
    FROM `tbl_accounts` AS acc 
    LEFT JOIN `tbl_agent`as age ON age.id_account=acc.id 
    LEFT JOIN `tbl_centers`AS cen ON cen.id=acc.id_center AND cen.status='on' 
    LEFT JOIN `tbl_departments`AS dep ON dep.id=acc.id_department AND dep.status='on' 
    LEFT JOIN `tbl_groups`AS gro ON gro.id=acc.id_group AND gro.status='on' 
    ORDER BY cen.id,dep.id,gro.id 
#WITH LOCAL CHECK OPTION 
*/
class Account extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
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
            $inputsearch= isset($request['inputsearch']) ? $request['inputsearch'] : '';
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;
            
            $this->db->start_cache();
            $this->db->from('view_account');

            $this->db->where_not_in('roles', array('supadmin'));

            if( ! in_array($this->_role, array('supadmin')) ){
                $this->db->where_not_in('roles', array('admin'));
            }

            if($this->_id_group){
                $this->db->where('id_group', $this->_id_group);
            }elseif($this->_id_department){
                $this->db->where('id_department', $this->_id_department);
            }elseif ($this->_id_center) {
                $this->db->where('id_center', $this->_id_center);
            }elseif($this->_id_city){
                $this->db->where('id_city', $this->_id_city);
            }

            $this->db->order_by('id', 'DESC');

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->limit($limit, $offset);
            $dataResult = $this->db->get()->result_array();

            $this->db->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 50,
            '_wth_mobile'   => 100,
            '_wth_username' => 120,
            '_wth_ext'      => 60,
            '_wth_status'   => 65,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'account/index';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function getext($id_center, $id_department){
        $ext = FALSE;

        $center     = array();
        $this->db->select('first_ext');
        $this->db->where('id', $id_center);
        $center     = $this->db->get('tbl_centers')->row_array();

        $department = array();
        $this->db->select('first_ext');
        $this->db->where('id', $id_department);
        $this->db->where('id_center', $id_center);
        $department = $this->db->get('tbl_departments')->row_array();

        #if( isset($center) AND !empty($center) ){ #AND isset($department) AND !empty($department) 
            $first_ext = ( isset($center['first_ext']) ? $center['first_ext'] : '9' ) . ( isset($department['first_ext']) ? $department['first_ext'] : '9' );

            $detail = array();
            $this->db->select('max(ext) as ext');
            $this->db->like('ext', $first_ext, 'after'); #before
            $this->db->limit(1);
            $detail = $this->db->get('tbl_agent')->row_array();

            if( isset($detail['ext']) AND !empty($detail['ext']) ){
                $ext = intval($detail['ext']) + 1;
            }else{
                $ext = intval($first_ext . '00');
            }
        #}

        return $ext;
    }

    function detail($id=0){
        $flag   = FALSE;
        if( $id AND $id > 0 ){
            $flag   = TRUE;
        }
        $data['flag'] = $flag;

        $success= '';
        $error  = '';
        $detail = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $detail = $_POST;
            $action = $this->input->post('hdaction');
            if( $action == 'dataaction' ){
                $roles = $this->input->post('roles');

                $validation = array();

                $validation[] = array(
                    'field' => 'roles',
                    'label' => 'Quyền tài khoản',
                    'rules' => 'required',
                );

                if( in_array($roles, array('center','department','group','staff')) ){
                    $validation[] = array(
                        'field' => 'id_city',
                        'label' => 'Tỉnh/T.Phố',
                        'rules' => 'required',
                    );

                    $validation[] = array(
                        'field' => 'id_center',
                        'label' => 'Trung tâm',
                        'rules' => 'required',
                    );
                }
                
                if( in_array($roles, array('department','group','staff')) ){
                    $validation[] = array(
                        'field' => 'id_department',
                        'label' => 'Phòng',
                        'rules' => 'required',
                    );
                }
                
                if( in_array($roles, array('group','staff')) ){
                    $validation[] = array(
                        'field' => 'id_group',
                        'label' => 'Nhóm',
                        'rules' => 'required',
                    );
                }

                $validation[] = array(
                    'field' => 'full_name',
                    'label' => 'Tên đầy đủ',
                    'rules' => 'required|max_length[150]',
                );

                $validation[] = array(
                    'field' => 'email',
                    'label' => 'Email',
                    'rules' => 'required|max_length[75]',
                );

                $validation[] = array(
                    'field' => 'mobile',
                    'label' => 'Điện thoại',
                    'rules' => 'required|max_length[15]',
                );

                $this->load->library('form_validation');
                $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
                $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
                $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
                $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

                $this->form_validation->set_rules($validation);
                if ( $this->form_validation->run() !== FALSE ){
                    $id_center      = $this->input->post('id_center');
                    $id_department  = $this->input->post('id_department');
                    $id_group       = $this->input->post('id_group');
                    $id_city        = $this->input->post('id_city');
                    $full_name      = trim($this->input->post('full_name'));
                    $first_name     = '';
                    $last_name      = '';
                    if( isset($full_name) AND !empty($full_name) ){
                        $listname   = explode(' ', $full_name);
                        $last_name  = $listname[count($listname) - 1];
                        $first_name = trim(str_replace($last_name, '', $full_name));
                    }
                    $email          = trim($this->input->post('email'));
                    $mobile         = trim($this->input->post('mobile'));
                    $mobile         = preg_replace("/[^0-9]/", "", $mobile);

                    $accounts = array(
                        'full_name' => $full_name,
                        'first_name'=> $first_name,
                        'last_name' => $last_name,
                        'email'     => $email,
                        'mobile'    => $mobile,
                        'roles'     => $roles,
                        'updated_at'=> date('Y-m-d H:i:s'),
                        'updated_by'=> $this->_uid,
                    );

                    if( in_array($roles, array('center','department','group','staff')) ){
                        $accounts['id_city'] = $id_city;
                        $accounts['id_center'] = $id_center;
                    }
                    
                    if( in_array($roles, array('department','group','staff')) ){
                        $accounts['id_department'] = $id_department;
                    }
                    
                    if( in_array($roles, array('group','staff')) ){
                        $accounts['id_group'] = $id_group;
                    }

                    $this->db->trans_begin();

                    if( ! $flag ){
                        $ext            = $this->getext($id_center, $id_department);
                        $username       = $roles . '_' . $ext;

                        $hash           = $this->generaterandomstring(rand(6,12));
                        $passacount     = $this->encrypassword('deaura@123', $hash);

                        $accounts['username']   = $username;
                        $accounts['hash']       = $hash;
                        $accounts['password']   = $passacount;
                        $accounts['status']     = 'off';

                        $this->db->insert('tbl_accounts', $accounts);
                        $id = $this->db->insert_id();

                        $agent = array(
                            'id_account'=> $id,
                            'ext'       => $ext,
                            'password'  => '123456',#$this->generaterandomstring(rand(6,8)),
                            'status'    => 'off',
                            'updated_at'=> date('Y-m-d H:i:s'),
                            'updated_by'=> $this->_uid,
                        );

                        if( in_array($roles, array('admin','center','department','group')) ){
                            $agent['callsoft']  = 'on';
                        }else{
                            $agent['callsoft']  = 'off';
                        }

                        $this->db->insert('tbl_agent', $agent);

                        $detail['ext'] = isset($ext) ? $ext : '';
                    }else{
                        $this->db->where('id', $id);
                        $this->db->update('tbl_accounts', $accounts);

                        $agent = array(
                            'id_account'=> $id,
                            'updated_at'=> date('Y-m-d H:i:s'),
                            'updated_by'=> $this->_uid,
                        );

                        if( in_array($roles, array('admin','center','department','group')) ){
                            $agent['callsoft']  = 'on';
                        }else{
                            $agent['callsoft']  = 'off';
                        }

                        $this->db->where('id_account', $id);
                        $this->db->update('tbl_agent', $agent);
                    }

                    # Luu thong tin log thay doi tai khoan
                    $accounts_log = array(
                        'id_account'=> $id,
                        'data'      => json_encode($accounts),
                        'updated_at'=> date('Y-m-d H:i:s'),
                        'updated_by'=> $this->_uid
                    );
                    $this->db->insert('tbl_accounts_log', $accounts_log);

                    if ($this->db->trans_status() === FALSE){
                        $this->db->trans_rollback();
                        $error      = 'Cập nhật thông tin không thành công';
                    }else{
                        $this->db->trans_commit();
                        $success    = 'Cập nhật thông tin thành công';
                    }
                }
            }else{
                $hash       = $this->generaterandomstring(rand(6,12));
                $passacount = $this->encrypassword('deaura@123', $hash);

                $accounts['hash']           = $hash;
                $accounts['password']       = $passacount;
                $accounts['lastupdatepass'] = date('Y-m-d H:i:s');
                $accounts['typepass']       = 'reset';
                $accounts['updated_at']     = date('Y-m-d H:i:s');
                $accounts['updated_by']     = $this->_uid;

                $this->db->trans_begin();

                $this->db->where('id', $id);
                $this->db->update('tbl_accounts', $accounts);

                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $error      = 'Cập nhật thông tin không thành công';
                }else{
                    $this->db->trans_commit();
                    $success    = 'Cập nhật thông tin thành công';
                }
            }
        }

        if( $flag AND ( ! isset($detail) OR empty($detail) ) ){
            $this->db->where('id', $id);
            $detail = $this->db->get('tbl_accounts')->row_array();
        }

        if( !isset($detail['id_city']) ){
            $detail['id_city'] = $this->_id_city;
        }
        if( !isset($detail['id_center']) ){
            $detail['id_center'] = $this->_id_center;
        }
        if( !isset($detail['id_department']) ){
            $detail['id_department'] = $this->_id_department;
        }
        if( !isset($detail['id_group']) ){
            $detail['id_group'] = $this->_id_group;
        }

        $city = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->order_by('position');
        $city = $this->db->get('tbl_city')->result_array();
        $data['city'] = $city;

        if( isset($detail['id_city']) AND !empty($detail['id_city']) ){
            $center = array();
            $this->db->where('status', 'on');
            $this->db->where('id_city', $detail['id_city']);
            $center = $this->db->get('tbl_centers')->result_array();
            $data['center']  = $center;
        }

        if( isset($detail['id_center']) AND !empty($detail['id_center']) ){
            $department = array();
            $this->db->where('status', 'on');
            $this->db->where('id_center', $detail['id_center']);
            $department = $this->db->get('tbl_departments')->result_array();
            $data['department'] = $department;
        }

        if( isset($detail['id_department']) AND !empty($detail['id_department']) ){
            $group = array();
            $this->db->where('status', 'on');
            $this->db->where('id_department', $detail['id_department']);
            $group = $this->db->get('tbl_groups')->result_array();
            $data['group'] = $group;
        }

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
        $data['content'] = 'account/detail';
        $this->setlayout($data, 'v2/dialog');
    }

    function createlist(){
        $success= '';
        $error  = '';
        $detail = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $detail = $_POST;
            $validation = array(
                array(
                    'field' => 'id_city',
                    'label' => 'Tỉnh/T.Phố',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'id_center',
                    'label' => 'Trung tâm',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'id_department',
                    'label' => 'Phòng',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'id_group',
                    'label' => 'Nhóm',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'fileSelect',
                    'label' => 'File dữ liệu',
                    'rules' => 'required',
                )
            );

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run() !== FALSE ){
                $id_city        = $this->input->post('id_city');
                $id_center      = $this->input->post('id_center');
                $id_department  = $this->input->post('id_department');
                $id_group       = $this->input->post('id_group');

                if( isset($_FILES) AND !empty($_FILES) ){
                    $FileType = pathinfo($_FILES['uploadedFiles']['name'],PATHINFO_EXTENSION);
                    $upload_conf['upload_path']     = APPPATH . 'uploads/v2/account/';
                    $upload_conf['file_name']       = date('YmdHis') . '-account.' . $FileType;
                    $upload_conf['allowed_types']   = 'xls|xlsx';
                    $upload_conf['overwrite']       = false;
                    $upload_conf['max_size']        = 1024 * 1;
                    $upload_conf['encrypt_name']    = TRUE;

                    $this->load->library('upload', $upload_conf);
                    if ( ! $this->upload->do_upload('uploadedFiles')) {
                        $error = array('error' => $this->upload->display_errors());
                        $error = $error['error'];

                        $msg_error = $this->upload->display_errors();
                    }else {
                        $data = $this->upload->data();
                        $file_name  = $data['file_name'];

                        $datafile = array(
                            'filename'      => $file_name,
                            'id_city'       => $id_city,
                            'id_center'     => $id_center,
                            'id_department' => $id_department,
                            'id_group'      => $id_group,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'created_by'    => $this->_uid
                        );

                        $this->db->insert('tbl_file_upload_account', $datafile);
                        $id = $this->db->insert_id();

                        $this->extractexcel($id);

                        $success = 'Upload file thành công';
                    }
                }else{
                    $error = 'File upload empty';
                }
            }
        }

        $city = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->order_by('position');
        $city = $this->db->get('tbl_city')->result_array();
        $data['city'] = $city;

        if( isset($detail['id_city']) AND !empty($detail['id_city']) ){
            $center = array();
            $this->db->where('status', 'on');
            $this->db->where('id_city', $detail['id_city']);
            $center = $this->db->get('tbl_centers')->result_array();
            $data['center']  = $center;
        }

        if( isset($detail['id_center']) AND !empty($detail['id_center']) ){
            $department = array();
            $this->db->where('status', 'on');
            $this->db->where('id_center', $detail['id_center']);
            $department = $this->db->get('tbl_departments')->result_array();
            $data['department'] = $department;
        }

        if( isset($detail['id_department']) AND !empty($detail['id_department']) ){
            $group = array();
            $this->db->where('status', 'on');
            $this->db->where('id_department', $detail['id_department']);
            $group = $this->db->get('tbl_groups')->result_array();
            $data['group'] = $group;
        }

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
        $data['content'] = 'account/createlist';
        $this->setlayout($data, 'v2/dialog');
    }

    function extractexcel($id){
        $this->db->where('id', $id);
        $this->db->update('tbl_file_upload_account', array('extract_start' => date('Y-m-d H:i:s')));

        $uploaddt = array();
        $this->db->select('id,filename,id_city,id_center,id_department,id_group');
        $this->db->where('id', $id);
        $this->db->where('status !=', 'success');
        $uploaddt = $this->db->get('tbl_file_upload_account')->row_array();

        $data_insert = array();
        $data_exists = array();
        if( isset($uploaddt) AND !empty($uploaddt) ){
            $id         = $uploaddt['id'];
            $filename   = $uploaddt['filename'];
            $id_city    = $uploaddt['id_city'];
            $id_center  = $uploaddt['id_center'];
            $id_department= $uploaddt['id_department'];
            $id_group   = $uploaddt['id_group'];

            $filepath= APPPATH . 'uploads/v2/account/' . $filename;
            if( file_exists($filepath) ){
                require_once( APPPATH  . 'third_party/PHPLib/PHPExcel/IOFactory.php');
                $objPHPExcel = PHPExcel_IOFactory::load($filepath);
                $max_row = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

                set_time_limit(0);
                for ($row = 2; $row <= $max_row; $row++) {
                    $fullname = trim($objPHPExcel->getActiveSheet()->getCell("B" . $row)->getCalculatedValue());
                    $fullname = !mb_detect_encoding($fullname, 'UTF-8', TRUE) ? utf8_encode($fullname) : $fullname;
                    $fullname = convert_codau_sang_khongdau($fullname);
                    $fullname = strtoupper($fullname);

                    $first_name = '';
                    $last_name  = '';
                    $explodename= explode(' ', $fullname);
                    $last_name  = $explodename[count($explodename) - 1];
                    $first_name = trim(str_replace($last_name, '', $fullname));

                    $email = trim($objPHPExcel->getActiveSheet()->getCell("C" . $row)->getCalculatedValue());
                    $email = !mb_detect_encoding($email, 'UTF-8', TRUE) ? trim(utf8_encode($email)) : trim($email);

                    $mobile = trim($objPHPExcel->getActiveSheet()->getCell("D" . $row)->getCalculatedValue());
                    $mobile = !mb_detect_encoding($mobile, 'UTF-8', TRUE) ? utf8_encode($mobile) : $mobile;
                    $mobile = preg_replace("/[^0-9]/", "", $mobile);
                    if( substr($mobile, 0, 1) != '0' ){
                        $mobile = '0'.$mobile;
                    }

                    $status = strtolower(trim($objPHPExcel->getActiveSheet()->getCell("E" . $row)->getCalculatedValue()));
                    $status = !mb_detect_encoding($status, 'UTF-8', TRUE) ? utf8_encode($status) : $status;
                    if( ( !isset($status) OR empty($status) ) OR $status != 'on' ){
                        $status = 'off';
                    }

                    # check ton tai email
                    $this->db->where('email', $email);
                    $check_exists = $this->db->get('tbl_accounts')->row_array();

                    if( isset($check_exists) AND !empty($check_exists) ){
                        $data_exists[] = array(
                            'full_name'  => $fullname,
                            'first_name' => $first_name,
                            'last_name'  => $last_name,
                            'email'      => $email,
                            'mobile'     => $mobile,
                        );
                    }else{
                        $username   = '';
                        $hash       = $this->generaterandomstring(rand(6,12));
                        $passacount = $this->encrypassword('deaura@123', $hash);
                        $data_insert = array(
                            'full_name'  => $fullname,
                            'first_name' => $first_name,
                            'last_name'  => $last_name,
                            'email'      => $email,
                            'mobile'     => $mobile,
                            //'username'
                            'hash'       => $hash,
                            'password'   => $passacount,
                            'id_city'    => $id_city,
                            'id_center'  => $id_center,
                            'id_department' => $id_department,
                            'id_group'   => $id_group,
                            'roles'      => 'staff',
                            'status'     => $status,
                            'typepass'   => 'create',
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => $this->_uid
                        );

                        $this->db->insert('tbl_accounts', $data_insert);
                        $id = $this->db->insert_id();

                        # get ext
                        $ext = $this->getext($id_center, $id_department);
                        if( $ext AND strlen($ext) == 4 ){
                            $agent = array(
                                'id_account'=> $id,
                                'ext'       => $ext,
                                'password'  => '123456',#$this->generaterandomstring(rand(6,8)),
                                'callsoft'  => 'off',
                                'status'    => $status,
                                'updated_at'=> date('Y-m-d H:i:s'),
                                'updated_by'=> $this->_uid,
                            );
                            $this->db->insert('tbl_agent', $agent);

                            $this->db->where('id', $id);
                            $this->db->update('tbl_accounts', array('username' => 'staff_' . $ext));
                        }else{
                            $this->db->where('id', $id);
                            $this->db->update('tbl_accounts', array('username' => 'staff_' . date('YmdHis'), 'status' => 'on'));
                        }
                    }
                }
            }
        }

        $this->db->where('id', $id);
        $this->db->update('tbl_file_upload_account', array('status' => 'success','extract_end' => date('Y-m-d H:i:s')));
    }

    function accountmn(){
        $data['content'] = 'account/createmn';
        $this->setlayout($data, 'v2/tmpl');
    }

    function accinfo(){

    }

    function changepass(){
        $msg_error  = '';
        $msg_success= '';

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $validation = array(
                array(
                    'field' => 'password',
                    'label' => 'Mật khẩu',
                    'rules' => 'required|max_length[50]',
                ),
                array(
                    'field' => 'newpass',
                    'label' => 'Mật khẩu',
                    'rules' => 'required|max_length[50]',
                ),
                array(
                    'field' => 'newpasscf',
                    'label' => 'Mật khẩu',
                    'rules' => 'required|matches[newpass]|max_length[50]',
                ),
            );

            $this->load->library( 'form_validation' );
            # Validation
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');
            $this->form_validation->set_message('matches', '<b>%s</b> không đúng.');

            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() !== FALSE){
                $username   = $this->_uid;
                $password   = $this->input->post('password');
                $newpass    = $this->input->post('newpass');

                $infodt = array();
                $this->db->select('hash,password');
                $this->db->where('username', $username);
                $this->db->where('status', 'on');
                $infodt = $this->db->get('tbl_accounts')->row_array();

                if( isset($infodt) AND !empty($infodt) ){
                    $hash       = $infodt['hash'];
                    $passwordac = $infodt['password'];
                    $password   = $this->encrypassword($password, $hash);

                    if( $passwordac == $password ){
                        $hash    = $this->generaterandomstring(rand(6,12));
                        $newpass = $this->encrypassword($newpass, $hash);

                        $dataupdate = array(
                            'hash'      => $hash,
                            'password'  => $newpass,
                            'typepass'  => 'change',
                            'lastupdatepass' => date('Y-m-d H:i:s')
                        );

                        $this->db->where('username', $username);
                        if( $this->db->update('tbl_accounts', $dataupdate) !== TRUE ){
                            # cap nhat co loi
                            $msg_error = 'Cập nhật thông tìn tài khoản lỗi';
                        }else{
                            $this->session->unset_userdata(OUTCALL_SESSION_INFO);
                            $this->session->sess_destroy();
                            session_destroy();
                            $msg_success = 'Thay đổi mật khẩu thành công';
                        }
                    }else{
                        # mat khau cu khong dung
                        $msg_error = 'Mật khậu cũ không đúng';
                    }
                }else{
                    # khong co thong tin
                    $msg_error = 'Không tìm thấy thông tin tài khoản';
                }
            }
        }

        $data['msg_success'] = $msg_success;
        $data['msg_error'] = $msg_error;
        $data['content'] = 'account/change';
        $this->setlayout($data, 'v2/dialog');
    }

    function expired(){
        $msg = '';
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $validation = array(
                array(
                    'field' => 'username',
                    'label' => 'Tên đăng nhập',
                    'rules' => 'required|max_length[25]',
                ),
                array(
                    'field' => 'password',
                    'label' => 'Mật khẩu',
                    'rules' => 'required|max_length[50]',
                ),
                array(
                    'field' => 'newpass',
                    'label' => 'Mật khẩu',
                    'rules' => 'required|max_length[50]',
                ),
                array(
                    'field' => 'newpasscf',
                    'label' => 'Mật khẩu',
                    'rules' => 'required|matches[newpass]|max_length[50]',
                ),
            );

            $this->load->library( 'form_validation' );
            # Validation
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');
            $this->form_validation->set_message('matches', '<b>%s</b> không đúng.');

            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() !== FALSE){
                $username   = $this->input->post('username');
                $password   = $this->input->post('password');
                $newpass    = $this->input->post('newpass');

                $infodt = array();
                $this->db->select('hash,password');
                $this->db->where('username', $username);
                $this->db->where('status', 'on');
                $infodt = $this->db->get('tbl_accounts')->row_array();

                if( isset($infodt) AND !empty($infodt) ){
                    $hash       = $infodt['hash'];
                    $passwordac = $infodt['password'];
                    $password   = $this->encrypassword($password, $hash);

                    if( $passwordac == $password ){
                        $hash    = $this->generaterandomstring(rand(6,12));
                        $newpass = $this->encrypassword($newpass, $hash);

                        $dataupdate = array(
                            'hash'      => $hash,
                            'password'  => $newpass,
                            'typepass'  => 'change',
                            'lastupdatepass' => date('Y-m-d H:i:s')
                        );

                        $this->db->where('username', $username);
                        if( $this->db->update('tbl_accounts', $dataupdate) !== TRUE ){
                            # cap nhat co loi
                            $msg = 'Cập nhật thông tìn tài khoản lỗi';
                        }else{
                            redirect(base_url() . 'auth');
                        }
                    }else{
                        # mat khau cu khong dung
                        $msg = 'Mật khậu cũ không đúng';
                    }
                }else{
                    # khong co thong tin
                    $msg = 'Không tìm thấy thông tin tài khoản';
                }
            }
        }

        $data['msg']     = $msg;
        $data['content'] = 'account/expired';
        $this->setlayout($data, NULL);
    }

    function activeacc(){
        $agent = array();
        $this->db->select('ext,password,callsoft');
        $this->db->where('status', 'on');
        $agent = $this->db->get('tbl_agent')->result_array();

        $user_conf_path         = dirname(APPPATH) . '/authconf/user.conf';
        $agents_ccs_conf_path   = dirname(APPPATH) . '/authconf/agents_ccs.conf';

        $agents_ccs_conf = '';
        $user_conf = '';

        if(isset($agent) AND !empty($agent)){
            foreach ($agent as $key => $value) {
                $ext        = $value['ext'];
                $password   = $value['password'];
                $callsoft   = $value['callsoft'];
                $fullname   = 'Agent_' . $ext;

                ############################################
                if( ! $agents_ccs_conf ){
                    $agents_ccs_conf = 'agent => ' . $ext . ',,' . $fullname . PHP_EOL;
                }else{
                    $agents_ccs_conf .= 'agent => ' . $ext . ',,' . $fullname . PHP_EOL;
                }

                ############################################
                if( ! $user_conf ){
                    $user_conf = '[' . $ext . ']' . PHP_EOL;
                }else{
                    $user_conf .= '[' . $ext . ']' . PHP_EOL;
                }
                $user_conf .= 'type=friend' . PHP_EOL;
                $user_conf .= 'port=5060' . PHP_EOL;
                $user_conf .= 'secret=' . $password . PHP_EOL;
                if( $callsoft == 'on' ){
                    $user_conf .= 'context=subscribers_office' . PHP_EOL;
                }else{
                    $user_conf .= 'context=subscribers' . PHP_EOL;
                }
                $user_conf .= 'host=dynamic' . PHP_EOL;
                $user_conf .= 'nat=no' . PHP_EOL;
                $user_conf .= 'disallow=all' . PHP_EOL;
                $user_conf .= 'allow=ulaw' . PHP_EOL;
                $user_conf .= 'allow=alaw' . PHP_EOL;
                $user_conf .= 'qualify=yes' . PHP_EOL;
                $user_conf .= 'dtmfmode=rfc2833' . PHP_EOL;
                $user_conf .= 'call-limit=1' . PHP_EOL . PHP_EOL;
            }
        }

        # Ghi thong tin vao file
        file_put_contents($agents_ccs_conf_path, $agents_ccs_conf);
        file_put_contents($user_conf_path, $user_conf);

        # Kich hoat
        if(0){
            #$url_rf = 'http://192.168.1.17/rs.php';
            #$ch = curl_init();
            #curl_setopt($ch, CURLOPT_URL            , $url_rf);
            #curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
            #$result = curl_exec($ch);
            #curl_close($ch);

            #if( $result ){
            #    echo 'SUCCESS';
            #}else{
            #    echo 'FAIL';
            #}
        }
    }

    function onoff(){
        $id     = isset($_POST['id']) ? $_POST['id'] : FALSE;
        $status = isset($_POST['status']) ? $_POST['status'] : 'off';
        $return = 'FAIL';

        if($id){
            $this->db->where('id', $id);
            if( $this->db->update('tbl_accounts', array('status' => $status)) !== FALSE){
                $this->db->where('id_account', $id);
                $this->db->update('tbl_agent', array('status' => $status));
                
                $return = 'SUCCESS';
            }
        }

        echo $return;
    }

    function dowloadfile(){
        $path_file = APPPATH . 'uploads/accounttmpl.xlsx';
        if( isset($path_file) AND !empty($path_file) ){
            if( file_exists($path_file) ){
                header("Content-Description: File Transfer"); 
                header("Content-Type: application/octet-stream"); 
                header("Content-Disposition: attachment; filename=" . basename($path_file));
                readfile ($path_file);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}