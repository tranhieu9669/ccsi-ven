<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Report extends MY_Controller {
	public function __construct()
    {
        parent::__construct();


    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách tài khoản', ''),
        );

        $columns = array(
        	array(
        		'field' => 'order',
        		'title' => 'No',
        		'locked' => true,
                'lockable' => false,
        		'sortable' => false,
        		'width' => 50,
            ),
            array(
            	'field' => "fullname",
            	'title' => 'FullName',
            	'locked' => true,
                'lockable' => false,
            	'width' => 180,
            	'sortable' => false,
        	),
        	array(
            	'field' => "ext",
            	'title' => 'Ext',
            	'locked' => true,
                'lockable' => false,
            	'width' => 60,
            	'sortable' => false,
        	),
    	);
        $status = array();
        $statuscall = array();
        $this->db->select('id, code');
        $this->db->where('status', 'on');
        $statuscall = $this->db->get('tbl_call_status')->result_array();

        if(isset($statuscall) AND !empty($statuscall)){
        	foreach ($statuscall as $key => $value) {
        		$col = array(
        			'field' => "status_".$value['id'],
	            	'title' => $value['code'],
	            	'width' => 100,
	            	'sortable' => false,
    			);

    			array_push($columns, $col);
        	}

            $col = array(
                'field' => "status_total",
                'title' => 'TOTAL',
                'width' => 100,
                'sortable' => false,
            );
            array_push($columns, $col);
        }
    	$data['columns'] = json_encode($columns);

    	$account_group = array();
    	$this->db->select('id, full_name, ext');
		$this->db->where('id_center', $this->_id_center);
        $this->db->where('id_department', $this->_id_department);
    	$this->db->where('id_group', $this->_id_group);
    	$this->db->where('status', 'on');
    	$this->db->where('roles', 'staff');
    	$account_group = $this->db->get('tbl_accounts')->result_array();

        if ($this->input->is_ajax_request()) {
        	$databegin = array();
            foreach ($account_group as $key => $value) {
            	$id = $value['id'];
            	$full_name = $value['full_name'];
            	$ext = $value['ext'];

            	$databegin[$id]['fullname'] = $full_name;
            	$databegin[$id]['ext'] = $ext;
            	foreach ($statuscall as $_key => $_value) {
            		$databegin[$id]['status_'.$_value['id']] = 0;
            	}
                $databegin[$id]['status_total'] = 0;
            }

            $dataResult = array();
            
            $request    = $_REQUEST;
            $startdate = isset($request['startdate']) ? $request['startdate'] : date('Y-m-d');

            $db_group = $this->setdbconnect($this->_id_group, 'group');            
            $db_group->select('agent_id,agent_ext,id_status,countcall');
            $db_group->from('tbl_call_report');
            $db_group->where('datecall', $startdate);
            $dataResult = $db_group->get()->result_array();

            if(isset($dataResult) AND !empty($dataResult)){
            	foreach ($dataResult as $key => $value) {
            		$agent_id = $value['agent_id'];
            		$agent_ext = $value['agent_ext'];
            		$id_status = $value['id_status'];
            		$countcall = $value['countcall'];

            		if( isset($databegin[$agent_id]['status_'.$id_status]) ){
            			$databegin[$agent_id]['status_'.$id_status] = $countcall;
                        $databegin[$agent_id]['status_total'] = $databegin[$agent_id]['status_total'] + $countcall;
            		}
            	}
            }

            $dataResult = array();
            $i=0;
            foreach ($databegin as $key => $value) {
                if( $value['status_total'] > 0){
                    foreach ($value as $_key => $_val) {
                        $dataResult[$i][$_key] = $_val;
                    }
                    $i++;
                }
            }
            # 
            $return = array(
                'total' => count($dataResult),
                'data'  => $dataResult
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 60,
            '_wth_time'     => 120,
            '_wth_status'   => 65,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'report/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function exstatistics(){
        $startdate = isset($_GET['startdate']) ? $_GET['startdate'] : false;

        $status = array();
        $statuscall = array();
        $this->db->select('id, code');
        $this->db->where('status', 'on');
        $statuscall = $this->db->get('tbl_call_status')->result_array();

        $account_group = array();
        $this->db->select('id, full_name, ext');
		$this->db->where('id_center', $this->_id_center);
        $this->db->where('id_department', $this->_id_department);
        $this->db->where('id_group', $this->_id_group);
        $this->db->where('status', 'on');
        $this->db->where('roles', 'staff');
        $account_group = $this->db->get('tbl_accounts')->result_array();

        $databegin = array();
        foreach ($account_group as $key => $value) {
            $id = $value['id'];
            $full_name = $value['full_name'];
            $ext = $value['ext'];

            $databegin[$id]['fullname'] = $full_name;
            $databegin[$id]['ext'] = $ext;
            foreach ($statuscall as $_key => $_value) {
                $databegin[$id]['status_'.$_value['id']] = 0;
            }
            $databegin[$id]['status_total'] = 0;
        }

        $db_group = $this->setdbconnect($this->_id_group, 'group');            
        $db_group->select('agent_id,agent_ext,id_status,countcall');
        $db_group->from('tbl_call_report');
        $db_group->where('datecall', $startdate);
        $dataResult = $db_group->get()->result_array();

        if(isset($dataResult) AND !empty($dataResult)){
            foreach ($dataResult as $key => $value) {
                $agent_id = $value['agent_id'];
                $agent_ext = $value['agent_ext'];
                $id_status = $value['id_status'];
                $countcall = $value['countcall'];

                if( isset($databegin[$agent_id]['status_'.$id_status]) ){
                    $databegin[$agent_id]['status_'.$id_status] = $countcall;
                    $databegin[$agent_id]['status_total'] = $databegin[$agent_id]['status_total'] + $countcall;
                }
            }
        }

        # excel
        require_once( APPPATH  . 'third_party/PHPLib/PHPExcel/IOFactory.php');
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load(APPPATH  . 'third_party/templates/statistics_call.xlsx');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        if(isset($statuscall) AND !empty($statuscall)){
            $col = 'D';
            foreach ($statuscall as $key => $value) {
                $objWorksheet->setCellValue($col.'1', $value['code']);
                $col++;
            }
            $objWorksheet->setCellValue($col.'1', 'TOTAL');
        }

        if(isset($dataResult) AND !empty($dataResult)){
            $row = 1;
            foreach ($databegin as $key => $value) {
                if( $value['status_total'] > 0){
                    $row++;
                    $objWorksheet->setCellValue('A'.$row, ($row - 1));
                    $col = 'B';
                    foreach ($value as $_key => $_val) {
                        $objWorksheet->setCellValue($col.$row, $_val);
                        $col++;
                    }
                }
            }
        }

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="statistics'.date('YmdHis').'.xlsx"');
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