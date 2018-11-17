<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Support extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        if($this->_role != 'support'){
            die('Bạn không có quyền trong trang này');
        }
    }

    function index(){
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

            $id_center  = isset($request['id_center']) ? $request['id_center'] : false;
            $id_department= isset($request['id_department']) ? $request['id_department'] : false;
            $id_group = isset($request['id_group']) ? $request['id_group'] : false;
            $inputsearch= isset($request['inputsearch']) ? $request['inputsearch'] : '';
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;
            
            $this->db->start_cache();

            $this->db->select('id,full_name,email,ext,mobile,username,status,roles');
            $this->db->from('tbl_accounts');
            $this->db->where_in('roles', array('department', 'group', 'staff'));

            if(isset($inputsearch) AND !empty($inputsearch)){
                $this->db->where('(ext LIKE "%'.$inputsearch.'%" OR mobile LIKE "%'.$inputsearch.'%")');
            }else{
                if($id_group){
                    $this->db->where('id_group', $id_group);
                }else{
                    if($id_department){
                        $this->db->where('id_department', $id_department);
                    }else{
                        if($id_center){
                            $this->db->where('id_center', $id_center);
                        }
                    }
                }
            }

            $this->db->where_not_in('roles', array('admin'));

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
            '_wth_mobile'   => 120,
            '_wth_email'    => 250,
            '_wth_username' => 150,
            '_wth_status'   => 65,
            '_wth_action'   => 60,
        ));

        $center = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'call');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center'] = $center;

        $data['content'] = 'index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }
}