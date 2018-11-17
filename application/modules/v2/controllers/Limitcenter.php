<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
CREATE
 	ALGORITHM = MERGE
VIEW `view_frametime_limit_center`
 	AS SELECT cen.`name` AS cenname, cen.id, frame.id as id_frametime, frame.`name` AS franame,frame.`start`,frame.`end`,limits.`id` as id_limits,limits.`date`,limits.`limit`,limits.`schedule`,limits.`status`
	FROM `tbl_frametime_center_limit` AS limits
	JOIN `tbl_frametime` AS frame ON frame.`id`=limits.`id_frametime` AND frame.`status`='on'
	JOIN `tbl_centers` AS cen ON cen.`id`=limits.`id_center` AND cen.`status`='on' 
	ORDER BY cen.id
 	WITH LOCAL  CHECK OPTION
*/
class Limitcenter extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Giới hạn ca', ''),
            array('Giới hạn Trung tâm', ''),
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
            $startdate = ( isset($request['startdate']) AND !empty($request['startdate']) ) ? $request['startdate'] : date('Y-m-d');
            $enddate = ( isset($request['enddate']) AND !empty($request['enddate']) ) ? $request['enddate'] : $startdate;
            
            $this->db->start_cache();
            $this->db->from('view_frametime_limit_center');

            if($id_center){
                $this->db->where('id', $id_center);
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
            '_wth_order'    => 60,
            '_wth_time'     => 70,
            '_wth_franame'  => 100,
            '_wth_date'  	=> 100,
            '_wth_limit'  	=> 60,
            '_wth_status'   => 65,
            '_wth_limit_de' => 100,
            '_wth_action'   => 60,
        ));

        $center = array();
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->order_by('id');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center']  = $center;

        $data['content'] = 'limit/center/index';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function detail(){
        $success= '';
        $error  = '';
        $detail = array();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $detail = $_POST;
            $validation = array(
                array(
                    'field' => 'id_center',
                    'label' => 'Trung tâm',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'startdate',
                    'label' => 'Từ ngày',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'enddate',
                    'label' => 'Đến ngày',
                    'rules' => 'trim|required',
                )
            );#callback_time_check

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run($this) !== FALSE ){
                $startdate = $this->input->post('startdate');
                $enddate = $this->input->post('enddate');
                $id_center = $this->input->post('id_center');
                $id_frametime = $this->input->post('id_frametime');
                $limit = $this->input->post('limit');

                $this->db->trans_begin();

                $startdiff = new DateTime($startdate);
                $enddiff   = new DateTime($enddate);
                $diff      = $enddiff->diff($startdiff)->days;

                for ($i=0; $i <= $diff; $i++) {
                    $date = date('Y-m-d', strtotime($startdate . ' + ' . $i . ' days'));
                    foreach ($id_frametime as $key => $value) {
                        $_id_frametime = $value;
                        $_limit        = $limit[$key];

                        if( !empty($_limit) AND $_limit ){
                            $checkdt = array();
                            $this->db->select('id');
                            $this->db->where('id_frametime', $_id_frametime);
                            $this->db->where('id_center', $id_center);
                            $this->db->where('date', $date);
                            $checkdt = $this->db->get('tbl_frametime_center_limit')->row_array();

                            $limitcenter = array(
                                'date'          => $date,
                                'id_frametime'  => $_id_frametime,
                                'id_center'     => $id_center,
                                'limit'         => $_limit,
                                'status'        => 'on',
                                'updated_at'    => date('Y-m-d H:i:s'),
                                'updated_by'    => $this->_uid
                            );

                            if( ! isset($checkdt) OR empty($checkdt)){
                                $this->db->insert('tbl_frametime_center_limit', $limitcenter);
                                $id = $this->db->insert_id();
                            }else{
                                $id = $checkdt['id'];
                                $this->db->where('id', $checkdt['id']);
                                $this->db->update('tbl_frametime_center_limit', $limitcenter);
                            }

                            $limitcenter_log = array(
                                'id_frametime_center_limit' => $id,
                                'data'      => json_encode($limitcenter),
                                'updated_at'=> date('Y-m-d H:i:s'),
                                'updated_by'=> $this->_uid
                            );
                            $this->db->insert('tbl_frametime_center_limit_log', $limitcenter_log);
                        }
                    }
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

        $frametime = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $frametime = $this->db->get('tbl_frametime')->result_array();
        $data['frametime']  = $frametime;

        $center = array();
        $this->db->select('id, name');
        $this->db->where('status', 'on');
        $this->db->order_by('id');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center']  = $center;

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
        $data['content'] = 'limit/center/detail';
    	$this->setlayout($data, 'v2/dialog');
    }

    function onoff(){
        $id     = isset($_POST['id']) ? $_POST['id'] : FALSE;
        $status = isset($_POST['status']) ? $_POST['status'] : 'off';
        $return = 'FAIL';

        if($id){
            $this->db->where('id', $id);
            if( $this->db->update('tbl_frametime_center_limit', array('status' => $status)) !== FALSE){
                $return = 'SUCCESS';
            }
        }

        echo $return;
    }
}