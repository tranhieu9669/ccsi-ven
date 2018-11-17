<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Center extends MY_Controller {
    private $dbcc = false;
    private $agtele = array();

	public function __construct()
    {
        parent::__construct();
        if($this->_role != 'center'){
            die('Bạn không có quyền trong trang này');
        }
        $host = '192.168.1.252';
        $user = 'haha';
        $pass = '123456';
        $dbname = 'haha';
        $port = 3306;

        $_dbcc['hostname'] = $host;
        $_dbcc['username'] = $user;
        $_dbcc['password'] = $pass;
        $_dbcc['database'] = $dbname;
        $_dbcc['dbdriver'] = 'mysqli';
        $_dbcc['port']     = $port;
        $_dbcc['dbprefix'] = '';
        $_dbcc['pconnect'] = FALSE;
        $_dbcc['db_debug'] = TRUE;
        $_dbcc['cache_on'] = FALSE;
        $_dbcc['cachedir'] = '';
        $_dbcc['char_set'] = 'utf8';
        $_dbcc['dbcollat'] = 'utf8_general_ci';
        $_dbcc['swap_pre'] = '';
        $_dbcc['encrypt'] = FALSE;
        $_dbcc['compress'] = FALSE;
        $_dbcc['stricton'] = FALSE;
        $_dbcc['failover'] = array();
        $_dbcc['save_queries'] = TRUE;

        $this->dbcc = $this->load->database($_dbcc, TRUE);

        $this->db->select('ext');
        $this->db->where('roles', 'staff');
        $this->db->where('status', 'on');
        $this->db->group_by('ext');
        $this->db->order_by('id_center,id_department,id_group');
        $tele = $this->db->get('tbl_accounts')->result_array();

        foreach ($tele as $key => $value) {
            array_push($this->agtele, $value['ext']);
        }
    }

    function formatTime($second = false){
        $output = '';

        $_hours = '00';
        $_minutes = '00';
        $_second = '00';

        if($second AND is_int($second)){
            $_h = intval($second/(60*60));
            if( $_h > 0 ){
                if($_h < 10)
                    $_hours = '0'.$_h;
                else
                    $_hours = $_h;
            }

            $_m = intval( ($second - $_h*(60*60))/60);
            if($_m > 0){
                if($_m < 10)
                    $_minutes = '0'.$_m;
                else
                    $_minutes = $_m;
            }

            $_s = intval( $second - $_h*(60*60) - $_m*60);
            if($_s > 0){
                if($_s < 10)
                    $_second = '0'.$_s;
                else
                    $_second = $_s;
            }
        }

        $output = $_hours . ':' . $_minutes . ':' . $_second;
        return $output;
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
            $this->dbcc->select('src, count(1) as total, SUM(CASE disposition WHEN "ANSWERED" THEN 1 ELSE 0 END) as ans, SUM(CASE disposition WHEN "ANSWERED" THEN billsec ELSE 0 END) as bill');
            $this->dbcc->from('bit_cdr');
            $this->dbcc->where_in('src', $this->agop);
            $this->dbcc->where('dst !=', 'main');
            $this->dbcc->where('calldate >', $startdate . ' 05:00:00');
            $this->dbcc->where('calldate <', $enddate . ' 22:00:00');
            #$this->dbcc->like('lastdata', 'SIP/0', 'after');
            $this->dbcc->group_by('src');
            $data_out = $this->dbcc->get()->result_array();
            $_data_out = array();
            foreach ($data_out as $key => $value) {
                $_data_out[$value['src']] = array(
                    'total' => $value['total'],
                    'ans' => $value['ans'],
                    'bill'  => $value['bill']
                );
            }

            $data_in = array();
            $this->dbcc->select('SUBSTRING_INDEX(SUBSTRING_INDEX(dstchannel, "SIP/",  -1), "-", 1) as src, count(1) as total, SUM(CASE WHEN (disposition = "ANSWERED" AND dstchannel != "") THEN billsec ELSE 0 END) as bill');
            $this->dbcc->from('bit_cdr');
            $this->dbcc->where('dst', 'main');
            $this->dbcc->like('dstchannel', 'SIP/4', 'after');
            //$this->dbcc->where('calldate >', date('Y-m-d') . ' 07:00:00');
            $this->dbcc->where('calldate >', $startdate . ' 05:00:00');
            $this->dbcc->where('calldate <', $enddate . ' 22:00:00');
            $this->dbcc->group_by('SUBSTRING_INDEX(SUBSTRING_INDEX(dstchannel, "SIP/",  -1), "-", 1)');
            $data_in = $this->dbcc->get()->result_array();
            $_data_in = array();
            foreach ($data_in as $key => $value) {
                $_data_in[$value['src']] = array(
                    'total' => $value['total'],
                    'bill'  => $value['bill']
                );
            }

            $this->db->select('full_name,mobile,ext,ext_extend');
            $this->db->where('roles', 'staff');
            $this->db->where('status', 'on');
            $this->db->group_by('ext');
            $this->db->order_by('id_center,id_department,id_group,ext');
            $tele = $this->db->get('tbl_accounts')->result_array();

            foreach ($tele as $key => $value) {
                $fullname = $value['full_name'];
                $mobile   = $value['mobile'];
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
                    $bill_in = $this->formatTime( intval($bill_in) );
                    /*$sc_in = $bill_in%60;
                    if($sc_in < 1){
                        $bill_in = ($bill_in/60).':00';
                    }else{
                        $bill_in = (($bill_in-$sc_in)/60).':'.$sc_in;
                    }*/
                }

                if($bill_out){
                    $bill_out = $this->formatTime( intval($bill_out) );
                    /*$sc_out = $bill_out%60;
                    if($sc_out < 1){
                        $bill_out = ($bill_out/60).':00';
                    }else{
                        $bill_out = (($bill_out-$sc_out)/60).':'.$sc_out;
                    }*/
                }

                $dataResult[] = array(
                    'fullname' => $fullname,
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

    function excalltime(){
        $dataResult = array();
        $startdate = isset($_GET['startdate']) ? $_GET['startdate'] : date('Y-m-d');
        $enddate = isset($_GET['enddate']) ? $_GET['enddate'] : date('Y-m-d');

        $data_out = array();
        $this->dbcc->select('src, count(1) as total, SUM(CASE disposition WHEN "ANSWERED" THEN 1 ELSE 0 END) as ans, SUM(CASE disposition WHEN "ANSWERED" THEN billsec ELSE 0 END) as bill');
        $this->dbcc->from('bit_cdr');
        $this->dbcc->where_in('src', $this->agop);
        $this->dbcc->where('dst !=', 'main');
        $this->dbcc->where('calldate >', $startdate . ' 05:00:00');
        $this->dbcc->where('calldate <', $enddate . ' 22:00:00');
        #$this->dbcc->like('lastdata', 'SIP/0', 'after');
        $this->dbcc->group_by('src');
        $data_out = $this->dbcc->get()->result_array();
        $_data_out = array();
        foreach ($data_out as $key => $value) {
            $_data_out[$value['src']] = array(
                'total' => $value['total'],
                'ans' => $value['ans'],
                'bill'  => $value['bill']
            );
        }

        $data_in = array();
        $this->dbcc->select('SUBSTRING_INDEX(SUBSTRING_INDEX(dstchannel, "SIP/",  -1), "-", 1) as src, count(1) as total, SUM(CASE WHEN (disposition = "ANSWERED" AND dstchannel != "") THEN billsec ELSE 0 END) as bill');
        $this->dbcc->from('bit_cdr');
        $this->dbcc->where('dst', 'main');
        $this->dbcc->like('dstchannel', 'SIP/4', 'after');
        //$this->dbcc->where('calldate >', date('Y-m-d') . ' 07:00:00');
        $this->dbcc->where('calldate >', $startdate . ' 05:00:00');
        $this->dbcc->where('calldate <', $enddate . ' 22:00:00');
        $this->dbcc->group_by('SUBSTRING_INDEX(SUBSTRING_INDEX(dstchannel, "SIP/",  -1), "-", 1)');
        $data_in = $this->dbcc->get()->result_array();
        $_data_in = array();
        foreach ($data_in as $key => $value) {
            $_data_in[$value['src']] = array(
                'total' => $value['total'],
                'bill'  => $value['bill']
            );
        }

        $this->db->select('full_name,mobile,ext,ext_extend');
        $this->db->where('roles', 'staff');
        $this->db->where('status', 'on');
        $this->db->group_by('ext');
        $this->db->order_by('id_center,id_department,id_group,ext');
        $tele = $this->db->get('tbl_accounts')->result_array();

        foreach ($tele as $key => $value) {
            $fullname = $value['full_name'];
            $mobile   = $value['mobile'];
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

            $bill_in = $this->formatTime( intval($bill_in) );
            $bill_out = $this->formatTime( intval($bill_out) );

            $dataResult[] = array(
                'fullname' => $fullname,
                'mobile' => $mobile,
                'ext' => $ext,
                'total_in' => $total_in,
                'bill_in' => $bill_in,
                'total_out' => $total_out,
                'total_ans' => $total_ans,
                'bill_out' => $bill_out,
            );
        }

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

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Talktime_'.date('YmdHis').'.xlsx"');
        $objWriter->save('php://output');
    }
}