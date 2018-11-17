<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Limit extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    /*
    CREATE
        ALGORITHM = MERGE
    VIEW `view_limit_center`
        AS SELECT ce.`name` as cenname, fr.`name` as faname, fr.`start`, fr.`end`, li.`id`, li.`id_center`, li.`id_frametime`, li.`date`, li.`limited`, li.`appointment`
        FROM `tbl_limit_center` AS li
        JOIN `tbl_frametime` AS fr ON fr.`id`=li.`id_frametime` AND fr.`status`='on'
        JOIN `tbl_centers` AS ce ON ce.`id`=li.`id_center` AND ce.`status`='on' 
        ORDER BY ce.id
        WITH LOCAL  CHECK OPTION
    */
    function center(){
        $data['breadcrumb'] = array(
            array('Trang chủ', base_url()),
            array('Gới hạn ca Trung tâm', ''),
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

            $id_center = isset($request['id_center']) ? $request['id_center'] : FALSE;
            $id_frametime = isset($request['id_frametime']) ? $request['id_frametime'] : FALSE;
            $startdate = ( isset($request['startdate']) AND !empty($request['startdate']) ) ? $request['startdate'] : date('Y-m-d');
            $enddate = ( isset($request['enddate']) AND !empty($request['enddate']) ) ? $request['enddate'] : $startdate;
            
            $this->db->start_cache();
            $this->db->from('view_limit_center');

            if($id_center){
                $this->db->where('id_center', $id_center);
            }

            if($id_frametime){
                $this->db->where('id_frametime', $id_frametime);
            }

            $this->db->where('date >=', $startdate);
            $this->db->where('date <=', $enddate);

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->order_by('start');
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
            '_wth_faname' => 180,
            '_wth_date' => 100,
            '_wth_number' => 80,
            '_wth_action' => 100,
        ));

        $center = array();
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->order_by('id');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center']  = $center;

        $frametime = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->order_by('id');
        $frametime = $this->db->get('tbl_frametime')->result_array();
        $data['frametime'] = $frametime;

        $data['content'] = 'limit/center';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function limitcenter(){
        $success= '';
        $error  = '';
        $detail = array();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $detail = $_POST;
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
                $limited = $this->input->post('limited');

                $frametime = array();
                $this->db->select('id');
                $this->db->where('status', 'on');
                $frametime = $this->db->get('tbl_frametime')->result_array();

                $db_insert = array();

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
                            }
                        }
                    }
                }

                $this->db->trans_begin();

                if(isset($db_insert) AND !empty($db_insert)){
                    $this->db->insert_batch('tbl_limit_center', $db_insert);
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
        $data['content'] = 'limit/centerdetail';
        $this->setlayout($data, 'v2/dialog');
    }

    function limitcenterupdate(){
        $request = $_REQUEST;
        $id = isset($request['id']) ? $request['id'] : FALSE;
        $limited = isset($request['limited']) ? $request['limited'] : FALSE;

        if($id){
            $this->db->where('id', $id);
            $this->db->update('tbl_limit_center', array('limited' => $limited, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->_uid));
        }

        /*$result = array(
            'id' => $request['id'],
            'cenname' => $request['cenname'],
            'faname' => $request['faname'],
            'start' => $request['start'],
            'end' => $request['end'],
            'id_center' => $request['id_center'],
            'id_frametime' => $request['id_frametime'],
            'date' => $request['date'],
            'limited' => $request['limited'],
            'appointment' => $request['appointment'],
        );
        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($result);
        return;*/
    }

    /*
    CREATE
        ALGORITHM = MERGE
    VIEW `view_limit_department`
        AS SELECT li.id,ce1.name as cenamecall,ce2.name as cenamespa,fr.name as frname,de.name as dename,li.id_center_call,li.id_center_spa,li.id_department,li.id_frametime,li.date,li.limited,li.appointment
        FROM `tbl_limit_department` AS li
        JOIN `tbl_frametime` AS fr ON fr.`id`=li.`id_frametime` AND fr.`status`='on'
        JOIN `tbl_centers` AS ce1 ON ce1.`id`=li.`id_center_call` AND ce1.`status`='on' 
        JOIN `tbl_centers` AS ce2 ON ce2.`id`=li.`id_center_spa` AND ce2.`status`='on' 
        JOIN `tbl_departments` AS de ON de.`id`=li.`id_department` AND de.`status`='on' 
        ORDER BY ce1.id,ce2.id,fr.id,de.id
        WITH LOCAL  CHECK OPTION
    */
    function department(){
        $data['breadcrumb'] = array(
            array('Trang chủ', base_url()),
            array('Gới hạn ca Trung tâm', ''),
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
            $id_center_spa = isset($request['id_center_spa']) ? $request['id_center_spa'] : FALSE;
            $id_frametime = isset($request['id_frametime']) ? $request['id_frametime'] : FALSE;
            $startdate = ( isset($request['startdate']) AND !empty($request['startdate']) ) ? $request['startdate'] : date('Y-m-d');
            $enddate = ( isset($request['enddate']) AND !empty($request['enddate']) ) ? $request['enddate'] : $startdate;
            
            $this->db->start_cache();
            $this->db->from('view_limit_department');

            if($id_center_call){
                $this->db->where('id_center_call', $id_center_call);
            }

            if($id_center_spa){
                $this->db->where('id_center_spa', $id_center_spa);
            }

            if($id_frametime){
                $this->db->where('id_frametime', $id_frametime);
            }

            $this->db->where('date >=', $startdate);
            $this->db->where('date <=', $enddate);

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->order_by('id_center_call,id_center_spa,id_frametime,date');
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
            '_wth_faname' => 180,
            '_wth_date' => 100,
            '_wth_number' => 80,
            '_wth_frname' => 120,
            '_wth_dename' => 120,
            '_wth_action' => 100,
        ));

        $center_call = array();
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'call');
        $this->db->order_by('id');
        $center_call = $this->db->get('tbl_centers')->result_array();
        $data['center_call']  = $center_call;

        $center_spa = array();
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'spa');
        $this->db->order_by('id');
        $center_spa = $this->db->get('tbl_centers')->result_array();
        $data['center_spa']  = $center_spa;

        $frametime = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->order_by('id');
        $frametime = $this->db->get('tbl_frametime')->result_array();
        $data['frametime'] = $frametime;

        $data['content'] = 'limit/department';
        $this->setlayout($data, 'v2/tmpl');
    }

    function limitdepartment(){
        $success= '';
        $error  = '';
        $detail = array();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $detail = $_POST;
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
                    'field' => 'id_center_call',
                    'label' => 'Trung tâm Call',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'id_center_spa',
                    'label' => 'Trung tâm Spa',
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
                $limited = $this->input->post('limited');

                $frametime = array();
                $this->db->select('id');
                $this->db->where('status', 'on');
                $frametime = $this->db->get('tbl_frametime')->result_array();

                $department = array();
                $this->db->select('id');
                $this->db->where('status', 'on');
                $this->db->where('id_center', $id_center_call);
                $department = $this->db->get('tbl_departments')->result_array();

                $db_insert = array();

                $startdiff = new DateTime($startdate);
                $enddiff   = new DateTime($enddate);
                $diff      = $enddiff->diff($startdiff)->days;
                for ($i=0; $i <= $diff; $i++) {
                    $date = date('Y-m-d', strtotime($startdate . ' + ' . $i . ' days'));
                    if(isset($frametime) AND !empty($frametime)){
                        foreach ($frametime as $key => $value) {
                            $id_frametime = $value['id'];
                            foreach ($department as $_key => $_value) {
                                $id_department = $_value['id'];
                                # check ton tai
                                $this->db->select('id');
                                $this->db->where('date', $date);
                                $this->db->where('id_center_call', $id_center_call);
                                $this->db->where('id_center_spa', $id_center_spa);
                                $this->db->where('id_frametime', $id_frametime);
                                $this->db->where('id_department', $id_department);
                                $checkdt = $this->db->get('tbl_limit_department')->row_array();

                                if( !isset($checkdt) OR empty($checkdt) ){
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
                                }
                            }
                        }
                    }
                }

                $this->db->trans_begin();

                if(isset($db_insert) AND !empty($db_insert)){
                    $this->db->insert_batch('tbl_limit_department', $db_insert);
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

        $center_call = array();
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'call');
        $this->db->order_by('id');
        $center_call = $this->db->get('tbl_centers')->result_array();
        $data['center_call']  = $center_call;

        $center_spa = array();
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'spa');
        $this->db->order_by('id');
        $center_spa = $this->db->get('tbl_centers')->result_array();
        $data['center_spa']  = $center_spa;

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
        $data['content'] = 'limit/departmentdetail';
        $this->setlayout($data, 'v2/dialog');
    }

    function limitdepartmentupdate(){
        $request = $_REQUEST;
        $id = isset($request['id']) ? $request['id'] : FALSE;
        $limited = isset($request['limited']) ? $request['limited'] : FALSE;

        if($id){
            $this->db->where('id', $id);
            $this->db->update('tbl_limit_department', array('limited' => $limited, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->_uid));
        }
    }

    /*
    CREATE
        ALGORITHM = MERGE
    VIEW `view_limit_transaction`
        AS SELECT ce.name as cename,fr.name as frname,def.name as defname,det.name detname,tr.date,tr.val_from,tr.val_to,tr.status,tr.created_at,tr.updated_at
        FROM `tbl_limit_transaction` AS tr
        JOIN `tbl_centers` AS ce ON ce.`id`=tr.`id_center_spa` AND ce.`status`='on' 
        JOIN `tbl_frametime` AS fr ON fr.`id`=tr.`id_frametime` AND fr.`status`='on'
        JOIN `tbl_departments` AS def ON def.`id`=tr.`id_department_call_from` AND def.`status`='on' 
        JOIN `tbl_departments` AS det ON det.`id`=tr.`id_department_call_to` AND det.`status`='on' 
        ORDER BY tr.created_at desc
        WITH LOCAL  CHECK OPTION
    */
    function transaction(){
        $data['breadcrumb'] = array(
            array('Trang chủ', base_url()),
            array('Trao đổi lịch hẹn', ''),
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
            
            $this->db->start_cache();
            $this->db->from('view_limit_transaction');

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            //$this->db->order_by('id_center_call,id_center_spa,id_frametime,date');
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
            '_wth_dep' => 180,
            '_wth_frame' => 100,
            '_wth_val' => 80,
            '_wth_status' => 65,
            '_wth_time' => 100,
        ));

        $data['content'] = 'limit/transaction';
        $this->setlayout($data, 'v2/tmpl');
    }

    function transaction_detail(){
        $success= '';
        $error  = '';
        $detail = array();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $detail = $_POST;
            $validation = array(
                array(
                    'field' => 'id_center_spa',
                    'label' => 'Trung tâm Spa',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'id_center_call',
                    'label' => 'Trung tâm Call',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'id_department',
                    'label' => 'Phòng',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'id_frametime',
                    'label' => 'Ca',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'date',
                    'label' => 'Ngày',
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
                $id_center_spa = $this->input->post('id_center_spa');
                $id_center_call = $this->input->post('id_center_call');
                $id_department = $this->input->post('id_department');
                $id_frametime = $this->input->post('id_frametime');
                $date = $this->input->post('date');
                $val_from = $this->input->post('val_from');

                # check
                if( intval($this->_id_department) < 1 OR intval($this->_id_group) > 0 OR intval($this->_id_agent) > 0 ){
                    $error = 'Bạn không có quyền trao đổi lịch';
                }elseif($this->_id_department == $id_department) {
                    $error = 'Không thể trao đổi lịch hẹn trong phòng';
                }else{
                    $checkdt = FALSE;
                    $this->db->select('id');
                    $this->db->where('(limited - appointment) > '.$val_from);
                    $this->db->where('id_center_call', $id_center_call);
                    $this->db->where('id_center_spa', $id_center_spa);
                    $this->db->where('id_frametime', $id_frametime);
                    $this->db->where('id_department', $id_department);
                    $this->db->where('date', $date);
                    $checkdt = $this->db->get('tbl_limit_department')->row_array();

                    if( isset($checkdt['id']) AND !empty($checkdt['id']) ){
                        $db_transaction = array(
                            'id_center_spa' => $id_center_spa,
                            'id_frametime' => $id_frametime,
                            'date' => $date,
                            'id_center_call_from' => $this->_id_center,
                            'id_department_call_from' => $this->_id_department,
                            'val_from' => $val_from,
                            'id_center_call_to' => $id_center_call,
                            'id_department_call_to' => $id_department,
                            'val_to' => $val_from,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        );

                        $this->db->trans_begin();

                        $this->db->insert('tbl_limit_transaction', $db_transaction);

                        if ($this->db->trans_status() === FALSE){
                            $this->db->trans_rollback();
                            $error      = 'Cập nhật thông tin không thành công';
                        }else{
                            $this->db->trans_commit();
                            $success    = 'Cập nhật thông tin thành công';
                        }
                    }else{
                        $error = 'Phòng đã hết số hẹn có thể trao đổi';
                    }
                }
            }
        }

        $centerspa = array();
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'spa');
        $centerspa = $this->db->get('tbl_centers')->result_array();
        $data['centerspa'] = $centerspa;

        $centercall = array();
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'call');
        $centercall = $this->db->get('tbl_centers')->result_array();
        $data['centercall'] = $centercall;

        $frametime = array();
        $this->db->select('id,name,start,end');
        $this->db->where('status', 'on');
        $frametime = $this->db->get('tbl_frametime')->result_array();
        $data['frametime'] = $frametime;

        $data['success'] = $success;
        $data['error']   = $error;
        $data['content'] = 'limit/transactiondetail';
        $this->setlayout($data, 'v2/dialog');
    }
}