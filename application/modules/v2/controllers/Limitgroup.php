<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Limitgroup extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách ca', ''),
        );

        $data['content'] = 'limit/group/index';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function detail($id=false){
        $flag   = FALSE;
        if( $id AND $id > 0 ){
            $flag   = TRUE;
        }

        $success= '';
        $error  = '';
        $detail = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $detail = $_POST;
        }

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
        $data['content'] = 'limit/group/detail';
        $this->setlayout($data, 'v2/dialog');
    }
}