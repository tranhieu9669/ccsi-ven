<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Operator extends MY_Controller {
    private $dbop = false;
    private $agop = array();
	public function __construct()
    {
        parent::__construct();
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
        #$this->db->where('roles', 'operator');
        $this->db->where('id_center', $this->_id_center);
        $op = $this->db->get('tbl_accounts')->result_array();
        foreach ($op as $key => $value) {
            array_push($this->agop, $value['ext']);
        }
    }

    function index(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Thông tin cuộc gọi', ''),
        );

        $dataResult = array();
        if ($this->input->is_ajax_request()) {
            $request    = $_REQUEST;
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            $enddate = isset($request['enddate']) ? $request['enddate'] : date('Y-m-d');

            $data_out = array();
            $this->dbop->select('src, count(1) as total, SUM(CASE WHEN disposition="ANSWERED" AND dcontext="qm-queuedial" THEN 1 ELSE 0 END) as ans, SUM(CASE WHEN disposition="ANSWERED" AND dcontext="qm-queuedial" THEN billsec ELSE 0 END) as bill');
            $this->dbop->from('bit_cdr');
            $this->dbop->where_in('src', $this->agop);
            $this->dbop->where('dst !=', 'main');
            //$this->dbop->where('dcontext', 'qm-queuedial');
            $this->dbop->where('calldate >', $startdate . ' 05:00:00');
            $this->dbop->where('calldate <', $enddate . ' 22:00:00');
            #$this->dbop->like('lastdata', 'SIP/0', 'after');
            $this->dbop->group_by('src');
            $data_out = $this->dbop->get()->result_array();
            $_data_out = array();
            foreach ($data_out as $key => $value) {
                $_data_out[$value['src']] = array(
                    'total' => $value['total'],
                    'ans' => $value['ans'],
                    'bill'  => $value['bill']
                );
            }

            $data_in = array();
            $this->dbop->select('SUBSTRING_INDEX(SUBSTRING_INDEX(dstchannel, "SIP/",  -1), "-", 1) as src, count(1) as total, SUM(CASE WHEN (disposition = "ANSWERED" AND dstchannel != "") THEN billsec ELSE 0 END) as bill');
            $this->dbop->from('bit_cdr');
            $this->dbop->where('dst', 'main');
            $this->dbop->like('dstchannel', 'SIP/4', 'after');
            //$this->dbop->where('calldate >', date('Y-m-d') . ' 07:00:00');
			$this->dbop->where('calldate >', $startdate . ' 05:00:00');
            $this->dbop->where('calldate <', $enddate . ' 22:00:00');
            $this->dbop->group_by('SUBSTRING_INDEX(SUBSTRING_INDEX(dstchannel, "SIP/",  -1), "-", 1)');
            $data_in = $this->dbop->get()->result_array();
            $_data_in = array();
            foreach ($data_in as $key => $value) {
                $_data_in[$value['src']] = array(
                    'total' => $value['total'],
                    'bill'  => $value['bill']
                );
            }

            $this->db->select('full_name,mobile,ext,ext_extend');
            $this->db->where('roles', 'operator');
            $this->db->where('ext is not null');
            $this->db->order_by('id_group', 'asc');
            $op = $this->db->get('tbl_accounts')->result_array();

            foreach ($op as $key => $value) {
                $fullname = $value['full_name'];
                $mobile   = $value['mobile'];
                $position = $value['ext_extend'];
                $ext      = $value['ext'];

                $total_in   = 0;
                $bill_in    = 0;
                $total_out  = 0;
                $total_ans  = 0;
                $bill_out   = 0;

                if( isset($_data_in[$ext]) ){
                    $total_in   = $_data_in[$ext]['total'];
                    $bill_in    = $_data_in[$ext]['bill'];
                }

                if( isset($_data_out[$ext]) ){
                    $total_out  = $_data_out[$ext]['total'];
                    $total_ans  = $_data_out[$ext]['ans'];
                    $bill_out   = $_data_out[$ext]['bill'];
                }

                if($bill_in){
                    $sc_in = $bill_in%60;
                    if($sc_in < 1){
                        $bill_in = ($bill_in/60).':00';
                    }else{
                        $bill_in = (($bill_in-$sc_in)/60).':'.$sc_in;
                    }
                }

                if($bill_out){
                    $sc_out = $bill_out%60;
                    if($sc_out < 1){
                        $bill_out = ($bill_out/60).':00';
                    }else{
                        $bill_out = (($bill_out-$sc_out)/60).':'.$sc_out;
                    }
                }

                $dataResult[] = array(
                    'fullname' => $fullname,
                    'position' => $position,
                    'mobile' => $mobile,
                    'ext' => $ext,
                    'total_in' => $total_in,
                    'bill_in' => $bill_in,
                    'total_out' => $total_out,
                    'total_ans' => $total_ans,
                    'bill_out' => $bill_out,
                );
            }

            $return = array(
                'total' => count($dataResult),
                'data'  => $dataResult,
            );
            echo json_encode($return);
            return;
        }

        $data["limit"] = 100;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 50,
            '_wth_mobile'   => 180,
            '_wth_ext'      => 75,
            '_wth_total'    => 80,
            '_wth_bill'     => 80
        ));

        $data['content'] = 'index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function exoperator(){
        $dataResult = array();
        $startdate = isset($_GET['startdate']) ? $_GET['startdate'] : date('Y-m-d');
        $enddate = isset($_GET['enddate']) ? $_GET['enddate'] : date('Y-m-d');

        $data_out = array();
        $this->dbop->select('src, count(1) as total, SUM(CASE WHEN disposition="ANSWERED" AND dcontext="qm-queuedial" THEN billsec ELSE 0 END) as bill ');
        $this->dbop->from('bit_cdr');
        $this->dbop->where_in('src', $this->agop);
        //$this->dbop->where('dst !=', 'main');
        $this->dbop->where('dcontext', 'qm-queuedial');
        $this->dbop->where('calldate >', $startdate . ' 05:00:00');
        $this->dbop->where('calldate <', $enddate . ' 22:00:00');
        #$this->dbop->like('lastdata', 'SIP/0', 'after');
        $this->dbop->group_by('src');
        $data_out = $this->dbop->get()->result_array();
        $_data_out = array();
        foreach ($data_out as $key => $value) {
            $_data_out[$value['src']] = array(
                'total' => $value['total'],
                'bill'  => $value['bill']
            );
        }

        $data_in = array();
        $this->dbop->select('SUBSTRING_INDEX(SUBSTRING_INDEX(dstchannel, "SIP/",  -1), "-", 1) as src, count(1) as total, SUM(CASE WHEN (disposition = "ANSWERED" AND dstchannel != "") THEN billsec ELSE 0 END) as bill');
        $this->dbop->from('bit_cdr');
        $this->dbop->where('dst', 'main');
        $this->dbop->like('dstchannel', 'SIP/4', 'after');
        //$this->dbop->where('calldate >', date('Y-m-d') . ' 07:00:00');
		$this->dbop->where('calldate >', $startdate . ' 05:00:00');
        $this->dbop->where('calldate <', $enddate . ' 22:00:00');
        $this->dbop->group_by('SUBSTRING_INDEX(SUBSTRING_INDEX(dstchannel, "SIP/",  -1), "-", 1)');
        $data_in = $this->dbop->get()->result_array();
        $_data_in = array();
        foreach ($data_in as $key => $value) {
            $_data_in[$value['src']] = array(
                'total' => $value['total'],
                'bill'  => $value['bill']
            );
        }

        $this->db->select('full_name,mobile,ext,ext_extend');
        $this->db->where('roles', 'operator');
        $this->db->where('ext is not null');
        $this->db->order_by('id_group', 'asc');
        $op = $this->db->get('tbl_accounts')->result_array();

        foreach ($op as $key => $value) {
            $fullname = $value['full_name'];
            $position = $value['ext_extend'];
            $mobile   = $value['mobile'];
            $ext      = $value['ext'];

            $total_in   = 0;
            $bill_in    = 0;
            $total_out  = 0;
            $bill_out   = 0;

            if( isset($_data_in[$ext]) ){
                $total_in   = $_data_in[$ext]['total'];
                $bill_in    = $_data_in[$ext]['bill'];
            }

            if( isset($_data_out[$ext]) ){
                $total_out  = $_data_out[$ext]['total'];
                $bill_out   = $_data_out[$ext]['bill'];
            }

            if($bill_in){
                $sc_in = $bill_in%60;
                if($sc_in < 1){
                    $bill_in = ($bill_in/60).':00';
                }else{
                    $bill_in = (($bill_in-$sc_in)/60).':'.$sc_in;
                }
            }

            if($bill_out){
                $sc_out = $bill_out%60;
                if($sc_out < 1){
                    $bill_out = ($bill_out/60).':00';
                }else{
                    $bill_out = (($bill_out-$sc_out)/60).':'.$sc_out;
                }
            }

            $dataResult[] = array(
                'fullname' => $fullname,
                'position' => $position,
                'mobile' => $mobile,
                'ext' => $ext,
                'total_in' => $total_in,
                'bill_in' => $bill_in,
                'total_out' => $total_out,
                'bill_out' => $bill_out,
            );
        }

        # excel
        require_once( APPPATH  . 'third_party/PHPLib/PHPExcel/IOFactory.php');
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load(APPPATH  . 'third_party/templates/operator.xlsx');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        if(isset($dataResult) AND !empty($dataResult)){
            $row = 3;
            foreach ($dataResult as $key => $value) {
                $fullname = $value['fullname'];
                $position = $value['position'];
                $mobile = $value['mobile'];
                $ext = $value['ext'];
                $total_in = $value['total_in'];
                $bill_in = $value['bill_in'];
                $total_out = $value['total_out'];
                $bill_out = $value['bill_out'];

                $objWorksheet->setCellValue('A'.$row, $fullname);
                $objWorksheet->setCellValue('B'.$row, $position);
                $objWorksheet->setCellValue('C'.$row, $mobile);
                $objWorksheet->setCellValue('D'.$row, $ext);
                $objWorksheet->setCellValue('E'.$row, $total_out);
                $objWorksheet->setCellValue('F'.$row, $bill_out);
                $objWorksheet->setCellValue('G'.$row, $total_in);
                $objWorksheet->setCellValue('H'.$row, $bill_in);

                $row++;
            }
        }

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="operator_'.date('YmdHis').'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    function listcall($calltype='out'){
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
                $this->dbop->like('dstchannel', 'SIP/4', 'after');
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

        $data['content'] = 'listcall';
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