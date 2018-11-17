<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Except extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        if( $this->_role != 'data' ){
                echo 'Bạn không có quyền trong chức năng này.';
        }
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Data ngoại lệ', ''),
        );

        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Data giới thiệu', ''),
        );

        if ($this->input->is_ajax_request()) {
        	$dataResult = array();
            $total      = 0;

            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $limit      = $pageSize;
            $offset 	= ($page - 1) * $pageSize;

            $this->db->start_cache();
            $this->db->select('fil.id,fil.title,cen.name as cenName,sou.name as souName,fil.num_cus');
            $this->db->from('tbl_file_upload as fil');
            $this->db->join('tbl_centers as cen', 'cen.id=fil.id_center');
            $this->db->join('tbl_source as sou', 'sou.id=fil.id_source');
            $this->db->where('fil.isexception', 1);

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->limit($limit, $offset);
            $dataResult = $this->db->get()->result_array();

            $this->db->flush_cache();
            ####

            $return = array(
                'total' => $total,
                'data'  => $dataResult,
				'query' => $last_query,
            );

            echo json_encode($return);
            return;
        }

        $data['content'] = 'exception/index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function Sale($mobile){
        $rtn = FALSE;

        $this->db->select('id,last_sale_time');
        $this->db->where('mobile', $mobile);
        $this->db->where('sale', 1);
        $this->db->limit(1);
        $detail = $this->db->get('tbl_customer')->row_array();

        if(!isset($detail) OR empty($detail)){
            $rtn = TRUE;
        }

        return $rtn;
    }

    function Demo($mobile){ # > 6t true
        $rtn = FALSE;

        $this->db->select('id,demo,last_demo_time');
        $this->db->where('mobile', $mobile);
        $this->db->where('demo > 0');
        $this->db->limit(1);
        $detail = $this->db->get('tbl_customer')->row_array();
        if(!isset($detail) OR empty($detail)){
            $rtn = TRUE;
        }

        return $rtn;
    }

    function Appointment($mobile){ #> 15day true
        $rtn = FALSE;

        $this->db->select('id,app_date');
        $this->db->where('cus_mobile', $mobile);
        $this->db->where('last_app', 'on');
        $this->db->where('app_status !=', 'cancel');
        $this->db->limit(1);
        $detail = $this->db->get('tbl_appointments')->row_array();

        if(isset($detail) AND !empty($detail)){
            $id = $detail['id'];
            $app_date = $detail['app_date'];

            $daynow = date('Y-m-d');
            $day15 = date('Y-m-d', strtotime($daynow . ' - 15 days'));

            if(strtotime($app_date) < strtotime($day15)){
                $rtn = TRUE;
            }
        }else{
            $rtn = TRUE;
        }

        return $rtn;
    }

    function LeadPG($mobile){ # > 30 true
        $rtn = FALSE;

        $this->db->select('id,focus,last_time_focus');
        $this->db->where('mobile', $mobile);
        $this->db->where_in('focus', array('mkt', 'pg'));
        $this->db->limit(1);
        $detail = $this->db->get('tbl_customer')->row_array();

        if(isset($detail) AND !empty($detail)){
            $id = $detail['id'];
            $focus = $detail['focus'];
            $last_time_focus = $detail['last_time_focus'];

            $daynow = date('Y-m-d');
            $day30 = date('Y-m-d', strtotime($daynow . ' - 30 days'));

            if(strtotime($last_time_focus) < strtotime($day30)){
                $rtn = TRUE;
            }
        }else{
            $rtn = TRUE;
        }

        return $rtn;
    }

    function CheckLock($mobile){
        $rtn = FALSE;

        $this->db->select('id');
        $this->db->where('mobile', $mobile);
        $this->db->where('block', 1);
        $this->db->limit(1);
        if(!$this->db->count_all_results('tbl_customer')){
            $rtn = TRUE;
        }

        return $rtn;
    }

    function CheckExists($mobile){
        $rtn = false;
        $this->db->select('id');
        $this->db->where('mobile', $mobile);
        $this->db->limit(1);
        if($this->db->count_all_results('tbl_customer')){
            $rtn = true;
        }
        return $rtn;
    }

    function checkMobile($mobile){
        $rtn = FALSE;

        $mobile10 = array('086','096','097','098','032','033','034','035','036','037','038','039','-','090','093','089','070','079','077','076','078','-','091','094','088','083','084','085','081','082','-','092','-','099','052','056','058','059');
        $mobi3 = substr($mobile, 0, 3);

        if(strlen($mobile) == 10){
            if(in_array($mobi3, $mobile10)){
                $rtn = TRUE;
            }
        }
        return $rtn;
    }

    function check_title(){
        $title     = $this->input->post('title');
        $title     = strtolower(trim($title));
        $title     = convert_codau_sang_khongdau($title);

        $checkdt = array();
        $this->db->select('id');
        $this->db->where('title', $title);
        $checkdt = $this->db->get('tbl_file_upload')->row_array();

        if( isset($checkdt['id']) AND !empty($checkdt['id']) ){
            $this->form_validation->set_message('check_title', '<b>%s</b> đã có thông tin trong hệ thống.');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function AddCommandos($mobile=false){
    	$com_id_center = 6;
    	$com_id_department = 6;
    	$com_id_group = 54;

    	$detail = array();
    	$this->db->select('id,id_fileup,first_name,last_name,fullname,mobile,email,id_source,mkt,mkt_time_input,pg,pg_time_input,pg_code,id_location,lastcode,focus,last_time_focus,status');
    	$this->db->where('mobile', $mobile);
    	$this->db->limit(1);
    	$detail = $this->db->get('tbl_customer')->row_array();
		# update assign
    	if(isset($detail) AND !empty($detail)){
    		$this->db->where('mobile', $mobile);
    		$this->db->update('tbl_customer', array(
    			'status' => 'assign',
    			'id_center' => $com_id_center,
    			'id_department' => $com_id_department,
    			'id_group' => $com_id_group,
                'exception' => 'tku',
                'last_exception_time' => date('Y-m-d H:i:s')
			));

    		$id = $detail['id'];
    		$id_fileup = $detail['id_fileup'];
    		$first_name = $detail['first_name'];
    		$last_name = $detail['last_name'];
    		$fullname = $detail['fullname'];
    		$mobile = $detail['mobile'];
    		$email = $detail['email'];
    		$id_source = $detail['id_source'];
    		$mkt = $detail['mkt'];
    		$mkt_time_input = $detail['mkt_time_input'];
    		$pg = $detail['pg'];
    		$pg_time_input = $detail['pg_time_input'];
    		$pg_code = $detail['pg_code'];
    		$id_location = $detail['id_location'];
    		$lastcode = $detail['lastcode'];
    		$focus = $detail['focus'];
    		$last_time_focus = $detail['last_time_focus'];

	    	# insert group
	    	$db_com_group = $this->setdbconnect($com_id_group, 'group');
	    	if($db_com_group){
                $db_com_group->where('mobile', $mobile);
                $db_com_group->delete('tbl_customer');
                ######
				$dbInsertGroup = array(
				 	'id_link' => $id,
				 	'id_fileup' => $id_fileup,
					'first_name' => $first_name,
					'last_name' => $last_name,
					'fullname' => $fullname,
					'mobile' => $mobile,
					'email' => $email,
					'id_source' => $id_source,
					//'id_department' => $com_id_department,
					//'id_group' => $com_id_group,
                    'exception' => 'tku',
					'status' => 'new',
					'created_at' => date('Y-m-d H:i:s'),
					'created_by' => 'exception',
					'start_date' => date('Y-m-d'),
					'close_date' => date('Y-m-d')
				);
				$db_com_group->insert('tbl_customer', $dbInsertGroup);
			}
    	}
    }

    function fileup(){
    	$msg_success = '';
        $msg_error   = '';
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $validation = array(
                array(
                    'field' => 'title',
                    'label' => 'Tiêu đề upload',
                    'rules' => 'required|max_length[250]|callback_check_title',
                ),
                array(
                    'field' => 'fileSelect',
                    'label' => 'File upload',
                    'rules' => 'required|max_length[250]',
                ),
                array(
                    'field' => 'id_source',
                    'label' => 'Source',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'id_center',
                    'label' => 'Center',
                    'rules' => 'required',
                ),
            );

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run($this) !== FALSE ){
                $title = $this->input->post('title');
                $title = strtolower(trim($title));
                $title = convert_codau_sang_khongdau($title);
                $id_source = $this->input->post('id_source');
                $id_center = $this->input->post('id_center');

                if( isset($_FILES) AND !empty($_FILES) ){
                    $FileType = pathinfo($_FILES['uploadedFiles']['name'],PATHINFO_EXTENSION);
                    $upload_conf['upload_path']     = APPPATH . 'uploads/v2/';
                    $upload_conf['file_name']       = 'referen-'.date('YmdHis').'.'.$FileType;
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
                            'title'     => $title,
                            'alias'     => 'Exception',
                            'filename'  => $file_name,
                            'id_source' => $id_source,
                            'id_center' => $id_center,
                            'filetype'  => $FileType,
                            'isexception' => 1,
                            'updated_at'=> date('Y-m-d H:i:s'),
                            'updated_by'=> $this->_uid
                        );

                        $this->db->insert('tbl_file_upload', $datafile);
                        $id = $this->db->insert_id();

                        $result_file = $this->extract($id);

                        $msg_success = 'Upload file thành công';
                    }

                    if(!isset($msg_success) OR empty($msg_success)){
                        $msg_error = 'Read file error';
                    }
                }else{
                    $msg_error = 'File upload empty';
                }
            }
        }
        #sourct
        $source = array();
        $this->db->select('id,name');
        $this->db->where('isexception', 1);
        $source = $this->db->get('tbl_source')->result_array();
        #center
        $center = array();
        $this->db->select('id,name');
        $this->db->where('type', 'call');
        $this->db->where('status', 'on');
        $center = $this->db->get('tbl_centers')->result_array();

        $data['success'] = $msg_success;
        $data['error'] = $msg_error;
        $data['source'] = $source;
        $data['center'] = $center;
        $data['content'] = 'exception/detail';
        $this->setlayout($data, 'v2/dialog');
    }

    function extract($id=false){
        $this->db->where('id', $id);
        $this->db->update('tbl_file_upload', array('extract_start' => date('Y-m-d H:i:s')));

        $uploaddt = array();
        $this->db->select('id,filename,id_center,id_source');
        $this->db->where('id', $id);
        $this->db->where('status !=', 'success');
        $uploaddt = $this->db->get('tbl_file_upload')->row_array();

        $data_extract = array();
        if( isset($uploaddt) AND !empty($uploaddt) ){
            #$id_fileup  = $uploaddt['id'];
            $filename = $uploaddt['filename'];
            $id_center = $uploaddt['id_center'];
            $id_source = $uploaddt['id_source'];

            $filepath= APPPATH . 'uploads/v2/' . $filename;
            if( file_exists($filepath) ){
                # city
                $citycode = array();
                $citylist = array();
                $this->db->select('id, code');
                $this->db->where('status', 'on');
                $citylist = $this->db->get('tbl_city')->result_array();
                if( isset($citylist) AND !empty($citylist) ){
                    foreach ($citylist as $key => $value) {
                        if( ! isset($citycode[$value['code']]) ){
                            $citycode[$value['code']] = $value['id'];
                        }
                    }
                }
                # extract data
                require_once( APPPATH  . 'third_party/PHPLib/PHPExcel/IOFactory.php');
                $objPHPExcel = PHPExcel_IOFactory::load($filepath);
                $max_row = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

                set_time_limit(0);
                $count  = 0;
                $numsse = 0;
                $numdup = 0;
                $numerr = 0;
                $data_insert = array();
                $data_general = array();
                $data_check  = array();
                for ($row = 2; $row <= $max_row; $row++) {
                    $mobile = trim($objPHPExcel->getActiveSheet()->getCell("C" . $row)->getCalculatedValue());
                    $mobile = !mb_detect_encoding($mobile, 'UTF-8', TRUE) ? utf8_encode($mobile) : $mobile;
                    $mobile = preg_replace("/[^0-9]/", "", $mobile);
                    if( substr($mobile, 0, 1) != '0' ){
                        $mobile = '0'.$mobile;
                    }
                    $mobile = convert_mobile($mobile);

                    $itemLog = array();
                    if( $this->checkMobile($mobile) ){
                        $fullname = trim($objPHPExcel->getActiveSheet()->getCell("B" . $row)->getCalculatedValue());
                        $fullname = !mb_detect_encoding($fullname, 'UTF-8', TRUE) ? utf8_encode($fullname) : $fullname;
                        $fullname = convert_codau_sang_khongdau($fullname);
                        $fullname = strtoupper($fullname);

                        $first_name = '';
                        $last_name  = '';
                        $explodename= explode(' ', $fullname);
                        $last_name  = $explodename[count($explodename) - 1];
                        $first_name = trim(str_replace($last_name, '', $fullname));

                        $code_city = trim($objPHPExcel->getActiveSheet()->getCell("D" . $row)->getCalculatedValue());
                        $id_city = isset($citycode[$code_city]) ? intval($citycode[$code_city]) : 0;
                        $gender = trim($objPHPExcel->getActiveSheet()->getCell("E" . $row)->getCalculatedValue());

                        switch ( strtolower($gender) ) {
                            case 'nam':
                                $gender = 'Male';
                                break;

                            case 'nu':
                                $gender = 'Female';
                                break;
                            
                            default:
                                $gender = 'Other';
                                break;
                        }

                        $checkCus = TRUE;
                        if(!$this->Sale($mobile)){
                            $checkCus = FALSE;
                            $itemLog = array(
                                'id_file' => $id,
                                'id_source' => $id_source,
                                'mobile' => $mobile,
                                'status' => 'Sale',
                                'created_at' => date('Y-m-d H:i:s')
                            );
                        }

                        if($checkCus AND !$this->Demo($mobile)){
                            $checkCus = FALSE;
                            $itemLog = array(
                                'id_file' => $id,
                                'id_source' => $id_source,
                                'mobile' => $mobile,
                                'status' => 'Demo',
                                'created_at' => date('Y-m-d H:i:s')
                            );
                        }

                        if($checkCus AND !$this->Appointment($mobile)){
                            $checkCus = FALSE;
                            $itemLog = array(
                                'id_file' => $id,
                                'id_source' => $id_source,
                                'mobile' => $mobile,
                                'status' => 'Appointment',
                                'created_at' => date('Y-m-d H:i:s')
                            );
                        }

                        if($checkCus AND !$this->LeadPG($mobile)){
                            $checkCus = FALSE;
                            $itemLog = array(
                                'id_file' => $id,
                                'id_source' => $id_source,
                                'mobile' => $mobile,
                                'status' => 'LeadPG',
                                'created_at' => date('Y-m-d H:i:s')
                            );
                        }

                        if($checkCus AND !$this->CheckLock($mobile)){
                            $checkCus = FALSE;
                            $itemLog = array(
                                'id_file' => $id,
                                'id_source' => $id_source,
                                'mobile' => $mobile,
                                'status' => 'CheckLock',
                                'created_at' => date('Y-m-d H:i:s')
                            );
                        }

                        if($checkCus){
                        	$numsse++;
                            $existsCus = $this->CheckExists($mobile);
                            $data_general = array(
                                #'id_fileup' => $id_fileup,
                                #'id_location' => 0,
                                'first_name'=> $first_name,
                                'last_name' => $last_name,
                                'fullname' => $fullname,
                                'mobile' => $mobile,
                                'id_center' => $id_center,
                                'employeeCode' => $employeeCode,
                                'employeeInput' => date('Y-m-d H:i:s')
                            );

                            if($existsCus){
                                # delete
                                $delDetail = array();
                                $this->db->select('id,id_center,id_department,id_group,id_source');
                                $this->db->where('mobile', $mobile);
                                $this->db->limit(1);
                                $delDetail = $this->db->get('tbl_customer')->row_array();
                                if(isset($delDetail) AND !empty($delDetail)){
                                    $id_center = $delDetail['id_center'];
                                    if(isset($id_center) AND !empty($id_center)){
                                        $dbCenter = $this->setdbconnect($id_center, 'center');
                                        if($dbCenter){
                                            $dbCenter->where('mobile', $mobile);
                                            $dbCenter->delete('tbl_customer');
                                        }
                                    }

                                    $id_department = $delDetail['id_department'];
                                    if(isset($id_department) AND !empty($id_department) AND intval($id_department) > 90){
                                        $dbAuto = $this->autodbconnect();
                                        if($dbAuto){
                                            $dbAuto->where('mobile', $mobile);
                                            $dbAuto->delete('tbl_customer');
                                        }
                                    }else{
                                        $dbDepartment = $this->setdbconnect($id_department, 'department');
                                        if($dbDepartment){
                                            $dbDepartment->where('mobile', $mobile);
                                            $dbDepartment->delete('tbl_customer');
                                        }

                                        $id_group = $delDetail['id_group'];
                                        if(isset($id_group) AND !empty($id_group)){
                                            $dbGroup = $this->setdbconnect($id_group, 'group');
                                            if($dbGroup){
                                                $dbGroup->where('mobile', $mobile);
                                                $dbGroup->delete('tbl_customer');
                                            }
                                        }
                                    }

                                    if(!isset($delDetail['id_source']) OR empty($delDetail['id_source'])){
                                        $data_general['id_source'] = $id_source;
                                    }
                                    $data_general['id_department'] = null;
                                    $data_general['id_group'] = null;
                                    $data_general['id_agent'] = null;
                                    $data_general['status'] = 'redata';
                                    $data_general['id_call_status'] = null;
                                    $data_general['id_call_status_c1'] = null;
                                    $data_general['id_call_status_c2'] = null;
                                    $this->db->where('mobile', $mobile);
                                    $this->db->update('tbl_customer', $data_general);

                                    $itemLog = array(
                                        'id_file' => $id,
                                        'id_source' => $id_source,
                                        'mobile' => $mobile,
                                        'status' => 'success-update',
                                        'created_at' => date('Y-m-d H:i:s')
                                    );
                                }
                            }else{
                                $data_general['id_source'] = $id_source;
                                $data_general['status'] = 'data';
                                $data_general['created_at'] = date('Y-m-d H:i:s');

                                $this->db->insert('tbl_customer', $data_general);
                                $itemLog = array(
                                    'id_file' => $id,
                                    'id_source' => $id_source,
                                    'mobile' => $mobile,
                                    'status' => 'success-new',
                                    'created_at' => date('Y-m-d H:i:s')
                                );
                            }

                            $this->AddCommandos($mobile);
                        }
                    }else{
                        $numerr++;
                        $itemLog = array(
                            'id_file' => $id,
                            'id_source' => $id_source,
                            'mobile' => $mobile,
                            'status' => 'error',
                            'created_at' => date('Y-m-d H:i:s')
                        );
                    }

                    if(isset($itemLog) AND !empty($itemLog)){
                        $this->db->insert('exception_log', $itemLog);
                        $data_extract[] = $itemLog;
                    }
                }

                $this->db->where('id', $id);
        		$this->db->update('tbl_file_upload', array(
        			'num_cus' => $numsse,
        			'status' => 'success',
        			'extract_end' => date('Y-m-d H:i:s')
    			));
            }
        }

        if( isset($data_extract) AND !empty($data_extract) ){
            $fileName = 'result_upload_' . date('YmdHis') . '.csv';
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File Transfer');
            header("Content-type: text/csv");
            header("Content-Type: text/csv; charset=utf-8");
            header("Content-Disposition: attachment; filename={$fileName}");
            header("Expires: 0");
            header("Pragma: public");

            //$out = @fopen('php://output', 'w');
            $out = fopen(APPPATH . 'uploads/v2/result/customer/' . $fileName, 'w');
            fputcsv($out, array('stt', 'mobile', 'status'));

            $count = 0;
            foreach ($data_extract as $key => $value) {
                $count++;
                fputcsv($out, array($count, $value['mobile'], $value['status']));
            }

            fclose($out);
            //exit;

            return $fileName;
        }else{
            return false;
        }
    }
}