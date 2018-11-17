<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Cấu hình sms', ''),
        );

    	if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 0;

            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;

            $id_center  = isset($request['id_center']) ? $request['id_center'] : false;
            $id_department= isset($request['id_department']) ? $request['id_department'] : false;
            $inputsearch= isset($request['inputsearch']) ? $request['inputsearch'] : '';
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;
            
            $this->db->start_cache();

            $this->db->select('id,`name`,client_id,secret,`status`,updated_at');
            $this->db->from('sms_config');

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

    	$data['content'] = 'index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function detail($id=0){
    	$success= '';
        $error  = '';
        $detail = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
        	$detail = $_POST;

        	$validation = array(
        		array(
                    'field' => 'name',
                    'label' => 'Name',
                    'rules' => 'required|max_length[75]',
                ),
                array(
                    'field' => 'client_id',
                    'label' => 'ClientID',
                    'rules' => 'required|max_length[250]|is_unique[sms_config.client_id]',
                ),
                array(
                    'field' => 'secret',
                    'label' => 'Cecret',
                    'rules' => 'required|max_length[250]|is_unique[sms_config.secret]',
                ),
    		);

    		$this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run() !== FALSE ){
            	$name = $this->input->post('name');
				$client_id = $this->input->post('client_id');
				$secret = $this->input->post('secret');

				$dbInsert = array(
					'name' => $name,
					'client_id' => $client_id,
					'secret' => $secret,
					'status' => 'off'
				);

				$this->db->trans_begin();
				$this->db->insert('sms_config', $dbInsert);

				if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $error = 'Thêm mới không thành công';
                }else{
                    $this->db->trans_commit();
                    $success = 'Thêm mới thành công';
                }
            }
        }

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;

    	$data['content'] = 'detail';
    	$this->setlayout($data, 'v2/dialog');
    }

    function onoff(){
    	$id = isset($_POST['id']) ? $_POST['id'] : FALSE;
        $return = 'FAIL';
        if($id){
        	$this->db->trans_begin();
        	$this->db->update('sms_config', array('status' => 'off'));

            $this->db->where('id', $id);
            $this->db->update('sms_config', array('status' => 'on'));

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
            }else{
                $this->db->trans_commit();
                $return = 'SUCCESS';
            }
        }
        echo $return;
    }
}