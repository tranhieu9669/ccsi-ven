<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
CREATE
 	ALGORITHM = MERGE
VIEW `view_blacklist`
 	AS SELECT cus.`fullname`,black.`id`,black.`id_customer`,black.`mobile`,black.`comment`,black.`status`,black.`updated_at`,black.`updated_by`
	FROM `tbl_blacklist` AS black
	JOIN `tbl_customer` AS cus ON cus.`id`=black.`id_customer`
	ORDER BY black.id
 	WITH LOCAL  CHECK OPTION
*/
class Blacklist extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách khóa số', ''),
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
            $this->db->from('view_blacklist');
            $this->db->where('status', 'on');
            $this->db->order_by('id', 'ASC');

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->limit($limit, $offset);
            $dataResult = $this->db->get()->result_array();

            $this->db->flush_cache();

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
            '_wth_name'     => 180,
            '_wth_mobile'   => 120,
            '_wth_status'   => 60,
            '_wth_time'   	=> 165,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'blacklist/index';
    	$this->setlayout($data, 'v2/tmpl');
    }

    public function check_mobile(){
        $mobile = $this->input->post('mobile');

        $this->db->select('id,blacklist');
        $this->db->where('mobile', $mobile);
        $checkdt = $this->db->get('tbl_customer')->row_array();

        if( isset($checkdt) AND !empty($checkdt) ){
        	if( intval($checkdt['blacklist']) > 0 ){
        		$this->form_validation->set_message('check_mobile', '<b>%s</b> đã có trong danh sách khóa.');
        		return FALSE;
        	}else{
        		return TRUE;
        	}
        }else{
        	$this->form_validation->set_message('check_mobile', '<b>%s</b> không có trong danh sách.');
        	return FALSE;
        }
    }

    function detail($id=false){
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

            $check_mobile = '';
            if( ! $flag ){
            	$check_mobile = '|callback_check_mobile';
            }

            $validation = array(
            	array(
            		'field' => 'mobile',
                    'label' => 'Số điện thoại',
                    'rules' => 'required|max_length[15]' . $check_mobile,
        		),
        		array(
            		'field' => 'comment',
                    'label' => 'Ghi chú',
                    'rules' => 'required|max_length[250]',
        		),
        	);

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run($this) !== FALSE ){
				$mobile 	= $this->input->post('mobile');
				$comment 	= $this->input->post('comment');

				$this->db->trans_begin();

				if( ! $flag ){
					$customerdt = array();
					$this->db->select('id');
					$this->db->where('mobile', $mobile);
					$customerdt = $this->db->get('tbl_customer')->row_array();
					$id_customer= $customerdt['id'];
					# insert blacklist
					$blacklist = array(
						'id_customer' 	=> $id_customer,
						'mobile' 		=> $mobile,
						'comment' 		=> $comment,
						'updated_at' 	=> date('Y-m-d H:i:s'),
						'updated_by'	=> $this->_uid
					);
					$this->db->insert('tbl_blacklist', $blacklist);
					# update customer
					$this->db->where('mobile', $mobile);
					$this->db->update('tbl_customer', array('blacklist' => 1));
				}else{
					$blacklist = array(
						'status' 	=> 'off',
						'updated_at'=> date('Y-m-d H:i:s'),
						'updated_by'=> $this->_uid
					);
					$this->db->where('id', $id);
					$this->db->update('tbl_blacklist', $blacklist);
					# update customer
					$this->db->where('mobile', $mobile);
					$this->db->update('tbl_customer', array('blacklist' => 0));
				}

				if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $error      = 'Cập nhật thông tin không thành công';
                }else{
                    $this->db->trans_commit();
                    $success    = 'Cập nhật thông tin thành công';
                }
            }
        }

        if( empty($detail) AND $flag ){
        	$this->db->where('id', $id);
        	$detail = $this->db->get('view_blacklist')->row_array();
        }
    	
    	$data['success'] = $success;
        $data['error']   = $error;
    	$data['detail']  = $detail;
        $data['content'] = 'blacklist/detail';
        $this->setlayout($data, 'v2/dialog');
    }
}