<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Inout extends MY_Controller {
	private $aginout = array();

	public function __construct()
    {
        parent::__construct();

        $this->db->select('username');
        $this->db->where('id_department', $this->_id_department);
        $this->db->where('roles', 'staff');
        $inout = $this->db->get('tbl_accounts')->result_array();
        foreach ($inout as $key => $value) {
            array_push($this->aginout, $value['username']);
        }
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('In-Out', ''),
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
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            $enddate = $startdate;

            $this->db->start_cache();

            $this->db->select('acc.full_name, log.username, log.type, log.datetime');

            $this->db->from('tbl_auth_log as log');
            $this->db->join('tbl_accounts as acc', 'acc.username=log.username');
            $this->db->where_in('log.username', $this->aginout);

            if($inputsearch){
                $this->db->where('(log.username like "%'.$inputsearch.'%")');
            }

        	if($startdate){
                $this->db->where('log.datetime > ', $startdate . ' 07:00:00');
                $this->db->where('log.datetime < ', $enddate . ' 22:00:00');
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

        $data['content'] = 'inout';
        $this->setlayout($data, 'v2/'.$this->_role);
    }
}
?>