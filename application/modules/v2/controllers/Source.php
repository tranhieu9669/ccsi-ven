<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Source extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách nguồn dữ liệu', ''),
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
            $this->db->select('id, name, status');
            $this->db->from('tbl_source');
            $this->db->order_by('id');

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
            '_wth_status'   => 65,
            '_wth_time'   	=> 180,
        ));

        $data['content'] = 'source/index';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function detail(){
    	$success= '';
		$error 	= '';
    	$detail = array();
    	if($_SERVER['REQUEST_METHOD'] === 'POST'){
    		$detail = $_POST;

    		$validation = array(
    			array(
	                'field' => 'name',
	                'label' => 'Nguồn dữ liệu',
	                'rules' => 'required|max_length[150]|is_unique[tbl_source.name]',
	            )
			);

			$this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run() !== FALSE ){
            	$name = $this->input->post('name');

            	$this->db->trans_begin();

            	$this->db->insert('tbl_source', array('name' => $name, 'status' => 'off', 'created_at' => date('Y-m-d H:i:s')));

            	if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $error      = 'Cập nhật thông tin không thành công';
                }else{
                    $this->db->trans_commit();
                    $success    = 'Cập nhật thông tin thành công';
                }
            }
    	}

    	$data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
    	$data['content'] = 'source/detail';
    	$this->setlayout($data, 'v2/dialog');
    }

    function onoff(){
        $id     = isset($_POST['id']) ? $_POST['id'] : FALSE;
        $status = isset($_POST['status']) ? $_POST['status'] : 'off';
        $return = 'FAIL';

        if($id){
            $this->db->where('id', $id);
            if( $this->db->update('tbl_source', array('status' => $status)) !== FALSE){
                $return = 'SUCCESS';
            }
        }

        echo $return;
    }
}