<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Call extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    # danh sach goi
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
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;

            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;
            $id_status = isset($request['id_status']) ? $request['id_status'] : false;
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            $enddate = $startdate;
            #$enddate = date('Y-m-d', strtotime($startdate . ' + 5 days'));

            $db_group = $this->setdbconnect($this->_id_group, 'group');
            $db_group->start_cache();

            $db_group->select('call.id,cus.id_link,CONCAT(cus.fullname," - ",cus.mobile) as customer,call.id_call_status,call.id_call_status_c1,call.id_call_status_c2,call.content_call,call.agent_ext,call.call_type,call.created_at');

            $db_group->from('tbl_call_detail as call');
            $db_group->join('tbl_customer as cus', 'cus.id_link=call.id_cus');
            $db_group->where('call.id_call_status > 0');
			$db_group->where('cus.status !=', 'unassign');

            if($inputsearch){
                $db_group->where('(cus.mobile like "%'.$inputsearch.'%" OR cus.fullname like "%'.$inputsearch.'%")');
            }else{
                if($id_status){
                    $exid = explode('-', $id_status);

                    switch (count($exid)) {
                        case 1:
                            $db_group->where('call.id_call_status', intval($exid[0]));
                            break;

                        case 2:
                            $db_group->where('call.id_call_status', intval($exid[0]));
                            $db_group->where('call.id_call_status_c1', intval($exid[1]));
                            break;

                        case 3:
                            $db_group->where('call.id_call_status', intval($exid[0]));
                            $db_group->where('call.id_call_status_c1', intval($exid[1]));
                            $db_group->where('call.id_call_status_c2', intval($exid[2]));
                            break;
                         
                        default:
                            # code...
                            break;
                     }
                }

                if($startdate){
                    $db_group->where('call.created_at > ', $startdate . ' 07:00:00');
                    $db_group->where('call.created_at < ', $enddate . ' 22:00:00');
                }
            }

            $db_group->stop_cache();
            $total = $db_group->count_all_results();

            $db_group->limit($limit, $offset);
            $dataResult = $db_group->get()->result_array();

            $db_group->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'query' => $db_group->last_query(),
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 50,
            '_wth_customer' => 300,
            '_wth_status'   => 165,
            '_wth_time'     => 160,
            '_wth_action'   => 60,
        ));

        #status call
        $tbl_call_status = array();
        $this->db->select('id,name,type');
        $this->db->where('status', 'on');
        $tbl_call_status = $this->db->get('tbl_call_status')->result_array();

        $tbl_call_status_child_c1 = array();
        $this->db->select('id,name,type,id_call_status');
        $this->db->where('status', 'on');
        $tbl_call_status_child_c1 = $this->db->get('tbl_call_status_child_c1')->result_array();

        $tbl_call_status_child_c2 = array();
        $this->db->select('id,name,type,id_call_status,id_call_status_c1');
        $this->db->where('status', 'on');
        $tbl_call_status_child_c2 = $this->db->get('tbl_call_status_child_c2')->result_array();

        $status_call = array();

        foreach ($tbl_call_status as $key => $value) {
            $id = $value['id'];
            $name = $value['name'];
            $type = $value['type'];

            if(isset($type) AND !empty($type)){
                $status_call[] = array(
                    'id' => $id,
                    'name' => $name,
                    'type' => $type
                );
            }else{
                $status_call[] = array(
                    'id' => $id,
                    'name' => $name
                );
            }

            foreach ($tbl_call_status_child_c1 as $key1 => $value1) {
                $id1 = $value1['id'];
                $name = $value1['name'];
                $type1 = $value1['type'];
                $id_call_status = $value1['id_call_status'];

                if($id_call_status == $id){
                    if( (!isset($type) OR empty($type)) AND (isset($type1) AND !empty($type1)) ){
                        $status_call[] = array(
                            'id' => $id.'-'.$id1,
                            'name' => ' --- '.$name,
                            'type' => $type1
                        );
                    }else{
                        $status_call[] = array(
                            'id' => $id.'-'.$id1,
                            'name' => ' --- '.$name
                        );
                    }

                    unset($tbl_call_status_child_c1[$key1]);

                    foreach ($tbl_call_status_child_c2 as $key2 => $value2) {
                        $id2 = $value2['id'];
                        $name = $value2['name'];
                        $type2 = $value2['type'];
                        $id_call_status = $value2['id_call_status'];
                        $id_call_status_c1 = $value2['id_call_status_c1'];

                        if($id_call_status == $id AND $id_call_status_c1 == $id1){
                            if( (!isset($type) OR empty($type)) AND (!isset($type1) OR empty($type1)) AND (isset($type2) AND !empty($type2)) ){
                                $status_call[] = array(
                                    'id' => $id.'-'.$id1.'-'.$id2,
                                    'name' => ' --- --- '.$name,
                                    'type' => $type2
                                );
                            }else{
                                $status_call[] = array(
                                    'id' => $id.'-'.$id1.'-'.$id2,
                                    'name' => ' --- --- '.$name
                                );
                            }
                        }
                    }
                }
            }
        }

        $data['status_call'] = $status_call;

        $status_grid = array();
        foreach ($status_call as $key => $value) {
            $status_grid[$value['id']] = $value['name'];
        }
        $data['status_grid'] = json_encode($status_grid);

        $data['content'] = 'call/index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function exlistcall(){
        $inputsearch = isset($_GET['inputsearch']) ? $_GET['inputsearch'] : false;
        $id_status = isset($_GET['id_status']) ? $_GET['id_status'] : false;
        $startdate = isset($_GET['startdate']) ? $_GET['startdate'] : date('Y-m-d');
        $enddate = $startdate;
        #$enddate = date('Y-m-d', strtotime($startdate . ' + 5 days'));

        #status call
        $tbl_call_status = array();
        $this->db->select('id,name,type');
        $this->db->where('status', 'on');
        $tbl_call_status = $this->db->get('tbl_call_status')->result_array();

        $tbl_call_status_child_c1 = array();
        $this->db->select('id,name,type,id_call_status');
        $this->db->where('status', 'on');
        $tbl_call_status_child_c1 = $this->db->get('tbl_call_status_child_c1')->result_array();

        $tbl_call_status_child_c2 = array();
        $this->db->select('id,name,type,id_call_status,id_call_status_c1');
        $this->db->where('status', 'on');
        $tbl_call_status_child_c2 = $this->db->get('tbl_call_status_child_c2')->result_array();

        $status_call = array();

        foreach ($tbl_call_status as $key => $value) {
            $id = $value['id'];
            $name = $value['name'];
            $type = $value['type'];

            $status_call[$id] = $name;

            foreach ($tbl_call_status_child_c1 as $key1 => $value1) {
                $id1 = $value1['id'];
                $name = $value1['name'];
                $type1 = $value1['type'];
                $id_call_status = $value1['id_call_status'];

                if($id_call_status == $id){
                    $status_call[$id.'-'.$id1] = ' --- '.$name;

                    unset($tbl_call_status_child_c1[$key1]);

                    foreach ($tbl_call_status_child_c2 as $key2 => $value2) {
                        $id2 = $value2['id'];
                        $name = $value2['name'];
                        $type2 = $value2['type'];
                        $id_call_status = $value2['id_call_status'];
                        $id_call_status_c1 = $value2['id_call_status_c1'];

                        if($id_call_status == $id AND $id_call_status_c1 == $id1){
                            $status_call[$id.'-'.$id1.'-'.$id2] = ' --- --- '.$name;
                        }
                    }
                }
            }
        }

        $dataResult = array();
        $db_group = $this->setdbconnect($this->_id_group, 'group');

        $db_group->select('call.id,cus.id_link,CONCAT(cus.fullname," - ",cus.mobile) as customer,call.id_call_status,call.id_call_status_c1,call.id_call_status_c2,call.content_call,call.agent_ext,call.call_type,call.created_at');
        
        $db_group->from('tbl_call_detail as call');
        $db_group->join('tbl_customer as cus', 'cus.id_link=call.id_cus');
        $db_group->where('call.id_call_status > 0');
		$db_group->where('cus.status !=', 'unassign');

        if($inputsearch){
            $db_group->where('(cus.mobile like "%'.$inputsearch.'%" OR cus.fullname like "%'.$inputsearch.'%")');
        }else{
            if($id_status){
                $exid = explode('-', $id_status);

                switch (count($exid)) {
                    case 1:
                        $db_group->where('call.id_call_status', intval($exid[0]));
                        break;

                    case 2:
                        $db_group->where('call.id_call_status', intval($exid[0]));
                        $db_group->where('call.id_call_status_c1', intval($exid[1]));
                        break;

                    case 3:
                        $db_group->where('call.id_call_status', intval($exid[0]));
                        $db_group->where('call.id_call_status_c1', intval($exid[1]));
                        $db_group->where('call.id_call_status_c2', intval($exid[2]));
                        break;
                     
                    default:
                        # code...
                        break;
                 }
            }

            if($startdate){
                $db_group->where('call.created_at > ', $startdate . ' 07:00:00');
                $db_group->where('call.created_at < ', $enddate . ' 22:00:00');
            }
        }
        $dataResult = $db_group->get()->result_array();

        # excel
        require_once( APPPATH  . 'third_party/PHPLib/PHPExcel/IOFactory.php');
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load(APPPATH  . 'third_party/templates/listcall.xlsx');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $row = 1;
        foreach ($dataResult as $key => $value) {
            $row++;
            $objWorksheet->setCellValue('A'.$row, ($row - 1));
            $objWorksheet->setCellValue('B'.$row, $value['customer']);
            $_id = $value['id_call_status'];
            $_id1 = $value['id_call_status_c1'];
            $_id2 = $value['id_call_status_c2'];
            
            if(isset($status_call[$id.'-'.$_id1.'-'.$_id2])){
                $objWorksheet->setCellValue('C'.$row, $status_call[$_id].PHP_EOL.$status_call[$id.'-'.$_id1].PHP_EOL.$status_call[$id.'-'.$_id1.'-'.$_id2]);
                $objWorksheet->getStyle('C'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setWrapText(true);
            }elseif(isset($status_call[$id.'-'.$_id1])){
                $objWorksheet->setCellValue('C'.$row, $status_call[$_id].PHP_EOL.$status_call[$id.'-'.$_id1]);
                $objWorksheet->getStyle('C'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setWrapText(true);
                #$objWorksheet->getStyle('C'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_TOP)->setWrapText(true);
            }else{
                $objWorksheet->setCellValue('C'.$row, $status_call[$_id]);
            }

            $objWorksheet->setCellValue('D'.$row, $value['content_call']);
            $objWorksheet->setCellValue('E'.$row, $value['agent_ext']);
            $objWorksheet->setCellValue('F'.$row, $value['call_type']);
            $objWorksheet->setCellValue('G'.$row, $value['created_at']);
        }

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="listcall'.date('YmdHis').'.xlsx"');
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
            $inputsearch = isset($request['inputsearch']) ? $request['inputsearch'] : false;
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');
            $enddate = $startdate;
            #$enddate = date('Y-m-d', strtotime($startdate . ' + 5 days'));

            $this->db->start_cache();

            $this->db->select('app.id,app.cus_mobile,app.cus_first_name,app.cus_last_name,cen.name,app.id_center,app.app_datetime,app.agent_ext,app.sms_status,app.app_status,app.crm_app_status, app.crm_app_content');
            $this->db->from('tbl_appointments as app');
            $this->db->join('tbl_centers as cen', 'cen.id=app.id_center and cen.type="spa"');
            $this->db->where('app.id_group', $this->_id_group);
            $this->db->where('app.last_app', 'on');
			$this->db->where('app.app_status !=', 'cancel');

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
            '_wth_first_name' => 160,
            '_wth_last_name' => 85,
            '_wth_mobile' => 100,
            '_wth_time' => 160,
            '_wth_status' => 80,
            '_wth_action' => 60,
        ));

        $centerspa = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'spa');
        $centerspa = $this->db->get('tbl_centers')->result_array();
        $data['centerspa'] = $centerspa;

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
        $this->db->where('app.id_group', $this->_id_group);
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

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-type: application/vnd.ms-excel');
        //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        //header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        //header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        //header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        //header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        //header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        //header ('Pragma: public'); // HTTP/1.0
        $objWriter->save('php://output');
    }
}