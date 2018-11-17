<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
CREATE
 	ALGORITHM = MERGE
VIEW `view_frametime`
 	AS SELECT cen.name AS cenname,frame.id,frame.name AS franame,frame.start,frame.end,frame.status
	FROM `tbl_frametime` AS frame
	JOIN `tbl_centers` AS cen ON cen.id=frame.id_center
	ORDER BY cen.id
 	WITH LOCAL  CHECK OPTION
*/
class Frametime extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách ca', ''),
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
            $this->db->select('id,name,start,end,status');
            $this->db->from('tbl_frametime');

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->order_by('id');
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
            '_wth_time'     => 120,
            '_wth_status'   => 65,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'frametime/index';
    	$this->setlayout($data, 'v2/tmpl');
    }

    public function time_check(){
        $start      = $this->input->post('starttime');
        $starttime  = '2000-01-01 ' . $start . ':00';
        $start      = preg_replace("/[^0-9]/", "", $start);

        $end        = $this->input->post('endtime');
        $endtime    = '2000-01-01 ' . $end . ':00';
        $end        = preg_replace("/[^0-9]/", "", $end);

        if ( intval($start) >= intval($end) ) {
            $this->form_validation->set_message('time_check', '<b>%s</b> không hợp lệ.');
            return FALSE;
        } else {
            return TRUE;
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
            $validation = array(
                // array(
                //     'field' => 'id_center',
                //     'label' => 'Trung tâm',
                //     'rules' => 'trim|required',
                // ),
                array(
                    'field' => 'name[]',
                    'label' => 'Ca',
                    'rules' => 'trim|required|max_length[75]',
                ),
                array(
                    'field' => 'starttime[]',
                    'label' => 'Bắt đầu',
                    'rules' => 'trim|required', //|callback_time_check
                ),
                array(
                    'field' => 'endtime[]',
                    'label' => 'Kết thúc',
                    'rules' => 'trim|required', //|callback_time_check
                ),
            );

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run($this) !== FALSE ){
                //$id_center  = $this->input->post('id_center');
                $name       = $this->input->post('name');
                $starttime  = $this->input->post('starttime');
                $endtime    = $this->input->post('endtime');
                $status     = $this->input->post('status');

                $dbframetime = array();

                foreach ($name as $key => $value) {
                    $_name = $value;
                    $_start= $starttime[$key];
                    $_end  = $endtime[$key];

                    $frameitem = array(
                        //'id_center' => $id_center,
                        'id_center' => 0,
                        'name'      => $_name,
                        'starttime' => '2000-01-01 ' . $_start . ':00',
                        'endtime'   => '2000-01-01 ' . $_end . ':00',
                        'start'     => $_start,
                        'end'       => $_end,
                        'updated_at'=> date('Y-m-d H:i:s'),
                        'updated_by'=> $this->_uid,
                    );

                    if( !$flag ){
                        $frameitem['status'] = 'off';
                    }

                    $dbframetime[] = $frameitem;
                }

                $this->db->trans_begin();

                if( ! $flag ){
                    $this->db->insert_batch('tbl_frametime', $dbframetime);
                }else{
                    $this->db->where('id', $id);
                    $this->db->update('tbl_frametime', $dbframetime[0]);
                }

                # Luu thong tin log thay doi
                $frametimes_log = array(
                    'id_frametime' => $id,
                    'data'      => json_encode($dbframetime),
                    'updated_at'=> date('Y-m-d H:i:s'),
                    'updated_by'=> $this->_uid
                );
                $this->db->insert('tbl_frametime_log', $frametimes_log);

                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $error      = 'Cập nhật thông tin không thành công';
                }else{
                    $this->db->trans_commit();
                    $success    = 'Cập nhật thông tin thành công';
                }
            }
        }

        if( $flag AND ( ! isset($detail) OR empty($detail) ) ){
            $this->db->where('id', $id);
            $detail = $this->db->get('tbl_frametime')->row_array();
            if( isset($detail) AND !empty($detail) ){
                $detail['starttime']    = $detail['start'];
                $detail['endtime']      = $detail['end'];
            }
        }

        $center = array();
        $this->db->where('status', 'on');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center']  = $center;

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
        $data['content'] = 'frametime/detail';
    	$this->setlayout($data, 'v2/dialog');
    }

    function onoff(){
        $id     = isset($_POST['id']) ? $_POST['id'] : FALSE;
        $status = isset($_POST['status']) ? $_POST['status'] : 'off';
        $return = 'FAIL';

        if($id){
            $this->db->where('id', $id);
            if( $this->db->update('tbl_frametime', array('status' => $status)) !== FALSE){
                $return = 'SUCCESS';
            }
        }

        echo $return;
    }
}