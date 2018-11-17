<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Approved extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        if($this->_role != 'center'){
            die('Bạn không có quyền trong trang này');
        }
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách yêu cầu hỗ trợ', ''),
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

            $id_categories = isset($request['id_categories']) ? $request['id_categories'] : false;
            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;

            $this->db->start_cache();

            $this->db->select('rq.id,rq.mobile,rq.request_at,rq.request_note,rc.name,rq.approved_at,rq.approved_by,rq.approved_note,rq.approved_status');
            $this->db->from('tbl_request as rq');
            $this->db->join('tbl_request_categories as rc', 'rc.id=rq.id_categories and rc.status="on"');
            $this->db->order_by('rq.request_at', 'desc');

            if($inputsearch){
                $this->db->where('rq.mobile like "%'.$inputsearch.'%"');
            }else{
                if($id_categories){
                    $this->db->where('rq.id_categories', $id_categories);
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

        $categories = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $categories = $this->db->get('tbl_request_categories')->result_array();
        $data['categories'] = $categories;

    	$data['content'] = 'approved/index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function action(){
    	$id = isset($_POST['id']) ? $_POST['id'] : false;
    	$status = isset($_POST['status']) ? $_POST['status'] : false;

    	$update = array(
    		'approved_ip' => '',
    		'approved_id' => $this->_id_agent,
    		'approved_at' => date('Y-m-d H:i:s'),
    		'approved_by' => $this->_uid,
		);

    	if($status == 'cancel'){
    		$update['approved_status'] = 'cancel';
    		$this->db->where('id', $id);
    		$this->db->update('tbl_request', $update);
    	}else if($status == 'approved'){
    		// check
    		$detail = array();
    		$this->db->select('rc.id,rc.code,rq.mobile');
    		$this->db->from('tbl_request_categories as rc');
			$this->db->join('tbl_request as rq', 'rq.id_categories=rc.id AND rq.id='.$id);
			$detail = $this->db->get()->row_array();
			if(isset($detail) AND !empty($detail)){
				$code = $detail['code'];
				$mobile = $detail['mobile'];

				if($code == 'DEMO6T'){
					$this->db->where('mobile', $mobile);
					$this->db->update('tbl_customer', array(
						'demo' => 0,
						'app' => 0
					));

					$this->db->where('cus_mobile', $mobile);
					$this->db->where('last_app', 'on');
					$this->db->update('tbl_appointments', array(
						'app_status' => 'cancel',
						'app_created_at' => date('Y-m-d H:i:s'),
					));
				}elseif($code == 'APPCANCEL'){
					$this->db->where('cus_mobile', $mobile);
					$this->db->where('last_app', 'on');
					$this->db->update('tbl_appointments', array(
						'app_status' => 'cancel',
						'app_created_at' => date('Y-m-d H:i:s')
					));
				}
			}
    		// update
    		$update['approved_status'] = 'approved';
    		$this->db->where('id', $id);
    		$this->db->update('tbl_request', $update);
    	}
    }
}