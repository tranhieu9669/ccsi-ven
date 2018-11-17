<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        $data['nm_select'] = 'home';
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
            $this->db->select('title, source_name, num_cus, num_dup, num_error, updated_at, updated_by, status');
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
            '_wth_num_dup'	=> 80,
            '_wth_up_by'	=> 120,
            '_wth_up_time'	=> 165,
            '_wth_status'	=> 80,
            '_wth_extract'	=> 80,
        ));

    	$data['content'] = 'upload/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function dowloadfile(){
        $path_file = APPPATH . 'uploads/customertmpl.xlsx';
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

    function detail($id=false){
    	$msg_success    = '';
        $msg_error      = '';

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
            	$title 	   = $this->input->post('title');
                $title     = strtolower(trim($title));
                $title     = convert_codau_sang_khongdau($title);
                $id_source = $this->input->post('id_source');
                $id_center = $this->input->post('id_center');
                
	    		if( isset($_FILES) AND !empty($_FILES) ){
	    			$FileType = pathinfo($_FILES['uploadedFiles']['name'],PATHINFO_EXTENSION);
	    			$upload_conf['upload_path']  	= APPPATH . 'uploads/v2/';
		            $upload_conf['file_name']    	= date('YmdHis') . '-customer.' . $FileType;
		            $upload_conf['allowed_types']	= 'xls|xlsx';
		            $upload_conf['overwrite']    	= false;
		            $upload_conf['max_size']     	= 1024 * 1;
		            $upload_conf['encrypt_name'] 	= TRUE;

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
		                	'title'		=> $title,
		                	'filename'	=> $file_name,
                            'id_source' => $id_source,
                            'source_name'=> $source_name,
                            'id_center' => $id_center,
		                	'filetype' 	=> $FileType,
		                	'updated_at'=> date('Y-m-d H:i:s'),
		                	'updated_by'=> $this->_uid
	                	);

	                	$this->db->insert('tbl_file_upload', $datafile);
                        $id = $this->db->insert_id();

                        $result_file = $this->extractexcel($id);

                        if( isset($result_file) AND !empty($result_file) AND $result_file ){
                            $data['result_file'] = $result_file;
                        }

	                	$msg_success = 'Upload file thành công';
		            }
	    		}else{
	    			$msg_error = 'File upload empty';
	    		}
            }
    	}

        #source
        $source = array();
        $this->db->select('id,name');
        $source = $this->db->get('tbl_source')->result_array();
        $data['source'] = $source;

        #center
        $center = array();
        $this->db->select('id,name');
        $this->db->where('type', 'call');
        $this->db->where('status', 'on');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center'] = $center;

    	$data['success'] = $msg_success;
        $data['error']   = $msg_error;
    	$data['content'] = 'upload/detail';
    	$this->setlayout($data, 'v2/dialog');
    }

    function checkdup($mobile=false, $id_fileup=false, $db_center = false){
    	$db_center->select('id, id_fileup');
    	$db_center->where('mobile', $mobile);
    	$detail = $db_center->get('tbl_customer')->row_array();
    	if( isset($detail) AND !empty($detail) ){
    		$id_customer 	= $detail['id'];
    		$id_file  		= $detail['id_fileup'];

    		$datadup = array(
    			'id_customer'=> $id_customer,
    			'mobile' 	 => $mobile,
    			'id_file'	 => $id_file,
    			'id_fileup'  => $id_fileup
			);
			$db_center->insert('tbl_customer_duplicate', $datadup);

    		return true;
    	}
    	return false;
    }

    /*
    Fullname 	= B
    Mobile 		= C
    Source 		= D
    Gender 		= E
    Birthday 	= F
    */

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
                $data_check  = array();
                for ($row = 2; $row <= $max_row; $row++) {
                    $mobile = trim($objPHPExcel->getActiveSheet()->getCell("C" . $row)->getCalculatedValue());
                    $mobile = !mb_detect_encoding($mobile, 'UTF-8', TRUE) ? utf8_encode($mobile) : $mobile;
                    $mobile = preg_replace("/[^0-9]/", "", $mobile);
                    if( substr($mobile, 0, 1) != '0' ){
                        $mobile = '0'.$mobile;
                    }

                    if( strlen($mobile) >= 10 ){
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

                        if( ! $this->checkdup($mobile, $id, $db_center) AND ! in_array($mobile, $data_check) ){
                            array_push($data_check, $mobile);

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
                                'created_at'=> date('Y-m-d H:i:s'),
                                'created_by'=> $this->_uid,
                                'start_date'=> date('Y-m-d')
                            );

                            $data_extract[] = array(
                                'mobile' => $mobile,
                                'status' => 'success'
                            );
                        }else{
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