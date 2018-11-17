<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Group extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách Trung tâm', base_url() . 'center'),
            array('Danh sách Phòng ban', base_url() . 'department'),
            array('Danh sách Nhóm', ''),
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
            $this->db->select('gr.id,ce.name as cename,de.name as dename,gr.name as grname,gr.status');
            $this->db->from('tbl_groups as gr');
            $this->db->join('tbl_departments as de', 'de.id=gr.id_department and de.status="on"');
            $this->db->join('tbl_centers as ce', 'ce.id=gr.id_center and ce.status="on"');
            $this->db->where('ce.type', 'call');
			$this->db->order_by('gr.id_center');
            $this->db->order_by('gr.id_department');
			$this->db->order_by('gr.id');

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

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 60,
            '_wth_num'      => 60,
            '_wth_status'   => 65,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'group/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function detail($id=0){
        $flag   = FALSE;
        if( $id AND $id > 0 ){
            $flag   = TRUE;
        }

        $success= '';
        $error  = '';
        $detail = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $validation = array(
                array(
                    'field' => 'id_center',
                    'label' => 'Trung tâm',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'id_department',
                    'label' => 'Phòng ban',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'name',
                    'label' => 'Tên Nhóm',
                    'rules' => 'required|max_length[150]',
                ),
                array(
                    'field' => 'host',
                    'label' => 'Host',
                    'rules' => 'required|max_length[150]',
                ),
                array(
                    'field' => 'dbname',
                    'label' => 'DB Name',
                    'rules' => 'required|max_length[150]',
                ),
                array(
                    'field' => 'user',
                    'label' => 'Username',
                    'rules' => 'required|max_length[150]',
                )
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
                $name   = trim($this->input->post('name'));
                $host   = $this->input->post('host');
                $dbname = $this->input->post('dbname');
                $user   = $this->input->post('user');
                $pass   = $this->input->post('pass');
                $port   = 3306;

                $this->db->trans_begin();

                $dbgroup = array(
                    'id_center'     => $id_center,
                    'id_department' => $id_department,
                    'name'          => $name,
                    'host'          => $host,
                    'dbname'        => $dbname,
                    'user'          => $user,
                    'pass'          => $pass,
                    'port'          => $port,
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'updated_by'    => $this->_uid,
                );

                if( ! $flag ){
                    $dbgroup['status'] = 'off';
                    $this->db->insert('tbl_groups', $dbgroup);
                    $id = $this->db->insert_id();
                }else{
                    $this->db->where('id', $id);
                    $this->db->update('tbl_groups', $dbgroup);
                }

                # Luu thong tin log thay doi
                $groups_log = array(
                    'id_group'  => $id,
                    'data'      => json_encode($dbgroup),
                    'updated_at'=> date('Y-m-d H:i:s'),
                    'updated_by'=> $this->_uid
                );
                $this->db->insert('tbl_groups_log', $groups_log);

                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $error      = 'Cập nhật thông tin không thành công';
                }else{
                    $this->db->trans_commit();
                    $success    = 'Cập nhật thông tin thành công';
                }
            }
            $detail = $_POST;
        }

        if( $flag AND ( ! isset($detail) OR empty($detail) ) ){
            $this->db->where('id', $id);
            $detail = $this->db->get('tbl_groups')->row_array();
        }

        if( isset($detail['id_center']) AND !empty($detail['id_center']) ){
            $department = array();
            $this->db->where('status', 'on');
            $this->db->where('id_center', $detail['id_center']);
            $department = $this->db->get('tbl_departments')->result_array();
            $data['department'] = $department;
        }

        $center = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'call');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center']  = $center;

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
        $data['content'] = 'group/detail';
        $this->setlayout($data, 'v2/dialog');
    }

    function onoff(){
        $id     = isset($_POST['id']) ? $_POST['id'] : FALSE;
        $status = isset($_POST['status']) ? $_POST['status'] : 'off';
        $return = 'FAIL';

        if($id){
            $this->db->where('id', $id);
            if( $this->db->update('tbl_groups', array('status' => $status)) !== FALSE){                
                $return = 'SUCCESS';
            }
        }

        echo $return;
    }
}