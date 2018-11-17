<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Request extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        if($this->_role != 'department'){
            die('Bạn không có quyền vào đây');
        }
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách yêu cầu hỗ trợ', ''),
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

            $id_categories = isset($request['id_categories']) ? $request['id_categories'] : false;
            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;

            $this->db->start_cache();

            $this->db->select('rq.id,rq.mobile,rq.request_at,rq.request_note,rc.name,rq.approved_at,rq.approved_by,rq.approved_note,rq.approved_status');
            $this->db->from('tbl_request as rq');
            $this->db->join('tbl_request_categories as rc', 'rc.id=rq.id_categories and rc.status="on"');
            $this->db->where('request_id', $this->_id_agent);
            $this->db->order_by('rq.request_at', 'desc');

            if($inputsearch){
                $this->db->where('rq.mobile like "%'.$inputsearch.'%"');
            }else{
                if($id_categories){
                    $this->db->where('rq.id_categories', $id_categories);
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

        $categories = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $categories = $this->db->get('tbl_request_categories')->result_array();
        $data['categories'] = $categories;

    	$data['content'] = 'request/index';
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
                    'field' => 'mobile',
                    'label' => 'Điện thoại',
                    'rules' => 'required|max_length[11]|min_length[10]',
                ),
                array(
                    'field' => 'id_categories',
                    'label' => 'Danh mục',
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
            	$mobile = $this->input->post('mobile');
				$id_categories = $this->input->post('id_categories');

				$this->db->trans_begin();

				$request = array(
					'request_ip' => '',
					'request_id' => $this->_id_agent,
					'request_at' => date('Y-m-d H:i:s'),
					'request_by' => $this->_uid,
					'mobile' => $mobile,
					'id_categories' => $id_categories,
				);

				if( $id AND $id > 0 ){
					$this->db->where('id', $id);
                    $this->db->update('tbl_request', $request);
                }else{
                    $this->db->insert('tbl_request', $request);
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

    	$categories = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $categories = $this->db->get('tbl_request_categories')->result_array();
        $data['categories'] = $categories;

        $data['success'] = $success;
        $data['error']   = $error;

    	$data['content'] = 'request/detail';
        $this->setlayout($data, 'v2/dialog');
    }
}