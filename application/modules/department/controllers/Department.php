<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Department extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách tài khoản', ''),
        );

        $data['content'] = 'index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function account(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách tài khoản', ''),
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

            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;

            $this->db->start_cache();

            $this->db->select('full_name,mobile,ext,roles,dbname');
            $this->db->from('tbl_accounts');
            $this->db->where('status','on');
            $this->db->where('roles !=','department');
            $this->db->where('id_department',$this->_id_department);

            if($inputsearch){
                $this->db->where('(full_name like "%'.$inputsearch.'%" OR ext like "%'.$inputsearch.'%")');
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
            '_wth_order'    => 50,
            '_wth_mobile'   => 150,
            '_wth_ext'      => 100,
            '_wth_dbname'   => 250,
            '_wth_role'     => 120,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'account';
        $this->setlayout($data, 'v2/'.$this->_role);
    }
}