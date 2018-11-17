<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Work extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        if($this->_role != 'department'){
            die('Bạn không có quyền vào đây');
        }
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách nhân viên làm việc', ''),
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

            $this->db->start_cache();
            $this->db->select('conf.id,conf.date,acc.full_name as fullname,conf.agent_ext,conf.pg,conf.mkt,conf.number,conf.status');
            $this->db->where('conf.date', date('Y-m-d'));
            //$this->db->where('conf.status', 'on');

            $this->db->from('tbl_account_commandos_config as conf');
            $this->db->join('tbl_accounts as acc', 'acc.id=conf.agent_id');

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->order_by('conf.status', 'asc');
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

        $data['content'] = 'work/index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function detail($id = 0){
        $success = '';
        $error = '';

        $dateNow = date('Y-m-d');
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(isset($_POST) AND !empty($_POST)){
                $startdate = $_POST['startdate'];
                $enddate = $_POST['enddate'];

                $this->db->where('date >=', $startdate);
                $this->db->where('date <=', $enddate);
                $this->db->update('tbl_account_commandos_config', array('status' => 'off'));

                $startdiff = new DateTime($startdate);
                $enddiff   = new DateTime($enddate);
                $diff      = $enddiff->diff($startdiff)->days;

                unset($_POST['startdate']);
                unset($_POST['enddate']);

                foreach ($_POST as $key => $value) {
                    //echo $key.'-'.$value.'<br>';
                    list($prefix, $agent_id, $agent_ext) = split('[/._]', $key);

                    $pg = 0;
                    $mkt = 0;
                    if($prefix == 'pg'){
                        $pg = 1;

                        if(isset($_POST['mkt_'.$agent_id.'_'.$agent_ext]) AND !empty($_POST['mkt_'.$agent_id.'_'.$agent_ext])){
                            $mkt = 1;
                        }
                    }else if($prefix == 'mkt'){
                        $mkt = 1;

                        if(isset($_POST['pg_'.$agent_id.'_'.$agent_ext]) AND !empty($_POST['pg_'.$agent_id.'_'.$agent_ext])){
                            $pg = 1;
                        }
                    }

                    for ($i=0; $i <= $diff; $i++) {
                        $date = date('Y-m-d', strtotime($dateNow . ' + ' . $i . ' days'));

                        $this->db->select('id');
                        $this->db->where('date', $date);
                        $this->db->where('agent_id', $agent_id);
                        $this->db->where('agent_ext', $agent_ext);
                        $check = $this->db->get('tbl_account_commandos_config')->row_array();

                        if(!isset($check) OR empty($check)){
                            $insert = array(
                                'id_city' => $this->_id_city,
                                'date' => $date,
                                'agent_id' => $agent_id,
                                'agent_ext' => $agent_ext,
                                'pg' => $pg,
                                'mkt' => $mkt,
                                'number' => 0,
                                'status' => 'on'
                            );

                            $this->db->insert('tbl_account_commandos_config', $insert);
                        }else{
                            $update = array(
                                'pg' => $pg,
                                'mkt' => $mkt,
                                'status' => 'on'
                            );

                            $this->db->where('date', $date);
                            $this->db->where('agent_id', $agent_id);
                            $this->db->where('agent_ext', $agent_ext);
                            $this->db->update('tbl_account_commandos_config', $update);
                        }
                    }
                }
                $success = "Cập nhật thành công.";
            }
        }

        $listAgent = array();
        $this->db->select('id,full_name,ext');
        $this->db->where('id_department', $this->_id_department);
        $this->db->where('id_group', 54);
        $this->db->where('roles', 'staff');
        $this->db->where('status', 'on');
        $listAgent = $this->db->get('tbl_accounts')->result_array();
        $data['listAgent'] = $listAgent;


        $detail = array();
        $this->db->select('agent_id,agent_ext,pg,mkt');
        $this->db->where('date', $dateNow);
        $this->db->where('status', 'on');
        $listSelected = $this->db->get('tbl_account_commandos_config')->result_array();

        //echo $this->db->last_query();

        if(isset($listSelected) AND !empty($listSelected)){
            foreach ($listSelected as $key => $value) {
                $agent_id = $value['agent_id'];
                $agent_ext = $value['agent_ext'];
                $pg = $value['pg'];
                $mkt = $value['mkt'];

                if($pg > 0){
                    array_push($detail, 'pg_'.$agent_id.'_'.$agent_ext);
                }

                if($mkt > 0){
                    array_push($detail, 'mkt_'.$agent_id.'_'.$agent_ext);
                }
            }
        }
        $data['detail'] = $detail;

        $data['success'] = $success;
        $data['error'] = $error;

        $data['content'] = 'work/detail';
        $this->setlayout($data, 'v2/dialog');
    }
}