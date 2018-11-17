<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Customer extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        # city
        $city = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $city = $this->db->get('tbl_city')->result_array();
        $this->_data['city'] = $city;

        # center
        $agent = array();
        $this->db->select('id,full_name,ext');
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $this->db->where('id_center', $this->_id_center);
        $this->db->where('id_department', $this->_id_department);
        $this->db->where('id_group', $this->_id_group);
        $agent = $this->db->get('tbl_accounts')->result_array();
        $this->_data['agent'] = $agent;

        # source
        $source = array();
        $this->db->select('id,name');
        $source = $this->db->get('tbl_source')->result_array();
        $this->_data['source'] = $source;
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Khách hàng', ''),
        );

    	$data['content'] = 'customer/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function assign(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Khách hàng', base_url() . 'customer'),
            array('Phân bổ khách hàng', ''),
        );

        if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 0;

            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $limit      = $pageSize;
            #$pg         = isset($request['pg']) ? $request['pg'] : 0;
            #$mkt        = isset($request['mkt']) ? $request['mkt'] : 0;
            $exception  = isset($request['exception']) ? $request['exception'] : 0;
            $offset     = ($page - 1) * $pageSize;

            $id_city = isset($request['id_city']) ? $request['id_city'] : false;

            $db_group = $this->setdbconnect($this->_id_group, 'group');
            $db_group->start_cache();
            $db_group->select('id,fullname,mobile,source,created_at');
            $db_group->where('status', 'new');
            //$db_group->where('start_ext', null);
	        $db_group->where('end_ext', null);

            /*if($pg AND $mkt){
                $db_group->where('(created_by="API" OR created_by="API-MKT")');
            }else{
                if($pg){
                    $db_group->where('created_by', 'API');
                }else if($mkt){
                    $db_group->where('created_by', 'API-MKT');
                }
            }*/

            if($this->_id_group==54 AND $this->_role == 'group'){
                if($exception){
                    $db_group->where('exception', 'tku');
                }else{
                    $db_group->where('exception is null');
                }
            }

            if( isset($id_city) AND !empty($id_city) AND $id_city ){
                $db_group->where('id_city in(' . $id_city . ')');
            }

            $db_group->from('tbl_customer');

            $db_group->stop_cache();
            $total = $db_group->count_all_results();

            $db_group->limit($limit, $offset);
            $dataResult = $db_group->get()->result_array();

            $db_group->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'query' => $db_group->last_query()
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 50,
            '_wth_mobile'   => 120,
            '_wth_source' 	=> 175,
            '_wth_time'     => 160,
            '_wth_action' 	=> 60,
        ));

    	$data['content'] = 'customer/assign';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function acassign(){
        $id_city = isset($_POST['id_city']) ? $_POST['id_city'] : false;
        $id_agent = isset($_POST['id_agent']) ? $_POST['id_agent'] : false;
        /*$pg = isset($_POST['pg']) ? $_POST['pg'] : 0;
        $mkt = isset($_POST['mkt']) ? $_POST['mkt'] : 0;*/
        $exception  = isset($_POST['exception']) ? $_POST['exception'] : 0;
		$assign_num = isset($_POST['assign_num']) ? $_POST['assign_num'] : 1;
		
		# SRART LOG
        $data_log = $_POST;
        $insert_log = array(
            'type' => 'Assign',
            'data' => json_encode($data_log),
            'created_by' => $this->_uid,
            'created_at' => date('Y-m-d H:i:s')
        );
        write_log($insert_log);
        # END LOG
		
		$agent = array();
        $this->db->select('id, ext');
		$this->db->where('id in('.$id_agent.')');
        $agent = $this->db->get('tbl_accounts')->result_array();

        $rtn_str = 'error';

        $db_center = $this->setdbconnect($this->_id_center, 'center');
        $db_department = $this->setdbconnect($this->_id_department, 'department');
        $db_group = $this->setdbconnect($this->_id_group, 'group');

        foreach ($agent as $_key => $_value) {
            $_id_agent = $_value['id'];
            $ext = $_value['ext'];
            
            $db_assign = array();
            $db_group->select('id,id_link,mobile');

            /*if($pg AND $mkt){
                $db_group->where('(created_by="API" OR created_by="API-MKT")');
            }else{
                if($pg){
                    $db_group->where('created_by', 'API');
                }else if($mkt){
                    $db_group->where('created_by', 'API-MKT');
                }
            }*/
            if($this->_id_group==54 AND $this->_role == 'group'){
                if($exception){
                    $db_group->where('exception', 'tku');
                }else{
                    $db_group->where('exception is null');
                }
            }

            if($id_city){
                $db_group->where('id_city in('.$id_city.')');
            }
            $db_group->where('status', 'new');
            $db_group->order_by('rand()');
            $db_group->limit($assign_num);
            $db_assign = $db_group->get('tbl_customer')->result_array();

            if( isset($db_assign) AND !empty($db_assign) ){
                $id_insert = array();

                foreach ($db_assign as $key => $value) {
                    array_push($id_insert, $value['id_link']);

                    $db_update = array(
                        'id_agent' => $_id_agent,
                        'start_ext' => $ext,
                        'start_time'=> date('Y-m-d H:i:s'),
                        'end_ext' => $ext,
                        'end_time'=> date('Y-m-d H:i:s'),
                        'status' => 'assign'
                    );
                    # update group
                    #$db_group->where('id_link', $value['id_link']);
                    $db_group->where('mobile', $value['mobile']);
                    $db_group->where('status', 'new');
                    $db_group->update('tbl_customer', $db_update);
                    # update department
                    #$db_department->where('id_link', $value['id_link']);
                    $db_department->where('mobile', $value['mobile']);
                    $db_department->update('tbl_customer', $db_update);
                    # update center
                    #$db_center->where('id', $value['id_link']);
                    $db_center->where('mobile', $value['mobile']);
                    $db_center->update('tbl_customer', $db_update);

                    $db_global = array(
                        'id_agent' => $_id_agent,
                    );

                    $this->db->where('mobile', $value['mobile']);
                    $this->db->update('tbl_customer', $db_global);
                }
                $rtn_str = 'success';
            }
        }	

		echo $rtn_str;
    }

    function detail($id=false){
    	$data['content'] = 'customer/detail';
        $this->setlayout($data, 'v2/dialog');
    }
}