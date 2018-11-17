<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Customer extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        if($this->_role != 'center'){
            die('Bạn không có quyền trong trang này');
        }
        # city
        $city = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
		$this->db->order_by('position');
        $city = $this->db->get('tbl_city')->result_array();
        $this->_data['city'] = $city;

        # center
        $department = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->where('id_center', $this->_id_center);
        $department = $this->db->get('tbl_departments')->result_array();
        $this->_data['department'] = $department;

        $group = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->where('id_center', $this->_id_center);
        $group = $this->db->get('tbl_groups')->result_array();
        $this->_data['group'] = $group;

        # Agent
        $agent = array();
        $this->db->select('id,full_name,ext');
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $this->db->where('id_center', $this->_id_center);
        $agent = $this->db->get('tbl_accounts')->result_array();
        $this->_data['agent'] = $agent;

        # source
        $source = array();
        $this->db->select('id,name');
        $this->db->select('status', 'on');
        if( (strpos($this->_uid, 'linhnh') === false ){
            $this->db->where('mkt', 0);
            $this->db->where('pg', 0);
        }
        $source = $this->db->get('tbl_source')->result_array();
        $this->_data['source'] = $source;
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Khách hàng', ''),
        );

        if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 0;
            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $limit      = $pageSize;
            $offset     = ($page - 1) * $pageSize;
            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;

            if( isset($inputsearch) AND $inputsearch ){
                $db_center = $this->setdbconnect($this->_id_center, 'center');
                $db_center->start_cache();
                $db_center->select('id, fullname, mobile, source, callval, id_agent, end_ext, status, start_date, close_date');
                $db_center->where_in('status', array('new','renew','assign','call','called'));
                $db_center->where('mobile like "%' . trim($inputsearch) . '%"');
                #$db_center->where('id_agent is not null');
                $db_center->from('tbl_customer');

                $db_center->stop_cache();
                $total = $db_center->count_all_results();

                $db_center->limit($limit, $offset);
                $dataResult = $db_center->get()->result_array();
                $db_center->flush_cache();
            }
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
            '_wth_order' => 50,
            '_wth_mobile' => 100,
            '_wth_source' => 150,
            '_wth_val' => 40,
            '_wth_ext' => 60,
            '_wth_status' => 70,
            '_wth_date' => 100,
            '_wth_action' => 60,
        ));

    	$data['content'] = 'customer/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function transaction($id=false, $id_agent_old=false){
        $success= '';
        $error  = '';
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $validation = array(
                array(
                    'field' => 'id_agent',
                    'label' => 'Agent',
                    'rules' => 'required',
                )
            );

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run() !== FALSE ){
                $id_agent = $this->input->post('id_agent');
                $agentdt = array();
                $this->db->select('id,ext,id_department,id_group');
                $this->db->where('id', $id_agent);
                $agentdt = $this->db->get('tbl_accounts')->row_array();

                if($id_agent_old AND $id_agent_old != 'null'){
                    $db_agent_old = $this->setdbconnect($id_agent_old);
                    $db_agent_old->where('id_link', $id);
                    $db_agent_old->delete('tbl_customer');
                }

                $db_agent_new = $this->setdbconnect($id_agent);
                $db_center = $this->setdbconnect($this->_id_center, 'center');

                $db_update = array(
                    'id_department' => $agentdt['id_department'],
                    'id_group' => $agentdt['id_group'],
                    'id_agent' => $agentdt['id'],
                    'start_ext' => $agentdt['ext'],
                    'start_time' => date('Y-m-d H:i:s'),
                    'end_ext' => $agentdt['ext'],
                    'end_time' => date('Y-m-d H:i:s'),
                    'status' => 'assign'
                );
                $db_center->where('id', $id);
                $db_center->update('tbl_customer', $db_update);

                $db_center->select('id,id_fileup,first_name,last_name,fullname,mobile,gender,code_city,id_city,source,start_date');
                $db_center->where('id', $id);
                $db_assign = $db_center->get('tbl_customer')->row_array();
                if(isset($db_assign) AND !empty($db_assign)){
                    $db_insert = array(
                        'id_link' => $db_assign['id'],
                        'id_fileup' => $db_assign['id_fileup'],
                        'first_name' => $db_assign['first_name'],
                        'last_name' => $db_assign['last_name'],
                        'fullname' => $db_assign['fullname'],
                        'mobile' => $db_assign['mobile'],
                        'gender' => $db_assign['gender'],
                        'code_city' => $db_assign['code_city'],
                        'id_city' => $db_assign['id_city'],
                        'source' => $db_assign['source'],
                        'callval' => 0,
                        'id_agent' => $agentdt['id'],
                        'start_ext' => $agentdt['ext'],
                        'start_time' => date('Y-m-d H:i:s'),
                        'end_ext' => $agentdt['ext'],
                        'end_time' => date('Y-m-d H:i:s'),
                        'status' => 'assign',
                        'start_date' => $db_assign['start_date'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $this->_uid
                    );

                    $db_agent_new->insert('tbl_customer', $db_insert);

                    $success= 'Chuyển Khách hàng thành công';
                }
            }else{
                $error  = 'Chuyển Khách hàng không thành công';
            }
        }

        $data['success'] = $success;
        $data['error'] = $error;
        $data['content'] = 'customer/transaction';
        $this->setlayout($data, 'v2/dialog');
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
            $offset = ($page - 1) * $pageSize;

            $id_city = isset($request['id_city']) ? $request['id_city'] : false;
            $id_source = isset($request['id_source']) ? $request['id_source'] : false;
            $id_fileup = isset($request['id_fileup']) ? $request['id_fileup'] : false;
            $pg = isset($request['pg']) ? $request['pg'] : 0;
            $mkt = isset($request['mkt']) ? $request['mkt'] : 0;
            $demo6t = isset($request['demo6t']) ? $request['demo6t'] : 0;
			$inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;

            $db_center = $this->setdbconnect($this->_id_center, 'center');
            $db_center->start_cache();
            $db_center->select('id,fullname,mobile,source,created_at');
            $db_center->where('status', 'new');
            //$db_center->where('start_ext', null);
            $db_center->where('end_ext', null);

            if($pg AND $mkt){
                $db_center->where("(focus='mkt' OR focus='pg')");
            }else{
                if($pg){
                    $db_center->where('focus', 'pg');
                }else{
                    $db_center->where("(`focus` <> 'pg' OR `focus` is null)");
                }

                if($mkt){
                    $db_center->where('focus', 'mkt');
                }else{
                    $db_center->where("(`focus` <> 'pg' OR `focus` is null)");
                }
            }

            if($demo6t){
                $db_center->where('id_fileup', 99999);
            }else{
                $db_center->where('(id_fileup < 99999 or id_fileup is null)');
            }

            if( isset($id_city) AND !empty($id_city) AND $id_city ){
                $db_center->where('id_city in(' . $id_city . ')');
            }
            
            if( $id_source ){
                $_source = strpos($id_source, '999999');
                if(!$_source){
                    $db_center->where('id_source in(' . $id_source . ')');
                }else{
                    if($id_source == '999999'){
                        $db_center->where('(id_source is null OR id_source=0)');
                    }
                }
            }
            
            if( isset($id_fileup) AND !empty($id_fileup) AND $id_fileup ){
                $db_center->where('id_fileup in(' . $id_fileup . ')');
            }
            
            if( isset($inputsearch) AND !empty($inputsearch) AND $inputsearch ){
                $db_center->where('mobile like "%' . trim($inputsearch) . '%"');
            }

            $db_center->from('tbl_customer');

            $db_center->stop_cache();
            $total = $db_center->count_all_results();

            $db_center->limit($limit, $offset);
            $dataResult = $db_center->get()->result_array();

            $db_center->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
				'query' => $db_center->last_query()
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
        $id_department = isset($_POST['id_department']) ? $_POST['id_department'] : false;
        $id_source = isset($_POST['id_source']) ? $_POST['id_source'] : false;
        $id_fileup = isset($_POST['id_fileup']) ? $_POST['id_fileup'] : false;
		$assign_num = isset($_POST['assign_num']) ? $_POST['assign_num'] : 1;
        $pg = isset($_POST['pg']) ? $_POST['pg'] : 0;
        $mkt = isset($_POST['mkt']) ? $_POST['mkt'] : 0;
        $demo6t = isset($_POST['demo6t']) ? $_POST['demo6t'] : 0;
        $inputsearch = isset($_POST['inputsearch']) ? $_POST['inputsearch'] : false;

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
        $rtn_str = 'error';
        $db_center = $this->setdbconnect($this->_id_center, 'center');
		$department = array();
		if($id_department){
			$department = explode(',', $id_department);
		}

		foreach ($department as $_key => $_value) {
			$_id_department = $_value;
			$db_department = $this->setdbconnect($_id_department, 'department');

			$db_assign = array();
			$db_center->select('id,id_link,id_fileup,id_group,first_name,last_name,fullname,mobile,gender,code_city,id_city,id_source,source,start_date');

            if($pg AND $mkt){
                $db_center->where("(focus='mkt' OR focus='pg')");
            }else{
                if($pg){
                    $db_center->where('focus', 'pg');
                }else{
                    $db_center->where("(`focus` <> 'pg' OR `focus` is null)");
                }

                if($mkt){
                    $db_center->where('focus', 'mkt');
                }else{
                    $db_center->where("(`focus` <> 'pg' OR `focus` is null)");
                }
            }

            if($demo6t){
                $db_center->where('id_fileup', 99999);
            }else{
                $db_center->where('(id_fileup < 99999 or id_fileup is null)');
            }

			if($id_city){
				$db_center->where('id_city in('.$id_city.')');
			}
			
			if($id_source){
                $_source = strpos($id_source, '999999');
                if(!$_source){
                    $db_center->where('id_source in(' . $id_source . ')');
                }else{
                    if($id_source == '999999'){
                        $db_center->where('(id_source is null OR id_source=0)');
                    }
                }
			}

			if($id_fileup){
				$db_center->where('id_fileup in('.$id_fileup.')');
			}

			$db_center->where('status', 'new');
			$db_center->order_by('rand()');
			$db_center->limit($assign_num);
			$db_assign = $db_center->get('tbl_customer')->result_array();
			#var_dump($db_assign);die;

			if( isset($db_assign) AND !empty($db_assign) ){
				foreach ($db_assign as $key => $value) {
					$checkdt = array();
					$db_department->select('id');
					$db_department->where('mobile', $value['mobile']);
					$checkdt = $db_department->get('tbl_customer')->row_array();

					if( !isset($checkdt['id']) OR empty($checkdt['id']) ){
						$db_insert = array(
							'id_link' => $value['id_link'],
							'id_fileup' => $value['id_fileup'],
							'first_name' => $value['first_name'],
							'last_name' => $value['last_name'],
							'fullname' => $value['fullname'],
							'mobile' => $value['mobile'],
							'gender' => $value['gender'],
							'code_city' => $value['code_city'],
							'id_city' => $value['id_city'],
							'id_source' => $value['id_source'],
							'source' => $value['source'],
							'start_date' => $value['start_date'],
							'created_at' => date('Y-m-d H:i:s'),
							'created_by' => $this->_uid
						);
						$db_department->insert('tbl_customer', $db_insert);
					}else{
                        $id_group = $value['id_group'];

						$db_department->where('id', $checkdt['id']);
						$db_department->where('mobile', $value['mobile']);
						$db_department->update('tbl_customer', array(
							'id_call_status' => null,
							'id_call_status_c1' => null,
							'id_call_status_c2' => null,
							'end_ext' => null,
							'end_time' => null,
							'status' => 'new',
							'created_at' => date('Y-m-d H:i:s'),
							'created_by' => $this->_uid
						));
					}

					$db_center->where('id', $value['id']);
					$db_center->update('tbl_customer', array('id_department' => $_id_department, 'status' => 'assign'));

					$db_global = array(
						'id_department' => $_id_department,
                        'status' => 'assign'
					);

					$this->db->where('mobile', $value['mobile']);
					$this->db->update('tbl_customer', $db_global);
				}
			}
		}
		$rtn_str = 'success';

		echo $rtn_str;
    }

    function unassign(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Khách hàng', base_url() . 'customer'),
            array('Lấy khách hàng đã phân', ''),
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

            $id_city = isset($request['id_city']) ? $request['id_city'] : false;
            $id_department = isset($request['id_department']) ? $request['id_department'] : false;
            $id_group = isset($request['id_group']) ? $request['id_group'] : false;
            $id_agent = isset($request['id_agent']) ? $request['id_agent'] : false;
            $id_source = isset($request['id_source']) ? $request['id_source'] : false;
            $id_fileup = isset($request['id_fileup']) ? $request['id_fileup'] : false;
            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;
            
            $db_center = $this->setdbconnect($this->_id_center, 'center');

            $db_center->start_cache();
            $db_center->select('id,fullname,mobile,source,end_ext,created_at');
            $db_center->where('status', 'assign');
            $db_center->where('callval is null');
            #$db_center->where('start_ext is not null');

            if( isset($inputsearch) AND !empty($inputsearch) AND $inputsearch ){
            	$db_center->where('mobile like "%' . trim($inputsearch) . '%"');
            }else{
                if($id_city){
                    $db_center->where('id_city in(' . $id_city . ')');
                }
                if($id_department){
                    $db_center->where('id_department in(' . $id_department . ')');
                }
                if($id_group){
                    $db_center->where('id_group in(' . $id_group . ')');
                }
                if( $id_agent ){
                    $db_center->where('id_agent in(' . $id_agent . ')');
                }else{
                    $db_center->where('id_agent is null');
                }
                if( $id_source ){
                    $db_center->where('id_source in(' . $id_source . ')');
                }

                if( $id_fileup ){
                    $db_center->where('id_fileup in(' . $id_fileup . ')');
                }
            }

            $db_center->from('tbl_customer');

            $db_center->stop_cache();
            $total = $db_center->count_all_results();

            $db_center->limit($limit, $offset);
            $dataResult = $db_center->get()->result_array();

            $db_center->flush_cache();
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
            '_wth_mobile'   => 120,
            '_wth_source' 	=> 175,
            '_wth_time'     => 160,
            '_wth_ext'		=> 60,
            '_wth_action' 	=> 60,
        ));

    	$data['content'] = 'customer/unassign';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function acunassign(){
        $hdidselected = ( isset($_POST['hdidselected']) AND !empty($_POST['hdidselected']) ) ? $_POST['hdidselected'] : false;
        $id_city = ( isset($_POST['id_city']) AND !empty($_POST['id_city']) ) ? $_POST['id_city'] : false;
        $id_department = ( isset($_POST['id_department']) AND !empty($_POST['id_department']) ) ? $_POST['id_department'] : false;
        $id_group = ( isset($_POST['id_group']) AND !empty($_POST['id_group']) ) ? $_POST['id_group'] : false;
        $id_agent = ( isset($_POST['id_agent']) AND !empty($_POST['id_agent']) ) ? $_POST['id_agent'] : false;
        $id_source = ( isset($_POST['id_source']) AND !empty($_POST['id_source']) ) ? $_POST['id_source'] : false;
        $id_fileup = ( isset($_POST['id_fileup']) AND !empty($_POST['id_fileup']) ) ? $_POST['id_fileup'] : false;
        $inputsearch = ( isset($_POST['inputsearch']) AND !empty($_POST['inputsearch']) ) ? $_POST['inputsearch'] : false;

        $db_center = $this->setdbconnect($this->_id_center, 'center');
    	$rtn_str = 'error';
    	if( isset($hdidselected) AND !empty($hdidselected) AND $hdidselected ){
            $dt_unassign = array();
            $db_center->select('id,id_department,id_group,id_agent');
            $db_center->where('id in(' . $hdidselected . ')');
            $dt_unassign = $db_center->get('tbl_customer')->result_array();

            if(isset($dt_unassign) AND !empty($dt_unassign)){
                foreach ($dt_unassign as $key => $value) {
                    $id = $value['id'];
                    $id_department = $value['id_department'];
                    $id_group = $value['id_group'];
                    $id_agent = $value['id_agent'];

                    if(isset($id_department) AND !empty($id_department) AND $id_department){
                        $db_department = $this->setdbconnect($id_department, 'department');

                        $db_department->where('id_link', $id);
                        $db_department->delete('tbl_customer');
                    }

                    if(isset($id_group) AND !empty($id_group) AND $id_group){
                        $db_group = $this->setdbconnect($id_group, 'group');

                        $db_group->where('id_link', $id);
                        $db_group->delete('tbl_customer');
                    }
                }
                $db_center->where('id in(' . $hdidselected . ')');
                $dt_update = array(
                    'status'=>'new',
                    'id_agent'=>NULL,
                    'start_ext'=>NULL,
                    'end_ext'=>NULL,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'last_assign_by'=>$this->_uid
                );
                if( $db_center->update('tbl_customer', $dt_update) !== FALSE ){
                    $rtn_str = 'success';
                }else{
                    $rtn_str = 'fail';
                }
            }
    	}else{
            $dt_unassign = array();
            $db_center->select('id,id_department,id_group,id_agent');
            $db_center->where('status', 'assign');
            $db_center->where('callval is null');

            if( isset($inputsearch) AND !empty($inputsearch) AND $inputsearch ){
                $db_center->where('mobile like "%' . trim($inputsearch) . '%"');
            }else{
                if($id_city){
                    $db_center->where('id_city in(' . $id_city . ')');
                }
                if($id_department){
                    $db_center->where('id_department in(' . $id_department . ')');
                }
                if($id_group){
                    $db_center->where('id_group in(' . $id_group . ')');
                }
                if( $id_agent ){
                    $db_center->where('id_agent in(' . $id_agent . ')');
                }else{
                    $db_center->where('id_agent is null');
                }
                if( $id_source ){
                    $db_center->where('id_source in(' . $id_source . ')');
                }

                if( $id_fileup ){
                    $db_center->where('id_fileup in(' . $id_fileup . ')');
                }
            }
            $dt_unassign = $db_center->get('tbl_customer')->result_array();

            if(isset($dt_unassign) AND !empty($dt_unassign)){
                $hdidselected = '';
                foreach ($dt_unassign as $key => $value) {
                    $id = $value['id'];
                    $hdidselected .= ','.$id;
                    $id_department = $value['id_department'];
                    $id_group = $value['id_group'];
                    $id_agent = $value['id_agent'];

                    if(isset($id_department) AND !empty($id_department) AND $id_department){
                        $db_department = $this->setdbconnect($id_department, 'department');

                        $db_department->where('id_link', $id);
                        $db_department->delete('tbl_customer');
                    }

                    if(isset($id_group) AND !empty($id_group) AND $id_group){
                        $db_group = $this->setdbconnect($id_group, 'group');

                        $db_group->where('id_link', $id);
                        $db_group->delete('tbl_customer');
                    }
                }
                if(isset($hdidselected) AND !empty($hdidselected)){
                    $hdidselected = substr($hdidselected, 1);
                    $db_center->where('id in(' . $hdidselected . ')');
                    $dt_update = array(
                        'status'=>'new',
                        'id_agent'=>NULL,
                        'start_ext'=>NULL,
                        'end_ext'=>NULL,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'last_assign_by'=>$this->_uid
                    );
                    if( $db_center->update('tbl_customer', $dt_update) !== FALSE ){
                        $rtn_str = 'success';
                    }else{
                        $rtn_str = 'fail';
                    }
                }else{
                    $rtn_str = 'success';
                }
            }
        }

    	echo $rtn_str;
    }

    function detail($id=false){
    	$data['content'] = 'customer/detail';
        $this->setlayout($data, 'v2/dialog');
    }
}