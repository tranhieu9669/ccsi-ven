<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Customer extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        if($this->_role != 'department'){
            die('Bạn không có quyền vào đây');
        }
        # city
        $city = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
		$this->db->order_by('position');
        $city = $this->db->get('tbl_city')->result_array();
        $this->_data['city'] = $city;

        # center
        $group = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->where('id_department', $this->_id_department);
        $group = $this->db->get('tbl_groups')->result_array();
        $this->_data['group'] = $group;

        # source
        $source = array();
        $this->db->select('id,name');
        $source = $this->db->get('tbl_source')->result_array();
        $this->_data['source'] = $source;

        # Agent
        $agent = array();
        $this->db->select('id,full_name,ext');
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $this->db->where('id_department', $this->_id_department);
        $agent = $this->db->get('tbl_accounts')->result_array();
        $this->_data['agent'] = $agent;
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
            $offset = ($page - 1) * $pageSize;

            $id_city = isset($request['id_city']) ? $request['id_city'] : false;
            $id_source = isset($request['id_source']) ? $request['id_source'] : false;
            $id_fileup = isset($request['id_fileup']) ? $request['id_fileup'] : false;
            $demo6t = isset($request['demo6t']) ? $request['demo6t'] : 0;
			$inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;

            $db_department = $this->setdbconnect($this->_id_department, 'department');
            $db_department->start_cache();
            $db_department->select('id,fullname,mobile,source,created_at');
            $db_department->where('status', 'new');

            if( isset($id_city) AND !empty($id_city) AND $id_city ){
                $db_department->where('id_city in(' . $id_city . ')');
            }

            if($id_source){
                $db_department->where('id_source', $id_source);
            }

            if($demo6t){
                $db_department->where('id_fileup', 99999);
            }else{
                $db_department->where('(id_fileup < 99999 or id_fileup is null)');
            }

            $db_department->from('tbl_customer');

            $db_department->stop_cache();
            $total = $db_department->count_all_results();

            $db_department->limit($limit, $offset);
            $dataResult = $db_department->get()->result_array();

            $db_department->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'query' => $db_department->last_query()
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
        $id_group = isset($_POST['id_group']) ? $_POST['id_group'] : false;
        $id_source = isset($_POST['id_source']) ? $_POST['id_source'] : false;
        $demo6t = isset($_POST['demo6t']) ? $_POST['demo6t'] : 0;
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
        $rtn_str = 'error';
		$group = array();
		if($id_group){
			$group = explode(',', $id_group);
			$db_center = $this->setdbconnect($this->_id_center, 'center');
			$db_department = $this->setdbconnect($this->_id_department, 'department');
			foreach ($group as $_key => $_value) {
				$_id_group = $_value;
				$db_group = $this->setdbconnect($_id_group, 'group');

				$db_assign = array();
				$db_department->select('id,id_link,id_fileup,id_group,first_name,last_name,fullname,mobile,gender,code_city,id_city,id_source,source,start_date');
				if($id_city){
					$db_department->where('id_city in('.$id_city.')');
				}
				if($id_source){
					$db_department->where('id_source', $id_source);
				}
                if($demo6t){
                    $db_department->where('id_fileup', 99999);
                }else{
                    $db_department->where('(id_fileup < 99999 or id_fileup is null)');
                }
				$db_department->where('status', 'new');
				$db_department->order_by('rand()');
				$db_department->limit($assign_num);
				$db_assign = $db_department->get('tbl_customer')->result_array();

				if( isset($db_assign) AND !empty($db_assign) ){
					foreach ($db_assign as $key => $value) {
                        $_id_group_del = $value['id_group'];
                        if(isset($_id_group_del) AND !empty($_id_group_del)){
                            $db_group_del = $this->setdbconnect($_id_group_del, 'group');
                            if(isset($db_group_del) AND !empty($db_group_del) AND $db_group_del){
                                $db_group_del->where('mobile', $value['mobile']);
                                $db_group_del->delete('tbl_customer');
                            }
                        }
                        
						$checkdt = false;
						$db_group->select('id');
						$db_group->where('mobile', $value['mobile']);
						$db_group->limit(1);
						$checkdt = $db_group->get('tbl_customer')->row_array();

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

							$db_group->insert('tbl_customer', $db_insert);
						}else{
							$db_group->where('id', $checkdt['id']);
							$db_group->where('mobile', $value['mobile']);
							$db_group->update('tbl_customer', array(
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

						#$db_department->where('id_link', $value['id_link']);
                        $db_department->where('mobile', $value['mobile']);
						$db_department->update('tbl_customer', array('id_group' => $_id_group, 'status' => 'assign'));

						#$db_center->where('id', $value['id_link']);
                        $db_center->where('mobile', $value['mobile']);
						$db_center->update('tbl_customer', array('id_group' => $_id_group));

						$this->db->where('mobile', $value['mobile']);
						$this->db->update('tbl_customer', array('id_group' => $_id_group));
					}
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