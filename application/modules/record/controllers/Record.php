<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Record extends MY_Controller {
    private $dbop = false;
    private $agop = array();
	public function __construct()
    {
        parent::__construct();
		if( in_array($this->_role, array('staff', 'group', 'department')) ){
            echo 'Bạn không có quyền trong chức năng này';
        }
		
        $host = '192.168.1.252';
        $user = 'haha';
        $pass = '123456';
        $dbname = 'haha';
        $port = 3306;

        $_dbop['hostname'] = $host;
        $_dbop['username'] = $user;
        $_dbop['password'] = $pass;
        $_dbop['database'] = $dbname;
        $_dbop['dbdriver'] = 'mysqli';
        $_dbop['port']     = $port;
        $_dbop['dbprefix'] = '';
        $_dbop['pconnect'] = FALSE;
        $_dbop['db_debug'] = TRUE;
        $_dbop['cache_on'] = FALSE;
        $_dbop['cachedir'] = '';
        $_dbop['char_set'] = 'utf8';
        $_dbop['dbcollat'] = 'utf8_general_ci';
        $_dbop['swap_pre'] = '';
        $_dbop['encrypt'] = FALSE;
        $_dbop['compress'] = FALSE;
        $_dbop['stricton'] = FALSE;
        $_dbop['failover'] = array();
        $_dbop['save_queries'] = TRUE;

        $this->dbop = $this->load->database($_dbop, TRUE);

        $this->db->select('ext');
        //$this->db->where_in('roles', array('staff','operator','confirm'));
		$this->db->where_in('roles', array('staff','operator','group','department','confirm'));
        $op = $this->db->get('tbl_accounts')->result_array();
        foreach ($op as $key => $value) {
            array_push($this->agop, $value['ext']);
        }
    }

    function index($calltype='out'){
        $data['calltype'] = $calltype;

        if($calltype == 'out'){
            $data['breadcrumb'] = array(
                array('Home', base_url()),
                array('Danh sách cuộc gọi ra', ''),
            );
        }else{
            $data['breadcrumb'] = array(
                array('Home', base_url()),
                array('Danh sách cuộc gọi vao', ''),
            );
        }

        if($calltype=='out'){
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
                    'width' => 80,
                    'sortable' => false,
                ),
                array(
                    'field' => "dst",
                    'title' => 'Số bị gọi',
                    'width' => 160,
                    'sortable' => false,
                ),
                array(
                    'field' => "calldate",
                    'title' => 'T/G Gọi',
                    'width' => 180,
                    'sortable' => false,
                ),
                array(
                    'field' => "duration",
                    'title' => 'Thời gian',
                    'width' => 120,
                    'sortable' => false,
                ),
                array(
                    'field' => "bill",
                    'title' => 'Đàm thoại',
                    'width' => 120,
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
        }else{
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
                    'width' => 160,
                    'sortable' => false,
                ),
                array(
                    'field' => "dstchannel",
                    'title' => 'Số bị gọi',
                    'width' => 160,
                    'sortable' => false,
                ),
                array(
                    'field' => "calldate",
                    'title' => 'T/G Gọi',
                    'width' => 180,
                    'sortable' => false,
                ),
                array(
                    'field' => "duration",
                    'title' => 'Thời gian',
                    'width' => 120,
                    'sortable' => false,
                ),
                array(
                    'field' => "bill",
                    'title' => 'Đàm thoại',
                    'width' => 120,
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
        }

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

            $this->dbop->start_cache();

            $this->dbop->select('uniqueid,calldate,clid,src,(CASE dst WHEN "s" THEN SUBSTRING_INDEX(SUBSTRING_INDEX(lastdata, "SIP/",  -1), "@", 1) ELSE dst END) as dst,SUBSTRING_INDEX(SUBSTRING_INDEX(dstchannel, "SIP/",  -1), "-", 1) as dstchannel,channel,disposition,duration, (CASE disposition WHEN "ANSWERED" THEN billsec ELSE 0 END) as bill');
            $this->dbop->from('bit_cdr');

            if($calltype=='out'){
                $this->dbop->where_in('src', $this->agop);
                $this->dbop->where('dst !=', 'main');
                //$this->dbop->like('lastdata', 'SIP/0', 'after');

                if(isset($inputsearch) AND !empty($inputsearch)){
                    $this->dbop->where('(src like "%'.$inputsearch.'%" OR lastdata like "%'.$inputsearch.'%")');
                }
            }else{
                $this->dbop->where('dst', 'main');
                #$this->dbop->like('dstchannel', 'SIP/4', 'after');
                if(isset($inputsearch) AND !empty($inputsearch)){
                    $this->dbop->where('(src like "%'.$inputsearch.'%" OR dstchannel like "%'.$inputsearch.'%")');
                }
            }
            $this->dbop->where('calldate >', $startdate . ' 07:00:00');
            $this->dbop->where('calldate <', $enddate . ' 22:00:00');

            $this->dbop->stop_cache();
            $total = $this->dbop->count_all_results();

            $this->dbop->limit($limit, $offset);
            $dataResult = $this->dbop->get()->result_array();

            $this->dbop->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'query' => $this->dbop->last_query(),
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['content'] = 'index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function infordetail(){
        $calltype = isset($_GET['calltype']) ? $_GET['calltype'] : 'out';
        $inputsearch = isset($_GET['inputsearch']) ? $_GET['inputsearch'] : false;
        $startdate = isset($_GET['startdate']) ? $_GET['startdate'] : date('Y-m-d');
        $enddate = isset($_GET['enddate']) ? $_GET['enddate'] : date('Y-m-d');

        $detail = array();
        $this->dbop->select('COUNT(1) as total, SUM(CASE disposition WHEN "ANSWERED" THEN billsec ELSE 0 END) as bill');
        if($calltype=='out'){
            $this->dbop->where_in('src', $this->agop);
            $this->dbop->where('dst !=', 'main');
            #$this->dbop->like('lastdata', 'SIP/0', 'after');

            if(isset($inputsearch) AND !empty($inputsearch)){
                $this->dbop->where('(src like "%'.$inputsearch.'%" OR lastdata like "%'.$inputsearch.'%")');
            }
        }else{
            $this->dbop->where('dst', 'main');
            if(isset($inputsearch) AND !empty($inputsearch)){
                $this->dbop->where('(src like "%'.$inputsearch.'%" OR dstchannel like "%'.$inputsearch.'%")');
            }
        }
        $this->dbop->where('calldate >', $startdate . ' 07:00:00');
        $this->dbop->where('calldate <', $enddate . ' 22:00:00');
        $this->dbop->from('bit_cdr');

        $detail = $this->dbop->get()->row_array();

        echo json_encode($detail);
    }

    function dowload(){
        $uniqueid = isset($_GET['uniqueid']) ? $_GET['uniqueid'] : false;
        if( isset($uniqueid) AND $uniqueid ){
            /*$file = base_url() . 'Q-GEN_vi_mb-2013-12-06-1386317413.34985.ogg';
            header("Content-Description: File Transfer"); 
            header("Content-Type: application/octet-stream"); 
            header("Content-Disposition: attachment; filename=" . basename($file));
            readfile ($file);*/

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

    function detail(){
        $uniqueid = isset($_GET['uniqueid']) ? $_GET['uniqueid'] : false;
        #$uniqueid = '1488977299.7946';
        $base64_file = '';
        if($uniqueid){
            $data_file = file_get_contents('http://192.168.1.17/recordfile.php?uniqid='.$uniqueid);
            $data_file = json_decode($data_file, TRUE);
            if($data_file['type'] != 'fail'){
                $base64_file = 'data:audio/'.$data_file['type'].';base64,' . $data_file['data'];
            }
        }

        $data['base64'] = $base64_file;

        $data['content'] = 'detail';
        $this->setlayout($data, 'v2/dialog');
    }

    function statistics(){
		$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Thống kê cuộc gọi', ''),
        );

        $data['content'] = 'statistics';
        $this->setlayout($data, 'v2/'.$this->_role);
    }
}