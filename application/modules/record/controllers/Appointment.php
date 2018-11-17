<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Appointment extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        if( ! in_array($this->_role, array('record')) ){
            echo 'Bạn không có quyền trong chức năng này';
        }
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', ''),
            array('Danh sách lịch hẹn', ''),
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

            $id_center = isset($request['id_center']) ? $request['id_center'] : false;
            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            $enddate = $startdate;
            #$enddate = date('Y-m-d', strtotime($startdate . ' + 5 days'));

            $this->db->start_cache();

            $this->db->select('app.id,app.cus_mobile,app.cus_first_name,app.cus_last_name,cen.name,app.id_center,app.app_datetime,app.agent_ext,app.sms_status,app.app_status,app.app_created_at');
            $this->db->from('tbl_appointments as app');
            $this->db->join('tbl_centers as cen', 'cen.id=app.id_center and cen.type="spa"');

            //$this->db->where('app.id_agent', $this->_id_agent);
            $this->db->where('app.last_app', 'on');
            $this->db->where('app.app_status !=', 'cancel');

            if($inputsearch){
                $this->db->where('app.cus_mobile like "%'.$inputsearch.'%"');
            }else{
                if($id_center){
                    $this->db->where('app.id_center', $id_center);
                }

                if($startdate){
                    $this->db->where('app.app_datetime > ', $startdate . ' 07:00:00');
                    $this->db->where('app.app_datetime < ', $enddate . ' 22:00:00');
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

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order' => 50,
            '_wth_first_name' => 160,
            '_wth_last_name' => 85,
            '_wth_mobile' => 100,
            '_wth_time' => 160,
            '_wth_status' => 80,
            '_wth_action' => 60,
        ));

        $centerspa = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'spa');
        $centerspa = $this->db->get('tbl_centers')->result_array();
        $data['centerspa'] = $centerspa;

        $data['content'] = 'appointment/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }
}
?>