<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Duplicate extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        if($this->_role != 'support'){
            die('Bạn không có quyền trong trang này');
        }
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Duplicate', ''),
        );

        $data['content'] = 'index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function clean($mobile){
    	//$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';
    	$mobile = preg_replace("/[^0-9]/", "", $mobile);
        if( substr($mobile, 0, 1) != '0' ){
            $mobile = '0'.$mobile;
        }

        //echo $mobile; die;

        $sqlDelete = "DELETE n1 FROM tbl_appointments n1, tbl_appointments n2 WHERE n1.id < n2.id AND n1.app_created_at = n2.app_created_at AND n1.cus_mobile = n2.cus_mobile AND n1.cus_mobile='".$mobile."'";

        //echo $sqlDelete; die;

        if($this->db->query($sqlDelete) !== FALSE){
        	echo 'Success';
        }else{
        	echo 'Error';
        }
    }
}