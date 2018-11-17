<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Data extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        #echo convert_mobile('01201111111');
    	if( $this->_role != 'data' ){
                echo 'B?n không có quy?n trong ch?c nang này';
    	}
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Upload file dữ liệu khách hàng', ''),
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
            $this->db->select('id,title,source_name,id_center,num_cus,num_dup,num_error,updated_at,updated_by,status');
            $this->db->from('tbl_file_upload');
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
            '_wth_order'    => 60,
            '_wth_source'   => 150,
            '_wth_num_cus'  => 80,
            '_wth_num_dup'  => 80,
            '_wth_up_by'    => 120,
            '_wth_up_time'  => 165,
            '_wth_status'   => 80,
            '_wth_extract'  => 80,
        ));

        $data['content'] = 'index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function check_source(){
        $source = $this->input->post('source_name');
        $source = convert_codau_sang_khongdau($source);
        $source = strtoupper($source);

        $this->db->select('id');
        $this->db->where('name', $source);
        $check_dt = $this->db->get('tbl_source')->row_array();

        if( isset($check_dt['id']) AND !empty($check_dt['id']) ){
            $this->form_validation->set_message('check_source', '<b>%s</b> đã có thông tin trong hệ thống.');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function check_title(){
        $title     = $this->input->post('title');
        $title     = strtolower(trim($title));
        $title     = convert_codau_sang_khongdau($title);
        $id_source = $this->input->post('id_source');

        $checkdt = array();
        $this->db->select('id');
        $this->db->where('id_source', $id_source);
        $this->db->where('title', $title);
        $checkdt = $this->db->get('tbl_file_upload')->row_array();

        if( isset($checkdt['id']) AND !empty($checkdt['id']) ){
            $this->form_validation->set_message('check_title', '<b>%s</b> đã có thông tin trong hệ thống.');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function checkdup($mobile=false, $id_fileup=false, $db_center = false){
        if( strlen($mobile) > 9 AND strlen($mobile) < 12){
            $this->db->select('id,id_fileup');
            $this->db->where('mobile', $mobile);
            $detail = $this->db->get('tbl_customer')->row_array();
            
            if( isset($detail) AND !empty($detail) ){
                $id_customer    = $detail['id'];
                $id_file        = $detail['id_fileup'];

                $datadup = array(
                    'id_customer'=> $id_customer,
                    'mobile'     => $mobile,
                    'id_file'    => $id_file,
                    'id_fileup'  => $id_fileup
                );
                $db_center->insert('tbl_customer_duplicate', $datadup);

                return true;
            }
        }
        return false;
    }

    function fileup(){
    	$msg_success = '';
    	$msg_error 	 = '';

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
                    'field' => 'id_center',
                    'label' => 'Center',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'id_source_type',
                    'label' => 'Type',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'id_source',
                    'label' => 'Source',
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
                $title     = $this->input->post('title');
                $title     = strtolower(trim($title));
                $title     = convert_codau_sang_khongdau($title);
                $source_type = $this->input->post('id_source_type');
                $source_type = strtolower($source_type);
                $id_source = $this->input->post('id_source');
                $id_center = $this->input->post('id_center');
                $schedule  = $this->input->post('schedule');
                
                if( isset($_FILES) AND !empty($_FILES) ){
                    $FileType = pathinfo($_FILES['uploadedFiles']['name'],PATHINFO_EXTENSION);
                    $upload_conf['upload_path']     = APPPATH . 'uploads/v2/';
                    $upload_conf['file_name']       = date('YmdHis').'-'.$source_type.'-customer.'.$FileType;
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

                        $sourcedt = array();
                        $this->db->select('name');
                        $this->db->where('id', $id_source);
                        $sourcedt = $this->db->get('tbl_source')->row_array();

                        $source_name = isset($sourcedt['name']) ? $sourcedt['name'] : '';

                        $datafile = array(
                            'title'     => $title,
                            'alias'     => $alias,
                            'filename'  => $file_name,
                            'id_source' => $id_source,
                            'source_name'=> $source_name,
                            'id_center' => $id_center,
                            'filetype'  => $FileType,
                            'updated_at'=> date('Y-m-d H:i:s'),
                            'updated_by'=> $this->_uid
                        );

                        if($schedule){
                            $datafile['extract_schedule'] = $schedule.':00';
                            $this->db->insert('tbl_file_upload', $datafile);
                        }else{
                            $this->db->insert('tbl_file_upload', $datafile);
                            $id = $this->db->insert_id();

                            switch ($source_type) {
                                case 'pg':
                                    $result_file = $this->promotion($id);
                                    break;
                                
                                default:
                                    $result_file = $this->extractexcel($id);
                                    #$result_file = $this->extractexcel2($id);
                                    break;
                            }
                            if( isset($result_file) AND !empty($result_file) AND $result_file ){
                                $data['result_file'] = $result_file;
                            }
                        }
                        $msg_success = 'Upload file thành công';
                    }
                }else{
                    $msg_error = 'File upload empty';
                }
            }
        }

        #center
        $center = array();
        $this->db->select('id,name');
        $this->db->where('type', 'call');
        $this->db->where('status', 'on');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center'] = $center;

    	$data['success'] = $msg_success;
        $data['error']   = $msg_error;
    	$data['content'] = 'detail';
    	$this->setlayout($data, 'v2/dialog');
    }

    function extractexcel($id=false){
        $this->db->where('id', $id);
        $this->db->update('tbl_file_upload', array('extract_start' => date('Y-m-d H:i:s')));

        $uploaddt = array();
        $this->db->select('id,filename,id_source,source_name,id_center');
        $this->db->where('id', $id);
        $this->db->where('status !=', 'success');
        $uploaddt = $this->db->get('tbl_file_upload')->row_array();

        $data_extract = array();
        if( isset($uploaddt) AND !empty($uploaddt) ){
            $id_fileup  = $uploaddt['id'];
            $filename   = $uploaddt['filename'];
            $id_source  = $uploaddt['id_source'];
            $source_name= $uploaddt['source_name'];
            $id_center  = $uploaddt['id_center'];

            $db_center = $this->setdbconnect($id_center, 'center');
            #var_dump($db_center);

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

                    if( check_mobile($mobile) ){
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
                        $code_city = !mb_detect_encoding($code_city, 'UTF-8', TRUE) ? trim(utf8_encode($code_city)) : trim($code_city);
                        $id_city   = isset($citycode[$code_city]) ? intval($citycode[$code_city]) : 0;

                        $gender = trim($objPHPExcel->getActiveSheet()->getCell("E" . $row)->getCalculatedValue());
                        $gender = !mb_detect_encoding($gender, 'UTF-8', TRUE) ? utf8_encode($gender) : $gender;
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

                        $this->db->where('mobile', $mobile);
                        $checkdata = $this->db->get('tbl_customer')->row_array();
                        if(isset($checkdata) AND !empty($checkdata)){
                            $numdup++;
                            $data_extract[] = array(
                                'mobile' => $mobile,
                                'status' => 'duplicate'
                            );
                        }else{
                            $data_general = array(
                                'id_fileup' => $id_fileup,
                                'first_name'=> $first_name,
                                'last_name' => $last_name,
                                'fullname'  => $fullname,
                                'mobile'    => $mobile,
                                'id_source' => $id_source,
                                'source'    => $source_name,
                                'id_center' => $id_center,
                                'status'    => 'data',
                                'created_at'=> date('Y-m-d H:i:s'),
                            );
                            $this->db->insert('tbl_customer', $data_general);
                            $data_extract[] = array(
                                'mobile' => $mobile,
                                'status' => 'success'
                            );
                        }
                    }else{
                        $numerr++;
                        $data_extract[] = array(
                            'mobile' => $mobile,
                            'status' => 'error'
                        );
                    }
                }

                $this->db->where('id', $id_fileup);
                $this->db->update('tbl_file_upload', array('num_cus' => ($max_row - 1), 'num_dup' => $numdup, 'num_error' => $numerr, 'extract_end' => date('Y-m-d H:i:s'), 'status' => 'success'));
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

    function extractexcel2($id=false){
        $this->db->where('id', $id);
        $this->db->update('tbl_file_upload', array('extract_start' => date('Y-m-d H:i:s')));

        $uploaddt = array();
        $this->db->select('id,alias,filename,id_source,source_name,id_center');
        $this->db->where('id', $id);
        $this->db->where('status !=', 'success');
        $uploaddt = $this->db->get('tbl_file_upload')->row_array();

	   #ini_set("memory_limit",-1);
        $data_extract = array();
        if( isset($uploaddt) AND !empty($uploaddt) ){
            $id_fileup  = $uploaddt['id'];
            $alias      = $uploaddt['alias'];
            $filename   = $uploaddt['filename'];
            $id_source  = $uploaddt['id_source'];
            $source_name= $uploaddt['source_name'];
            $id_center  = $uploaddt['id_center'];

            $db_center = $this->setdbconnect($id_center, 'center');
            #var_dump($db_center);

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
                $numdup = 0;
                $numerr = 0;
                $data_insert = array();
                $data_update = array();
                $data_general = array();
                $data_check  = array();
                for ($row = 2; $row <= $max_row; $row++) {
                    $mobile = trim($objPHPExcel->getActiveSheet()->getCell("C" . $row)->getCalculatedValue());
                    $mobile = !mb_detect_encoding($mobile, 'UTF-8', TRUE) ? utf8_encode($mobile) : $mobile;
                    $mobile = preg_replace("/[^0-9]/", "", $mobile);
                    if( substr($mobile, 0, 1) != '0' ){
                        $mobile = '0'.$mobile;
                    }

                    if( strlen($mobile) > 9 AND strlen($mobile) < 12 ){
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
                        $code_city = !mb_detect_encoding($code_city, 'UTF-8', TRUE) ? trim(utf8_encode($code_city)) : trim($code_city);
                        $id_city   = isset($citycode[$code_city]) ? intval($citycode[$code_city]) : 0;

                        $gender = trim($objPHPExcel->getActiveSheet()->getCell("E" . $row)->getCalculatedValue());
                        $gender = !mb_detect_encoding($gender, 'UTF-8', TRUE) ? utf8_encode($gender) : $gender;
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

                        $db_center->select('id, appointment');
                        $db_center->where('mobile', $mobile);
                        $centerdt = $db_center->get('tbl_customer')->row_array();

                        if(!isset($centerdt['id']) OR empty($centerdt['id'])){
                            $data_insert[] = array(
                                'id_fileup' => $id_fileup,
                                'first_name'=> $first_name,
                                'last_name' => $last_name,
                                'fullname'  => $fullname,
                                'mobile'    => $mobile,
                                'gender'    => $gender,
                                'code_city' => $code_city,
                                'id_city'   => $id_city,
                                'id_source' => $id_source,
                                'source'    => $source_name,
                                'alias'    => $alias,
                                'created_at'=> date('Y-m-d H:i:s'),
                                'created_by'=> $this->_uid,
                                'start_date'=> date('Y-m-d')
                            );
                        }else{
                            /*if( !isset($centerdt['appointment']) OR empty($centerdt['appointment']) ){
                                $data_update[] = array(
                                    'id'        => $centerdt['id'],
                                    'id_fileup' => $id_fileup,
                                    'first_name'=> $first_name,
                                    'last_name' => $last_name,
                                    'fullname'  => $fullname,
                                    'mobile'    => $mobile,
                                    'gender'    => $gender,
                                    'code_city' => $code_city,
                                    'id_city'   => $id_city,
                                    'id_source' => $id_source,
                                    'source'    => $source_name,
                                    'alias'    => $alias,
                                    'status' => 'new',
                                    'created_at'=> date('Y-m-d H:i:s'),
                                    'created_by'=> $this->_uid,
                                    'start_date'=> date('Y-m-d')
                                );
                            }*/
							$numdup++;
                            $data_extract[] = array(
                                'mobile' => $mobile,
                                'status' => 'duplicate'
                            );
                        }
                    }else{
                        $numerr++;
                        $data_extract[] = array(
                            'mobile' => $mobile,
                            'status' => 'error'
                        );
                    }
                }

                $this->db->where('id', $id_fileup);
                $this->db->update('tbl_file_upload', array('num_cus' => ($max_row - 1), 'num_dup' => $numdup, 'num_error' => $numerr));

                if( isset($data_insert) AND !empty($data_insert) ){
                    if( $db_center->insert_batch('tbl_customer', $data_insert) !== FALSE ){
                        $this->db->where('id', $id);
                        $this->db->update('tbl_file_upload', array('extract_end' => date('Y-m-d H:i:s'), 'status' => 'success'));
                    }else{
                        $this->db->where('id', $id);
                        $this->db->update('tbl_file_upload', array('extract_end' => date('Y-m-d H:i:s'), 'status' => 'error'));
                    }
                }else{
                    $this->db->where('id', $id);
                    $this->db->update('tbl_file_upload', array('extract_end' => date('Y-m-d H:i:s'), 'status' => 'success'));
                }

                /*if(isset($data_update) AND !empty($data_update)){
                    $db_center->insert_batch('tbl_customer', $update_batch, 'id');
                }*/
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

    function promotion($id=false){
        $this->db->where('id', $id);
        $this->db->update('tbl_file_upload', array('extract_start' => date('Y-m-d H:i:s')));

        $uploaddt = array();
        $this->db->select('id,filename,id_source,source_name,id_center');
        $this->db->where('id', $id);
        $this->db->where('status !=', 'success');
        $uploaddt = $this->db->get('tbl_file_upload')->row_array();

        $data_extract = array();

        if( isset($uploaddt) AND !empty($uploaddt) ){
            $id_fileup  = $uploaddt['id'];
            $filename   = $uploaddt['filename'];
            $id_source  = $uploaddt['id_source'];
            $source_name= $uploaddt['source_name'];
            $id_center  = $uploaddt['id_center'];

            $db_center = $this->setdbconnect($id_center, 'center');

            $filepath= APPPATH . 'uploads/v2/' . $filename;
            if( file_exists($filepath) ){
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

                    $codepg = trim($objPHPExcel->getActiveSheet()->getCell("F" . $row)->getCalculatedValue());
                    $codepg = !mb_detect_encoding($codepg, 'UTF-8', TRUE) ? utf8_encode($codepg) : $codepg;

                    if( strlen($mobile) > 9 AND strlen($mobile) < 12 ){
                        $fullname = trim($objPHPExcel->getActiveSheet()->getCell("B" . $row)->getCalculatedValue());
                        $fullname = !mb_detect_encoding($fullname, 'UTF-8', TRUE) ? utf8_encode($fullname) : $fullname;
                        $fullname = convert_codau_sang_khongdau($fullname);
                        $fullname = strtoupper($fullname);

                        $firstname = '';
                        $lastname  = '';
                        $explodename= explode(' ', $fullname);
                        $lastname  = $explodename[count($explodename) - 1];
                        $firstname = trim(str_replace($lastname, '', $fullname));

                        $code_city = trim($objPHPExcel->getActiveSheet()->getCell("D" . $row)->getCalculatedValue());
                        $code_city = !mb_detect_encoding($code_city, 'UTF-8', TRUE) ? trim(utf8_encode($code_city)) : trim($code_city);
                        $id_city   = isset($citycode[$code_city]) ? intval($citycode[$code_city]) : 0;

                        $gender = trim($objPHPExcel->getActiveSheet()->getCell("E" . $row)->getCalculatedValue());
                        $gender = !mb_detect_encoding($gender, 'UTF-8', TRUE) ? utf8_encode($gender) : $gender;
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

                        # check data
                        $dt = array();
                        $this->db->select('id,id_center,id_department,id_group,id_source,source,mkt,pg,demo,sale,app,block');
                        $this->db->from('tbl_customer');
                        $this->db->where('mobile', $mobile);
                        $dt = $this->db->get()->row_array();

                        if( !isset($dt) OR empty($dt) ){ # dua vao luon
                            $count++;
                            // Insert Log
                            $this->db->insert('tbl_promotion_log',array(
                                'mobile' => $mobile,
                                'code' => $codepg,
                                'status' => 0,
                                'created_at' => date('Y-m-d H:i:s')
                            ));

                            # Them KH tong
                            $data_general = array(
                                'first_name'=> $firstname,
                                'last_name' => $lastname,
                                'fullname'  => $fullname,
                                'mobile'    => $mobile,
                                'pg'        => 1,
                                'pg_code'   => $codepg,
                                'pg_time_input' => date('Y-m-d H:i:s'),
                                'id_center' => $id_center,
                                'id_group'  => $id_group,
                                'id_source' => $id_source,
                                'source'    => $source_name,
                                'created_at'=> date('Y-m-d H:i:s'),
                            );
                            $this->db->insert('tbl_customer', $data_general);
                            $id_insert_general = $this->db->insert_id();

                            # Center
                            $data_center = array(
                                'id_link'   => $id_insert_general,
                                'first_name'=> $firstname,
                                'last_name' => $lastname,
                                'fullname'  => $fullname,
                                'mobile'    => $mobile,
                                'gender'    => $gender,
                                'pg'        => 1,
                                'pg_code'   => $codepg,
                                'pg_time_input' => date('Y-m-d H:i:s'),
                                'id_source' => $id_source,
                                'source'    => $source_name,
                                'created_at'=> date('Y-m-d H:i:s'),
                                'created_by'=> $this->_uid,
                                'start_date'=> date('Y-m-d')
                            );
                            $db_center->insert('tbl_customer', $data_center);
                        }else{
                            $id_cus = $dt['id'];
                            $cusdemo = $dt['demo'];
                            $cussale = $dt['sale'];
                            $cusapp = $dt['app'];
                            $cusblock = $dt['block'];
                            $cus_id_source = $dt['id_source'];
                            $cus_source = $dt['source'];
                            $delete_id_center = $dt['id_center'];
                            $delete_id_department = $dt['id_department'];
                            $delete_id_group = $dt['id_group'];
                            $cus_mkt = $dt['mkt'];
                            $cus_pg = $dt['pg'];

                            $appcheck = array();
                            if( intval($cussale) > 0 ){
                                // Insert Log
                                $this->db->insert('tbl_promotion_log', array(
                                    'mobile' => $mobile,
                                    'code' => $codepg,
                                    'status' => 1,  # status-promotion; KH đã có sale
                                    'created_at' => date('Y-m-d H:i:s'),
                                ));
                            }else if( intval($cusdemo) > 0 ){
                                // Insert Log
                                $this->db->insert('tbl_promotion_log', array(
                                    'mobile' => $mobile,
                                    'code' => $codepg,
                                    'status' => 2,  # status-promotion; KH đã có demo
                                    'created_at' => date('Y-m-d H:i:s'),
                                ));
                            }else if( intval($cusblock) > 0 ){
                                // Insert Log
                                $this->db->insert('tbl_promotion_log', array(
                                    'mobile' => $mobile,
                                    'code' => $codepg,
                                    'status' => 6,  # status-promotion; KH block
                                    'created_at' => date('Y-m-d H:i:s'),
                                ));
                            }else if( intval($cus_mkt) > 0 ){
                                // Insert Log
                                $this->db->insert('tbl_promotion_log', array(
                                    'mobile' => $mobile,
                                    'code' => $codepg,
                                    'status' => 12,  # status-promotion; KH MKT
                                    'created_at' => date('Y-m-d H:i:s'),
                                ));
                            }else if( intval($cus_pg) > 0 ){
                                // Insert Log
                                $this->db->insert('tbl_promotion_log', array(
                                    'mobile' => $mobile,
                                    'code' => $codepg,
                                    'status' => 13,  # status-promotion; KH PG
                                    'created_at' => date('Y-m-d H:i:s'),
                                ));
                            }else if( intval($cusapp) > 0 ){
                                $daynow = date('Y-m-d');
                                $daycheck = date('Y-m-d', strtotime($daynow . ' - 16 days'));
                                $this->db->select('id, app_datetime, crm_app_status');
                                $this->db->from('tbl_appointments');
                                $this->db->where('cus_mobile', $mobile);
                                $this->db->where('last_app', 'on');
                                $this->db->where('app_status !=', 'cancel');
                                $appcheck = $this->db->get()->row_array();

                                if(isset($appcheck) AND !empty($appcheck)){
                                    $app_datetime = $appcheck['app_datetime'];
                                    $crm_app_status = $appcheck['crm_app_status'];

                                    if( date('Y-m-d', strtotime($app_datetime)) >= date('Y-m-d') ){
                                        // Insert Log
                                        $this->db->insert('tbl_promotion_log', array(
                                            'mobile' => $mobile,
                                            'code' => $codepg,
                                            'status' => 3,  # status-promotion; KH có hẹn trong trương lai
                                            'datecheck' => $app_datetime,
                                            'created_at' => date('Y-m-d H:i:s'),
                                        ));
                                    }else if( date('Y-m-d', strtotime($app_datetime)) > date('Y-m-d', strtotime($daycheck)) ){
                                        $this->db->insert('tbl_promotion_log', array(
                                            'mobile' => $mobile,
                                            'code' => $codepg,
                                            'status' => 15,  # status-promotion; co hẹn trong 15 ngay
                                            'datecheck' => $app_datetime,
                                            'created_at' => date('Y-m-d H:i:s'),
                                        ));
                                    }else if( date('Y-m-d', strtotime($app_datetime)) > date('Y-m-d', strtotime('2018-01-01')) AND in_array(intval($crm_app_status), array(0,1,5)) ){
                                        try {
                                            # delete
                                            if( isset($delete_id_center) AND !empty($delete_id_center) ){
                                                $dellete_db_center = $this->setdbconnect($delete_id_center, 'center');
                                                $dellete_db_center->where('mobile', $mobile);
                                                $dellete_db_center->delete('tbl_customer');
                                            }
                                            
                                            if( isset($delete_id_department) AND !empty($delete_id_department) ){
                                                $dellete_db_department = $this->setdbconnect($delete_id_department, 'department');
                                                $dellete_db_department->where('mobile', $mobile);
                                                $dellete_db_department->delete('tbl_customer');
                                            }

                                            if( isset($delete_id_group) AND !empty($delete_id_group) ){
                                                $dellete_db_group = $this->setdbconnect($delete_id_group, 'group');
                                                $dellete_db_group->where('mobile', $mobile);
                                                $dellete_db_group->delete('tbl_customer');
                                            }

                                            # update vao he thong
                                            $data_general = array(
                                                'first_name'=> $firstname,
                                                'last_name' => $lastname,
                                                'fullname'  => $fullname,
                                                'pg'        => 1,
                                                'pg_code'   => $codepg,
                                                'pg_time_input' => date('Y-m-d H:i:s'),
                                                'id_center' => $id_center,
                                            );
                                            $this->db->where('mobile', $mobile);
                                            $this->db->update('tbl_customer', $data_general);

                                            // check ton tai trong center
                                            $db_center->select('id, id_group, id_department');
                                            $db_center->where('mobile', $mobile);
                                            $_checkdt = $db_center->get('tbl_customer')->row_array();

                                            if( isset($_checkdt) AND !empty($_checkdt)){
                                                $db_center->where('mobile', $mobile);
                                                $db_center->delete('tbl_customer');

                                                $_id_group_check = $_checkdt['id_group'];
                                                $_id_department_check = $_checkdt['id_department'];

                                                if( isset($_id_department_check) AND !empty($_id_department_check) ){
                                                    $del_db_department = $this->setdbconnect($_id_department_check, 'department');
                                                    $del_db_department->where('mobile', $mobile);
                                                    $del_db_department->delete('tbl_customer');
                                                }

                                                if( isset($_id_group_check) AND !empty($_id_group_check) ){
                                                    $del_db_group = $this->setdbconnect($_id_group_check, 'group');
                                                    $del_db_group->where('mobile', $mobile);
                                                    $del_db_group->delete('tbl_customer');
                                                }
                                            }

                                            $data_center = array(
                                                'id_link'   => $id_cus,
                                                'first_name'=> $firstname,
                                                'last_name' => $lastname,
                                                'fullname'  => $fullname,
                                                'mobile'    => $mobile,
                                                'gender'    => 'NU',
                                                'gender'    => $gender,
                                                'pg'        => 1,
                                                'pg_code'   => $codepg,
                                                'pg_time_input' => date('Y-m-d H:i:s'),
                                                'id_source' => $cus_id_source,
                                                'source'    => $cus_source,
                                                'created_at'=> date('Y-m-d H:i:s'),
                                                'created_by'=> $this->_uid,
                                                'start_date'=> date('Y-m-d')
                                            );
                                            $db_center->insert('tbl_customer', $data_center);
                                            // Insert Log
                                            $this->db->insert('tbl_promotion_log', array(
                                                'mobile' => $mobile,
                                                'code' => $codepg,
                                                'status' => 4,  # status-promotion; KH có hẹn cu, chưa tới
                                                'datecheck' => $app_datetime,
                                                'created_at' => date('Y-m-d H:i:s'),
                                            ));

                                            $count++;
                                        }
                                        catch(Exception $e) {
                                            $this->db->insert('tbl_promotion_log', array(
                                                'mobile' => $mobile,
                                                'code' => $codepg,
                                                'status' => 100,  # status-promotion;
                                                'created_at' => date('Y-m-d H:i:s'),
                                            ));
                                        }
                                    }else{
                                        // Insert Log
                                        $this->db->insert('tbl_promotion_log', array(
                                            'mobile' => $mobile,
                                            'code' => $codepg,
                                            'status' => 5,  # status-promotion; truoc 2017
                                            'datecheck' => '2017-01-01 09"30:00',
                                            'created_at' => date('Y-m-d H:i:s'),
                                        ));
                                    }
                                }else{
                                    // Insert Log
                                    $this->db->insert('tbl_promotion_log', array(
                                        'mobile' => $mobile,
                                        'code' => $codepg,
                                        'status' => 5,  # status-promotion; truoc 2017
                                        'datecheck' => '2017-01-01 09"30:00',
                                        'created_at' => date('Y-m-d H:i:s'),
                                    ));
                                }
                            }else{
                                #check lich hen
                                $this->db->select('id');
                                $this->db->from('tbl_appointments');
                                $this->db->where('cus_mobile', $mobile);
                                $this->db->where('last_app', 'on');
                                $this->db->where('app_status !=', 'cancel');
                                $appcheck = $this->db->get()->row_array();

                                if(!isset($appcheck) OR empty($appcheck)){
                                    try {
                                        # delete
                                        if( isset($delete_id_center) AND !empty($delete_id_center) ){
                                            $dellete_db_center = $this->setdbconnect($delete_id_center, 'center');
                                            $dellete_db_center->where('mobile', $mobile);
                                            $dellete_db_center->delete('tbl_customer');
                                        }
                                        
                                        if( isset($delete_id_department) AND !empty($delete_id_department) ){
                                            $dellete_db_department = $this->setdbconnect($delete_id_department, 'department');
                                            $dellete_db_department->where('mobile', $mobile);
                                            $dellete_db_department->delete('tbl_customer');
                                        }

                                        if( isset($delete_id_group) AND !empty($delete_id_group) ){
                                            $dellete_db_group = $this->setdbconnect($delete_id_group, 'group');
                                            $dellete_db_group->where('mobile', $mobile);
                                            $dellete_db_group->delete('tbl_customer');
                                        }

                                        $data_general = array(
                                            'first_name'=> $firstname,
                                            'last_name' => $lastname,
                                            'fullname'  => $fullname,
                                            'pg'        => 1,
                                            'pg_code'   => $codepg,
                                            'pg_time_input' => date('Y-m-d H:i:s'),
                                            'id_center' => $id_center,
                                        );

                                        $this->db->where('mobile', $mobile);
                                        $this->db->update('tbl_customer', $data_general);

                                        // check ton tai trong center
                                        $db_center->select('id, id_group, id_department');
                                        $db_center->where('mobile', $mobile);
                                        $_checkdt = $db_center->get('tbl_customer')->row_array();

                                        if( isset($_checkdt) AND !empty($_checkdt)){
                                            $db_center->where('mobile', $mobile);
                                            $db_center->delete('tbl_customer');

                                            $_id_group_check = $_checkdt['id_group'];
                                            $_id_department_check = $_checkdt['id_department'];

                                            if( isset($_id_department_check) AND !empty($_id_department_check) ){
                                                $del_db_department = $this->setdbconnect($_id_department_check, 'department');
                                                $del_db_department->where('mobile', $mobile);
                                                $del_db_department->delete('tbl_customer');
                                            }

                                            if( isset($_id_group_check) AND !empty($_id_group_check) ){
                                                $del_db_group = $this->setdbconnect($_id_group_check, 'group');
                                                $del_db_group->where('mobile', $mobile);
                                                $del_db_group->delete('tbl_customer');
                                            }
                                        }

                                        $data_center = array(
                                            'id_link'   => $id_cus,
                                            'first_name'=> $firstname,
                                            'last_name' => $lastname,
                                            'fullname'  => $fullname,
                                            'mobile'    => $mobile,
                                            'gender'    => 'NU',
                                            'gender'    => $gender,
                                            'pg'        => 1,
                                            'pg_code'   => $codepg,
                                            'pg_time_input' => date('Y-m-d H:i:s'),
                                            'id_source' => $cus_id_source,
                                            'source'    => $cus_source,
                                            'created_at'=> date('Y-m-d H:i:s'),
                                            'created_by'=> $this->_uid,
                                            'start_date'=> date('Y-m-d')
                                        );
                                        $db_center->insert('tbl_customer', $data_center);

                                        // Insert Log
                                        $this->db->insert('tbl_promotion_log', array(
                                            'mobile' => $mobile,
                                            'code' => $codepg,
                                            'status' => 7,  # status-promotion; chua co lịch
                                            'created_at' => date('Y-m-d H:i:s'),
                                        ));
                                    }
                                    catch(Exception $e) {
                                        $this->db->insert('tbl_promotion_log', array(
                                            'mobile' => $mobile,
                                            'code' => $codepg,
                                            'status' => 100,  # status-promotion;
                                            'created_at' => date('Y-m-d H:i:s'),
                                        ));
                                    }
                                }else{
                                    $app_datetime = $appcheck['app_datetime'];
                                    $crm_app_status = $appcheck['crm_app_status'];

                                    if( date('Y-m-d', strtotime($app_datetime)) >= date('Y-m-d') ){
                                        // Insert Log
                                        $this->db->insert('tbl_promotion_log', array(
                                            'mobile' => $mobile,
                                            'code' => $codepg,
                                            'status' => 3,  # status-promotion; KH có hẹn trong trương lai
                                            'datecheck' => $app_datetime,
                                            'created_at' => date('Y-m-d H:i:s'),
                                        ));
                                    }else if( date('Y-m-d', strtotime($app_datetime)) > date('Y-m-d', strtotime($daycheck)) ){
                                        $this->db->insert('tbl_promotion_log', array(
                                            'mobile' => $mobile,
                                            'code' => $codepg,
                                            'status' => 15,  # status-promotion; co hẹn trong 15 ngay
                                            'datecheck' => $app_datetime,
                                            'created_at' => date('Y-m-d H:i:s'),
                                        ));
                                    }else if( date('Y-m-d', strtotime($app_datetime)) > date('Y-m-d', strtotime('2018-01-01')) AND in_array(intval($crm_app_status), array(0,1,5)) ){
                                        try {
                                            # delete
                                            if( isset($delete_id_center) AND !empty($delete_id_center) ){
                                                $dellete_db_center = $this->setdbconnect($delete_id_center, 'center');
                                                $dellete_db_center->where('mobile', $mobile);
                                                $dellete_db_center->delete('tbl_customer');
                                            }
                                            
                                            if( isset($delete_id_department) AND !empty($delete_id_department) ){
                                                $dellete_db_department = $this->setdbconnect($delete_id_department, 'department');
                                                $dellete_db_department->where('mobile', $mobile);
                                                $dellete_db_department->delete('tbl_customer');
                                            }

                                            if( isset($delete_id_group) AND !empty($delete_id_group) ){
                                                $dellete_db_group = $this->setdbconnect($delete_id_group, 'group');
                                                $dellete_db_group->where('mobile', $mobile);
                                                $dellete_db_group->delete('tbl_customer');
                                            }

                                            # update vao he thong
                                            $data_general = array(
                                                'first_name'=> $firstname,
                                                'last_name' => $lastname,
                                                'fullname'  => $fullname,
                                                'pg'        => 1,
                                                'pg_code'   => $codepg,
                                                'pg_time_input' => date('Y-m-d H:i:s'),
                                                'id_center' => $id_center,
                                            );
                                            $this->db->where('mobile', $mobile);
                                            $this->db->update('tbl_customer', $data_general);

                                            // check ton tai trong center
                                            $db_center->select('id, id_group, id_department');
                                            $db_center->where('mobile', $mobile);
                                            $_checkdt = $db_center->get('tbl_customer')->row_array();

                                            if( isset($_checkdt) AND !empty($_checkdt)){
                                                $db_center->where('mobile', $mobile);
                                                $db_center->delete('tbl_customer');

                                                $_id_group_check = $_checkdt['id_group'];
                                                $_id_department_check = $_checkdt['id_department'];

                                                if( isset($_id_department_check) AND !empty($_id_department_check) ){
                                                    $del_db_department = $this->setdbconnect($_id_department_check, 'department');
                                                    $del_db_department->where('mobile', $mobile);
                                                    $del_db_department->delete('tbl_customer');
                                                }

                                                if( isset($_id_group_check) AND !empty($_id_group_check) ){
                                                    $del_db_group = $this->setdbconnect($_id_group_check, 'group');
                                                    $del_db_group->where('mobile', $mobile);
                                                    $del_db_group->delete('tbl_customer');
                                                }
                                            }

                                            $data_center = array(
                                                'id_link'   => $id_cus,
                                                'first_name'=> $firstname,
                                                'last_name' => $lastname,
                                                'fullname'  => $fullname,
                                                'mobile'    => $mobile,
                                                'gender'    => 'NU',
                                                'gender'    => $gender,
                                                'pg'        => 1,
                                                'pg_code'   => $codepg,
                                                'pg_time_input' => date('Y-m-d H:i:s'),
                                                'id_source' => $cus_id_source,
                                                'source'    => $cus_source,
                                                'created_at'=> date('Y-m-d H:i:s'),
                                                'created_by'=> $this->_uid,
                                                'start_date'=> date('Y-m-d')
                                            );
                                            $db_center->insert('tbl_customer', $data_center);
                                            // Insert Log
                                            $this->db->insert('tbl_promotion_log', array(
                                                'mobile' => $mobile,
                                                'code' => $codepg,
                                                'status' => 4,  # status-promotion; KH có hẹn cu, chưa tới
                                                'datecheck' => $app_datetime,
                                                'created_at' => date('Y-m-d H:i:s'),
                                            ));

                                            $count++;
                                        }
                                        catch(Exception $e) {
                                            $this->db->insert('tbl_promotion_log', array(
                                                'mobile' => $mobile,
                                                'code' => $codepg,
                                                'status' => 100,  # status-promotion;
                                                'created_at' => date('Y-m-d H:i:s'),
                                            ));
                                        }
                                    }else{
                                        // Insert Log
                                        $this->db->insert('tbl_promotion_log', array(
                                            'mobile' => $mobile,
                                            'code' => $codepg,
                                            'status' => 5,  # status-promotion; truoc 2017
                                            'datecheck' => '2017-01-01 09"30:00',
                                            'created_at' => date('Y-m-d H:i:s'),
                                        ));
                                    }
                                }
                            }
                        }
                    }else{
                        $numerr++;
                        $this->db->insert('tbl_promotion_log',array(
                            'mobile' => $mobile,
                            'code' => $codepg,
                            'status' => 10,
                            'created_at' => date('Y-m-d H:i:s')
                        ));
                    }
                    ################
                }

                $this->db->where('id', $id_fileup);
                $this->db->update('tbl_file_upload', array('num_cus' => ($max_row - 1), 'num_dup' => $count, 'num_error' => $numerr));

                $this->db->where('id', $id);
                $this->db->update('tbl_file_upload', array('extract_end' => date('Y-m-d H:i:s'), 'status' => 'success'));
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

    function checkpg($mobile=false){

        return false;
    }
}
