<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
CREATE
 	ALGORITHM = MERGE
VIEW `view_call_detail_status`
 	AS SELECT cal.id,cal.uniqid,cal.id_cus,cus.fullname,cus.mobile,cus.source,cus.id_source,cus.id_fileup,cus.start_date,cus.close_date,cal.agent_ext,sta.id as id_status,sta.`name` as sname,cal.id_city,cal.id_center,cal.id_frametime,cal.id_center_call,cal.id_department,cal.id_group,cal.id_agent,cal.content_call,cal.callback,cal.callback_status,cal.appointment,cal.appointment_status,cal.call_type,cal.created_at,cal.updated_at
	FROM `tbl_call_detail` AS cal
	JOIN `tbl_customer` AS cus ON cus.`id`=cal.`id_cus`
	JOIN `tbl_call_status` AS sta ON sta.`id`=cal.`id_call_status`
	ORDER BY cal.id
 	WITH LOCAL CHECK OPTION

CREATE
 	ALGORITHM = MERGE
VIEW `view_call_detail_no_status`
 	AS SELECT cal.id,cal.uniqid,cal.id_cus,cus.fullname,cus.mobile,cus.source,cal.agent_ext,cal.content_call,cal.callback,cal.appointment,cal.call_type,cal.created_at,cal.updated_at
	FROM `tbl_call_detail` AS cal
	JOIN `tbl_customer` AS cus ON cus.`id`=cal.`id_cus`
	WHERE cal.id_call_status IS NULL
	ORDER BY cal.id
 	WITH LOCAL CHECK OPTION
*/
class Report extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        # source
        $source = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $source = $this->db->get('tbl_source')->result_array();
        $this->_data['source'] = $source;

        # Agent
        $agent = array();
        $this->db->select('id,full_name,ext');
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $agent = $this->db->get('view_account')->result_array();
        $this->_data['agent'] = $agent;
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách cuộc gọi', ''),
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

            $inputsearch = ( isset($request['inputsearch']) AND !empty($request['inputsearch']) ) ? $request['inputsearch'] : FALSE;
            $id_center = ( isset($request['id_center']) AND !empty($request['id_center']) ) ? $request['id_center'] : $this->_id_center;
            $id_department = ( isset($request['id_department']) AND !empty($request['id_department']) ) ? $request['id_department'] : $this->_id_department;
            $id_group = ( isset($request['id_group']) AND !empty($request['id_group']) ) ? $request['id_group'] : $this->_id_group;
            $id_agent = ( isset($request['id_agent']) AND !empty($request['id_agent']) ) ? $request['id_agent'] : $this->_id_agent;
            $id_source = ( isset($request['id_source']) AND !empty($request['id_source']) ) ? $request['id_source'] : FALSE;
            $id_fileup = ( isset($request['id_fileup']) AND !empty($request['id_fileup']) ) ? $request['id_fileup'] : FALSE;
            $id_status = ( isset($request['id_status']) AND !empty($request['id_status']) ) ? $request['id_status'] : FALSE;
            $startdate = ( isset($request['startdate']) AND !empty($request['startdate']) ) ? $request['startdate'] : FALSE;
            $enddate = ( isset($request['enddate']) AND !empty($request['enddate']) ) ? $request['enddate'] : FALSE;
            
            $this->db->start_cache();
            $this->db->from('view_call_detail_status');
            if( $inputsearch ){
                $this->db->where('(mobile like "%'.$inputsearch.'%" OR fullname like "%'.strtoupper($inputsearch).'%")');
            }
            if( $id_center ){
                $this->db->where('id_center_call in('.$id_center.')');
            }
            if( $id_department ){
                $this->db->where('id_department in('.$id_department.')');
            }
            if( $id_group ){
                $this->db->where('id_group in('.$id_group.')');
            }
            if( $id_agent AND $this->_role == 'staff' ){
                $this->db->where('id_agent in('.$id_agent.')');
            }
            if( $id_source ){
                $this->db->where('id_source in('.$id_source.')');
            }
            if( $id_fileup ){
                $this->db->where('id_fileup in('.$id_fileup.')');
            }
            if( $id_status ){
                $this->db->where('id_status in('.$id_status.')');
            }
            if( $startdate AND !$enddate ){
                $this->db->where('start_date', $startdate);
            }elseif( !$startdate AND $enddate ){
                $this->db->where('close_date', $enddate);
            }elseif($startdate AND $enddate){
                $this->db->where('created_at >=', $startdate.' 00:00:00');
                $this->db->where('created_at <=', $enddate.' 23:59:59');
            }

            $this->db->order_by('id');

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

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 50,
            '_wth_fullname' => 160,
            '_wth_mobile'   => 120,
            '_wth_source'   => 100,
            '_wth_ext'      => 60,
            '_wth_status'   => 160,
            '_wth_time' 	=> 160,
            '_wth_content'  => 250,
            '_wth_action'   => 60,
        ));

        # center
        $center = array();
        $this->db->select('id,name');
        $this->db->where('type', 'call');
        $this->db->where('status', 'on');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center'] = $center;

        # call_status
        $call_status = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $call_status = $this->db->get('tbl_call_status')->result_array();
        $data['call_status'] = $call_status;

        $data['content'] = 'report/index';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function callnotstatus(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách cuộc gọi không lưu log', ''),
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
            $this->db->from('view_call_detail_no_status');
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
            '_wth_order'    => 50,
            '_wth_fullname' => 160,
            '_wth_mobile'   => 120,
            '_wth_source'   => 100,
            '_wth_ext'      => 60,
            '_wth_time' 	=> 160,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'report/nostatus';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function callback(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách cuộc gọi lại', ''),
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
            $this->db->from('view_call_detail_status');
            $this->db->where('callback is not null');
            $this->db->where('callback_status', 'on');
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
            '_wth_order'    => 50,
            '_wth_fullname' => 160,
            '_wth_mobile'   => 120,
            '_wth_source'   => 100,
            '_wth_ext'      => 60,
            '_wth_status'   => 160,
            '_wth_time' 	=> 160,
            '_wth_content'  => 250,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'report/callback';
    	$this->setlayout($data, 'v2/tmpl');
    }
    /*
    CREATE
        ALGORITHM = MERGE
    VIEW `view_appointments`
    AS SELECT app.app_code,app.id_cus,app.cus_mobile,app.cus_first_name,app.cus_last_name,app.uniqid_call,app.id_city,cit.name as cname,app.id_center,cen.name as cename,app.id_frametime,fra.name as fname,fra.start,fra.end,app.app_date,app.app_time,app.app_content,app.id_department,app.id_group,app.id_agent,app.agent_ext,app.sms_time_send,app.sms_status,app.app_status
        FROM `tbl_appointments` AS app
        JOIN `tbl_city` AS cit ON cit.`id`=app.`id_city`
        JOIN `tbl_centers` AS cen ON cen.`id`=app.`id_center`
        JOIN `tbl_frametime` AS fra ON fra.`id`=app.`id_frametime`
        WHERE app.`status`='on'
        ORDER BY app.`id`
        WITH LOCAL CHECK OPTION
    */
    function appointment(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách cuộc lịch hẹn', ''),
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

            $inputsearch = (isset($request['inputsearch']) AND !empty($request['inputsearch'])) ? $request['inputsearch'] : FALSE;
            $id_center = (isset($request['id_center']) AND !empty($request['id_center'])) ? $request['id_center'] : FALSE;
            $id_frametime = (isset($request['id_frametime']) AND !empty($request['id_frametime'])) ? $request['id_frametime'] : FALSE;
            $id_agent = (isset($request['id_agent']) AND !empty($request['id_agent'])) ? $request['id_agent'] : FALSE;
            $id_source = (isset($request['id_source']) AND !empty($request['id_source'])) ? $request['id_source'] : FALSE;
            $id_fileup = (isset($request['id_fileup']) AND !empty($request['id_fileup'])) ? $request['id_fileup'] : FALSE;
            $startdate = (isset($request['startdate']) AND !empty($request['startdate'])) ? $request['startdate'] : FALSE;
            $enddate = (isset($request['enddate']) AND !empty($request['enddate'])) ? $request['enddate'] : FALSE;
            
            $this->db->start_cache();
            $this->db->select('CONCAT(cus_first_name, " ", cus_last_name) as fullname,cus_mobile,cname,cename,fname,CONCAT(app_date, " ", app_time) as app_datetime,app_content,agent_ext');
            $this->db->from('view_appointments');

            if( $inputsearch ){
                $this->db->where('(cus_mobile like "%'.$inputsearch.'%"');
            }

            if( $id_center ){
                $this->db->where('id_center in('.$id_center.')');
            }

            if( $id_frametime ){
                $this->db->where('id_frametime in('.$id_frametime.')');
            }

            if( $id_agent ){
                $this->db->where('id_agent in('.$id_agent.')');
            }

            /*if( $id_source ){
                $this->db->where('id_source in('.$id_source.')');
            }

            if( $id_fileup ){
                $this->db->where('id_fileup in('.$id_fileup.')');
            }*/

            if( $startdate AND $enddate ){
                $this->db->where('app_date >=', $startdate);
                $this->db->where('app_date <=', $enddate);
                $this->db->where('app_time >=', "00:00");
                $this->db->where('app_time <=', "23:59");
            }elseif ( $startdate ) {
                $this->db->where('app_date', $startdate);
            }elseif ($enddate) {
                $this->db->where('app_date', $enddate);
            }

            $this->db->order_by('app_date');
            $this->db->order_by('app_time');

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
            '_wth_order'    => 50,
            '_wth_fullname' => 160,
            '_wth_mobile'   => 120,
            '_wth_city'     => 90,
            '_wth_center'   => 200,
            '_wth_frame'    => 60,
            '_wth_ext'      => 60,
            '_wth_time'     => 160,
            '_wth_content'  => 300,
        ));

        # center
        $center = array();
        $this->db->select('id,name');
        $this->db->where('type', 'spa');
        $this->db->where('status', 'on');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center'] = $center;

        # frametime
        $frametime = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $frametime = $this->db->get('tbl_frametime')->result_array();
        $data['frametime'] = $frametime;

        $data['content'] = 'report/appointment';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function startclose(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách cuộc gọi không lưu log', ''),
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

            $search= isset($request['inputsearch']) ? $request['inputsearch'] : '';
            $date  = ( isset($request['date']) AND !empty($request['date']) ) ? $request['date'] : date('Y-m-d');
            $type  = ( isset($request['type']) AND !empty($request['type']) ) ? $request['type'] : 'start';
            
            $this->db->start_cache();
            $this->db->from('tbl_call_detail');
            if( $type == 'start' ){
                $this->db->where('start_date', $date);
            }else{
                $this->db->where('close_date', $date);
            }
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
            '_wth_order'    => 50,
            '_wth_fullname' => 160,
            '_wth_mobile'   => 120,
            '_wth_source'   => 100,
            '_wth_ext'      => 60,
            '_wth_time'     => 160,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'report/startclose';
        $this->setlayout($data, 'v2/tmpl');
    }

    function exportdata(){

    }
}