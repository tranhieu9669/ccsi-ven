<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Customer extends MY_Controller {
	public function __construct()
    {
        parent::__construct();

        # city
        $city = array();
        $this->db->where('status', 'on');
        $this->db->order_by('position', 'asc');
        $this->db->order_by('id', 'asc');
        $city = $this->db->get('tbl_city')->result_array();
        $this->_data['city'] = $city;

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
        $agent = $this->db->get('view_account')->result_array();
        $this->_data['agent'] = $agent;
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Khách hàng', ''),
        );

    	$data['content'] = 'customer/index';
    	$this->setlayout($data, 'v2/tmpl');
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
			$inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;
            
            $this->db->start_cache();
            $this->db->select('id,fullname,mobile,source,created_at');
            $this->db->where('status', 'new');
            $this->db->where('start_ext', null);

            if( isset($id_city) AND !empty($id_city) AND $id_city ){
                $this->db->where('id_city in(' . $id_city . ')');
            }
            
            if( isset($id_source) AND !empty($id_source) AND $id_source ){
                $this->db->where('id_source in(' . $id_source . ')');
            }
            
            if( isset($id_fileup) AND !empty($id_fileup) AND $id_fileup ){
                $this->db->where('id_fileup in(' . $id_fileup . ')');
            }
            
            if( isset($inputsearch) AND !empty($inputsearch) AND $inputsearch ){
                $this->db->where('mobile like "%' . trim($inputsearch) . '%"');
            }

            $this->db->from('tbl_customer');

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
            '_wth_mobile'   => 120,
            '_wth_source' 	=> 175,
            '_wth_time'     => 160,
            '_wth_action' 	=> 60,
        ));

    	$data['content'] = 'customer/assign';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function acassign(){
    	$id_agent	= isset($_POST['id_agent']) ? $_POST['id_agent'] : false;
    	$idselected = isset($_POST['hdidselected']) ? $_POST['hdidselected'] : false;
        $id_city = isset($_POST['id_city']) ? $_POST['id_city'] : false;
        $id_source = isset($_POST['id_source']) ? $_POST['id_source'] : false;
        $id_fileup = isset($_POST['id_fileup']) ? $_POST['id_fileup'] : false;
		$inputsearch= isset($_POST['inputsearch']) ? $_POST['inputsearch'] : false;
		$assign_num = isset($_POST['assign_num']) ? $_POST['assign_num'] : 1;

		$agent = array();
		$this->db->select('id, ext');
		$this->db->where('id in('.$id_agent.')');
		$agent = $this->db->get('tbl_agent')->result_array();

		$agentdt = array();
		if( isset($agent) AND !empty($agent) ){
			foreach ($agent as $key => $value) {
				$agentdt[$value['id']] = $value['ext'];
			}
		}

		$rtn_str = 'error';
		if( isset($id_agent) AND !empty($id_agent) AND $id_agent ){
			$list_agent = explode(',', $id_agent);
			if( isset($idselected) AND !empty($idselected) ){
				$list_cus 	= explode(',', $idselected);
				if( count($list_cus) < count($list_agent) ){
					$assign_num = 1;
				}else{
					$assign_num = count($list_cus)/count($list_agent);
				}

				$assign_data = array();
                $assign_log  = array();
				foreach ($list_agent as $key => $value) {
					$assign_id 	= $value;
					$assign_ext = $agentdt[$assign_id];

					$start 	= $i * $assign_num;
					$end 	= ($i + 1) * $assign_num - 1;

					for ($j=$start; $j <= $end; $j++) { 
						$assign_cus_id = $list_cus[$j];
						$assign_data[] = array(
							'id'		=> $assign_cus_id, # id customer
                            'id_agent'  => $assign_id,
							'start_ext'	=> $assign_ext,
							'end_ext'	=> $assign_ext,
							'status'	=> 'assign',
							'last_assign_by' => $this->_uid,
							'start_time'=> date('Y-m-d H:i:s'),
						);

                        $assign_log[] = array(
                            'id_customer'=> $assign_cus_id,
                            'ext'        => $assign_ext,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $this->_uid
                        );
					}
				}

				if( isset($assign_data) AND !empty($assign_data) ){
					if( $this->db->update_batch('tbl_customer', $assign_data, 'id') !== FALSE ){
						$rtn_str = 'success';

                        if( isset($assign_log) AND !empty($assign_log) ){
                            $this->db->insert_batch('tbl_customer_assign', $assign_log);
                        }
					}else{
						$rtn_str = 'fail';
					}
				}
			}else{
				$assign_data = array();
                $assign_data_id = array();
				foreach ($list_agent as $key => $value) {
					$assign_id 	= $value;
					$assign_ext = $agentdt[$assign_id];

					$list_cus = array();
					$this->db->select('id');
	                $this->db->where('status', 'new');
                    if($id_city){
                        $this->db->where('id_city in('.$id_city.')');
                    }
                    if($id_source){
                        $this->db->where('id_source in('.$id_source.')');
                    }
                    if($id_fileup){
                        $this->db->where('id_fileup in('.$id_fileup.')');
                    }
                    if($inputsearch){
                        $this->db->where('mobile LIKE "%'.$inputsearch.'%"');
                    }
                    if( isset($assign_data_id) AND !empty($assign_data_id) ){
                        $this->db->where_not_in('id', $assign_data_id);
                    }
	                $this->db->order_by('rand()');
	                $this->db->limit($assign_num);
	                $list_cus = $this->db->get('tbl_customer')->result_array();

					foreach ($list_cus as $_key => $_val) {
	                	$assign_cus_id = $_val['id'];
                        array_push($assign_data_id, $assign_cus_id);
						$assign_data[] = array(
							'id'		=> $assign_cus_id, # id customer
                            'id_agent'  => $assign_id,
							'start_ext'	=> $assign_ext,
							'end_ext'	=> $assign_ext,
							'status'	=> 'assign',
							'last_assign_by' => $this->_uid,
							'start_time'=> date('Y-m-d H:i:s'),
						);

                        $assign_log[] = array(
                            'id_customer'=> $assign_cus_id,
                            'ext'        => $assign_ext,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $this->_uid
                        );
	                }	                
				}

				if( isset($assign_data) AND !empty($assign_data) ){
					if( $this->db->update_batch('tbl_customer',$assign_data, 'id') !== FALSE ){
						$rtn_str = 'success';
                        if( isset($assign_log) AND !empty($assign_log) ){
                            $this->db->insert_batch('tbl_customer_assign', $assign_log);
                        }
					}else{
						$rtn_str = 'fail';
					}
				}
			}
		}

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

            $id_agent   = isset($request['id_agent']) ? $request['id_agent'] : false;
            $id_source	= isset($request['id_source']) ? $request['id_source'] : false;
            $id_fileup  = isset($request['id_fileup']) ? $request['id_fileup'] : false;
			$inputsearch= isset($request['inputsearch']) ? $request['inputsearch'] : false;
            
            $this->db->start_cache();
            $this->db->select('id,fullname,mobile,source,end_ext,created_at');
            $this->db->where('status', 'assign');
            if( isset($inputsearch) AND !empty($inputsearch) AND $inputsearch ){
            	$this->db->where('mobile like "%' . trim($inputsearch) . '%"');
            }

            if( isset($id_agent) AND !empty($id_agent) AND $id_agent ){
            	$this->db->where('id_agent in(' . $id_agent . ')');
            }

            if( isset($id_source) AND !empty($id_source) AND $id_source ){
                $this->db->where('id_source in(' . $id_source . ')');
            }

            if( isset($id_fileup) AND !empty($id_fileup) AND $id_fileup ){
                $this->db->where('id_fileup in(' . $id_fileup . ')');
            }

            $this->db->from('tbl_customer');

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
            '_wth_mobile'   => 120,
            '_wth_source' 	=> 175,
            '_wth_time'     => 160,
            '_wth_ext'		=> 60,
            '_wth_action' 	=> 60,
        ));

    	$data['content'] = 'customer/unassign';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function acunassign(){
    	$idselected = ( isset($_POST['hdidselected']) AND !empty($_POST['hdidselected']) ) ? $_POST['hdidselected'] : false;

    	$rtn_str = 'error';
    	if( isset($idselected) AND !empty($idselected) ){
    		$this->db->where('id in(' . $idselected . ')');
    		if( $this->db->update('tbl_customer', array('status'=>'new', 'id_agent'=>NULL, 'start_ext'=>NULL, 'end_ext'=>NULL, 'last_assign_by'=>$this->_uid)) !== FALSE ){
    			$rtn_str = 'success';

                $list_cus = explode(',', $idselected);

                if( isset($list_cus) AND !empty($list_cus) ){
                    $unassign_log = array();
                    foreach ($list_cus as $key => $value) {
                        $unassign_log[] = array(
                            'id_customer'=> $value,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $this->_uid
                        );
                    }

                    if( isset($unassign_log) AND !empty($unassign_log) ){
                        $this->db->insert_batch('tbl_customer_unassign', $unassign_log);
                    }
                }

    		}else{
    			$rtn_str = 'fail';
    		}
    	}else{
            $id_agent = ( isset($_POST['id_agent']) AND !empty($_POST['id_agent']) ) ? $_POST['id_agent'] : false;
            $id_source = ( isset($_POST['id_source']) AND !empty($_POST['id_source']) ) ? $_POST['id_source'] : false;
            $id_fileup = ( isset($_POST['id_fileup']) AND !empty($_POST['id_fileup']) ) ? $_POST['id_fileup'] : false;
            $inputsearch = ( isset($_POST['inputsearch']) AND !empty($_POST['inputsearch']) ) ? $_POST['inputsearch'] : false;

            $this->db->where('status', 'assign');
            if( $id_agent OR $id_source OR $id_fileup OR $inputsearch ){
                if($id_agent){
                    $this->db->where('id_agent in('.$id_agent.')');
                }
                if($id_source){
                    $this->db->where('id_source in('.$id_source.')');
                }
                if($id_fileup){
                    $this->db->where('id_fileup in('.$id_fileup.')');
                }
                if($inputsearch){
                    $this->db->where('mobile like "%' . trim($inputsearch) . '%"');
                }
            }

            if( $this->db->update('tbl_customer', array('status'=>'new', 'id_agent'=>NULL, 'start_ext'=>NULL, 'end_ext'=>NULL, 'last_assign_by'=>$this->_uid)) !== FALSE ){
                $rtn_str = 'success';
            }else{
                $rtn_str = 'fail';
            }
        }

    	echo $rtn_str;
    }

    function detail($id=false){
    	$data['content'] = 'customer/detail';
        $this->setlayout($data, 'v2/dialog');
    }
}