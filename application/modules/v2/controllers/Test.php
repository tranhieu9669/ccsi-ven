<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách nguồn dữ liệu', ''),
        );

        if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 20;

            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $inputsearch= isset($request['inputsearch']) ? $request['inputsearch'] : '';
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;
			
			for ($i=0; $i < $total; $i++) { 
			    $dataResult[] = array(
			    	'ProductID' => $i,
			    	'ProductName' => 'Phung manh huong ' . $i,
			    	'UnitPrice' => $i*10,
			    	'UnitsInStock' => $i*5,
			    	'Discontinued' => False
		    	);
            }            
            
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult
            );

            echo json_encode($return);
            return;
        }

        $data['content'] = 'test/index';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function update(){
    	$request    = $_REQUEST;
    	$ProductID = isset($request['ProductID']) ? $request['ProductID'] : 1;
		$ProductName = isset($request['ProductName']) ? $request['ProductName'] : 1;
		$UnitPrice = isset($request['UnitPrice']) ? $request['UnitPrice'] : 1;
		$Discontinued = isset($request['Discontinued']) ? $request['Discontinued'] : 1;
		$UnitsInStock = isset($request['UnitsInStock']) ? $request['UnitsInStock'] : true;

		$dataResult = array(
			'ProductID' => $ProductID,
			'ProductName' => $ProductName,
			'UnitPrice' => $UnitPrice,
			'Discontinued' => $Discontinued,
			'UnitsInStock' => $UnitsInStock,
		);
		
		header('Content-Type: application/json;charset=utf-8');
		echo json_encode($dataResult);
        return;
    }

	function destroy(){

	}

	function create(){

	}

}