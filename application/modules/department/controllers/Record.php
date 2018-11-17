<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Record extends MY_Controller {
	private $dbrd = false;
    private $agrd = array();
	public function __construct()
    {
        parent::__construct();
		
		if( $this->_role != 'department' ){
            echo 'Bạn không có quyền trong chức năng này';
        }
	
        $host = '192.168.1.252';
        $user = 'haha';
        $pass = '123456';
        $dbname = 'haha';
        $port = 3306;

        $_dbrd['hostname'] = $host;
        $_dbrd['username'] = $user;
        $_dbrd['password'] = $pass;
        $_dbrd['database'] = $dbname;
        $_dbrd['dbdriver'] = 'mysqli';
        $_dbrd['port']     = $port;
        $_dbrd['dbprefix'] = '';
        $_dbrd['pconnect'] = FALSE;
        $_dbrd['db_debug'] = TRUE;
        $_dbrd['cache_on'] = FALSE;
        $_dbrd['cachedir'] = '';
        $_dbrd['char_set'] = 'utf8';
        $_dbrd['dbcollat'] = 'utf8_general_ci';
        $_dbrd['swap_pre'] = '';
        $_dbrd['encrypt'] = FALSE;
        $_dbrd['compress'] = FALSE;
        $_dbrd['stricton'] = FALSE;
        $_dbrd['failover'] = array();
        $_dbrd['save_queries'] = TRUE;

        $this->dbrd = $this->load->database($_dbrd, TRUE);

        $this->db->select('ext');
        $this->db->where('id_department', $this->_id_department);
        $this->db->where('roles', 'staff');
        $rd = $this->db->get('tbl_accounts')->result_array();
        foreach ($rd as $key => $value) {
            array_push($this->agrd, $value['ext']);
        }
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Quản lý file ghi âm', ''),
        );

        $columns = array(
            array(
                'field' => 'order',
                'title' => 'No',
                'sortable' => false,
                'width' => 50,
            ),
            array(
                'field' => "src",
                'title' => 'Số gọi',
                'width' => 70,
                'sortable' => false,
            ),
            array(
                'field' => "dst",
                'title' => 'Số bị gọi',
                'width' => 150,
                'sortable' => false,
            ),
            array(
                'field' => "calldate",
                'title' => 'T/G Gọi',
                'width' => 170,
                'sortable' => false,
            ),
            array(
                'field' => "duration",
                'title' => 'T/Gian',
                'width' => 70,
                'sortable' => false,
            ),
            array(
                'field' => "bill",
                'title' => 'Đ/Thoại',
                'width' => 70,
                'sortable' => false,
            ),
            array(
                'field' => "disposition",
                'title' => 'Trạng thái',
                'width' => 120,
                'sortable' => false,
            ),
            array(
                'field' => "uniqueid",
                'title' => 'Mã call',
                'sortable' => false,
            ),
            array(
                'field' => "action",
                'title' => '#',
                'sortable' => false,
                'width' => 100,
            ),
        );
        $data['columns'] = json_encode($columns);

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
            $enddate = isset($request['enddate']) ? $request['enddate'] : date('Y-m-d');

            $this->dbrd->start_cache();

            $this->dbrd->select('uniqueid,calldate,clid,src,dst,lastdata,dstchannel,channel,disposition,duration, (CASE disposition WHEN "ANSWERED" THEN billsec ELSE 0 END) as bill');
            $this->dbrd->from('bit_cdr');

            $this->dbrd->where_in('src', $this->agrd);
            $this->dbrd->where('dst !=', 'main');
            //$this->dbrd->where('LENGTH(dst) >', 6);
            $this->dbrd->like('lastdata', 'SIP/0', 'after');

            if(isset($inputsearch) AND !empty($inputsearch)){
                $this->dbrd->where('(src like "%'.$inputsearch.'%" OR lastdata like "%'.$inputsearch.'%")');
            }

            $this->dbrd->where('calldate >', $startdate . ' 07:00:00');
            $this->dbrd->where('calldate <', $enddate . ' 22:00:00');

            $this->dbrd->stop_cache();
            $total = $this->dbrd->count_all_results();

            $this->dbrd->limit($limit, $offset);
            $dataResult = $this->dbrd->get()->result_array();

            $this->dbrd->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'query' => $this->dbrd->last_query(),
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['content'] = 'record/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function detail(){
        $uniqueid = isset($_GET['uniqueid']) ? $_GET['uniqueid'] : false;
        $base64_file = '';
        if($uniqueid){
            $data_file = file_get_contents('http://192.168.1.17/recordfile.php?uniqid='.$uniqueid);
            $data_file = json_decode($data_file, TRUE);
            if($data_file['type'] != 'fail'){
                $base64_file = 'data:audio/'.$data_file['type'].';base64,' . $data_file['data'];
            }
        }

        $data['base64'] = $base64_file;

        $data['content'] = 'record/detail';
        $this->setlayout($data, 'v2/dialog');
    }

    function dowload(){
        $uniqueid = isset($_GET['uniqueid']) ? $_GET['uniqueid'] : false;
        if( isset($uniqueid) AND $uniqueid ){
            $data_file = file_get_contents('http://192.168.1.17/recordfile.php?uniqid='.$uniqueid);
            $data_file = json_decode($data_file, TRUE);
            if($data_file['type'] != 'fail'){
                $file = $uniqueid.'.'.$data_file['type'];
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=".$file);
                header('Content-Type: application/force-download');
                echo base64_decode($data_file['data']);
            }
        }
    }
}