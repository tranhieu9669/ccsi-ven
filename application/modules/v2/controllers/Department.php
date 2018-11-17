<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
CREATE 
    ALGORITHM=MERGE 
VIEW `view_departments`  
    AS  select `cen`.`name` AS `cenname`,`dep`.`id` AS `id`,`dep`.`name` AS `depname`,`dep`.`first_ext` AS `first_ext`,`dep`.`con` AS `con`,`dep`.`cagent` AS `cagent`,`dep`.`coff` AS `coff`,`dep`.`status` AS `status` 
    from (`tbl_departments` `dep` join `tbl_centers` `cen` on((`cen`.`id` = `dep`.`id_center`))) where (`cen`.`status` = 'on') WITH LOCAL CHECK OPTION ;
*/
class Department extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách Trung tâm', base_url() . 'center'),
            array('Danh sách Phòng ban', ''),
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
            $this->db->from('view_departments');
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
            '_wth_num'      => 60,
            '_wth_name'     => 180,
            '_wth_status'   => 65,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'department/index';
    	$this->setlayout($data, 'v2/tmpl');
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
            $validation = array();

            $hdfirst_ext   = $this->input->post('hdfirst_ext');
            $first_ext     = $this->input->post('first_ext');
            
            if( $flag ){
                if( $hdfirst_ext != $first_ext ){
                    $validation[] = array(
                        'field' => 'first_ext',
                        'label' => 'Đầu số',
                        'rules' => 'required|integer|max_length[1]|is_unique[tbl_departments.first_ext]',
                    );
                }else{
                    $validation[] = array(
                        'field' => 'first_ext',
                        'label' => 'Đầu số',
                        'rules' => 'required|integer|max_length[1]',
                    );
                }
            }else{
                $validation[] = array(
                    'field' => 'first_ext',
                    'label' => 'Đầu số',
                    'rules' => 'required|integer|max_length[1]|is_unique[tbl_departments.first_ext]',
                );
            }

            $validation[] = array(
                'field' => 'id_center',
                'label' => 'Trung tâm',
                'rules' => 'required',
            );
            $validation[] = array(
                'field' => 'name',
                'label' => 'Tên Trung tâm',
                'rules' => 'required|max_length[150]',
            );

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run() !== FALSE ){
                $name       = trim($this->input->post('name'));
                $id_center  = $this->input->post('id_center');

                $this->db->trans_begin();

                $dbdepartment = array(
                    'id_center' => $id_center,
                    'name'      => $name,
                    'first_ext' => $first_ext,
                    'updated_at'=> date('Y-m-d H:i:s'),
                    'updated_by'=> $this->_uid,
                );

                if( ! $flag ){
                    $dbdepartment['status'] = 'off';
                    $this->db->insert('tbl_departments', $dbdepartment);
                    $id = $this->db->insert_id();
                }else{
                    $this->db->where('id', $id);
                    $this->db->update('tbl_departments', $dbdepartment);
                }

                # Luu thong tin log thay doi
                $departments_log = array(
                    'id_department' => $id,
                    'data'      => json_encode($dbdepartment),
                    'updated_at'=> date('Y-m-d H:i:s'),
                    'updated_by'=> $this->_uid
                );
                $this->db->insert('tbl_departments_log', $departments_log);

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
            $detail = $this->db->get('tbl_departments')->row_array();
        }

        $center = array();
        $this->db->where('status', 'on');
        $this->db->where('type', 'call');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center']  = $center;

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
        $data['content'] = 'department/detail';
        $this->setlayout($data, 'v2/dialog');
    }

    function onoff(){
        $id     = isset($_POST['id']) ? $_POST['id'] : FALSE;
        $status = isset($_POST['status']) ? $_POST['status'] : 'off';
        $return = 'FAIL';

        if($id){
            $this->db->where('id', $id);
            if( $this->db->update('tbl_departments', array('status' => $status)) !== FALSE){                
                $return = 'SUCCESS';
            }
        }

        echo $return;
    }
}