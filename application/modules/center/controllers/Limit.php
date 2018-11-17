<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Limit extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        if($this->_role != 'center'){
            die('Bạn không có quyền trong trang này');
        }
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
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;

            $id_center  = isset($request['id_center']) ? $request['id_center'] : FALSE;
            $startdate  = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            
            $this->db->start_cache();
            $this->db->select('cen.id,cen.date,fra.name,cen.limited,cen.appointment');
            $this->db->from('tbl_limit_center as cen');
            $this->db->join('tbl_frametime as fra', 'cen.id_frametime=fra.id');

            $this->db->where('cen.id_center', $id_center);
            $this->db->where('cen.date', $startdate);

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
            '_wth_order' => 60,
            '_wth_date' => 160,
            '_wth_name' => 100,
            '_wth_limited' => 130,
            '_wth_appointment' => 130,
            '_wth_action' => 60,
        ));

        $center = array();
        $this->db->select('id,name');
        $this->db->where('type', 'spa');
        $this->db->where('status', 'on');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center'] = $center;

        $data['content'] = 'limit/center/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function detail(){
        $success= '';
        $error  = '';
        $detail = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $detail = $_POST;
			
			$data_log = $detail;
            $insert_log = array(
                'type' => 'LimitC',
                'data' => json_encode($data_log),
                'created_by' => $this->_uid,
                'created_at' => date('Y-m-d H:i:s')
            );
            write_log($insert_log);
			
            $validation = array(
                array(
                    'field' => 'startdate',
                    'label' => 'Từ ngày',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'enddate',
                    'label' => 'Đến ngày',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'id_center',
                    'label' => 'Trung tâm',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'id_frametime[]',
                    'label' => 'Ca hẹn',
                    'rules' => 'required',
                ),
                array(
                    'field' => 'limited',
                    'label' => 'Giới hạn',
                    'rules' => 'trim|required',
                ),
            );
            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run() !== FALSE ){
                $startdate = $this->input->post('startdate');
                $enddate = $this->input->post('enddate');
                $id_center = $this->input->post('id_center');
                $frametime = $this->input->post('id_frametime');
                $limited = $this->input->post('limited');

                #$frametime = array();
                #$this->db->select('id');
                #$this->db->where('status', 'on');
                #$frametime = $this->db->get('tbl_frametime')->result_array();

                $db_insert = array();
                $db_update = array();

                $startdiff = new DateTime($startdate);
                $enddiff   = new DateTime($enddate);
                $diff      = $enddiff->diff($startdiff)->days;
                for ($i=0; $i <= $diff; $i++) {
                    $date = date('Y-m-d', strtotime($startdate . ' + ' . $i . ' days'));
                    if(isset($frametime) AND !empty($frametime)){
                        foreach ($frametime as $key => $value) {
                            //$id_frametime = $value['id'];
                            $id_frametime = $value;
                            # check ton tai
                            $this->db->select('id');
                            $this->db->where('date', $date);
                            $this->db->where('id_center', $id_center);
                            $this->db->where('id_frametime', $id_frametime);
                            $checkdt = $this->db->get('tbl_limit_center')->row_array();

                            if( !isset($checkdt) OR empty($checkdt) ){
                                $db_insert[] = array(
                                    'date' => $date,
                                    'id_center' => $id_center,
                                    'id_frametime' => $id_frametime,
                                    'limited' => $limited,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => $this->_uid,
                                );
                            }else{
                                $db_update[] = array(
                                    'id' => $checkdt['id'],
                                    'date' => $date,
                                    'id_center' => $id_center,
                                    'id_frametime' => $id_frametime,
                                    'limited' => $limited,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => $this->_uid,
                                );
                            }
                        }
                    }
                }

                $this->db->trans_begin();

                if(isset($db_insert) AND !empty($db_insert)){
                    $this->db->insert_batch('tbl_limit_center', $db_insert);
                    $success = 'Cập nhật thông tin thành công';
                }

                if(isset($db_update) AND !empty($db_update)){
                    $this->db->update_batch('tbl_limit_center', $db_update, 'id');
                    $success = 'Cập nhật thông tin thành công';
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

        $center = array();
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'spa');
        $this->db->order_by('id');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center']  = $center;

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
        $data['content'] = 'limit/center/detail';
        $this->setlayout($data, 'v2/dialog');
    }

    function department(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách tài khoản', ''),
        );

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
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;

            $id_center_call = isset($request['id_center_call']) ? $request['id_center_call'] : FALSE;
            $id_department = isset($request['id_department']) ? $request['id_department'] : FALSE;
            $id_center_spa = isset($request['id_center_spa']) ? $request['id_center_spa'] : FALSE;
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            
            $this->db->start_cache();
            $this->db->select('dep.id,dep.date,fra.name,dep.limited,dep.appointment');
            $this->db->from('tbl_limit_department as dep');
            $this->db->join('tbl_frametime as fra', 'dep.id_frametime=fra.id');

            $this->db->where('id_center_call', $id_center_call);
            $this->db->where('id_department', $id_department);
            $this->db->where('id_center_spa', $id_center_spa);
            $this->db->where('dep.date', $startdate);

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
            '_wth_order' => 60,
            '_wth_date' => 160,
            '_wth_name' => 100,
            '_wth_limited' => 130,
            '_wth_appointment' => 130,
            '_wth_action' => 60,
        ));

        $center_call = array();
        $this->db->select('id,name');
        $this->db->where('type', 'call');
        $this->db->where('status', 'on');
        $center_call = $this->db->get('tbl_centers')->result_array();
        $data['center_call'] = $center_call;

        $center_spa = array();
        $this->db->select('id,name');
        $this->db->where('type', 'spa');
        $this->db->where('status', 'on');
        $center_spa = $this->db->get('tbl_centers')->result_array();
        $data['center_spa'] = $center_spa;

        $data['content'] = 'limit/department/index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function depdetail(){
        $success= '';
        $error  = '';
        $detail = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $detail = $_POST;
			
			$data_log = $detail;
            $insert_log = array(
                'type' => 'LimitD',
                'data' => json_encode($data_log),
                'created_by' => $this->_uid,
                'created_at' => date('Y-m-d H:i:s')
            );
            write_log($insert_log);
	
            $validation = array(
                array(
                    'field' => 'startdate',
                    'label' => 'Từ ngày',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'enddate',
                    'label' => 'Đến ngày',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'id_center_spa',
                    'label' => 'Trung tâm spa',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'id_center_call',
                    'label' => 'Trung tâm call',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'id_department',
                    'label' => 'Phòng call',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'limited',
                    'label' => 'Giới hạn',
                    'rules' => 'trim|required',
                ),
            );
            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run() !== FALSE ){
                $startdate = $this->input->post('startdate');
                $enddate = $this->input->post('enddate');
                $id_center_call = $this->input->post('id_center_call');
                $id_center_spa = $this->input->post('id_center_spa');
                $id_department = $this->input->post('id_department');
                $limited = $this->input->post('limited');

                $frametime = array();
                $this->db->select('id');
                $this->db->where('status', 'on');
                $frametime = $this->db->get('tbl_frametime')->result_array();

                $db_insert = array();
                $db_update = array();

                $startdiff = new DateTime($startdate);
                $enddiff   = new DateTime($enddate);
                $diff      = $enddiff->diff($startdiff)->days;
                for ($i=0; $i <= $diff; $i++) {
                    $date = date('Y-m-d', strtotime($startdate . ' + ' . $i . ' days'));
                    if(isset($frametime) AND !empty($frametime)){
                        foreach ($frametime as $key => $value) {
                            $id_frametime = $value['id'];
                            # check ton tai
                            $this->db->select('id');
                            $this->db->where('date', $date);
                            $this->db->where('id_center_call', $id_center_call);
                            $this->db->where('id_center_spa', $id_center_spa);
                            $this->db->where('id_department', $id_department);
                            $this->db->where('id_frametime', $id_frametime);
                            $checkdt = $this->db->get('tbl_limit_department')->row_array();

                            if( !isset($checkdt['id']) OR empty($checkdt['id']) ){
                                $db_insert[] = array(
                                    'date' => $date,
                                    'id_center_call' => $id_center_call,
                                    'id_center_spa' => $id_center_spa,
                                    'id_frametime' => $id_frametime,
                                    'id_department' => $id_department,
                                    'limited' => $limited,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => $this->_uid,
                                );
                            }else{
                                $db_update[] = array(
                                    'id' => $checkdt['id'],
                                    'date' => $date,
                                    'id_center_call' => $id_center_call,
                                    'id_center_spa' => $id_center_spa,
                                    'id_frametime' => $id_frametime,
                                    'id_department' => $id_department,
                                    'limited' => $limited,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => $this->_uid,
                                );
                            }
                        }
                    }
                }

                $this->db->trans_begin();

                if(isset($db_insert) AND !empty($db_insert)){
                    $this->db->insert_batch('tbl_limit_department', $db_insert);
                }

                if(isset($db_update) AND !empty($db_update)){
                    $this->db->update_batch('tbl_limit_department', $db_update, 'id');
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

        $center_call = array();
        $this->db->select('id,name');
        $this->db->where('type', 'call');
        $this->db->where('status', 'on');
        $center_call = $this->db->get('tbl_centers')->result_array();
        $data['center_call'] = $center_call;

        $center_spa = array();
        $this->db->select('id,name');
        $this->db->where('type', 'spa');
        $this->db->where('status', 'on');
        $center_spa = $this->db->get('tbl_centers')->result_array();
        $data['center_spa'] = $center_spa;
        
        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
        $data['content'] = 'limit/department/detail';
        $this->setlayout($data, 'v2/dialog');
    }
}