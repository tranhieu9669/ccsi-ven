<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Undata extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Lấy lại khách hàng', ''),
        );

        if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 0;

            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $limit      = $pageSize;
            $offset 	= ($page - 1) * $pageSize;

            $id_center = isset($request['id_center']) ? $request['id_center'] : false;
            $id_department = isset($request['id_department']) ? $request['id_department'] : false;
            $id_group = isset($request['id_group']) ? $request['id_group'] : false;
            $id_agent = isset($request['id_agent']) ? $request['id_agent'] : false;
            $id_call_status = isset($request['id_call_status']) ? $request['id_call_status'] : false;
            $id_call_status_c1 = isset($request['id_call_status_c1']) ? $request['id_call_status_c1'] : false;
            $id_call_status_c2 = isset($request['id_call_status_c2']) ? $request['id_call_status_c2'] : false;
            $id_source = isset($request['id_source']) ? $request['id_source'] : false;
            $closedate = isset($request['closedate']) ? $request['closedate'] : false;
            $closedate2 = isset($request['closedate2']) ? $request['closedate2'] : false;
            $followdate = isset($request['followdate']) ? $request['followdate'] : false;
            $followdate2 = isset($request['followdate2']) ? $request['followdate2'] : false;
            #$inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;
            $inputsearch = false;
			$last_query = '';
            if($id_center){
                $dbcenter = $this->setdbconnect($id_center, 'center');

                $dbcenter->start_cache();
                $dbcenter->select('id, fullname, mobile, end_ext, source, callback, start_date, close_date');
                $dbcenter->from('tbl_customer');
                #$dbcenter->where('end_ext is not null');

                if($id_department){
                    $dbcenter->where('id_department in ('.$id_department.')');
                }
                if($id_group){
                    $dbcenter->where('id_group in ('.$id_group.')');
                }
                if($id_agent){
                    if (strpos($id_agent, '99999') !== false) {
                        $dbcenter->where('(id_agent in ('.$id_agent.') OR id_agent is null)');
                    }else{
                        $dbcenter->where('id_agent in ('.$id_agent.')');
                    }                    
                }

                if($id_call_status){
                    //$dbcenter->where('id_call_status in ('.$id_call_status.')');
                    if (strpos($id_call_status, '99999') !== false) {
                        $dbcenter->where('(id_call_status in ('.$id_call_status.') OR id_call_status is null)');
                    }else{
                        $dbcenter->where('id_call_status in ('.$id_call_status.')');
                    }
                }else{
                    $dbcenter->where('id_call_status is not null');
                }
                if($id_call_status_c1){
                    $dbcenter->where('id_call_status_c1 in ('.$id_call_status_c1.')');
                }
                if($id_call_status_c2){
                    $dbcenter->where('id_call_status_c2 in ('.$id_call_status_c2.')');
                }
                if($id_source){
                    if (strpos($id_source, '99999') !== false) {
                        $dbcenter->where('id_source is null');
                    }else{
                        $dbcenter->where('id_source', $id_source);
                    }
                }

                if($closedate){
                    if($closedate2){
                        $dbcenter->where('close_date >=', $closedate);
                        $dbcenter->where('close_date <=', $closedate2);
                    }else{
                        $dbcenter->where('close_date', $closedate);
                    }
                }

                if($followdate){
                    if($followdate2){
                        $dbcenter->where('callback >', $followdate . ' 07:00:00');
                        $dbcenter->where('callback <', $followdate2 . ' 23:00:00');
                    }else{
                        $dbcenter->where('callback >', $followdate . ' 07:00:00');
                        $dbcenter->where('callback <', $followdate . ' 23:00:00');
                    }
                }

                if($inputsearch){
                    $dbcenter->where('mobile like "%'.$inputsearch.'%"');
                }

                $dbcenter->stop_cache();
                $total = $dbcenter->count_all_results();

                $dbcenter->limit($limit, $offset);
                $dataResult = $dbcenter->get()->result_array();

                $dbcenter->flush_cache();
				
				$last_query = $dbcenter->last_query();
            }
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
				'query' => $last_query,
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 60,
            '_wth_mobile'   => 110,
            '_wth_ext'      => 70,
            '_wth_date'     => 110,
        ));

        # center
        $center = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'call');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center'] = $center;
        # call_status
        $call_status = array();
        $this->db->select('id,name');
        //$this->db->where('status', 'on');
        $this->db->where('undata', 1);
        $call_status = $this->db->get('tbl_call_status')->result_array();
        $call_status[] = array(
            'id' => '99999',
            'name' => 'Không trạng thái'
        );
        $data['call_status'] = $call_status;
        # tbl_source
        $source = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $source = $this->db->get('tbl_source')->result_array();
        $source[] = array(
            'id' => '99999',
            'name' => 'Nguồn giới thiệu'
        );
        $data['source'] = $source;

    	$data['content'] = 'undata/index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function acundata(){
        $rtn = 'error';
    	$id_center = isset($_POST['id_center']) ? $_POST['id_center'] : false;
        $listCustomer = array();
        if($id_center){
            $id_department = isset($_POST['id_department']) ? $_POST['id_department'] : false;
            $id_group = isset($_POST['id_group']) ? $_POST['id_group'] : false;
            $id_agent = isset($_POST['id_agent']) ? $_POST['id_agent'] : false;
            $id_call_status = isset($_POST['id_call_status']) ? $_POST['id_call_status'] : false;
            $id_call_status_c1 = isset($_POST['id_call_status_c1']) ? $_POST['id_call_status_c1'] : false;
            $id_call_status_c2 = isset($_POST['id_call_status_c2']) ? $_POST['id_call_status_c2'] : false;
            $id_source = isset($_POST['id_source']) ? $_POST['id_source'] : false;
            $closedate = isset($_POST['closedate']) ? $_POST['closedate'] : false;
            $closedate2 = isset($_POST['closedate2']) ? $_POST['closedate2'] : false;
            $followdate = isset($_POST['followdate']) ? $_POST['followdate'] : false;
            $followdate2 = isset($_POST['followdate2']) ? $_POST['followdate2'] : false;
            $inputsearch = isset($_POST['inputsearch']) ? $_POST['inputsearch'] : false;

            # SRART LOG
            $data_log = $_POST;
            $insert_log = array(
                'type' => 'Undata',
                'data' => json_encode($data_log),
                'created_by' => $this->_uid,
                'created_at' => date('Y-m-d H:i:s')
            );
            write_log($insert_log);
            # END LOG

            $dbcenter = $this->setdbconnect($id_center, 'center');
            
            $dbcenter->select('id, mobile, id_department, id_group');
            $dbcenter->from('tbl_customer');
            #$dbcenter->where('end_ext is not null');

            if($id_department){
                $dbcenter->where('id_department in ('.$id_department.')');
            }
            if($id_group){
                $dbcenter->where('id_group in ('.$id_group.')');
            }
            if($id_agent){
                if (strpos($id_agent, '99999') !== false) {
                    $dbcenter->where('(id_agent in ('.$id_agent.') OR id_agent is null)');
                }else{
                    $dbcenter->where('id_agent in ('.$id_agent.')');
                }
                //$dbcenter->where('id_agent in ('.$id_agent.')');
            }

            if($id_call_status){
                //$dbcenter->where('id_call_status in ('.$id_call_status.')');
                if (strpos($id_call_status, '99999') !== false) {
                    $dbcenter->where('(id_call_status in ('.$id_call_status.') OR id_call_status is null)');
                }else{
                    $dbcenter->where('id_call_status in ('.$id_call_status.')');
                }
            }else{
                $dbcenter->where('id_call_status is not null');
            }
            if($id_call_status_c1){
                $dbcenter->where('id_call_status_c1 in ('.$id_call_status_c1.')');
            }
            if($id_call_status_c2){
                $dbcenter->where('id_call_status_c2 in ('.$id_call_status_c2.')');
            }
            if($id_source){
                if (strpos($id_source, '99999') !== false) {
                    $dbcenter->where('id_source is null');
                }else{
                    $dbcenter->where('id_source', $id_source);
                }
                #$dbcenter->where('id_source', $id_source);
            }
            if($closedate){
                if($closedate2){
                    $dbcenter->where('close_date >=', $closedate);
                    $dbcenter->where('close_date <=', $closedate2);
                }else{
                    $dbcenter->where('close_date', $closedate);
                }
            }
            
            if($followdate){
                if($followdate2){
                    $dbcenter->where('callback >', $followdate . ' 07:00:00');
                    $dbcenter->where('callback <', $followdate2 . ' 23:00:00');
                }else{
                    $dbcenter->where('callback >', $followdate . ' 07:00:00');
                    $dbcenter->where('callback <', $followdate . ' 23:00:00');
                }
            }

            if($inputsearch){
                //$dbcenter->where('mobile like "%'.$inputsearch.'%"');
                $dbcenter->order_by('id', 'asc');
                $dbcenter->limit($inputsearch);
            }
            $listCustomer = $dbcenter->get()->result_array();

            if(isset($listCustomer) AND !empty($listCustomer)){
                foreach ($listCustomer as $key => $value) {
                    $id = $value['id'];
                    $mobile = $value['mobile'];
                    $id_department = $value['id_department'];
                    $id_group = $value['id_group'];
					# xoa group
					if(isset($id_group) AND !empty($id_group)){
						$dbgroup = $this->setdbconnect($id_group, 'group');
						if($dbgroup){
							$dbgroup->where('id_link', $id);
							$dbgroup->where('mobile', $mobile);
							$dbgroup->delete('tbl_customer');
						}
					}
					# xoa department
					if(isset($id_department) AND !empty($id_department)){
						$dbdepartment = $this->setdbconnect($id_department, 'department');
						if($dbdepartment){
							$dbdepartment->where('id_link', $id);
							$dbdepartment->where('mobile', $mobile);
							$dbdepartment->delete('tbl_customer');
						}
					}
					# cap nhat
					/*$cusupdate = array(
						'id_department' => null,
						'id_group' => null,
						'id_agent' => null,
						'end_ext' => null,
						'end_time' => null,
						'id_call_status' => null,
						'id_call_status_c1' => null,
						'id_call_status_c2' => null,
						'status' => 'new',
						'callval' => null,
						'no_retrieve' => 1,
						'time_retrieve' => date('Y-m-d H:i:s'),
					);
					$dbcenter->where('id', $id);
					$dbcenter->where('mobile', $mobile);
					$dbcenter->update('tbl_customer', $cusupdate);*/

                    #xoa trung tam
                    $dbcenter->where('id', $id);
                    $dbcenter->where('mobile', $mobile);
                    $dbcenter->delete('tbl_customer');
					
                    # update tong
					$this->db->where('mobile', $mobile);
                    $this->db->update('tbl_customer', array(
                        'id_center' => $id_center,
                        'id_department' => null,
                        'id_group' => null,
                        'id_agent' => null,
                        'id_call_status' => null,
                        'id_call_status_c1' => null,
                        'id_call_status_c2' => null,
                        'status' => 'redata',
                        'no_retrieve' => 1,
                        'time_retrieve' => date('Y-m-d H:i:s')
                    ));
                }
            }
            $rtn = 'success';
        }
        echo $rtn;
    }
}