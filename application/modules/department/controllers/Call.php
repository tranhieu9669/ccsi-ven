<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Call extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){

    }

    function appointment(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách lịch hẹn', ''),
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

            $id_center = isset($request['id_center']) ? $request['id_center'] : false;
            $id_frametime = isset($request['id_frametime']) ? $request['id_frametime'] : false;
            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            $enddate = $startdate;
            #$enddate = date('Y-m-d', strtotime($startdate . ' + 5 days'));

            $this->db->start_cache();

            $this->db->select('app.id,app.cus_mobile,CONCAT(app.cus_first_name," ",app.cus_last_name) as fullname,cen.name,app.id_center,app.app_datetime,app.agent_ext,app.sms_status,app.app_status,app.crm_status,app.app_created_at,app.crm_app_status, app.crm_app_content');
            $this->db->from('tbl_appointments as app');
            $this->db->join('tbl_centers as cen', 'cen.id=app.id_center and cen.type="spa"');
            $this->db->where('app.id_department', $this->_id_department);
            $this->db->where('app.last_app', 'on');
			$this->db->where('app.app_status !=', 'cancel');

            if($inputsearch){
                $this->db->where('app.cus_mobile like "%'.$inputsearch.'%"');
            }else{
                if($id_center){
                    $this->db->where('app.id_center', $id_center);
                }

                if($id_frametime){
                    $this->db->where('app.id_frametime', $id_frametime);
                }
                
                if($startdate){
                    $this->db->where('app.app_datetime > ', $startdate . ' 07:00:00');
                    $this->db->where('app.app_datetime < ', $enddate . ' 22:00:00');
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

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order' => 50,
            '_wth_fullname' => 175,
            '_wth_mobile' => 100,
            '_wth_time' => 150,
            '_wth_status' => 80,
            '_wth_action' => 60,
        ));

        $centerspa = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'spa');
        $centerspa = $this->db->get('tbl_centers')->result_array();
        $data['centerspa'] = $centerspa;

        $frametime = array();
        $this->db->select('id,name,start,end');
        $this->db->where('status', 'on');
        $frametime = $this->db->get('tbl_frametime')->result_array();
        $data['frametime'] = $frametime;

        $data['content'] = 'call/appointment';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function exappointment(){
        $id_center = isset($_GET['id_center']) ? $_GET['id_center'] : false;
        $startdate = isset($_GET['startdate']) ? $_GET['startdate'] : date('Y-m-d');
        $enddate = $startdate;
        #$enddate = date('Y-m-d', strtotime($startdate . ' + 5 days'));
        $inputsearch = isset($_GET['inputsearch']) ? $_GET['inputsearch'] : false;

        $this->db->select('app.cus_mobile, app.cus_first_name, app.cus_last_name, cen.name, app.app_datetime, app.agent_ext, app.app_status');
        $this->db->from('tbl_appointments as app');
        $this->db->join('tbl_centers as cen', 'cen.id=app.id_center and cen.type="spa"');
        $this->db->where('app.id_department', $this->_id_department);
        $this->db->where('app.last_app', 'on');
		$this->db->where('app.app_status !=', 'cancel');
        #$this->db->where('app.id_center', $id_center);

        if($inputsearch){
            $this->db->where('app.cus_mobile like "%'.$inputsearch.'%"');
        }else{
            if($id_center){
                $this->db->where('app.id_center', $id_center);
            }

            if($startdate){
                $this->db->where('app.app_datetime > ', $startdate . ' 07:00:00');
                $this->db->where('app.app_datetime < ', $enddate . ' 22:00:00');
            }
        }
        $dataResult = $this->db->get()->result_array();

        # excel
        require_once( APPPATH  . 'third_party/PHPLib/PHPExcel/IOFactory.php');
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load(APPPATH  . 'third_party/templates/appointment.xlsx');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        #$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

        if(isset($dataResult) AND !empty($dataResult)){
            $row = 1;
            foreach ($dataResult as $key => $value) {
                $row++;
                $cus_mobile = $value['cus_mobile'];
                $cus_first_name = $value['cus_first_name'];
                $cus_last_name = $value['cus_last_name'];
                $name = $value['name'];
                $app_datetime = $value['app_datetime'];
                $agent_ext = $value['agent_ext'];
                $app_status = $value['app_status'];

                $objWorksheet->setCellValue('A'.$row, ($row - 1))
                            ->setCellValue('B'.$row, $cus_first_name)
                            ->setCellValue('C'.$row, $cus_last_name)
                            ->setCellValue('D'.$row, "'".$cus_mobile)
                            ->setCellValue('E'.$row, "'".$app_datetime)
                            ->setCellValue('F'.$row, $agent_ext)
                            ->setCellValue('G'.$row, $name)
                            ->setCellValue('H'.$row, $app_status);
            }
        }

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
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
}